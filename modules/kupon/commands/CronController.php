<?php

namespace app\modules\kupon\commands;
 
use yii\console\Controller;
use yii\helpers\Console;

use app\modules\kupon\parser\ChocolifeApi as ChocolifeApi;
use app\modules\kupon\parser\BlizzardApi as BlizzardApi;
use app\modules\kupon\parser\KupiKuponApi as KupiKuponApi;
use app\modules\kupon\parser\MirKuponovApi as MirKuponovApi;
use app\modules\kupon\parser\AutoKuponApi as AutoKuponApi;
use app\modules\kupon\parser\BiglionApi as BiglionApi;
use app\modules\kupon\parser\BeSmartApi as BeSmartApi;
 
class CronController extends Controller
{

    public function actionIndex($message = 'hello world from module')
    {
    	\Yii::info(__FUNCTION__, 'kupon');
        echo $message . "\n";
        \Yii::error('error test', 'kupon');
    }

    public function actionBiglion()
    {
    	\Yii::info(__FUNCTION__, 'kupon');
        $api = new BiglionApi();
        $api->initData();
        $api->fetchAllCities();
        $api->updateAllCoupons();
        \Yii::info('complete', 'kupon');
    }

    public function actionChocolife()
    {
    	\Yii::info(__FUNCTION__, 'kupon');
        $api = new ChocolifeApi();
        $api->initData();
        $api->fetchAllCities();
        $api->updateAllCoupons();
        \Yii::info('complete', 'kupon');
    }

    public function actionBlizzard()
    {
    	\Yii::info(__FUNCTION__, 'kupon');
        $api = new BlizzardApi();
        $api->initData();
        $api->fetchAllCities();
        $api->updateAllCoupons();
        \Yii::info('complete', 'kupon');
    }

    public function actionKupikupon()
    {
    	\Yii::info(__FUNCTION__, 'kupon');
        $api = new KupiKuponApi();
        $api->initData();
        $api->fetchAllCities();
        $api->updateAllCoupons();
        \Yii::info('complete', 'kupon');
    }

    public function actionMirkuponov()
    {
    	\Yii::info(__FUNCTION__, 'kupon');
        $api = new MirKuponovApi();
        $api->initData();
        $api->fetchAllCities();
        $api->updateAllCoupons();
        \Yii::info('complete', 'kupon');
    }

    public function actionAutokupon()
    {
    	\Yii::info(__FUNCTION__, 'kupon');
        $api = new MirKuponovApi();
        $api->initData();
        $api->fetchAllCities();
        $api->updateAllCoupons();
        \Yii::info('complete', 'kupon');
    }
    
    public function actionBesmartkz()
    {
    	\Yii::info(__FUNCTION__, 'kupon');
        $api = new BeSmartApi();
        $api->initData();
        $api->fetchAllCities();
        $api->updateAllCoupons();
        \Yii::info('complete', 'kupon');
    }

    public function actionUpdate()
    {
        \Yii::info(__FUNCTION__, 'kupon');

        $chocolife = new ChocolifeApi();
        $chocolife->updateAllCoupons();
        \Yii::info('ChocolifeApi Success!', 'kupon');

        $blizzard = new BlizzardApi();
        $blizzard->updateAllCoupons();
        \Yii::info('BlizzardApi Success!', 'kupon');

        $kupiKupon = new KupiKuponApi();
        $kupiKupon->updateAllCoupons();
        \Yii::info('KupiKuponApi Success!', 'kupon');

        $mirKuponov = new MirKuponovApi();
        $mirKuponov->updateAllCoupons();
        \Yii::info('MirKuponovApi Success!', 'kupon');

        $autoKupon = new AutoKuponApi();
        $autoKupon->updateAllCoupons();
        \Yii::info('AutoKuponApi Success!', 'kupon');
        
        $besmart = new BeSmartApi();
        $besmart->updateAllCoupons();
        \Yii::info('BeSmartApi Success!', 'kupon');

        \Yii::info('complete', 'kupon');
    }
}
