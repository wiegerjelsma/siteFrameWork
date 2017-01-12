<div class='message-container'>
<?php 
$a_Msg = isset($messages) ? $messages : (isset($this->Messages) && $this->Messages ? $this->Messages : false);
if($a_Msg && count($a_Msg) > 0){
	if(isset($a_Msg['error']))
		foreach($a_Msg['error'] as $msg)
			$this->includeTpl('includes/messages/message.tpl', array('msg' => $msg, 'type' => 'error'));
	
	if(isset($a_Msg['success']))
		foreach($a_Msg['success'] as $msg)
			$this->includeTpl('includes/messages/message.tpl', array('msg' => $msg, 'type' => 'success'));
	
	if(isset($a_Msg['general']))
		foreach($a_Msg['general'] as $msg)
			$this->includeTpl('includes/messages/message.tpl', array('msg' => $msg, 'type' => ''));			
}
?>	
</div>
<div class='clear'></div>