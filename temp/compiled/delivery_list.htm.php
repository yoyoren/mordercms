<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('header.html'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'utils.js,listtable.js,transport.js,datepicker/WdatePicker.js')); ?>
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
	配送站<select name="stations" onchange="cemployee(this);" id="stations">
	       <option value="" <?php if ($this->_var['Current']): ?> disabled="disabled"<?php endif; ?>>全部</option>
	       <option value="100" <?php if ($this->_var['Current']): ?> disabled="disabled"<?php endif; ?>>未分站</option>
		   <?php $_from = $this->_var['stations']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'sta');if (count($_from)):
    foreach ($_from AS $this->_var['sta']):
?>
		   <option value="<?php echo $this->_var['sta']['station_id']; ?>" <?php if ($this->_var['Current']): ?>selected="selected"<?php endif; ?>><?php echo $this->_var['sta']['station_name']; ?></option>
		   	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	</select>
	配送员<select name="employee" id="employee">
			<option value="">全&nbsp;&nbsp;部</option>
		   <?php $_from = $this->_var['employees']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'elist');if (count($_from)):
    foreach ($_from AS $this->_var['elist']):
?>
			<option value="<?php echo $this->_var['elist']['sender']; ?>"><?php echo $this->_var['elist']['name']; ?></option>
			<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		   </select>
	配送号<input type="text" name="pack_no" id="pack_no" size="4" />
	配送批次<select name="turn">
	       <option value="">全部</option>
		   <?php echo $this->html_options(array('options'=>$this->_var['timeplan'])); ?>
	    </select>
	订单状态<select name="orderstatus">
	       <option value="100">全部</option>
	       <option value="0">未确认</option>
		   <option value="1" selected="selected">确认</option>
		   <option value="2">取消</option>
		   <option value="3">无效</option>
		   <option value="4">退订</option>
	    </select>
	状态<select name="status">
	       <option value="">全部</option>
		   <option value="1" selected="selected">待配送</option>
		   <option value="2">配送中</option>
		   <option value="3">完成</option>
	    </select>
	订单号<input name="order_sn" type="text" value="<?php echo $this->_var['filter']['order_sn']; ?>" maxlength="16" id="order_sn" size="16">
	流水号<input name="print_sn" type="text" value="" maxlength="4" id="print_sn" size="6">
    <input type="submit" value="搜索" class="button" onclick="ver_check()" />
  </form>
</div>

<form method="post" action="shipping_delivery.php?act=batch_operate" name="listForm" onsubmit="return check()">
<div class="list-div" id="listDiv">
<?php endif; ?>
<div class="list-div">&nbsp;&nbsp;&nbsp;
	<input onclick='listTable.selectAll(this, "checkboxes")' type="checkbox" id="ct" />&nbsp;&nbsp;全选
	<select name="sender" id="sender">
		<option value="">配送员</option>
		<?php $_from = $this->_var['employees']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'elist');if (count($_from)):
    foreach ($_from AS $this->_var['elist']):
?>
		<option value="<?php echo $this->_var['elist']['sender']; ?>"><?php echo $this->_var['elist']['name']; ?></option>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	</select>
	<input name="pack" type="button" value="手动分配"  onclick="return setSender();" class="button" id="pack" />
	<input type="button" value="批量审核" name="print" onclick="return set_check();" class="button" id="print" />&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="button" value="批量删除" name="delete" onclick="return set_delete();" class="button" id="print" />
    <input name="order_id" type="hidden" value="" />
</div>
<table width="100%" cellspacing="1">
  <tr>
    <th width="4%">序号</th>
    <th width="4%">流水号</th>
    <th width="6%"><a href="javascript:listTable.sort('shipping_pack_no', 'DESC'); ">配送代码</a></th>
    <th width="12%">订单号</th>
    <th width="32%"><a href="javascript:listTable.sort('address', 'DESC'); ">配送地址</a></th>
    <th width="13%"><a href="javascript:listTable.sort('best_time', 'DESC'); ">配送时间</a></th>
    <th width="6%">配送员</th>
    <th width="4%">状态</th>
    <th width="4%">操作</th> 
  </tr>
  <?php $_from = $this->_var['order_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'glist');if (count($_from)):
    foreach ($_from AS $this->_var['glist']):
?>
  <tr title="<?php echo $this->_var['glist']['address']; ?>" bgcolor="#ffffff" onMouseover="this.style.backgroundColor='#e9f6f8'" onMouseout="this.style.backgroundColor=''">
    <td align="center"><input type="checkbox" name="checkboxes" key="<?php echo $this->_var['sn']; ?>" value="<?php echo $this->_var['glist']['order_id']; ?>" id="check_<?php echo $this->_var['glist']['order_id']; ?>" /><?php echo $this->_var['glist']['i']; ?></td>
    <td align="center"><?php echo empty($this->_var['glist']['print_sn']) ? '1000' : $this->_var['glist']['print_sn']; ?></td>
    <td align="center" id="<?php echo $this->_var['glist']['order_id']; ?>"><?php echo $this->_var['glist']['route_name']; ?>-<?php echo $this->_var['glist']['turn']; ?></td>  
    <td align="center"><a href="more_order_info.php?order_id=<?php echo $this->_var['glist']['order_id']; ?>" target="_blank"><?php echo $this->_var['glist']['order_sn']; ?></a></td>
    <td align="left"><?php echo sub_str($this->_var['glist']['address'],28); ?></td> 
    <td align="center"><?php echo $this->_var['glist']['best_time']; ?></td>
	<td align="center" id="em_<?php echo $this->_var['glist']['order_id']; ?>"><?php echo $this->_var['glist']['employee_name']; ?></td>
    <td align="center"><span id="txt_<?php echo $this->_var['glist']['order_id']; ?>"><?php if ($this->_var['glist']['status'] == '1'): ?>待配送<?php elseif ($this->_var['glist']['status'] == '2'): ?>配送中<?php elseif ($this->_var['glist']['status'] == '3'): ?>完成<?php endif; ?></span></td>
    <td align="center"><a href="javascript:;" onclick="listTable.remove(<?php echo $this->_var['glist']['order_id']; ?>, '确认审核当前订单吗？', 'check_eg')">审核</a>
	</td>
  </tr>
  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</table>
<table id="page-table" cellspacing="0" width="100%" bgcolor='#ffffff'>
  <tr>
    <td align="center" nowrap="true">
    <?php echo $this->fetch('page.htm'); ?>
    </td>
  </tr>
</table>
<?php if ($this->_var['full_page']): ?>
</div>
</form>
<script language="JavaScript">
listTable.recordCount = <?php echo $this->_var['record_count']; ?>;
listTable.pageCount = <?php echo $this->_var['page_count']; ?>;

<?php $_from = $this->_var['filter']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
listTable.filter.<?php echo $this->_var['key']; ?> = '<?php echo $this->_var['item']; ?>';
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>


/**
* 搜索订单
*/
function searchOrder()
{
        listTable.filter['sdate']    = Utils.trim(document.forms['searchForm'].elements['sdate'].value);
        listTable.filter['order_sn'] = Utils.trim(document.forms['searchForm'].elements['order_sn'].value);
        listTable.filter['turn']     = Utils.trim(document.forms['searchForm'].elements['turn'].value);
        listTable.filter['station']  = Utils.trim(document.forms['searchForm'].elements['stations'].value);
        listTable.filter['employee'] = Utils.trim(document.forms['searchForm'].elements['employee'].value);
        listTable.filter['pack_no']  = Utils.trim(document.forms['searchForm'].elements['pack_no'].value);
        listTable.filter['status']   = Utils.trim(document.forms['searchForm'].elements['status'].value);
        listTable.filter['print_sn']   = Utils.trim(document.forms['searchForm'].elements['print_sn'].value);
        listTable.filter['orderstatus']   = Utils.trim(document.forms['searchForm'].elements['orderstatus'].value);
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
     eles['order_id'].value = snArray.toString();
     return true;
   }
}
function setSender()
{
   var sender = document.getElementById("sender").value;
   
   if(sender == '')
   {
   		alert("请选择配送员"); return false;
   }
   var eles = get_checked();
   if (eles == '')
   {
     return false;
   }
   else
   {
	 var args = "act=sender&order_id=" +eles+"&sender="+sender +gfilter();
	 //alert(args);
	 Ajax.call('shipping_delivery.php', args, Callback, "GET", "JSON");
   }
   document.getElementById('ct').checked = '';  
}
function set_check()
{
   var eles = document.forms['listForm'].elements;
   var eles = get_checked();
   if (eles == '')
   {
     return false;
   }
   else
   {
	 var args = "act=print&order_id=" +eles +gfilter();
	 //alert(args);
	 Ajax.call('shipping_delivery.php', args, Callback, "GET", "JSON");
   }
   document.getElementById('ct').checked = '';  
}
function check_eg(order_id)
{
	//alert(order_id);
	document.getElementById('txt_'+order_id).innerHTML = '配送中';
	Ajax.call('shipping_delivery.php','act=check_eg&order_id='+order_id, null ,  'POST', 'JSON');
}
function employee_null()
{
	var employee = document.getElementById('station').value;
	if(employee == '')
	{
		document.getElementById('pack_no').value = '';
	}
}
function set_delete()
{
   var eles = get_checked();
   if (eles == '')
   {
     return false;
   }
   else
   {
   		if(confirm("确定要删除吗？"))
		{
			 var args = "act=delete&order_id=" +eles +gfilter();
			  //alert(args);
			 Ajax.call('shipping_delivery.php', args, Callback, "GET", "JSON");
		}
   }
   document.getElementById('ct').checked = '';  
}
function gfilter()
{
  var args ='';
  for (var i in listTable.filter)
  {
    
	if (typeof(listTable.filter[i]) != "function" && typeof(listTable.filter[i]) != "undefined")
    {
      args += "&" + i + "=" + encodeURIComponent(listTable.filter[i]);
    }
  }
  return args;
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
     if (eles[i].tagName == 'INPUT' && eles[i].type == 'checkbox' && eles[i].checked && eles[i].value != 'on')
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
function ver_check()
{
	var eles = document.forms['listForm'].elements;
	
   	for (var i=0; i<eles.length; i++)
   	{
		if (eles[i].tagName == 'INPUT' && eles[i].type == 'checkbox' && eles[i].checked && eles[i].value != 'on' && eles[i].id =='checkboxes')
     	{
			document.forms["listForm"].elements["ct"].checked = true;
     	}
		else (eles[i].tagName == 'INPUT' && eles[i].type == 'checkbox' && !eles[i].checked && eles[i].value != 'on')
		{
       		//alert(eles[i].name);
			document.forms["listForm"].elements["ct"].checked = false;
		}
   	}
}
function cemployee(obj)
{
    var asd = obj.options[obj.selectedIndex].value;  
  //alert(parent);
  Ajax.call('shipping_delivery.php?act=employee', 'stn=' + asd , show_response, "GET", "JSON");
}
function show_response(result)
{
  //alert(result);return false;
  
  var sel = document.getElementById('employee');

  sel.length = 1;

  if (document.all)
  {
    sel.fireEvent("onchange");
  }
  else
  {
    var evt = document.createEvent("HTMLEvents");
    evt.initEvent('change', true, true);
    sel.dispatchEvent(evt);
  }

  if (result)
  {
  //alert(result.regions.length);   
    for (i = 0; i < result.length; i ++ )
    {
      var opt = document.createElement("OPTION");
      opt.value = result[i].employee_id;
      opt.text  = result[i].employee_name;

      sel.options.add(opt);
    }
  }
}
</script>


<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>