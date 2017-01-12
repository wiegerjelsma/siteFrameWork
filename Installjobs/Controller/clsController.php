<?php
/**
 * @name	Module (install script)
 * @author 	wiegerjelsma
 *
 */
class Controller extends Install {	
	
	protected $modulePrefix = 'c';
	protected $moduleKey = 'Controller';
	protected $baseDir = CONTROLLERS_DIR;
	protected $fileTpl = 'controller.cls.filetpl';
	protected $fileTplConfig = 'controller.conf.filetpl';

}