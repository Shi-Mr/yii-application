<?php

namespace app\modules\v1\controllers;

use app\components\RestController;

class IndexController extends RestController
{
    public function actionIndex()
    {
        return $this->api->success(1234);
    }
}