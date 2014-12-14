<?php

namespace app\modules\kupon\controllers;

use yii\web\Controller;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionFetchAll($pass)
    {
        if ($pass == 'kafeg') {
            $chocolife = new ChocolifeApi();
            $chocolife->initData();
            $chocolife->fetchAllCities();

            $blizzard = new BlizzardApi();
            $blizzard->initData();
            $blizzard->fetchAllCities();

            $kupiKupon = new KupiKuponApi();
            $kupiKupon->initData();
            $kupiKupon->fetchAllCities();
        } else {
            echo 'Access denied!';
        }
    }
}
