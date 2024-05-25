<?php

namespace app\modules\v1\controllers;

use app\models\Patient;
use app\models\PatientSearch;
use Yii;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\rest\Serializer;
use yii\web\Response;

class PatientsController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['POST'],
                    'index' => ['GET']
                ]
            ],
            /*'authenticator' => [
                'class' => HttpBasicAuth::class,
            ],*/
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ]
            ],
        ];
    }

    public function actionIndex()
    {
        $params = Yii::$app->request->get();
        $model = new PatientSearch();
        $dataProvider = $model->restSearch($params);

        return ['count' => $dataProvider->count,'data' => $dataProvider];
    }

    public function actionCreate()
    {
        $patient = new Patient();
        if (
            $patient->load(Yii::$app->request->post(), '')
            && $patient->validate()
            && $patient->save()
        ) {
            $patient->created = date("Y-m-d H:i:s");
            $patient->updated = date("Y-m-d H:i:s");
            $patient->created_by = \Yii::$app->user->id;
            $patient->updated_by = \Yii::$app->user->id;
            $patient->birthday = $patient->birthday  ? date("Y-m-d", strtotime($patient->birthday)) : null;
            Yii::$app->response->statusCode = 201;
            return ['success' => true];
        }

        Yii::$app->response->statusCode = 400;
        return ['success' => false, 'data' => $patient->getErrors()];
    }
}