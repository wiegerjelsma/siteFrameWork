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
							
							<h1>OsteoVitaal praktijk voor osteopathie in Groningen</h1>	
							<p class='intro'>De praktijk Groningen is in 2013 helemaal opgeknapt en modern vormgegeven, met twee behandelkamers en in de kleuren van OsteoVitaal. In de wachtkamer kunt u genieten van een heerlijk kopje koffie of thee.</p>						
							
							<div id="binnenkijken-popup" class="reveal-modal" style='height: 600px;' data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-lat='53.207822' data-lng='6.568574' data-heading='-150' data-pitch='1'> 
  								<h2 id="modalTitle">Binnenkijken bij OsteoVitaal Groningen</h2>
								<div style="position: relative; height: 500px;"> 
									<div id="map-canvas" style="width: 0px; height: 0px; display: none;"></div>
									<div id="pano" style="position:absolute; left:0px; top: 0px; height: 500px; width: 100%;"></div> 
								</div> 
								<a class="close-reveal-modal" aria-label="Close">&#215;</a>
							</div>							
							
													
							<a id='binnenkijken' data-reveal-id="binnenkijken-popup">
								<span>Binnenkijken</span>
								<img src="<?=WEBROOT?>/images/binnenkijken-groningen.jpg" alt="" />
							</a>
							<div id="c-map-canvas"></div>
																					
							<h3>Adres</h3>
							<p>Vechtstraat 73, 9725 CT Groningen (<a href="https://www.google.nl/maps/preview?q=Vechtstraat+71+Groningen&amp;hl=nl&amp;sll=53.093751,6.167198&amp;sspn=0.008917,0.022724&amp;gl=nl&amp;z=16" target="_blank">route</a>)</p>
							
							<h3>Bereikbaarheid &amp; Parkeren</h3>
							<p>De praktijk is zowel met de auto als met het openbaar vervoer goed bereikbaar. De praktijk ligt vlakbij de zuidelijke stadsring. U kunt om de hoek bij de Plus supermarkt 1,5u gratis parkeren. Wilt u voor de deur parkeren dan kunt een parkeerkaartje kopen door te pinnen bij &eacute;&eacute;n van de betaalautomaten in de straat. Komt u met het openbaar vervoer? Vanaf het Centraal Station is het nog ongeveer 10 minuten lopen (<a href="https://www.google.nl/maps/preview?q=Vechtstraat+71+Groningen&amp;hl=nl&amp;sll=53.093751,6.167198&amp;sspn=0.008917,0.022724&amp;gl=nl&amp;z=16" target="_blank">kaart</a>).</p>
							

							<a title='Klik hier om een afspraak te maken met OsteoVitaal' href='https://altagenda.crossuite.com/osteovitaal/l/n5r4v5z5j454p2b45464/o/74x2449423w2x2d444d48423c4p2/t/6423c444y253q2a4a4a4944364u2a4747413s28494t243' target='_blank'>Klik hier om meteen een afspraak te maken</a>
							<p>&nbsp;</p>
							<div class="row">
								<div class="columns small-24 medium-12">
									<p><img src="http://osteovitaal.nl/site.front/file/image/13afedcba71773c59d23a8b07831e5af-EntreePraktijk-l.jpg" alt="Entree praktijk Groningen"></p>	
								</div>
								<div class="columns small-24 medium-12">
									<p><img src="http://osteovitaal.nl/site.front/file/image/783755202a6b031821fa0b25049363db-PraktijkRuimte-l.jpg" alt="Praktijkruimte Groningen"></p>
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