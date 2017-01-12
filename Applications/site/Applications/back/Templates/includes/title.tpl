<?php
/**
 * @name	includes/title.tpl
 * @version	1.0 2012-11-26 11:36:09
 *
 * @usage:	<?php $this->includeTpl('includes/title.tpl'); ?>
 */
?>
<h1><?=isset($this->Titel) && $this->Titel ? $this->Titel : CONTROLLER_NAME?></h1>
<?=isset($this->Subtitel) && $this->Subtitel ? '<h3>'.$this->Subtitel.'</h3>' : ''?>