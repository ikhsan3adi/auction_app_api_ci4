<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class User extends Seeder
{
    public function run()
    {
        $user_data = [
            [
                'username' => 'user123',
                'password_hash'  => password_hash('user123', PASSWORD_DEFAULT),
                'name' => 'John Doe',
                'email' => 'example@gmail.com',
                'phone' => '12345'
            ]
        ];

        foreach ($user_data as $data) {
            $this->db->table('users')->insert($data);
        }
    }
}
