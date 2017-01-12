<?php 
$a_Params['type'] = isset($type) ? $type : false;
$a_Params['css'] = isset($css) ? $css : false;
$a_Params['width'] = isset($width) ? $width : false;
$this->includeTpl('includes/content/blocks/includes/header.tpl', $a_Params); ?>

<h5>Zoeken</h5>

<form id='<?=$this->SearchForm['id']?>' name='<?=$this->SearchForm['name']?>' method='POST' action='<?=$this->SearchForm['action']?>' accept-charset="UTF-8">
<input type='hidden' name='submitted-search' value='1' />

<?php foreach($this->SearchFields as $name => $a_Params){
	$this->includeTpl('includes/content/blocks/form/input.tpl', array(	
		'label' => false, 
		'type' => isset($a_Params['type']) && $a_Params['type'] ? $a_Params['type'] : false,
		'placeholder' => isset($a_Params['placeholder']) && $a_Params['placeholder'] ? $a_Params['placeholder'] : false,
		'name' => $name,
		'value' => isset($this->SearchValues[$name]) ? $this->SearchValues[$name] : (isset($a_Params['value']) && $a_Params['value'] ? $a_Params['value'] : false), 
		'size' => isset($a_Params['size']) && $a_Params['size'] ? $a_Params['size'] : false,
		'display' => 'inline',
	));
}
?>
<input type='submit' value='ok' class="button inline" />

</form>

<?php $this->includeTpl('includes/content/blocks/includes/footer.tpl'); ?>
