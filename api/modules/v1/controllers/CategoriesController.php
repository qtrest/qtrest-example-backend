<?php

namespace app\api\modules\v1\controllers;

use yii\rest\ActiveController;

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

        return $actions;
    }
}
