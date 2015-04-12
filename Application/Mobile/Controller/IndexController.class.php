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
		$remen = $img_M->where($where)->order('orderid DESC')->limit(10)->select();
		$this->assign('remen', $remen); //热门旅游
		
		//广告
		$adtype_M = new Model('Adtype');
		$admanage_M = new Model('admanage');
		$adtype = $adtype_M->where("checkinfo='true' AND siteid=".C('SITEID'))->order('orderid')->select();
		$ads = array();
		foreach ($adtype as $val) {
			$tmp = $admanage_M->where("checkinfo='true' AND classid=".$val['id']." AND siteid=".C('SITEID'))->order('orderid')->select();
			$ads[$val['id']] = $tmp;
		}
		$this->assign('ads', $ads); //广告
		
		$this->display();
	}
}