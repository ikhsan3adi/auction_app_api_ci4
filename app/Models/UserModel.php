<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'users';
    protected $primaryKey       = 'user_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['username', 'password_hash', 'name', 'email', 'phone'];

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

    public function getUser($id = NULL)
    {
        if ($id != NULL) {
            return $this->select('user_id, username, name, email, phone, created_at')
                ->where($this->primaryKey, $id)
                ->first();
        }
        return $this->select('user_id, username, name, email, phone, created_at')->findAll();
    }

    public function getUserByIdUsernameEmail($id, $username, $email)
    {
        return $this->where([
            $this->primaryKey => $id,
            'username' => $username,
            'email' => $email
        ])->first();
    }
}
