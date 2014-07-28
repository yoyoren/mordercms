<?php echo $this->fetch('header.html'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'datepicker/WdatePicker.js')); ?>
<div class="text_title">
<h3 style="float:left;display:inline;">--<?php echo $this->_var['ur_here']; ?></h3>
<?php if ($this->_var['action_link']): ?>
<div class="action-span"><a href="<?php echo $this->_var['action_link']['href']; ?>"><?php echo $this->_var['action_link']['text']; ?></a></div>
<?php endif; ?>
<div style="clear:both"></div>
</div>



<div class="form-div">
    
    <form name="form1" action="" method="get">
    <div id="search_box" class="ziti1">
    送货日期：
 <input name="datetime" type="text" id="sdate" value="<?php echo $this->_var['datetime']; ?>" onClick="javascript:WdatePicker()" readonly="true" size="10" />
    订单号：<input type="text" name="order_sn" value="<?php echo $this->_var['order_sn']; ?>" />
    流水号：<input size="6" type="text" name="print_sn" value="<?php echo $this->_var['print_sn']; ?>" maxlength="4" />
    <input type="submit" name="sub" class="button"  value="查询" /> <input type="hidden" value="query" name="act" />
     </div>
     </form>
    
</div>

    
    <div class="list-div" id="listDiv">
    <div>
   	  <table width="100%" border="0" cellspacing="1" cellpadding="5">
<tr  class="ziti1">
    			<th >送货日期</td>
    			<th>订单号</td>
    			<th>流水号</td>
    			<th>配送打印时间</td>
    			<th>生产打印时间</td>
  			</tr>
  			<?php if ($this->_var['arr']): ?>
  			<tr bgcolor="#ffffff" align="center">
   			  <td><?php echo $this->_var['arr']['best_time']; ?></td>
   			  <td><a href="more_order_info.php?order_id=<?php echo $this->_var['arr']['order_id']; ?>" target="_blank"><?php echo $this->_var['arr']['order_sn']; ?></a></td>
   			  <td><?php echo $this->_var['arr']['print_sn']; ?></td>
   			  <td><?php echo $this->_var['arr']['ptime']; ?></td>
   			  <td><?php echo $this->_var['arr']['stime']; ?></td>
  			</tr>
  			<?php else: ?>
  			<tr bgcolor="#ffffff"><td colspan="5">无记录</td></tr>
  			<?php endif; ?>
		</table>

  </div>
    
</body>
</html>