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

    public static function verifyUser($user_id, $username, $email)
    {
        $user_db = new UserModel;
        $user_exist = $user_db->getUserByIdUsernameEmail($user_id, $username, $email);

        if (!$user_exist) return false;

        session()->setFlashdata([
            'user_id' => $user_id,
            'username' => $username,
            'email' => $email
        ]);

        return true;
    }

    public static function arrayKeyToCamelCase($array, $nested = false)
    {
        $newArray = [];

        if ($nested) {
            foreach ($array as $key1 => $value1) {
                foreach ($value1 as $key2 => $value2) {
                    $newKey = preg_replace_callback(
                        '/_([^_])/',
                        function (array $m) {
                            return ucfirst($m[1]);
                        },
                        $key2
                    );

                    $newArray[$key1][$newKey] = $value2;
                }
            }
        } else {
            foreach ($array as $key => $value) {
                $newKey = preg_replace_callback(
                    '/_([^_])/',
                    function (array $m) {
                        return ucfirst($m[1]);
                    },
                    $key
                );

                $newArray[$newKey] = $value;
            }
        }

        return $newArray;
    }
}
