<?php
/**
 * @name	/page.tpl
 * @version	1.0 2013-10-13 18:49:46
 *
 * @usage:	<?php $this->includeTpl('/page.tpl'); ?>
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
							
							<?php $this->includeTpl('includes/messages.tpl'); ?>
							
							<?php if(isset($this->Form_Success) && $this->Form_Success): ?>
<!--							<div class='panel'>
								<p>Geachte heer, mevrouw,<br /><br />Dank voor uw email. Wij zullen deze zo spoedig mogelijk proberen te beantwoorden. Gezien de vakantie periode kan het zijn dat dit wat langer dan gebruikelijk duurt. Anne-Ruurd Hoogeveen heeft vakantie tot 13 augustus. Tot die tijd zal Rutger Veneklaas uw email beantwoorden. Hopende op uw begrip!<br /><br />
								Vriendelijke groeten,<br /><br />
								OsteoVitaal</p>
							</div> -->
							<?php endif; ?>
							
							
							
							<?php if($this->isTrue('Content_Title')): ?>
							<h1><?=$this->Content_Title?></h1>
							<?php endif; ?>							
							
							<?=$this->Content_Body?>
							
							<?php if(isset($this->Content_Form)): ?>            
								<?php $this->includeTpl('includes/forms/form.tpl', array('Form' => $this->Content_Form, 'Elements' => $this->Content_FormElements)); ?>            
							<?php endif; ?>															
							
							<div class='share facebook'><a id='facebooksharelink' href='#'>deel deze pagina</a></div>
							<div class='share twitter'><a id='twittersharelink' href='#'>tweet erover</a></div>
							
							<div class='clear'></div>
							
<!--							<p class='quote'>Een gespannen twaalfvingerige darm bemoeilijkt de galafgifte en dus de vetvertering.</p>					-->
							
						</div>
						<div class='clear'></div>
					</div> <!-- end content -->

<?php $this->includeTpl('includes/footer.tpl'); ?>