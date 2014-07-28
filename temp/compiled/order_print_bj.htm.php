<style type="text/css">
body {margin:0;}
td {font-size:12px;}
@media print {
	.noprint {display:none;}
}
.zt{
border:1px white solid;
padding-left:10px;
height:831px;
overflow:hidden;
}
.con1{
border:1px white solid;
width:700px;
height:30px;
padding-left:80px;
}
.con2{
border:1px white solid;
width:700px;
padding-left:60px;
height:109px;
}
.con3{
border:1px white solid;
width:700px;
height:136px;
padding-left:20px;
}
.con4{
border:1px white solid;
width:700px;
height:166px;
padding-left:25px;
}

</style>
<div class="zt">
<table height="144"><tr><td>&nbsp;&nbsp;&nbsp;</td></tr></table>

<div class="con1">
<table width="700" border="0" height="">
	<tr height="30">
      <td width="18%" valign="center" ><b>
	  <?php $_from = $this->_var['array']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'pack_0_70509700_1399363383');if (count($_from)):
    foreach ($_from AS $this->_var['pack_0_70509700_1399363383']):
?>
	  <font size="-1"><?php echo $this->_var['pack_0_70509700_1399363383']; ?></font><br/>
	  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	  </b></td>
      <td width="17%">  &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->_var['order']['add_date']; ?></td>	  
	  
      <td width="65%"><font size="-1">
	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	  &nbsp;&nbsp;&nbsp;&nbsp;
	  <?php echo $this->_var['order']['order_sn']; ?></font>&nbsp;&nbsp;<B><br/>
	   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	   &nbsp;&nbsp;&nbsp;&nbsp;
	  <font size="-1"><?php echo $this->_var['psn']; ?>-<?php echo $this->_var['goods']['cakenum']; ?></font></B></td>
	</tr>
</table>
</div>
<table width="700" height="50">
<tr><td>&nbsp;&nbsp;</td></tr>
</table>

<div class="con2">
<table width="700" border="0" height="">
    <tr height="27">
      <td width="16%" colspan="2" style="overflow:hidden;">&nbsp;&nbsp;<?php echo $this->_var['order']['orderman']; ?></td>
	  <td width="20%">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->_var['order']['ordertel']; ?></td>
      <td width="7%">&nbsp;</td>
	  <td width="19%">&nbsp;</td>
      <td width="1%">&nbsp;</td>
	  <td width="38%"><?php echo $this->_var['order']['tel']; ?>&nbsp;</td>
    </tr>
    <tr height='28'>
    	<td width="16%" colspan="2">&nbsp;&nbsp;<?php echo $this->_var['order']['consignee']; ?></td>
        <td width="16%">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->_var['order']['mobile']; ?>&nbsp;&nbsp;&nbsp;<?php echo $this->_var['order']['tel']; ?></td>
		<td width="7%">&nbsp;</td>
        <td width="19%"><?php echo $this->_var['order']['btime']; ?></td>
		<td width="1%">&nbsp;</td>
        <td width="38%"><?php if ($this->_var['order']['integral'] > 0): ?>-<?php echo $this->_var['order']['integral']; ?><?php endif; ?></td>
  </tr>
     <tr height="26">
	    <td width="2%">
    	<td colspan="6" width="98%"><?php echo $this->_var['order']['address']; ?></td>
     </tr>
     <tr height="26">
	    <td width="2%"></td>
    	<td colspan="6" height='25'><?php echo $this->_var['order']['money_address']; ?></td>
      </tr>
</table>
</div>
<table height="10">
<tr>
<td>&nbsp;</td>
</tr>
</table>

<div class="con3">
<table width="700" height="136" border="0">
	<tr height="26">
		<td colspan="7">&nbsp;</td>
  	</tr>
    <?php $_from = $this->_var['goods']['goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods_0_70591500_1399363383');if (count($_from)):
    foreach ($_from AS $this->_var['goods_0_70591500_1399363383']):
?>
     <tr height="16" style="line-height:12px;">
          <td width="11%" align="left"><?php if ($this->_var['goods_0_70591500_1399363383']['goods_name'] == '猫爪蛋糕'): ?><span style="font-size:11px;"><?php echo $this->_var['goods_0_70591500_1399363383']['goods_name']; ?>+花环</span><?php else: ?><?php echo $this->_var['goods_0_70591500_1399363383']['goods_name']; ?><?php endif; ?></td>
          <td width="9%" align="center"><?php if ($this->_var['goods_0_70591500_1399363383']['goods_name'] == '猫爪蛋糕'): ?>1  套<?php else: ?><?php echo $this->_var['goods_0_70591500_1399363383']['goods_attr']; ?><?php endif; ?></td>
           <td width="8%" align="center"><?php echo $this->_var['goods_0_70591500_1399363383']['goods_number']; ?></td>
           <td width="8%" align="center"><?php if ($this->_var['goods_0_70591500_1399363383']['goods_sn'] == 'D3'): ?>套<?php else: ?>个<?php endif; ?></td>
           <td width="13%" align="center"><?php echo $this->_var['goods_0_70591500_1399363383']['goods_price']; ?></td>
           <td width="10%" align="center"><?php echo $this->_var['goods_0_70591500_1399363383']['goods_discount']; ?></td>
           <td width="41%"><?php echo $this->_var['goods_0_70591500_1399363383']['goods_sub']; ?></td>
     </tr>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	<tr><td colspan="7"></td></tr>
 </table>

</div>

<div class="con4" >
<table width="700" height="90" border="0">
   <tr>
     <td>
     <table width="210" height="90" style="float:left;" border="0">
	   <!--<tr height="5">
			  <td colspan="3">&nbsp;</td>
		</tr>-->
      <tr height="40">
      <td></td>
	  <td align="center"><?php echo $this->_var['goods']['canju']; ?></td>
	  <td align="center"><?php echo $this->_var['goods']['canju_sum']; ?></td>
     </tr>
     <tr height="40">
      <td></td>
      <td align="center"><?php echo $this->_var['goods']['candle']; ?></td>
      <td align="center"><?php echo $this->_var['goods']['candle_sum']; ?></td>
     </tr>
     <!--<tr>
	   <td></td>
	  <td width="60">&nbsp;</td>
	  <td width="60">&nbsp;</td>
      </tr>-->
		</table>
	 </td>
	 <td>
		<table width="590" height="90" border="0">
		  <!-- <tr height="5">
			  <td colspan="5">&nbsp;</td>
		   </tr>-->
		   <tr height="40">
		     <!-- <td width="35"></td>-->
			  <td width="110" align="center"><?php echo $this->_var['order']['shipping_fee']; ?></td>
			 <td width="85" align="center"><?php echo $this->_var['order']['card_name']; ?> </td>
			 <?php if ($this->_var['order']['card_name'] == '中文'): ?>
			  <td width="170" align="left">生日快乐</td>
			  <?php elseif ($this->_var['order']['card_name'] == '英文'): ?>
			  <td width="170" align="left">Happy Birthday</td>
			  <?php else: ?>
			 <td width="170" align="left"><?php echo $this->_var['order']['card_message']; ?> </td>
			 <?php endif; ?>
			  <td></td>
				</tr>         
       <tr height="40">
	   <td width="110" align="center"> <?php echo $this->_var['order']['pay_fee']; ?></td>
	  <td width="90">&nbsp;</td>
	  <td width="170">&nbsp;</td>
      </tr>
			</table>
	   </td>
	</tr>
</table>
<table width="700" height="40" border="0">
  <tr>
      <td colspan="7" height="20">&nbsp;</td>
  </tr>
   <tr>
      <td width="40"></td>

      <td height='20' width="60"><?php echo $this->_var['order']['goods_amount']; ?></td>
      <td width="60" align="center"><?php echo $this->_var['order']['fj_fee']; ?></td>
      <td width="60" align="center"><?php echo $this->_var['order']['fw_fee']; ?></td>
      <td width="50" align="center"><?php echo $this->_var['pay']['paid']; ?></td>
      <td width="65"><?php echo $this->_var['pay']['payname']; ?><?php if ($this->_var['pay'] [ card_no ]): ?>(<?php echo $this->_var['pay']['card_no']; ?>)<?php endif; ?></td>
      <td width="65" align="left"><?php echo $this->_var['pay']['unpaid']; ?> </td>
	  <td width="80">&nbsp;</td>
	  <td width="80">&nbsp;</td> 
  </tr>
  <tr>
    <td width="80"></td>
	<td colspan="7" height="20"></td>
  </tr>
 </table>
 <table width="700" height="25" border="0">
  <tr>
      <td width="55"></td>
      <td height='20' width="10"></td>
      <td width="100"><?php echo $this->_var['order']['inv_payee']; ?></td>
      <td width="1"></td>
      <td width="40"><?php echo $this->_var['order']['inv_content']; ?></td>
      <td width="50"></td>
      <td width="60"><?php echo $this->_var['order']['wsts']; ?></td>
	  <td width="60">&nbsp;</td>
	  <td width="60">&nbsp;</td>	 
 </tr>
</table>
</div>


</div>


