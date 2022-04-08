<?php

namespace app\controllers;

use yii\web\Controller;

class ErrorController extends Controller {

    public function actionError() {
        $this->asJson(['code' => 1001, 'msg' => '访问页面不存在！']);
        $this->response->send();
    }
}