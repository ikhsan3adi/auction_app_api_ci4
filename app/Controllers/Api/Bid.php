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

        foreach ($bids as $key => $value) {
            if ($value['profile_image']) {
                $bids[$key]['profile_image'] = Services::fullImageURL($value['profile_image']);
            }
        }

        return $this->respond([
            'status' => 200,
            'messages' => ['success' => 'OK'],
            'data' => Services::arrayKeyToCamelCase($bids, nested: true),
        ]);
    }

    public function showBids($auctionId = null)
    {
        $db = new BidModel;
        $bids = $db->where(['auction_id' => $auctionId])->findAll();

        if ($bids) {
            foreach ($bids as $key => $value) {
                if ($value['profile_image']) {
                    $bids[$key]['profile_image'] = Services::fullImageURL($value['profile_image']);
                }
            }
        }

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

        if ($bid['profile_image']) {
            $bid['profile_image'] = Services::fullImageURL($bid['profile_image']);
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
            'auctionId'  => 'required|numeric',
            'bidPrice'   => 'required|numeric',
        ])) {
            return $this->failValidationErrors(\Config\Services::validation()->getErrors());
        }

        $insert = [
            'user_id'       => $this->userId,
            'auction_id'     => $this->request->getVar('auctionId'),
            'bid_price'   => $this->request->getVar('bidPrice'),
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
            'auctionId'       => 'permit_empty|numeric',
            'bidPrice'       => 'permit_empty|numeric',
        ])) {
            return $this->failValidationErrors(\Config\Services::validation()->getErrors());
        }

        $db = new BidModel;
        $exist = $db->where([
            'bid_id' => $id,
            'auction_id' => $this->request->getRawInputVar('auctionId'),
            'user_id' => $this->userId
        ])->first();

        if (!$exist) {
            return $this->failNotFound(description: 'Bid not found');
        }

        $update = [
            'bid_price' => $this->request->getRawInputVar('bidPrice')
                ?? $exist['bidPrice'],
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
