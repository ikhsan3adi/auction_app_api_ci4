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
    protected $allowedFields    = [
        'user_id',
        'auction_id',
        'bid_price'
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

    public function getBid($id = NULL, $where = NULL)
    {
        $whereArray = [];

        if ($where) {
            foreach ($where as $key => $value) {
                $whereArray[$key] = $value;
            }
        }

        if ($id) {
            $whereArray[$this->primaryKey] = $id;
            return $this->select()->where($whereArray)->first();
        }

        return $this->select()->where($whereArray)->findAll();
    }
}
