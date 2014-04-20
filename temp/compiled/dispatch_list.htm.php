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
<div class="form-div">
  <form action="javascript:searchOrder()"  name="searchForm">
    <img src="images/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
    起始日期<input name="sdate" type="text" id="sdate" value="<?php echo $this->_var['filter']['sdate']; ?>" onClick="javascript:WdatePicker()" readonly size="10" />
    结束日期<input name="edate" type="text" id="edate" value="<?php echo $this->_var['filter']['edate']; ?>" onClick="javascript:WdatePicker()" readonly size="10" />
	城市<select name="city" >
		<option value="">全部</option>
			<?php echo $this->html_options(array('options'=>$this->_var['city_arr'])); ?>
		</select>
	配送站<select name="station">
	       <option value="">全部</option>
	       <option value="100" >未分站</option>
		   <?php $_from = $this->_var['stations2']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'sta');if (count($_from)):
    foreach ($_from AS $this->_var['sta']):
?>
		   <option value="<?php echo $this->_var['sta']['station_id']; ?>" ><?php echo $this->_var['sta']['station_name']; ?></option>
		   	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	</select>
	配送批次<select name="turn">
	       <option value="">全部</option>
		   <?php echo $this->html_options(array('options'=>$this->_var['timeplan'])); ?>
	    </select>
	    
	订单状态<select name="otatus">
	       <option value="9">全部</option>
	       <option value="0">未确认</option>
		   <option value="1" selected="selected">确认</option>
		   <option value="2">取消</option>
		   <option value="3">无效</option>
		   <option value="4">退订</option>
	    </select>
	状态<select name="status">
	       <option value="">全部</option>
		   <option value="1" selected="selected">调度中</option>
		   <option value="2">完成</option>
	    </select>
	订单号<input name="order_sn" type="text" value="<?php echo $this->_var['filter']['order_sn']; ?>" id="order_sn" maxlength="16" size="16">
	大磅<input type="checkbox" name="big_goods" value='1'/>
	<select name="route_s" >
	       <option value="">配送包号</option>
		   <?php $_from = $this->_var['route']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'pack');if (count($_from)):
    foreach ($_from AS $this->_var['pack']):
?>
		   <option value="<?php echo $this->_var['pack']['route_id']; ?>"><?php echo $this->_var['pack']['route_name']; ?></option>
		   	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	</select>
    <input type="submit" value="搜索" class="button" class="button" />
  </form>
</div>
<form method="post" action="shipping_dispatch.php?act=batch_operate" name="listForm" onSubmit="return check()">
<div class="list-div">
    &nbsp;<input onclick='listTable.selectAll(this, "checkboxes")' type="checkbox" id="ct" />&nbsp;&nbsp;全选&nbsp;
    <input name="remove" type="button"  value="批量删除"  onclick="return set_delete();" class="button" />&nbsp;&nbsp;&nbsp;&nbsp;
	
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<select name="station" id="station"  onChange="show_sub(this,'route')">
	       <option value="">配送站</option>
		   <?php $_from = $this->_var['stations']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'ss');if (count($_from)):
    foreach ($_from AS $this->_var['ss']):
?>
		   <option value="<?php echo $this->_var['ss']['route_id']; ?>"><?php echo $this->_var['ss']['station_code']; ?></option>
		   	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	</select>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<select name="route" id="route">
	       <option value="">配送包号</option>
		   <?php $_from = $this->_var['route']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'pack');if (count($_from)):
    foreach ($_from AS $this->_var['pack']):
?>
		   <option value="<?php echo $this->_var['pack']['route_id']; ?>"><?php echo $this->_var['pack']['route_name']; ?></option>
		   	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	</select>
    <input name="pack" type="button" value="手动分包" onClick="return set_station();" class="button" />&nbsp;
	<input type="button" name="print" value="批量审核" onClick="return batchCheck();" class="button" id="print"/>
    <input name="order_id" type="hidden" value="" id="order_id"/>
</div>
  <table  width="100%" border="0"  >
    <tr>
   	<th width="6%">序号</th>
    <th width="13%">订单号</th>
    <th width=""><a href="javascript:listTable.sort('address', 'DESC'); ">配送地址</a></th>
    <th width="13%"><a href="javascript:listTable.sort('best_time', 'DESC'); ">配送时间</a></th>
    <th width="9%">配送批次</th>
    <th width="7%"><a href="javascript:listTable.sort('shipping_station_name', 'DESC'); ">配送站</a></th>
    <th width="6%">配送号</th>
    <th width="4%">状态</th>
    <th width="8%">操作</th>
	<th width="8%">操作员</th>
  </tr>
  </table>
</div>
<div style="width:100%;height:170px;" id="dis"> &nbsp;</div>

<div class="list-div" id="listDiv" style="margin:0;border:0">
<?php endif; ?>

<table cellspacing='1' cellpadding='3' id='list-table' width="100%">
  <?php $_from = $this->_var['order_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
<tr bgcolor="#ffffff" title="<?php echo $this->_var['list']['address']; ?>" onMouseover="this.style.backgroundColor='#e9f6f8'" onMouseout="this.style.backgroundColor=''" id="trList">
  	<td width="6%" align="center"><?php echo $this->_var['list']['i']; ?></td>
    <td width="13%" align="center">
	   <input type="checkbox" name="checkboxes" value="<?php echo $this->_var['list']['order_id']; ?>" />
	  <a href="more_order_info.php?order_id=<?php echo $this->_var['list']['order_id']; ?>" target="_blank"><?php echo $this->_var['list']['order_sn']; ?></a></td>
    <td   align="left"><?php if ($this->_var['list']['big']): ?><font color="#FF0000"><?php endif; ?><?php echo sub_str($this->_var['list']['address'],30); ?><?php if ($this->_var['list']['big']): ?></font><?php endif; ?></td> 
    <td width="13%" align="center"><?php echo $this->_var['list']['best_time']; ?></td>
    <td width="9%" align="center"><span id="picis_<?php echo $this->_var['list']['order_id']; ?>">第<?php echo $this->_var['list']['turn']; ?>批</span></td>
    <td width="7%" align="center" class="stationId">
	 <span id="station_<?php echo $this->_var['list']['order_id']; ?>"><?php echo $this->_var['list']['station_code']; ?></span></td>
    <td width="6%" align="center">
	<span id="pack_<?php echo $this->_var['list']['order_id']; ?>"><?php echo $this->_var['list']['route_code']; ?></span></td>
    <td  width="4%" align="center"><span id="check_<?php echo $this->_var['list']['order_id']; ?>"><?php if ($this->_var['list']['status'] == '1'): ?>完成<?php else: ?>调度中<?php endif; ?></span></td>
    <td  width="8%" align="center">
	<a href="javascript:;" onClick="listTable.remove(<?php echo $this->_var['list']['order_id']; ?>, '确认审核当前订单吗？', 'single_check')">审核</a>&nbsp;&nbsp;&nbsp;
	<a href="javascript:;" onClick="listTable.remove(<?php echo $this->_var['list']['order_id']; ?>, '确认删除当前订单吗？', 'remove_order')">删除</a></td>
	<td width="8%" align="center"><?php echo $this->_var['list']['sname']; ?></td>
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

var offset1 = $('#title').offset(); 
var offset2 = $('#title').prev().offset();
titleTop = offset1.top - offset2.top;
window.onscroll=function(){
	fixedTitle()
}
function searchOrder() {
        listTable.filter['sdate']    = Utils.trim(document.forms['searchForm'].elements['sdate'].value);
        listTable.filter['edate']    = Utils.trim(document.forms['searchForm'].elements['edate'].value);
        listTable.filter['order_sn'] = Utils.trim(document.forms['searchForm'].elements['order_sn'].value);
        listTable.filter['turn']     = Utils.trim(document.forms['searchForm'].elements['turn'].value);
        listTable.filter['station']  = Utils.trim(document.forms['searchForm'].elements['station'].value);
        listTable.filter['status']   = Utils.trim(document.forms['searchForm'].elements['status'].value);
        listTable.filter['route_s']   = Utils.trim(document.forms['searchForm'].elements['route_s'].value);
        listTable.filter['otatus']   = Utils.trim(document.forms['searchForm'].elements['otatus'].value);
        listTable.filter['big_goods']= document.forms['searchForm'].elements['big_goods'].checked?1:0;
        listTable.filter['city']     = Utils.trim(document.forms['searchForm'].elements['city'].value);
        listTable.filter['page']     = 1;
        listTable.loadList();
}

function batchCheck() {
	var args = '';
	var eles = getSelectedElement();

	if (eles == '' || typeof(eles) == 'undefined') {
		return false;
	} else {
		args = "act=batch_check&id=" + eles +listTable.compileFilter();
		Ajax.call('shipping_dispatch.php', args, Callback, "GET", "JSON");
	}
	document.getElementById('ct').checked = '';
}

function set_check() {
   var eles = get_checked();

   if (eles == '') {
     return false;
   } else {
	 var args = "act=remove_batch&id=" +eles +listTable.compileFilter();
	 Ajax.call('shipping_dispatch.php', args, Callback, "GET", "JSON");
   }
   document.getElementById('ct').checked = '';  
}


function set_delete() {
   var eles = get_checked();
   
   if (eles == '') {
     return false;
   } else {
	 var args = "act=delete_batch&id=" +eles +listTable.compileFilter();
	 Ajax.call('shipping_dispatch.php', args, Callback, "GET", "JSON");
   }
   document.getElementById('ct').checked = '';  
}


function set_station() {
   var eles = document.forms['listForm'].elements;
   var snArray = new Array();   
   var packArray = new Array();   
   var route = eles['route'].selectedIndex > 0 ? eles['route'] : eles['station'];
   
   var route_name = route.options[route.selectedIndex].text;  
   var route_id   = route.options[route.selectedIndex].value; 
   //var picis   = document.getElementById('picis').value;
   var picis = ''; 

   if(!route_id) {
      alert("请选择配送路区！"); return false;
   }

   for (var i=0; i<eles.length; i++) {
     if (eles[i].tagName == 'INPUT' && eles[i].type == 'checkbox' && eles[i].checked && eles[i].value != 'on') {
       var sn = eles[i].value;
       document.getElementById('station_' + sn).innerHTML = route_name.substring(0,2);
       document.getElementById('pack_' + sn).innerHTML = route_name.substring(2,4); 
	   /*if(picis > '0') {
	      document.getElementById('picis_' + sn).innerHTML = '第'+picis+'批';  
	   } */ 
       snArray.push(eles[i].value);
	   eles[i].checked = '';
     }
   }

   if (snArray.length == 0) {
     return false;
   } else {	
     var orders = snArray.toString();
     Ajax.call('shipping_dispatch.php','act=batch_operate&route='+route_id+'&order_id='+orders+'&pcs='+picis, null ,  'POST', 'JSON');
   }
}

function filter() {			// @过滤
	var args = '';
	
	for (var i in listTable.filter) {
		if (typeof(listTable.filter[i]) != 'function' && typeof(listTable.filter[i]) != 'undefined') {
			args += "&" + i + "=" + encodeURIComponent(listTable.filter[i]);
		}
	}
	return args;
}

function Callback(result, txt) {
  if (result.error > 0) {
    alert(result.message);
  } else {
	try {
      document.getElementById('listDiv').innerHTML = result.content;
      if (typeof result.filter == "object") {
        listTable.filter = result.filter;
      }
      listTable.pageCount = result.page_count;
    } catch (e) {
      alert(e.message);
    }
  }
}

function getSelectedElement() {			// @获取选择的元素
	var flag = false;
	var status = true;
	var arr = new Array();
	var eles = document.forms['listForm'].elements;
	
	for (var i = 0; i < eles.length; i++) {
		if (eles[i].tagName == 'INPUT' && eles[i].type == 'checkbox' && eles[i].checked == true && eles[i].value != 'on') {
			var flag = true;
			var val = document.getElementById('station_' + eles[i].value).innerHTML;
			if (val == '' || val == 'undefined')			status = false;
			arr.push(eles[i].value);
		}
	}
	
	if (flag) {
		if (!status) {
			alert('审核的订单没有分包！');
			return false;
		}
		if (arr.length > 0)			arr = arr.toString();
		
		return arr;
	}
}

function get_checked() {
	var flag = true;
	var status = false;
   var snArray = new Array();
   var res = '';
   var eles = document.forms['listForm'].elements;
   for (var i=0; i<eles.length; i++) {
     if (eles[i].tagName == 'INPUT' && eles[i].type == 'checkbox' && eles[i].checked && eles[i].value != 'on') {
		snArray.push(eles[i].value);
     }
   }
  
	   if (snArray.length > 0) {
		  res = snArray.toString();
	   }
	   return res;
}
function show_sub( obj, target ) {
	var parent = obj.options[obj.selectedIndex].value;
	Ajax.call('shipping_dispatch.php?act=sub_list', 'station=' + parent + '&target=' + target , show_response, "GET", "json");
}

function show_response(result) {
  var sel = document.getElementById(result.target);

  sel.length = 1;
  sel.selectedIndex = 0;
  sel.style.display = "" ;

  if (document.all) {
    sel.fireEvent("onchange");
  } else {
    var evt = document.createEvent("HTMLEvents");
    evt.initEvent('change', true, true);
    sel.dispatchEvent(evt);
  }

  if (result.regions) {
  //alert(result.regions.length);   
    for (i = 0; i < result.regions.length; i ++ ) {
      var opt = document.createElement("OPTION");
      opt.value = result.regions[i].route_id;
      opt.text  = result.regions[i].route_name;
      sel.options.add(opt);
    }
  }
}
function fixedTitlew(tp){

	var title = document.getElementById("title");
	 if(document.documentElement.scrollTop>=0){
		title.style.cssText="position: fixed;_position: absolute;width:98%;background:#ffffff;height:25px;";
		document.getElementById("dis").style.display="block";
		title.style.top=0+"px";

	 }else{
	 	title.style.cssText="position:relative;width:100%;height:25px;background:#ffffff";
		document.getElementById("dis").style.display="none";

		
	 }
	
}
--></script>

<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>