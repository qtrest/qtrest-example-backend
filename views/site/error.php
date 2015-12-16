<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <p>
        Мда, что-то пошло не так =)
    </p>
    <p>
        Но не отчаивайтесь, предлагаем Вам перейти на главную страницу <a href="http://skid.kz">Skid.KZ</a>
    </p>

</div>
