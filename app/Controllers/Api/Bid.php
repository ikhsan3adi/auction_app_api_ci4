<?php

namespace App\Controllers\Api;

use App\Models\AuctionModel;
use App\Models\BidModel;
use App\Models\UserModel;
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

    // Basic CRUD operation

    public function index()
    {
        $db = new BidModel;
        $bids = $db->getBid();

        if (!$bids) {
            return $this->failNotFound('Bids not found');
        }

        $userDb = new UserModel;

        foreach ($bids as $key => $value) {
            if ($value['profile_image']) {
                $bids[$key]['profile_image'] = Services::fullImageURL($value['profile_image']);
            }

            $bids[$key]['bidder'] = $userDb->getUser(id: $value['user_id'] ?? -69);

            $bids[$key]['mine'] = $bids[$key]['user_id'] == $this->userId;
        }

        return $this->respond([
            'status' => 200,
            'messages' => ['success' => 'OK'],
            'data' => $bids,
        ]);
    }

    public function showBids($auctionId = null)
    {
        $db = new BidModel;
        $bids = $db->getBid(where: ['auction_id' => $auctionId]);

        if ($bids) {
            $userDb = new UserModel;

            foreach ($bids as $key => $value) {
                $bids[$key]['bidder'] = $userDb->getUser(id: $value['user_id'] ?? -69);

                if ($bids[$key]['bidder']['profile_image']) {
                    $bids[$key]['profile_image'] = Services::fullImageURL($bids[$key]['bidder']['profile_image']);
                } else {
                    $bids[$key]['profile_image'] = null;
                }

                $bids[$key]['mine'] = $bids[$key]['user_id'] == $this->userId;
            }
        }

        return $this->respond([
            'status' => 200,
            'messages' => ['success' => 'OK'],
            'data' => $bids,
        ]);
    }

    public function show($id = null)
    {
        $db = new BidModel;
        $bid = $db->getBid($id);

        if (!$bid) {
            return $this->failNotFound('Bid not found');
        }

        $userDb = new UserModel;

        $bid['bidder'] = $userDb->getUser(id: $value['user_id'] ?? -69);

        if ($bid['bidder']['profile_image']) {
            $bid['bidder']['profile_image'] = Services::fullImageURL($bid['bidder']['profile_image']);
        }

        return $this->respond([
            'status' => 200,
            'messages' => ['success' => 'OK'],
            'data' => $bid,
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

        $auctionDb = new AuctionModel;
        $checkAuction = $auctionDb->find($this->request->getVar('auction_id'));

        if (!$checkAuction) {
            return $this->failNotFound(description: 'Failed to place bid, auction not found');
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
