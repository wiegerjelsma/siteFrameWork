<?php
/**
 * @name	cron_frontend_ImageException
 * @author 	wiegerjelsma
 * @version	1.0 2011-03-16 11:02:27
 */
class cron_frontend_ImageException extends cron_CronjobException {}

/**
 * @name	cron_frontend_Image
 * @author 	wiegerjelsma
 * @version	1.0 2011-03-16 11:02:27
 */
class cron_Image extends cron_Cronjob {

	private $a_Images = array();
	
	private $obj_FileSystem = '';
	
	private $DB;
	private $obj_Module;

	/**
	 * @name 	handle
	 */
	public function handle(){
		return; 		
 		$this->obj_Module = Loader::loadModule('Files');	

		$a_ErrMsg = array();
		
		# Vars: 
		# 	width (breedte waarnaar we toe gaan resize, comma seperated)
		#	suffix (de toevoeging na de bestandsnaam, comma seperated)
		#	prefix (de toevoeging voor de bestandsnaam, comma seperated)
		#	srcdir (de directory waar we de images uithalen)
		#	desdir (de directory waar we de images in gaan zetten)
		try {
			
			$this->a_Images = $this->obj_Module->getImagesForResize();
			
			
			
			if($this->a_Images){
				foreach($this->a_Images as $image){			

					if(!preg_match('#image/jpeg#', $image['type']))
						continue;
					
					try {
					
					
//						dump(substr($image['src'], 0, 10));
					
						$b = imagecreatefromstring(base64_decode($image['src']));
						if(!$b)
							throw new Exception("Unable to create img from source"); 
						
						$a_Prefix = false;
						$a_Suffix = false;
						$a_Sizes = (strpos($this->vars['width'], ',') !== false) ? explode(',', $this->vars['width']) : array($this->vars['width']);
						if(array_key_exists("prefix", $this->vars))
							$a_Prefix = (strpos($this->vars['prefix'], ',') !== false) ? explode(',', $this->vars['prefix']) : array($this->vars['prefix']);
						if(array_key_exists("suffix", $this->vars))
							$a_Suffix = (strpos($this->vars['suffix'], ',') !== false) ? explode(',', $this->vars['suffix']) : array($this->vars['suffix']);
										
						$a_Elem = explode('.', $image);
						$ext = array_pop($a_Elem);
						$imgName = join('.', $a_Elem);					
					
						// create image from source
				//		$b = imagecreatefromjpeg(INPUT_APPLICATION.$this->vars['srcdir'].'/_'.$image);
				//		if(!$b)
				//			throw new Exception("Unable to create img from jpg"); 
							
						// resize the different sizes	
  						for($i=0; $i<count($a_Sizes); $i++){  						
  							$prefix = $a_Prefix ? (array_key_exists($i, $a_Prefix) && $a_Prefix[$i] ? $a_Prefix[$i].'-' : ($a_Prefix[count($a_Prefix)-1] ? $a_Prefix[count($a_Prefix)-1].'-' : '')) : '';
  							$suffix = $a_Suffix ? (array_key_exists($i, $a_Suffix) && $a_Suffix[$i] ? '-'.$a_Suffix[$i] : ($a_Suffix[count($a_Suffix)-1] ? '-'.$a_Suffix[count($a_Suffix)-1] : '')) : '';
  						//	$desdir = $a_Desdir ? (array_key_exists($i, $a_Desdir) && $a_Desdir[$i] ? $a_Desdir[$i] : ($a_Desdir[count($a_Desdir)-1] ? $a_Desdir[count($a_Desdir)-1] : '')) : '';
  							
  							//$this->obj_FileSystem->makeDir($desdir);
  							
  							$resized = $this->_resize($b, $a_Sizes[$i]);
  							$filename = $prefix.$imgName.$suffix.'.'.$ext;
  						dump($filename,'$filename');	
  						//	$this->obj_Module->saveResizedImage($resized, $filename, $image['id']);
  						}
  						
  						// delete the file
  					//	if(!$this->obj_FileSystem->deleteApplicationInputFile('_'.$image, $this->vars['srcdir']))
  					//		throw new Exception("Unable to delete source image from directory");
  					
  					} catch(Exception $e){
 	 					$a_ErrMsg[] = '['.date("Y-m-d H:i:s").'] : '.$image['id'].' : '.$e->getMessage();
  					}
  					
  					if(count($a_ErrMsg) >= 1){
  					dump($a_ErrMsg,'$a_ErrMsg');
  						throw new Exception("Errors occured");
  					
  					}
				}
			} else {
				dump('geen images gevonden');
			}			
		} catch(Exception $e){
			dump("[CRON ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			
			$a_Params['messages'] = $a_ErrMsg;
			$this->sendEmailException($e, $a_Params);			
		}
	}
	
	/**
	 * @name	_resize
	 * @access	private
	 */
	private function _resize($srcimage, $w){
		$bx = imagesx($srcimage);   //breedte van het origineel
  		$by = imagesy($srcimage);   //hoogte van het origineel
  		
		$width = $w;
  		$height = $by/($bx/$width); 
  		
  		$newimage = imagecreatetruecolor($width, $height);
  		if($newimage){
  			if(!imagecopyresampled($newimage, $srcimage, 0, 0, 0, 0, $width, $height, $bx, $by))
  				throw new Exception("Unable to copy image from sample : (imagecopyresampled)"); 
  								
  			if(!imageDestroy($srcimage))
  				throw new Exception("Unable to destroy blankImg : (imageDestroy)");	
  				
			ob_start();
    		imagejpeg($newimage, null, 100);
    		
  			if(!imageDestroy($newimage))
  				throw new Exception("Unable to destroy newimage : (imageDestroy)");	    		
    		
    		return ob_get_clean();  			
  			
  		} else
  			throw new Exception("Unable to create blank image");
  		return true;  		 					
	}
	
	
	/**
	 * @name	delete
	 * @access	public
	 */
	public function delete(){
		try {
			$obj_Images = Loader::loadModule('Images');
			$a_Images = $obj_Images->get(array('controller' => $this->vars['controller']));
			
			$a_Apps = explode(',', $this->vars['apps']);
			if(array_key_exists("suffix", $this->vars))
				$a_Suffix = (strpos($this->vars['suffix'], ',') !== false) ? explode(',', $this->vars['suffix']) : array($this->vars['suffix']);
			
			if($a_Images)
				foreach($a_Images as $image){
					for($i=0; $i<count($a_Suffix); $i++){
							
						$application = $a_Apps ? (array_key_exists($i, $a_Apps) && $a_Apps[$i] ? $a_Apps[$i] : ($a_Apps[count($a_Apps)-1] ? $a_Apps[count($a_Apps)-1] : '')) : '';
 						$applicationDir = join('/'.APPLICATIONS_DIR, explode('.', $application));
 				
						$dir = ROOTFRAMEWORK.APPLICATIONS_DIR.$applicationDir.'/'.ROOT_DIR.$this->cfg['filesystem']['desdir'][$this->vars['controller']];
						$file = $dir.'/'.$image['image'].'-'.$a_Suffix[$i].'.jpg';
						if(is_file($file))
							unlink($file);
					}
					// hier het record uit de DB verwijderen.
					$obj_Images->delete($image['id']);
				}
			
		} catch(Exception $e){
			dump("[CRON ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			
			$a_Params['messages'] = $e->getMessage();
			$this->sendEmailException($e, $a_Params);			
		}		
	}
	
	
	/**
	 * @name	_getImages
	 */
	private function _getImages(){
	
	
	
		if(!isset($this->vars['srcdir']) or !$this->vars['srcdir']){
			dump("[CRON ERROR] ".__METHOD__." :: missing parameter 'srcdir'.");	 				
			return false;
		}
//		dump(INPUT_APPLICATION.$this->vars['srcdir']);

		$res = $this->obj_FileSystem->getFiles(INPUT_APPLICATION.$this->vars['srcdir']);		
		return $res;
	}
}