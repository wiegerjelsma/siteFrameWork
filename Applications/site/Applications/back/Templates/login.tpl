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
				<h4>Login</h4> 
			</div>	
			
<form id='<?=$this->Form['id']?>' name='<?=$this->Form['name']?>' method='POST' action='<?=$this->Form['action']?>' accept-charset="UTF-8">
<input type='hidden' name='submitted' value='1' />

<?php
	$this->includeTpl('includes/content/blocks/form/input.tpl', array(	
		'label' => 'Gebruikersnaam', 
		'type' => 'text',
		'required' => true,		
		'name' => 'gebruikersnaam',
		'value' => isset($this->DataSet['gebruikersnaam']) ? $this->DataSet['gebruikersnaam'] : false, 
		'error' => isset($this->ErrorFields['gebruikersnaam']) ? true : false
	));
	
	$this->includeTpl('includes/content/blocks/form/input.tpl', array(	
		'label' => 'Wachtwoord',
		'sublabel_link' => array('name' => 'Vergeten?', 'url' => APPLICATION_URL.'/passmailer'), 
		'type' => 'password',
		'required' => true,		
		'name' => 'wachtwoord',
		'value' => false, 
		'error' => isset($this->ErrorFields['wachtwoord']) ? true : false
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