<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CouponSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Coupons');
$this->params['breadcrumbs'] = '';

?>
<div class="coupon-index">

    <h1><?php // echo Html::encode($this->title) ?></h1>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

<!--    <p>
        <? echo Html::a(Yii::t('app', 'Create {modelClass}', ['modelClass' => 'Coupon',]), ['create'], ['class' => 'btn btn-success']);?>
    </p>
-->
    <?= ListView::widget([
		//'summary' => '',
        'dataProvider' => $dataProvider,
        //'options' => ['class' => ''],
        'itemOptions' => ['class' => 'coupon-item col-xs-4'],
        'itemView' => '_item',
    ]) ?>

</div>
