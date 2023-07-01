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
        'date_completed',
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

    public function getAuction($id = NULL, $status = 'open', $where = NULL, $allStatus = false, $page = 1)
    {
        $select = 'auctions.auction_id, items.item_id, items.user_id, item_name, description, items.initial_price, auctions.final_price, auctions.winner_user_id, auctions.status, auctions.date_completed, auctions.created_at';

        if ($allStatus) {
            $whereArray = [
                'items.deleted_at' => NULL
            ];
        } else {
            $whereArray = [
                'status' => $status,
                'items.deleted_at' => NULL
            ];
        }

        if ($where) {
            foreach ($where as $key => $value) {
                $whereArray[$key] = $value;
            }
        }

        if ($id) {
            $whereArray[$this->primaryKey] = $id;
            return $this->setTable('items')
                ->select($select)
                ->join('auctions', 'auctions.item_id = items.item_id', 'inner')
                ->where($whereArray)->first();
        }
        return $this->setTable('items')
            ->select($select)
            ->join('auctions', 'auctions.item_id = items.item_id', 'inner')
            ->where($whereArray)
            ->orderBy('auctions.created_at', 'desc')
            ->findAll(limit: 20, offset: ($page - 1) * 20);
    }

    public function getBidAuctions($userId)
    {
        return $this->select()
            ->join(
                '(SELECT user_id bid_user_id, auction_id FROM bids) bids',
                'auctions.auction_id = bids.auction_id',
                'left'
            )
            ->join('(SELECT items.item_id, items.user_id, item_name, description, items.initial_price, items.deleted_at FROM items) items', 'items.item_id = auctions.item_id', 'right')
            ->where([
                'bids.bid_user_id' => $userId,
                'auctions.deleted_at' => NULL,
                'items.deleted_at' => NULL
            ])
            ->groupBy('auctions.auction_id')
            ->findAll();
    }
}
