<?php
/**
 * @name	includes/head/javascript.tpl
 * @version	1.0 2011-11-20 14:40:56
 *
 */
?>

	<script type="text/javascript" src="<?=WEBROOT_FW?>/js/mediaplayer/jwplayer.js"></script>

	<script type="text/javascript" src="<?=WEBROOT_FW?>/js/jquery-1.8.3.min.js"></script>
	<script type="text/javascript" src="<?=WEBROOT_FW?>/js/jquery-ui-1.8.min.js"></script>	
	<script type="text/javascript" src="<?=WEBROOT_FW?>/js/plugins/jquery.jtml.js"></script>	
	<script type="text/javascript" src="<?=WEBROOT_FW?>/js/plugins/jquery.poshytip.min.js"></script>
	<script type="text/javascript" src="<?=WEBROOT_FW?>/js/plugins/jstree/_lib/jquery.cookie.js"></script>

	<script type="text/javascript" src="<?=WEBROOT_FW?>/js/plugins/jstree/jquery.jstree.js"></script>
			
	<script type="text/javascript" src="<?=WEBROOT_FW?>/js/functions/php.min.js"></script>		
	<script type="text/javascript" src="<?=WEBROOT_FW?>/js/classes/class.js"></script>
	
	<script type="text/javascript" src="<?=WEBROOT_SITE_BACK?>/js/classes/module.js"></script>
	<script type="text/javascript" src="<?=WEBROOT_SITE_BACK?>/js/classes/ajax.js"></script>
	<script type="text/javascript" src="<?=WEBROOT_SITE_BACK?>/js/classes/datastore.js"></script>
	<script type="text/javascript" src="<?=WEBROOT_SITE_BACK?>/js/classes/spinner.js"></script>
	<script type="text/javascript" src="<?=WEBROOT_SITE_BACK?>/js/classes/popup.js"></script>	
	<script type="text/javascript" src="<?=WEBROOT_SITE_BACK?>/js/classes/confirm.js"></script>
	<script type="text/javascript" src="<?=WEBROOT_SITE_BACK?>/js/classes/imagepopup.js"></script>
	<script type="text/javascript" src="<?=WEBROOT_SITE_BACK?>/js/classes/loadingpopup.js"></script>
			
	<!-- RedactorPlugins (moet voor de redactor code ingeladen worden) -->
	<?php	
		$obj_FileSystem = Loader::load('FileSystem');
		$dir = APPLICATIONS_DIR.'site/'.APPLICATIONS_DIR.'back/'.ROOT_DIR.'js/redactorplugins/';
		$a_Files = $obj_FileSystem->getFiles($dir);
		$a_Dirs = $obj_FileSystem->getDirs($dir);
		foreach($a_Files as $file){
			if(preg_match('/js$/', $file))
				echo "<script type=\"text/javascript\" src=\"".WEBROOT_SITE_BACK."/js/redactorplugins/".$file."\"></script>";			
		}
		
		foreach($a_Dirs as $d){
			$a_Files = $obj_FileSystem->getFiles($dir.$d);
			foreach($a_Files as $file){
				if(preg_match('/js$/', $file))
					echo "<script type=\"text/javascript\" src=\"".WEBROOT_SITE_BACK."/js/redactorplugins/".$d.'/'.$file."\"></script>";			
				if(preg_match('/css$/', $file))
					echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".WEBROOT_SITE_BACK."/js/redactorplugins/".$d.'/'.$file."\" />";					
			}
		}
		
	?>
	
	
	

	<!-- Redactor -->
<!--	<script type="text/javascript" src="<?=WEBROOT_FW?>/js/plugins/redactor/nl.js"></script>		-->
	<script type="text/javascript" src="<?=WEBROOT_FW?>/js/plugins/redactor/redactor.js"></script>	
	
	
	<?php if(TEMPLATE_NAME == 'statistics'): ?>
	<script type="text/javascript" src="<?=WEBROOT_FW?>/js/highcharts/highcharts.js"></script>	
	<?php endif; ?>
	
	<script type="text/javascript">			
		$(function(){					
		
			$('body').data("Webroot", '<?=WEBROOT_SITE_BACK?>');
			$('body').data("ApplicationUrl", '<?=APPLICATION_URL?>');
			$('body').data("ApplicationUrlAjax", '<?=APPLICATION_URL_AJAX?>');
			$('body').data("FrontendDomainname", '<?=$this->cfg['frontend']['domain'][SERVER]?>');
			
			$('input[innerlabel]').each(function(){
				var value = $(this).val();
				var innerlabel = $(this).attr('innerlabel');
							
				if(!value){
					$(this).addClass('innerlabel');
					$(this).val(innerlabel);					
				}
			});
			
			$('input[innerlabel]').focus(function(){
				if($(this).val() == $(this).attr('innerlabel')){
					$(this).val('');
					$(this).removeClass('innerlabel');
				}
					
			});
						
			$('input[innerlabel]').blur(function(){
				var value = $(this).val();
				var innerlabel = $(this).attr('innerlabel');
				if((value == innerlabel) || !value){
					$(this).val(innerlabel);
					$(this).addClass('innerlabel');
				}
			});
			
			$('div[fire="imagepopup"], img[fire="imagepopup"]').click(function(){				
				var imagepopup = new Imagepopup({
					imagesrc : $(this).attr('imagesrc')
					,moreinfo : $(this).attr('moreinfo') ? $(this).attr('moreinfo') : 0 
				});
			});						
			
			$('a[fire="confirm"]').click(function(){
				var confirm = new Confirm({
					url : $(this).attr('url')
					,title : $(this).attr('msg')			
				});
			});
			
			$('input[type="submit"]').click(function(){				
				var loadingpopup = new Loadingpopup({});
			});		
				
						
			var blocks = $('div.content-block[swap]');
			if(blocks){
				$("select").change(function(){
					var name = $(this).attr('name');
					$('div.content-block[swap="'+name+'"]').hide();
					var value = $(this).val();
					$('div.content-block[swap_value="'+value+'"]').show();					
				});
				$("select").each(function(){
					var value = $(this).val();
					$('div.content-block[swap_value="'+value+'"]').show();									
				});				
			}	
					
		});
	</script>
