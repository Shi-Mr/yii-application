<?php

namespace app\controllers;

use yii\web\Controller;

class IndexController extends Controller {

    public function actionIndex() {
        $this->asJson(['code' => 0, 'msg' => 'Hello World!']);
        $this->response->send();
    }
}