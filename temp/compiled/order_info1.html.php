<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>廿一客ERP</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<meta name="Generator" content="廿一客" />
<meta name="Keywords" content="廿一客,蛋糕,免费品尝" />
<?php echo $this->smarty_insert_scripts(array('files'=>'transport.js,utils.js,listtable.js')); ?>
</head>
<body>
<div id="topbar">
<div class="list-div" style="margin-bottom: 5px">
<table width="100%" cellpadding="3" cellspacing="1">
  <tr>
    <th colspan="4">基本信息</th>
  </tr>
  <tr>
    <td width="18%"><div align="right"><strong>客服工号：</strong></div></td>
    <td width="34%"><?php echo $this->_var['order']['kfgh']; ?></td>
    <td width="15%"><div align="right"><strong>本单K金：</strong></div></td>
    <td><?php echo $this->_var['order']['give_integral']; ?></td>
  </tr>
  <tr>
    <td width="18%"><div align="right"><strong>订单号：</strong></div></td>
    <td width="34%"><?php echo $this->_var['order']['order_sn']; ?></td>
    <td width="15%"><div align="right"><strong>订单状态：</strong></div></td>
    <td><?php echo $this->_var['order']['status']; ?></td>
  </tr>
  <tr>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_user_name']; ?></strong></div></td>
    <td>
    
    <?php echo empty($this->_var['order']['user_name']) ? $this->_var['lang']['anonymous'] : $this->_var['order']['user_name']; ?>
    <?php if ($this->_var['order']['orderman']): ?>
    	<?php echo empty($this->_var['order']['orderman']) ? $this->_var['order']['ordertel'] : $this->_var['order']['orderman']; ?>
    <?php else: ?>
    	<?php echo empty($this->_var['order']['rea_name']) ? $this->_var['order']['user_name'] : $this->_var['order']['rea_name']; ?>
    <?php endif; ?>
<!--    <?php if ($this->_var['order']['rea_name']): ?>
    	<?php echo empty($this->_var['order']['rea_name']) ? $this->_var['order']['user_name'] : $this->_var['order']['rea_name']; ?>
    <?php else: ?>
    	<?php echo empty($this->_var['order']['mobile_phone']) ? $this->_var['order']['user_name'] : $this->_var['order']['mobile_phone']; ?>
    <?php endif; ?>-->
 </td>
    <td><div align="right"><strong>下单时间：</strong></div></td>
    <td><?php echo $this->_var['order']['order_time']; ?></td>
  </tr>
  <tr>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_payment']; ?></strong></div></td>
    <td><?php echo $this->_var['paystr']; ?></td>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_pay_time']; ?></strong></div></td>
    <td><?php echo $this->_var['order']['pay_time']; ?></td>
  </tr>
  <tr>
    <td><div align="right"><strong>配送信息</strong></div></td>
    <td><?php if ($this->_var['exist_real_goods']): ?><?php if ($this->_var['order']['shipping_id'] > 0): ?><?php echo $this->_var['order']['shipping_name']; ?><?php else: ?><?php echo $this->_var['lang']['require_field']; ?><?php endif; ?> <?php if ($this->_var['order']['insure_fee'] > 0): ?>（<?php echo $this->_var['lang']['label_insure_fee']; ?><?php echo $this->_var['order']['formated_insure_fee']; ?>）<?php endif; ?><?php endif; ?></td>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_shipping_time']; ?></strong></div></td>
    <td><?php echo $this->_var['order']['shipping_time']; ?></td>
  </tr>
  <tr>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_invoice_no']; ?></strong></div></td>
    <td><?php if ($this->_var['order']['shipping_id'] > 0 && $this->_var['order']['shipping_status'] > 0): ?><span onclick="listTable.edit(this, 'edit_invoice_no', <?php echo $this->_var['order']['order_id']; ?>)"><?php if ($this->_var['order']['invoice_no']): ?><?php echo $this->_var['order']['invoice_no']; ?><?php else: ?>N/A<?php endif; ?></span><?php endif; ?></td>
    <td><div align="right"><strong><?php echo $this->_var['lang']['from_order']; ?></strong></div></td>
    <td><?php echo $this->_var['order']['referer']; ?></td>
  </tr>
  <tr>
    <th colspan="4"><?php echo $this->_var['lang']['consignee_info']; ?></th>
    </tr>
  <tr>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_consignee']; ?></strong></div></td>
    <td><?php echo htmlspecialchars($this->_var['order']['consignee']); ?></td>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_email']; ?></strong></div></td>
    <td><?php echo $this->_var['order']['email']; ?></td>
  </tr>
  <tr>
    <td><div align="right"><strong>送货地址:</strong></div></td>
    <td>[<?php echo $this->_var['order']['region']; ?>] <?php echo htmlspecialchars($this->_var['order']['address']); ?></td>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_zipcode']; ?></strong></div></td>
    <td><?php echo htmlspecialchars($this->_var['order']['zipcode']); ?></td>
  </tr>
  <tr>
    <td><div align="right"><strong>收款地址:</strong></div></td>
    <td><?php echo htmlspecialchars($this->_var['order']['money_address']); ?></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td><div align="right"><strong>收货人电话：</strong></div></td>
    <td><?php echo $this->_var['order']['tel']; ?></td>
    <td><div align="right"><strong>收货人手机：</strong></div></td>
    <td><?php echo htmlspecialchars($this->_var['order']['mobile']); ?></td>
  </tr>
  <tr>
    <td><div align="right"><strong>标志建筑：</strong></div></td>
    <td><?php echo htmlspecialchars($this->_var['order']['sign_building']); ?></td>
    <td><div align="right"><strong>送货时间：</strong></div></td>
    <td><?php echo htmlspecialchars($this->_var['order']['best_time']); ?></td>
  </tr>
</table>
<div class="list-div" style="margin-bottom: 5px">
<table width="100%" cellpadding="3" cellspacing="1">
  <tr>
    <th colspan="7" scope="col">商品信息</th>
    </tr>
  <tr>
    <td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['goods_name_brand']; ?></strong></div></td>
    <td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['goods_sn']; ?></strong></div></td>
    <td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['goods_price']; ?></strong></div></td>
    <td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['goods_number']; ?></strong></div></td>
    <td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['goods_attr']; ?></strong></div></td>
    <td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['storage']; ?></strong></div></td>
    <td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['subtotal']; ?></strong></div></td>
  </tr>
  <?php $_from = $this->_var['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
  <?php if ($this->_var['goods']['is_integral'] == 1): ?>
  <tr>
    <td><?php echo $this->_var['goods']['goods_name']; ?> <?php if ($this->_var['goods']['brand_name']): ?>[ <?php echo $this->_var['goods']['brand_name']; ?> ]<?php endif; ?>
    <?php if ($this->_var['goods']['is_gift']): ?><?php if ($this->_var['goods']['goods_price'] > 0): ?><?php echo $this->_var['lang']['remark_favourable']; ?><?php else: ?><?php echo $this->_var['lang']['remark_gift']; ?><?php endif; ?><?php endif; ?>
    <?php if ($this->_var['goods']['parent_id'] > 0): ?><?php echo $this->_var['lang']['remark_fittings']; ?><?php endif; ?></td>
    <td><?php echo $this->_var['goods']['goods_sn']; ?></td>
    <td><div align="right"><?php if ($this->_var['goods']['cat_id'] <> 4): ?><?php echo $this->_var['goods']['integral']; ?><?php else: ?><?php echo $this->_var['goods']['goods_price']; ?><?php endif; ?>K金</div></td>
    <td><div align="right"><?php echo $this->_var['goods']['goods_number']; ?>
    </div></td>
    <td><?php echo nl2br($this->_var['goods']['goods_attr']); ?></td>
    <td><div align="right"><?php echo $this->_var['goods']['storage']; ?></div></td>
    <td><div align="right"><?php echo $this->_var['goods']['kjtotal']; ?>K金</div></td>
  </tr>
  <?php else: ?>
  <tr>
    <td><?php echo $this->_var['goods']['goods_name']; ?> <?php if ($this->_var['goods']['brand_name']): ?>[ <?php echo $this->_var['goods']['brand_name']; ?> ]<?php endif; ?>
    <?php if ($this->_var['goods']['is_gift']): ?><?php if ($this->_var['goods']['goods_price'] > 0): ?><?php echo $this->_var['lang']['remark_favourable']; ?><?php else: ?><?php echo $this->_var['lang']['remark_gift']; ?><?php endif; ?><?php endif; ?>
    <?php if ($this->_var['goods']['parent_id'] > 0): ?><?php echo $this->_var['lang']['remark_fittings']; ?><?php endif; ?></td>
    <td><?php echo $this->_var['goods']['goods_sn']; ?></td>
    <td><div align="right"><?php echo $this->_var['goods']['formated_goods_price']; ?></div></td>
    <td><div align="right"><?php echo $this->_var['goods']['goods_number']; ?>
    </div></td>
    <td><?php echo nl2br($this->_var['goods']['goods_attr']); ?></td>
    <td><div align="right"><?php echo $this->_var['goods']['storage']; ?></div></td>
    <td><div align="right"><?php echo $this->_var['goods']['formated_subtotal_new']; ?>元</div></td>
  </tr>
  <?php endif; ?>
  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
  <tr>
    <td></td>
    <td>&nbsp;</td>
    <td><?php if ($this->_var['order']['total_weight']): ?><div align="right"><strong><?php echo $this->_var['lang']['label_total_weight']; ?>
    </strong></div><?php endif; ?></td>
    <td><?php if ($this->_var['order']['total_weight']): ?><div align="right"><?php echo $this->_var['order']['total_weight']; ?>
    </div><?php endif; ?></td>
    <td>&nbsp;</td>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_total']; ?></strong></div></td>
    <td><div align="right"><?php echo $this->_var['order']['formated_goods']; ?><?php if ($this->_var['order']['integral'] > 0): ?> 含&nbsp;<?php echo $this->_var['order']['integral']; ?>K金<?php endif; ?></div></td>
  </tr>
</table>
</div>

<table width="100%" cellpadding="3" cellspacing="1">
  <tr>
    <th colspan="4"><?php echo $this->_var['lang']['other_info']; ?></th>
  </tr>
  <tr>
    <td width="17%"><div align="right"><strong>积分卡办理:</strong></div></td>
    <td width="35%">
    	<?php if ($this->_var['order']['ispointcard'] == '1'): ?>办理<?php else: ?>不办理<?php endif; ?>
    </td>
    <td width="21%"><div align="right"><strong>送货人:</strong></div></td>
    <td width="27%"><?php echo $this->_var['order']['sender']; ?></td>
  </tr>
  <tr>
    <td width="17%"><div align="right"><strong>生产提示:</strong></div></td>
    <td width="35%"><?php echo $this->_var['order']['scts']; ?></td>
    <td width="21%"><div align="right"><strong>外送提示:</strong></div></td>
    <td width="27%"><?php echo $this->_var['order']['wsts']; ?></td>
  </tr>
  <tr>
    <td width="17%"><div align="right"><strong>发票类型：</strong></div></td>
    <td width="35%"><?php echo $this->_var['order']['inv_type']; ?></td>
    <td width="21%">&nbsp;</td>
    <td width="27%">&nbsp;</td>
  </tr>
  <tr>
    <td><div align="right"><strong>发票抬头：</strong></div></td>
    <td><?php echo $this->_var['order']['inv_payee']; ?></td>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_inv_content']; ?></strong></div></td>
    <td><?php echo $this->_var['order']['inv_content']; ?></td>
  </tr>
  <tr>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_postscript']; ?></strong></div></td>
    <td colspan="3"><?php echo $this->_var['order']['postscript']; ?></td>
  </tr>
  <tr>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_how_oos']; ?></strong></div></td>
    <td><?php echo $this->_var['order']['how_oos']; ?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_pack']; ?></strong></div></td>
    <td><?php echo $this->_var['order']['pack_name']; ?></td>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_card']; ?></strong></div></td>
    <td><?php echo $this->_var['order']['card_name']; ?></td>
  </tr>
  <tr>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_card_message']; ?></strong></div></td>
    <td colspan="3"><?php echo $this->_var['order']['card_message']; ?></td>
  </tr>
  <tr>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_to_buyer']; ?></strong></div></td>
    <td colspan="3"><?php echo $this->_var['order']['to_buyer']; ?></td>
  </tr>
</table>
</div>

</body>
</html>
