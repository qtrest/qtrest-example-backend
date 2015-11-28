<?php

/* @var $this yii\web\View */
use kartik\switchinput\SwitchInput;
use yii\bootstrap\Modal;
use yii\grid\GridView;
$this->title = 'Skid.KZ - Статистика';

Modal::begin([
    //'headerOptions' => ['id' => 'statisticsModal'],
    'header' => '<h2>Статистика</h2>',
    'footer' => '<button type="button" class="btn btn-primary" data-dismiss="modal">Закрыть</button> ',
    'id' => 'statModal',
    'size' => 'modal-lg',
    //keeps from closing modal with esc key or by clicking out of the modal.
    // user must click cancel or X to close
    //'clientOptions' => ['backdrop' => 'static', 'keyboard' => FALSE]
]);
echo SwitchInput::widget([
    'name'=>'statSwitch',
    'value' => true,
    'pluginOptions'=>[
        'onText'=>'Новые',
        'offText'=>'Архивные'
    ],
    'pluginEvents' => [
        'switchChange.bootstrapSwitch' => 'function(event, state) {
            $("#gridNew").toggle();
            $("#gridArchive").toggle();
        }',
    ]]);
Modal::end();
