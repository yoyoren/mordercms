<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>改单系统</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body bgcolor="#DDEEF2" style='font-size:13px;'>
<div style='line-height:40px;'><b>改单记录</b></div>

<div>
<form action='change_order.php' method='post' onsubmit='return checksousuo(1)'>
<table>
	<tr>
		<td width='120'>输入订单后五位：</td>
		<td width='350'><input type='text' name='order_num' id='order_num'value='' />
			<input type='hidden' name='step' value='sousuo'/></td>
		<td width='120'><input type='submit' value='搜索'/> </td>
	</tr>
</table>
</form>
</div>

<div>
<form action='change_order.php' method='post' onsubmit='return checksousuo(2)'>
<table>
	<tr>
		<td width='120'>输入搜索时间：</td>
		<td width='350'>
			<input type='text' name='order_time1' id='order_time1' value='' />-<input type='text' name='order_time2' id='order_time2' value='' />
			<input type='hidden' name='step' value='shijian'/></td>
		<td width='120'><input type='submit' value='搜索'/> </td>
	</tr>
</table>		  
</form>
</div>
<?php if ($this->_var['act'] == 'sousuo'): ?>
<table border='1' bordercolor='#80BDCB' cellspacing='0' cellpadding='0' width='1200' style='line-height:30px;background:white;'>
	<tr style='font-weight:bold;'>
		<td width='55'>订单号</td>
		<td width='180'>修改时间</td>
		<td width='55'>修改人</td>
		<td width='418'>改前内容</td>
		<td width='415'>改后内容</td>
		<td width='70'>修改类型</td>
	</tr>
	<?php if ($this->_var['kong'] == kong): ?>
	<tr>
		<td colspan='6'>搜索的订单不存在</td>
	</tr>
	<?php else: ?>
<?php $_from = $this->_var['result']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'v');if (count($_from)):
    foreach ($_from AS $this->_var['v']):
?>	
	<tr onmouseover='changecolor(<?php echo $this->_var['v']['log_id']; ?>)' id=<?php echo $this->_var['v']['log_id']; ?> onmouseout="changecolor2(<?php echo $this->_var['v']['log_id']; ?>)">
		<td><?php if ($this->_var['v']['order_id'] == ''): ?>&nbsp;<?php else: ?><?php echo $this->_var['v']['order_id']; ?><?php endif; ?></td>
		<td><?php if ($this->_var['v']['editime'] == ''): ?>&nbsp;<?php else: ?><?php echo $this->_var['v']['editime']; ?><?php endif; ?></td>
		<td><?php if ($this->_var['v']['admin_id'] == ''): ?>&nbsp;<?php else: ?><?php echo $this->_var['v']['admin_id']; ?><?php endif; ?></td>
		<td><?php if ($this->_var['v']['order_fore'] == ''): ?>&nbsp;<?php else: ?><?php echo $this->_var['v']['order_fore']; ?><?php endif; ?></td>
		<td><?php if ($this->_var['v']['order_after'] == ''): ?>&nbsp;<?php else: ?><?php echo $this->_var['v']['order_after']; ?><?php endif; ?></td>
		<td><?php if ($this->_var['v']['alter_type'] == ''): ?>&nbsp;<?php else: ?><?php echo $this->_var['v']['alter_type']; ?><?php endif; ?></td>
	</tr>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	<?php endif; ?>
</table>
<?php elseif ($this->_var['act'] == 'shijian'): ?>
<table border='1' bordercolor='#80BDCB' cellspacing='0' cellpadding='0' width='1200' style='line-height:30px;background:white;'>
<tr style='font-weight:bold;'>
		<td width='55'>订单号</td>
		<td width='180'>修改时间</td>
		<td width='55'>修改人</td>
		<td width='418'>改前内容</td>
		<td width='415'>改后内容</td>
		<td width='70'>修改类型</td>
	</tr>
<?php if ($this->_var['kong1'] == kong1): ?>
	<tr>
		<td colspan='6'>搜索的结果不存在</td>
	</tr>
	<?php else: ?>
<?php $_from = $this->_var['res']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'v');if (count($_from)):
    foreach ($_from AS $this->_var['v']):
?>
	<tr onmouseover='changecolor(<?php echo $this->_var['v']['log_id']; ?>)' id=<?php echo $this->_var['v']['log_id']; ?> onmouseout="changecolor2(<?php echo $this->_var['v']['log_id']; ?>)">
		<td><?php if ($this->_var['v']['order_id'] == ''): ?>&nbsp;<?php else: ?><?php echo $this->_var['v']['order_id']; ?><?php endif; ?></td>
		<td><?php if ($this->_var['v']['editime'] == ''): ?>&nbsp;<?php else: ?><?php echo $this->_var['v']['editime']; ?><?php endif; ?></td>
		<td><?php if ($this->_var['v']['admin_id'] == ''): ?>&nbsp;<?php else: ?><?php echo $this->_var['v']['admin_id']; ?><?php endif; ?></td>
		<td><?php if ($this->_var['v']['order_fore'] == ''): ?>&nbsp;<?php else: ?><?php echo $this->_var['v']['order_fore']; ?><?php endif; ?></td>
		<td><?php if ($this->_var['v']['order_after'] == ''): ?>&nbsp;<?php else: ?><?php echo $this->_var['v']['order_after']; ?><?php endif; ?></td>
		<td><?php if ($this->_var['v']['alter_type'] == ''): ?>&nbsp;<?php else: ?><?php echo $this->_var['v']['alter_type']; ?><?php endif; ?></td>
	</tr>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	<?php endif; ?>
</table>
<?php else: ?>
<table border='1' bordercolor='#80BDCB' cellspacing='0' cellpadding='0' width='1200' style='line-height:30px;background:white;'>
	<tr style='font-weight:bold;'>
		<td width='55'>订单号</td>
		<td width='180'>修改时间</td>
		<td width='55'>修改人</td>
		<td width='418'>改前内容</td>
		<td width='415'>改后内容</td>
		<td width='70'>修改类型</td>
	</tr>
<?php $_from = $this->_var['rows']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'v');if (count($_from)):
    foreach ($_from AS $this->_var['v']):
?>	
	<tr onmouseover='changecolor(<?php echo $this->_var['v']['log_id']; ?>)' id=<?php echo $this->_var['v']['log_id']; ?> onmouseout="changecolor2(<?php echo $this->_var['v']['log_id']; ?>)">
		<td><?php if ($this->_var['v']['order_id'] == ''): ?>&nbsp;<?php else: ?><?php echo $this->_var['v']['order_id']; ?><?php endif; ?></td>
		<td><?php if ($this->_var['v']['editime'] == ''): ?>&nbsp;<?php else: ?><?php echo $this->_var['v']['editime']; ?><?php endif; ?></td>
		<td><?php if ($this->_var['v']['admin_id'] == ''): ?>&nbsp;<?php else: ?><?php echo $this->_var['v']['admin_id']; ?><?php endif; ?></td>
		<td><?php if ($this->_var['v']['order_fore'] == ''): ?>&nbsp;<?php else: ?><?php echo $this->_var['v']['order_fore']; ?><?php endif; ?></td>
		<td><?php if ($this->_var['v']['order_after'] == ''): ?>&nbsp;<?php else: ?><?php echo $this->_var['v']['order_after']; ?><?php endif; ?></td>
		<td><?php if ($this->_var['v']['alter_type'] == ''): ?>&nbsp;<?php else: ?><?php echo $this->_var['v']['alter_type']; ?><?php endif; ?></td>
	</tr>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
<tr>
	<td colspan='6' align='center'><a href='change_order.php?currentPage=1'>首页</a>
	<a href='change_order.php?currentPage=<?php echo $this->_var['prepage']; ?>'>上一页</a>
	<a href='change_order.php?currentPage=<?php echo $this->_var['nextpage']; ?>'>下一页</a>
	<a href='change_order.php?currentPage=<?php echo $this->_var['last']; ?>'>尾页</a></td>
</tr>
</table>
<?php endif; ?>
</body>
</html>
<script language='javascript'>
//检查订单号是否为空
function checksousuo(k)
{ 
	var num=document.getElementById('order_num').value;

    var time1=document.getElementById('order_time1').value;
    var time2=document.getElementById('order_time2').value;
	var pattern=/^\d{4}-\d{2}-\d{2}$/;
	if(k==1)
	{
		if(num =='')
		{
			alert('请先输入五位订单号');
			return false;
		}
	}
	if(k==2)
	{
		
		if(time1 =='')
		{
			alert('请输入起始时间');
			return false;
		}
		if(!pattern.test(time1))
		{
			alert('时间格式不对');
			return false;
		}
		if(time2 =='')
		{
			alert('请输入结束时间');
			return false;
		}
		if(!pattern.test(time2))
		{
			alert('时间格式不对');
			return false;
		}
	}
}
function changecolor(n)
{
	 document.getElementById(n).style.background = '#DDEEF2';
}
function changecolor2(m)
{
	document.getElementById(m).style.background = 'white';
}
</script>