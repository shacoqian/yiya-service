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

class AccountModel extends Model {

    protected $table = 'account';
    public static $tableName = 'account';

    protected $fillable = ['account_name', 'password', 'account_type', 'role_ids',
        'nick_name', 'creater', 'created_at', 'disabled', 'phone', 'salt',
        'company_id', 'email', 'qq'];

    //分页获取用户列表
    public static function getLists($company_id, $page, $size, $disbaled, $keywords = '') {
        $query = self::where(['disabled' => $disbaled, 'company_id' => $company_id]);
        if ($keywords) {
            $query->where(function($query) use ($keywords) {
                $query->where('account_name', 'like', '%'. $keywords .'%')
                    ->orWhere('phone', 'like', '%'. $keywords .'%')
                    ->orWhere('nick_name', 'like', '%'. $keywords .'%');
            });
        }

        $count = $query->count();
        $data = $query
            ->offset(($page-1) * $size)
            ->limit($size)
            ->orderBy('account_id', 'DESC')
            ->get()
            ->toArray();
        return [$count, $data];
    }

    //创建账号
    public static function create_account($data) {
        $data['salt'] = generate_string(4);
        $data['password'] = generate_password($data['password'], $data['salt']);
        return self::insertGetId($data);
    }

    //判断用户名是否存在
    public static function is_account_name_repeat($account_name) {
        return self::where(['account_name' => $account_name])->first();
    }

    public static function insert($data) {
      $id = DB::table('account')->insertGetId($data);

      return $id;
    }

}