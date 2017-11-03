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

class RoleModel extends Model {

    protected $table = 'role';


    //获取所有的角色和权限列表
    public static function getRoles($company_id) {

        return DB::select('
                select 
                    a.*, b.* 
                from 
                    role a 
                LEFT JOIN 
                    permission b 
                on 
                    a.role_id = b.role_id 
                where 
                    a.company_id = ? ' ,[ $company_id]
        );
    }

}