<?php

namespace App\Service;

use Illuminate\Support\Facades\DB;
use PFinal\Passport\Dao\Store;

/***
 *  管理端
 */
class ManageStore implements Store
{
    /**
     * 查询用户
     * @param array $condition
     * @return array|null
     */
    public function findUser(array $condition)
    {
        return DB::table('manage_user')->where($condition)->first()->toArray();
    }

    /**
     * 保存token
     *
     * @param array $tokenData
     */
    public function saveToken(array $tokenData)
    {
        DB::table('token')->insert($tokenData);
    }

    /**
     * 查询Token信息
     * @param string $token
     * @return array|null
     */
    public function findToken($token)
    {
        $value = DB::table('token')->where(['token' => $token])->first();

        if($value == null) {
            return null;
        }

        return json_decode(json_encode($value),true);
    }

    /**
     * 删除token
     * @param string $token
     * @return bool
     */
    public function deleteToken($token)
    {
        return DB::table('token')->where(['token' => $token])->delete() == 1;
    }

    /**
     * 删除过期token
     * @param string $time
     * @return int
     */
    public function deleteExpireToken($time)
    {
        return DB::table('token')->where("created_at<?", [$time])->delete();
    }

    /**
     * @param $platform
     * @param $appid
     * @param $openid
     * @return string|null
     */
    public function findUserIdByOpenid($platform, $appid, $openid)
    {
        $arr = DB::table('oauth')->where("platform=? AND appid=? AND openid=?", [$platform, $appid, $openid])
            ->first()->toArray();
        if ($arr == null) {
            return null;
        }

        return (string)$arr['user_id'];
    }
}
