<?php
/**
 * @name	Application (install script)
 * @author 	wiegerjelsma
 *
 */
class Template extends Install {

	/**
	 * @name	createBaseTemplates
	 * @desc	creates a header, footer and an home.tpl
	 */	
	public function create($tpl, $regIncludes = true){
		if(!is_array($tpl)){			
			$tplname = preg_replace("/\.tpl$/", "", $tpl);	
			$packages = explode('/', $tplname);
			$filename = array_pop($packages).'.tpl';
			$dir = join('/', $packages);
			$dir .= preg_match("/\/$/", $dir) ? '' : '/';
			$tpl = array('tplname' => $filename, 'dir' => $dir, 'filetpl' => 'template.filetpl');		
		}
		if(is_file(ROOTFRAMEWORK.APPLICATIONS_DIR.APPLICATION_DIR.TEMPLATES_DIR.$tpl['dir'].$tpl['tplname'])){
			print "[NOTICE] template ".$tpl['dir'].$tpl['tplname']." already exists\n";
			return;
		}
				
		$src = file_get_contents(ROOTFRAMEWORK.INSTALLJOBS_DIR.'Template/'.$tpl['filetpl']);
		$src = str_replace("#applicationname#", APPLICATION_NAME, $src);
		$src = str_replace("#templatename#", $tpl['tplname'], $src);
		$src = str_replace("#templatedir#", $tpl['dir'], $src);
		$src = str_replace('#date#', date('Y-m-d H:i:s'), $src);
		$this->_writeTemplate($tpl['tplname'], ROOTFRAMEWORK.APPLICATIONS_DIR.APPLICATION_DIR.TEMPLATES_DIR.$tpl['dir'], $src);

		// regenerate includes		
		if($regIncludes)
			$this->createincludes();		
	}
	
	/**
	 * @name	createBaseTemplates
	 * @desc	creates a header, footer and an home.tpl
	 */	
	public function createBaseTemplates(){		
		$a_ToCreate[] = array('tplname' => 'home.tpl', 'dir' => '', 'filetpl' => 'template.home.filetpl');
		$a_ToCreate[] = array('tplname' => 'header.tpl', 'dir' => 'includes/', 'filetpl' => 'template.include.header.filetpl');
		$a_ToCreate[] = array('tplname' => 'footer.tpl', 'dir' => 'includes/', 'filetpl' => 'template.include.footer.filetpl');
		
		foreach($a_ToCreate as $tpl)
			$this->create($tpl, false);
	}
	
	/**
	 * @param string $filename
	 * @param string $dir
	 * @param string $src
	 */
	private function _writeTemplate($filename, $dir, $src){
		if(!is_file($dir.$filename)){
			require_once LIBRARY_DIR.'FileSystem/clsFileSystem.php';
			$obj_FileSystem = FileSystem::singleton();	
			$obj_FileSystem->setCache(false);		
			if($obj_FileSystem->writeFile($dir, $filename, $src, 'overwrite'))
				print "[SUCCESS] Tpl created :: '$filename'\n";					
		} else
			print "[NOTICE] Tpl already exists :: '$filename'\n"; 	
	}
	
}