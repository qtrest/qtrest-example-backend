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
		$chocolife = new ChocolifeApi();
        Tools::print_array('ChocolifeApi',$chocolife->index());
		//print_r($chocolife->index());
		echo '<br/>';
		$blizzard = new BlizzardApi();
		print_r($blizzard->index());
	?>
	
</div>

