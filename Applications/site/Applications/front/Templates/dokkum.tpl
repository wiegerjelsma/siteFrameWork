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
							
							<h1>OsteoVitaal praktijk voor osteopathie in Dokkum, Friesland</h1>	
							
							<p class='intro'>De mooie, moderne en ruime praktijk in Dokkum is de derde praktijk van OsteoVitaal en werd in 2016 geopend door Pieter Holvast. In dit pand zitten o.a. ook disciplines als fysiotherapie, manuele therapie en kinderfysiotherapie (Fysio Dokkum – Damwoude). </p>												
							
							<div id="binnenkijken-popup" class="reveal-modal" style='height: 600px;' data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-lat='53.3197795' data-lng='6.0170608' data-heading='0' data-pitch='1'> 
  								<h2 id="modalTitle">Binnenkijken bij OsteoVitaal Dokkum</h2>
								<div style="position: relative; height: 500px;"> 
									<div id="map-canvas" style="width: 0px; height: 0px; display: none;"></div>
									<div id="pano" style="position:absolute; left:0px; top: 0px; height: 500px; width: 100%;"></div> 
								</div> 
								<a class="close-reveal-modal" aria-label="Close">&#215;</a>
							</div>					
							
													
							<a id='binnenkijken' data-reveal-id="binnenkijken-popup">
								<span>Binnenkijken</span>
								<img src="<?=WEBROOT?>/images/binnenkijken-dokkum.jpg" alt="" />
							</a>
							<div id="c-map-canvas"></div>							
							
							<h3>Adres</h3>
							<p>Bocksmeulen 41, 9101 RA Dokkum (<a href="https://www.google.nl/maps/place/Bocksmeulen+41,+9101+RA+Dokkum/@53.3197742,6.0148772,17z/data=!3m1!4b1!4m2!3m1!1s0x47c9abe6a9f5f35b:0xe672d0da7b973f5a" target="_blank">route</a>)</p>

							<h3>Bereikbaarheid &amp; Parkeren</h3>
							<p>De praktijk is goed bereikbaar met de auto en openbaar vervoer. Medio 2016 is de praktijk nog beter bereikbaar via de nieuwe autoweg tussen Dokkum en Drachten (De Centrale As). Bij de praktijk is een groot parkeerterrein aanwezig. Parkeren is hier gratis. </p>

							<a title='Klik hier om een afspraak te maken met OsteoVitaal' href='https://altagenda.crossuite.com/osteovitaal/l/n5r4v5z5j454p2d45444/o/74x24494/t/6423b4a4u253q2a4b44484' target='_blank'>Klik hier om meteen een afspraak te maken</a>
							<p>&nbsp;</p>	
							<div class="row">
								<div class="columns small-24 medium-12">
									<p><img src="http://osteovitaal.nl/site/site.front/file/image/ab2e2db6a9fdc8023477c884080ac75e-Gevel-l.jpg" alt="Gevel Praktijk Dokkum"></p>	
								</div>
								<div class="columns small-24 medium-12">
									<p><img src="http://osteovitaal.nl/site/site.front/file/image/0c204ee31b31edea758f5249b421a185-Behandelkamer-l.jpg" alt="Behandelkamer Prakijk Dokkum"></p>
								</div>	
							</div>												

							<?php if(isset($this->Content_Form)): ?>            
								<?php $this->includeTpl('includes/forms/form.tpl', array('Form' => $this->Content_Form, 'Elements' => $this->Content_FormElements)); ?>            
							<?php endif; ?>								
							
							<div class='share facebook'><a id='facebooksharelink' href='#'>deel deze pagina</a></div>
							<div class='share twitter'><a id='twittersharelink' href='#'>tweet erover</a></div>
							
<!--							<p class='quote'>Een gespannen twaalfvingerige darm bemoeilijkt de galafgifte en dus de vetvertering.</p>					-->
							
						</div>
					</div> <!-- end content -->

<?php $this->includeTpl('includes/footer.tpl'); ?>