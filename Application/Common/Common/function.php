<?php
//公共函数

/**
 *判断是否是通过手机访问
 */
if(!function_exists('isMobile'))
{
	function IsMobile()
	{

		//如果有HTTP_X_WAP_PROFILE则一定是移动设备
		if(isset($_SERVER['HTTP_X_WAP_PROFILE']))  return TRUE;

		//如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
		if(isset($_SERVER['HTTP_VIA']))
		{
			//找不到为flase,否则为true
			return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
		}

		//判断手机发送的客户端标志,兼容性有待提高
		if(isset($_SERVER['HTTP_USER_AGENT']))
		{

			$clientkeywords = array('nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile');

			//从HTTP_USER_AGENT中查找手机浏览器的关键字
			if(preg_match('/('.implode('|', $clientkeywords).')/i', strtolower($_SERVER['HTTP_USER_AGENT'])))
			{
				return TRUE;
			}
		}

		//协议法，因为有可能不准确，放到最后判断
		if(isset($_SERVER['HTTP_ACCEPT']))
		{
			//如果只支持wml并且不支持html那一定是移动设备
			//如果支持wml和html但是wml在html之前则是移动设备
			if((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) &&
					(strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false ||
							(strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
			{
				return TRUE;
			}
		}

		return FALSE;
	}
}

/*
 * 函数说明：截取指定长度的字符串
 *         utf-8专用 汉字和大写字母长度算1，其它字符长度算0.5
 *
 * @param  string  $str  原字符串
 * @param  int     $len  截取长度
 * @param  string  $etc  省略字符...
 * @return string        截取后的字符串
 */
if(!function_exists('ReStrLen'))
{
	function ReStrLen($str, $len=10, $etc='...')
	{
		$restr = '';
		$i = 0;
		$n = 0.0;

		//字符串的字节数
		$strlen = strlen($str);
		while(($n < $len) and ($i < $strlen))
		{
			$temp_str = substr($str, $i, 1);

			//得到字符串中第$i位字符的ASCII码
			$ascnum = ord($temp_str);

			//如果ASCII位高与252
			if($ascnum >= 252)
			{
				//根据UTF-8编码规范，将6个连续的字符计为单个字符
				$restr = $restr.substr($str, $i, 6);
				//实际Byte计为6
				$i = $i + 6;
				//字串长度计1
				$n++;
			}
			else if($ascnum >= 248)
			{
				$restr = $restr.substr($str, $i, 5);
				$i = $i + 5;
				$n++;
			}
			else if($ascnum >= 240)
			{
				$restr = $restr.substr($str, $i, 4);
				$i = $i + 4;
				$n++;
			}
			else if($ascnum >= 224)
			{
				$restr = $restr.substr($str, $i, 3);
				$i = $i + 3 ;
				$n++;
			}
			else if ($ascnum >= 192)
			{
				$restr = $restr.substr($str, $i, 2);
				$i = $i + 2;
				$n++;
			}

			//如果是大写字母 I除外
			else if($ascnum>=65 and $ascnum<=90 and $ascnum!=73)
			{
				$restr = $restr.substr($str, $i, 1);
				//实际的Byte数仍计1个
				$i = $i + 1;
				//但考虑整体美观，大写字母计成一个高位字符
				$n++;
			}

			//%,&,@,m,w 字符按1个字符宽
			else if(!(array_search($ascnum, array(37, 38, 64, 109 ,119)) === FALSE))
			{
				$restr = $restr.substr($str, $i, 1);
				//实际的Byte数仍计1个
				$i = $i + 1;
				//但考虑整体美观，这些字条计成一个高位字符
				$n++;
			}

			//其他情况下，包括小写字母和半角标点符号
			else
			{
				$restr = $restr.substr($str, $i, 1);
				//实际的Byte数计1个
				$i = $i + 1;
				//其余的小写字母和半角标点等与半个高位字符宽
				$n = $n + 0.5;
			}
		}

		//超过长度时在尾处加上省略号
		if($i < $strlen)
		{
			$restr = $restr.$etc;
		}

		return $restr;
	}
}

// 分析枚举类型配置值 格式 a:名称1,b:名称2
function parse_config_attr($string) {
	$array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
	if(strpos($string,':')){
		$value  =   array();
		foreach ($array as $val) {
			list($k, $v) = explode(':', $val);
			$value[$k]   = $v;
		}
	}else{
		$value  =   $array;
	}
	return $value;
}
/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login(){
	$user = session('user_auth');
	if (empty($user)) {
		return 0;
	} else {
		return session('user_auth_sign') == data_auth_sign($user) ? $user['id'] : 0;
	}
}

/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data) {
	//数据类型检测
	if(!is_array($data)){
		$data = (array)$data;
	}
	ksort($data); //排序
	$code = http_build_query($data); //url编码并生成query字符串
	$sign = sha1($code); //生成签名
	return $sign;
}

/**
 * 检测当前用户是否为管理员
 * @param string $uid
 * @return boolean true-管理员，false-非管理员
 */
function is_administrator($uid = null){
	$uid = is_null($uid) ? is_login() : $uid;
	return $uid && (in_array(intval($uid), C('USER_ADMINISTRATOR')));
}

/**
 * 密码加密方法
 */
function pwd_hash($password, $type = 'ripemd128') {
	return hash ( $type, C('PW_PREFIX').md5($password).C('PW_SUFFIX') );
}

/**
 * 生成member_id
 */
function member_id() {
	$uniqid = uniqid('',true);
	return hash ('ripemd160', session_id().$uniqid.microtime());
}

/**
 * 生成10位order_sn
 */
function order_sn() {
	static $seed=array();
	$rs = mt_rand(1000000000, 9999999999);
	if (in_array($rs, $seed)) {
		return order_sn();
	}else {
		$seed[] = $rs;
		return $rs;
	}
}

/**
 * 处理要在数据库中使用的字符串
 */
function strfordb($string) {
	return str_replace( array('%','_'), array('\%','\_'), $string );
}

/**
 * 批量ID处理
 */
function idshandle($idstr) {
//	$idstr = trim(trim($idstr,','));
	if (preg_match('/^([0-9]+(\,){0,1})+[^\,]$/',$idstr)) {
		return TRUE;
	}
	return FALSE;
}

/**
 * 站点URL匹配判断
 */
function urlmatch($url) {
	if (preg_match('/^(https?:\/\/)?([\da-z\.-])+\.([a-z\.]{2,6})\/?$/',$url)) {
		return TRUE;
	}
	return FALSE;
}

/**
 * 数据库查询前,时间区间处理
 */
function timehandle($start,$end) {
	$result = FALSE;
	$start_time = strtotime($start);//开始时间
	if ($start_time > 0) $result = array('egt',$start_time);//大于等于开始时间
	
	$end_time = strtotime($end);//截止时间
	if ($end_time > 0) {
		$tmp_end_time = getdate($end_time);
		$end_time = mktime('23','59','59',$tmp_end_time['mon'],$tmp_end_time['mday'],$tmp_end_time['year']);
		//小于等于截止时间
		if (is_array($result)) $result = array($result,array('elt',$end_time));
		else $result = array('elt',$end_time);
	}
	return $result;
}

/**
 * select()数据中, 返回 某些字段的所有数据
 * @param array $list
 * @param string/array $filed
 */
function field_unique($list, $filed) {
	$arr = array();
	if (!empty($list)) {
		if (is_array($filed)) {
			foreach ($filed as $k => $v) {
				$arr[$k] = field_unique($list,$v);
			}
		}else {
			$filedarr = explode(",", $filed);
			foreach ($list as $row) {
				foreach ($filedarr as $k => $v) {
					$arr[] = $row[$v];
				}
			}
			$arr = array_unique($arr);
		}
	}
	return $arr;
}


/**
 * 调用系统的API接口方法（静态方法）
 * api('User/getName','id=5'); 调用公共模块的User接口的getName方法
 * api('Admin/User/getName','id=5');  调用Admin模块的User接口
 * @param  string  $name 格式 [模块名]/接口名/方法名
 * @param  array|string  $vars 参数
 */
function api($name,$vars=array()){
	$array     = explode('/',$name);
	$method    = array_pop($array);
	$classname = array_pop($array);
	$module    = $array? array_pop($array) : 'Common';
	$callback  = $module.'\\Api\\'.$classname.'Api::'.$method;
	if(is_string($vars)) {
		parse_str($vars,$vars);
	}
	return call_user_func_array($callback,$vars);
}