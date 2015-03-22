<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
use Think\Model;

class IndexController extends HomeBaseController {
	
	public function index() {
		$list_M = new Model('Infolist');
		$img_M = new Model('Infoimg');
		$adtype_M = new Model('Adtype');
		$admanage_M = new Model('admanage');
		
		//广告
		$adtype = $adtype_M->where("checkinfo='true' AND siteid=".C('SITEID'))->order('orderid')->select();
		$ads = array();
		foreach ($adtype as $val) {
			$tmp = $admanage_M->where("checkinfo='true' AND classid=".$val['id']." AND siteid=".C('SITEID'))->order('orderid')->select();
			$ads[$val['id']] = $tmp;
		}
		$this->assign('ads', $ads); //广告
		
		//网站公告
		$classid = 11;
		$where = array(
				'siteid' => C('SITEID'),
				'classid' => $classid,
				'checkinfo' => 'true',
				'delstate' => ''
		);
		$gonggao = $list_M->where($where)->find();
		$this->assign('gonggao', $gonggao); //公告
		
		//热门旅游 5个  使用widget
		
		//当季热门 6个
		$classid = 16;
		$where = array(
				'siteid' => C('SITEID'),
				'checkinfo' => 'true',
				'delstate' => ''
		);
		$map_class = array();
		$map_class['classid'] = $classid;
		$map_class['parentstr'] = array('like','%,'.$classid.',%');
		$map_class['_logic'] = 'or';
		$where['_complex'] = $map_class;
		$dangji = $img_M->where($where)->order('orderid DESC')->limit(6)->select();
		$this->assign('dangji', $dangji); //当季热门
		

		//周末去哪 6个
		$flag = array('like','%z%');
		$where = array(
				'siteid' => C('SITEID'),
				'flag' => $flag,
				'checkinfo' => 'true',
				'delstate' => ''
		);
		$zhoumo = $img_M->where($where)->order('orderid DESC')->limit(6)->select();
		$this->assign('zhoumo', $zhoumo); //周末去哪
		
		
		$this->display();
	}
	
}