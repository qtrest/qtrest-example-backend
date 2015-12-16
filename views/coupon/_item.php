<?php
/**
 * Created by PhpStorm.
 * User: Виталий
 * Date: 09.12.2014
 * Time: 19:35
 */

use yii\helpers\Html;
use yii\db\Query;
use app\components\Tools;
use yii\helpers\Url;

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

$cityName = $query->select('cityName')
    ->from('city')
    ->where('id=:id', [':id' => $model->cityId])
    ->createCommand()
    ->queryScalar();

$phpDate = strtotime( $model->createTimestamp );
$createDate = date( 'd.m.y', $phpDate );

$strippedBoughtCount = $model->boughtCount;
$strippedBoughtCount = trim(str_replace("Уже купили", "", $strippedBoughtCount));
$strippedBoughtCount = trim(str_replace("человек", "", $strippedBoughtCount));

//Дополнительная пост обработка и актуализация данных в БД.
if ($strippedBoughtCount != $model->boughtCount) {
    $model->boughtCount = $strippedBoughtCount;
    $model->save();
}

?>

    <div itemscope itemtype="http://schema.org/Product" class="thumbnail image-ratio-base" style="background-image:url('/img/skid_bg_2.jpg')">
        <div class="image-ratio" itemprop="image" style="background-image:url('<?= (substr_count($model->mainImageLink, 'http') > 0 ? ($model->mainImageLink) :($serviceBaseUrl . '/' . $model->mainImageLink)); ?>')">
            <span itemprop="brand" itemscope itemtype="http://schema.org/Brand"><span itemprop="name" class="label label-info span-right"><?= $serviceName . '<br/>' . $cityName; ?></span></span>
            <span itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating"><span itemprop="ratingCount" ><span itemprop="ratingValue" class="label label-warning span-left "><?= 'Купили: ' . ($strippedBoughtCount > '' ? $strippedBoughtCount : '?') . '</span></span><br/> ' . $createDate; ?></span>
            <div class="coupon-content" style="display:block">
                <p class="coupon-caption"><span itemprop="name"><?= Html::encode($model->title) ?></span><br/></p>
                <div itemprop="description" class="coupon-description">
                    <?= Html::encode($model->shortDescription) ?>
                    <div class="span-bottomright">
                        <a target="_BLANK" href="<?= Url::toRoute(['view', 'id' => $model->id]); ?>" class="btn btn-success btn-sm">i</a>

                        <?php if (Yii::$app->controller->action->id == 'actual'): ?>
                            <a target="_BLANK" href="<?php 
                            if(Tools::startsWith($model->pageLink, 'http://') || Tools::startsWith($model->pageLink, 'https://')) {
                                echo $model->pageLink;
                            } else {
                                echo $serviceBaseUrl . $model->pageLink;
                            }
                            ?>" class="btn btn-info btn-sm">Купить</a>
                        <?php endif; ?>
                    </div>
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
