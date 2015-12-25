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

    // public function actionFetchall()
    // {
    //     $chocolife = new ChocolifeApi();
    //     $chocolife->initData();
    //     $chocolife->fetchAllCities();
    //     $chocolife->updateAllCoupons();
    //     echo 'ChocolifeApi Success!<br/>';

    //     $blizzard = new BlizzardApi();
    //     $blizzard->initData();
    //     $blizzard->fetchAllCities();
    //     $blizzard->updateAllCoupons();
    //     echo 'BlizzardApi Success!<br/>';

    //     $kupiKupon = new KupiKuponApi();
    //     $kupiKupon->initData();
    //     $kupiKupon->fetchAllCities();
    //     $kupiKupon->updateAllCoupons();
    //     echo 'KupiKuponApi Success!<br/>';

    //     $mirKuponov = new MirKuponovApi();
    //     $mirKuponov->initData();
    //     $mirKuponov->fetchAllCities();
    //     $mirKuponov->updateAllCoupons();
    //     echo 'MirKuponovApi Success!<br/>';

    //     $autoKupon = new AutoKuponApi();
    //     $autoKupon->initData();
    //     $autoKupon->fetchAllCities();
    //     $autoKupon->updateAllCoupons();
    //     echo 'AutoKuponApi Success!<br/>';
    // }

    // public function actionUpdateall()
    // {
    //     echo 'Start updating!<br/>';

    //     //ob_flush();

    //     \Yii::info('actionUpdateall start', 'kupon');

    //     $chocolife = new ChocolifeApi();
    //     \Yii::info('ChocolifeApi created', 'kupon');
    //     //\Yii::getLogger()->collect();
    //     $chocolife->updateAllCoupons();
    //     echo 'ChocolifeApi Success!<br/>';

    //     //ob_flush();

    //     \Yii::info('ChocolifeApi Success!', 'kupon');

    //     $blizzard = new BlizzardApi();
    //     $blizzard->updateAllCoupons();
    //     echo 'BlizzardApi Success!<br/>';

    //     //ob_flush();

    //     \Yii::info('BlizzardApi Success!', 'kupon');

    //     $kupiKupon = new KupiKuponApi();
    //     $kupiKupon->updateAllCoupons();
    //     echo 'KupiKuponApi Success!<br/>';

    //     //ob_flush();

    //     \Yii::info('KupiKuponApi Success!', 'kupon');

    //     $mirKuponov = new MirKuponovApi();
    //     $mirKuponov->updateAllCoupons();
    //     echo 'MirKuponovApi Success!<br/>';

    //     //ob_flush();

    //     \Yii::info('MirKuponovApi Success!', 'kupon');

    //     $autoKupon = new AutoKuponApi();
    //     $autoKupon->updateAllCoupons();
    //     echo 'AutoKuponApi Success!<br/>';

    //     //ob_flush();

    //     \Yii::info('AutoKuponApi Success!', 'kupon');

    //     \Yii::info('actionUpdateall end', 'kupon');

    //     return;
    // }

    // public function actionUpdateproxies()
    // {
    //     //if ($pass == 'kafeg') {
    //         $proxyHandler = new ProxyParser();
    //         $proxyList = $proxyHandler->googleProxy();
    //         foreach ($proxyList as $p) {
    //             $m = new Proxy();
    //             $m->ip = $p['ip'];
    //             $m->port = $p['port'];
    //             $m->save();
    //         }
    //         echo count($proxyList) . ' proxies added.';
    //     //}
    // }
    
    public function actionTestapi($serviceId, $testType = 1, $couponId = 0)
    {

        \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = \Yii::$app->response->headers;
        $headers->add('Content-Type', 'text/html; charset=utf-8');
        
        $api = NULL;
        switch ($serviceId) {
            case 1:
                $api = new ChocolifeApi();
                break;
            case 2:
                $api = new BlizzardApi();
                break;
            case 3:
                $api = new KupiKuponApi();
                break;
            case 4:
                $api = new MirKuponovApi();
                break;
            case 5:
                $api = new AutoKuponApi();
                break;
            default:
                echo "Not found!";
                return;
        }
        
        echo $api->getBaseUrl() . "<br/>";
        
        switch ($testType) {
            case 1:
                $api->testCities();
                break;
            case 2:
                $api->testCategories();
                break;
            case 3:
                $api->testCoupons(1, false); //almaty
                break;
            case 4:
                $api->testCoupons(2, false); //astana
                break;
            case 5:
                $lastCouponId = \Yii::$app->db->createCommand('SELECT id FROM coupon WHERE sourceServiceId=\''.$api->getSourceServiceId().'\' ORDER BY id DESC')->queryScalar();
                $api->testAdvancedCoupon($lastCouponId);
                break;
            case 6:
                $lastCouponId = \Yii::$app->db->createCommand('SELECT id FROM coupon WHERE sourceServiceId=\''.$api->getSourceServiceId().'\' ORDER BY id DESC')->queryScalar();
                $api->testAdvancedCoupon($lastCouponId, true);
                break;
            case 7:
                $lastCouponId = \Yii::$app->db->createCommand('SELECT id FROM coupon WHERE sourceServiceId=\''.$api->getSourceServiceId().'\' ORDER BY id ASC')->queryScalar();
                $api->testAdvancedCoupon($lastCouponId);
                break;
            case 8:
                $lastCouponId = \Yii::$app->db->createCommand('SELECT id FROM coupon WHERE sourceServiceId=\''.$api->getSourceServiceId().'\' ORDER BY id ASC')->queryScalar();
                $api->testAdvancedCoupon($lastCouponId, true);
                break;
            case 9:
                $api->testAdvancedCoupon($couponId, true);
                break;
            default:
                echo "Not found!";
                return;
        }
    }
}
