<?php

namespace App\Controllers\Api\Admin;

use App\Controllers\BaseApiController;
use CodeIgniter\Model;

/**
 * Small generic CRUD for the admin "settings / content" tables.
 * Subclasses only declare the model, label and validation rules.
 *
 * Use "{id}" inside a rule (e.g. is_unique[services.name,id,{id}]) — it is
 * replaced with the record id on update and dropped on create.
 */
abstract class AbstractCrudController extends BaseApiController
{
    protected string $modelClass;
    protected string $label = 'Record';
    /** @var array<string,string> */
    protected array $rules = [];
    /** Columns the list can be filtered on via ?column=value */
    protected array $filters = [];
    protected string $orderBy = 'id';
    protected string $orderDir = 'DESC';

    protected function model(): Model
    {
        return new $this->modelClass();
    }

    /** Hook: extra columns to set on create (e.g. author_id). */
    protected function extraOnCreate(): array
    {
        return [];
    }

    /** Hook: runs after a successful create. */
    protected function afterCreate(array $row): void
    {
    }

    public function index()
    {
        $m = $this->model();
        foreach ($this->filters as $f) {
            $v = $this->request->getGet($f);
            if ($v !== null && $v !== '') {
                $m->where($f, $v);
            }
        }

        return $this->ok($m->orderBy($this->orderBy, $this->orderDir)->findAll());
    }

    public function show(int $id)
    {
        $row = $this->model()->find($id);

        return $row ? $this->ok($row) : $this->notFound($this->label);
    }

    public function create()
    {
        $data = $this->prepare($this->input());
        if ($res = $this->check($data, $this->rulesFor(null))) {
            return $res;
        }

        $m   = $this->model();
        $id  = $m->insert($this->only($data) + $this->extraOnCreate());
        $row = $m->find($id);
        $this->afterCreate($row);

        return $this->ok($row, "{$this->label} created.", 201);
    }

    public function update(int $id)
    {
        $m = $this->model();
        if (! $m->find($id)) {
            return $this->notFound($this->label);
        }

        $data  = $this->prepare($this->input());
        $rules = array_intersect_key($this->rulesFor($id, true), $data); // validate only what was sent
        if ($res = $this->check($data, $rules)) {
            return $res;
        }

        $update = $this->only($data);
        if ($update) {
            $m->update($id, $update);
        }

        return $this->ok($m->find($id), "{$this->label} updated.");
    }

    public function delete(int $id)
    {
        $m = $this->model();
        if (! $m->find($id)) {
            return $this->notFound($this->label);
        }

        try {
            $m->delete($id);
        } catch (\Throwable $e) {
            return $this->respond(['status' => 409, 'message' => "{$this->label} is still in use and cannot be deleted. Deactivate it instead."], 409);
        }

        return $this->ok(null, "{$this->label} deleted.");
    }

    // ---------------------------------------------------------------

    private function rulesFor(?int $id, bool $forUpdate = false): array
    {
        $out = [];
        foreach ($this->rules as $field => $rule) {
            $rule = $id === null ? str_replace(',id,{id}', '', $rule) : str_replace('{id}', (string) $id, $rule);
            if ($forUpdate) {
                $rule = str_replace('required|', '', $rule);
            }
            $out[$field] = $rule;
        }

        return $out;
    }

    /** Cast JSON booleans to 0/1 so validation and MySQL are happy. */
    private function prepare(array $data): array
    {
        foreach ($data as $k => $v) {
            if (is_bool($v)) {
                $data[$k] = $v ? 1 : 0;
            }
        }

        return $data;
    }

    private function only(array $data): array
    {
        return array_intersect_key($data, $this->rules);
    }
}
