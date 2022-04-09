<?php

namespace app\controllers;

use app\components\ApiResult;
use app\components\RestController;

class ErrorController extends RestController {

    public function actionError() {
        
         return $this->api->error(ApiResult::CODE_SYSTEM);
    }
}