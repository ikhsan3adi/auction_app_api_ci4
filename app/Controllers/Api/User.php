<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

use App\Models\UserModel;
use Config\Services;

class User extends ResourceController
{
    use ResponseTrait;

    protected String $userId;

    public function __construct()
    {
        $this->userId = session()->getFlashdata('user_id');
    }

    public function index()
    {
        $db = new UserModel;
        $users = $db->getUser();

        if (!$users) {
            return $this->failNotFound('Users not found');
        }

        return $this->respond([
            'status' => 200,
            'messages' => ['success' => 'OK'],
            'data' => Services::arrayKeyToCamelCase($users, nested: true),
        ]);
    }

    public function show($id = null)
    {
        $db = new UserModel;
        $user = $db->getUser($id);

        if (!$user) {
            return $this->failNotFound('User not found');
        }

        return $this->respond([
            'status' => 200,
            'messages' => ['success' => 'OK'],
            'data' => Services::arrayKeyToCamelCase($user, nested: false),
        ]);
    }

    public function create()
    {
        if (!$this->validate([
            'username'     => 'required|is_unique[users.username]|min_length[4]',
            'password'     => 'required|min_length[6]',
            'name'         => 'required',
            'email'        => 'required|valid_email',
            'phone'        => 'required'
        ])) {
            return $this->failValidationErrors(\Config\Services::validation()->getErrors());
        }

        $insert = [
            'username'      => $this->request->getVar('username'),
            'password_hash' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
            'name'          => $this->request->getVar('name'),
            'email'         => $this->request->getVar('email'),
            'phone'         => $this->request->getVar('phone'),
        ];

        $db = new UserModel;
        $save  = $db->insert($insert);

        if (!$save) {
            return $this->failServerError(description: 'Failed to create user');
        }

        return $this->respondCreated([
            'status' => 200,
            'messages' => ['success' => 'OK']
        ]);
    }

    public function update($id = null)
    {
        if (!$this->validate([
            'username' => 'permit_empty|is_unique[users.username]',
            'password' => 'permit_empty|min_length[6]',
            'name'     => 'permit_empty',
            'email'    => 'permit_empty|valid_email',
            'phone'    => 'permit_empty',
        ])) {
            return $this->failValidationErrors(\Config\Services::validation()->getErrors());
        }

        $db = new UserModel;
        $exist = $db->where(['user_id' => $this->userId])->first();

        if (!$exist) {
            return $this->failNotFound(description: 'User not found');
        }

        $update = [
            'username' => $this->request->getRawInputVar('username')
                ? $this->request->getRawInputVar('username')
                : $exist['username'],

            'password_hash' => $this->request->getRawInputVar('password')
                ? password_hash($this->request->getRawInputVar('password'), PASSWORD_DEFAULT)
                : $exist['password_hash'],

            'name' => $this->request->getRawInputVar('name')
                ?? $exist['name'],
            'email' => $this->request->getRawInputVar('email')
                ?? $exist['email'],
            'phone' => $this->request->getRawInputVar('phone')
                ?? $exist['phone']
        ];

        $save = $db->update($id, $update);

        if (!$save) {
            return $this->failServerError(description: 'Failed to update user');
        }

        return $this->respondUpdated([
            'status' => 200,
            'messages' => [
                'success' => 'User updated successfully'
            ]
        ]);
    }

    public function delete($id = null)
    {
        $db = new UserModel;
        $exist = $db->where(['user_id' => $this->userId])->first();

        if (!$exist) return $this->failNotFound(description: 'User not found');

        $delete = $db->delete($id);

        if (!$delete) return $this->failServerError(description: 'Failed to delete user');

        return $this->respond([
            'status' => 200,
            'messages' => ['success' => 'User successfully deleted']
        ]);
    }
}
