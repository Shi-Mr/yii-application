<?php

namespace app\components;

use app\filters\CustomizeAuth;
use yii\web\Response;
use yii\rest\Controller;
use yii\filters\RateLimiter;
use yii\filters\ContentNegotiator;

/**
 * 控制器基类
 */
class RestController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        /**
         * 实现Json格式输出
         */
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ]
        ];

        /**
         * 实现用户认证
         */
        $behaviors['authenticator'] = [
            /*
             全部支持
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                HttpHeaderAuth::className(),
                QueryParamAuth::className()
            ]
            */

            /**
             * 支持一种
             * 'class' => QueryParamAuth::className()
             */
            
            /**
             * 自定义
             */
            'class' => CustomizeAuth::className(),
            'user' => function() {
                return new UserIdentify();
            }
        ];
        
        /**
         * 实现速率控制功能
         */
        $behaviors['rateLimiter'] = [
            'class' => RateLimiter::className(),
            'user'  => function() {
                return (new RateLimitIdentify())->identity;
            }
        ];

        return $behaviors;
    }

    /**
     * 输出器
     */
    public $api;

    /**
     * 实例化输出器
     */
    public function init()
    {
        parent::init();

        $this->api = new ApiResult();
    }
}