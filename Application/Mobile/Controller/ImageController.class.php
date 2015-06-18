<?php
namespace Mobile\Controller;
use Common\Controller\MobileBaseController;
use Think\Model;

class ImageController extends MobileBaseController {

	//属性选取		zc租车  qz签证 mp门票 jd酒店
	protected $attr = array('zc','qz','mp','jd');
	
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
		//属性选取		zc租车  qz签证 mp门票
		$attr = (in_array($attr, $this->attr)) ? $attr : '';
		/******************/
		$total = $model->where($map)->count();
		$page = new \Common\Lib\Page($total, 10, $REQUEST);
		$list = $model->where($map)->order('orderid DESC')->limit($page->firstRow.','.$page->listRows)->select();
		if (IS_AJAX) {
			foreach ($list as &$row) {
				$row['url'] = U('info?id='.$row['id'].'&attr='.$attr);
				$row['price'] = intval($row['price']);
			}
			unset($row);
			$this->ajaxReturn($list);
		}else {
			$thisclass = $class_M->find($cid);
			$this->assign('thisclass', $thisclass); //当前栏目
			$this->assign('list', $list); //列表
			$this->assign('cid', $cid);
			$this->assign('attr', $attr);//属性选取
			$this->assign('nowPage',$page->nowPage);
			
			// 记录当前列表页的cookie
			cookie(C('CURRENT_URL_NAME'),$_SERVER['REQUEST_URI']);
			
			$this->display();
		}
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
		
		//属性选取		zc租车  qz签证 mp门票
		$attr = (in_array($attr, $this->attr)) ? $attr : '';
		$this->assign('attr', $attr);
		
		if ($attr) $this->display();
		else $this->display('image');
	}
	
}