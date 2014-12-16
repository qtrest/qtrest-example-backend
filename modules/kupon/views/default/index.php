<?php 
use app\modules\kupon\parser\ChocolifeApi as ChocolifeApi;
use app\modules\kupon\parser\BlizzardApi as BlizzardApi;
use app\modules\kupon\parser\KupiKuponApi as KupiKuponApi;
use app\modules\kupon\parser\MirKuponovApi as MirKuponovApi;
use app\modules\kupon\parser\AutoKuponApi as AutoKuponApi;
use app\modules\kupon\parser\BiglionApi as BiglionApi;

use app\components\Tools;

?>

<div class="kupon-default-index">
    <h1><?= $this->context->action->uniqueId ?></h1>
    <p>
        This is the view content for action "<?= $this->context->action->id ?>".
        The action belongs to the controller "<?= get_class($this->context) ?>"
        in the "<?= $this->context->module->id ?>" module.
    </p>
    <p>
        You may customize this page by editing the following file:<br>
        <code><?= __FILE__ ?></code>
    </p>
	
	<?php
    //echo parse_url('http://www.chocolife.me/18236-zimnie-kanikuly-v-karakole-prozhivanie-v-otelyah-intour-i-park-hotel-so-skidkoy-do-42-ot-logos-travel#showOffers')['path'];

    $chocolife = new ChocolifeApi();
    $chocolife->initData();
    //$chocolife->fetchAllCities();

    $blizzard = new BlizzardApi();
    $blizzard->initData();
    //$blizzard->fetchAllCities();
    //$blizzard->testCoupons(2, false);
    //$blizzard->testCategories();
    //$blizzard->testAdvancedCoupon(1);

    $kupiKupon = new KupiKuponApi();
    $kupiKupon->initData();
    //$kupiKupon->testCities();
    //$kupiKupon->testCategories();
    //$kupiKupon->testCoupons(1, false);
    //$kupiKupon->fetchAllCities();
    //$kupiKupon->testAdvancedCoupon(2);

    $mirKuponov = new MirKuponovApi();
    $mirKuponov->initData();
    //$mirKuponov->testCities();
    //$mirKuponov->testCategories();
    //$mirKuponov->testCoupons(1, false);
    //$mirKuponov->fetchAllCities();
    //$mirKuponov->testAdvancedCoupon(624);

    $autoKupon = new AutoKuponApi();
    $autoKupon->initData();
    //$autoKupon->testCities();
    //$autoKupon->testCategories();
    //$autoKupon->testCoupons(1, false);
    //$autoKupon->fetchAllCities();

    $biglion = new BiglionApi();
    $biglion->initData();
    //$biglion->testCities();
    $biglion->testCategories();
    //$autoKupon->testCoupons(1, false);
    //$autoKupon->fetchAllCities();
    //$autoKupon->testAdvancedCoupon(624);
	?>
	
</div>
