<?php
use ijackua\sharelinks\ShareLinks;
use \yii\helpers\Html;

/**
 * @var yii\base\View $this
 */

?>

<div class="socialShareBlock">
    Поделиться: 
    <?=
    Html::a('Facebook', $this->context->shareUrl(ShareLinks::SOCIAL_FACEBOOK),
        ['title' => 'Share to Facebook']) ?>,
    <?=
    Html::a('Google Plus', $this->context->shareUrl(ShareLinks::SOCIAL_GPLUS),
        ['title' => 'Share to Google Plus']) ?>,
    <?=
    Html::a('Вконтакте', $this->context->shareUrl(ShareLinks::SOCIAL_VKONTAKTE),
        ['title' => 'Share to Vkontakte']) ?>
</div>
