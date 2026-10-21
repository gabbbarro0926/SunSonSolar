<?php

namespace App\Controllers\Api\Admin;

use App\Controllers\BaseApiController;
use App\Models\TicketMessageModel;
use App\Models\TicketModel;

/** Support desk for admins and customer-service staff. */
class TicketController extends BaseApiController
{
    /** GET /api/admin/tickets?status= */
    public function index()
    {
        $m = (new TicketModel())
            ->select('tickets.*, users.first_name AS customer_first_name, users.last_name AS customer_last_name')
            ->join('users', 'users.id = tickets.customer_id');
        if ($s = $this->request->getGet('status')) {
            $m->where('tickets.status', $s);
        }

        return $this->ok($m->orderBy('tickets.id', 'DESC')->findAll());
    }

    /** GET /api/admin/tickets/(:num) */
    public function show(int $id)
    {
        $t = (new TicketModel())->find($id);
        if (! $t) {
            return $this->notFound('Ticket');
        }
        $t['messages'] = (new TicketMessageModel())
            ->select('ticket_messages.*, users.first_name, users.last_name, users.user_type')
            ->join('users', 'users.id = ticket_messages.user_id')
            ->where('ticket_id', $id)->orderBy('ticket_messages.id')->findAll();

        return $this->ok($t);
    }

    /** POST /api/admin/tickets/(:num)/reply — { message } */
    public function reply(int $id)
    {
        $t = (new TicketModel())->find($id);
        if (! $t) {
            return $this->notFound('Ticket');
        }
        $data = $this->input();
        if ($res = $this->check($data, ['message' => 'required|max_length[3000]'])) {
            return $res;
        }

        (new TicketMessageModel())->insert(['ticket_id' => $id, 'user_id' => $this->me()['id'], 'message' => $data['message']]);
        (new TicketModel())->update($id, ['status' => 'in_progress']);
        $this->notify((int) $t['customer_id'], 'system', 'Support replied', "We replied to your ticket #$id: {$t['subject']}");

        return $this->ok(null, 'Reply sent.', 201);
    }

    /** PUT /api/admin/tickets/(:num)/status — { status, priority? } */
    public function status(int $id)
    {
        if (! (new TicketModel())->find($id)) {
            return $this->notFound('Ticket');
        }
        $data = $this->input();
        if ($res = $this->check($data, [
            'status'   => 'required|in_list[open,in_progress,resolved,closed]',
            'priority' => 'permit_empty|in_list[low,normal,high]',
        ])) {
            return $res;
        }
        (new TicketModel())->update($id, array_intersect_key($data, ['status' => 1, 'priority' => 1]));

        return $this->ok(null, 'Ticket updated.');
    }
}
