<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAuctionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'auction_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'item_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'           => true,
            ],
            'user_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'           => true,
            ],
            'final_price' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'winner_user_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'           => true,
            ],
            'status' => [
                'type'           => 'ENUM',
                'constraint'     => ['open', 'closed'],
                'default'        => 'open',
            ],
            'date_completed' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL',
            'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL',
            'deleted_at TIMESTAMP NULL',
        ]);

        // primary key
        $this->forge->addKey('auction_id', TRUE);

        $this->forge->addKey('item_id', FALSE);

        $this->forge->addKey('user_id', FALSE);

        $this->forge->addKey('winner_user_id', FALSE);

        $this->forge->createTable('auctions', TRUE);
    }

    public function down()
    {
        $this->forge->dropTable('auctions');
    }
}
