<?php
/**
 * Created by PhpStorm.
 * User: guohao
 * Date: 2017/9/4
 * Time: 上午11:28
 */

namespace App\Util;


class StringUtil
{
  public static function substr($string, $length = 10)
  {
    $string = strip_tags($string);
    $suffix = '';
    if (mb_strlen($string) > $length)
      $suffix = '...';
    return mb_substr($string, 0, $length) . $suffix;
  }

  /**
   * 生成订单号
   * @return string
   */
  public static function generateOrderNo()
  {
    return 'CY-' . date('YmdHismw') . mt_rand(10000, 10000000);
  }

  /**
   * 生成8位消费码
   * @return string
   */
  public static function generateConsumeCode()
  {
    $string = date('YmdHisw') . mt_rand(10000, 10000000);
    $string = str_shuffle($string);
    $string = str_shuffle($string);
    return substr($string, 2, 2) . substr($string, 5, 2) . substr($string, 15, 2) . substr($string, 20, 2);
  }

    //生成支付流水号
    public static function generatePayNo() {
        return date('YmdHis') . rand(1000, 9999);
    }
}