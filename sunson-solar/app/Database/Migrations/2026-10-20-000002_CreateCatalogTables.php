<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCatalogTables extends Migration
{
    private function id(): array
    {
        return ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true];
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
        // ---- services (consultation, installation, ...) ----
        $this->forge->addField([
            'id'               => $this->id(),
            'name'             => ['type' => 'VARCHAR', 'constraint' => 120],
            'description'      => ['type' => 'TEXT', 'null' => true],
            'price'            => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'duration_minutes' => ['type' => 'INT', 'constraint' => 11, 'default' => 60],
            'is_active'        => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
        ] + $this->ts());
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('name');
        $this->forge->createTable('services', true);

        // ---- products (panels, inverters, batteries, racking_mounting, wires) ----
        $this->forge->addField([
            'id'          => $this->id(),
            'category'    => ['type' => 'VARCHAR', 'constraint' => 40],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 150],
            'description' => ['type' => 'TEXT', 'null' => true],
            'price'       => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'stock'       => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'image_url'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_active'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
        ] + $this->ts());
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('category');
        $this->forge->createTable('products', true);

        // ---- availability (admin-configurable weekly slots) ----
        $this->forge->addField([
            'id'            => $this->id(),
            'day_of_week'   => ['type' => 'TINYINT', 'constraint' => 1], // 0=Sun ... 6=Sat
            'start_time'    => ['type' => 'TIME'],
            'end_time'      => ['type' => 'TIME'],
            'max_bookings'  => ['type' => 'INT', 'constraint' => 11, 'default' => 5],
            'is_active'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
        ] + $this->ts());
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('availability', true);

        // ---- quote_requests (public "free quote" form) ----
        $this->forge->addField([
            'id'         => $this->id(),
            'name'       => ['type' => 'VARCHAR', 'constraint' => 150],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 150],
            'phone'      => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'address'    => ['type' => 'TEXT', 'null' => true],
            'service_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'message'    => ['type' => 'TEXT', 'null' => true],
            'status'     => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'new'], // new|contacted|closed
        ] + $this->ts());
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('service_id', 'services', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('quote_requests', true);

        // ---- testimonials ----
        $this->forge->addField([
            'id'           => $this->id(),
            'customer_name' => ['type' => 'VARCHAR', 'constraint' => 150],
            'content'      => ['type' => 'TEXT'],
            'rating'       => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 5],
            'is_published' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
        ] + $this->ts());
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('testimonials', true);

        // ---- faqs ----
        $this->forge->addField([
            'id'         => $this->id(),
            'question'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'answer'     => ['type' => 'TEXT'],
            'sort_order' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'is_published' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
        ] + $this->ts());
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('faqs', true);

        // ---- posts (blog / news) ----
        $this->forge->addField([
            'id'           => $this->id(),
            'title'        => ['type' => 'VARCHAR', 'constraint' => 200],
            'body'         => ['type' => 'TEXT'],
            'author_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'is_published' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
        ] + $this->ts());
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('author_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('posts', true);
    }

    public function down()
    {
        foreach (['posts', 'faqs', 'testimonials', 'quote_requests', 'availability', 'products', 'services'] as $t) {
            $this->forge->dropTable($t, true);
        }
    }
}
