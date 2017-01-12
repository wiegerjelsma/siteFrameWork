<?php
/**
 * @name	Cronjob (install script)
 * @author 	wiegerjelsma
 *
 */
class Cronjob extends Install {
	
	protected $modulePrefix = 'cron';
	protected $moduleKey = 'Cronjob';		
	protected $baseDir = CRONJOBS_DIR;
	protected $fileTpl = 'cronjob.cls.filetpl';
	protected $fileTplConfig = false;	
	
}