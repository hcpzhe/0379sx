<?php	require_once(dirname(__FILE__).'/inc/config.inc.php');IsModelPriv('yuding');

/*
**************************
(C)2010-2014 phpMyWind.com
update: 2014-5-30 17:22:45
person: Feng
**************************
*/


//初始化参数
$tbname = '#@__yuding';
$gourl  = 'yuding.php';


//引入操作类
require_once(ADMIN_INC.'/action.class.php');


//添加留言
if($action == 'add')
{
	if(!isset($htop)) $htop = '';
	if(!isset($rtop)) $rtop = '';
	$posttime = GetMkTime($posttime);
	$ip = GetIP();

	$sql = "INSERT INTO `$tbname` (siteid, linename, customer, contact, content, remark, orderid, posttime) VALUES ('$cfg_siteid', '$linename', '$customer', '$contact', '$content', '$remark', '$orderid', '$posttime')";
	if($dosql->ExecNoneQuery($sql))
	{
		header("location:$gourl");
		exit();
	}
}


//修改留言
else if($action == 'update')
{
	$posttime = GetMkTime($posttime);

	$sql = "UPDATE `$tbname` SET siteid='$cfg_siteid', customer='$customer', contact='$contact', content='$content', remark='$remark', orderid='$orderid', posttime='$posttime' WHERE id=$id";
	if($dosql->ExecNoneQuery($sql))
	{
		header("location:$gourl");
		exit();
	}
}


//无条件返回
else
{
    header("location:$gourl");
	exit();
}
?>