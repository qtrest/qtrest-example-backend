<?php

namespace app\modules\kupon;
use yii\base\BootstrapInterface;
use yii\base\Module as BaseModule;

class Kupon extends BaseModule implements BootstrapInterface
{
    public $controllerNamespace = 'app\modules\kupon\controllers';

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    public function bootstrap($app)
    {
        if ($app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'app\modules\kupon\commands';
        }
    }
}
