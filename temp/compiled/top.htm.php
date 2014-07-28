<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="js/transport.js" ></script>
<script type="text/javascript" src="js/jquery.min.js" ></script>
<style type="text/css">
body {
  margin: 0px;
  padding: 0px;
  color: #491903;
  font: 12px "sans-serif", "Arial", "Verdana";
}
#header-div {
  background: #278296;
  border-bottom: 1px solid #FFF;
}

#logo-div {
  height: 50px;
  float: left;
}

#license-div {
  height: 50px;
  float: left;
  text-align:center;
  vertical-align:middle;
  line-height:50px;
}

#license-div a:visited, #license-div a:link {
  color: #EB8A3D;
}

#license-div a:hover {
  text-decoration: none;
  color: #EB8A3D;
}

#submenu-div {
  height: 50px;
}

#submenu-div ul {
  margin: 0;
  padding: 0;
  list-style-type: none;
}

#submenu-div li {
  float: right;
  padding: 0 10px;
  margin: 3px 0;
  border-left: 1px solid #FFF;
}

#submenu-div a:visited, #submenu-div a:link {
  color: #FFF;
  text-decoration: none;
}

#submenu-div a:hover {
  color: #F5C29A;
}

#loading-div {
  clear: right;
  text-align: right;
  display: block;
}

#menu-div {
  background: #80BDCB;
  font-weight: bold;
  height: 24px;
  line-height:24px;
}

#menu-div ul {
  margin: 0;
  padding: 0;
  list-style-type: none;
}

#menu-div li {
  float: left;
  border-right: 1px solid #192E32;
  border-left:1px solid #BBDDE5;
}

#menu-div a:visited, #menu-div a:link {
  display:block;
  padding: 0 20px;
  text-decoration: none;
  color: #335B64;
}

#menu-div a:hover {
  color: #000;
  background:#80BDCB;
  
}
#current{
background:url('__PUBLIC__/images/sanjiao.png');
}

#submenu-div a.fix-submenu{clear:both; margin-left:5px; padding:1px 5px; *padding:3px 5px 5px; background:#DDEEF2; color:#278296;}
#submenu-div a.fix-submenu:hover{padding:1px 5px; *padding:3px 5px 5px; background:#FFF; color:#278296;}
#menu-div li.fix-spacel{width:30px; border-left:none;}
#menu-div li.fix-spacer{border-right:none;}
</style>
<?php echo $this->smarty_insert_scripts(array('files'=>'js/transport.js')); ?>
<script type="text/javascript">

</script>
</head>
<body>
<div id="header-div">
  <div id="logo-div"><img src="images/mes_log1.gif" alt="21cake" /></div>
  <div id="license-div" style="color:#ffffff;font-size:18px;font-family:'微软雅黑'">订单流转系统</div>
  <div style="float:left;margin-top:20px;margin-left:15px;color:#fff">欢迎你，<?php echo $this->_var['admin_name']; ?>！ 今天是<?php echo $this->_var['date']; ?>，星期<?php echo $this->_var['week']; ?></session></div>
  <div id="submenu-div">
    <div id="send_info" style="padding: 5px 10px 0 0; clear:right;text-align: right; color: #FF9900;width:40%;float: right;">
	  <a href="javascript:window.top.frames['main'].document.location.reload();" class="fix-submenu">刷新</a>
      <a href="index.php?act=editpassword" target="main" class="fix-submenu">个人中心</a>
      <a href="privilege.php?act=logout" target="_top" class="fix-submenu">退出</a>
    </div>
    <div id="load-div" style="padding: 5px 10px 0 0; text-align: right; color: #FF9900; display: none;width:40%;float:right;"><img src="images/top_loader.gif" width="16" height="16" alt="<?php echo $this->_var['lang']['loading']; ?>" style="vertical-align: middle" /> <?php echo $this->_var['lang']['loading']; ?></div>
  </div>
</div>
<div id="menu-div">
  <ul>
    <li class="fix-spacel">&nbsp;</li>
    <?php $_from = $this->_var['city']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'c');if (count($_from)):
    foreach ($_from AS $this->_var['c']):
?>
	    <?php if ($this->_var['c']['city_code'] == $this->_var['city_group']): ?>
		<li ><a href='javascript:;' id="c<?php echo $this->_var['c']['city_code']; ?>" style="text-decoration:underline" onclick="changeCity(<?php echo $this->_var['c']['city_code']; ?>)" ><?php echo $this->_var['c']['city_name']; ?></a></li>
		<?php else: ?>
		<li ><a href='javascript:;' id="c<?php echo $this->_var['c']['city_code']; ?>" style="" onclick="changeCity(<?php echo $this->_var['c']['city_code']; ?>)" ><?php echo $this->_var['c']['city_name']; ?></a></li>
		<?php endif; ?>
	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    <li class="fix-spacer">&nbsp;</li>
  </ul>
  <br class="clear" />
</div>
<script>
function changeCity(city_code){
	var nowcity=document.getElementById('c'+city_code);
	$("[id^='c']").css("text-decoration","none");
	$("#c"+city_code).css("text-decoration","underline");
	var args = 'act=changecity&city_code='+city_code;
	Ajax.call('privilege.php',args,callback,'GET')
}
function callback(){
	window.top.frames['main'].document.location.reload();
}
</script>
</body>
</html>