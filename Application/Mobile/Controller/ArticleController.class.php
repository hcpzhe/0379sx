<?php
namespace Mobile\Controller;
use Common\Controller\MobileBaseController;
use Think\Model;

class ArticleController extends MobileBaseController {
	
	/**
	 * 列表
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
		$total = $model->where($map)->count();
		$page = new \Common\Lib\Page($total, 10, $REQUEST);
		$list = $model->where($map)->order('orderid DESC')->limit($page->firstRow.','.$page->listRows)->select();
		if (IS_AJAX) {
			foreach ($list as &$row) {
				$row['url'] = U('info?id='.$row['id']);
				$row['content'] = ReStrLen(strip_tags($row['content']),65);
			}
			unset($row);
			$this->ajaxReturn($list);
		}else {
			$thisclass = $class_M->find($cid);
			$this->assign('thisclass', $thisclass); //当前栏目
			$this->assign('list', $list); //列表
			$this->assign('cid', $cid);
			$this->assign('nowPage',$page->nowPage);
			
			// 记录当前列表页的cookie
			cookie(C('CURRENT_URL_NAME'),$_SERVER['REQUEST_URI']);
			
			$this->display();
		}
	}
	
	/**
	 * 内容
	 */
	public function info($id) {
		$model = new Model('Infolist');
		$info = $model->find($id);
		$this->assign('info',$info);
		if ($info) $model->where('id='.$info['id'])->setInc('hits');
		$info['hits']++;
		
		$class_M = new Model('Infoclass');
		
		//侧栏
		$thisclass = $class_M->find($info['classid']);
		$this->assign('thisclass', $thisclass); //当前栏目
		
		$this->display();
	}
	
}