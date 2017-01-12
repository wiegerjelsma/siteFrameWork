<?php
/**
 * @name	includes/forms/elements/date.tpl
 * @version	1.0 2013-04-09 11:54:43
 *
 * @usage:	<?php $this->includeTpl('includes/forms/elements/date.tpl'); ?>
 */
?>
<label class='label' for="<?=$Element['id']?>"><?=$Element['label']?><?=$Element['required'] ? ' *' : ''?></label>

<?php	
		$value = $Element['value'] ? $Element['value'] : date('Y-m-d');
		list($year, $month, $day) = explode('-', $value);
	
		// Dagen
		echo "<select name='".$Element['fieldname']."_day' ";
		echo (isset($this->{$Form['id'].'_ErrorFields'}[$Element['fieldname']])) ? 'class="error" ' : '';
		echo '>';
		
		for($i=1; $i<32; $i++){
			echo "<option value='".$i."'";
			if($day == $i)
				echo ' selected';
			echo ">".$i."</option>";			
		}
		echo "</select>";
		
		// maanden
		echo "<select name='".$Element['fieldname']."_month' ";
		echo (isset($this->{$Form['id'].'_ErrorFields'}[$Element['fieldname']])) ? 'class="error" ' : '';
		echo '>';
		
		for($i=1; $i<13; $i++){
			echo "<option value='$i'";
			if($month == $i)
				echo ' selected';
			echo ">".$this->cfg['months'][$i]."</option>";			
		}
		echo "</select>";
		
		// jaren
		$startyear = date('Y');
		echo "<select name='".$Element['fieldname']."_year' ";
		echo (isset($this->{$Form['id'].'_ErrorFields'}[$Element['fieldname']])) ? 'class="error" ' : '';
		echo '>';
		for($i=$startyear; $i>=$startyear-100; $i--){
			echo "<option value='$i'";
			if($year == $i)
				echo ' selected';
			echo ">$i</option>";			
		}
		echo "</select>";		
?>
<div class='clear'></div>