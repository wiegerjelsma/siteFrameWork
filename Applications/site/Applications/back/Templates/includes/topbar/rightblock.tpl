<?php if($this->isTrue('loggedInUserName')): ?>	
<div class="rightblock">Welkom <strong><?=$this->loggedInUserName?><?php if($this->isTrue('loggedInGalerieName')){echo ', '.$this->loggedInGalerieName.' ('.$this->loggedInGalerieNr.')';}?></strong>
<?php foreach($this->cfg['headerlinks'] as $a_Link): ?>
<a href="<?=APPLICATION_URL.$a_Link['url']?>"><?=$a_Link['name']?></a>
<?php endforeach;?>
</div>
<?php endif; ?>