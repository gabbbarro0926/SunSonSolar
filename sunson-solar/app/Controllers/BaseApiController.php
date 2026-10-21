<?php

namespace App\Controllers;

use App\Filters\AuthFilter;
use App\Models\NotificationModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Shared helpers for every JSON API controller.
 */
abstract class BaseApiController extends BaseController
{
    use ResponseTrait;

    protected $format = 'json';

    protected function me(): array
    {
        return AuthFilter::$user ?? [];
    }

    /** Request body as array — works for JSON and form posts. */
    protected function input(): array
    {
        $json = $this->request->getJSON(true);

        return is_array($json) ? $json : ($this->request->getPost() ?? []);
    }

    protected function ok($data = null, string $message = 'OK', int $code = 200): ResponseInterface
    {
        return $this->respond(['status' => $code, 'message' => $message, 'data' => $data], $code);
    }

    protected function fail422(array $errors): ResponseInterface
    {
        return $this->respond(['status' => 422, 'message' => 'Validation failed.', 'errors' => $errors], 422);
    }

    protected function notFound(string $what = 'Record'): ResponseInterface
    {
        return $this->respond(['status' => 404, 'message' => "$what not found."], 404);
    }

    protected function forbidden(string $message = 'Forbidden.'): ResponseInterface
    {
        return $this->respond(['status' => 403, 'message' => $message], 403);
    }

    /** Validate an array against rules; returns null when valid, else a 422 response. */
    protected function check(array $data, array $rules, array $messages = []): ?ResponseInterface
    {
        $validation = service('validation');
        $validation->reset();
        $validation->setRules($rules, $messages);

        return $validation->run($data) ? null : $this->fail422($validation->getErrors());
    }

    /** Create a notification row for one user. */
    protected function notify(int $userId, string $type, string $title, string $message): void
    {
        (new NotificationModel())->insert([
            'user_id'    => $userId,
            'type'       => $type,
            'title'      => $title,
            'message'    => $message,
            'created_by' => $this->me()['id'] ?? null,
        ]);
    }
}
