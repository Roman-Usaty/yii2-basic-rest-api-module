<?php

namespace app\modules\api\v1\controllers;

use app\models\LoginForm;
use Throwable;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\db\StaleObjectException;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class SiteController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'login' => ['POST'],
                'index' => ['GET']
            ]
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

    public function actionIndex(): string
    {
        return 'This is the api action index';
    }

    /**
     * Метод входа пользователя
     *
     * @return string
     *
     * @throws Throwable
     * @throws InvalidConfigException
     * @throws Exception
     * @throws StaleObjectException
     * @throws BadRequestHttpException
     */
    public function actionLogin(): string
    {
        $model = new LoginForm();
        if ($model->load(Yii::$app->getRequest()->getBodyParams(), '') && $token = $model->auth()) {
            return $token;
        } else {
            throw new BadRequestHttpException($model->getReadableErrors());
        }
    }
}