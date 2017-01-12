<?php
/**
 * @name	includes/forms/form.tpl
 * @version	1.0 2013-04-09 10:50:36
 *
 * @usage:	<?php $this->includeTpl('includes/forms/form.tpl'); ?>
 */
?>

<form id='<?=$Form['id']?>' name='<?=$Form['name']?>' method='POST' action='<?=THIS_URL?>' accept-charset="UTF-8">
<input type='hidden' name='submitted_<?=$Form['id']?>' value='1' />

<?php 
foreach($Elements as $element){
	$this->includeTpl('includes/forms/elements/'.$element['type'].'.tpl', array('Element' => $element, 'Form' => $Form)); 
}
	$this->includeTpl('includes/forms/elements/captcha.tpl', array('Form' => $Form));

?>
								<div class="row">
								    <div class="large-8 medium-8 show-for-medium-up columns">&nbsp;
								    </div>
								    <div class="large-16 medium-16 small-24 columns radius">
								    	<input class='button tiny radius' type="submit" value='<?=isset($Form['submitvalue']) ? $Form['submitvalue'] : 'OK'?>'>
								    </div>
								</div>