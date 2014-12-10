<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Coupon */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="coupon-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'sourceServiceId')->textInput() ?>

    <?= $form->field($model, 'cityId')->textInput() ?>

    <?= $form->field($model, 'createTimestamp')->textInput() ?>

    <?= $form->field($model, 'lastUpdateDateTime')->textInput() ?>

    <?= $form->field($model, 'recordHash')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'shortDescription')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'longDescription')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'conditions')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'features')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'imagesLinks')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'timeToCompletion')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'mainImageLink')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'originalPrice')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'discountPercent')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'discountPrice')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'boughtCount')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'sourceServiceCategories')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'pageLink')->textInput(['maxlength' => 255]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
