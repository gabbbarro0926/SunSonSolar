<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserTables extends Migration
{
    public function up()
    {
        // ---- departments ----
        $this->forge->addField([
            'id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 100],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('name');
        $this->forge->createTable('departments', true);

        // ---- users (customers + employees) ----
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'first_name'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'last_name'     => ['type' => 'VARCHAR', 'constraint' => 100],
            'middle_name'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'birthdate'     => ['type' => 'DATE', 'null' => true],
            'gender'        => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'email'         => ['type' => 'VARCHAR', 'constraint' => 150],
            'phone'         => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'address'       => ['type' => 'TEXT', 'null' => true],
            'username'      => ['type' => 'VARCHAR', 'constraint' => 60],
            'password_hash' => ['type' => 'VARCHAR', 'constraint' => 255],
            // customer | admin | hr | dispatcher | technician
            'role'          => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'customer'],
            // customer | employee
            'user_type'     => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'customer'],
            'department_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'is_active'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'pref_reminders'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'pref_promotions' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'pref_system'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('email');
        $this->forge->addUniqueKey('username');
        $this->forge->addForeignKey('department_id', 'departments', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('users', true);

        // ---- api_tokens (login sessions for the API) ----
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'token_hash' => ['type' => 'VARCHAR', 'constraint' => 64],
            'expires_at' => ['type' => 'DATETIME'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('token_hash');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('api_tokens', true);
    }

    public function down()
    {
        $this->forge->dropTable('api_tokens', true);
        $this->forge->dropTable('users', true);
        $this->forge->dropTable('departments', true);
    }
}
