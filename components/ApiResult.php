<?php

namespace app\components;

class ApiResult {

    /**
     * 状态码
     */
    CONST CODE_SUCCESS = 0;

    public static $_aCode = [
        self::CODE_SUCCESS
    ];

    /**
     * 描述
     */
    public static $_aMsg = [
        self::CODE_SUCCESS => 'OK'
    ];
}