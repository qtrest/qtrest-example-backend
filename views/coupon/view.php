<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Coupon */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Coupons'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coupon-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'sourceServiceId',
            'cityId',
            'createTimestamp',
            'lastUpdateDateTime',
            'recordHash',
            'title',
            'shortDescription',
            'longDescription:ntext',
            'conditions:ntext',
            'features:ntext',
            'imagesLinks:ntext',
            'timeToCompletion',
            'mainImageLink',
            'originalPrice',
            'discountPercent',
            'discountPrice',
            'boughtCount',
            'sourceServiceCategories',
            'pageLink',
        ],
    ]) ?>

</div>
