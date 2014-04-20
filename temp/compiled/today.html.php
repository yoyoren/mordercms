<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('header.html'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'utils.js,listtable.js,datepicker/WdatePicker.js')); ?>
<div class="text_title">
	<h3 style="float:left;display:inline;">--<?php echo $this->_var['ur_here']; ?></h3>
	<?php if ($this->_var['action_link']): ?>
	<div class="action-span"><a href="<?php echo $this->_var['action_link']['href']; ?>"><?php echo $this->_var['action_link']['text']; ?></a></div>
	<?php endif; ?>
	<div style="clear:both"></div>
</div>

<div class="form-div">
    <form action="javascript:searchOrder();" name="searchForm">
        <img src="images/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
        城市：<select name="city"><?php echo $this->html_options(array('options'=>$this->_var['city_list'])); ?></select>
        日期：<input name="bdate" type="text" value="<?php echo $this->_var['filter']['bdate']; ?>" onClick="javascript:WdatePicker();" readonly="true" size="10">&nbsp;&nbsp;
        批次：<select name="turn" id="turn"><option value="">-全部-</option><?php echo $this->html_options(array('options'=>$this->_var['lot_list'])); ?></select>
        <input type="submit" value="搜索" class="button" class="button" />
    </form>
</div>



<div id="listDiv" class="list-div"><?php endif; ?>
    <table cellspacing='1' cellpadding='3' id="table" width="100%">
        <tr id="attr">
            <th>蛋糕</th>
            <th>规格</th>
            <th>数量</th>
            <th>详情</th>
        </tr>
        <?php $_from = $this->_var['stat_list']['stat']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
        <tr bgcolor="#ffffff" onMouseover="this.style.backgroundColor='#EDEF9E'" onMouseout="this.style.backgroundColor=''">
            <td align="left" width="25%" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->_var['list']['goods_name']; ?></td>
            <td align="center"><span ><?php echo $this->_var['list']['goods_attr']; ?></span>&nbsp;磅</td>
            <td align="center"><span ><?php echo $this->_var['list']['goods_sum']; ?></span>&nbsp;</td>
            <td align="center"><a href="cake_detail.php?act=list&id=<?php echo $this->_var['list']['order_group']; ?>" target="_blank">详情</a></td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td align="center" colspan="4" style="color:#F0F">没有符合条件的查询记录！</td></tr>
        <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
        <tr style="background-color:#FF0; font-size:14px;">
            <td align="center">合计：</td>
            <td align="center">总磅数：<span style="color:#F00"><?php echo $this->_var['stat_list']['weight_total']; ?></span>&nbsp;磅</td>
            <td align="center">总数量：<span style="color:#F0F"><?php echo $this->_var['stat_list']['num_total']; ?></span>&nbsp;个</td>
            <td align="center">&nbsp;</td>
        </tr>
        <tr><td colspan="4" align="center" style="font-size:16px; color:#F00;";>本处只提供即时的当日及今日以后的数据！</td></tr>
        <tr><td colspan="4" align="left" style="font-size:16px; margin-right:50px;"><br /></td></tr>
    </table>
    <table id="page-table" cellspacing="0"><tr><td align="center" nowrap="true"></td></tr></table><?php if ($this->_var['full_page']): ?>
</div>

<script language="JavaScript">
<?php $_from = $this->_var['filter']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
listTable.filter.<?php echo $this->_var['key']; ?> = '<?php echo $this->_var['item']; ?>';
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

function searchOrder() { // Search order
	var frm = document.forms['searchForm'].elements;
	listTable.filter['city'] = Utils.trim(frm['city'].value);
	listTable.filter['bdate'] = Utils.trim(frm['bdate'].value);
	listTable.filter['turn'] = Utils.trim(frm['turn'].value);
	listTable.loadList();
}
</script>
<?php echo $this->fetch('pagefooter.htm'); ?><?php endif; ?>