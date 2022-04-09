<?php

namespace app\controllers;

use app\components\ApiResult;
use app\components\BaseController;

class ErrorController extends BaseController {

    public function actionError() {
         return $this->api->error(ApiResult::CODE_SYSTEM);
    }
}