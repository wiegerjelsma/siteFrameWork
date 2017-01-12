<?php if($this->isTrue('Leftmenu')): ?>
<div id="menu-left">
<?php foreach($this->Leftmenu as $link): ?>
	<a<?=$link['active']?' class="active"':''?> href="<?=APPLICATION_URL.'/'.$link['controller']?><?=(isset($link['function']) && $link['function']) ? '/'.$link['function'] : ''?><?=(isset($link['id']) && $link['id']) ? '/'.$link['id'] : ''?>"><?=$link['text']?></a>
<?php endforeach; ?>
</div>
<?php endif; ?>