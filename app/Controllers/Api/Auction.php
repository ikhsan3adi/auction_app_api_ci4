<?php

namespace App\Controllers\Api;

use App\Models\AuctionModel;
use App\Models\BidModel;
use App\Models\ItemModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Config\Services;

class Auction extends ResourceController
{
    use ResponseTrait;

    protected String $userId;

    public function __construct()
    {
        $this->userId = session()->getFlashdata('user_id');
    }

    public function index()
    {
        $db = new AuctionModel;
        $auctions = $db->getAllAuctions();

        if (!$auctions) {
            return $this->failNotFound('Auctions not found');
        }

        return $this->respond([
            'status' => 200,
            'messages' => ['success' => 'OK'],
            'data' => Services::arrayKeyToCamelCase($auctions, nested: true),
        ]);
    }

    public function show($id = null)
    {
        $db = new AuctionModel;
        $auction = $db->getAuction($id);

        if (!$auction) {
            return $this->failNotFound('Auction not found');
        }

        return $this->respond([
            'status' => 200,
            'messages' => ['success' => 'OK'],
            'data' => Services::arrayKeyToCamelCase($auction, nested: false),
        ]);
    }

    public function create()
    {
        if (!$this->validate([
            'item_id'       => 'required|numeric',
            // 'user_id'       => 'required|numeric',
        ])) {
            return $this->failValidationErrors(\Config\Services::validation()->getErrors());
        }

        $itemDb = new ItemModel;
        $itemExist = $itemDb->where([
            'item_id' => $this->request->getVar('item_id'),
            'user_id' => $this->userId
        ])->first();

        if (!$itemExist) {
            return $this->failNotFound(description: 'Item not found');
        }

        $insert = [
            'item_id'       => $this->request->getVar('item_id'),
            'user_id'       => $this->userId,
            'status'        => 'open',
        ];

        $db = new AuctionModel;
        $save  = $db->insert($insert);

        if (!$save) {
            return $this->failServerError(description: 'Failed to create auction');
        }

        return $this->respondCreated([
            'status' => 200,
            'messages' => ['success' => 'OK']
        ]);
    }

    public function update($id = null)
    {
        if (!$this->validate([
            'status'       => 'permit_empty|alpha_numeric',
        ])) {
            return $this->failValidationErrors(\Config\Services::validation()->getErrors());
        }

        $db = new AuctionModel;
        $exist = $db->getAuction($id);

        if (!$exist) {
            return $this->failNotFound(description: 'Auction not found');
        }

        $update = [
            'status' => $this->request->getRawInputVar('status') ?? $exist['status'],
        ];

        $save = $db->update($id, $update);

        if (!$save) {
            return $this->failServerError(description: 'Failed to update auction');
        }

        return $this->respondUpdated([
            'status' => 200,
            'messages' => [
                'success' => 'Auction updated successfully'
            ]
        ]);
    }

    public function delete($id = null)
    {
        $db = new AuctionModel;
        $exist = $db->getAuction($id);

        if (!$exist) return $this->failNotFound(description: 'Auction not found');

        $delete = $db->delete($id);

        if (!$delete) return $this->failServerError(description: 'Failed to delete auction');

        return $this->respondDeleted([
            'status' => 200,
            'messages' => ['success' => 'Auction successfully deleted']
        ]);
    }

    public function history()
    {
        $db = new AuctionModel;
        $auctions = $db->getAllAuctions(
            status: 'closed',
            where: $this->request->getVar('userId')
                ? ['items.user_id' => $this->request->getVar('userId')] : NULL
        );

        if (!$auctions) {
            return $this->failNotFound('Auctions not found');
        }

        return $this->respond([
            'status' => 200,
            'messages' => ['success' => 'OK'],
            'data' => Services::arrayKeyToCamelCase($auctions, nested: true),
        ]);
    }

    public function showHistory($id = null)
    {
        $db = new AuctionModel;
        $auction = $db->getAuction($id, status: 'closed');

        if (!$auction) {
            return $this->failNotFound('Auction not found');
        }

        return $this->respond([
            'status' => 200,
            'messages' => ['success' => 'OK'],
            'data' => Services::arrayKeyToCamelCase($auction, nested: false),
        ]);
    }

    public function setWinner($id)
    {
        if (!$this->validate([
            'bid_id'   => 'required|numeric',
        ])) {
            return $this->failValidationErrors(\Config\Services::validation()->getErrors());
        }

        $bidId = $this->request->getRawInputVar('bid_id');

        $bidDb = new BidModel;

        $bid = $bidDb->where(['bid_id' => $bidId])->first();

        if (!$bid) {
            return $this->failNotFound('Bid not found');
        }

        $db = new AuctionModel;

        $verifyAuction = $db->where([
            'auction_id' => $id,
            'user_id' => $this->userId
        ])->first();

        if (!$verifyAuction) {
            return $this->failForbidden('Access Forbidden');
        }

        $update = [
            'winner_user_id' => $bid['user_id'],
            'final_price'    => $bid['bid_price']
        ];

        $save = $db->update($id, $update);

        if (!$save) {
            return $this->failServerError(description: 'Failed to set auction winner');
        }

        return $this->respondUpdated([
            'status' => 200,
            'messages' => [
                'success' => 'Auction winner successfully added'
            ]
        ]);
    }

    public function close($id)
    {
        $db = new AuctionModel;

        $verifyAuction = $db->where([
            'auction_id' => $id,
            'user_id' => $this->userId
        ])->first();

        if (!$verifyAuction) {
            return $this->failForbidden('Access Forbidden');
        }

        $update = [
            'status' => 'closed',
        ];

        $save = $db->update($id, $update);

        if (!$save) {
            return $this->failServerError(description: 'Failed to set auction status');
        }

        return $this->respondUpdated([
            'status' => 200,
            'messages' => [
                'success' => 'Auction status successfully changed'
            ]
        ]);
    }
}
