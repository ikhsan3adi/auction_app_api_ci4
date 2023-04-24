<?php

namespace App\Models;

use CodeIgniter\Model;

class BidModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bids';
    protected $primaryKey       = 'bid_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

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

    public function getBid($id = NULL, $where = NULL)
    {
        $select = 'bids.bid_id, bids.auction_id, bids.bid_price, users.user_id, users.username, users.name, users.email, users.phone, users.profile_image, bids.created_at';

        $whereArray = [
            'users.deleted_at' => NULL,
            'bids.deleted_at' => NULL
        ];

        if ($where) {
            foreach ($where as $key => $value) {
                $whereArray[$key] = $value;
            }
        }

        if ($id) {
            $whereArray[$this->primaryKey] = $id;
            return $this->setTable('users')
                ->select($select)
                ->join('bids', 'bids.user_id = users.user_id', 'inner')
                ->where($whereArray)
                ->first();
        }

        return $this->setTable('users')
            ->select($select)
            ->join('bids', 'bids.user_id = users.user_id', 'inner')
            ->where($whereArray)
            ->findAll();
    }
}
