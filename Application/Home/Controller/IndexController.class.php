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
		$gonggao = $list_M->where($where)->order('id DESC')->find();
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

		//亲子游 3个
		$flag = array('like','%q%');
		$where = array(
				'siteid' => C('SITEID'),
				'flag' => $flag,
				'checkinfo' => 'true',
				'delstate' => ''
		);
		$qinzi = $img_M->where($where)->order('orderid DESC')->limit(3)->select();
		$this->assign('qinzi', $qinzi); //亲子游
		
		//情侣游 3个
		$flag = array('like','%l%');
		$where = array(
				'siteid' => C('SITEID'),
				'flag' => $flag,
				'checkinfo' => 'true',
				'delstate' => ''
		);
		$qinglv = $img_M->where($where)->order('orderid DESC')->limit(3)->select();
		$this->assign('qinglv', $qinglv); //情侣游
		
		//假日游 3个
		$flag = array('like','%j%');
		$where = array(
				'siteid' => C('SITEID'),
				'flag' => $flag,
				'checkinfo' => 'true',
				'delstate' => ''
		);
		$jiari = $img_M->where($where)->order('orderid DESC')->limit(3)->select();
		$this->assign('jiari', $jiari); //假日游
		
		//团队风采 20个
		$classid = 29;
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
		$tuandui = $img_M->where($where)->order('orderid DESC')->limit(20)->select();
		$this->assign('tuandui', $tuandui); //团队风采
		
		//友情连接
		$weblink_M = new Model('Weblink');
		$where = array(
				'siteid' => C('SITEID'),
				'checkinfo' => 'true'
		);
		$weblinks = $weblink_M->where($where)->order('orderid')->select();
		$this->assign('weblinks', $weblinks); //友情连接
		
		$this->display();
	}
	
	public function message() {
		
		$this->display();
	}
	
	public function msgSave() {
		$data = I('post.');
		if (empty($data['nickname']) || empty($data['contact']) || empty($data['content'])) {
			$this->error('内容,姓名,手机号码不能为空!!');
		}
		$data['posttime'] = time();
		$data['htop'] = '';
		$data['rtop'] = '';
		$data['ip'] = get_client_ip(1);
		$data['recont'] = '';
		$data['retime'] = 0;
		$data['orderid'] = 1;
		$data['checkinfo'] = 'true';
		$model = new Model('message');
		if (!$model->add($data)) $this->error('预订提交失败, 请拨打电话进行预订!!');
		$this->success('留言提交成功');
	}
}