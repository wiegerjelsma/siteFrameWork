<?php
/**
 * @name	includes/forms/elements/pulldown.tpl
 * @version	1.0 2013-04-09 11:54:24
 *
 * @usage:	<?php $this->includeTpl('includes/forms/elements/pulldown.tpl'); ?>
 */
?>
<label class='label' for="<?=$Element['id']?>"><?=$Element['label']?><?=$Element['required'] ? ' *' : ''?></label>

<select name='<?=$Element['fieldname']?>' id='<?=$Element['id']?>' class='<?php echo (isset($this->{$Form['id'].'_ErrorFields'}[$Element['fieldname']])) ? 'error' : ''?>'>
	<?php
	 
	$a_Options = explode(',', $Element['options']);
	foreach($a_Options as $option){
		$selected = ($Element['value'] && ($Element['value'] == $option)) ? 'selected' : '';
		echo "<option value='".$option."' ".$selected.">".$option."</option>";
	}
	?>
</select>
<div class='clear'></div>