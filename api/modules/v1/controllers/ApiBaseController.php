<?php

namespace app\api\modules\v1\controllers;

use yii\rest\ActiveController;

//Auth
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;

class ApiBaseController extends ActiveController
{
	//auth
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
        ];

        //authentication
        // $behaviors['authenticator'] = [
        //     'class' => QueryParamAuth::className(),
        // ];

        //only json
        // $behaviors['bootstrap'] = [
        //     'class' => ContentNegotiator::className(),
        //     'formats' => [
        //         'application/json' => Response::FORMAT_JSON,
        //     ],
        // ];  
        return $behaviors;
    }
}
