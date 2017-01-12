<?php
/**
 * @name	m_site_FilesException
 * @author 	wiegerjelsma
 * @version	1.0 2012-12-15 11:07:36
 */
class m_site_FilesException extends m_site_ModuleException {}

/**
 * @name	m_site_Files
 * @author 	wiegerjelsma
 * @version	1.0 2012-12-15 11:07:36
 */
class m_site_Files extends m_site_Module {
	
	/**
	 * @name	init
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2012-12-15 11:07:36
 	 * @desc	called on creation (from loader)
 	 */	 
	public function init(){
		parent::init();
	}
	
	
	/**
	 * @name	getImagesForResize
	 */
	public function getImagesForResize(){
		return $this->get(array('resize' => true));	
	}
	
	/**
	 * @name	save
 	 * @desc	generieke save method.
 	 * @returns	id van het gewijzigde of toegevoegde record
 	 */	 
	public function save($a_Data, $tablesuffix = false){
		try {				
			$basetable = isset($this->cfg['db']['tables']['basetable']) && $this->cfg['db']['tables']['basetable'] ? $this->cfg['db']['tables']['basetable'] : 'm_'.strToLower(str_replace('.','_', $this->myname));		
			$table = $tablesuffix ? $basetable.'-'.$tablesuffix : $basetable;
			$user_id = $this->SESSION->read('loggedin_userid');
			
			if(isset($a_Data['filename'])){
				$a_Elem = explode('.', $a_Data['filename']);
				$a_Data['ext'] = array_pop($a_Elem);
				$a_Data['filename'] = join('.', $a_Elem);
			}
			
			$a_Data['name'] = isset($a_Data['name']) && $a_Data['name'] ? $a_Data['name'] : (isset($a_Data['filename']) ? $a_Data['filename'] : '');				
			
			
			if(isset($a_Data['id']) && $a_Data['id']){
				$a_Data['edit_user_id'] = $user_id;
				$a_Data['edit'] = date('Y-m-d H:i:s');				
				$a_Where[] = "id = '".$a_Data['id']."'";
				$id = $a_Data['id'];
				unset($a_Data['id']);
				$action = 'edit';
				$this->DB->update($table, $a_Data, $a_Where);
			} else {
				unset($a_Data['id']);
				$a_Data['added_user_id'] = $user_id;	
				
				if($this->cfg['files']['save'] == 'dir' && $a_Data['src']){
					$src = $a_Data['src'];
					$a_Data['src'] = '';
				}
				
				$this->DB->insert($table, $a_Data);
				$id = $this->DB->lastInsertId();
				$action = 'add';	
				
				$a_Data['src'] = $src;
			}
			
			if($id && (isset($a_Data['src']) && $a_Data['src']) && $a_Data['filename']){
				try {
					$this->_deleteCopies($id);
				
					if(isset($this->cfg['files'][$a_Data['type']]['resize'])){												
				
						foreach($this->cfg['files'][$a_Data['type']]['resize'] as $a_ResizeConfig){
							try {
								$srcimage = imagecreatefromstring($a_Data['src']);
								$bx = imagesx($srcimage);   //breedte van het origineel
  								$by = imagesy($srcimage);   //hoogte van het origineel
						
								if(!$srcimage)
									throw new Exception("Unable to create img from source"); 
  			
								$width = $a_ResizeConfig['width'];
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
    		
						    		$resizedSource =  ob_get_clean();  			
						
//									$resizedSource = $this->_resize($b, $a_ResizeConfig['width']);	
									$filename = $a_ResizeConfig['prefix'].$a_Data['filename'].$a_ResizeConfig['suffix'].'.'.$a_Data['ext'];
									$this->saveResizedImage($resizedSource, $filename, $id);
								}
							} catch(Exception $e){
								throw new Exception($e);
							}
						}				
					}
				} catch(Exception $e){
					$this->_deleteCopies($id);
					$this->delete($id);
					throw new Exception($e);					
				}
			}
			
			$obj_History = Loader::LoadModule('History');
			if($user_id)
				$obj_History->trackEvent(CONTROLLER_NAME.'-'.$tablesuffix, $action, 'id :: user_id', $id.' :: '.$user_id);
			else
				$obj_History->trackEvent(CONTROLLER_NAME.'-'.$tablesuffix, $action, 'id', $id);
			return $id;
   		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			return false;
		}	
	}
	
	public function delete($id, $a_Images = array()){
		try {
			$table = isset($this->cfg['db']['tables']['basetable']) && $this->cfg['db']['tables']['basetable'] ? $this->cfg['db']['tables']['basetable'] : 'm_'.strToLower(str_replace('.','_', $this->myname));				
			
				
			if($this->_deleteCopies($id))	
				$this->DB->delete($table, array("id = '".$id."'"));			
			else
				throw new Exception('Unable to delete copies');
						
			return true;	
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			return false;
		}
	}
	
	
	private function _deleteCopies($id){
		try {
			
			if($this->cfg['files']['save'] == 'dir'){
				$a_Copies = $this->getCopies($id);
				if($a_Copies){
					foreach($a_Copies as $copy){
						$obj_FileSystem =  Loader::load('FileSystem');
						
						// remove file
						if(!$obj_FileSystem->deleteApplicationInputFile($copy['name'], $this->cfg['files']['dir'], true))
							dump('Unable to remove file from disk');						
					}
				}
			}
			
			$this->DB->delete('m_files_copies', array("id = '".$id."'"));			
						
			return true;	
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			return false;
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
	
	
	
	
	
	public function saveResizedImage($source, $filename, $file_id){
		try {	
			$a_Data['id'] = $file_id;			
			$a_Data['name'] = $filename;

			switch($this->cfg['files']['save']){
				default: // DB
					$a_Data['src'] = $source;
				break;
				case 'dir':
					$a_Data['src'] = '';			
				break;
			}
			
			$this->DB->insert('m_files_copies', $a_Data);
			$id = $this->DB->lastInsertId();
			$action = 'add';
			
			if($this->cfg['files']['save'] == 'dir'){
				$obj_FileSystem =  Loader::load('FileSystem');
				if(!$obj_FileSystem->writeApplicationInputFile($filename, $this->cfg['files']['dir'], $source, 'create', true))
					throw new Exception('Unable to write file to disk');
			}
			
			return $id;
   		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			return false;
		}	
	}
	
	public function deleteImageByFileNameAndGroup($name, $group){
		$imageId = $this->getImageIdByFileNameAndGroup($name, $group);
		try {	
			if($imageId){
				$this->delete($imageId);
				$this->_deleteCopies($imageId);
			}
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			dump($select->__toString());
		}		
	}
	
	public function getImageByFileName($name){
		try {
			$select = $this->DB->select();						
			$select->from(array('c' => 'm_files_copies'), '*');
			$select->joinLeft(array('f'=> 'm_files_files'), 'c.id = f.id', array('type'));	
			$select->where('c.name = ?', $name);
			$select->where("f.type = 'image/jpeg' or f.type = 'image/pjpeg'");			
			$a_Res = $this->DB->query($select)->fetchAll();
			return (count($a_Res)>0) ? $a_Res[0] : false;
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			dump($select->__toString());
		}
	}
	
	public function getFileSpecsById($id){
		try {
			$select = $this->DB->select();						
			$select->from(array('f' => 'm_files_files'), array('filename','type','ext','size','name'));
			$select->where('f.id = ?', $id);
			$a_Res = $this->DB->query($select)->fetchAll();
			return (count($a_Res)>0) ? $a_Res[0] : false;
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			dump($select->__toString());
		}
	
	}

	public function getPdfByFileName($name){
		try {
			$select = $this->DB->select();						
			$select->from(array('f' => 'm_files_files'), '*');
			$select->where('f.filename = ?', $name);
			$select->where('f.type = ?', 'application/pdf');
			$a_Res = $this->DB->query($select)->fetchAll();
			return (count($a_Res)>0) ? $a_Res[0] : false;
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			dump($select->__toString());
		}
	}
	
	
	
	public function getImageIdByFileNameAndGroup($name, $group){
		try {
			$select = $this->DB->select();						
			$select->from(array('f' => 'm_files_files'), 'id');
			$select->where('f.filename = ?', $name);
			$select->where('f.group = ?', $group);			
			$a_Res = $this->DB->query($select)->fetchAll();
			return (count($a_Res)>0) ? $a_Res[0]['id'] : false;
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			dump($select->__toString());
		}
	}	
	
	public function getCopies($id){
		try {
			$select = $this->DB->select();						
			$select->from(array('c' => 'm_files_copies'), 'name');
			$select->where('c.id = ?', $id);
			$a_Res = $this->DB->query($select)->fetchAll();
			
			return (count($a_Res)>0) ? $a_Res : false;
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			dump($select->__toString());
		}
		
	}
	
	public function getImageFileName($id){
		try {
			$select = $this->DB->select();						
			$select->from(array('f' => 'm_files_files'), 'filename');
			$select->where('f.id = ?', $id);
			$a_Res = $this->DB->query($select)->fetchAll();
			
			return (count($a_Res)>0) ? $a_Res[0]['filename'] : false;
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			dump($select->__toString());
		}
	}	
		

	public function getImages(){
		try {
			$select = $this->DB->select();						
			$select->from(array('c' => 'm_files_files'), array('name','filename','group','id'));
			$select->where('c.type = ?', 'image/jpeg');
			$select->orWhere('c.type = ?', 'image/pjpeg');			
			$a_Res = $this->DB->query($select)->fetchAll();
			return $a_Res ? $a_Res : false;
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			dump($select->__toString());
		}
	}	
	
	public function getFiles(){
		try {
			$select = $this->DB->select();						
			$select->from(array('c' => 'm_files_files'), array('name','filename','ext'));
			$select->where('c.type <> ?', 'image/jpeg');
			$select->where('c.type <> ?', 'image/pjpeg');			
			$a_Res = $this->DB->query($select)->fetchAll();
			return $a_Res ? $a_Res : false;
		} catch(Exception $e){
			dump("[DATABASE ERROR] ".__METHOD__."\n".'"'.$e->getMessage().'" at line '.$e->getLine().' in file '. $e->getFile());
			dump($select->__toString());
		}
	}
}
