<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>廿一客ERP</title>
</head>
<style type="text/css">
body{
	background:#fffdde;
}
#container{
	width:955px;
	margin:auto;
}

.ziti1{
	font:Arial, Helvetica, sans-serif;
	font-size:14px;
	color:#491903;
}
.ziti2{
	font:Arial, Helvetica, sans-serif;
	font-size:14px;
	color:#491903;
	font-weight:bold;
}
.ziti3{
	font:Arial, Helvetica, sans-serif;
	font-size:16px;
	color:#491903;
	font-weight:bold;
}
</style>

<body>

<div id="container">
	<h3 style="padding:padding:0px;margin:5px;">欢迎使用，亲！</h3>
    
    <div>
   	 <table width="100%" bgcolor="#491903" cellpadding="5" cellspacing="1" border="0">
      <tr bgcolor="#999999">
        <th colspan="4" align="left" bgcolor="#faf7cb" class="ziti3">基本信息</th>
      </tr>
      <tr bgcolor="#999999">
        <td width="15%" align="right" bgcolor="#fffdde" class="ziti2"><strong>客服工号</strong></td>
        <td width="34%" bgcolor="#fffdde" class="ziti1"><?php if ($this->_var['arr']['remark'] == '9999'): ?>web<?php else: ?><?php echo $this->_var['arr']['remark']; ?><?php endif; ?></td>
        <td width="15%" align="right" bgcolor="#fffdde" class="ziti2"><strong>订单号</strong></td>
        <td bgcolor="#fffdde" class="ziti1"><?php echo $this->_var['arr']['order_sn']; ?></td>
      </tr>
      <tr bgcolor="#999999">
        <td align="right" bgcolor="#fffdde" class="ziti2"><strong>订货人</strong></td>
        <td bgcolor="#fffdde" class="ziti1"><?php echo $this->_var['arr']['orderman']; ?></td>
        <td align="right" bgcolor="#fffdde" class="ziti2"><strong>本单K金</strong></td>
        <td bgcolor="#fffdde" class="ziti1"><?php echo $this->_var['arr']['integral_money']; ?></td>
      </tr>
      <tr bgcolor="#999999">
        <td align="right" bgcolor="#fffdde" class="ziti2"><strong>支付方式</strong></td>
        <td bgcolor="#fffdde" class="ziti1"><?php echo $this->_var['arr']['pay_name']; ?>(<?php echo $this->_var['arr']['pay_note']; ?>)</td>
        <td align="right" bgcolor="#fffdde" class="ziti2"><strong>订单状态</strong></td>
        <td bgcolor="#fffdde" class="ziti1"><?php if ($this->_var['arr']['order_status'] == '0'): ?>未确认<?php elseif ($this->_var['arr']['order_status'] == '1'): ?>确认<?php elseif ($this->_var['arr']['order_status'] == '2'): ?>取消<?php elseif ($this->_var['arr']['order_status'] == '3'): ?>无效<?php elseif ($this->_var['arr']['order_status'] == '4'): ?>退订<?php endif; ?>,
        <?php if ($this->_var['arr']['pay_status'] == '0'): ?>
                	未付款
                <?php elseif ($this->_var['arr']['pay_status'] == '1'): ?>
                	付款中
                <?php elseif ($this->_var['arr']['pay_status'] == '2'): ?>
                	已付款
                <?php endif; ?>,
        <?php if ($this->_var['arr']['sta2'] == '1'): ?>待配送<?php elseif ($this->_var['arr']['sta2'] == '2'): ?>配送中<?php elseif ($this->_var['arr']['sta2'] == '3'): ?>已完成<?php else: ?>无效<?php endif; ?></td>
      </tr>
      <tr bgcolor="#999999">
        <td align="right" bgcolor="#fffdde" class="ziti2"><strong>收货时间</strong></td>
        <td bgcolor="#fffdde" class="ziti1"><?php echo $this->_var['arr']['best_time']; ?></td>
        <td align="right" bgcolor="#fffdde" class="ziti2"><strong>下单时间</strong></td>
        <td bgcolor="#fffdde" class="ziti1"><?php echo date("Y-m-d H:i:s",$this->_var['arr']['time1']); ?></td>
      </tr>
      <tr bgcolor="#999999">
        <td align="right" bgcolor="#fffdde" class="ziti2"><strong>收货人</strong></td>
        <td colspan="3" bgcolor="#fffdde" class="ziti1"><?php echo $this->_var['arr']['consignee']; ?></td>
       </tr>
      <tr bgcolor="#999999">
        <td align="right" bgcolor="#fffdde" class="ziti2"><strong>送货地址</strong></td>
        <td colspan="3" bgcolor="#fffdde" class="ziti1"><?php echo $this->_var['arr']['address']; ?></td>
       </tr>
      <tr bgcolor="#999999">
        <td align="right" bgcolor="#fffdde" class="ziti2"><strong>收款地址</strong></td>
        <td colspan="3" bgcolor="#fffdde" class="ziti1"><?php echo $this->_var['arr']['money_address']; ?></td>
       </tr>
      <tr bgcolor="#999999">
        <td align="right" bgcolor="#fffdde" class="ziti2"><strong>收货人手机</strong></td>
        <td bgcolor="#fffdde" class="ziti1"><?php echo $this->_var['arr']['mobile']; ?></td>
        <td align="right" bgcolor="#fffdde" class="ziti2"><strong>收货人电话</strong></td>
        <td bgcolor="#fffdde" class="ziti1"><?php echo $this->_var['arr']['tel']; ?></td>
      </tr>
      <tr bgcolor="#999999">
        <td align="right" bgcolor="#fffdde" class="ziti2"><strong>送货时间</strong></td>
        <td bgcolor="#fffdde" class="ziti1"colspan="3"><?php echo $this->_var['arr']['best_time']; ?></td>
      </tr>
    </table>
    <div class="list-div" style="margin-bottom: 5px">
    <table width="100%" bgcolor="#491903" cellpadding="5" cellspacing="1" border="0">
      <tr bgcolor="#faf7cb">
        <th colspan="7" align="left" class="ziti3" scope="col">商品信息</th>
      </tr>
      <tr>
        <td width="15%" align="center" bgcolor="#fffdde"><span class="ziti1">商品名称</span></td>
        <td width="15%" align="center" bgcolor="#fffdde"><span class="ziti1">goods_sn</span></td>
        <td width="15%" align="center" bgcolor="#fffdde"><span class="ziti1"><span class="ziti11">规格</span></span></td>
        <td width="15%" align="center" bgcolor="#fffdde"><span class="ziti1">数量</span></td>
        <td width="10%" align="center" bgcolor="#fffdde"><span class="ziti1">单价</span></td>
        <td width="10%" align="center" bgcolor="#fffdde"><span class="ziti1">折扣</span></td>
        <td bgcolor="#fffdde"><span class="ziti1">总价</span></td>
	  	  </tr>
	  	  <?php $_from = $this->_var['arr2']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'v');if (count($_from)):
    foreach ($_from AS $this->_var['v']):
?>
          <tr>
        <td  bgcolor="#fffdde" align="right"><span class="ziti1"><?php echo $this->_var['v']['goods_name']; ?></span></td>
        <td bgcolor="#fffdde" align="center"><span class="ziti1"><?php echo $this->_var['v']['goods_sn']; ?></span></td>
        <td bgcolor="#fffdde" align="center"><span class="ziti1"><span class="ziti11"><?php echo $this->_var['v']['goods_attr']; ?></span></td>
        <td bgcolor="#fffdde" align="center"><span class="ziti1"><?php echo $this->_var['v']['goods_number']; ?></span></td>
        <td bgcolor="#fffdde" align="center"><span class="ziti1"><?php echo $this->_var['v']['goods_price']; ?></span></td>
        <td bgcolor="#fffdde" align="center"><span class="ziti1"><?php echo $this->_var['v']['goods_discount']; ?></span></td>
        <td bgcolor="#fffdde"><span class="ziti1"><?php echo $this->_var['v']['j']; ?>元<?php if ($this->_var['v']['goods_discount'] == '-1'): ?>(k金兑换)<?php endif; ?></span></td>
	  	  </tr>
           <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
          <tr>
            <td bgcolor="#fffdde" colspan="6" align="right"><span class="ziti1"><strong>合计</strong></span></td>
            <td  bgcolor="#fffdde"><span class="ziti1"><strong><?php echo $this->_var['heji']; ?>元</strong></span></td>
      </tr>
    </table>
    
     
    <table width="100%" bgcolor="#491903" cellpadding="5" cellspacing="1" border="0">

      <tr>
        <td width="15%" align="right" bgcolor="#fffdde" class="ziti2"><strong>送货人</strong></td>
        <td width="35%" bgcolor="#fffdde" class="ziti1"><?php echo $this->_var['arr']['name']; ?></td>
        <td width="21%" align="right" bgcolor="#fffdde" class="ziti2"><strong>外送提示</strong></td>
        <td width="27%" bgcolor="#fffdde" class="ziti1"><?php echo $this->_var['arr']['wsts']; ?></td>
      </tr>
      <tr>
        <td width="15%" align="right" bgcolor="#fffdde" class="ziti2"><strong>生产提示</strong></td>
        <td width="35%" bgcolor="#fffdde" class="ziti1"><?php echo $this->_var['arr']['scts']; ?></td>
        <td width="21%" align="right" bgcolor="#fffdde" class="ziti2"></td>
        <td width="27%" bgcolor="#fffdde" class="ziti1"> </td>
      </tr>
      <tr>
        <td width="15%" align="right" bgcolor="#fffdde" class="ziti2"><strong>发票内容</strong></td>
        <td colspan="3" bgcolor="#fffdde" class="ziti1"><?php echo $this->_var['arr']['inv_content']; ?></td>
      </tr>
      <tr>
        <td align="right" bgcolor="#fffdde" class="ziti2"><strong>发票抬头</strong></td>
        <td colspan="3" bgcolor="#fffdde" class="ziti1"><?php echo $this->_var['arr']['inv_payee']; ?></td>
      </tr>
      <tr>
        <td align="right" bgcolor="#fffdde" class="ziti2">客户留言</td>
        <td colspan="3" bgcolor="#fffdde" class="ziti1"><?php echo $this->_var['arr']['postscript']; ?></td>
      </tr>
      <tr>
        <td align="right" bgcolor="#fffdde" class="ziti2">客服备注</td>
        <td colspan="3" bgcolor="#fffdde" class="ziti1"><?php echo $this->_var['arr']['to_buyer']; ?><?php echo $this->_var['arr']['referer']; ?></td>
      </tr>
      <tr>
        <td align="right" bgcolor="#fffdde" class="ziti2">生日牌</td>
        <td bgcolor="#fffdde" class="ziti1"><?php echo $this->_var['arr']['card_name']; ?></td>
        <td bgcolor="#fffdde" class="ziti2" align="right">内容</td>
        <td bgcolor="#fffdde" class="ziti1"><?php echo $this->_var['arr']['card_message']; ?></td>
      </tr>
      <tr>
        <th align="left" colspan="4" bgcolor="#faf7cb" class="ziti3">费用信息</th>
      </tr>
      <tr>
        <td bgcolor="#fffdde" colspan="4"><span class="ziti2"><br>
          商品金额<strong><?php echo $this->_var['arr']['goods_amount']; ?></strong>
          + 配送费<strong><?php echo $this->_var['arr']['shipping_fee']; ?></strong>
          + 结款费<strong><?php echo $this->_var['arr']['pay_fee']; ?></strong>
          
          - 已付金额<?php echo $this->_var['arr']['money_paid']; ?>
          - 积分 <?php echo $this->_var['arr']['integral_money']; ?>
          - 代金卡金额 <?php echo $this->_var['arr']['bonus']; ?>
          + 餐具和蜡烛 <?php echo $this->_var['c_money']; ?>
        </span></td>
      <tr>
        <td bgcolor="#fffdde" colspan="4"><span class="ziti2"> = 订单金额<?php echo $this->_var['arr']['order_amount']; ?></span></td>
    </table>
</div>
    
</div>

</body>
</html>
