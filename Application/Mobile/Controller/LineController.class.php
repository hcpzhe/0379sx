<?php
namespace Mobile\Controller;
use Common\Controller\MobileBaseController;
use Think\Model;

class LineController extends MobileBaseController {

	/**
	 * 旅游路线列表
	 * @param  $cid			栏目ID
	 */
	public function lists($cid=2,$keywords=null) {
		$cid = (int)$cid;
		if ($cid <= 0) $this->redirect('Index/index');
		
		$model = New Model('Infoimg'); $class_M = new Model('Infoclass');
		$map = array(); $map_class = array();
		//查询条件 处理
		$map_class['classid'] = $cid;
		$map_class['parentstr'] = array('like','%,'.$cid.',%');
		$map_class['_logic'] = 'or';
		$map['_complex'] = $map_class;
		$map['siteid'] = C('SITEID');
		if (!empty($keywords)) $map['title'] = array('like','%'.$keywords.'%');
		$map['delstate'] = '';
		$map['checkinfo'] = 'true';
		/******************/
		$total = $model->where($map)->count();
		$page = new \Common\Lib\Page($total, 10, $REQUEST);
		$list = $model->where($map)->order('orderid DESC')->limit($page->firstRow.','.$page->listRows)->select();
		if (IS_AJAX) {
			$this->ajaxReturn($list);
		}else {
			$thisclass = $class_M->find($cid);
			$this->assign('thisclass', $thisclass); //当前栏目
			$this->assign('list', $list); //列表
			$this->assign('cid', $cid);
			$this->assign('keywords',$keywords);
			
			// 记录当前列表页的cookie
			cookie(C('CURRENT_URL_NAME'),$_SERVER['REQUEST_URI']);
			
			$this->display();
		}
	}
}