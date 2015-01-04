<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CouponSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

if ( Yii::$app->controller->action->id == 'actual' ) {
    $this->title = 'Skid.KZ - Актуальные';
} else if ( Yii::$app->controller->action->id == 'archive' ) {
    $this->title = 'Skid.KZ - Архив';
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
    <?= ListView::widget([
		//'summary' => '',
        'dataProvider' => $dataProvider,
        'layout' => '{summary}<div class="block-items">{items}</div>{pager}',
        //'options' => ['class' => ''],
        'itemOptions' => ['class' => 'coupon-item col-xs-4'],
        'itemView' => '_item',
    ]) ?>

</div>
