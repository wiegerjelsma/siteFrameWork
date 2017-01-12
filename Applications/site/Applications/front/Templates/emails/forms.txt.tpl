<?=$this->Form['name']."\n\r"?>
====================================================================
<?php
foreach($this->Data as $value){
	echo $value['label'].': '.$value['value']."\n\r";
}
?>