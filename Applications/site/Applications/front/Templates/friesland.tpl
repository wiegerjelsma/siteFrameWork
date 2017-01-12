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
							
							<?php $this->includeTpl('includes/messages.tpl'); ?>
							
							<h1>OsteoVitaal praktijk voor osteopathie in Friesland</h1>	
							
							<p class='intro'>De ruime, lichte praktijk in Ureterp is de eerste praktijk van OsteoVitaal en was voorheen bekend als tandartspraktijk. U kunt in de wachtkamer rustig een boekje of tijdschrift lezen.</p>						
							
							<div id="binnenkijken-popup" class="reveal-modal" style='height: 600px;' data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-lat='53.093732' data-lng='6.16722' data-heading='-150' data-pitch='1'> 
  								<h2 id="modalTitle">Binnenkijken bij OsteoVitaal Friesland</h2>
								<div style="position: relative; height: 500px;"> 
									<div id="map-canvas" style="width: 0px; height: 0px; display: none;"></div>
									<div id="pano" style="position:absolute; left:0px; top: 0px; height: 500px; width: 100%;"></div> 
								</div> 
								<a class="close-reveal-modal" aria-label="Close">&#215;</a>
							</div>							
							
													
							<a id='binnenkijken' data-reveal-id="binnenkijken-popup">
								<span>Binnenkijken</span>
								<img src="<?=WEBROOT?>/images/binnenkijken-friesland.jpg" alt="" />
							</a>
							<div id="c-map-canvas"></div>							
							
							<h3>Adres</h3>
							<p>Sichte 2, 9247 BJ Ureterp (<a href="https://www.google.nl/maps/preview?hl=nl&amp;q=sichte+2+ureterp&amp;ie=UTF8&amp;hq=&amp;hnear=Sichte+2,+Ureterp,+Opsterland,+Friesland&amp;gl=nl&amp;z=16" target="_blank">route</a>)</p>

							<h3>Parkeren</h3>
							<p>Voor de praktijk zijn er 3 gratis parkeerplaatsen voor praktijkbezoekers. De praktijk is gelegen aan een hoekwoning. U vindt de ingang van de praktijk aan de kant van de ’Telle’, schuin tegenover de sporthal.</p>

							<a title='Klik hier om een afspraak te maken met OsteoVitaal' href='https://altagenda.crossuite.com/osteovitaal/l/n5r4v5z5j454p2b45474/o/74x2449423w2x2d444d48423c4p2/t/6423c444y253q2a4a4a4944364u2a4747413s28494t243' target='_blank'>Klik hier om meteen een afspraak te maken</a>
							<p>&nbsp;</p>	
<!--							<div class="row">
								<div class="columns small-24 medium-12">
									<p><img src="http://osteovitaal.nl/site.front/file/image/verbouwing26-l.jpg" alt="Praktijkruimte 1"></p>	
								</div>
								<div class="columns small-24 medium-12">
									<p><img src="http://osteovitaal.nl/site.front/file/image/verbouwing26-l.jpg" alt="Praktijkruimte 1"></p>
								</div>	
							</div>													-->

							<?php if(isset($this->Content_Form)): ?>            
								<?php $this->includeTpl('includes/forms/form.tpl', array('Form' => $this->Content_Form, 'Elements' => $this->Content_FormElements)); ?>            
							<?php endif; ?>								
							
							<div class='share facebook'><a id='facebooksharelink' href='#'>deel deze pagina</a></div>
							<div class='share twitter'><a id='twittersharelink' href='#'>tweet erover</a></div>
							
<!--							<p class='quote'>Een gespannen twaalfvingerige darm bemoeilijkt de galafgifte en dus de vetvertering.</p>					-->
							
						</div>
					</div> <!-- end content -->

<?php $this->includeTpl('includes/footer.tpl'); ?>