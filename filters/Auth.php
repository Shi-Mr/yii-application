<?php

namespace app\filters;

use yii\web\IdentityInterface;

/**
 * 用户认证 过滤器
 * 使用 yii\filters\auth下验证器进行验证
 */
class Auth implements IdentityInterface
{
    /**
     * yii\filters\auth目录下各认证过滤器实现authenticate方法
     * yii\web\user类中实现loginByAccessToken
     * 通过access-token获取认证信息
     * @param $sToken
     * @param $bType
     */
    public static function findIdentityByAccessToken($sToken, $bType = null)
    {
        return null;
    }

    /**
     * @param string|int $mId
     */
    public static function findIdentity($mId)
    {
        
    }

    public function getId()
    {
        
    }

    public function getAuthKey()
    {
        
    }

    /**
     * @param $sAuthKey
     */
    public function validateAuthKey($sAuthKey)
    {
        
    }
}