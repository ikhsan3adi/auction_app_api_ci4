<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateImagesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'image_id' => [
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
            'image' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255
            ],
        ]);

        // primary key
        $this->forge->addKey('image_id', TRUE);

        $this->forge->addKey('item_id', FALSE);

        $this->forge->createTable('images', TRUE);
    }

    public function down()
    {
        $this->forge->dropTable('images');
    }
}
