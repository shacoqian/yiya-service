<?php
/**
 * Created by PhpStorm.
 * User: guohao
 * Date: 2017/8/27
 * Time: 下午4:53
 */

namespace App\Util;

use Illuminate\Contracts\Auth\Factory as Auth;

class UserUtil
{
  private static $user;

  public static function setUser(Auth $auth)
  {
    self::$user = $auth->user();
  }

  public static function getUser()
  {
    return self::$user;
  }

  public static function getOpenId()
  {
    return self::getUser()['open_id'];
  }

  public static function getSessionKey()
  {
    return self::getUser()['session_key'];
  }
}