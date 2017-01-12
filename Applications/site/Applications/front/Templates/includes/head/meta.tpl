<?php
/**
 * @name	includes/head/meta.tpl
 * @version	1.0 2013-10-13 18:49:17
 *
 * @usage:	<?php $this->includeTpl('includes/head/meta.tpl'); ?>
 */
?>

	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		
	<?php if($this->isTrue('Meta_Title')): ?>
	<title><?=$this->Meta_Title?></title>
	<?php else: ?>
	<title>Osteopaat, Osteopathie | Groningen | Ureterp</title>
	<?php endif; ?>	
	
	<meta name="description" lang="nl" content="<?=$this->Meta_Description?>" />
	<meta name="keywords" content="<?=$this->Meta_Keywords?>" />
		
	<meta http-equiv="Content-Language" content="nl" />
	<meta http-equiv="language" content="NL" />
	<meta http-equiv="content-style-type" content="text/css" />
	<meta http-equiv="content-script-type" content="text/javascript" />	
	<meta name="copyright" content="Copyright OsteoVitaal <?=date('Y')?>" />
	<meta name="author" content="Anne-Ruurd Hoogeveen" />	
	<meta name="robots" content="index, follow" />
	<meta name="googlebot" content="noodp" />

	<?php if($this->isTrue('Fb_Sharer_Title')): ?>
	<meta property='og:title' content="<?=$this->Fb_Sharer_Title?>" />
	<?php else: ?>
	<meta property='og:title' content="Osteopaat, Osteopathie | Groningen | Ureterp" />
	<?php endif; ?>	
	<meta property='og:type' content='website' />	
	<meta property='og:url' content='<?=THIS_URL?>'/>
	<meta property='og:image' content="http://osteovitaal.nl/OGImage.png"/>
	<meta property="og:image:height" content="630" />
	<meta property="og:image:width" content="1200" />	
	
	<?php if($this->isTrue('Fb_Sharer_Description')): ?>
	<meta property='og:description' content="<?=$this->Fb_Sharer_Description?>" />
	<?php else: ?>
	<meta property='og:description' content="Osteopathie in Groningen en Friesland" />
	<?php endif; ?>	
	
	<meta property='og:site_name' content='Osteopaat, Osteopathie | Groningen | Ureterp'/>	
		
	<meta http-equiv="Expires" content="Tue, 01 Jan 1995 12:12:12 GMT" />
	<meta http-equiv="Pragma" content="no-cache" />		
	
	<link rel="canonical" href="<?=THIS_URL?>" />
	<link rel="publisher" href="https://plus.google.com/+OsteoVitaalGroningen" />	
	
<meta name="google-site-verification" content="hUk00SkLw5Ub9IWpNhz-Nsor39d4S2ciPTVvTUPIRYs" />	
