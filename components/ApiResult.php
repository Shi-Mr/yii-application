<?php

namespace app\components;

use yii\base\InvalidParamException;

/**
 * 输出器 组件
 */
class ApiResult {

    /**
     * 状态码
     */
    CONST CODE_SUCCESS = 0;         // 成功

    CONST CODE_SYSTEM  = 5000;      // 资源不存在

    public static $_aCode = [
        self::CODE_SUCCESS,
        self::CODE_SYSTEM
    ];

    /**
     * 描述
     */
    public static $_aMsg = [
        self::CODE_SUCCESS => 'OK',
        self::CODE_SYSTEM => '请求资源错误！'
    ];

    /**
     * 成功
     * @param array $aData
     * @return array
     */
    public function success($aData = []) {
        $aRes = [
            'code' => self::CODE_SUCCESS,
            'msg'  => self::$_aMsg[self::CODE_SUCCESS]
        ];

        if (!empty($aData)) {
            $aRes['data'] = $aData;
        }

        return $aRes;
    }

    /**
     * 失败
     * @param $iCode 状态码
     * @param string $sMsg 描述
     * @return array
     */
    public function error($iCode, $sMsg = '') {
        if (!in_array($iCode, self::$_aCode)) {
            throw new InvalidParamException("未定义的状态码！");
        }

        if (empty($sMsg)) {
            $sMsg = self::$_aMsg[$iCode];
        }

        return [
            'code' => $iCode,
            'msg' => $sMsg
        ];
    }
}