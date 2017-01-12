<?php 
$this->includeTpl('includes/header.tpl');
$this->includeTpl('includes/topbar.tpl'); 

/**
 * @desc	Hier opent de content (grids, forms etc)
 */
?>
<div id='content-container'>

<?php $this->includeTpl('includes/messages.tpl'); ?>

	<div class='content-block nobackground login center'>
		<img src="<?=WEBROOT?>/img/logo-large.png" />
	</div>	
	<div class="clear"></div>

	<div class='content-block form login'>
	
		<div class='content'>
		
		<?php $this->includeTpl('includes/messages.tpl', array('messages' => isset($this->Messages['form']) ? $this->Messages['form'] : false)); ?>
		
			<div class='form-header'>
				<h4>Wachtwoord vergeten</h4> 
				<p>Vul hieronder uw emailadres in waarmee u bij ons geregistreerd staat. Wij zullen u via email een nieuw wachtwoord doen toekomen.</p>
			</div>	
			
<form id='<?=$this->Form['id']?>' name='<?=$this->Form['name']?>' method='POST' action='<?=$this->Form['action']?>' accept-charset="UTF-8">
<input type='hidden' name='submitted' value='1' />

<?php
	$this->includeTpl('includes/content/blocks/form/input.tpl', array(	
		'label' => 'Emailadres', 
		'type' => 'text',
		'required' => true,		
		'name' => 'emailadres',
		'value' => isset($this->DataSet['emailadres']) ? $this->DataSet['emailadres'] : false, 
		'error' => isset($this->ErrorFields['emailadres']) ? true : false
	));	
	
	$this->includeTpl('includes/content/includes/buttoncontainer.tpl', array('buttons' => $this->Buttons));
	
?>
		</form>
		</div>
		
		<div class='content-top'>
			<div class='left'></div>
			<div class='center'></div>
			<div class='right'></div>			
		</div>
		<div class='content-center'>
			<div class='left'></div>
			<div class='right'></div>					
		</div>
		<div class='content-bottom'>
			<div class='left'></div>
			<div class='center'></div>
			<div class='right'></div>							
		</div>
	</div>
</div>
<?php

$this->includeTpl('includes/footer.tpl'); 
?>