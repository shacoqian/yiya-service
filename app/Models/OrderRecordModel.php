<?php
/**
 * 订单 - 收款记录表
 * User: guohao
 * Date: 2017/8/30
 * Time: 上午12:04
 */

namespace App\Models;


use App\Util\DateUtil;
use App\Util\ModelUtil;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\CustomerInfoModel;

use App\Util\UserUtil;

class OrderRecordModel extends Model
{

  const RECORD_TYPE_1 = '创建订货单';
  const RECORD_TYPE_2 = '订货单订单审核';
  const RECORD_TYPE_3 = '订货单财务审核';
  const RECORD_TYPE_4 = '订货单出库审核';
  const RECORD_TYPE_5 = '订货单发货';
  const RECORD_TYPE_6 = '添加收款记录';
  const RECORD_TYPE_7 = '完成';
  const RECORD_TYPE_8 = '作废';

  public static $record_type_content = [
      1 => '创建订货单，待订单审核',
      2 => '支付了订单',
      4 => '订货单财务审核通过,待出库',
      5 => '订货单出库，待发货',
      6 => '订货单发货',
      7 => '添加了收款记录',
      8 => '订货单完成',
      9 => '作废了订单'
  ];

  public static $record_type = [
      1 => '创建订单',
      2 => '支付订单',
      9 => '作废订单'
  ];

  public $table = 'order_record';

  /**
   * @param $accountId
   * @param $companyId
   * @param $orderId
   * @param $recordType
   * @param $recordContent
   * @return mixed
   */
  public static function create($orderId, $recordType)
  {
    $customerInfo = CustomerInfoModel::getCustomerInfo(UserUtil::getAccountId());
    $recordContent = self::$record_type_content[$recordType];
    $data['account_id'] = UserUtil::getAccountId();
    $data['company_id'] = UserUtil::getCompanyId();
    $data['order_id'] = $orderId;
    $data['record_type'] = self::$record_type[$recordType];
    $data['record_content'] = $recordContent;
    $data['created_time'] = DateUtil::now();
    $data['creator'] = $customerInfo ? $customerInfo->name : '';

    return self::insertGetId($data);
  }

  /**
   * @param $companyId
   * @param $orderId
   * @return mixed
   */
  public static function getList($companyId, $orderId)
  {
    return self
        ::where(['company_id' => $companyId, 'order_id' => $orderId])
        ->get();
  }
}