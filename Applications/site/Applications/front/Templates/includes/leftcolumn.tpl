<?php
/**
 * @name	includes/leftcolumn.tpl
 * @version	1.0 2013-10-13 18:54:39
 *
 * @usage:	<?php $this->includeTpl('includes/leftcolumn.tpl'); ?>
 */
?>
			
			
<?php
$obj_Pages = Loader::loadModule('Cms.Pages');
$a_PredefinedPage = $obj_Pages->getPredefinedPage('blog', 'view');
$a_Page = $obj_Pages->get(array('virtual_page_id' => $a_PredefinedPage['id']));
$blogurl = APPLICATION_URL_SHORT.'/'.$a_Page[0]['short_url'];

$obj_Blog = Loader::loadModule('Blog'); 
$a_LastPosts = $obj_Blog->get(array('status' => true), array('count' => 2, 'page' => 1, 'order' => 'datum DESC'));
?>					
		
<a data-reveal-id="myModal" href="#" title='Neem contact met ons op!' class="button radius">maak een afspraak</a>
<a href="<?=APPLICATION_URL_SHORT?>/contact" title='Vraag meer informatie aan' class="below-button">of vraag meer informatie aan</a>


							<div class="panel radius">
								<h3><a href='<?=$blogurl?>' title='Lees meer tips en adviezen'>Tips en Advies</a></h3>
								
								<h5><?=$this->format($a_LastPosts[0]['datum'], 'date', '%d %B %Y')?></h5>
								<h4><a href="<?=APPLICATION_URL_SHORT?>/blog/<?=$a_LastPosts[0]['short_url']?>" title=''><?=$a_LastPosts[0]['titel']?></a></h4>
								
								<h5><?=$this->format($a_LastPosts[1]['datum'], 'date', '%d %B %Y')?></h5>
								<h4><a href="<?=APPLICATION_URL_SHORT?>/blog/<?=$a_LastPosts[1]['short_url']?>" title=''><?=$a_LastPosts[1]['titel']?></a></h4>
								
								<a href="<?=APPLICATION_URL_SHORT?>/blog/" title='Lees meer tips en adviezen'>Meer tips en adviezen</a>
							</div>
							
<!--<h3>Contact</h3>
<p>06-137 84 618 <em>(Centraal nr.)</em><br />
<em class="grey">Tussen 9:00 en 10:00 uur iedere werkdag, behalve donderdags, bereikbaar.</em><br />info@osteovitaal.nl
</p>
							
<p><strong><a href="#">Praktijk Ureterp</a></strong> <em>(Hoofdpraktijk)</em><br />Sichte 2<br />9247 BJ Ureterp<br /><a href="http://maps.google.nl/maps?hl=nl&q=sichte+2+ureterp&ie=UTF8&hq=&hnear=Sichte+2,+Ureterp,+Opsterland,+Friesland&gl=nl&z=16">Route</a></p>
<p><strong><a href="#">Praktijk Groningen</a></strong><br />Vechtstraat 73 2<br />9725 CT Groningen<br /><a href="http://maps.google.nl/maps?q=Vechtstraat+71+Groningen&hl=nl&sll=53.093751,6.167198&sspn=0.008917,0.022724&gl=nl&z=16">Route</a></p>
			-->				
<h3>NRO &amp; NVO</h3>
<p class='nvorno'>OsteoVitaal is aangesloten bij de beroepsverenigingen <a href="http://osteopathie-nro.nl/" target='_blank' title='Ga naar de website van de NRO'>NRO</a> en <a href="http://www.osteopathie.nl/" target='_blank' title='Ga naar de website van de NVO'>NVO</a>.
<img src="<?=WEBROOT?>/images/nro_nvo@1x.png" alt="NVO & NRO" /></p>

<div class="clear"></div>