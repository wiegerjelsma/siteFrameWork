<?php $this->includeTpl('includes/info/header.tpl'); ?>

<h1>Defines</h1>
<?php 
$a_Constants = get_defined_constants(true);
$a_Vars['a_Table'] = $a_Constants['user'];
$this->includeTpl('includes/info/table.tpl', $a_Vars);
?>

<?php
$a_Types = array('Controller','Module','Bootstrapper');
foreach($a_Types as $type): ?>
<h1><?=$type?>s</h1>
<?php

if(!function_exists('getParentClasses')){
	function getParentClasses($class){
		$reflectionClass = new ReflectionClass($class);
		$parent = (array) $reflectionClass->getParentClass();
	
		$toReturn = '';
		if(array_key_exists('name', $parent)) {
			$toReturn .= $parent['name'];
			$extends = getParentClasses($parent['name']);
			$toReturn .=  ($extends) ? ','.$extends : '';
		} else
			return false;
		return $toReturn;
	}
}

$a_Modules = Loader::getModules($type);
$a_Includes = Loader::getIncludes($type);
$a_ToAssign = array();
foreach($a_Modules as $module){		
	$count = count($a_Includes[$module]);
	for($i=0; $i<$count; $i++)
		require_once $a_Includes[$module][$i]['directory'].$a_Includes[$module][$i]['filename'];
						
	$key = $module;
	$value = array();
	$a_Class = array_pop($a_Includes[$module]);		
	$parent = $a_Class['class'].','.getParentClasses($a_Class['class']);
	$a_Parents = explode(',', $parent);
	$a_ToAssign[$key] = join(' <strong><em>extends</em></strong> ', $a_Parents);	
}
$a_Vars['a_Table'] = $a_ToAssign;;
$this->includeTpl('includes/info/table.tpl', $a_Vars);
endforeach;
$this->includeTpl('includes/info/footer.tpl'); 
?>
