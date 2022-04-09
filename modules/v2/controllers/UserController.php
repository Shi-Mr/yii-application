<?php

namespace app\modules\v2\controllers;

use app\components\RestController;

class UserController extends RestController
{
    public function actionView()
    {
        return $this->api->success(['name' => 'demo', 'age' => 12]);
    }
}