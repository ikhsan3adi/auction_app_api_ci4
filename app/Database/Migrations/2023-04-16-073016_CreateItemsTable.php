<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateItemsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'item_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'user_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'           => true,
            ],
            'item_name' => [
                'type'           => 'VARCHAR',
                'constraint'     => 32
            ],
            'description' => [
                'type'           => 'TEXT',
                'null'           => true,
            ],
            'initial_price' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'default'        => 0,
            ],
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL',
            'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL',
            'deleted_at TIMESTAMP NULL',
        ]);

        // primary key
        $this->forge->addKey('item_id', TRUE);

        $this->forge->addKey('user_id', FALSE);

        $this->forge->createTable('items', TRUE);
    }

    public function down()
    {
        $this->forge->dropTable('items');
    }
}
