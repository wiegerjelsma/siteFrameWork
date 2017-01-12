<?php 
$a_Json = array();

$obj_Images = Loader::loadModule('Files');
$a_Images = $obj_Images->getImages();

foreach($a_Images as $image){

	$thmb = PROTOCOL.'://'.APPLICATION_DOMAIN.'/site.front/file/image/'.$image['filename'].'-admin.jpg';
	$img = PROTOCOL.'://'.APPLICATION_DOMAIN.'/site.front/file/image/'.$image['filename'].'-l.jpg';
	$title = $image['name'];
		
	$a_Json[] = array("thumb" => $thmb, "image" => $img, 'title' => $title);
}

echo json_encode($a_Json);
?>