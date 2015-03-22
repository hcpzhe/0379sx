<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
use Think\Model;

class ArticleController extends HomeBaseController {
	
	/**
	 * 文章列表
	 */
	public function lists($cid) {
		$cid = (int)$cid;
		if ($cid <= 0) $this->redirect('Index/index');
		
		$model = New Model('Infolist'); $class_M = new Model('Infoclass');
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
		if (!empty($list)) {
			$class_ids = field_unique($list, 'classid'); //列表中用到的栏目ID
			$map = array('id'=>array('in',$class_ids));
			$classlist = $class_M->where($map)->getField('id,classname');
			$this->assign('classlist',$classlist); //列表用到的栏目, ID为key索引
		}
		
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
		
		// 记录当前列表页的cookie
		cookie(C('CURRENT_URL_NAME'),$_SERVER['REQUEST_URI']);
		
		$this->display();
	}
	
	/**
	 * 文章内容
	 */
	public function info($id) {
		$model = new Model('Infolist');
		$info = $model->find($id);
		$this->assign('info',$info);
		if ($info) $model->where('id='.$info['id'])->setInc('hits');
		$info['hits']++;
		
		//面包屑
		$class_M = new Model('Infoclass');
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
		
		$this->display();
	}
	
	/**
	 * 单页内容
	 */
	public function one($id,$tpl='info') {
		$class_M = new Model('Infoclass');
		$class = $class_M->find($id);
		$model = new Model('Info');
		$info = $model->where('classid='.$id)->find();
		
		if ($info) $model->where('id='.$info['id'])->setInc('hits');
		
		$info['title'] = $class['classname'];
		$info['hits']++;
		
		$this->assign('info',$info);
		
		$this->display($tpl);
	}
	
}