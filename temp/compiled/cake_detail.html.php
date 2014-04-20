<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('header.html'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'utils.js,listtable.js,datepicker/WdatePicker.js,jquery.js')); ?>
<div id="title" style="position: fixed;_position: absolute;width:100%;background:#ffffff;">
<div class="text_title">
	<h3 style="float:left;display:inline;">--<?php echo $this->_var['ur_here']; ?></h3>
	<?php if ($this->_var['action_link']): ?>
	<div class="action-span"><a href="<?php echo $this->_var['action_link']['href']; ?>"><?php echo $this->_var['action_link']['text']; ?></a></div>
	<?php endif; ?>
	<div style="clear:both"></div>
</div>

<form method="post" action="shipping_dispatch.php?act=batch_operate" name="listForm" onSubmit="return check()">

  <table  width="100%" border="0"  >
    <tr>
            <th>序号</th>
            <th>订单号</th>
            <th>详情</th>
            <th><a href="javascript:listTable.sort('add_time','<?php echo $this->_var['ids']; ?>'); ">下单时间</a></th>
            <th><a href="javascript:listTable.sort('best_time','<?php echo $this->_var['ids']; ?>'); ">送货时间</a></th>
            <th><a href="javascript:listTable.sort('best_time','<?php echo $this->_var['ids']; ?>'); ">完成时间</a></th>
            <th>生产提示</th>
     </tr>
  </table>
</div>
<div style="width:100%;height:90px;" id="dis"> &nbsp;</div>

<div class="list-div" id="listDiv" style="margin:0;border:0">
<?php endif; ?>

<table cellspacing='1' cellpadding='3' id='list-table' width="100%" border="0">
 <?php $_from = $this->_var['cake_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
<tr onMouseover="this.style.backgroundColor='#EDEF9E'" onMouseout="this.style.backgroundColor=''">
            <td align="center" width="120"><?php echo $this->_var['list']['i']; ?></td>
            <td align="center" style="background-color:#FF0; color:#F00; font-size:16px;" width="120">&nbsp;<?php echo $this->_var['list']['order_sn']; ?></td>
            <td align="center" width="100"><a href="cake_detail.php?act=info&id=<?php echo $this->_var['list']['order_id']; ?>" target="_blank">详情</a></td>
            <td align="center" width="250"><?php echo $this->_var['list']['add_time']; ?></td>
            <td align="center" width="200"><?php echo $this->_var['list']['best_time']; ?></td>
            <td align="center" width="200"><?php echo $this->_var['list']['done_time']; ?></td>
            <td align="center" width="200"><?php echo $this->_var['list']['scts']; ?></td>
        </tr>
  <?php endforeach; else: ?>
  <tr bgcolor="#ffffff"><td class="no-records" colspan="10">没有记录！</td></tr>
  <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
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
</form>

<script language="JavaScript"><!--
listTable.recordCount = <?php echo $this->_var['record_count']; ?>;
listTable.pageCount = <?php echo $this->_var['page_count']; ?>;

<?php $_from = $this->_var['filter']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
	listTable.filter.<?php echo $this->_var['key']; ?> = '<?php echo $this->_var['item']; ?>';
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

--></script>

<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>