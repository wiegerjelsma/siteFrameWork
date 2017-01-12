<?php
/**
 * @name	/blog_detail.tpl
 * @version	1.0 2013-10-14 13:43:27
 *
 * @usage:	<?php $this->includeTpl('/blog_detail.tpl'); ?>
 */
?>
<?php $this->includeTpl('includes/header.tpl'); ?>

					<!-- content -->
					<div id="content" class="row" data-equalizer>
						<div id='column-left' class="show-for-large-up columns large-7" data-equalizer-watch>
							<?php $this->includeTpl('includes/menu.tpl'); ?>
							<?php $this->includeTpl('includes/leftcolumn.tpl'); ?>																					
						</div>
						<div id='column-right' class="columns large-17 medium-24" data-equalizer-watch>
							
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
							
<!--							<h4><?=$this->format($this->Blog['datum'], 'date', '%d %B %Y')?></h4>-->
							<h1><?=$this->Blog['titel']?></h1>
							<?=$this->Blog['body']?>						
							
							<?php if(isset($this->Content_Form)): ?>            
								<?php $this->includeTpl('includes/forms/form.tpl', array('Form' => $this->Content_Form, 'Elements' => $this->Content_FormElements)); ?>            
							<?php endif; ?>								
							
							<div class='share facebook'><a id='facebooksharelink' href='#'>deel deze pagina</a></div>
							<div class='share twitter'><a id='twittersharelink' href='#'>tweet erover</a></div>
							
							<p class='quote'>Een gespannen twaalfvingerige darm bemoeilijkt de galafgifte en dus de vetvertering.</p>					
							
						</div>
					</div> <!-- end content -->

<?php $this->includeTpl('includes/footer.tpl'); ?>