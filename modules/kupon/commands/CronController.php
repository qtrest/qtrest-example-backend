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
 
class CronController extends Controller
{

    public function actionIndex($message = 'hello world from module')
    {
    	\Yii::info(__FUNCTION__, 'kupon');
        echo $message . "\n";
        \Yii::error('error test', 'kupon');
    }

    public function actionFetchbiglion()
    {
    	\Yii::info(__FUNCTION__, 'kupon');
        $bgl = new BiglionApi();
        $bgl->initData();
        $bgl->fetchAllCities();
        $bgl->updateAllCoupons();
        \Yii::info('complete', 'kupon');
    }

    public function actionFetchchocolife()
    {
    	\Yii::info(__FUNCTION__, 'kupon');
        $bgl = new ChocolifeApi();
        $bgl->initData();
        $bgl->fetchAllCities();
        $bgl->updateAllCoupons();
        \Yii::info('complete', 'kupon');
    }

    public function actionFetchblizzard()
    {
    	\Yii::info(__FUNCTION__, 'kupon');
        $bgl = new BlizzardApi();
        $bgl->initData();
        $bgl->fetchAllCities();
        $bgl->updateAllCoupons();
        \Yii::info('complete', 'kupon');
    }

    public function actionFetchkupikupon()
    {
    	\Yii::info(__FUNCTION__, 'kupon');
        $bgl = new KupiKuponApi();
        $bgl->initData();
        $bgl->fetchAllCities();
        $bgl->updateAllCoupons();
        \Yii::info('complete', 'kupon');
    }

    public function actionFetchmirkuponov()
    {
    	\Yii::info(__FUNCTION__, 'kupon');
        $bgl = new MirKuponovApi();
        $bgl->initData();
        $bgl->fetchAllCities();
        $bgl->updateAllCoupons();
        \Yii::info('complete', 'kupon');
    }

    public function actionFetchautokupon()
    {
    	\Yii::info(__FUNCTION__, 'kupon');
        $bgl = new MirKuponovApi();
        $bgl->initData();
        $bgl->fetchAllCities();
        $bgl->updateAllCoupons();
        \Yii::info('complete', 'kupon');
    }

    public function actionUpdateall()
    {
        \Yii::info(__FUNCTION__, 'kupon');

        $chocolife = new ChocolifeApi();
        \Yii::info('ChocolifeApi created', 'kupon');

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

        \Yii::info('complete', 'kupon');
    }
}
