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
    日期<input name="sdate" type="text" id="sdate" value="<?php echo $this->_var['filter']['sdate']; ?>" onclick="javascript:WdatePicker()" readonly="true" size="10">
	发票项目<select name="turns">
	       <option value="">全部</option>
		   <option value="1">蛋糕</option>
		   <option value="2">食品</option>
	    </select>
	批次<select name="turn">
	       <option value="">全部</option>
		   <option value="1">第1批</option>
		   <option value="2">第2批</option>
		   <option value="3">第3批</option>
		   <option value="4">第4批</option>
	    </select>
	配送站<select name="station">
	       <option value="">全部</option>
		   <option value="1">中关村</option>
		   <option value="2">航天桥</option>
		   <option value="3">亚运村</option>
		   <option value="4">国美</option>
		   <option value="5">大郊亭</option>
		   <option value="6">洋桥</option>
		   <option value="7">台湖</option>
		   <option value="8">陶然亭</option>
		   <option value="9">天津</option>
	    </select>
	订单号<input name="order_sn" type="text" value="<?php echo $this->_var['filter']['order_sn']; ?>" maxlength="21" id="order_sn" size="16">
	流水号<input name="print_sn" type="text" value="<?php echo $this->_var['filter']['print_sn']; ?>" maxlength="4" id="print_sn" size="5">
	发票<select name="inv_f">
			<option value="" selected="selected">全部</option>
			<option value="1">已开</option>
			<option value="2" selected="selected">未开</option>
		</select>
	<input type="submit" value="搜索" class="button" />
  </form>
</div>
<form method="post" action="finance_inv.php" name="listForm" onSubmit="return check()">
<div class="list-div">
   &nbsp;<input onclick='listTable.selectAll(this, "checkboxes")' type="checkbox" id="ct" />&nbsp;&nbsp;全选&nbsp;
    <input name="checks" type="button"  value="批量处理"  onclick="return set_check();" class="button"/>&nbsp;&nbsp;&nbsp;&nbsp;
</div>
<div class="list-div" id="listDiv">
<?php endif; ?>

<table cellspacing='1' cellpadding='3' id='list-table' width="100%">
  <tr>
   	<th valign=top style="overflow: hidden; text-overflow:ellipsis" width="">	订单号</th>
	<th valign=top style="overflow: hidden; text-overflow:ellipsis" width="7%">	流水号</th>
	<th valign=top style="overflow: hidden; text-overflow:ellipsis" width="8%">	配送号</th>
    <th valign=top style="overflow: hidden; text-overflow:ellipsis" width="15%">支付方式</th>
    <th valign=top style="overflow: hidden; text-overflow:ellipsis" width="10%">金额</th>
    <th valign=top style="overflow: hidden; text-overflow:ellipsis" width="6%">项目</th>
    <th valign=top style="overflow: hidden; text-overflow:ellipsis" width="20%">发票抬头</th>
    <th valign=top style="overflow: hidden; text-overflow:ellipsis" width="6%">状态</th>
    <th valign=top style="overflow: hidden; text-overflow:ellipsis" width="6%">操作</th>
  </tr>
  <?php $_from = $this->_var['order_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('sn', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['sn'] => $this->_var['list']):
?>
<tr bgcolor="#ffffff" title="配送时间：<?php echo $this->_var['list']['best_time']; ?>，<?php echo $this->_var['list']['to_buyer']; ?>" onMouseover="this.style.backgroundColor='#e9f6f8'" onMouseout="this.style.backgroundColor=''" id="trList">
  	<td align="left">
	   <input type="checkbox" name="checkboxes" value="<?php echo $this->_var['list']['order_id']; ?>" />
       <input type="hidden" name="order[<?php echo $this->_var['sn']; ?>][oid]" value="<?php echo $this->_var['list']['order_id']; ?>" />
	  <a href="more_order_info.php?order_id=<?php echo $this->_var['list']['order_id']; ?>" target="_blank"><?php echo $this->_var['list']['order_sn']; ?></a></td>
	  <td align="left"><?php echo $this->_var['list']['print_sn']; ?></td>
	   <td align="left"><?php echo $this->_var['list']['route_name']; ?>-<?php echo $this->_var['list']['turn']; ?></td>
    <td align="left"><?php echo $this->_var['list']['pay_name']; ?><?php echo $this->_var['list']['pay_note']; ?></td>
    <td align="center"><input type="text" name="mount" id="mount_<?php echo $this->_var['list']['order_id']; ?>" value="<?php echo $this->_var['list']['total']; ?>" size="5" /></td>
    <td align="center"><?php echo $this->_var['list']['inv_content']; ?></td>
    <td align="left"><?php echo $this->_var['list']['inv_payee']; ?></td>
    <td align="center" id="txt_<?php echo $this->_var['list']['order_id']; ?>"><?php if ($this->_var['list']['id']): ?>已开<?php else: ?>未开<?php endif; ?></td>
	<td align="center"><a href="javascript:;" onclick="listTable.remove(<?php echo $this->_var['list']['order_id']; ?>, '确认开票吗？', 'ud')">开票</a>
 	<!--a href="javascript:;" onclick="check_sg(<?php echo $this->_var['list']['order_id']; ?>,'<?php echo $this->_var['list']['order_id']; ?>')">开票</a></td-->
  </tr>
  <?php endforeach; else: ?>
  <tr bgcolor="#ffffff"><td class="no-records" colspan="15">没有记录！</td></tr>
  <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
  <?php if ($this->_var['order_list']): ?>	
 <?php endif; ?>
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
   <input name="sn" type="hidden" value="" />
</form>

<script language="JavaScript"><!--
listTable.recordCount = <?php echo $this->_var['record_count']; ?>;
listTable.pageCount = <?php echo $this->_var['page_count']; ?>;

<?php $_from = $this->_var['filter']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
	listTable.filter.<?php echo $this->_var['key']; ?> = '<?php echo $this->_var['item']; ?>';
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

function searchOrder()
{
        listTable.filter['sdate']    = Utils.trim(document.forms['searchForm'].elements['sdate'].value);
        listTable.filter['order_sn'] = Utils.trim(document.forms['searchForm'].elements['order_sn'].value);
		listTable.filter['print_sn'] = Utils.trim(document.forms['searchForm'].elements['print_sn'].value);
        listTable.filter['turns']     = Utils.trim(document.forms['searchForm'].elements['turns'].value);//发票项目
		listTable.filter['turn']     = Utils.trim(document.forms['searchForm'].elements['turn'].value);
		listTable.filter['station']     = Utils.trim(document.forms['searchForm'].elements['station'].value);
        listTable.filter['inv_f']    = Utils.trim(document.forms['searchForm'].elements['inv_f'].value);//发票状态
        listTable.filter['page']     = 1;
        listTable.loadList();
}
function check()
{
   var snArray = new Array();
   var eles = document.forms['listForm'].elements;
   for (var i=0; i<eles.length; i++)
   {
     if (eles[i].tagName == 'INPUT' && eles[i].type == 'checkbox' && eles[i].checked && eles[i].value != 'on')
     {
       snArray.push(eles[i].value);
     }
   }
   if (snArray.length == 0)
   {
     return false;
   }
   else
   {
     eles['sn'].value = snArray.toString();
     return true;
   }
}
function check_sg(id,sn)
{	
	var inv = document.getElementById('mount_'+sn).value;
	if(confirm('确认发票已开？'))
	{
		Ajax.call('finan_inv.php', 'act=ud&id=' + id + '&inv=' + inv, null, "GET", "TEXT");
	}
	document.getElementById('txt_'+sn).innerHTML = '已开';
}
function Callback(result, txt)
{
  
  if (result.error > 0)
  {
    alert(result.message);
  }
  else
  {
	try
    {
      document.getElementById('listDiv').innerHTML = result.content;

      if (typeof result.filter == "object")
      {
        listTable.filter = result.filter;
      }

      listTable.pageCount = result.page_count;
    }
    catch (e)
    {
      alert(e.message);
    }
  }
}
function get_checked()
{
   var snArray = new Array();
   var res = '';
   var eles = document.forms['listForm'].elements;
   for (var i=0; i<eles.length; i++)
   {
     if (eles[i].tagName == 'INPUT' && eles[i].type == 'checkbox' && eles[i].checked && eles[i].value != 'on' && eles[i].value >1)
     {
       snArray.push(eles[i].value);
     }
   }
   if (snArray.length > 0)
   {
      res = snArray.toString();
   }
   return res;
}
--></script>

<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>