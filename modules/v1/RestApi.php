<?php

namespace app\modules\v1;

use Yii;
use yii\base\Module;

class RestApi extends Module
{
    public function init()
    {
        parent::init();
        Yii::$app->user->enableSession = false;
    }
}