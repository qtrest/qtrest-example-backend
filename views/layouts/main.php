<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>

    <META name="description" CONTENT="Skid.KZ - единый агрегатор скидок в Казахстане! Все скидки на одном сайте! Skid.KZ - Архив всех скидок всех городов Казахстана! Более 10000 актуальных и архивных купонов и скидок В Астане, Алмате и других городах Казахстана! Skid.KZ - все актуальные скидки и купоны всех городов Казахстана в одном месте! Более 6000 скидок и купонов! Все купоны с сайтов Chocolife.me, Blizzard.kz, MirKuponov.kz, AutoKupon.kz и других! Все купоны и скидки в городах Алматы, Астана, Актау, Актобе, Атырау, Балхаш, Жезказган, Караганда, Кокшетау, Костанай, Кызылорда, Павлодар, Петропавловск, Рудный, Семей, Талдыкорган, Тараз, Темиртау, Туркестан, Уральск, Усть-Каменогорск, Шымкент, Экибастуз">
    <META name="keywords" CONTENT="купон, все купоны, все скидки Казахстана, все скидки, скидки, агрегатор скидок, поиск скидок, фильтр скидок, база скидок, 
    скидки Алматы,  скидки Астана,  скидки Актау,  скидки Актобе,  скидки Атырау,  скидки Балхаш,  скидки Жезказган,  скидки Караганда,  скидки Кокшетау,  скидки Костанай,  скидки Кызылорда,  скидки Павлодар,  скидки Петропавловск,  скидки Рудный,  скидки Семей,  скидки Талдыкорган,  скидки Тараз,  скидки Темиртау,  скидки Туркестан,  скидки Уральск,  скидки Усть-Каменогорск,  скидки Шымкент,  скидки Экибастуз,
    купоны Алматы,  купоны Астана,  купоны Актау,  купоны Актобе,  купоны Атырау,  купоны Балхаш,  купоны Жезказган,  купоны Караганда,  купоны Кокшетау,  купоны Костанай,  купоны Кызылорда,  купоны Павлодар,  купоны Петропавловск,  купоны Рудный,  купоны Семей,  купоны Талдыкорган,  купоны Тараз,  купоны Темиртау,  купоны Туркестан,  купоны Уральск,  купоны Усть-Каменогорск,  купоны Шымкент,  купоны Экибастуз">

      <!-- CSS  -->
      <!--<link href="/themes/material-default/css/materialize.css" type="text/css" rel="stylesheet" media="screen,projection"/>-->
      <!--<link href="/themes/material-default/css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>-->

    <!-- Google Analitycs -->
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-58205853-1', 'auto');
        ga('send', 'pageview');

    </script>

    <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
        (function (d, w, c) {
            (w[c] = w[c] || []).push(function() {
                try {
                    w.yaCounter27811728 = new Ya.Metrika({id:27811728,
                        webvisor:true,
                        clickmap:true,
                        trackLinks:true,
                        accurateTrackBounce:true});
                } catch(e) { }
            });

            var n = d.getElementsByTagName("script")[0],
                s = d.createElement("script"),
                f = function () { n.parentNode.insertBefore(s, n); };
            s.type = "text/javascript";
            s.async = true;
            s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

            if (w.opera == "[object Opera]") {
                d.addEventListener("DOMContentLoaded", f, false);
            } else { f(); }
        })(document, window, "yandex_metrika_callbacks");
    </script>
    <noscript><div><img src="//mc.yandex.ru/watch/27811728" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <!-- /Yandex.Metrika counter -->
</head>
<body>

<?php $this->beginBody() ?>
    <div class="wrap">
        <?php
            NavBar::begin([
                'brandLabel' => 'Skid.KZ',
                'brandUrl' => ['/coupon/actual'],
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-left'],
                'items' => [
                    ['label' => 'Актуальные', 'url' => ['/coupon/actual']],
                    ['label' => 'Архив', 'url' => ['/coupon/archive']],
                    ['label' => 'Статистика', 'url' => '#', 'options' => [
                        'data-toggle' => 'modal',
                        'data-target' => '#statModal'
                    ]],
                ],
            ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => [
                    //['label' => 'Home', 'url' => ['/site/index']],
                    ['label' => 'Отзывы', 'url' => ['/site/reformal']],
                    ['label' => 'О сайте', 'url' => ['/site/about']],
                    //['label' => 'Contact', 'url' => ['/site/contact']],
//                    Yii::$app->user->isGuest ?
//                        ['label' => 'Login', 'url' => ['/site/login']] :
//                        ['label' => 'Logout (' . Yii::$app->user->identity->username . ')',
//                            'url' => ['/site/logout'],
//                            'linkOptions' => ['data-method' => 'post']],
                ],
            ]);
            NavBar::end();
        ?>

        <div class="container">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= $content ?>
            <?=Yii::$app->view->render('/site/statistics')?>
            <?php Yii::$app->view->registerJs(<<<JS
                $.ajax({
                        url: "/site/statistics",
                        success: function (data) {
                            $("#statModal .modal-body").append(data);
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.log(errorThrown);
                        }

                });
JS
                , \yii\web\View::POS_READY);?>
            <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- Все мои сервисы -->
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="ca-pub-7212327600726803"
                 data-ad-slot="3252207281"
                 data-ad-format="auto"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; Skid.KZ, <?= '2014 - ' . date('Y') ?></p>
        </div>
    </footer>

  <!--  Scripts-->
  <!--  <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>-->
  <!--  <script src="/themes/material-default/js/materialize.js"></script>-->
  <!--  <script src="/themes/material-default/js/init.js"></script>-->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
