<?php 
$this->includeTpl('includes/header.tpl'); 
$this->includeTpl('includes/topbar.tpl'); 

/**
 * @desc	Hier opent de content (grids, forms etc)
 */
?>
<div id='content-container'>
	<h1>Content Management</h1> 
	
<?php $this->includeTpl('includes/content/blocks/includes/header.tpl', array('css' => 'width: 50%;')); ?>
<h4><a href='<?=APPLICATION_URL.'/paginas'?>'>Pagina's</a></h4>
<p>Lorem ipsum dolor sit amet</p>
<?php $this->includeTpl('includes/content/blocks/includes/footer.tpl'); ?>

</div>
	
<?php
$this->includeTpl('includes/leftmenu.tpl'); 
$this->includeTpl('includes/credits.tpl'); 
$this->includeTpl('includes/footer.tpl'); 
?>