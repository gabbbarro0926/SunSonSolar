<?php

namespace App\Controllers\Api\Admin;

use App\Controllers\BaseApiController;
use App\Models\QuoteRequestModel;

/** Free-quote requests from the public homepage. */
class QuoteController extends BaseApiController
{
    /** GET /api/admin/quotes?status= */
    public function index()
    {
        $m = new QuoteRequestModel();
        if ($s = $this->request->getGet('status')) {
            $m->where('status', $s);
        }

        return $this->ok($m->orderBy('id', 'DESC')->findAll());
    }

    /** PUT /api/admin/quotes/(:num) — { status: new|contacted|closed } */
    public function update(int $id)
    {
        $m = new QuoteRequestModel();
        if (! $m->find($id)) {
            return $this->notFound('Quote request');
        }
        $data = $this->input();
        if ($res = $this->check($data, ['status' => 'required|in_list[new,contacted,closed]'])) {
            return $res;
        }
        $m->update($id, ['status' => $data['status']]);

        return $this->ok($m->find($id), 'Quote request updated.');
    }
}
