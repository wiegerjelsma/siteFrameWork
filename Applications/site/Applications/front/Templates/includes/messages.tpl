<?php 
$a_Msg = isset($messages) ? $messages : (isset($this->Messages) && $this->Messages ? $this->Messages : false);
if($a_Msg && count($a_Msg) > 0){
	if(isset($a_Msg['error']))
		foreach($a_Msg['error'] as $msg)
			echo '<div data-alert class="alert-box error round">'.$msg.'</div>';

	if(isset($a_Msg['success']))
		foreach($a_Msg['success'] as $msg)
			echo '<div data-alert class="alert-box success round">'.$msg.'</div>';		
	
	if(isset($a_Msg['general']))
		foreach($a_Msg['general'] as $msg)
			echo '<div data-alert class="alert-box info round">'.$msg.'</div>';
		
}
?>