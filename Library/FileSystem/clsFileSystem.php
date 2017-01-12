<?php
class FileSystem {
	
	private $cache = true;
	private static $a_Cache = array();
	private static $instance;
	
	public function __construct(){
		if(!array_key_exists('dirs', self::$a_Cache))
			self::$a_Cache['dirs'] = array();
		if(!array_key_exists('files', self::$a_Cache))
			self::$a_Cache['files'] = array();
	}
	
    public static function singleton(){
    	if (!isset(self::$instance)) {
     		$c = __CLASS__;
       		self::$instance = new $c;
    	}
    
       	return self::$instance;
   	}	
   	
   	public function setCache($cache){
   		$this->cache = $cache;
   	}
	
	private function getBaseDirectory($directory){
		$root = ROOTFRAMEWORK;
		$directory = preg_replace("#/$#","",$directory);
		$directory = preg_replace("#$root#","",$directory);

		return ROOTFRAMEWORK.$directory;
	}
	
	public function getDirs($dir){
		$dir = $this->getBaseDirectory($dir);
		if($this->cache && array_key_exists($dir, self::$a_Cache['dirs']))	
			return self::$a_Cache['dirs'][$dir];
					
		$a_Dir = array();
		if(is_dir($dir)){
			$d = dir($dir);
			while(false !== ($f = $d->read())){
				if(('.' == $f) || ('..' == $f) || preg_match("/^\./",$f))
					continue;
				
				if(is_dir($d->path.'/'.$f))
					$a_Dir[] = $f;
			}
		}
		if($this->cache)
			self::$a_Cache['dirs'][$dir] = $a_Dir;
		return $a_Dir;
	}
	

	
	public function getFiles($directory){
		$directory = $this->getBaseDirectory($directory);
		
		if($this->cache && array_key_exists($directory, self::$a_Cache['files']))
			return self::$a_Cache['files'][$directory];	
			
		if(is_dir($directory)){
			$d = dir($directory);
			$aFiles = array();
			while(false !== ($f = $d->read())){
				if(('.' == $f) || ('..' == $f)){
					continue;
				}
				if(is_file($d->path.'/'.$f)){
					$aFiles[] = $f;
				}
			}
			if($this->cache)
				self::$a_Cache['files'][$directory] = $aFiles;			
			return $aFiles;
		} else
			return array();
	}	
	
	/**
	 * @name	writeApplicationFile
	 * @desc	writes an file into the application 'bin' directory 
	 */
	public function writeApplicationFile($filename, $directory = false, $source = false, $mode = 'create', $useBaseApplication = false){
		if(!defined('BIN_APPLICATION'))
			throw new Exception('Unable to write file : BIN_APPLICATION not defined');
			
		$directory = preg_replace("#/$#",'',$directory);
		
		$dir = ($useBaseApplication) ? BIN_BASEAPPLICATION.$directory : BIN_APPLICATION.$directory;
		if($directory)
			$dir .= '/';
		return $this->writeFile($dir, $filename, $source, $mode);	
	}
	
	/**
	 * @name	writeApplicationFile
	 * @desc	writes an file into the application 'bin' directory 
	 */
	public function writeApplicationInputFile($filename, $directory = false, $source = false, $mode = 'create', $useBaseApplication = false){
		if(!defined('INPUT_APPLICATION'))
			throw new Exception('Unable to write file : INPUT_APPLICATION not defined');
			
		$directory = preg_replace("#/$#",'',$directory);
		
		$dir = ($useBaseApplication) ? INPUT_BASEAPPLICATION.$directory : INPUT_APPLICATION.$directory;
		if($directory)
			$dir .= '/';
		return $this->writeFile($dir, $filename, $source, $mode);	
	}	
	
	public function writeFile($dir, $filename, $source, $mode){		
		$a_Modes['create'] = 'x+';
		$a_Modes['overwrite'] = 'w+';
		$a_Modes['add'] = 'a+';
		
		$this->makeDir($dir);
		if($handle = fopen($dir.$filename, $a_Modes[$mode])){
			fwrite($handle,$source."\n");	
			fclose($handle);
			return true;
		} else {			
			return false;
		}
	}
	
	
	/**
	 * @name	renameApplicationFile
	 * @desc	reads an file into the application 'bin' directory 
	 */
	public function renameApplicationInputFile($filename, $newFilename, $directory = false){
		$directory = preg_replace("#/$#",'',$directory);
		$dir = INPUT_APPLICATION.$directory.'/';
		return $this->renameFile($dir, $filename, $newFilename);	
	}	
	
	
	/**
	 * @name	renameFile
	 */
	private function renameFile($dir, $filename, $newFilename){
		$dir = $this->getBaseDirectory($dir).'/';
		$filename = preg_replace("#/$#",'',$filename);
		$newFilename = preg_replace("#/$#",'',$newFilename);		
		return rename($dir.$filename, $dir.$newFilename);
	}
	
	
	/**
	 * @name	deleteFile
	 */
	public function deleteFile($dir, $filename){
		$dir = $this->getBaseDirectory($dir).'/';
		if(!is_file($dir.$filename))
			return true;
		if(!unlink($dir.$filename))
			return false;
		return true;		
	}	
	
	/**
	 * @name	deleteApplicationInputFile
	 * @desc	reads an file into the application 'bin' directory 
	 */
	public function deleteApplicationInputFile($filename, $directory = false, $useBaseApplication = false){
		$directory = preg_replace("#/$#",'',$directory);
		$dir = ($useBaseApplication) ? INPUT_BASEAPPLICATION.$directory : INPUT_APPLICATION.$directory;
		return $this->deleteFile($dir, $filename);	
	}	
	
	
	/**
	 * @name	deleteApplicationOutputFile
	 * @desc	reads an file into the application 'bin' directory 
	 */
	public function deleteApplicationOutputFile($filename, $directory = false){
		$directory = preg_replace("#/$#",'',$directory);
		$dir = OUTPUT_APPLICATION.$directory.'/';
		return $this->deleteFile($dir, $filename);	
	}	
	
	
	/**
	 * @name	readFrameworkInputFile
	 * @desc	reads an file into the application 'bin' directory 
	 */
	public function deleteFrameworkInputFile($filename, $directory = false){
		$directory = preg_replace("#/$#",'',$directory);
		$dir = INPUT_FRAMEWORK.$directory.'/';
		return $this->deleteFile($dir, $filename);	
	}	
	
	public function moveFile($from, $to){
		if(!is_file($from))
			return false;
		
		if(rename($from, $to))
			return true;
		return false;
	}
	
	public function chown($user, $file){
		return chown($file , $user);
	}
	
	public function chmod($mode, $file){
		return chmod($file, $mode);
	}
	
		

	/**
	 * @name	makeDir
	 * @desc	if the directory does not exist create it
	 */
	public function makeDir($directory){
		$directory = $this->getBaseDirectory($directory);
		if(is_dir($directory))
			return;
		
		$a_Elements = explode('/', $directory);
		array_shift($a_Elements);
		
		// get the element count van het ROOTFRAMEWORK
		$root = preg_replace('#/$#', '', ROOTFRAMEWORK);
		$root = preg_replace('#^/#', '', $root);		
		$i = count(explode('/', $root));
		
		$root = '/'.join('/', array_slice($a_Elements, 0, $i));
		$a_Path = array_slice($a_Elements, $i);
		
		$path = $root;
		foreach($a_Path as $dir){
			$path .= '/'.$dir;
			if(!is_dir($path))
				if(!mkdir($path)){
					dump("[FILEYSTEM ERROR] :: Unable to make dir ($path)");
					return false;
				}
					
		}
		return true;
	}	
	
	
}