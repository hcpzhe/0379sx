<?php
namespace Common\Model;
use Think\Model;

/**
 * 公共配置模型; 主要用户页面初始化loadconfig
 */
class WebconfigModel extends Model {
	
	/**
	 * 载入数据库中的配置
	 * @return NULL
	 */
	public function loadConfig() {
		$map = array('siteid' => C('SITEID'));
		$config = $this->where($map)->getField('varname,varvalue');
		if ($config) {
			C($config); //添加配置
			C('LIST_ROWS',$config['cfg_pagenum']);
		}
		return null;
	}
}
