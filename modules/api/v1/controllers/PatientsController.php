<?php

namespace app\modules\api\v1\controllers;

use app\models\Patient;
use app\models\PatientSearch;
use app\models\User;
use Yii;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

class PatientsController extends Controller
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'create' => ['POST'],
                'index' => ['GET']
            ]
        ];
        $behaviors['authenticator']['authMethods'] = [
            HttpBasicAuth::class,
            HttpBearerAuth::class,
        ];
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
            'acceptParams' => [
                'version' => '1.0'
            ]
        ];
        return $behaviors;
    }

    /**
     * @return array - возвращает массив пациентов.
     *                 Где ключ 'count' указывает на количество пациентов
     *                 а 'data' - сами пациенты
     *
     * @throws ForbiddenHttpException
     */
    public function actionIndex(): array
    {
        if (User::hasPermission('/patientss/index')) {
            $params = Yii::$app->request->get();
            $model = new PatientSearch();
            $dataProvider = $model->restSearch($params);

            return ['count' => $dataProvider->count,'data' => $dataProvider];
        }
        throw new ForbiddenHttpException('You are not allowed to access this page');
    }

    /**
     * @return array - Возвращает массив с id созданного пациента
     *
     * @throws BadRequestHttpException|ServerErrorHttpException|ForbiddenHttpException
     */
    public function actionCreate(): array
    {
        if (User::hasPermission('/patientss/create')) {
            $patient = new Patient();
            if (
                $patient->load(Yii::$app->request->post(), '')
            ) {
                $patient->created_by = Yii::$app->user->id;
                $patient->updated_by = Yii::$app->user->id;

                if (!Yii::$app->user->isSuperadmin) {
                    $patient->polyclinic_id = Yii::$app->user->polyclinic_id;
                }

                if ($patient->save()) {
                    Yii::$app->response->statusCode = 201;
                    return ['patientId' => $patient->id];
                } elseif (empty($patient->getErrors())) {
                    throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
                }
            }
            throw new BadRequestHttpException($patient->getReadableErrors());
        }
        throw new ForbiddenHttpException('You are not allowed to access this page');
    }
}