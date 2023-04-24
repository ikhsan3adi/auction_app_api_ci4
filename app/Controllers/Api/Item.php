<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ImageModel;
use App\Models\ItemModel;
use CodeIgniter\API\ResponseTrait;
use Config\Services;

class Item extends BaseController
{
    use ResponseTrait;

    protected String $userId;

    public function __construct()
    {
        $this->userId = session()->getFlashdata('user_id');
    }

    public function index()
    {
        $db = new ItemModel;
        $items = $db->where(['user_id' => $this->userId])->findAll();

        if (!$items) {
            return $this->failNotFound('Items not found');
        }

        $imageDb = new ImageModel;
        $images = $imageDb->findAll();

        foreach ($items as $key1 => $value1) {
            $imageArray = [];
            foreach ($images as $key2 => $value2) {
                if ($value1['item_id'] == $value2['item_id']) {
                    array_push($imageArray, [
                        'id' => $value2['image_id'],
                        'url' => Services::fullImageURL($value2['image'])
                    ]);
                }
            }
            $items[$key1]['images'] = $imageArray != [] ? $imageArray : null;
        }

        $items = $this->tidyingResponseData($items, nested: TRUE);

        return $this->respond([
            'status' => 200,
            'messages' => ['success' => 'OK'],
            'data' => Services::arrayKeyToCamelCase($items, nested: true),
        ]);
    }

    public function show($id = null)
    {
        $db = new ItemModel;
        $item = $db->where(['item_id' => $id, 'user_id' => $this->userId])->first();

        if (!$item) {
            return $this->failNotFound('Item not found');
        }

        $imageDb = new ImageModel;
        $images = $imageDb->findAll();

        $imageArray = [];
        foreach ($images as $key2 => $value2) {
            if ($item['item_id'] == $value2['item_id']) {
                array_push($imageArray, [
                    'id' => $value2['image_id'],
                    'url' => Services::fullImageURL($value2['image'])
                ]);
            }
        }
        $item['images'] = $imageArray != [] ? $imageArray : null;

        $item = $this->tidyingResponseData($item);

        return $this->respond([
            'status' => 200,
            'messages' => ['success' => 'OK'],
            'data' => Services::arrayKeyToCamelCase($item, nested: false),
        ]);
    }

    public function create()
    {
        if (!$this->validate([
            // 'user_id'       => 'required|numeric',
            'itemName'     => 'required',
            'description'   => 'required',
            'initialPrice' => 'required|numeric',
        ])) {
            return $this->failValidationErrors(\Config\Services::validation()->getErrors());
        }

        $insert = [
            // 'user_id'       => $this->request->getVar('user_id'),
            'user_id'       => $this->userId,
            'item_name'     => $this->request->getVar('itemName'),
            'description'   => $this->request->getVar('description'),
            'initial_price' => $this->request->getVar('initialPrice'),
        ];

        $db = new ItemModel;
        $save  = $db->insert($insert);

        if (!$save) {
            return $this->failServerError(description: 'Failed to create item');
        }

        return $this->respondCreated([
            'status' => 200,
            'messages' => ['success' => 'OK']
        ]);
    }

    public function update($id = null)
    {
        if (!$this->validate([
            'userId'       => 'permit_empty|numeric',
            'itemName'     => 'permit_empty',
            'description'   => 'permit_empty',
            'initialPrice' => 'permit_empty|numeric',
        ])) {
            return $this->failValidationErrors(\Config\Services::validation()->getErrors());
        }

        $db = new ItemModel;
        $exist = $db->where(['item_id' => $id, 'user_id' => $this->userId])->first();

        if (!$exist) {
            return $this->failNotFound(description: 'Item not found');
        }

        $update = [
            // 'user_id' => $this->request->getRawInputVar('userId')
            //     ?? $exist['userId'],
            'item_name' => $this->request->getRawInputVar('itemName')
                ?? $exist['itemName'],
            'description' => $this->request->getRawInputVar('description')
                ?? $exist['description'],
            'initial_price' => $this->request->getRawInputVar('initialPrice')
                ?? $exist['initialPrice']
        ];

        $save = $db->update($id, $update);

        if (!$save) {
            return $this->failServerError(description: 'Failed to update item');
        }

        return $this->respondUpdated([
            'status' => 200,
            'messages' => [
                'success' => 'Item updated successfully'
            ]
        ]);
    }

    public function delete($id = null)
    {
        $db = new ItemModel;
        $exist = $db->where(['item_id' => $id, 'user_id' => $this->userId])->first();

        if (!$exist) return $this->failNotFound(description: 'Item not found');

        $delete = $db->delete($id);

        if (!$delete) return $this->failServerError(description: 'Failed to delete item');

        return $this->respondDeleted([
            'status' => 200,
            'messages' => ['success' => 'Item successfully deleted']
        ]);
    }


    private function tidyingResponseData(array $data, $nested = FALSE): array
    {
        $newArray = [];

        if ($nested) {
            foreach ($data as $key => $value) {
                $newArray[$key]['id'] = $value['item_id'];
                $newArray[$key]['user_id'] = $value['user_id'];
                $newArray[$key]['item_name'] = $value['item_name'];
                $newArray[$key]['description'] = $value['description'];
                $newArray[$key]['initial_price'] = $value['initial_price'];
                $newArray[$key]['created_at'] = $value['created_at'];
                $newArray[$key]['images'] = $value['images'];
            }
            return $newArray;
        }

        $newArray['id'] = $data['item_id'];
        $newArray['user_id'] = $data['user_id'];
        $newArray['item_name'] = $data['item_name'];
        $newArray['description'] = $data['description'];
        $newArray['initial_price'] = $data['initial_price'];
        $newArray['created_at'] = $data['created_at'];
        $newArray['images'] = $data['images'];

        return $newArray;
    }
}
