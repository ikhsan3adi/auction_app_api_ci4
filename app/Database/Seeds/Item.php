<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Item extends Seeder
{
    public function run()
    {
        $item_data = [
            [
                'user_id' => 1,
                'item_name' => 'Roti O St. Lempuyangan',
                'description' => 'Asli',
                'initial_price' => 15000
            ],
            [
                'user_id' => 2,
                'item_name' => 'Fiesta Chicken nugget',
                'description' => 'Asli',
                'initial_price' => 10000
            ],
            [
                'user_id' => 3,
                'item_name' => 'Kue',
                'description' => 'Asli',
                'initial_price' => 15000
            ],
        ];

        foreach ($item_data as $data) {
            $this->db->table('items')->insert($data);
        }
    }
}
