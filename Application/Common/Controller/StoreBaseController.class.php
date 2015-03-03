<?php
namespace Common\Controller;
use Think\Controller;
use Common\Model\ConfigModel;

/**
 * Store 控制器基类
 */
abstract class StoreBaseController extends Controller {
	
	/**
	 * 不做权限处理, 所有用户权限相同
	 * 游客即可访问, 不需要判断登录;
	 * 需要登录的控制器extends别的基类
	 */
	protected function _initialize() {
		defined('STID') or define('STID',is_login());
// 		if (!defined('STID')) {
// 			define('STID',2);
// 			$store = M('Store')->find(STID);
// 			$auth = array(
// 					'id'		=> $store['id'],
// 					'account'	=> $store['account'],
// 					'logins'	=> $store['logins'],
// 					'last_ip'	=> $store['last_ip']
// 			);
// 			session('user_auth', $auth);
// 		}
		if (! STID) { // 还没登录 跳转到登录页面
			$this->redirect(C('LOGIN_URL'));
		}
		$model = new ConfigModel();
		$model->loadConfig();
	}
	
	public function _empty() {
		//TODO 判断是否存在当前controller的模板文件, 若存在,则display 不存在,再跳转
		$this->redirect('Index/index');
	}
	
	protected function _state($id,$act,$modelname) {
		$id = (int)$id;
		if ($id <= 0) {
			$this->error('主要参数非法');
		}
		$class = '\\Manage\\Model\\'.$modelname.'Model';
		$acts = $class::$mystat;
		if (!key_exists($act, $acts)) {
			$this->error('参数非法');
		}
		$model = M($modelname);
		if (false === $model->where('`id`='.$id)->setField('status',$acts[$act])) {
			$this->error('更新失败,未知错误!');
		}
		$this->success('更新成功');
	}
	
	/**
	 * 通用分页列表数据集获取方法
	 *
	 *  可以通过url参数传递where条件,例如:  index.html?name=asdfasdfasdfddds
	 *  可以通过url空值排序字段和方式,例如: index.html?_field=id&_order=asc
	 *  可以通过url参数r指定每页数据条数,例如: index.html?r=5
	 *
	 * @param sting|Model  $model   模型名或模型实例
	 * @param array        $where   where查询条件(优先级: $where>$_REQUEST>模型设定)
	 * @param array|string $order   排序条件,传入null时使用sql默认排序或模型属性(优先级最高);
	 *                              请求参数中如果指定了_order和_field则据此排序(优先级第二);
	 *                              否则使用$order参数(如果$order参数,且模型也没有设定过order,则取主键降序);
	 *
	 * @param array        $base    基本的查询条件
	 * @param boolean      $field   单表模型用不到该参数,要用在多表join时为field()方法指定参数
	 * @author 朱亚杰 <xcoolcc@gmail.com>
	 *
	 * @return array|false
	 * 返回数据集
	 */
	protected function _lists ($model,$where=array(),$order='',$base = array('status'=>array('egt',0)),$field=true){
		$options    =   array();
		$REQUEST    =   (array)I('request.');
		if(is_string($model)){
			$model  =   M($model);
		}
		
		$OPT        =   new \ReflectionProperty($model,'options');
		$OPT->setAccessible(true);
		
		$pk         =   $model->getPk();
		if($order===null){
			//order置空
		}else if ( isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']),array('desc','asc')) ) {
			$options['order'] = '`'.$REQUEST['_field'].'` '.$REQUEST['_order'];
		}elseif( $order==='' && empty($options['order']) && !empty($pk) ){
			$options['order'] = $pk.' desc';
		}elseif($order){
			$options['order'] = $order;
		}
		unset($REQUEST['_order'],$REQUEST['_field']);
		
		$options['where'] = array_filter(array_merge( (array)$base, /*$REQUEST,*/ (array)$where ),function($val){
			if($val===''||$val===null){
				return false;
			}else{
				return true;
			}
		});
		if( empty($options['where'])){
			unset($options['where']);
		}
		$options      =   array_merge( (array)$OPT->getValue($model), $options );
		$total        =   $model->where($options['where'])->count();
		
		if( isset($REQUEST['r']) ){
			$listRows = (int)$REQUEST['r'];
		}else{
			$listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
		}
		$page = new \Manage\Lib\Page($total, $listRows, $REQUEST);
		// 		if($total>$listRows){
		// 			$page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
		// 		}
		$p =$page->show();
		$this->assign('_page', $p? $p: '');
		$this->assign('_total',$total);
		$options['limit'] = $page->firstRow.','.$page->listRows;
		
		$model->setProperty('options',$options);
		
		return $model->field($field)->select();
	}
}