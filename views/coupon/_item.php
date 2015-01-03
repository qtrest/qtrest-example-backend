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
$serviceName = $query->select('serviceName')
    ->from('sourceService')
    ->where('id=:id', [':id' => $model->sourceServiceId])
    ->createCommand()
    ->queryScalar();
?>


    <div class="thumbnail">
        <div class="image-ratio" style="background-image:url('<?= $serviceBaseUrl . '/' . $model->mainImageLink; ?>')">
            <span class="label label-info span-right"><?= $serviceName; ?></span>
            <span class="label label-warning span-left "><?= 'Купили: ' . $model->boughtCount; ?></span>
            <div class="coupon-content" style="display:block">
                <p class="coupon-caption"><?= Html::encode($model->title) ?>
                    <br/>
                </p>
                <div class="coupon-description">
                    <?= Html::encode($model->shortDescription) ?>
                    <a target="_BLANK" href="<?= $serviceBaseUrl . $model->pageLink; ?>" class="btn btn-info span-bottomright">Купить</a>
                </div>

            </div>
            <span class="label label-success span-full-down">
                    <?= ((($model->discountType == 'coupon') || ($model->discountType == 'freeCoupon') )
                        ? 'Купон: ' . Html::encode($model->originalCouponPrice)
                        : 'Цена: ' . (Html::encode($model->originalPrice) . '. Сертификат: ' . Html::encode($model->discountPrice))) . '. Скидка: ' . str_replace('%', '', Html::encode($model->discountPercent)) . '%'
                    ?>
            </span>
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
