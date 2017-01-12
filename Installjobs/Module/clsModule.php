<?php
/**
 * @name	Module (install script)
 * @author 	wiegerjelsma
 *
 */
class Module extends Install {
	
	protected $modulePrefix = 'm';
	protected $moduleKey = 'Module';
	protected $baseDir = MODULES_DIR;
	protected $fileTpl = 'module.cls.filetpl';
	protected $fileTplConfig = 'module.conf.filetpl';
		
}