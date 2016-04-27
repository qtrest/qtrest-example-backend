<?php

namespace app\api\modules\v1\controllers;

use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use app\models\CouponCategories;

class CategoriesController extends ApiBaseController
{
    public $modelClass = 'app\models\CouponCategories';

    public function actions()
    {
        $actions = parent::actions();

        // отключить действия "delete" и "create"
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['index']);

        return $actions;
    }

    public function actionIndex(){
        $activeData = new ActiveDataProvider([
            'query' => CouponCategories::find()->where(['isActive' => 1]),
            'pagination' => [
                'defaultPageSize' => 2,
            ],
        ]);
        return $activeData;
    }
}
