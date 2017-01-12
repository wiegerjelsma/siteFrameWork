<?php
/**
 * @name	includes/content/includes/buttoncontainer.tpl
 * @version	1.0 2011-11-20 14:54:29
 *
 * @usage:	<?php $this->includeTpl('includes/content/includes/buttoncontainer.tpl'); ?>
 */
?>

<?php if($buttons): ?>
<div class='button-container'>
	<ul>
<?php foreach($buttons as $button): ?>

	<?php if(isset($button['url']) && $button['url']): ?>		
		<li><a href='<?=$button['url']?>' class='button'><?=$button['value']?></a></li>
	<?php else: ?>
		<li><input type='<?=(isset($button['type']) && $button['type'])?$button['type']:'button'?>' value='<?=$button['value']?>' class="button" /></li>
	<?php endif; ?>

<?php endforeach; ?>
	</ul>
</div>
	<?php endif; ?>
