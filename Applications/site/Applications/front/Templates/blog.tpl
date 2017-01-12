<?php
/**
 * @name	/blog.tpl
 * @version	1.0 2013-10-14 13:43:09
 *
 * @usage:	<?php $this->includeTpl('/blog.tpl'); ?>
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
							
							<h1>Blog</h1>							
							
							<?php if(isset($this->Blog) && $this->Blog): ?> 
								<?php foreach($this->Blog as $bericht):?>
							
								<!--<h4><?=$this->format($bericht['datum'], 'date', '%d %B %Y')?></h4>-->
								<?php if($bericht['body_teaser']): ?>
									<h3 class='nomargin'><a href='<?=APPLICATION_URL_SHORT?>/blog/<?=$bericht['short_url']?>'><?=$bericht['titel']?></a></h3>
								<?php else: ?>
									<h3 class='nomargin'><?=$bericht['titel']?></h3>							
								<?php endif;?>
									
									
								<?php if($bericht['body_teaser']): ?>							
									<p><?=$bericht['body_teaser']?><br /><a href='<?=APPLICATION_URL_SHORT?>/blog/<?=$bericht['short_url']?>'>Lees meer</a></p>
								<?php else: ?>
									<?=$bericht['body']?>
								<?php endif;?>
								<?php endforeach; ?>
							<?php else: ?>					
							<p>Er zijn geen blogs gevonden</p>
							<?php endif; ?>	
							
							<?php if(isset($this->Content_Form)): ?>            
								<?php $this->includeTpl('includes/forms/form.tpl', array('Form' => $this->Content_Form, 'Elements' => $this->Content_FormElements)); ?>            
							<?php endif; ?>								
							
							<div class='share facebook'><a id='facebooksharelink' href='#'>deel deze pagina</a></div>
							<div class='share twitter'><a id='twittersharelink' href='#'>tweet erover</a></div>
							
<!--							<p class='quote'>Een gespannen twaalfvingerige darm bemoeilijkt de galafgifte en dus de vetvertering.</p>					-->
							
						</div>
					</div> <!-- end content -->

<?php $this->includeTpl('includes/footer.tpl'); ?>