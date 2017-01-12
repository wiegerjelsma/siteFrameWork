<?php
/**
 * @name	includes/forms/elements/text.tpl
 * @version	1.0 2013-04-09 11:54:11
 *
 * @usage:	<?php $this->includeTpl('includes/forms/elements/text.tpl'); ?>
 */
?>
<div class="row">
								    <div class="large-8 medium-8 show-for-medium-up columns">
								    	<label for="<?=$Element['id']?>" class="left inline<?php echo (isset($this->{$Form['id'].'_ErrorFields'}[$Element['fieldname']])) ? ' error' : ''?>"><?=$Element['label']?> <?=$Element['required'] ? '*' : ''?></label>
								    </div>
								    <div class="large-16 medium-16 small-24 columns radius">
								    	<input class='radius' type="text" placeholder="<?=$Element['label']?> <?=$Element['required'] ? '*' : ''?>" value='<?=$Element['value']?>' name='<?=$Element['fieldname']?>' id='<?=$Element['id']?>'>
								    </div>
								</div>