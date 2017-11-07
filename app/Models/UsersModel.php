<?php
/**
 * Created by PhpStorm.
 * User: FengQian
 * Date: 2017/8/22
 * Time: 下午5:08
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UsersModel extends Model {

    protected $table = 'users';

    //设置 open_id和session_key
    public static function setWxKeyInfo($open_id, $session_key) {
        $userInfo = self::getUserInfo($open_id);
        if ($userInfo) {
            self::updateUser(['open_id' => $open_id], ['session_key' => $session_key]);
        } else {
            self::create([
                'open_id' => $open_id,
                'session_key' => $session_key
            ]);
        }
        return true;
    }

    //获取用户信息
    public static function getUserInfo($open_id) {
        return self::where(['open_id' => $open_id])->first();
    }

    //修改用户信息
    public static function updateUser($where, $data) {
        return self::where($where)->update($data);
    }

}