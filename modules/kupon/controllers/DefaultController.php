<?php

namespace app\modules\kupon\controllers;

use yii\web\Controller;

use app\modules\kupon\parser\ChocolifeApi as ChocolifeApi;
use app\modules\kupon\parser\BlizzardApi as BlizzardApi;
use app\modules\kupon\parser\KupiKuponApi as KupiKuponApi;
use app\modules\kupon\parser\MirKuponovApi as MirKuponovApi;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionFetchall($pass)
    {
        if ($pass == 'kafeg') {
            $chocolife = new ChocolifeApi();
            $chocolife->initData();
            $chocolife->fetchAllCities();
            echo 'ChocolifeApi Success!<br/>';

            $blizzard = new BlizzardApi();
            $blizzard->initData();
            $blizzard->fetchAllCities();
            echo 'BlizzardApi Success!<br/>';

            $kupiKupon = new KupiKuponApi();
            $kupiKupon->initData();
            $kupiKupon->fetchAllCities();
            echo 'KupiKuponApi Success!<br/>';

            $mirKuponov = new MirKuponovApi();
            $mirKuponov->initData();
            $mirKuponov->fetchAllCities();
            echo 'MirKuponovApi Success!<br/>';

            $autoKupon = new AutoKuponApi();
            $autoKupon->initData();
            $autoKupon->fetchAllCities();
            echo 'AutoKuponApi Success!<br/>';

        } else {
            echo 'Access denied!';
        }
    }
}
