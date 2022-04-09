<?php

namespace app\components;

use yii\web\Response;
use yii\rest\Controller;
use yii\filters\ContentNegotiator;

/**
 * 控制器基类
 * 实现接口统一输出功能
 */
class BaseController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ]
        ];

        return $behaviors;
    }

    /**
     * 统一输出器
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