<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use ijackua\sharelinks\ShareLinks;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CouponSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

if ( Yii::$app->controller->action->id == 'actual' ) {
    $this->title = 'Skid.KZ - все актуальные скидки и купоны всех городов Казахстана в одном месте! Более 6000 скидок и купонов! Все купоны с сайтов Chocolife.me, Blizzard.kz, MirKuponov.kz, AutoKupon.kz и других!';
} else if ( Yii::$app->controller->action->id == 'archive' ) {
    $this->title = 'Skid.KZ - Архив всех скидок всех городов Казахстана! Более 10000 актуальных и архивных купонов и скидок В Астане, Алмате и других городах Казахстана!';
}
$this->params['breadcrumbs'] = '';

?>
<div class="coupon-index">

    <h1><?php // echo Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

<!--    <p>
        <? echo Html::a(Yii::t('app', 'Create {modelClass}', ['modelClass' => 'Coupon',]), ['create'], ['class' => 'btn btn-success']);?>
    </p>
-->
    <?php
    
    $itemClass = 'coupon-item ';
    if (\Yii::$app->devicedetect->isMobile()) {
        $itemClass .= ' col-xs-12';
    } else {
        $itemClass .= ' col-xs-4';
    }
    
    echo ListView::widget([
		//'summary' => '',
        'dataProvider' => $dataProvider,
        'layout' => '{summary}<div class="block-items">{items}</div>{pager}',
        //'options' => ['class' => ''],
        'itemOptions' => ['class' => $itemClass],
        'itemView' => '_item',
    ]) ?>

    <?php
    //echo \ijackua\sharelinks\ShareLinks::widget(
    //[
    //'viewName' => '@app/views/mypath/shareLinks.php'   //custom view file for you links appearance
    //]);
    ?>

</div>
