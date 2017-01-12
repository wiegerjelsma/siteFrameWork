<?php
/**
 * @name	Bootstrapper (install script)
 * @author 	wiegerjelsma
 *
 */
class Bootstrapper extends Install {
	
	protected $modulePrefix = 'b';
	protected $moduleKey = 'Bootstrapper';		
	protected $baseDir = BOOTSTRAPPERS_DIR;
	protected $fileTpl = 'bootstrapper.cls.filetpl';
	protected $fileTplConfig = false;	
		
}