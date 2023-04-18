<?php

namespace App\Models;

use CodeIgniter\Model;

class AuctionModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'auctions';
    protected $primaryKey       = 'auction_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'item_id',
        'user_id',
        'final_price',
        'winner_user_id',
        'status',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getAllAuctions($status = 'open', $where = NULL)
    {
        $whereArray = [
            'status' => $status,
            'users.deleted_at' => NULL,
            'items.deleted_at' => NULL
        ];

        if ($where) {
            foreach ($where as $key => $value) {
                $whereArray[$key] = $value;
            }
        }

        return $this->setTable('items')
            ->select('auctions.auction_id, items.item_id, items.user_id, users.name, users.username, item_name, description, items.initial_price, auctions.final_price, auctions.winner_user_id, auctions.status, auctions.created_at')
            ->join('auctions', 'auctions.item_id = items.item_id', 'inner')
            ->join('users', 'auctions.user_id = users.user_id', 'inner')
            ->where($whereArray)
            ->findAll();
    }

    public function getAuction($id, $status = 'open', $where = NULL)
    {
        $whereArray = [
            $this->primaryKey => $id,
            'status' => $status,
            'users.deleted_at' => NULL,
            'items.deleted_at' => NULL
        ];

        if ($where) {
            foreach ($where as $key => $value) {
                $whereArray[$key] = $value;
            }
        }

        return $this->setTable('items')
            ->select('auctions.auction_id, items.item_id, items.user_id, users.name, users.username, item_name, description, items.initial_price, auctions.final_price, auctions.winner_user_id, auctions.status, auctions.created_at')
            ->join('auctions', 'auctions.item_id = items.item_id', 'inner')
            ->join('users', 'auctions.user_id = users.user_id', 'inner')
            ->where($whereArray)->first();
    }
}
