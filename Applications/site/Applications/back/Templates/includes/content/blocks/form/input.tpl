<?php
/**
 * @name	includes/content/blocks/form/input.tpl
 * @version	1.0 2011-11-20 15:12:17
 *
 * @usage:	<?php $this->includeTpl('includes/content/blocks/form/input.tpl'); ?>
 */
?>


<?php  
// make sure everything isset
$label = isset($label) ? $label : false;
$sublabel = isset($sublabel) ? $sublabel : false;
$sublabel_link = isset($sublabel_link) ? $sublabel_link : false;
$labelclass = isset($labelclass) ? $labelclass : false;
$type = isset($type) ? $type : false;
$required = isset($required) ? $required : false;
$required_on = isset($required_on) ? $required_on : false;
$name = isset($name) ? $name : false;
$value = isset($value) ? $value : false;
$values = isset($values) ? $values : false;
$dataset = isset($dataset) ? $dataset : false;
$error = isset($error) ? $error : false;
$hr = isset($hr) ? $hr : false;
$size = isset($size) ? $size : false;
$readonly = isset($readonly) ? $readonly : false;
$readonly_on = isset($readonly_on) ? $readonly_on : false;
$hideReadOnlyTag = isset($hideReadOnlyTag) ? $hideReadOnlyTag : false;
$format = isset($format) ? $format : false;
$show_on = isset($show_on) ? $show_on : false;
$addoredit = isset($addoredit) ? $addoredit : false;
$display = isset($display) ? $display : false;
$placeholder = isset($placeholder) ? $placeholder : false;
$gridtype = $type == 'gridtype' ? true : false;
$textarea_height = isset($textarea_height) ? $textarea_height : false;
$block_css = isset($block_css) ? $block_css : false;
$block_width = isset($block_width) ? $block_width : false;
$block_id = isset($block_id) ? $block_id : false;
$block_header = isset($block_header) ? $block_header : false;
$attributes = isset($attributes) ? $attributes : false;
$language = isset($language) ? $language : false;
$directorykey = isset($directorykey) ? $directorykey : false;
?>

<?php if($type == 'blockseperator'): ?>

<?php	
$this->includeTpl('includes/content/blocks/includes/footer.tpl');
$a_Params['type'] = 'form';
$a_Params['css'] = $block_css;
$a_Params['width'] = $block_width;		
$a_Params['id'] = $block_id;
$a_Params['attributes'] = $attributes;
		
$this->includeTpl('includes/content/blocks/includes/header.tpl', $a_Params);
?>
<?php if($block_header): ?>
<div class='form-header'>
	<h3><?=$block_header?></h3> 
</div>
<?php endif; ?>

<?php else: ?>

<?php if(!$show_on or ($show_on == $addoredit)): ?>

<?php if($type == 'divider'): ?>
<?php if($hr): ?><hr><?php endif; ?>

<?php if($label && !$gridtype): ?>			
<div class='form-header'>
	<?php if($label): ?><h4 class='<?=$labelclass?>'><?=$label?></h4><?php endif; ?>
	<?php if($sublabel): ?><p><?=$sublabel?></p><?php endif; ?>	
</div>
<?php endif; ?>	
			
<?php else: ?>

<?php if($type != 'hidden' && $display != 'inline'): ?>
<div class='form-row odd<?=isset($error) && $error ? ' error' : ''?><?=$type == 'wysiwyg' ? ' wysiwyg':''?><?=$type == 'textarea' ? ' text':''?><?=$size ? ' '.$size : ''?><?=$gridtype ? ' grid six' : ''?>'>	
		<?php if(!$gridtype): ?><h6><?=$label?><?=$required?' *':($required_on == $addoredit ? ' *' : '')?></h6><?php endif; ?>
<?php endif; ?>	

<?php

switch($type){
	default:
		echo "<input type='text' value='".str_replace("'","&#39;",$value)."' name='".$name."' class='";
		if($size)
			echo $size;		
		if($readonly or ($readonly_on == $addoredit && $addoredit)){
			echo " disabled' readonly";
		} else {
			echo "'";		
		}
		if($placeholder)
			echo " innerlabel='".$placeholder."'";
		echo " />";
	break;	
	case 'themaselector':	
		$a_Ids = array();
		$a_Themas = array();

		if($value)
			foreach($value as $tag){				
				$a_Ids[] = $tag['thema_id'];
				$a_Themas[] = ucfirst($tag['thema']);			
			}	
		$id = rand();
	
		echo "<input id='".$id."' type='text' value='".join(', ', $a_Themas)."' name='".$name."_displayed' ";
		echo "class='disabled' readonly";		
		echo " />";
		
		echo "<input id='".$id."-ids' type='hidden' value='".join(',', $a_Ids)."' name='".$name."' />";
		echo "<input type='button' fire='themaselector' field_id='".$id."' field_name='".$name."' class='button inline' value='zoeken' />";		
	break;
	case 'gridtype':
		for($i=0; $i<6; $i++){
			$_value = $value ? $value : (isset($dataset[$name.'_'.$i]) ? $dataset[$name.'_'.$i] : false);				
			echo "<input type='text' value='".str_replace("'","&#39;",$_value)."' name='".$name.'_'.$i."' class='";
			if($size)
				echo $size;		
			if($readonly or ($readonly_on == $addoredit && $addoredit)){
				echo " disabled' readonly";
			} else {
				echo "'";		
			}
			if($placeholder)
				echo " innerlabel='".$placeholder."'";
			echo " />";	
		}
	break;
	case 'assessment':
		$a_Answers = array(1 => 'Strongly Disagree','Disagree','Neutral','Agree','Strongly Agree');
		foreach($a_Answers as $key => $answer){
			echo "<input type='radio' name='".$name."' value='".$key."' ";
			if($value == $key)
				echo 'checked';		
			echo "/>";
			echo '<p class=\'assessment\'>'.$answer.'</p>';					
		}
	break;
	case 'textarea':
		echo "<textarea name='".$name."' ";
		echo $textarea_height ? "style='height:".$textarea_height."px;'" : '';	
		if($readonly or ($readonly_on == $addoredit && $addoredit))
			echo "class='disabled' readonly";		
		echo ">".$value."</textarea>";
	break;
	case 'wysiwyg':	
	
		$value = $value ? '<p class="dummy">Voeg hier iets toe.</p>'.$value.'<p class="dummy">Voeg hier iets toe.</p>' : '<p class="dummy">Voeg hier iets toe.</p>';
		echo "<div class='editor'>";
	
	
		echo "<textarea id='".$name."' name=".'"'.$name.'"';
		echo $textarea_height ? "style='height:".$textarea_height."px;'" : '';	
		if($readonly or ($readonly_on == $addoredit && $addoredit))
			echo "class='disabled' readonly";		
		echo ">".$value."</textarea>";
		?>
		<script type='text/javascript'>
		
		$(function(){
			$('#<?=$name?>').redactor({
				buttons : ['html', '|','formatting','|', 'bold', 'italic', '|', 'unorderedlist', 'orderedlist','|','image', 'video', '|', 'link','|','table', '|', 'horizontalrule']
				,plugins: ['clips','myimages']							
				,observeImages: true
				,pastePlainText: true
				,convertDivs: false
				,formattingTags: ['p', 'h2', 'h3', 'h5']
				,allowedTags: ['p', 'h2', 'h3', 'h5', 'a', 'strong', 'em', 'img', 'div', 'ul', 'li', 'iframe', 'br', 'table','tr','td','th','tbody']
				,boldTag : 'strong'
				,italicTag : 'em'	
				,iframe: true
				,convertVideoLinks: true
				,imageFloatMargin: '0'
				,imageGetJson: '<?=PROTOCOL.'://'.APPLICATION_DOMAIN.'/jsonimages'?>'
				,predefinedLinks: '<?=PROTOCOL.'://'.APPLICATION_DOMAIN.'/jsonmenu'?>'
				,css: '<?=WEBROOT?>/css/redactor_styles.css?version=1.3.1' 
				,changeCallback: function(html)
				{					
					redactor_clean(html, this);				
				}
				,initCallback: function(html)
				{										
					
					var frame = this.$frame;
					var iframe = $('iframe').contents().get(0);
					$('body').data('redactor', this);
					$(iframe, 'p.dummy').click(function(event){
						if($(event.target).hasClass('dummy')){
							$(event.target).removeClass('dummy');
							$('#<?=$name?>').redactor('selectionElement', event.target);
						}						
					});															
				}	
				,blurCallback : function(html){
					if(!$('body').data('modalOpen'))
						setDummy(this.get(), this);
					
					setDummyListener();						
				}
				,modalOpenedCallback: function()
					{
						$('body').data('modalOpen', true);
					}
				,modalClosedCallback: function(html)
					{
						$('body').data('modalOpen', false);
					}
				,syncBeforeCallback: function(html)
					{
						var currentsrc = $('<div id="currentsrc" style="display: none;"/>');
						$('body').append(currentsrc);
						$('#currentsrc').html(html);
						$('#currentsrc').find('.dummy').remove();
						var newHtml = $('#currentsrc').html();
						$('#currentsrc').remove();
					
						return newHtml;						
					}								
			});
		});
			
			$('body').data('modalOpen', false);
			$('body').data('to', false);
		
			function setDummyListener(){
				var iframe = $('iframe').contents().get(0);
				$('body').data('redactor', this);
				$(iframe, 'p.dummy').click(function(event){
					if($(event.target).hasClass('dummy')){
						$(event.target).removeClass('dummy');
						$('#<?=$name?>').redactor('selectionElement', event.target);
					}						
				});				
			}
			
			function redactor_clean(src, editor){				
				$('body').data('src', src);
				$('body').data('editor', editor);
				
				if($('body').data('to'))
					clearTimeout($('body').data('to'));
										
					$('body').data('to', setTimeout(function(){						
						if(!$('body').data('modalOpen')){
							var newsrc = cleanPanels($('body').data('src'));						
							newsrc = cleanImages(newsrc);
						//	newsrc = cleanDummy(newsrc);
							if(newsrc && newsrc != $('body').data('src')){
								editor.set(newsrc);							
							}	
							editor.observeImages();																		
						}						
					}, 500));									
			}	
			
			
			function cleanPanels(src){
				src = src.replace('<p><div class="panel"','<div class="panel"');
				return src.replace('</div></p>','</div>');
			}
			
			function cleanImages(src){
				var currentsrc = $('<div id="currentsrc" style="display: none;"/>');			
				$('body').append(currentsrc);
				$('#currentsrc').html(src);
				
				$('#currentsrc').find("Img").each(function(){
					var style = $(this).attr('style');
					if(style == 'margin: auto; display: block;' || style == 'display: block; margin: auto;'){					
						$(this).removeClass('left');										
						$(this).removeClass('right');
						$(this).removeAttr('style');												
					} else {
						if(style == 'float: left; margin: 0px;'){
							$(this).removeAttr('style');
							$(this).addClass('left');										
							$(this).removeClass('right');
						} else {
							if(style == 'float: right; margin: 0px;'){								
								$(this).removeAttr('style');
								$(this).addClass('right');										
								$(this).removeClass('left');							
							}							
						}
					}
				});
								
				var newSrc = $('#currentsrc').html();
				$('#currentsrc').remove();
				return newSrc;
			}
			
			function cleanDummy(src){
				var currentsrc = $('<div id="currentsrc" style="display: none;"/>');
				$('body').append(currentsrc);
				$('#currentsrc').html(src);
				$('#currentsrc').find('.dummy').remove();

				var newSrc = $('#currentsrc').html();
				$('#currentsrc').remove();
				
				return newSrc;												
			}
			
			function setDummy(src, editor){
				if(editor.getSelectionText())
					return;
				
				var currentsrc = $('<div id="currentsrc" style="display: none;"/>');
				$('body').append(currentsrc);
				$('#currentsrc').html(src);
				$('#currentsrc').find('.dummy').remove();
				
				if($('#currentsrc').html() != '')
					editor.set('<p class="dummy">Voeg hier iets toe.</p>'+$('#currentsrc').html()+'<p class="dummy">Voeg hier iets toe.</p>');
				else
					editor.set('<p class="dummy">Voeg hier iets toe.</p>');
				
				setDummyListener();
				$('#currentsrc').remove();
				return;
			}
				
		
		</script>			
		<?php	
		echo "</div>";	
		echo "<div style='clear: both'></div>";			
	break;
	case 'hidden':
		echo "<input type='hidden' value='".str_replace("'","&#39;",$value)."' name='".$name."' />";
	break;
	case 'checkbox':
		echo "<input type='checkbox' name='".$name."' value='1' ";
		if($value)
			echo 'checked';		
		echo "/>";
	break;	
	case 'password':
		echo "<input type='password' name='".$name."' />";
	break;
	case 'datetime':	
		echo "<input type='hidden' value='".str_replace("'","&#39;",$value)."' name='".$name."' />";
	
		if($readonly){
			echo '<p class="readonlycontent">'.$this->format($value, $type, $format).'</p>';
			break;
		}	
	break;
	case 'fileuploaddb':
		echo '<input type="file" name="'.$name.'" id="'.$name.'" />';
	
	break;
	case 'fileupload':
		
		if($value){
			echo '<div class="image" fire="imagepopup"  imagesrc="'.APPLICATION_URL.'/file/image/'.$value.'-admin-l.jpg'.'" style="background: #fff url('."'".APPLICATION_URL.'/file/image/'.$value.'-admin.jpg'."'".') no-repeat center center;"></div>';
			echo "<input type='text' value='".$value."' name='".$name."_displayed' class='";
			if($size)
				echo $size;		
			echo " disabled' readonly />";
			
			echo "</div><div class='form-row odd";
			echo isset($error) && $error ? ' error' : '';
			echo $type == 'textarea' ? ' text' : '';
			echo $size ? ' '.$size : '';
			echo "'>";
			echo '<h6>&nbsp;</h6>';			
		}			
	
		echo '<input type="file" name="src" id="'.$name.'" />';		
				
	break;
/*	case 'fileupload':
		$directory = isset($directorykey) && $directorykey ? $directorykey : strToLower(CONTROLLER_NAME);
		$preview = ($value && !preg_match('/\.jpg$/', $value)) ? true : false;
		$file = is_file(ROOTFRAMEWORK.APPLICATIONS_DIR.FWPREFIX.'/'.APPLICATIONS_DIR.'back/'.ROOT_DIR.$this->cfg['filesystem']['desdir'][$directory].'/'.$value.'-admin.jpg') ? true : false;
		
		if($preview){		
			echo '<div class="image" fire="imagepopup"  imagesrc="'.WEBROOT_SITE_BACK.'/'.$this->cfg['filesystem']['desdir'][$directory].'/'.$value.'-admin-l.jpg"'.' style="background: #fff url('."'".WEBROOT_SITE_BACK.'/'.$this->cfg['filesystem']['desdir'][$directory].'/'.$value.'-admin.jpg'."'".') no-repeat center center;"></div>';
			echo "<input type='hidden' value='".str_replace("'","&#39;",$value)."' name='".$name."-mask' />";
		}
		if($value && !$preview){
			echo "<input type='hidden' value='".str_replace("'","&#39;",$value)."' name='".$name."_stored' />";			
			echo "<input type='hidden' value='".str_replace("'","&#39;",$value)."' name='".$name."_displayed' />";			
		}

		if($value){	
			if(isset($dataset[$name.'_displayed'])){
				echo "<input type='hidden' value='".str_replace("'","&#39;",$value)."' name='".$name."' />";		
				echo "<input type='text' value='".str_replace("'","&#39;",$dataset[$name.'_displayed'])."' name='".$name."_displayed' class='";
				if($size)
					echo $size;		
				echo " disabled' readonly />";
			} else {
				echo "<input type='text' value='".str_replace("'","&#39;",$value)."' name='".$name."' class='";
				if($size)
					echo $size;		
				echo " disabled' readonly />";
			}
		}
		if(!$file && $preview)
			echo '<p>De image is nog niet verwerkt.</p>'; 
		
		if($preview or $value){		
			echo "</div><div class='form-row odd";
			echo isset($error) && $error ? ' error' : '';
			echo $type == 'textarea'?' text':'';
			echo $size ? ' '.$size : '';
			echo "'>";
			echo '<h6>&nbsp;</h6>';
		}
						
		echo '<input type="file" name="'.$name.'" id="'.$name.'" />';		
	break; */
	case 'date':
		if($readonly){
			echo '<p class="readonlycontent">'.$this->format($value, $type, $format).'</p>';
			break;
		}
		
		if($value)
			list($year, $month, $day) = explode('-', $value);
		else
			list($year, $month, $day) = explode('-', date('Y-m-d'));
	
		// Dagen
		echo "<select name='".$name."_day'>";
		for($i=1; $i<32; $i++){
			echo "<option value='".$i."'";
			if($day == $i)
				echo ' selected';
			echo ">".$i."</option>";			
		}
		echo "</select>";
		
		// maanden
		echo "<select name='".$name."_month'>";
		for($i=1; $i<13; $i++){
			echo "<option value='$i'";
			if($month == $i)
				echo ' selected';
			echo ">".$this->cfg['months'][$i]."</option>";			
		}
		echo "</select>";
		
		// jaren
		$startyear = date('Y')-1;
		echo "<select name='".$name."_year'>";
		for($i=$startyear; $i<=$startyear+2; $i++){
			echo "<option value='$i'";
			if($year == $i)
				echo ' selected';
			echo ">$i</option>";			
		}
		echo "</select>";
	break;
	case 'added_by_user':
	//	echo "<input type='hidden' value='".$value."' name='".$name."' />";
		
		if($value){	
			list($added, $user) = explode('[split]', $value);		
			if($readonly){
				echo '<p class="readonlycontent">'.$this->format($added, 'datetime', $format).' <span class="small">door</span> '.$user.'</p>';
				break;
			}	
		} else {
				echo '<p class="readonlycontent">Not set</p>';
				break;		
		}
	
	break;
	case 'edit_by_user':
		echo "<input type='hidden' value='".$value."' name='".$name."' />";
		if(!$value)
			echo '<p class="readonlycontent">Nog niet gewijzigd</span>';	
		else {
			list($edit, $user) = explode('[split]', $value);		
			if($readonly && $edit != '0000-00-00 00:00:00'){
				echo '<p class="readonlycontent">'.$this->format($edit, 'datetime', $format).' <span class="small">door</span> '.$user.'</p>';
				break;
			} else
				echo '<p class="readonlycontent">Nog niet gewijzigd</span>';	
		}
	break;
	case 'content':
		echo '<p class="readonlycontent">'.$value.'</span>';	
	break;	
	case 'status_online-offline':
		echo "<select name='".$name."'";		
		if($size)
			echo ' class="'.$size.'"';		
		echo ">";		

		if($placeholder)
			echo "<option value='0'>".$placeholder."</option>";
				
		echo "<option value='1'";
		if($value == 1)
			echo ' selected';
		echo ">online</option>";
		echo "<option value='0'";
		if($value == 0)
			echo ' selected';
		echo ">offline</option>";
		echo "</select>";
	break;
	case 'status_actief-inactief':	
		echo "<select name='".$name."'";		
		if($size)
			echo ' class="'.$size.'"';		
		echo ">";		

		if($placeholder)
			echo "<option value='0'>".$placeholder."</option>";
		echo "<option value='1'";
		if($value == 1)
			echo ' selected';
		echo ">actief</option>";
		echo "<option value='0'";
		if($value == 0)
			echo ' selected';
		echo ">inactief</option>";
		echo "</select>";
	break;	
	case 'pulldown':
		echo "<select name='".$name."'>";	
		if($readonly){
			if($values)
				foreach($values as $key => $val){
					if($value == $key){
						echo "<option value='".str_replace("'","&#39;",$key)."'";					
						echo ' selected';
						echo ">".$val."</option>";					
					}
				}		
		} else {
			echo '<option value="0">Select a value</option>';		
			if($values)
				foreach($values as $key => $val){
					echo "<option value='".str_replace("'","&#39;",$key)."'";
					if($value == $key)
						echo ' selected';
					echo ">".$val."</option>";			
				}
		
		}
		echo "</select>";
	break;
	case 'predefined_page':
		$obj_Pages = Loader::loadModule('Cms.Pages');
		$a_PredefinedPages = $obj_Pages->get(array('is_predefined' => true));

		echo "<select name='".$name."'>";
		if(!$readonly)
			echo '<option value="0">Selecteer een soort</option>';
		if($a_PredefinedPages)	
			foreach($a_PredefinedPages as $page){
				if($readonly){					
					if($value == $page['id']){
						echo "<option value='".$page['id']."'";
						echo ' selected';
						echo ">".$page['name']."</option>";				
					}				
				} else { 
					echo "<option value='".$page['id']."'";
					if($value == $page['id'])
						echo ' selected';
					echo ">".$page['name']."</option>";				
				}							
			}
		echo "</select>";			
	break;

	case 'evenement_id':
		$obj_Evenementen = Loader::loadModule('Evenementen');
		$a_Evenementen = $obj_Evenementen->get(array('status' => true), array('order' => 'sequence_id DESC'));
		
		echo "<select name='".$name."'>";
		if(!$readonly)
			echo '<option value="0">Selecteer een evenement</option>';
		if($a_Evenementen)	
			foreach($a_Evenementen as $evenement){
				if($readonly){					
					if($value == $evenement['id']){
						echo "<option value='".$evenement['id']."'";
						echo ' selected';
						echo ">".$evenement['name']."</option>";				
					}				
				} else { 
					echo "<option value='".$evenement['id']."'";
					if($value == $evenement['id'])
						echo ' selected';
					echo ">".$evenement['name']."</option>";				
				}							
			}
		echo "</select>";			
	break;	
	
	case 'page_id':
		$a_Structure = array();
		$obj_Cms = Loader::loadModule('Cms');		
		$a_HoofdMenu = $obj_Cms->getMenu('Hoofdmenu', false, false, true);
		$a_Options = array();
		foreach($a_HoofdMenu['nodes'] as $node){		
			$a_Options[] = array('value' => $node['page_id'], 'name' => $node['name']);
			$submenu = $obj_Cms->getSubMenu($node, false, false, true);			
			foreach($submenu as $subnode){
				$a_Options[] = array('value' => $subnode['page_id'], 'name' => '-- '.$subnode['name']);
				$subsubmenu = $obj_Cms->getSubMenu($subnode, false, false, true);
				foreach($subsubmenu as $subsubnode){
					$a_Options[] = array('value' => $subsubnode['page_id'], 'name' => '---- '.$subsubnode['name']);
				}
			}			
		}

		echo "<select name='".$name."'>";
		if(!$readonly)
			echo '<option value="0">Selecteer een pagina</option>';
		if($a_Options)	
			foreach($a_Options as $option){
				if($readonly){					
					if($value == $option['value']){
						echo "<option value='".$option['value']."'";
						echo ' selected';
						echo ">".$option['name']."</option>";				
					}				
				} else { 
					echo "<option value='".$option['value']."'";
					if($value == $option['value'])
						echo ' selected';
					echo ">".$option['name']."</option>";				
				}							
			}
		echo "</select>";			
	break;	

	case 'page_shorturl':
		$a_Structure = array();
		$obj_Cms = Loader::loadModule('Cms');		
		$a_HoofdMenu = $obj_Cms->getMenu('Hoofdmenu', false, false, true);
		$a_Options = array();
		foreach($a_HoofdMenu['nodes'] as $node){		
			$a_Options[] = array('value' => $node['short_url'], 'name' => $node['name']);
			$submenu = $obj_Cms->getSubMenu($node, false, false, true);			
			foreach($submenu as $subnode){
				$a_Options[] = array('value' => $subnode['short_url'], 'name' => '-- '.$subnode['name']);
				$subsubmenu = $obj_Cms->getSubMenu($subnode, false, false, true);
				foreach($subsubmenu as $subsubnode){
					$a_Options[] = array('value' => $subsubnode['short_url'], 'name' => '---- '.$subsubnode['name']);
				}
			}			
		}

		echo "<select name='".$name."'>";
		if(!$readonly)
			echo '<option value="0">Selecteer een pagina</option>';
		if($a_Options)	
			foreach($a_Options as $option){
				if($readonly){					
					if($value == $option['value']){
						echo "<option value='".$option['value']."'";
						echo ' selected';
						echo ">".$option['name']."</option>";				
					}				
				} else { 
					echo "<option value='".$option['value']."'";
					if($value == $option['value'])
						echo ' selected';
					echo ">".$option['name']."</option>";				
				}							
			}
		echo "</select>";			
	break;	
	
	case 'country':
		echo "<select name='".$name."'>";
		echo '<option value="0">Select a country</option>';		
		foreach($this->cfg['countries'] as $key => $val){
			echo "<option value='".str_replace("'","&#39;",$key)."'";
			if($value == $key)
				echo ' selected';
			echo ">".$val['name']."</option>";			
		}
		echo "</select>";		
	break;
	
	
	case 'userlevel':
		echo "<select name='".$name."'";		
		if($size)
			echo ' class="'.$size.'"';		
		echo ">";		

		if($placeholder)
			echo "<option value='0'>".$placeholder."</option>";

		echo "<option value='user'";
		if($value == 1)
			echo ' selected';
		echo ">user</option>";
		echo "<option value='admin'";
		if($value == 2)
			echo ' selected';
		echo ">admin</option>";
		echo "</select>";	
	break;
	}


?>
<?php if($type != 'hidden'): ?>
<?php
$suffix = array();
if($language)
	$suffix[] = '<img src="'.WEBROOT_SITE_BACK.'/img/flags/'.$language.'.png" />';
 
if(($readonly or ($readonly_on == $addoredit && $addoredit)) && $type != 'date' && $type != 'datetime' && $type != 'added_by_user' && $type != 'edit_by_user' && !$hideReadOnlyTag){
	// We tonen dit niet als we een date field hebben
	$suffix[] = '(Alleen lezen)';
}
if($sublabel_link){
	$suffix[] = '<a href="'.$sublabel_link['url'].'">'.$sublabel_link['name'].'</a>';
}
if($sublabel){
	$suffix[] = $sublabel;
}
if(count($suffix) >= 1)
	echo '<p>'.join(' ', $suffix).'</p>';
?>

<?php if($display != 'inline'): ?>
<div class='clear'>&nbsp;</div></div>
<?php endif; ?>

<?php endif; ?>
<?php endif; ?>	
<?php endif; ?>
<?php endif; ?>	