<?php

namespace App\Controllers\Api;

use App\Models\BidModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Config\Services;

class Bid extends ResourceController
{
    use ResponseTrait;

    protected String $userId;

    public function __construct()
    {
        $this->userId = session()->getFlashdata('user_id');
    }

    public function index()
    {
        $db = new BidModel;
        $bids = $db->findAll();

        if (!$bids) {
            return $this->failNotFound('Bids not found');
        }

        return $this->respond([
            'status' => 200,
            'messages' => ['success' => 'OK'],
            'data' => Services::arrayKeyToCamelCase($bids, nested: true),
        ]);
    }

    public function showBids($auction_id = null)
    {
        $db = new BidModel;
        $bids = $db->where(['auction_id' => $auction_id])->findAll();

        return $this->respond([
            'status' => 200,
            'messages' => ['success' => 'OK'],
            'data' => Services::arrayKeyToCamelCase($bids, nested: true),
        ]);
    }

    public function show($id = null)
    {
        $db = new BidModel;
        $bid = $db->where(['bid_id' => $id])->first();

        if (!$bid) {
            return $this->failNotFound('Bid not found');
        }

        return $this->respond([
            'status' => 200,
            'messages' => ['success' => 'OK'],
            'data' => Services::arrayKeyToCamelCase($bid, nested: false),
        ]);
    }

    public function create()
    {
        if (!$this->validate([
            'auction_id'  => 'required|numeric',
            'bid_price'   => 'required|numeric',
        ])) {
            return $this->failValidationErrors(\Config\Services::validation()->getErrors());
        }

        $insert = [
            'user_id'       => $this->userId,
            'auction_id'     => $this->request->getVar('auction_id'),
            'bid_price'   => $this->request->getVar('bid_price'),
        ];

        $db = new BidModel;
        $save  = $db->insert($insert);

        if (!$save) {
            return $this->failServerError(description: 'Failed to place bid');
        }

        return $this->respondCreated([
            'status' => 200,
            'messages' => ['success' => 'OK']
        ]);
    }

    public function update($id = null)
    {
        if (!$this->validate([
            'auction_id'       => 'permit_empty|numeric',
            'bid_price'       => 'permit_empty|numeric',
        ])) {
            return $this->failValidationErrors(\Config\Services::validation()->getErrors());
        }

        $db = new BidModel;
        $exist = $db->where([
            'bid_id' => $id,
            'auction_id' => $this->request->getRawInputVar('auction_id'),
            'user_id' => $this->userId
        ])->first();

        if (!$exist) {
            return $this->failNotFound(description: 'Bid not found');
        }

        $update = [
            'bid_price' => $this->request->getRawInputVar('bid_price')
                ?? $exist['bid_price'],
        ];

        $save = $db->update($id, $update);

        if (!$save) {
            return $this->failServerError(description: 'Failed to update bid');
        }

        return $this->respondUpdated([
            'status' => 200,
            'messages' => [
                'success' => 'Bid updated successfully'
            ]
        ]);
    }

    public function delete($id = null)
    {
        $db = new BidModel;
        $exist = $db->where(['bid_id' => $id, 'user_id' => $this->userId])->first();

        if (!$exist) return $this->failNotFound(description: 'Bid not found');

        $delete = $db->delete($id);

        if (!$delete) return $this->failServerError(description: 'Failed to delete bid');

        return $this->respondDeleted([
            'status' => 200,
            'messages' => ['success' => 'Bid successfully deleted']
        ]);
    }
}
