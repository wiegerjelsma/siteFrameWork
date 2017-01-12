<?php
/**
 * @name	includes/content/blocks/grid/record.tpl
 * @version	1.0 2011-11-20 15:03:25
 *
 * @usage:	<?php $this->includeTpl('includes/content/blocks/grid/record.tpl'); ?>
 */ 
?>
				<tr class='<?=$row?>'>
					<td width="5"></td>
					
					<?php foreach($record as $key => $value):?>
					
					<?php if(isset($dataheader[$key]) && (!isset($dataheader[$key]['hidden']) or !$dataheader[$key]['hidden'])): ?>					
						<td class="<?=isset($dataheader[$key]['class']) && $dataheader[$key]['class']?$dataheader[$key]['class']:''?> <?=isset($dataheader[$key]['align']) && $dataheader[$key]['align']?$dataheader[$key]['align']:'left'?>-align" width="<?=isset($dataheader[$key]['width']) && $dataheader[$key]['width']?$dataheader[$key]['width']:''?>">
						<?php
						if(isset($dataheader[$key]['type']) && $dataheader[$key]['type']){
							$obj_Tools = Loader::load('Tools');
							$callback = curry('on_match', 2);						
							switch($dataheader[$key]['type']){
								default:
									echo '<p>'.$this->format($value, $dataheader[$key]['type'], isset($dataheader[$key]['format']) && $dataheader[$key]['format'] ? $dataheader[$key]['format'] : false).'</p>';
								break;
								case 'status_actief-inactief-dot':
								
									echo !$value ? "<div class='gridicon-dot inactive'></div>" : "<div class='gridicon-dot'></div>";
													
//									echo $value ? "<p class='dot active'>&nbsp;</p>" : "<p class='dot inactive'>&nbsp;</p>";
								break;
								case 'statistics_country_link':
									echo '<p><a href="'.preg_replace_callback('/\{(.*?[^\}])\}/',$callback($record),$dataheader[$key]['url']).'"';
									echo isset($dataheader[$key]['target']) ? ' target="'.$dataheader[$key]['target'].'" ' : '';
									echo '>'.$value.'</a></p>';
								break;
								case 'link':
									echo '<p>';
									echo isset($dataheader[$key]['prefix']) ? preg_replace_callback('/\{(.*?[^\}])\}/',$callback($record),$dataheader[$key]['prefix']).' ': '';
									echo '<a href="'.preg_replace_callback('/\{(.*?[^\}])\}/',$callback($record),$dataheader[$key]['url']).'"';
									echo isset($dataheader[$key]['target']) ? ' target="'.$dataheader[$key]['target'].'" ' : '';
									echo isset($dataheader[$key]['linkname']) ? '>'.$dataheader[$key]['linkname'].'</a>' : '>'.$dataheader[$key]['url'].'</a>';
									echo isset($dataheader[$key]['suffix']) ? ' '.preg_replace_callback('/\{(.*?[^\}])\}/',$callback($record),$dataheader[$key]['suffix']) : '';									
									echo '</p>';
								break;
								case 'links':
									echo '<p>';								
									foreach($dataheader[$key]['values'] as $value){
										echo isset($value['prefix']) ? preg_replace_callback('/\{(.*?[^\}])\}/',$callback($record),$value['prefix']).' ' : '';										
										echo '<a href="'.preg_replace_callback('/\{(.*?[^\}])\}/',$callback($record),$value['url']).'"';
										echo isset($value['target']) ? ' target="'.$value['target'].'" ' : '';
										echo '>'.$value['value'].'</a>';
										echo isset($value['suffix']) ? ' '.preg_replace_callback('/\{(.*?[^\}])\}/',$callback($record),$value['suffix']) : '';
										echo ' ';																		
									}								
									echo '</p>';									
								break;
								case 'thumb':
									$url = 'http://'.APPLICATION_DOMAIN.'/'.ORIG_FWPREFIX.'/'.FWPREFIX.'.front/file/image/'.$value.'-admin.jpg';
									echo "<div class='gridicon-thumb' style='background: url(".'"'.$url.'"'.") no-repeat left top;'>&nbsp;</div>";
								break;																							
							}
						} else						
							echo '<p>'.$value ? $value : '&nbsp;'.'</p>';
						?></td>
						
					<?php endif; ?>
					<?php endforeach; ?>						
					<?php if(isset($this->Access['actions']['view']) && $this->Access['actions']['view']): ?><td width="24" class='center-align'><div class='gridicon'><a class='view' title='View' href="<?=APPLICATION_URL.'/'.strToLower(CONTROLLER_NAME).'/detail';?><?=($issubset) ? ':sub/' : '/';?><?=$record['id']?>"></a></div></td><?php endif; ?>

					<?php if($this->Access['actions']['edit']): ?><td width="24" class='center-align'><div class='gridicon'><a class='edit' title='Edit' href="<?=APPLICATION_URL.'/'.(isset($this->EditController) && $this->EditController ? strToLower($this->EditController) : strToLower(CONTROLLER_NAME)).'/edit';?><?=($issubset) ? ':sub/' : '/';?><?=$record['id']?>"></a></div></td><?php endif; ?>
					<?php if($this->Access['actions']['delete']): ?><td width="24" class='center-align'><div class='gridicon'><a class='delete' title='Delete' fire='confirm' msg='Weet u zeker dat u dit item wilt verwijderen?' url="<?=APPLICATION_URL.'/'.(isset($this->DeleteController) && $this->DeleteController ? strToLower($this->DeleteController) : strToLower(CONTROLLER_NAME)).'/delete';?><?=($issubset) ? ':sub/'.$record['id'].'?subid='.ID_NAME : '/'.$record['id'];?>"></a></div></td><?php endif; ?>
					<td width="5"></td>
				</tr>
