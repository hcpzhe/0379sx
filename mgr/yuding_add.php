<?php require_once(dirname(__FILE__).'/inc/config.inc.php');IsModelPriv('yuding'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>添加预订</title>
<link href="templates/style/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="templates/js/jquery.min.js"></script>
<script type="text/javascript" src="templates/js/checkf.func.js"></script>
<script type="text/javascript" src="editor/kindeditor-min.js"></script>
<script type="text/javascript" src="editor/lang/zh_CN.js"></script>
</head>
<body>
<div class="formHeader"> <span class="title">添加预订</span> <a href="javascript:location.reload();" class="reload">刷新</a> </div>
<form name="form" id="form" method="post" action="yuding_save.php" onsubmit="return cfm_yuding();">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="formTable">
		<tr>
			<td width="25%" height="40" align="right">线路名称：</td>
			<td width="75%"><input type="text" name="linename" id="linename" class="input" />
				<span class="maroon">*</span><span class="cnote">带<span class="maroon">*</span>号表示为必填项</span></td>
		</tr>
		<tr>
			<td height="40" align="right">联系人：</td>
			<td><input type="text" name="customer" id="customer" class="input" />
				<span class="maroon">*</span><span class="cnote">带<span class="maroon">*</span>号表示为必填项</span></td>
		</tr>
		<tr>
			<td height="40" align="right">联系电话：</td>
			<td><input type="text" name="contact" id="contact" class="input" />
			<span class="maroon">*</span><span class="cnote">带<span class="maroon">*</span>号表示为必填项</span></td>
			</td>
		</tr>
		<tr>
			<td height="198" align="right">预订留言：</td>
			<td><textarea name="content" id="content"></textarea>
				<script>
				var editor;
				KindEditor.ready(function(K) {
					editor = K.create('textarea[name="content"]', {
						resizeType : 1,
						width:'500px',
						height:'180px',
						extraFileUploadParams : {
							sessionid :  '<?php echo session_id(); ?>'
						},
						allowPreviewEmoticons : false,
						allowImageUpload : false,
						items : [
							'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
							'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
							'insertunorderedlist', '|', 'emoticons', 'image', 'link']
					});
				});
				</script></td>
		</tr>
		<tr>
			<td height="198" align="right">跟进备注：</td>
			<td><textarea name="remark" id="remark"></textarea>
				<script>
				var editor;
				KindEditor.ready(function(K) {
					editor = K.create('textarea[name="remark"]', {
						resizeType : 1,
						width:'500px',
						height:'180px',
						extraFileUploadParams : {
							sessionid :  '<?php echo session_id(); ?>'
						},
						allowPreviewEmoticons : false,
						allowImageUpload : false,
						items : [
							'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
							'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
							'insertunorderedlist', '|', 'emoticons', 'image', 'link']
					});
				});
				</script></td>
				</td>
		</tr>
		<tr>
			<td height="40" align="right">排列排序：</td>
			<td><input type="text" name="orderid" id="orderid" class="inputos" value="<?php echo GetOrderID('#@__yuding'); ?>" /></td>
		</tr>
		<tr>
			<td height="40" align="right">提交时间：</td>
			<td><input type="text" name="posttime" id="posttime" class="inputms" value="<?php echo GetDateTime(time()); ?>" readonly="readonly" />
				<script type="text/javascript" src="plugin/calendar/calendar.js"></script> 
				<script type="text/javascript">
				Calendar.setup({
					inputField     :    "posttime",
					ifFormat       :    "%Y-%m-%d %H:%M:%S",
					showsTime      :    true,
					timeFormat     :    "24"
				});
				</script></td>
		</tr>
	</table>
	<div class="formSubBtn">
		<input type="submit" class="submit" value="提交" />
		<input type="button" class="back" value="返回" onclick="history.go(-1);" />
		<input type="hidden" name="action" id="action" value="add" />
	</div>
</form>
<script type="text/javascript">
function cfm_yuding() {
	if($("#linename").val() == "") {
		alert("请填写线路名称！");
		$("#linename").focus();
		return false;
	}
	if($("#customer").val() == "") {
		alert("请填写联系人！");
		$("#customer").focus();
		return false;
	}
	if($("#contact").val() == "") {
		alert("请填写联系电话！");
		$("#contact").focus();
		return false;
	}
}
</script>
</body>
</html>