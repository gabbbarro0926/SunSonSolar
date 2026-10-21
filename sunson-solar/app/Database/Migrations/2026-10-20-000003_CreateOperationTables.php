<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOperationTables extends Migration
{
    private function id(): array
    {
        return ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true];
    }

    private function fk(bool $null = false): array
    {
        return ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => $null];
    }

    private function ts(): array
    {
        return [
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ];
    }

    public function up()
    {
        // ---- bookings ----
        $this->forge->addField([
            'id'            => $this->id(),
            'customer_id'   => $this->fk(),
            'service_id'    => $this->fk(),
            'technician_id' => $this->fk(true),
            'scheduled_at'  => ['type' => 'DATETIME'],
            'site_address'  => ['type' => 'TEXT', 'null' => true],
            'notes'         => ['type' => 'TEXT', 'null' => true],
            // pending | approved | declined | assigned | in_progress | completed | cancelled
            'status'        => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'],
            'decline_reason' => ['type' => 'TEXT', 'null' => true],
            'completed_at'  => ['type' => 'DATETIME', 'null' => true],
        ] + $this->ts());
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('scheduled_at');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('customer_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('service_id', 'services', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('technician_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('bookings', true);

        // ---- payments (history + receipts) ----
        $this->forge->addField([
            'id'          => $this->id(),
            'booking_id'  => $this->fk(),
            'customer_id' => $this->fk(),
            'amount'      => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'method'      => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'cash'],
            'status'      => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'paid'], // pending|paid|refunded
            'receipt_no'  => ['type' => 'VARCHAR', 'constraint' => 40],
            'paid_at'     => ['type' => 'DATETIME', 'null' => true],
        ] + $this->ts());
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('receipt_no');
        $this->forge->addForeignKey('booking_id', 'bookings', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('customer_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('payments', true);

        // ---- attendance (technician site check-in) ----
        $this->forge->addField([
            'id'            => $this->id(),
            'user_id'       => $this->fk(),
            'full_name'     => ['type' => 'VARCHAR', 'constraint' => 200], // snapshot of name at check-in
            'booking_id'    => $this->fk(true),
            'checked_in_at' => ['type' => 'DATETIME'],
            'latitude'      => ['type' => 'DECIMAL', 'constraint' => '10,7'],
            'longitude'     => ['type' => 'DECIMAL', 'constraint' => '10,7'],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('checked_in_at');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('booking_id', 'bookings', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('attendance', true);

        // ---- notifications (one row per recipient) ----
        $this->forge->addField([
            'id'         => $this->id(),
            'user_id'    => $this->fk(),
            'type'       => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'system'], // reminder|promotion|schedule|system
            'title'      => ['type' => 'VARCHAR', 'constraint' => 200],
            'message'    => ['type' => 'TEXT'],
            'is_read'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_by' => $this->fk(true),
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['user_id', 'is_read']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('notifications', true);

        // ---- support tickets (+ chat-style messages) ----
        $this->forge->addField([
            'id'          => $this->id(),
            'customer_id' => $this->fk(),
            'subject'     => ['type' => 'VARCHAR', 'constraint' => 200],
            'status'      => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'open'], // open|in_progress|resolved|closed
            'priority'    => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'normal'],
        ] + $this->ts());
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('customer_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tickets', true);

        $this->forge->addField([
            'id'         => $this->id(),
            'ticket_id'  => $this->fk(),
            'user_id'    => $this->fk(),
            'message'    => ['type' => 'TEXT'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('ticket_id', 'tickets', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('ticket_messages', true);

        // ---- announcements (admin/HR -> employees) ----
        $this->forge->addField([
            'id'         => $this->id(),
            'title'      => ['type' => 'VARCHAR', 'constraint' => 200],
            'body'       => ['type' => 'TEXT'],
            'priority'   => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'normal'], // normal|safety|urgent
            'audience'   => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'all'],    // all|technician|dispatcher|hr|admin
            'created_by' => $this->fk(true),
        ] + $this->ts());
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('announcements', true);

        // ---- incident reports (technician -> admin) ----
        $this->forge->addField([
            'id'            => $this->id(),
            'technician_id' => $this->fk(),
            'booking_id'    => $this->fk(true),
            'category'      => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'other'], // equipment|customer_concern|safety|other
            'description'   => ['type' => 'TEXT'],
            'status'        => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'new'],   // new|reviewed|resolved
            'admin_notes'   => ['type' => 'TEXT', 'null' => true],
        ] + $this->ts());
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('technician_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('booking_id', 'bookings', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('incident_reports', true);
    }

    public function down()
    {
        foreach (['incident_reports', 'announcements', 'ticket_messages', 'tickets', 'notifications', 'attendance', 'payments', 'bookings'] as $t) {
            $this->forge->dropTable($t, true);
        }
    }
}
