<?php
/**
 * @name	includes/content/blocks/includes/header.tpl
 * @version	1.0 2011-11-20 23:30:59
 *
 * @usage:	<?php $this->includeTpl('includes/content/blocks/includes/header.tpl'); ?>
 */
?>
<?php
$width = isset($width) && $width ? $width : '';
$type = isset($type) && $type ? $type : '';
$css = isset($css) && $css ? $css : '';
$id = isset($id) && $id ? $id : false;
$attributes = isset($attributes) && $attributes ? $attributes : false;
?>
	<div class='content-block <?=$type?> <?=$width?>' style='<?=$css?>' <?php if($id): ?>id='<?=$id?>'<?php endif; ?><?php if($attributes){foreach($attributes as $key => $value){echo ' '.$key.'="'.$value.'"';}}?>>
		<div class='content'>
