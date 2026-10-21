<?php

namespace App\Controllers\Api\Customer;

use App\Controllers\BaseApiController;
use App\Models\TicketMessageModel;
use App\Models\TicketModel;
use App\Models\UserModel;

/** Customer support/help: ticket with a chat-style thread. */
class TicketController extends BaseApiController
{
    /** GET /api/customer/tickets */
    public function index()
    {
        return $this->ok((new TicketModel())->where('customer_id', $this->me()['id'])->orderBy('id', 'DESC')->findAll());
    }

    /** GET /api/customer/tickets/(:num) */
    public function show(int $id)
    {
        $t = (new TicketModel())->where('customer_id', $this->me()['id'])->find($id);
        if (! $t) {
            return $this->notFound('Ticket');
        }
        $t['messages'] = (new TicketMessageModel())->where('ticket_id', $id)->orderBy('id')->findAll();

        return $this->ok($t);
    }

    /** POST /api/customer/tickets — { subject, message } */
    public function create()
    {
        $data = $this->input();
        if ($res = $this->check($data, ['subject' => 'required|max_length[200]', 'message' => 'required|max_length[3000]'])) {
            return $res;
        }

        $tickets = new TicketModel();
        $id = $tickets->insert(['customer_id' => $this->me()['id'], 'subject' => $data['subject'], 'status' => 'open']);
        (new TicketMessageModel())->insert(['ticket_id' => $id, 'user_id' => $this->me()['id'], 'message' => $data['message']]);

        foreach ((new UserModel())->whereIn('role', ['admin', 'staff'])->where('is_active', 1)->findAll() as $u) {
            $this->notify((int) $u['id'], 'system', 'New support ticket', "Ticket #$id: {$data['subject']}");
        }

        return $this->ok($tickets->find($id), 'Ticket submitted.', 201);
    }

    /** POST /api/customer/tickets/(:num)/reply — { message } */
    public function reply(int $id)
    {
        $t = (new TicketModel())->where('customer_id', $this->me()['id'])->find($id);
        if (! $t) {
            return $this->notFound('Ticket');
        }
        if ($t['status'] === 'closed') {
            return $this->fail422(['status' => 'This ticket is closed.']);
        }
        $data = $this->input();
        if ($res = $this->check($data, ['message' => 'required|max_length[3000]'])) {
            return $res;
        }

        (new TicketMessageModel())->insert(['ticket_id' => $id, 'user_id' => $this->me()['id'], 'message' => $data['message']]);
        (new TicketModel())->update($id, ['status' => 'open']);

        return $this->ok(null, 'Reply sent.', 201);
    }
}
