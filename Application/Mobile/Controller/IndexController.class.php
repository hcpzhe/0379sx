<?php
namespace Mobile\Controller;
use Common\Controller\MobileBaseController;
use Think\Model;

class IndexController extends MobileBaseController {
	
	public function index() {
		
		$this->display();
	}
}