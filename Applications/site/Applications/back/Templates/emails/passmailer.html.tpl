<?php $this->includeTpl('emails/includes/header.tpl'); ?>

<table cellSpacing=0 cellPadding=0 align=center width=484 border=0 style="font-family: Arial, Tahoma, Verdana, Helvetica; font-size: 16px; color: #243552;">
	<tr>
		<td align=left><p style='margin: 0 0 20px;'>Uw nieuwe wachtwoord</p></td>								
	</tr>
</table>	

<table cellSpacing=0 cellPadding=0 align=center width=484 border=0 style="font-family: Arial, Tahoma, Verdana, Helvetica; font-size: 12px; color: #666;">
	<tr>
		<td align=left style="line-height: 1.4em;">Beste <?=$this->User['gebruikersnaam']?>,<br /><br />Uw nieuwe wachtwoord is: <?=$this->Wachtwoord?></td>								
	</tr>
</table>

<?php $this->includeTpl('emails/includes/footer.tpl'); ?>