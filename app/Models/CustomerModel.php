<?php
/**
 * Created by PhpStorm.
 * User: guohao
 * Date: 2017/8/26
 * Time: 下午12:41
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Util\ModelUtil;

use Log;


class CustomerModel extends Model
{
    protected static $tableName = 'customer';
    protected $table = 'customer';


    CONST ONE_LEVEL_AGENT_ROLE_ID = 7; //一级代理商
    CONST SECOND_LEVEL_AGENT_ROLE_ID = 8; //二级代理商
    CONST ALLIANCE_BUSINESS_ROLE_ID = 9; //加盟商

    //获取客户所有信息
    public static function customer_info($account_id) {
        $customerInfo = DB::table(self::$tableName)
            ->leftJoin('customer_info', 'customer.account_id', '=', 'customer_info.account_id')
            ->leftJoin('customer_finance', 'customer.account_id', '=', 'customer_finance.account_id')
            ->where('customer.account_id', '=', $account_id)
            ->first();
        if ($customerInfo) {
            if ($customerInfo->role_id == self::ONE_LEVEL_AGENT_ROLE_ID) {
                $customerInfo->role_id = '一级代理商';
            } else if ($customerInfo->role_id == self::SECOND_LEVEL_AGENT_ROLE_ID) {
                $customerInfo->role_id = '二级代理商';
            } else if ($customerInfo->role_id == self::ALLIANCE_BUSINESS_ROLE_ID) {
                $customerInfo->role_id = '加盟商';
            } else {
                $customerInfo->role_id = '未知';
            }
        }
        return $customerInfo;
    }

    public static function editCustomer($account_id, $customer, $customer_info, $customer_finance) {
        DB::beginTransaction();

        try {
            if (! empty($customer)) {
                DB::table('customer')->where('account_id', '=', $account_id)->update($customer);
            }

            if (! empty($customer_info)) {
                DB::table('customer_info')->where('account_id', '=', $account_id)->update($customer_info);
            }

            if (! empty($customer_finance)) {
                DB::table('customer_finance')->where('account_id', '=', $account_id)->update($customer_finance);
            }
            DB::commit();
            return true;

        } catch(\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return false;
        }
    }


    //获取客户的 客户信息  公司信息
    public static function getCustomerDetail($account_id) {
        return DB::table('customer')
            ->select(
                'company.logo',
                'company.company_name',
                'customer.customer_name',
                'customer_info.name'
            )
            ->leftJoin('company', 'company.company_id', '=', 'customer.company_id')
            ->leftJoin('customer_info', 'customer.account_id', '=', 'customer_info.account_id')
            ->where(['customer.account_id' => $account_id])
            ->first();
    }

}