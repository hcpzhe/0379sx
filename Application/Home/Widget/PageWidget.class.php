<?php
namespace Home\Widget;
use Common\Controller\HomeBaseController;
use Think\Model;
/**
 * Home挂件
 * @author RockSnap
 *
 */
class PageWidget extends HomeBaseController {
	
	public function menu() {
		$model = new Model('Nav');
		$map = array(
				'siteid' => C('SITEID'),
				'checkinfo' => 'true'
		);
		$group_map = $map;
		$group_map['classname'] = 'menu';
		$group = $model->where($group_map)->find();
		
		$menu_map = $map;
		$menu_map['parentid'] = $group['id'];
		$menu_list = $model->where($menu_map)->order('orderid')->select();
		$this->assign('menu_list',$menu_list);
		
		$this->display('Widget:menu');
	}
	
	public function foot() {
		$class_M = new Model('Infoclass'); $list_M = new Model('Infolist');
		$classid = 10; //底部文章栏目
		$basemap = array(
				'siteid' => C('SITEID'),
				'checkinfo' => 'true',
				'delstate' => ''
		);
		$where = $basemap;
		$where['parentid'] = $classid;
		$class = $class_M->where($where)->order('orderid')->limit(4)->select();
		$list = array();
		foreach ($class as $row) {
			$tmp = $row;
			$tmpmap = $basemap;
			$tmpmap['classid'] = $row['id'];
			$tmp['art'] = $list_M->where($tmpmap)->order('orderid')->limit(4)->select();
			$list[] = $tmp;
		}
		$this->assign('list',$list);
		
		$this->display('Widget:foot');
	}
	
	/**
	 * 热门旅游
	 */
	public function remen() {
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
		
		$this->display('Widget:Remen');
	}
	
	/**
	 * 团队风采   非首页使用, 全宽的栏目,滚动
	 */
	public function tuandui() {
		$img_M = new Model('Infoimg');
		$flag = array('like','%r%');
		$where = array(
				'siteid' => C('SITEID'),
				'classid' => 29,
				'checkinfo' => 'true',
				'delstate' => ''
		);
		$remen = $img_M->where($where)->order('orderid DESC')->limit(10)->select();
		$this->assign('remen', $remen); //热门旅游
		
		$this->display('Widget:Tuandui');
	}
	
	public function sider($thisclass,$ads=1) {
		$cascade = array(
				'当季热门' => 'remen',
				'洛阳周边' => 'zhoubian',
				'国内旅游' => 'guonei',
				'出境旅游' => 'chujing',
				'特价旅游' => 'tejia'
		);
		$cascadedata_M = new Model('Cascadedata');
		$where = array();
		
		$class_M = new Model('Infoclass');
		$parent_arr = explode(',', trim($thisclass['parentstr'],','));
		if (in_array(2, $parent_arr)) {
			$classid = ($parent_arr[2] > 0) ? $parent_arr[2] : $thisclass['id'];
			$use_class = $class_M->find($classid);
			$where['datagroup'] = $cascade[$use_class['classname']];
		}else {
			$where['datagroup'] = array('in',array_values($cascade));
		}
		$list = $cascadedata_M->where($where)->order('orderid')->select();
		$this->assign('list', $list); //热门旅游
		
		if ($ads) {
			//广告, 使用广告位ID为5的广告 , 只有出境5,国内4 有广告, 其它都随机显示, 最多显示3个
			$admanage_M = new Model('admanage');
			$where = array(
					'checkinfo' => 'true',
					'classid' => 5,
					'siteid' => C('SITEID')
			);
			$order = 'rand()';
			if ($classid=='4' || $classid=='5') {
				$where['title']=$classid;
				$order = "orderid";
			}
			$sider_ad = $admanage_M->where($where)->order($order)->limit(3)->select();
			$this->assign('sider_ad', $sider_ad); //广告
		}
		
		$this->display('Widget:Sider');
	}
}