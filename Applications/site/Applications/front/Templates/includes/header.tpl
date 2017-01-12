<?php
/**
 * @name	includes/header.tpl
 * @version	1.0 2013-10-13 18:49:02
 *
 * @usage:	<?php $this->includeTpl('includes/header.tpl'); ?>
 */
?>
<!doctype html>
<html class="no-js" lang="en">
	<head>
<?php $this->includeTpl('includes/head/meta.tpl'); ?>
<?php $this->includeTpl('includes/head/css.tpl'); ?>
<?php $this->includeTpl('includes/head/javascript.tpl'); ?>	
	</head>

	<body>

		<div id="myModal" class="reveal-modal small" data-reveal="" aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
			<h2 id="modalTitle">Zelf online een afspraak maken</h2>
			<p>
				Wilt u meteen de eerste plek reserveren? Of heeft u voorkeur voor een osteopaat? Maak hieronder uw keuze.
			</p>
		  •&nbsp;
			<a href="https://altagenda.crossuite.com/osteovitaal/l/n5r4v5z5j454p2b45464/o/74x2449423w2x2d444d48423c4p2/t/6423c444y253q2a4a4a4944364u2a4747413s28494t243" target="_blank" title="Ga meteen naar de eerstvolgende beschikbare plek in praktijk Groningen">Eerste beschikbare plek in Groningen.</a><br>
		  •&nbsp;
			<a href="https://altagenda.crossuite.com/osteovitaal/l/n5r4v5z5j454p2d45444/o/74x24494/t/6423b4a4u253q2a4b44484" target="_blank" title="Ga meteen naar de eerstvolgende beschikbare plek in praktijk Dokkum">Eerste beschikbare plek in Dokkum.</a><br>
		  •&nbsp;
			<a href="https://altagenda.crossuite.com/osteovitaal/l/n5r4v5z5j454p2b45474/o/74x2449423w2x2d444d48423c4p2/t/6423c444y253q2a4a4a4944364u2a4747413s28494t243" target="_blank" title="Ga meteen naar de eerstvolgende beschikbare plek in praktijk Ureterp">Eerste beschikbare plek in Ureterp.</a><br>
			<br>
		  •&nbsp;
			<a href="https://newagenda.crossuite.com/osteovitaal/nl_BE" target="_blank" title="Klik hier om uw afspraak in te plannen bij de osteopaat van uw voorkeur.">Ik wil zoeken op locatie en osteopaat.</a>
			<p>
				<br>
				Mocht u vragen hebben over het inplannen van een afspraak online, bel ons dan gerust; 06 - 137 846 18
			</p>
			<a class="close-reveal-modal" aria-label="Sluiten">×</a>
		</div>
		
		<div id="lichaam" class='show-for-large-up'></div>
		
		<div id='container' class="row off-canvas-wrap" data-offcanvas>
			<div class="inner-wrap">			
		
				<!-- CONTENT -->
				<div class="large-24 columns">
										
					<!-- header -->	
					<div id='header' class='row'>
						<div class="overlay">
							<a class='logo' href='<?=APPLICATION_URL_SHORT?>' title='Ga naar de homepage' alt='Logo OsteoVitaal' data-interchange="[<?=WEBROOT?>/images/logo@1x.png, (default)], [<?=WEBROOT?>/images/logo@2x.png, (retina)]"></a>
						</div>
						<img id='header-image' src="" alt="" />
					</div> <!-- end header -->			
					
					
					<!-- Green bar -->
					<div id="bar" class="row">
						<div class="social show-for-large-up" data-interchange="[<?=WEBROOT?>/images/volgons@1x.png, (default)], [<?=WEBROOT?>/images/volgons@2x.png, (retina)]">
							<a href="http://www.facebook.com/OsteoVitaal" target='_blank' title='Volg ons op Facebook!' class="facebook"></a>
							<a href="https://plus.google.com/+OsteoVitaalGroningen" target='_blank' rel='publisher' title='Volg ons op Google+!' class="google"></a>

<!--							<a href="https://plus.google.com/103552514496099772418
								https://plus.google.com/+OsteoVitaalGroningen
								https://plus.google.com/115125296392426898178/about
									" target='_blank' title='Volg ons op Google+!' class="google"></a> -->
						</div>	
						
						<div class="phone">
							<span data-interchange="[<?=WEBROOT?>/images/iconsprite@1x.png, (default)], [<?=WEBROOT?>/images/iconsprite@2x.png, (retina)]"></span><p><a href='tel:+31613784618'>06-137 84 618</a> <em>(Centraal nr.)</em></p>
						</div>
												
						<a href="" class="right-off-canvas-toggle hide-for-large-up" data-interchange="[<?=WEBROOT?>/images/iconsprite@1x.png, (default)], [<?=WEBROOT?>/images/iconsprite@2x.png, (retina)]"></a>
					</div> <!-- end bar -->	
