<?php

namespace app\components;

use yii\filters\RateLimiter;

/**
 * 控制器基类
 * 实现用户访问速率控制功能
 */
class RateLimitController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['rateLimiter'] = [
            'class' => RateLimiter::className(),
            'user' => function() {
                return (new RateLimitIdentify)->getIdentity();
            }
        ];

        return $behaviors;
    }
}