<?php 
use app\modules\kupon\parser\ChocolifeApi as ChocolifeApi;
use app\modules\kupon\parser\BlizzardApi as BlizzardApi;

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

	?>
	
</div>
