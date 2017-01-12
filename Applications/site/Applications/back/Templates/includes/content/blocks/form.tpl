<?php
/**
 * @name	includes/content//blocks/form.tpl
 * @version	1.0 2011-11-20 14:55:02
 *
 * @usage:	<?php $this->includeTpl('includes/content//blocks/form.tpl'); ?>
 */
?>

<?php 
$a_Params['type'] = 'form';
$a_Params['css'] = isset($css) ? $css : false;
$a_Params['width'] = isset($width) ? $width : false;

?>
<form id='<?=$this->Form['id']?>' name='<?=$this->Form['name']?>' method='POST' action='<?=$this->Form['action']?>' accept-charset="UTF-8"<?=isset($this->Form['enctype']) ? ' enctype="'.$this->Form['enctype'].'"' : ''?>>
<input type='hidden' name='submitted' value='1' />

<?php

$this->includeTpl('includes/content/blocks/includes/header.tpl', $a_Params); ?>

<?php $this->includeTpl('includes/messages.tpl', array('messages' => $messages)); ?>

<?php if($formheader): ?>
<div class='form-header'>
	<h4><?=$formheader?></h4> 
</div>
<?php endif; ?>


<?php foreach($fields as $name => $a_Params){
	$this->includeTpl('includes/content/blocks/form/input.tpl', array(	
		'label' => isset($a_Params['label']) ? $a_Params['label'] : ucfirst($name), 
		'labelclass' => isset($a_Params['labelclass']) ? $a_Params['labelclass'] : false, 
		'sublabel' => isset($a_Params['sublabel']) && $a_Params['sublabel'] ? $a_Params['sublabel'] : false, 
		'type' => isset($a_Params['type']) && $a_Params['type'] ? $a_Params['type'] : false,
		'placeholder' => isset($a_Params['placeholder']) && $a_Params['placeholder'] ? $a_Params['placeholder'] : false,		
		'required' => isset($a_Params['required']) && $a_Params['required'] ? $a_Params['required'] : false,		
		'required_on' => isset($a_Params['required_on']) && $a_Params['required_on'] ? $a_Params['required_on'] : false,		
		'name' => $name,
		'value' => isset($dataset[$name]) ? $dataset[$name] : (isset($a_Params['value']) && $a_Params['value'] ? $a_Params['value'] : false), 
		'values' => isset($a_Params['values']) && $a_Params['values'] ? $a_Params['values'] : false,
		'dataset' => isset($dataset) ? $dataset : false,		
		'error' => isset($errorfields[$name]) ? true : false,
		'hr' => isset($a_Params['hr']) && $a_Params['hr'] ? $a_Params['hr'] : false,
		'size' => isset($a_Params['size']) && $a_Params['size'] ? $a_Params['size'] : false,
		'readonly' => isset($a_Params['readonly']) && $a_Params['readonly'] ? $a_Params['readonly'] : false,
		'readonly_on' => isset($a_Params['readonly_on']) && $a_Params['readonly_on'] ? $a_Params['readonly_on'] : false,		
		'hideReadOnlyTag' => isset($a_Params['hideReadOnlyTag']) && $a_Params['hideReadOnlyTag'] ? $a_Params['hideReadOnlyTag'] : false,				
		'format' => isset($a_Params['format']) && $a_Params['format'] ? $a_Params['format'] : false,
		'show_on' => isset($a_Params['show_on']) && $a_Params['show_on'] ? $a_Params['show_on'] : false,
		'textarea_height' => isset($a_Params['textarea_height']) && $a_Params['textarea_height'] ? $a_Params['textarea_height'] : false,		
		'addoredit' => isset($this->Form['addoredit']) && $this->Form['addoredit'] ? $this->Form['addoredit'] : false,		
		'block_css' => isset($a_Params['block_css']) && $a_Params['block_css'] ? $a_Params['block_css'] : false,
		'block_width' => isset($a_Params['block_width']) && $a_Params['block_width'] ? $a_Params['block_width'] : false,
		'block_id' => isset($a_Params['block_id']) && $a_Params['block_id'] ? $a_Params['block_id'] : false,
		'block_header' => isset($a_Params['block_header']) && $a_Params['block_header'] ? $a_Params['block_header'] : false,
		'attributes' => isset($a_Params['attributes']) && $a_Params['attributes'] ? $a_Params['attributes'] : false,
		'language' => isset($a_Params['language']) && $a_Params['language'] ? $a_Params['language'] : false,		
		'directorykey' => isset($a_Params['directorykey']) && $a_Params['directorykey'] ? $a_Params['directorykey'] : false		
	));
}

// include some buttons
$this->includeTpl('includes/content/includes/buttoncontainer.tpl', array('buttons' => $buttons)); ?>

		
<?php $this->includeTpl('includes/content/blocks/includes/footer.tpl'); ?>	

</form>