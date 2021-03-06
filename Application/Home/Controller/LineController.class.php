<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
use Think\Model;

class LineController extends HomeBaseController {
	
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
		if (isset($keywords)) $map['title'] = array('like','%'.$keywords.'%');
		$map['delstate'] = '';
		$map['checkinfo'] = 'true';
		/******************/
		
		C('LIST_ROWS',C('cfg_linerows'));
		$list = $this->_lists($model,$map,'orderid DESC');
		$this->assign('list', $list); //列表
		$this->assign('keywords',$keywords);
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
		
		// 记录当前列表页的cookie
		cookie(C('CURRENT_URL_NAME'),$_SERVER['REQUEST_URI']);
		
		$this->display();
	}
	
	/**
	 * 旅游路线详情
	 */
	public function info($id) {
		$model = New Model('Infoimg');
		$info = $model->find($id);
		$this->assign('info', $info); //列表
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
		
		// 记录当前列表页的cookie
		cookie(C('CURRENT_URL_NAME'),$_SERVER['REQUEST_URI']);
		$this->display();
	}

	/**
	 * 预定页面
	 */
	public function yuding($id) {
		$model = New Model('Infoimg');
		$info = $model->where("id=$id AND parentstr like '%,2,%'")->find($id);
		if (empty($info)) redirect(cookie(C('CURRENT_URL_NAME')));
		$this->assign('info', $info);
		
		$this->display();
	}
	
	/**
	 * 预定提交
	 */
	public function yudingSave() {
		$data = I('post.');
		if (empty($data['linename']) || empty($data['customer']) || empty($data['contact'])) {
			$this->error('线路名称,联系人,联系电话 不能为空!!');
		}
		$data['posttime'] = time();
		$model = new Model('Yuding');
		if (!$model->add($data)) $this->error('预订提交失败, 请拨打电话进行预订!!');
		$this->success('预订提交成功, 稍后我们的客服会通过电话与您联系!!',cookie(C('CURRENT_URL_NAME')));
	}
	
}