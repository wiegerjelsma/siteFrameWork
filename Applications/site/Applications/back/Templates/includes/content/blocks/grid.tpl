<?php
/**
 * @name	includes/content//blocks/grid.tpl
 * @version	1.0 2011-11-20 14:54:57
 *
 * @usage:	<?php $this->includeTpl('includes/content/blocks/grid.tpl'); ?>
 */
?>

	<div class='content-block grid <?=isset($width) && $width ? $width : ''?>'>
		<div class='content'>
		
		<?php if($dataset): ?>
		
			<table>
			<?php 
			foreach($dataset as $record){
				$row = isset($row) && $row == 'odd' ? 'even' : 'odd';
				$this->includeTpl('includes/content/blocks/grid/record.tpl', array(
					'record' => $record, 
					'dataheader' => $dataheader, 
					'row' => $row,
					'issubset' => isset($issubset) ? $issubset : false
					));
			}

			?>
			</table>
		<?php else: ?>
			<p>Er zijn geen resultaten gevonden</p>
		<?php endif; ?>

		<?php $this->includeTpl('includes/content/includes/buttoncontainer.tpl', array('buttons' => $buttons)); ?>
		
		</div>
		<div class='content-top'>
			<div class='left'></div>
			<div class='center'>
				<?php if($dataheader): ?>

				<table>
					<tr>
					<?php foreach($dataheader as $key => $values):?>
						<?php if(!isset($values['hidden']) or !$values['hidden']): ?>
							<td class='<?php if($this->OrderValues['key'] == $key){echo'order ';} echo strToLower($this->OrderValues['direction']).' ';?><?=isset($values['class']) && $values['class']?$values['class']:''?>' width="<?=isset($values['width']) && $values['width']?$values['width']:''?>"><a href="<?=APPLICATION_URL.'/'.CONTROLLER_NAME.'/'.FUNCTION_NAME.'/'.ID_NAME.'?order='.$key?>"><?=isset($values['name']) ? ucfirst($values['name']) : ucfirst($key)?></a></td>
						<?php endif; ?>
					<?php endforeach; ?>					
						<?php if(isset($this->Access['actions']['view']) && $this->Access['actions']['view']): ?><td width="24">&nbsp;</td><?php endif; ?>											
						<?php if($this->Access['actions']['edit']): ?><td width="24">&nbsp;</td><?php endif; ?>
						<?php if($this->Access['actions']['delete']): ?><td width="24">&nbsp;</td><?php endif; ?>
					</tr>
				</table>
				
				<div class='clear'></div>			
				<?php endif; ?>
			
			</div>
			<div class='right'></div>			
		</div>
		<div class='content-center'>
			<div class='left'></div>
			<div class='right'></div>					
		</div>
		<div class='content-bottom'>
			<div class='left'></div>
			<div class='center'></div>
			<div class='right'></div>							
		</div>
	</div>
