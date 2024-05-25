<?php

namespace app\api\controllers;

use Yii;
use yii\rest\Controller;

class PatientsController extends Controller
{

    public function actionIndex() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return ['success' => true, 'message' => 'success'];
    }
}