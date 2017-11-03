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

  public static function getAccountId()
  {
    return self::getUser()['account_id'];
  }

  public static function getCompanyId()
  {
    return self::getUser()['company_id'];
  }

  public static function getRoleId()
  {
    return explode(',', self::getUser()['role_ids']);
  }

  public static function getParentAccountId()
  {
    return self::getUser()['parent_account_id'];
  }

  public static function getParentAccountIdLevel()
  {
    return self::getParentAccountId() . '-' . self::getAccountId();
  }

}