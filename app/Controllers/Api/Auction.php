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
        $this->userId = session()->getFlashdata('user_id');
    }

    // Basic CRUD operation

    public function index()
    {
        $db = new AuctionModel;
        $auctions = $db->getAuction();

        if (!$auctions) {
            return $this->failNotFound('Auctions not found');
        }

        $imageDb = new ImageModel;
        $images = $imageDb->findAll();

        foreach ($auctions as $key1 => $value1) {
            $imageArray = [];
            foreach ($images as $key2 => $value2) {
                if ($value1['item_id'] == $value2['item_id']) {
                    array_push($imageArray, [
                        'url' => Services::fullImageURL($value2['image'])
                    ]);
                }
            }
            $auctions[$key1]['images'] = $imageArray != [] ? $imageArray : null;
        }

        $auctions = $this->tidyingResponseData($auctions, nested: TRUE);

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

        $userDb = new UserModel;
        $winnerUser = $userDb->getUser(id: $auction['winner_user_id']);

        $auction['winner'] = $winnerUser;

        $imageDb = new ImageModel;
        $images = $imageDb->findAll();

        $imageArray = [];
        foreach ($images as $key2 => $value2) {
            if ($auction['item_id'] == $value2['item_id']) {
                array_push($imageArray, [
                    'url' => Services::fullImageURL($value2['image'])
                ]);
            }
        }
        $auction['images'] = $imageArray != [] ? $imageArray : null;

        $auction = $this->tidyingResponseData($auction);

        return $this->respond([
            'status' => 200,
            'messages' => ['success' => 'OK'],
            'data' => Services::arrayKeyToCamelCase($auction, nested: false),
        ]);
    }

    public function create()
    {
        if (!$this->validate([
            'itemId'       => 'required|numeric',
            // 'user_id'       => 'required|numeric',
        ])) {
            return $this->failValidationErrors(\Config\Services::validation()->getErrors());
        }

        $itemDb = new ItemModel;
        $itemExist = $itemDb->where([
            'item_id' => $this->request->getVar('itemId'),
            'user_id' => $this->userId
        ])->first();

        if (!$itemExist) {
            return $this->failNotFound(description: 'Item not found');
        }

        $insert = [
            'item_id'       => $this->request->getVar('itemId'),
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
        $images = $imageDb->findAll();

        foreach ($auctions as $key1 => $value1) {
            $imageArray = [];
            foreach ($images as $key2 => $value2) {
                if ($value1['item_id'] == $value2['item_id']) {
                    array_push($imageArray, [
                        'url' => Services::fullImageURL($value2['image'])
                    ]);
                }
            }
            $auctions[$key1]['images'] = $imageArray != [] ? $imageArray : null;
        }

        $auctions = $this->tidyingResponseData($auctions, nested: TRUE);

        return $this->respond([
            'status' => 200,
            'messages' => ['success' => 'OK'],
            'data' => Services::arrayKeyToCamelCase($auctions, nested: true),
        ]);
    }


    /** Get user bid history  */
    public function myBids()
    {
        $db = new AuctionModel;
        $auctions = $db->getBidAuctions($this->userId);

        if (!$auctions) {
            return $this->failNotFound('Bids not found');
        }

        foreach ($auctions as $key => $value) {
            if ($value['image']) {
                $auctions[$key]['image'] = ['url' => Services::fullImageURL($value['image'])];
            }
        }

        $db = new BidModel;
        $bids = $db->getBid(where: ['users.user_id' => $this->userId]);

        if (!$bids) {
            return $this->failNotFound('Bids not found');
        }

        $newData = [];

        foreach ($auctions as $key1 => $value1) {
            $_bids = [];

            foreach ($bids as $key2 => $value2) {
                if ($value2['auction_id'] == $value1['auction_id']) {
                    array_push($_bids, [
                        'id' => $value2['bid_id'],
                        'auctionId' => $value2['auction_id'],
                        'bidPrice' => $value2['bid_price'],
                        'createdAt' => $value2['created_at']
                    ]);
                }
            }

            $newData[$key1]['auction']['id'] = $value1['auction_id'];
            $newData[$key1]['auction']['item_id'] = $value1['item_id'];
            $newData[$key1]['auction']['user_id'] = $value1['user_id'];
            $newData[$key1]['auction']['item_name'] = $value1['item_name'];
            $newData[$key1]['auction']['description'] = $value1['description'];
            $newData[$key1]['auction']['initial_price'] = $value1['initial_price'];
            $newData[$key1]['auction']['winner_user_id'] = $value1['winner_user_id'];
            $newData[$key1]['auction']['status'] = $value1['status'];
            $newData[$key1]['auction']['created_at'] = $value1['created_at'];
            $newData[$key1]['auction']['images'] = $value1['image'];

            $newData[$key1]['bids'] = $_bids;

            $newData[$key1] = Services::arrayKeyToCamelCase($newData[$key1], nested: true);
        }

        return $this->respond([
            'status' => 200,
            'messages' => ['success' => 'OK'],
            'data' => $newData,
            // 'data' => Services::arrayKeyToCamelCase($newData, nested: true),
        ]);
    }

    public function history()
    {
        $db = new AuctionModel;
        $auctions = $db->getAuction(
            status: 'closed',
            where: $this->request->getVar('userId')
                ? ['items.user_id' => $this->request->getVar('userId')] : NULL
        );

        if (!$auctions) {
            return $this->failNotFound('Auctions not found');
        }

        $imageDb = new ImageModel;
        $images = $imageDb->findAll();

        foreach ($auctions as $key1 => $value1) {
            $imageArray = [];
            foreach ($images as $key2 => $value2) {
                if ($value1['item_id'] == $value2['item_id']) {
                    array_push($imageArray, [
                        'url' => Services::fullImageURL($value2['image'])
                    ]);
                }
            }
            $auctions[$key1]['images'] = $imageArray != [] ? $imageArray : null;
        }

        $auctions = $this->tidyingResponseData($auctions, nested: TRUE);

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

        $imageDb = new ImageModel;
        $images = $imageDb->findAll();

        $imageArray = [];
        foreach ($images as $key2 => $value2) {
            if ($auction['item_id'] == $value2['item_id']) {
                array_push($imageArray, [
                    'url' => Services::fullImageURL($value2['image'])
                ]);
            }
        }
        $auction['images'] = $imageArray != [] ? $imageArray : null;

        $auction = $this->tidyingResponseData($auction);

        return $this->respond([
            'status' => 200,
            'messages' => ['success' => 'OK'],
            'data' => Services::arrayKeyToCamelCase($auction, nested: false),
        ]);
    }

    public function setWinner($id)
    {
        if (!$this->validate([
            'bidId'   => 'required|numeric',
        ])) {
            return $this->failValidationErrors(\Config\Services::validation()->getErrors());
        }

        $bidId = $this->request->getRawInputVar('bidId');

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

    private function tidyingResponseData(array $data, $nested = FALSE): array
    {
        $newArray = [];

        if ($nested) {
            foreach ($data as $key => $value) {
                $newArray[$key]['id'] = $value['auction_id'];
                $newArray[$key]['item_id'] = $value['item_id'];
                $newArray[$key]['author'] = [
                    'id' => $value['user_id'],
                    'username' => $value['username'],
                    'name' => $value['name'],
                    'email' => $value['email'],
                    'phone' => $value['phone'],
                    'profileImageUrl' => $value['profile_image'],
                ];
                $newArray[$key]['item_name'] = $value['item_name'];
                $newArray[$key]['description'] = $value['description'];
                $newArray[$key]['initial_price'] = $value['initial_price'];
                $newArray[$key]['final_price'] = $value['final_price'];
                $newArray[$key]['winner'] = [
                    'id' => $value['winner']['user_id'],
                    'username' => $value['winner']['username'],
                    'name' => $value['winner']['name'],
                    'email' => $value['winner']['email'],
                    'phone' => $value['winner']['phone'],
                    'profileImageUrl' => $value['winner']['profile_image'],
                ];
                $newArray[$key]['status'] = $value['status'];
                $newArray[$key]['created_at'] = $value['created_at'];
                $newArray[$key]['images'] = $value['images'];
            }
            return $newArray;
        }

        $newArray['id'] = $data['auction_id'];
        $newArray['item_id'] = $data['item_id'];
        $newArray['author'] = [
            'id' => $data['user_id'],
            'username' => $data['username'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'profileImageUrl' => $data['profile_image'],
        ];
        $newArray['item_name'] = $data['item_name'];
        $newArray['description'] = $data['description'];
        $newArray['initial_price'] = $data['initial_price'];
        $newArray['final_price'] = $data['final_price'];
        $newArray['winner'] = [
            'id' => $data['winner']['user_id'],
            'username' => $data['winner']['username'],
            'name' => $data['winner']['name'],
            'email' => $data['winner']['email'],
            'phone' => $data['winner']['phone'],
            'profileImageUrl' => $data['winner']['profile_image'],
        ];
        $newArray['status'] = $data['status'];
        $newArray['created_at'] = $data['created_at'];
        $newArray['images'] = $data['images'];

        return $newArray;
    }
}
