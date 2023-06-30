<?php

namespace App\Controllers\Api;

use App\Models\AuctionModel;
use App\Models\BidModel;
use App\Models\ImageModel;
use App\Models\ItemModel;
use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Config\Services;

class Auction extends ResourceController
{
    use ResponseTrait;

    protected String $userId;

    public function __construct()
    {
        $session = \Config\Services::session();
        $this->userId = $session->getFlashdata('user_id');
    }

    // Basic CRUD operation

    public function index()
    {
        $db = new AuctionModel;
        $auctions = $db->getAuction(page: intval($this->request->getGet('page')));

        if (!$auctions) {
            return $this->failNotFound('Auctions not found');
        }

        $imageDb = new ImageModel;
        $bidDb = new BidModel;
        $userDb = new UserModel;

        foreach ($auctions as $key1 => $value1) {
            $imageArray = $imageDb->where(['item_id' => $value1['item_id']])->findAll();

            if ($imageArray) {
                foreach ($imageArray as $key2 => $value2) {
                    $auctions[$key1]['images'][$key2]['image'] = Services::fullImageURL($value2['image']);
                }
            }

            $auctions[$key1]['bid_count'] = count($bidDb->getBid(where: ['auction_id' => $auctions[$key1]['auction_id']]));

            $auctions[$key1]['author'] = $userDb->getUser(id: $value1['user_id'] ?? -69);

            $auctions[$key1]['winner'] = $userDb->getUser(id: $value1['winner_user_id'] ?? -69);
        }

        return $this->respond([
            'status' => 200,
            'messages' => ['success' => 'OK'],
            'data' => $auctions,
        ]);
    }

    public function show($id = null)
    {
        $db = new AuctionModel;
        $auction = $db->getAuction($id, allStatus: true);

        if (!$auction) {
            return $this->failNotFound('Auction not found');
        }

        $userDb = new UserModel;

        $auction['author'] = $userDb->getUser(id: $auction['user_id'] ?? -69);

        $auction['winner'] = $userDb->getUser(id: $auction['winner_user_id'] ?? -69);

        $imageDb = new ImageModel;

        $imageArray = $imageDb->where(['item_id' => $auction['item_id']])->findAll();

        if ($imageArray) {
            foreach ($imageArray as $key2 => $value2) {
                $auction['images'][$key2]['image'] = Services::fullImageURL($value2['image']);
            }
        }

        return $this->respond([
            'status' => 200,
            'messages' => ['success' => 'OK'],
            'data' => $auction,
        ]);
    }

    public function create()
    {
        if (!$this->validate([
            'item_id'       => 'required|numeric',
            'date_completed'=> 'permit_empty|valid_date',
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
            'date_completed'=> $this->request->getVar('date_completed'),
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
        $exist = $db->getAuction(
            $id,
            where: ['items.user_id' => $this->userId]
        );

        if (!$exist) {
            return $this->failNotFound(description: 'Auction not found');
        }

        $update = [
            'status' => $this->request->getRawInputVar('status') ?? $exist['status'],
        ];

        $db = new AuctionModel;
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

    // Additional operation
    /** Get user auction */
    public function myAuctions()
    {
        $db = new AuctionModel;
        $auctions = $db->getAuction(
            where: ['items.user_id' => $this->userId],
            allStatus: true
        );

        if (!$auctions) {
            return $this->failNotFound('Auctions not found');
        }

        $imageDb = new ImageModel;
        $bidDb = new BidModel;
        $userDb = new UserModel;

        foreach ($auctions as $key1 => $value1) {
            $imageArray = $imageDb->where(['item_id' => $value1['item_id']])->findAll();

            if ($imageArray) {
                foreach ($imageArray as $key2 => $value2) {
                    $auctions[$key1]['images'][$key2]['image'] = Services::fullImageURL($value2['image']);
                }
            }

            $auctions[$key1]['bid_count'] = count($bidDb->getBid(where: ['auction_id' => $auctions[$key1]['auction_id']]));

            $auctions[$key1]['author'] = $userDb->getUser(id: $value1['user_id'] ?? -69);

            $auctions[$key1]['winner'] = $userDb->getUser(id: $value1['winner_user_id'] ?? -69);
        }

        return $this->respond([
            'status' => 200,
            'messages' => ['success' => 'OK'],
            'data' => $auctions,
        ]);
    }

    /** Get user bid  */
    public function myBids()
    {
        $db = new AuctionModel;
        $auctions = $db->getBidAuctions($this->userId);

        $db = new BidModel;
        $bids = $db->getBid(where: ['user_id' => $this->userId]);

        $imageDb = new ImageModel;
        $userDb = new UserModel;

        $newData = [];

        foreach ($auctions as $key1 => $value1) {
            $_bids = [];

            foreach ($bids as $key2 => $value2) {
                if ($value2['auction_id'] == $value1['auction_id']) array_push($_bids, $value2);
            }

            $newData[$key1]['auction'] = $value1;


            $imageArray = $imageDb->where(['item_id' => $value1['item_id']])->findAll();

            if ($imageArray) {
                foreach ($imageArray as $key2 => $value2) {
                    $newData[$key1]['auction']['images'][$key2]['image'] = Services::fullImageURL($value2['image']);
                }
            }

            $newData[$key1]['auction']['author'] = $userDb->getUser(id: $value1['user_id'] ?? -69);

            $newData[$key1]['auction']['winner'] = $userDb->getUser(id: $value1['winner_user_id'] ?? -69);

            $newData[$key1]['bids'] = $_bids;
        }

        return $this->respond([
            'status' => 200,
            'messages' => ['success' => 'OK'],
            'data' => $newData,
        ]);
    }

    /** Get single user auction */
    public function showMyAuction($id = null)
    {
        $db = new AuctionModel;
        $auction = $db->getAuction(
            $id,
            where: ['items.user_id' => $this->userId],
            allStatus: true
        );

        if (!$auction) {
            return $this->failNotFound('Auction not found');
        }

        $imageDb = new ImageModel;

        $imageArray = $imageDb->where(['item_id' => $auction['item_id']])->findAll();

        if ($imageArray) {
            foreach ($imageArray as $key2 => $value2) {
                $auction['images'][$key2]['image'] = Services::fullImageURL($value2['image']);
            }
        }

        return $this->respond([
            'status' => 200,
            'messages' => ['success' => 'OK'],
            'data' => $auction,
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
