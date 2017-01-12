<?php
/**
 * @name	c_site_back_user_HomeException
 * @author 	wiegerjelsma
 * @version	1.0 2012-03-27 12:25:58
 */
class c_site_back_user_HomeException extends c_site_back_HomeException {}

/**
 * @name	c_site_back_user_Home
 * @author 	wiegerjelsma
 * @version	1.0 2012-03-27 12:25:58
 */
class c_site_back_user_Home extends c_site_back_Home {
	
	/**
	 * @name	init
 	 * @author 	wiegerjelsma
 	 * @version	1.0 2012-03-27 12:25:58
 	 * @desc	called on creation (from loader)
 	 */	 
	public function init(){
		parent::init();
	}
	
	/**
	 * @name	view
 	 * @desc	this is the homepage
 	 */	 
	public function tab(){
		$obj_Assessment = Loader::loadModule('Training.Lesson.Assessment');
		$overallscore = $obj_Assessment->getOverallScore(USER_ID);
		$this->TPL->assign("Overallscore", $overallscore);
		parent::tab();
	}	
}
