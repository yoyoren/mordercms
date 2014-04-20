<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('header.html'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'utils.js,listtable.js,datepicker/WdatePicker.js')); ?>
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
     配送日期<input class="inpf" name="bdate" type="text" id="bdate" value="<?php echo $this->_var['tdm']; ?>" readonly="true" onFocus="javascript:WdatePicker()" size="10">
	 打印明日<input type="checkbox" name="tmd" id="tmd" value="1" onClick="setdate();" />
	<!--调度状态<select name="diaodu">
			<option value="">全部</option>
			<option value="1">分拣完成</option>
			<option value="2">未分拣</option>
	</select>
	配送站<select name="station" class="inpf">
	       <option value="">全部</option>
	       <option value="100">未分站</option>
		   <?php echo $this->html_options(array('options'=>$this->_var['stations'],'selected'=>$this->_var['filter']['station'])); ?>	
	</select>-->
	城市<select name="city">
	   <!--/*<option value="441">北京</option>
	   <option value="443">天津</option>*/-->
	   <?php $_from = $this->_var['city_arr']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'city');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['city']):
?>
	     <option value="<?php echo $this->_var['k']; ?>"><?php echo $this->_var['city']; ?></option>
	   <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	</select>
	配送站<select name="station" class="inpf">
	       <option value="">全部</option>
	       <option value="100">未分站</option>
		   <?php $_from = $this->_var['stations']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'station');if (count($_from)):
    foreach ($_from AS $this->_var['station']):
?>	
		   <option value="<?php echo $this->_var['station']['station_id']; ?>"><?php echo $this->_var['station']['station_name']; ?></option>
		   <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	</select>
	配送批次<select name="turn" class="inpf">
	        <option value="">全部</option>
		    <?php $_from = $this->_var['turn']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 't');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['t']):
?>
			<option value="<?php echo $this->_var['k']; ?>"><?php echo $this->_var['t']; ?></option>
			<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	    </select>
	订单状态<select name="order_status" class="inpf">	               
		           <option value="1">已确认</option>
                   <option value="2">取消</option>
                   <option value="4">退订</option>
                   <option value="0">未确认</option>
	    </select>
	打印状态<select name="prints" class="inpf">
		   <option value="2">未打印</option>
		   <option value="1">已打印</option>
	       <option value="">全部</option>
	    </select>
张数<select name="psize"><option value="30">30张</option><option value="10">10张</option>
<option value="40">40张</option><option value="20">20张</option><option value="50">50张</option></select>


	订单号<input name="order_sn" type="text" value="" maxlength="20" id="order_sn" class="inpf" size="15">
	流水号<input name="print_sn" type="text" value="" size="5" id="print_sn" />
    <input type="submit" value="搜索订单" class="button" />
	
	<!--<a href="print_stat.php" >打印结果查询</a>-->
  </form>
</div>
<table width="100%" border="0">
<tr>
 <th width="5%">
	
	订单状态
	</th>
    <th width="4%" >
	
	生产单
	</th>
    <th width="4%" >
	
	打印
	</th>
    <th width="4%">
	
	调度状态
	</th>
    <th width="4%">
	
	路区号
	</th>
    <th width="4%">
	
	流水号
	</th>
    <th  width="4%">
	
	查看
	</th>
    <th width="2%">
	
	序
	</th>
    <th width="10%">
	
	订单号
	</th> 
	<th width="8%">
	
	蛋糕名称
	</th>
	<th width="7%" >
	
	下单时间
	</th>
	<th width="7%">
	
	送货时间
	</th>
	<th>
	
	送货地址
	</th>
	<th  width="4%">
	
	订货人
	</th>
	<th width="8%">
	
	电话
	</th>
	<th width="5%">
	
	应收
	</th>
	<th width="4%">	
	坐席
	</th>	
	
	<th width="4%">	
	打印次数
	</th>

  </tr>
</table>
</div>
<div style="width:100%;height:153px;" id="dis"> &nbsp;</div>

<form action="order_print.php?act=print" method="post" name="listForm" target="_blank" onSubmit="return check();">

<div class="list-div" id="listDiv" style="margin:0;border:0">
<?php endif; ?>

<table cellspacing='1' cellpadding='3' id='list-table' width="100%">
  <?php $_from = $this->_var['orders']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
			<tr bgcolor="#ffffff" onMouseOver="this.style.backgroundColor='#e9f6f8'" onMouseOut="this.style.backgroundColor=''">
				<td bgcolor=#FFFF66 width="5%"><?php echo $this->_var['list']['order_status']; ?></td>
				<td width="4%" align=center bgcolor=#FFFF66><?php if ($this->_var['list']['stime'] > '0'): ?><font color=#FF0000>已打</font><?php else: ?><font color=#33CC00>未打</font><?php endif; ?></td>
				<td width="4%" align=center bgcolor=#FFFF66><?php if ($this->_var['list']['ptime'] > '0'): ?><font color=#FF0000>已打</font><?php else: ?><font color=#33CC00>未打</font><?php endif; ?>
					<input type="checkbox" name="checkboxes" value="<?php echo $this->_var['list']['order_id']; ?>" id="selectId">
				</td>
				<td width="4%" align=center bgcolor=#FFFF66><?php if ($this->_var['list']['status'] > '0'): ?><font color=#FF0000>完成</font><?php else: ?><font color=#33CC00>未完成</font><?php endif; ?></td>
				<td width="4%"><?php echo $this->_var['list']['route_name']; ?></td>
				<td width="4%"><?php echo $this->_var['list']['print_sn']; ?></td>
				<td width="4%" align=center><a href="order_info1.php?oid=<?php echo $this->_var['list']['order_id']; ?>" target="_blank">查看</a></td>
				<td width="2%" align=center><?php echo $this->_var['list']['i']; ?></td>
				<td width="10%" ><?php echo $this->_var['list']['order_sn']; ?></td>
				<td width="8%"><?php echo $this->_var['list']['goods']; ?></td>
				<td width="7%"><?php echo $this->_var['list']['add_time']; ?></td>
				<td width="7%"><?php echo $this->_var['list']['best_time']; ?></td>
				<td width=""><?php echo $this->_var['list']['address']; ?></td>
				<td width="4%"><div  style="overflow-x:scroll;width:31px"><?php echo $this->_var['list']['orderman']; ?></div></td>
				<td width="2%"><div  style="overflow-x:hidden;width:86px"><?php echo $this->_var['list']['ordertel']; ?></div></td>
				<td width="5%"><?php echo $this->_var['list']['order_amount']; ?></td>
				<td width="4%"><?php echo $this->_var['list']['kfgh']; ?></td>
				<td width="4%"><?php echo $this->_var['list']['pt']; ?></td>
			</tr>
			<?php endforeach; else: ?>
            <tr bgcolor="#FFFFFF"><td class="no-records" colspan="18">没有记录！</td></tr>
           <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
</table>

<table id="page-table" bgcolor="#ffffff" cellspacing="0" width="100%">
  <tr >
    <td>
	   &nbsp;<input onclick='listTable.selectAll(this, "checkboxes")' type="checkbox" id="ct" />&nbsp;&nbsp;全选&nbsp;
	  <!--<input id="allselect" name="allselect" type="checkbox" value="all" onClick="selectA();">全选-->
      <input type="reset" value="重　置" style="cursor:pointer;" class="button">　
      <input type="submit" value="打　印"  style="cursor:pointer;" class="button">
	</td>
    <td align="center"  nowrap="true">
    <?php echo $this->fetch('page.htm'); ?>
    </td>
  </tr>
</table>
    <input type="hidden" name="act" value="print" />
<input name="order_id" type="hidden" value="" />
<input name="sdate" type="hidden" value="<?php echo $this->_var['filter']['bdate']; ?>" />
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

function searchOrder()
{
        listTable.filter['bdate']    = Utils.trim(document.forms['searchForm'].elements['bdate'].value);
		//alert(listTable.filter['bdate']);
        listTable.filter['city'] = Utils.trim(document.forms['searchForm'].elements['city'].value);
        listTable.filter['turn']     = Utils.trim(document.forms['searchForm'].elements['turn'].value);
		listTable.filter['station']     = Utils.trim(document.forms['searchForm'].elements['station'].value);
        listTable.filter['order_status']  = Utils.trim(document.forms['searchForm'].elements['order_status'].value);
        listTable.filter['prints'] = Utils.trim(document.forms['searchForm'].elements['prints'].value);
        listTable.filter['psize']  = Utils.trim(document.forms['searchForm'].elements['psize'].value);
        listTable.filter['order_sn']   = Utils.trim(document.forms['searchForm'].elements['order_sn'].value);
        listTable.filter['print_sn'] =   Utils.trim(document.forms['searchForm'].elements['print_sn'].value);       
        listTable.filter['page']     = 1;
        listTable.loadList();
}
	function setdate()
   {
 	var year  = new Date().getFullYear();
	var month = new Date().getMonth()+1;

	var checked =  document.getElementById('tmd').checked;
	//alert(checked);
	var date  = checked == true ? new Date().getDate() + 1 : new Date().getDate();
	month = (month < 10) ? "0" + month : month;
    date  = (date < 10 ) ? "0" + date : date;	
    //alert(year + '-' + month + '-' + date);
    document.getElementById('bdate').value = year + '-' + month + '-' + date;
   }
  function check()
{
   //if (!confirm('确实要打印选中的订单吗？')) return false;
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
   }
}


--></script>

<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>