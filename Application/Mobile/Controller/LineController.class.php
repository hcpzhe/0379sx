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
			foreach ($list as &$row) {
				$row['url'] = U('Line/info?id='.$row['id']);
				$row['price'] = intval($row['price']);
			}
			unset($row);
			$this->ajaxReturn($list);
		}else {
			$thisclass = $class_M->find($cid);
			$this->assign('thisclass', $thisclass); //当前栏目
			$this->assign('list', $list); //列表
			$this->assign('cid', $cid);
			$this->assign('keywords',$keywords);
			$this->assign('nowPage',$page->nowPage);
			
			// 记录当前列表页的cookie
			cookie(C('CURRENT_URL_NAME'),$_SERVER['REQUEST_URI']);
			
			$this->display();
		}
	}
	
	/**
	 * 旅游路线详情
	 */
	public function info($id,$jup=null) {
		$model = New Model('Infoimg');
		$info = $model->find($id);
		$this->assign('info', $info);
		
		//判断id是否是旅游路线
		$parent_arr = explode(',', trim($info['parentstr'],','));
		if (!in_array(2, $parent_arr)) {
			redirect(cookie(C('CURRENT_URL_NAME')));
		}
		
		//增加点击
		if ($info) $model->where('id='.$info['id'])->setInc('hits');
		$info['hits']++;
		
		$class_M = new Model('Infoclass');
		$thisclass = $class_M->find($info['classid']);
		$this->assign('thisclass', $thisclass); //当前栏目
		
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
		$this->success('预订提交成功, 稍后我们的客服会通过电话与您联系!!',U('lists'));
	}
	
}