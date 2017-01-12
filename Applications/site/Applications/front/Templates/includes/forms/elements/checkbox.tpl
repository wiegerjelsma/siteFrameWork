<?php
/**
 * @name	includes/forms/elements/checkbox.tpl
 * @version	1.0 2013-04-09 11:54:37
 *
 * @usage:	<?php $this->includeTpl('includes/forms/elements/checkbox.tpl'); ?>
 */
?>
<label class='label'><?=$Element['label']?></label>
<?php $checked = $Element['value'] ? 'checked' : ''; ?>
<input type='checkbox' name='<?=$Element['fieldname']?>' id='<?=$Element['id']?>' class='<?php echo (isset($this->{$Form['id'].'_ErrorFields'}[$Element['fieldname']])) ? 'error' : ''?>' <?=$checked?>/>
<label class='sublabel' for="<?=$Element['id']?>"><?=$Element['sublabel']?></label>
<div class='clear'></div>