<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use ijackua\sharelinks\ShareLinks;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CouponSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

if ( Yii::$app->controller->action->id == 'actual' ) {
    $this->title = 'Skid.KZ - все актуальные скидки и купоны всех городов Казахстана в одном месте! Более 6000 скидок и купонов! Все купоны с сайтов Chocolife.me, Blizzard.kz, MirKuponov.kz, AutoKupon.kz и других! гих городах Казахстана! Skid.KZ - все актуальные скидки и купоны всех городов Казахстана в одном месте! Более 6000 скидок и купонов! Все купоны с сайтов Chocolife.me, Blizzard.kz, MirKuponov.kz, AutoKupon.kz и других! Все купоны и скидки в городах Алматы, Астана, Актау, Актобе, Атырау, Балхаш, Жезказган, Караганда, Кокшетау, Костанай, Кызылорда, Павлодар, Петропавловск, Рудный, Семей, Талдыкорган, Тараз, Темиртау, Туркестан, Уральск, Усть-Каменогорск, Шымкент, Экибастуз!';
} else if ( Yii::$app->controller->action->id == 'archive' ) {
    $this->title = 'Skid.KZ - Архив всех скидок всех городов Казахстана! Более 10000 актуальных и архивных купонов и скидок В Астане, Алмате и других городах Казахстана!гих городах Казахстана! Skid.KZ - все актуальные скидки и купоны всех городов Казахстана в одном месте! Более 6000 скидок и купонов! Все купоны с сайтов Chocolife.me, Blizzard.kz, MirKuponov.kz, AutoKupon.kz и других! Все купоны и скидки в городах Алматы, Астана, Актау, Актобе, Атырау, Балхаш, Жезказган, Караганда, Кокшетау, Костанай, Кызылорда, Павлодар, Петропавловск, Рудный, Семей, Талдыкорган, Тараз, Темиртау, Туркестан, Уральск, Усть-Каменогорск, Шымкент, Экибастуз!';
}
$this->params['breadcrumbs'] = '';

?>

<div class="col-md-12" style="margin-bottom: 30px; text-align: justify;">
    <div class="col-md-8">
        <br/>
        Привет! На сайте Skid.KZ Вы можете найти купоны всех популярных сервисов скидок (купонаторов) во всех городах Казахстана!
        <br/>
        Skid.KZ - это единый агрегатор скидок, нацеленный на максимальную простоту в представлении информации о скидках всем гражданам и гостям нашей страны!
        <br/>
        Для поиска любой доступной у Казахстанских купонаторов скидки, теперь достаточно просто зайти на сайт skid.kz!
    </div>
    <div class="col-md-4">
        <!-- Put this script tag to the <head> of your page -->
        <script type="text/javascript" src="//vk.com/js/api/openapi.js?121"></script>

        <script type="text/javascript">
          VK.init({apiId: 4797182, onlyWidgets: true});
        </script>

        <!-- Put this div tag to the place, where the Poll block will be -->
        <div id="vk_poll"></div>
        <script type="text/javascript">
        VK.Widgets.Poll("vk_poll", {width: "350"}, "206881005_df8c142231b4afa13c");
        </script>
    </div>
</div>



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
        if (\Yii::$app->devicedetect->isTablet()) {
            $itemClass .= ' col-xs-6';
        } else {
            $itemClass .= ' col-xs-12';
        }
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
