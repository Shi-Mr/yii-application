<?php

namespace app\filters;

use yii\filters\auth\AuthMethod;

/**
 * 用户认证 过滤器
 * 自定义过滤器
 */
class CustomizeAuth extends AuthMethod
{
    /**
     * 认证方法
     * @param $oUser
     * @param $oRequest
     * @param $oResponse
     * @return IdentityInterface|NULL
     */
    public function authenticate($oUser, $oRequest, $oResponse)
    {
        return null;
    }
}