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

$cats = implode(", ", explode(",",$model->sourceServiceCategories));

$categoriesCommand = $query->select('categoryName')
    ->from('categories')
    ->where('sourceServiceId=:sourceServiceId AND categoryIdentifier IN ( :categoryIdentifier )', 
        [':sourceServiceId' => $model->sourceServiceId, ':categoryIdentifier' => $cats])
    ->createCommand();

//TODO why is Database Exception - SQLSTATE[HY093]: Invalid parameter number: number of bound variables does not match number of tokens
//The SQL being executed was: SELECT `categoryName` FROM `categories` WHERE sourceServiceId=2 AND categoryIdentifier IN ( '101' )
//$categories = $categoriesCommand->queryColumn();

$this->title = 'Skid.kz - Все купоны Казахстана - ' . $serviceName . ' - ' . $model->title;
if ($model->isArchive == 0) {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Все актуальный купоны и скидки Казахстана'), 'url' => ['index']];
} else {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Все архивные купоны и скидки Казахстана'), 'url' => ['index']];
}
$this->params['breadcrumbs'][] =  $serviceName;
$this->params['breadcrumbs'][] =  $this->title;

//text blocks

$longDescription =  (trim($model->longDescription) > '') ? preg_replace('/href="(?!http:\/\/)([^"]+)"/', "href=\"".$model->sourceService->serviceBaseUrl."/\\1\"", $model->longDescription) : '<span class="not-set">(не задано)</span>';
$longDescription = preg_replace('/src="(?!http:\/\/)([^"]+)"/', "src=\"".$model->sourceService->serviceBaseUrl."/\\1\"", $longDescription);

$conditions =  (trim($model->conditions) > '') ? preg_replace('/href="(?!http:\/\/)([^"]+)"/', "href=\"".$model->sourceService->serviceBaseUrl."/\\1\"", $model->conditions) : '<span class="not-set">(не задано)</span>';  
$conditions = preg_replace('/src="(?!http:\/\/)([^"]+)"/', "src=\"".$model->sourceService->serviceBaseUrl."/\\1\"", $conditions);

$features =  (trim($model->features) > '') ? preg_replace('/href="(?!http:\/\/)([^"]+)"/', "href=\"".$model->sourceService->serviceBaseUrl."/\\1\"", $model->features) : '<span class="not-set">(не задано)</span>';
$features = preg_replace('/src="(?!http:\/\/)([^"]+)"/', "src=\"".$model->sourceService->serviceBaseUrl."/\\1\"", $features);

//images for carousel
$images = explode (",", $model->imagesLinks);
if (trim($model->imagesLinks) != 'empty') {
    foreach ($images as &$image) {
        $image = "<center><img src=\"".(substr_count($image, 'http') > 0 ? trim(($image)) :($serviceBaseUrl . '/' . trim($image)))."\" alt=\"".$serviceName." - ". $model->shortDescription ."\"/></center>";
    }
    $images[] = '<center><img src="' . (substr_count($model->mainImageLink, 'http') > 0 ? ($model->mainImageLink) :($serviceBaseUrl . '/' . trim($model->mainImageLink))) . '"/></center>';
} else {
    $images = '<center><img src="' . (substr_count($model->mainImageLink, 'http') > 0 ? ($model->mainImageLink) :($serviceBaseUrl . '/' . trim($model->mainImageLink))) . '"/></center>';
}

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
            //'title',
            'shortDescription',
            [                      // the owner name of the model
                'label' => 'Купонатор',
                'value' => $model->sourceService->serviceName,
            ],
            [                      // the owner name of the model
                'label' => 'Город',
                'value' => $model->city->cityName,
            ],
            [
                'label' => 'Актуальное предложение',
                'value' => ($model->isArchive == 0 ? 'да' : 'нет'),
                'format' => 'raw'
            ],
            [
                'label' => 'Оригинальная цена',
                'value' => (trim($model->originalPrice) > '' ? $model->originalPrice : '<span class="not-set">(не задано)</span>'),
                'format' => 'raw'
            ],
            //'originalPrice',
            'discountPercent',
            'discountPrice',
            'boughtCount',
            [
                'label' => 'Ссылка на страницу',
                'value' => '<a hreflang="ru" target="_BLANK" href="' . (substr_count($model->pageLink, 'http') > 0 ? ($model->pageLink) :($serviceBaseUrl . '/' . $model->pageLink)) . '">Перейти в магазин источник</a>',
                'format' => 'raw'
            ],
            [
                'label' => 'Изображения',
                'value' => (trim($model->imagesLinks) != 'empty') ? yii\bootstrap\Carousel::widget(['items'=>$images]) : $images,
                'format' => 'raw'
            ],
            // [
            //     'label' => 'Основное изображение',
            //     'value' => '<img src="' . (substr_count($model->mainImageLink, 'http') > 0 ? ($model->mainImageLink) :($serviceBaseUrl . '/' . $model->mainImageLink)) . '"/>',
            //     'format' => 'raw'
            // ],
            'id',
            'createTimestamp',
            'lastUpdateDateTime',
            //'recordHash',
            //'longDescription:html',
            [
                'label' => 'Подробное описание',
                'value' => $longDescription,
                'format' => 'raw'
            ],
            //'conditions:html',
            [
                'label' => 'Условия',
                'value' => $conditions,
                'format' => 'raw'
            ],
            //'features:html',
            [
                'label' => 'Особенности',
                'value' => $features,
                'format' => 'raw'
            ],
            //'imagesLinks:ntext',
            'timeToCompletion',
            'sourceServiceCategories',
            // [
            //     'label' => 'Категории',
            //     'value' => implode(", ", $categories),
            //     //'format' => 'raw'
            // ],
        ],
    ]) ?>

</div>
