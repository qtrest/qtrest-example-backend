<?php
/**
 * Created by PhpStorm.
 * User: Виталий
 * Date: 09.12.2014
 * Time: 19:35
 */

use yii\helpers\Html;
use yii\db\Query;

$query = new Query;
$serviceBaseUrl = $query->select('serviceBaseUrl')
    ->from('sourceService')
    ->where('id=:id', [':id' => $model->sourceServiceId])
    ->createCommand()
    ->queryScalar();
?>


    <div class="thumbnail">
        <div class="image-ratio" style="background-image:url('<?= $serviceBaseUrl . '/' . $model->mainImageLink; ?>')">
            <div class="coupon-content" style="display:block">
                <p class="coupon-caption"><?= Html::encode($model->title) ?>
                    <br/>
                <?= Html::encode($model->originalPrice) . ' / ' . Html::encode($model->discountPrice) . ' (' . Html::encode($model->discountPercent) . ')' ?></p>
                <div class="coupon-description">
                    <?= Html::encode($model->shortDescription) ?>
                </div>
            </div>
        </div>
    </div>
<!--    <?//= Html::a(Html::encode($model->title), ['view', 'id' => $model->id]) ?>-->

<!--    <div class="col-md-6">-->
<!--        <img src="http://placekitten.com/300/300" class="img-responsive portfolio_frontpage" alt="">-->
<!--        <div class="portfolio_description">-->
<!--            <h2>Heading</h2>-->
<!---->
<!--            <p>Some random tekst blablabla</p> <span class="read_more"></span>-->
<!---->
<!--        </div>-->
<!--    </div>-->
