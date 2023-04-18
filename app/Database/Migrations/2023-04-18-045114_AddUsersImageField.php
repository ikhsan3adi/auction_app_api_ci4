<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUsersImageField extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', 'profile_image VARCHAR(255) NULL AFTER phone');
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'profile_image');
    }
}
