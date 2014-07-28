<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('header.html'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'utils.js,listtable.js,datepicker/WdatePicker.js')); ?>
<EMBED NAME="MUSIC1" SRC="images/tip.mp3" id='music1' Loop=-1 AUTOSTART=false hidden=true MASTERSOUND> 
<div class="text_title">
	<h3 style="float:left;display:inline;">--<?php echo $this->_var['ur_here']; ?></h3>
	<?php if ($this->_var['action_link']): ?>
	<div class="action-span"><a href="<?php echo $this->_var['action_link']['href']; ?>"><?php echo $this->_var['action_link']['text']; ?></a></div>
	<?php endif; ?>
	<div style="clear:both"></div>
</div>
<div class="form-div">
  <form action="javascript:searchPro()"  name="searchForm">
    <img src="images/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
    配送日期<input class="inpf" name="bdate" type="text" id="bdate" value="<?php echo $this->_var['tdm']; ?>" readonly="true" onFocus="javascript:WdatePicker()"   size="10">
	打印明日<input type="checkbox" name="tmd" id="tmd" value="1" onClick="setdate();" />
	<!--蛋糕<select name="cake">
	    <option value="">全部</option>
		<option value="35">01郎姆</option>
		<option value="36">02布莱克</option>
		<option value="37">03核桃</option>
		<option value="38">04杏仁克鲁兹</option>
		<option value="39">05布朗尼</option>
		<option value="40">06松仁蛋奶</option>
		<option value="41">07栗蓉暗香</option>
		<option value="42">08黑森林</option>
		<option value="43">09榴莲飘飘</option>
		<option value="44">10卡布其诺</option>
		<option value="45">11心语心愿</option>
		<option value="46">12百利甜</option>
		<option value="47">13卡百利</option>
		<option value="48">14花格</option>
		<option value="49">15椰蓉可可</option>
		<option value="50">16巧克力丝语</option>
		<option value="51">17黑白</option>
		<option value="52">18黑方</option>
		<option value="53">19黒越橘</option>
		<option value="54">20杰瑞</option>
		<option value="55">21芒果慕斯</option>
		<option value="58">24提拉米苏</option>
		<option value="63">26平安夜</option>
		<option value="64">27爱尔兰咖啡</option>
        <option value="92">28清境</option>
		<option value="75">29桂圆冰激凌</option>
		<option value="93">30中秋蛋糕</option>
		<option value="91">34体验装</option>
		<option value="86">无糖卡布其诺</option>
		<option value="87">无糖松仁蛋奶</option>
	</select>-->
	蛋糕<select name="cake">
	     <option value="">全部</option>
	<?php $_from = $this->_var['cakes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'cake');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['cake']):
?>
		<option value="<?php echo $this->_var['cake']['goods_id']; ?>"><?php echo $this->_var['cake']['goods_sn']; ?><?php echo $this->_var['cake']['goods_name']; ?></option>
	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	
	</select>
	城市<select name="city" class="inpf">
                 <option value="">全部</option>
               <option value="441">北京</option> 
		   
	       	
	</select>
	配送批次<select name="turn" class="inpf">
	        <option value="">全部</option>
		    <?php $_from = $this->_var['turn']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 't');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['t']):
?>
			<option value="<?php echo $this->_var['k']; ?>"><?php echo $this->_var['t']; ?></option>
			<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	    </select>
	生产打印状态<select name="printp" class="inpf">
		   <option value="2">未打印</option>
		   <option value="1">已打印</option>
	       <option value="">全部</option>
	    </select>
	订单号<input name="order_sn" type="text" value="" maxlength="18" class="inpf" size="16">
	流水号<input name="print_sn" type="text" value="" maxlength="4"  class="inpf" size="4">
    <input type="submit" value="搜索打印订单" class="button" />	
  </form>
</div>
<form method="post" action="pro_print.php?act=print" name="listForm" target="_blank" onSubmit="return check()">

<div class="list-div" id="listDiv">
<?php endif; ?>

<table cellspacing='1' cellpadding='3' id='list-table' width="100%">
 <tr>
		<th  width="80" >
		 
		 打印状态
		 </th>
		<th  width="80" >
		 
		 订单状态
		 </th>
		<th  width="80" >
		
		调度状态
		</th>
		<th  width="80">
		
		 序
		</th>
		<th   width="80">
		
		 流水号
		 </th>
		<th  width="120" >
		 
		 订单号
		 </th>
		<th  width="100" >
		  
		 订购商品
		 </th>
		<th  width="120" >
		 
		 下单时间
		 </th>
		 <th  width="120" >
		 
		 送货时间
		 </th>
		<th  width="" >
		
		 送货地址
		 </th>
		 
		 <th  width="30" >
		 
		 打印次数
		 </th>
		 
    </tr>
  <?php $_from = $this->_var['orders']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
			<tr  bgcolor="#ffffff" onMouseOver="this.style.backgroundColor='#e9f6f8'" onMouseOut="this.style.backgroundColor=''">
				<td width="80" align=center bgcolor=#FFFF66><?php if ($this->_var['list']['stime'] > '0'): ?><font color=#FF0000>已打</font><?php else: ?><font color=#33CC00>未打</font><?php endif; ?>
					<input type="checkbox" name="checkboxes" value="<?php echo $this->_var['list']['order_id']; ?>" id="selectId" <?php if ($this->_var['list']['status'] > '0'): ?>checked<?php endif; ?>>
				</td>
				<td width="80" align=center><?php echo $this->_var['list']['order_status']; ?></td>
				<td width="80" align=center><input type="hidden" value="<?php echo $this->_var['list']['status']; ?>" id="status<?php echo $this->_var['list']['order_id']; ?>" /><?php if ($this->_var['list']['status'] > '0'): ?><font color=#FF0000>完成</font><?php else: ?><font color=#33CC00>未完成</font><?php endif; ?></td>
				<td  width="80" align=center><?php echo $this->_var['list']['i']; ?></td>
				<td width="80"><?php echo $this->_var['list']['print_sn']; ?></td>
				<td width="120"><?php echo $this->_var['list']['order_sn']; ?></td>
				<td width="100"><?php echo $this->_var['list']['goods']; ?></td>
				<td width="120"><?php echo $this->_var['list']['add_time']; ?></td>
				<td width="120"><?php echo $this->_var['list']['best_time']; ?></td>
				<td width=""><?php echo $this->_var['list']['address']; ?></td>				
				<td width="30">				
				   <?php echo $this->_var['list']['st']; ?>				
				</td>
			</tr>		
		<?php endforeach; else: ?>
            <tr bgcolor="#FFFFFF"><td class="no-records" colspan="11">没有记录！</td></tr>
        <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
</table>

<table id="page-table" bgcolor="#ffffff" cellspacing="0" width="100%">
  <tr >
    <td>
	  <!--<input id="allselect" name="allselect" type="checkbox" value="all" onClick="selectA();">全选-->
	  &nbsp;<input onclick='listTable.selectAll(this, "checkboxes")' type="checkbox" id="ct" />&nbsp;&nbsp;全选&nbsp;
      <input type="reset" value="重　置" style="cursor:pointer;" class="button">　
      <input type="submit" value="打　印" style="cursor:pointer;" class="button">
	 &nbsp;&nbsp;&nbsp;蛋糕数量： <?php echo $this->_var['cake_count']; ?>个
	</td>
    <td align="center"  nowrap="true">
    <?php echo $this->fetch('page.htm'); ?>
	
    </td>
  </tr>
</table>
    <input type="hidden" name="act" value="print"  />
    <input name="order_id" type="hidden" value="" />
	<input name="sdate" type="hidden" value="<?php echo $this->_var['filter']['bdate']; ?>" />
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



function searchPro()
    {
        listTable.filter['bdate']    = Utils.trim(document.forms['searchForm'].elements['bdate'].value);
		//alert(listTable.filter['bdate']);       
        listTable.filter['cake']     = Utils.trim(document.forms['searchForm'].elements['cake'].value);
        listTable.filter['city']     = Utils.trim(document.forms['searchForm'].elements['city'].value);
        listTable.filter['turn']     = Utils.trim(document.forms['searchForm'].elements['turn'].value);
        listTable.filter['printp']   = Utils.trim(document.forms['searchForm'].elements['printp'].value);
        listTable.filter['order_sn']   = Utils.trim(document.forms['searchForm'].elements['order_sn'].value);
        listTable.filter['print_sn']   = Utils.trim(document.forms['searchForm'].elements['print_sn'].value);       
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
   //if (!confirm('确实要打印选中的生产单吗？')) return false;
   var snArray = new Array();
   var eles = document.forms['listForm'].elements;
   for (var i=0; i<eles.length; i++)
   {
     if (eles[i].tagName == 'INPUT' && eles[i].type == 'checkbox' && eles[i].checked && eles[i].value != 'on' && eles[i].value != 'all')
     {
       snArray.push(eles[i].value);
       var status=document.getElementById('status'+eles[i].value);
		if(status.value<=0){
			alert('有未调度的订单，请联系调度人员审核后再进行打印');
			return false;
		} 
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
  function refresh(){
	Ajax.call('pro_print.php', 'act=alert', alertMessage, "GET", "json");
    
   }
   function alertMessage(result){
	if(result){

		var mytime = new Date();
		if(mytime.getHours() < 21 && mytime.getHours()>8){
		
			//document.getElementById('music1').play();
			
			document.getElementById('music1').play(); 
			alert('您有新的订单');
			// document.MUSIC1.play(); 
			window.location.reload();
		}

		
		
	}
   }
   var int = window.setInterval("refresh()",50000);
   
</script>

<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>