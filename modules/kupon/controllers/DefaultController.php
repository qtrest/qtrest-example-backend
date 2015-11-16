<?php

namespace app\modules\kupon\controllers;

use app\components\ProxyHandler;
use app\models\Proxy;
use app\modules\kupon\parser\ProxyParser;
use yii\web\Controller;

use app\modules\kupon\parser\ChocolifeApi as ChocolifeApi;
use app\modules\kupon\parser\BlizzardApi as BlizzardApi;
use app\modules\kupon\parser\KupiKuponApi as KupiKuponApi;
use app\modules\kupon\parser\MirKuponovApi as MirKuponovApi;
use app\modules\kupon\parser\AutoKuponApi as AutoKuponApi;
use app\modules\kupon\parser\BiglionApi as BiglionApi;

class DefaultController extends Controller
{
//    public function actionIndex()
//    {
//        return $this->render('index');
//    }

    public function actionFetchbiglion()
    {
        $bgl = new BiglionApi();
        $bgl->initData();
        $bgl->fetchAllCities();
        $bgl->updateAllCoupons();
    }

    public function actionFetchall()
    {
        //echo 'Access denied!';
        //return;
        //if ($pass == 'kafeg') {
            $chocolife = new ChocolifeApi();
            $chocolife->initData();
            $chocolife->fetchAllCities();
            $chocolife->updateAllCoupons();
            echo 'ChocolifeApi Success!<br/>';

            $blizzard = new BlizzardApi();
            $blizzard->initData();
            $blizzard->fetchAllCities();
            $blizzard->updateAllCoupons();
            echo 'BlizzardApi Success!<br/>';

            $kupiKupon = new KupiKuponApi();
            $kupiKupon->initData();
            $kupiKupon->fetchAllCities();
            $kupiKupon->updateAllCoupons();
            echo 'KupiKuponApi Success!<br/>';

            $mirKuponov = new MirKuponovApi();
            $mirKuponov->initData();
            $mirKuponov->fetchAllCities();
            $mirKuponov->updateAllCoupons();
            echo 'MirKuponovApi Success!<br/>';

            $autoKupon = new AutoKuponApi();
            $autoKupon->initData();
            $autoKupon->fetchAllCities();
            $autoKupon->updateAllCoupons();
            echo 'AutoKuponApi Success!<br/>';

        //} else {
        //    echo 'Access denied!';
        //}
    }

    public function actionUpdateall()
    {
        //echo 'Access denied!';
        //return;

        //if ($pass == 'kafeg') {
            $chocolife = new ChocolifeApi();
            $chocolife->updateAllCoupons();
            echo 'ChocolifeApi Success!<br/>';

            $blizzard = new BlizzardApi();
            $blizzard->updateAllCoupons();
            echo 'BlizzardApi Success!<br/>';

            $kupiKupon = new KupiKuponApi();
            $kupiKupon->updateAllCoupons();
            echo 'KupiKuponApi Success!<br/>';

            $mirKuponov = new MirKuponovApi();
            $mirKuponov->updateAllCoupons();
            echo 'MirKuponovApi Success!<br/>';

            $autoKupon = new AutoKuponApi();
            $autoKupon->updateAllCoupons();
            echo 'AutoKuponApi Success!<br/>';

        //} else {
        //    echo 'Access denied!';
        //}
    }

    public function actionUpdateproxies()
    {
        //if ($pass == 'kafeg') {
            $proxyHandler = new ProxyParser();
            $proxyList = $proxyHandler->googleProxy();
            foreach ($proxyList as $p) {
                $m = new Proxy();
                $m->ip = $p['ip'];
                $m->port = $p['port'];
                $m->save();
            }
            echo count($proxyList) . ' proxies added.';
        //}
    }
}
