<script type="text/javascript" src="<?=WEBROOT?>/bower_components/jquery/dist/jquery.min.js"></script>	
<script type="text/javascript">	
$('body').data('THIS_URL', "<?=THIS_URL?>");
$('body').data('WEBROOT', "<?=WEBROOT?>");			
$('body').data('CAPTCHARIGHT', "<?php if(isset($this->captchaRight)) echo $this->captchaRight; ?>");
$('body').data('CAPTCHALEFT', "<?php if(isset($this->captchaLeft)) echo $this->captchaLeft; ?>");



<?php if(defined('SHORT_URL')):?>
$('body').data('SHORT_URL', '<?=SHORT_URL?>');
<?php else: ?>
$('body').data('SHORT_URL', '');				
<?php endif; ?>
    	
</script>    	
<script type="text/javascript" src="<?=WEBROOT?>/bower_components/foundation/js/foundation.min.js"></script>
<script type="text/javascript" src="<?=WEBROOT?>/bower_components/imagesloaded/imagesloaded.pkgd.min.js"></script>		

<?php if(defined('SHORT_URL') && (SHORT_URL == 'groningen' or SHORT_URL == 'ureterp' or SHORT_URL == 'dokkum')): ?>
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
	<?php endif; ?>


<script type="text/javascript" src="<?=WEBROOT?>/js/app.js"></script>
		
<!-- Google Analytics Tag -->
<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-18619443-12']);
_gaq.push(['_trackPageview']);

(function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>			