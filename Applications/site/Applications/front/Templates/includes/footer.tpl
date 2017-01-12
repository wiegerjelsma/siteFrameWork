<?php
/**
 * @name	includes/footer.tpl
 * @version	1.0 2013-10-13 18:49:07
 *
 * @usage:	<?php $this->includeTpl('includes/footer.tpl'); ?>
 */
?>

					<!-- Footer -->
					<div id="bar_footer" class="row">
						<div class="large-24 columns footer_margin"></div>
						<div class="large-24 columns footer_bar">
							<div class="row">
								<div class="large-12 medium-12 columns links"><a href="<?=APPLICATION_URL_SHORT?>/relaties">Relaties</a> <span>&bull;</span> <a href="<?=APPLICATION_URL_SHORT?>/publicaties">Publicaties</a> <span>&bull;</span> <a href="<?=APPLICATION_URL_SHORT?>/verhuur-praktijkruimte">Verhuur praktijkruimte</a></div>
								<div class="large-12 medium-12 columns quote">Het lichaam van A tot Z</div>
							</div>
						</div>
					</div> <!-- end footer -->					
				</div> <!-- end columns -->
				
				
			    <!-- Off Canvas Menu -->
			    <aside class="right-off-canvas-menu">
			    	<div class="container">				    	
						<?php $this->includeTpl('includes/menu.tpl'); ?>						
						<?php $this->includeTpl('includes/leftcolumn.tpl'); ?>
			    	</div>
					
			    </aside>								
				<a class="exit-off-canvas"></a> <!-- close the off canvas -->
				
			</div> <!-- end inner-wrap -->
		</div> <!-- end #container -->
		
		<div id='footer' class="row">
			<div class="columns large-24 medium-24 small-24">
				<div class='socialmedia'>
					<a href='http://www.facebook.com/OsteoVitaal' target='_blank' class='facebook' title='Volg ons op Facebook!'></a>
					<a href='https://plus.google.com/+OsteoVitaalGroningen' class='google' title='Volg ons op Google+!' target='_blank'></a>			
				</div>								
				<p>&copy; OsteoVitaal <?=date('Y')?> <!-- <span>&bull;</span> <a href='<?=APPLICATION_URL_SHORT?>/colofon' target='_blank'>Colofon</a> --></p>
			</div>
		</div>

    	<?php $this->includeTpl('includes/footer/javascript.tpl'); ?>	
	</body>
</html>