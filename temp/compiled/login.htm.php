<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>订单系统登录</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php echo $this->smarty_insert_scripts(array('files'=>'utils.js,validator.js,transport.js')); ?>
<style type="text/css">
body {
  color: white;
  font-family:"微软雅黑";
}
.inputw{
width:130px;
}

.ziti{
font-size:14px;
}
.jz{
font-size:12px;
}
.button{
border:0px;
background:#ece9d8;
padding:0px 6px;
text-align:center;
line-height:20px;
font-family:"微软雅黑";
color:#20201c
}
</style>
<script language="JavaScript">
<!--
// 这里把JS用到的所有语言都赋值到这里

if (window.parent != window)
{
  window.top.location.href = location.href;
}

//-->	
</script>
</head>
<body style="background: #278296">
<form method="post" action="privilege.php" name='theForm' onsubmit="return validate()">
  <table cellspacing="0" cellpadding="0" style="margin-top: 100px" align="center">
  <tr>
    <td><img src="images/mes_log.gif"/></td>
    <td style="padding-left: 50px">
      <table>
      <tr>
        <td>用户名</td>
        <td><input type="text" name="username" class="inputw"  /></td>
      </tr>
      <tr>
        <td>密码</td>
        <td><input type="password" name="password" class="inputw" /></td>
      </tr>
      <tr><td colspan="2" class="jz"><label><input type="checkbox" value="1" name="remember" />记住登录状态</label></td></tr>
      <tr><td>&nbsp;</td><td><input type="submit" value="登录系统" class="button" /></td></tr>
      </table>
    </td>
  </tr>
  </table>
  <input type="hidden" name="act" value="signin" />
</form>
<script language="JavaScript"><!--

  document.forms['theForm'].elements['username'].focus();

  /**
   * 检查表单输入的内容
   */
  function validate()
  { 
    var validator = new Validator('theForm');

    validator.required('username', '姓名不能为空');
    validator.required('password','密码不能为空');
    if (document.forms['theForm'].elements['captcha'])
    {
      validator.required('captcha', '验证码没填');
    }
    return validator.passed();

  }

//
--></script>
</body>