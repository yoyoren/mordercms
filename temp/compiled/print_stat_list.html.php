<?php echo $this->fetch('header.html'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'utils.js,transport.js,validator.js,datepicker/WdatePicker.js')); ?>
<div style="position:fixed;_position:absolute;width:100%;background:#ddeef2">
<div class="text_title">
	<h3 style="float:left;display:inline;">--<?php echo $this->_var['ur_here']; ?></h3>
	<?php if ($this->_var['action_link']): ?>
	<div class="action-span"><a href="<?php echo $this->_var['action_link']['href']; ?>"><?php echo $this->_var['action_link']['text']; ?></a></div>
	<?php endif; ?>
	<div style="clear:both"></div>
</div>
<div class="form-div">
<form  action="print_stat.php?act=list" name="searchForm" method="POST" >
送货日期<input name="bdate" value="<?php echo $this->_var['bdate']; ?>" type="text" onFocus="javascript:WdatePicker()" style="width:70px"/>
<select name="status"><option value="9">全部</option><option value="1" <?php if ($this->_var['status'] == 1): ?>selected="selected" <?php endif; ?>>确认</option><option <?php if ($this->_var['status'] == 2): ?>selected="selected" <?php endif; ?> value="2">取消</option></select>
<input type="submit" class="button" value="提交" />
</form>
</div>
<div style="margin:0px 10px 0 10px">
<table width="100%" border="0" >
<tr bgcolor=#FFFFFF><td colspan="8">共计<?php echo $this->_var['count']; ?>条</td></tr>
    <tr>
		<th  width="130">送货日期</th>
		<th width="250">订单号</th>
        <th width="80">状态</th>
		<th width="100">流水号</th>
		<th width="160">配送打印时间</th>
        <th width="160">生产打印时间</th>
		<th width="103">配送打印操作人</th>
        <th width="110">生产打印操作人</th>
	</tr>
</table>
</div>
</div>
<div style="height:140px;width:100%"></div>
<div class="list-div" id="listDiv">

<table  cellpadding='3' cellspacing=1  border=0>
		<?php $_from = $this->_var['orders']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
		<tr  bgcolor="#ffffff" onMouseOver="this.style.backgroundColor='#e9f6f8'" onMouseOut="this.style.backgroundColor=''">
				<td width="130"><?php echo $this->_var['list']['bdate']; ?></td>
				<td width="250"><?php echo $this->_var['list']['order_sn']; ?></td>
                <td width="80"><?php echo $this->_var['list']['order_status']; ?></td>
				<td width="100"><?php echo $this->_var['list']['print_sn']; ?></td>
				<td width="160"><?php echo $this->_var['list']['ptime']; ?></td>
                <td width="160"><?php echo $this->_var['list']['stime']; ?></td>
				<td width="103"><?php echo $this->_var['list']['sname2']; ?></td>
                <td width="110"><?php echo $this->_var['list']['sname']; ?></td>
		</tr>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</table>

</div>
</body>
</html>
