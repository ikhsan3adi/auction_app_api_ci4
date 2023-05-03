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

    public function getAuction($id = NULL, $status = 'open', $where = NULL, $allStatus = false)
    {
        $select = 'auctions.auction_id, items.item_id, items.user_id, users.username, users.name, users.email, users.phone, users.profile_image, item_name, description, items.initial_price, auctions.final_price, auctions.winner_user_id, auctions.status, auctions.created_at';

        if ($allStatus) {
            $whereArray = [
                'users.deleted_at' => NULL,
                'items.deleted_at' => NULL
            ];
        } else {
            $whereArray = [
                'status' => $status,
                'users.deleted_at' => NULL,
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
                ->join('users', 'auctions.user_id = users.user_id', 'inner')
                ->where($whereArray)->first();
        }
        return $this->setTable('items')
            ->select($select)
            ->join('auctions', 'auctions.item_id = items.item_id', 'inner')
            ->join('users', 'auctions.user_id = users.user_id', 'inner')
            ->where($whereArray)
            ->findAll();
    }

    public function getBidAuctions($userId)
    {
        return $this->select()
            ->join(
                '(SELECT bid_id, user_id, auction_id, bid_price, created_at bid_created_at FROM bids) bids',
                'auctions.auction_id = bids.auction_id',
                'right'
            )
            ->join('items', 'items.item_id = auctions.item_id', 'left')
            ->join(
                '(SELECT image_id, image, item_id image_item_id FROM images LIMIT 1) images',
                'images.image_item_id = auctions.item_id',
                'left'
            )
            ->where(['bids.user_id' => $userId])
            ->findAll();
    }
}
