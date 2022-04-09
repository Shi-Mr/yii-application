<?php

namespace app\controllers;

use app\components\RestController;

class IndexController extends RestController {

    public function actionIndex() {

        return $this->api->success();
    }
}