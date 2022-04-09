<?php

namespace app\controllers;

use app\components\RateLimitController;

/**
 * Demo
 */
class IndexController extends RateLimitController {

    public function actionIndex() {

        return $this->api->success();
    }
}