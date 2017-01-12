<?php
/**
 * @name	includes/menu.tpl
 * @version	1.0 2013-10-13 18:49:10
 *
 * @usage:	<?php $this->includeTpl('includes/menu.tpl'); ?>
 */
?>


					<div class='menu'>
            		<?php foreach($this->Hoofdmenu['nodes'] as $a_Node): ?>
            			<?php if($a_Node['visible_in_menu']): ?>
	          			<a class="<?=(isset($a_Node['active']) && $a_Node['active'])?'active':''?>" href="<?=$a_Node['url']?>" target='<?=$a_Node['target']?>'><?=$a_Node['name']?></a>

			  			<?php if(isset($a_Node['active']) && $a_Node['active']): ?>
			  				<?php if(count($this->Submenu) >= 1): ?>					
					
								<?php foreach($this->Submenu as $a_sub_Node): ?>
									<?php if($a_sub_Node['visible_in_menu']): ?>
									
									<a class="sub <?=(isset($a_sub_Node['active']) && $a_sub_Node['active'])?'active':''?>" href="<?=$a_sub_Node['url']?>" target='<?=$a_sub_Node['target']?>'><?=$a_sub_Node['name']?></a>
										<?php if(isset($a_sub_Node['active']) && $a_sub_Node['active']): ?>
											<?php if(count($this->SubSubmenu) >= 1): ?>
												<?php foreach($this->SubSubmenu as $a_sub_sub_Node): ?>
													<?php if($a_sub_Node['visible_in_menu']): ?>
														<a class="subsub <?=(isset($a_sub_sub_Node['active']) && $a_sub_sub_Node['active'])?'active':''?>" href="<?=$a_sub_sub_Node['url']?>" target='<?=$a_sub_sub_Node['target']?>'><?=$a_sub_sub_Node['name']?></a>
													<?php endif; ?>												
												<?php endforeach; ?>
											<?php endif; ?>
										<?php endif; ?>
									
									<?php endif; ?>
									
									
								<?php endforeach; ?>						

		              		<?php endif; ?>

						<?php endif; ?>
						<?php endif; ?>    
              			   
              		<?php endforeach; ?>			
					</div>