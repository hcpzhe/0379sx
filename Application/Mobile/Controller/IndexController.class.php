<?php
namespace Mobile\Controller;
use Common\Controller\MobileBaseController;
use Think\Model;

class IndexController extends MobileBaseController {
	
	public function index() {
		$img_M = new Model('Infoimg');
		$flag = array('like','%r%');
		$where = array(
				'siteid' => C('SITEID'),
				'flag' => $flag,
				'checkinfo' => 'true',
				'delstate' => ''
		);
		$remen = $img_M->where($where)->order('orderid DESC')->limit(5)->select();
		$this->assign('remen', $remen); //热门旅游
		
		$this->display();
	}
}