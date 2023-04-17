<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'user_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'username' => [
                'type'           => 'VARCHAR',
                'constraint'     => 30
            ],
            'password_hash' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255
            ],
            'name' => [
                'type'           => 'VARCHAR',
                'constraint'     => 64
            ],
            'email' => [
                'type'           => 'VARCHAR',
                'constraint'     => 64
            ],
            'phone' => [
                'type'           => 'VARCHAR',
                'constraint'     => 32
            ],
            'created_at' => [
                'type'           => 'DATETIME',
                'default'        => 'CURRENT_TIMESTAMP',
                'null'           => true
            ],
            'updated_at' => [
                'type'           => 'DATETIME',
                'default'        => 'CURRENT_TIMESTAMP',
                'null'           => true,
            ],
            'deleted_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
        ]);

        // primary key
        $this->forge->addKey('user_id', TRUE);

        $this->forge->createTable('users', TRUE);
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
