<?php if($this->isTrue('Tabs')): ?>
	<div id="tabs">
		<ul>
	<?php foreach($this->Tabs as $tab): ?>
			<li<?=$tab['active']?' class="active"':''?>><a href="<?=APPLICATION_URL.'/'.$tab['controller']?><?=(isset($tab['function']) && $tab['function']) ? '/'.$tab['function'] : ''?><?=(isset($tab['id']) && $tab['id']) ? '/'.$tab['id'] : ''?>"><?=$tab['text']?></a></li>
	<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>