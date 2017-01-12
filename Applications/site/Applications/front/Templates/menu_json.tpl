<?php 
$a_Json = array();

$a_Json[] = array("name" => 'PAGINA\'S',"url" => false);

$strip = PROTOCOL.'://'.APPLICATION_DOMAIN;
$obj_Cms = Loader::loadModule('Cms');
foreach($this->Hoofdmenu['nodes'] as $a_Node){
	$a_Json[] = array("name" => '&bull; '.$a_Node['name'],"url" => str_replace($strip,'',$a_Node['url']));
	$a_Submenu = $obj_Cms->getSubMenu($a_Node, false, false, true);
		
	foreach($a_Submenu as $a_sub_Node)
		$a_Json[] = array("name" => "&nbsp;&nbsp;&bull; ".$a_sub_Node['name'],"url" => str_replace($strip,'',$a_sub_Node['url']));
} 

$a_Json[] = array("name" => 'NIEUWSBERICHTEN', "url" => false);

$obj_Nieuws = Loader::loadModule('Nieuws');
$a_Nieuws = $obj_Nieuws->get(array('status' => true), array('order' => 'datum DESC'));
foreach($a_Nieuws as $bericht)
	$a_Json[] = array("name" => '&bull; '.$bericht['titel'],"url" => '/nieuws/'.$bericht['short_url']);

$a_Json[] = array("name" => 'BLOGS', "url" => false);

$obj_Blog = Loader::loadModule('Blog');
$a_Blog = $obj_Blog->get(array('status' => true), array('order' => 'datum DESC'));
foreach($a_Blog as $bericht)
	$a_Json[] = array("name" => '&bull; '.$bericht['titel'],"url" => '/blog/'.$bericht['short_url']);

echo json_encode($a_Json);
?>