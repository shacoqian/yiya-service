<?php
/**
 * Created by PhpStorm.
 * User: FengQian
 * Date: 2017/9/5
 * Time: 下午2:26
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\FinanceBankAccount;
use App\Util\UserUtil;

/**
 * 公司相关
 * @tags 公司相关
 */

class CompanyController extends Controller {

    /**
     * @method get
     * @desc 获取银行卡列表
     * @param string $token token header
     * @path /api/company/bank/list
     */
    public function bankList(Request $request) {
        $company_id = UserUtil::getCompanyId();
        $data = FinanceBankAccount::where(['disabled' => 0, 'company_id' => $company_id])->get();
        return $this->success($data);
    }

   /**
    * @method get
    * @desc 获取默认银行卡
    * @param string $token token header
    * @path /api/company/default/bank
    */
   public function bankDefault() {
       $company_id = UserUtil::getCompanyId();
       $data = FinanceBankAccount::where(['is_default' => 1, 'company_id' => $company_id, 'disabled' => 0])->first();
       return $this->success($data == null ? [] : $data);
   }

}