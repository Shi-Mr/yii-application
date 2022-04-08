<?php

namespace app\components;

use yii\web\Controller;

class BaseController extends Controller
{
    /**
     * 成功
     */
    public function success()
    {
        $response = $this->asJson([
            'code' => ApiResult::CODE_SUCCESS,
            'msg' => ApiResult::$_aMsg[ApiResult::CODE_SUCCESS]]
        );
        $response->send();
    }

    /**
     * 错误
     * @param $iCode
     * @param $sMsg
     */
    public function error($iCode, $sMsg = '') {
        if (!in_array($iCode, ApiResult::$_aCode)) {
            throw new \InvalidArgumentException("状态码未定义");
        }
        if (empty($sMsg)) {
            $sMsg = ApiResult::$_aMsg[$iCode];
        }
        $response = $this->asJson(['code' => $iCode, 'msg' => $sMsg]);
        $response->send();
    }
}