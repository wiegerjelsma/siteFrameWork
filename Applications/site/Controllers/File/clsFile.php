<?php
/**
 * @name	c_site_FilesException
 * @author 	wiegerjelsma
 * @version	1.0 2013-04-04 16:17:06
 */
class c_site_FileException extends c_site_ControllerException {}

/**
 * @name	c_site_Files
 * @author 	wiegerjelsma
 * @version	1.0 2013-04-04 16:17:06
 */
class c_site_File extends c_site_Controller {
	
	/**
	 * @name	init
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2013-04-04 16:17:06
 	 * @desc	called on creation (from loader)
 	 */	 
	public function init(){
		parent::init();
	}
	
	public function image($filename = false){
		if(!ID_NAME && !$filename)
			return;
			
		$filename = $filename ? $filename : ID_NAME;				
					
		$obj_Files = Loader::loadModule('Files');
		$image = $obj_Files->getImageByFileName($filename);
		if($image){
			if($image['type'] == 'image/jpeg' or $image['type'] == 'image/pjpeg'){			
				
				if($this->cfg['files']['save'] == 'dir'){
					if(is_file(INPUT_BASEAPPLICATION.$this->cfg['files']['dir'].'/'.$filename))
						$image['src'] = file_get_contents(INPUT_BASEAPPLICATION.$this->cfg['files']['dir'].'/'.$filename);						
				}
				
				$this->TPL->assign('File', $image);
				$this->TPL->display('file.tpl');	
			}		
		}
	}
	
	public function imagebyid(){
		if(!ID_NAME)
			return;
			
		$obj_Files = Loader::loadModule('Files');
		$specs = $obj_Files->getFileSpecsById(ID_NAME);
		if($specs){
			$filename = $specs['filename'].ID_PARAM.'.'.$specs['ext'];
			$image = $obj_Files->getImageByFileName($filename);
			if($image){
				if($image['type'] == 'image/jpeg' or $image['type'] == 'image/pjpeg'){			
					
					if($this->cfg['files']['save'] == 'dir'){
						if(is_file(INPUT_BASEAPPLICATION.$this->cfg['files']['dir'].'/'.$filename))
							$image['src'] = file_get_contents(INPUT_BASEAPPLICATION.$this->cfg['files']['dir'].'/'.$filename);
					}
					
					$this->TPL->assign('File', $image);
					$this->TPL->display('file.tpl');	
				}		
			}
		}	
	}	

	public function pdf($filename = false){
		if(!ID_NAME && !$filename)
			return;
			
		$filename = $filename ? $filename : ID_NAME;
		
		$obj_Files = Loader::loadModule('Files');
		$pdf = $obj_Files->getPdfByFileName($filename);
		if($pdf){
			if($pdf['type'] == 'application/pdf'){
				$this->TPL->assign('File', $pdf);
				$this->TPL->display('file.tpl');	
			}		
		} else {
			dump($filename);
		}
	}
	
	public function view(){
		if(!ID_NAME && !$filename)
			return;
						
		$a_Elmts = explode('.', ID_NAME);	
		$ext = array_pop($a_Elmts);
		$filename = join('.', $a_Elmts);
		switch($ext){
			case 'pdf':
				$this->pdf($filename);
			break;
			case 'jpg':
				$this->image($filename);
			break;
		}
	}	
}
