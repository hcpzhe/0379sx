<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
use Think\Model;

class ImageController extends HomeBaseController {
	
	//属性选取		zc租车  qz签证
	protected $attr = array('zc','qz');
	
	/**
	 * 图片列表
	 */
	public function lists($cid,$attr=null) {
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
		$map['delstate'] = '';
		$map['checkinfo'] = 'true';
		/******************/
		
		$list = $this->_lists($model,$map,'orderid DESC');
		$this->assign('list', $list); //列表
		
		//**面包屑
		$thisclass = $class_M->find($cid);
		$this->assign('thisclass', $thisclass); //当前栏目
		
		$where = array();
		$where['id'] = array('in',$thisclass['parentstr'].$cid);
		$class_arr = $class_M->where($where)->getField('id,classname');
		$bread = array();
		$parent_arr = explode(',', $thisclass['parentstr'].$cid);
		foreach ($parent_arr as $val) {
			if (array_key_exists($val, $class_arr)) {
				$bread[$val] = $class_arr[$val];
			}
		}
		$this->assign('bread', $bread); //面包屑
		
		//属性选取		zc租车  qz签证
		$attr = (in_array($attr, $this->attr)) ? $attr : '';
		$this->assign('attr', $attr);
		
		// 记录当前列表页的cookie
		cookie(C('CURRENT_URL_NAME'),$_SERVER['REQUEST_URI']);
		
		$this->display();
	}
	
	/**
	 * 图片内容
	 */
	public function info($id,$attr=null) {
		$model = new Model('Infoimg');
		$info = $model->find($id);
		$this->assign('info',$info);
		if ($info) $model->where('id='.$info['id'])->setInc('hits');
		$info['hits']++;
		
		$class_M = new Model('Infoclass');
		
		//侧栏
		$thisclass = $class_M->find($info['classid']);
		$this->assign('thisclass', $thisclass); //当前栏目
		
		//面包屑
		$where = array();
		$where['id'] = array('in',$info['parentstr'].$info['classid']);
		$class_arr = $class_M->where($where)->getField('id,classname');
		
		$bread = array();
		$parent_arr = explode(',', $info['parentstr'].$info['classid']);
		foreach ($parent_arr as $val) {
			if (array_key_exists($val, $class_arr)) {
				$bread[$val] = $class_arr[$val];
			}
		}
		$this->assign('bread', $bread); //面包屑

		//属性选取		zc租车  qz签证
		$attr = (in_array($attr, $this->attr)) ? $attr : '';
		$this->assign('attr', $attr);
		
		$this->display();
	}
	
}