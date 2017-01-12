<?php
/**
 * @name	includes/forms/elements/radio.tpl
 * @version	1.0 2013-04-09 11:54:30
 *
 * @usage:	<?php $this->includeTpl('includes/forms/elements/radio.tpl'); ?>
 */
?>
<div class="row">
								    <div class="large-8 medium-8 show-for-medium-up columns">
								    	<label for="<?=$Element['id']?>" class="left inline<?php echo (isset($this->{$Form['id'].'_ErrorFields'}[$Element['fieldname']])) ? ' error' : ''?>"><?=$Element['label']?> <?=$Element['required'] ? '*' : ''?></label>
								    </div>
								    <div class="large-16 medium-16 small-24 columns radius">
	<?php 
	$a_Options = explode(',', $Element['options']);
	foreach($a_Options as $option){
		$checked = ($Element['value'] && ($Element['value'] == $option)) ? 'checked' : '';		
		echo "<input type='radio' name='".$Element['fieldname']."' id='".$Element['id'].'_'.$option."' value='".$option."' "; 
		echo (isset($this->{$Form['id'].'_ErrorFields'}[$Element['fieldname']])) ? 'class="error" ' : '';		
		echo $checked.">";
		echo "<label class='sublabel' style='font-weight: 400;' for=".$Element['id'].'_'.$option.">".$option."</label>";
	}
	?>
								    </div>
								</div>