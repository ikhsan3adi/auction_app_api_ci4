<?php

namespace Config;

use CodeIgniter\Config\BaseService;
use App\Models\UserModel;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    /*
     * public static function example($getShared = true)
     * {
     *     if ($getShared) {
     *         return static::getSharedInstance('example');
     *     }
     *
     *     return new \CodeIgniter\Example();
     * }
     */

    public static function verifyUser($userId, $username, $email)
    {
        $userDb = new UserModel;
        $userExist = $userDb->getUserByIdUsernameEmail($userId, $username, $email);

        if (!$userExist) return false;

        session()->setFlashdata([
            'user_id' => $userId,
            'username' => $username,
            'email' => $email
        ]);

        return true;
    }

    public static function fullImageURL($imageName): string
    {
        $baseUrl = 'http://' . $_SERVER['SERVER_NAME'] . '/ci4_online_auction_api' . '/public/images/item/';
        return $baseUrl . $imageName;
    }

    public static function fullProfileImageURL($imageName): string
    {
        $baseUrl = 'http://' . $_SERVER['SERVER_NAME'] . '/ci4_online_auction_api' . '/public/images/user/';
        return $baseUrl . $imageName;
    }
}
