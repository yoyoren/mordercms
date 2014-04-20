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
    <form action="javascript:searchOrder()" name="searchForm">
    	<img src="images/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
        日期：<input name="bdate" type="text" value="<?php echo $this->_var['filter']['bdate']; ?>" onClick="javascript:WdatePicker()" readonly="true" size="10">&nbsp;&nbsp;
        <input type="submit" value="搜索" class="button" class="button" />
    </form>
</div>

<div id="listDiv"><?php endif; ?>
    <table cellspacing='1' cellpadding='3' id="table" width="100%">
        <tr id="attr">
            <th>配送站</th>
            <th>批次</th>
            <th>蛋糕数</th>
        </tr>
        <?php $_from = $this->_var['temp_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
        <tr bgcolor="#ffffff" onMouseover="this.style.backgroundColor='#e9f6f8'" onMouseout="this.style.backgroundColor=''">
            <td align="center" width="50px" >
            <?php if ($this->_var['list']['station_id'] == '1'): ?>中关村
            <?php elseif ($this->_var['list']['station_id'] == '2'): ?>航天桥
            <?php elseif ($this->_var['list']['station_id'] == '3'): ?>大郊亭
            <?php elseif ($this->_var['list']['station_id'] == '5'): ?>国美
            <?php elseif ($this->_var['list']['station_id'] == '4'): ?>亚运村
            <?php elseif ($this->_var['list']['station_id'] == '6'): ?>洋桥
            <?php elseif ($this->_var['list']['station_id'] == '7'): ?>台湖站
            <?php elseif ($this->_var['list']['station_id'] == '8'): ?>陶然亭
            <?php else: ?>未分站<?php endif; ?>
            </td>
            <td align="center" style="color:#F0F"><?php echo $this->_var['list']['turn']; ?></td>
            <td align="center" style="color:#00F"><?php echo $this->_var['list']['gnum']; ?></td>
        </tr>
        <?php endforeach; else: ?>
		<tr><td align="center" colspan="3" >没有查询到任何记录！</td></tr>
        <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
        <tr><td align="center" colspan="3" style="font-size:14px;">本处只提供临时站点的信息查询！</td></tr>
    </table>
    <table id="page-table" cellspacing="0"><tr align="center"><td align="center" nowrap="true"></td></tr></table><?php if ($this->_var['full_page']): ?>
</div>

<script language="JavaScript">
<?php $_from = $this->_var['filter']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
listTable.filter.<?php echo $this->_var['key']; ?> = '<?php echo $this->_var['item']; ?>';
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

function searchOrder() {
	listTable.filter['bdate'] = Utils.trim(document.forms['searchForm'].elements['bdate'].value);
	listTable.loadList();
}
</script><?php echo $this->fetch('pagefooter.htm'); ?><?php endif; ?>