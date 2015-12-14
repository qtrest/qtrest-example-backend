<?php
namespace app\api\modules\v1\controllers;

//Formato json
use yii\filters\ContentNegotiator;
use yii\web\Response;

//Segurança
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;

use yii\rest\ActiveController;

class CouponController extends ActiveController
{
    // We are using the regular web app modules:
    public $modelClass = 'app\models\Coupon';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // $behaviors['authenticator'] = [
        //     'class' => QueryParamAuth::className(),
        // ];
        $behaviors['bootstrap'] = [
            'class' => ContentNegotiator::className(),
        'formats' => [
            'application/json' => Response::FORMAT_JSON,
        ],
    ];  
        return $behaviors;  
    }
}
