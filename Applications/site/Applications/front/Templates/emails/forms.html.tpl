<?php $this->includeTpl('emails/includes/header.tpl'); ?>

		<td style='color: #fff;'  style='background-color: #0072be'><?=strftime('%A %d %B %Y', mktime(0, 0, 0, date('m'), date('d'), date('Y')))?><br /><h1><?=$this->Form['name']?></h1></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td width='75'>&nbsp;</td>
		<td>
		<?php
		foreach($this->Data as $value){
			if(preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $value['value']))
				echo '<strong>'.$value['label'].'</strong>&nbsp;'.$this->format($value['value'], 'date')."<br />";
			else
				echo '<strong>'.$value['label'].'</strong>&nbsp;'.$value['value']."<br />";
		}
		?>		
		</td>
	</tr>	

<?php $this->includeTpl('emails/includes/footer.tpl'); ?>