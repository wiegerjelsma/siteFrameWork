<?php
/**
 * @name	/publicaties.tpl
 * @version	1.0 2013-10-14 15:54:13
 *
 * @usage:	<?php $this->includeTpl('/publicaties.tpl'); ?>
 */
?>
<?php $this->includeTpl('includes/header.tpl'); ?>

					<!-- content -->
					<div id="content" class="row">
						<div id='column-left' class="show-for-large-up columns large-7">
							<?php $this->includeTpl('includes/menu.tpl'); ?>
							<?php $this->includeTpl('includes/leftcolumn.tpl'); ?>																					
						</div>
						<div id='column-right' class="columns large-17 medium-24">
							
							<div class='kruimelpad'>
								<p>U bent nu hier:&nbsp;</p>
								<?php $count = 1; foreach($this->Kruimelpad as $node): ?>
								<?php if(count($this->Kruimelpad) == $count): ?>
								<p><strong><?=$node['name']?></strong></p>
								<?php else: ?>
								<p><a href='<?=$node['url']?>' target='<?=$node['target']?>' title='<?=$node['name']?>'><?=$node['name']?></a>&nbsp;/&nbsp;</p>
								<?php endif; $count++; ?>
								<?php endforeach; ?>
							</div>							
							
							<h1>Publicaties</h1>							
							
							
							<?php if(count($this->Submenu) >= 1): ?>					
					
							<?php foreach($this->Submenu as $a_sub_Node): ?>
							<h3 class='nomargin'><a href="<?=$a_sub_Node['url']?>" target='<?=$a_sub_Node['target']?>'><?=$a_sub_Node['name']?></a></h3>							
							<?php endforeach; ?>						

							<?php endif; ?>
							
							
							
							<?php if(isset($this->Content_Form)): ?>            
								<?php $this->includeTpl('includes/forms/form.tpl', array('Form' => $this->Content_Form, 'Elements' => $this->Content_FormElements)); ?>            
							<?php endif; ?>
							
							<div class="clear"></div>								
							
							<div class='share facebook'><a id='facebooksharelink' href='#'>deel deze pagina</a></div>
							<div class='share twitter'><a id='twittersharelink' href='#'>tweet erover</a></div>
							
<!--							<p class='quote'>Een gespannen twaalfvingerige darm bemoeilijkt de galafgifte en dus de vetvertering.</p>					-->
							
						</div>
					</div> <!-- end content -->

<?php $this->includeTpl('includes/footer.tpl'); ?>