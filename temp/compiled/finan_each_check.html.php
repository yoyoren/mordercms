<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('header.html'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'utils.js,listtable.js,datepicker/WdatePicker.js')); ?>
<div class="text_title">
	<h3 style="float:left;display:inline;">--<?php echo $this->_var['ur_here']; ?></h3>
	<div class="action-span"><a href="export_excel.php?date=<?php echo $this->_var['filter']['sdate']; ?>">导出</a></div>
	<div style="clear:both"></div>
</div>
<div class="form-div">
  <form action="finan_each_check.php?act=list" name="listForm">
    <img src="images/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
    日期<input name="sdate" type="text" id="sdate" value="<?php echo $this->_var['filter']['sdate']; ?>" onClick="javascript:WdatePicker()" readonly="true" size="10" />
	<input type="submit" value="搜索" class="button" />
  </form>
</div>
<div class="list-div" id="listDiv">
<?php endif; ?>

<table cellspacing='1' cellpadding='3' id='list-table' width="100%">
  <tr>
   	<td>序号</td>
    <td>订单编号</td>
    <td>流水号</td>
    <td>数量</td>
    <td>折后金额</td>
    <td>附件费</td>
    <td>配送费</td>
    <td>订单总额</td>
    <td>现金</td>
    <td>POS</td>
    <td>支付宝</td>
    <td>快钱</td>
    <td>礼金卡</td>
    <td>现金券</td>
    <td>月结</td>
    <td>免费支付</td>
  </tr>
  <?php $_from = $this->_var['order_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('sn', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['sn'] => $this->_var['list']):
?>
  <?php if ($this->_var['list']['key'] == 0): ?>
 <tr bgcolor="#FFFFFF" title="<?php echo $this->_var['list']['best_time']; ?>" onMouseover="this.style.backgroundColor='#FFFF99'" onMouseout="this.style.backgroundColor=''" id="trList">
 <?php elseif ($this->_var['list']['key'] == 1): ?>
 <tr bgcolor="#99FFFF" title="<?php echo $this->_var['list']['best_time']; ?>" onMouseover="this.style.backgroundColor='#99FF99'" onMouseout="this.style.backgroundColor='#99FFFF'" id="trList">
 <?php endif; ?>
    <td><?php echo $this->_var['list']['xuhao']; ?></td>
    <td><?php echo $this->_var['list']['order_sn']; ?></td>		
    <td><?php echo $this->_var['list']['p_sn']; ?></td>						
    <td><?php echo $this->_var['list']['goods_numbers']; ?></td>	
    <td><?php echo $this->_var['list']['goods_amount']; ?></td>	
    <td><?php echo $this->_var['list']['pack_fee']; ?></td>		
    <td><?php echo $this->_var['list']['peisongfei']; ?></td>		
    <td><?php echo $this->_var['list']['totalprice']; ?></td>		
    <td><?php echo $this->_var['list']['cash']; ?></td>			
    <td><?php echo $this->_var['list']['pos']; ?></td>				
    <td><?php if ($this->_var['list']['isornoz'] == 1): ?><span style="color:#FF0000"><?php echo $this->_var['list']['zhifubao']; ?></span><?php else: ?><?php echo $this->_var['list']['zhifubao']; ?><?php endif; ?></td>		
    <td><?php if ($this->_var['list']['isornok'] == 1): ?><span style="color:#FF0000"><?php echo $this->_var['list']['kuaiqian']; ?></span><?php else: ?><?php echo $this->_var['list']['kuaiqian']; ?><?php endif; ?></td>		
    <td><?php echo $this->_var['list']['surplus']; ?></td>			
    <td><?php echo $this->_var['list']['bonus']; ?></td>			
    <td><?php echo $this->_var['list']['yuejie']; ?></td>			
    <td><?php echo $this->_var['list']['free']; ?></td>			
</tr>
  <?php endforeach; else: ?>
  <tr bgcolor="#ffffff"><td class="no-records" colspan="15">没有记录！</td></tr>
  <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
<tr bgcolor="#FF00FF">
    <td colspan="3">总计</td>							
    <td><?php echo $this->_var['total']['goods_numbers']; ?></td>	
    <td><?php echo $this->_var['total']['goods_amount']; ?></td>	
    <td><?php echo $this->_var['total']['pack_fee']; ?></td>		
    <td><?php echo $this->_var['total']['peisongfei']; ?></td>		
    <td><?php echo $this->_var['total']['totalprice']; ?></td>		
    <td><?php echo $this->_var['total']['cash']; ?></td>			
    <td><?php echo $this->_var['total']['pos']; ?></td>				
    <td><?php echo $this->_var['total']['zhifubao']; ?></td>		
    <td><?php echo $this->_var['total']['kuaiqian']; ?></td>		
    <td><?php echo $this->_var['total']['surplus']; ?></td>			
    <td><?php echo $this->_var['total']['bonus']; ?></td>			
    <td><?php echo $this->_var['total']['yuejie']; ?></td>			
    <td><?php echo $this->_var['total']['free']; ?></td>			
</tr>
</table>

<table id="page-table" bgcolor="#ffffff" cellspacing="0" width="100%">
  <tr >
    <td align="center"  nowrap="true">
    <?php echo $this->fetch('page.htm'); ?>
    </td>
  </tr>
</table>
<?php if ($this->_var['full_page']): ?>
  </div>

<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>