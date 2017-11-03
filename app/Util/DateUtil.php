<?php
/**
 * Created by PhpStorm.
 * User: guohao
 * Date: 2017/9/4
 * Time: 下午6:15
 */

namespace App\Util;
date_default_timezone_set('Asia/shanghai');


class DateUtil
{
  public static function now($format = 'Y-m-d H:i:s') {
    return date($format);
  }
}