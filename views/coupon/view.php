<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\db\Query;

use app\modules\kupon\parser\BaseApi as BaseApi;
use app\modules\kupon\parser\ChocolifeApi as ChocolifeApi;
use app\modules\kupon\parser\BlizzardApi as BlizzardApi;
use app\modules\kupon\parser\KupiKuponApi as KupiKuponApi;
use app\modules\kupon\parser\MirKuponovApi as MirKuponovApi;
use app\modules\kupon\parser\AutoKuponApi as AutoKuponApi;
use app\modules\kupon\parser\BiglionApi as BiglionApi;

/* @var $this yii\web\View */
/* @var $model app\models\Coupon */


$api = BaseApi::getApiObject($model->sourceService->id);
$api->updateCouponById($model->id);

$query = new Query;
$serviceBaseUrl = $query->select('serviceBaseUrl')
    ->from('sourceService')
    ->where('id=:id', [':id' => $model->sourceServiceId])
    ->createCommand()
    ->queryScalar();

$serviceName = $query->select('serviceName')
    ->from('sourceService')
    ->where('id=:id', [':id' => $model->sourceServiceId])
    ->createCommand()
    ->queryScalar();

$this->title = 'Skid.kz - Все купоны Казахстана - ' . $serviceName . ' - ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Все купоны'), 'url' => ['index']];
$this->params['breadcrumbs'][] =  $serviceName;
$this->params['breadcrumbs'][] =  $this->title;

?>
<div class="coupon-view">

    <h1><?= Html::encode($model->title) ?></h1>

    <?php echo \ijackua\sharelinks\ShareLinks::widget(
        [
            'viewName' => '@app/views/coupon/shareLinks.php'   //custom view file for you links appearance
        ]
    ); ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [                      // the owner name of the model
                'label' => 'Купонатор',
                'value' => $model->sourceService->serviceName,
            ],
            [                      // the owner name of the model
                'label' => 'Город',
                'value' => $model->city->cityName,
            ],
            'createTimestamp',
            'lastUpdateDateTime',
            //'recordHash',
            'title',
            'shortDescription',
            'longDescription:html',
            [
                'label' => 'Подробное описание',
                'value' => (trim($model->longDescription) > '') ? preg_replace('/href="(?!http:\/\/)([^"]+)"/', "href=\"".$model->sourceService->serviceBaseUrl."/\\1\"", $model->longDescription) : '<span class="not-set">(не задано)</span>',
                'format' => 'raw'
            ],
            'conditions:html',
            [
                'label' => 'Условия',
                'value' => (trim($model->conditions) > '') ? preg_replace('/href="(?!http:\/\/)([^"]+)"/', "href=\"".$model->sourceService->serviceBaseUrl."/\\1\"", $model->conditions) : '<span class="not-set">(не задано)</span>',
                'format' => 'raw'
            ],
            'features:html',
            [
                'label' => 'Особенности',
                'value' => (trim($model->features) > '') ? preg_replace('/href="(?!http:\/\/)([^"]+)"/', "href=\"".$model->sourceService->serviceBaseUrl."/\\1\"", $model->features) : '<span class="not-set">(не задано)</span>',
                'format' => 'raw'
            ],
            'imagesLinks:ntext',
            'timeToCompletion',
            [
                'label' => 'Основное изображение',
                'value' => '<img src="' . (substr_count($model->mainImageLink, 'http') > 0 ? ($model->mainImageLink) :($serviceBaseUrl . '/' . $model->mainImageLink)) . '"/>',
                'format' => 'raw'
            ],
            'originalPrice',
            'discountPercent',
            'discountPrice',
            'boughtCount',
            'sourceServiceCategories',
            [
                'label' => 'Ссылка на страницу',
                'value' => '<a target="_BLANK" href="' . (substr_count($model->pageLink, 'http') > 0 ? ($model->pageLink) :($serviceBaseUrl . '/' . $model->pageLink)) . '">Перейти в магазин источник</a>',
                'format' => 'raw'
            ],
        ],
    ]) ?>

</div>
