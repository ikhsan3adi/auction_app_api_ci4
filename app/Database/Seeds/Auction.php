<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Auction extends Seeder
{
    public function run()
    {
        $auction = [
            [
                'item_id' => 1,
                'user_id' => 1,
                'status' => 'open'
            ],
        ];

        foreach ($auction as $data) {
            $this->db->table('auctions')->insert($data);
        }
    }
}
