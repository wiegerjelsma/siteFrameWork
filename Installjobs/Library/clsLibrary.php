<?php
/**
 * @name	Library (install script)
 * @author 	wiegerjelsma
 *
 */
class Library extends Install {
	
	protected $modulePrefix = false;
	protected $moduleKey = 'Library';		
	protected $baseDir = LIBRARY_DIR;
	protected $fileTpl = 'library.cls.filetpl';
	protected $fileTplConfig = false;	
	
}