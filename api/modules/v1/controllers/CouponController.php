<?php
namespace app\api\modules\v1\controllers;
//Formato json
use yii\filters\ContentNegotiator;
use yii\web\Response;
//Segurança
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;

/**
 * Coupon Controller
 *
 * Coupon API controller
 *
 * The blank line above denotes a paragraph break
 */

class CouponController extends ActiveController
{

    // We are using the regular web app modules:
    public $modelClass = 'app\models\Coupon';

     public $serializer = [
        'class' => 'app\api\components\CouponSerializer',
        //'collectionEnvelope' => 'categories',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();

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

    public function actions()
    {
        $actions = parent::actions();

        // отключить действия "delete" и "create"
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);

        // настроить подготовку провайдера данных с помощью метода "prepareDataProvider()"
        //sample https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/rest-controllers.md

        $actions['index'] = [
            'class'       => 'app\api\components\SearchAction',
            'modelClass'  => $this->modelClass,
            'checkAccess' => [$this, 'checkAccess'],
            'params'      => \Yii::$app->request->get(),
            'pagination' => [
                $pageSizeLimit = [1, 2500]
            ],
            //'prepareDataProvider' => [$this, 'prepareDataProvider']
        ];

        return $actions;
    }

    public function prepareDataProvider()
    {
        return "1";
    }

    // public function verbs() {
    //     $verbs = [
    //         'search'   => ['GET']
    //     ];
    //     return array_merge(parent::verbs(), $verbs);
    // }
}
