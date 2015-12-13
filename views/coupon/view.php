<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\db\Query;

/* @var $this yii\web\View */
/* @var $model app\models\Coupon */


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
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Все упоны'), 'url' => ['index']];
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
            'conditions:html',
            'features:html',
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
