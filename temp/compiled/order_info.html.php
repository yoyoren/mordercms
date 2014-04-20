<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>订单详情</title>
</head>
<body>
<div id="topbar">
<div class="list-div" style="margin-bottom: 5px">
<table width="100%" cellpadding="3" cellspacing="1" border="1">
  <tr>
    <th colspan="1">基本信息</th>
  </tr>
  <tr>
    <td><div align="right"><strong>客服工号：</strong></div></td>
    <td><?php echo empty($this->_var['order']['kfgh']) ? '网单' : $this->_var['order']['kfgh']; ?></td>
    <td><div align="right"><strong>订单号：</strong></div></td>
    <td><?php echo $this->_var['order']['order_sn']; ?></td>
    <td><div align="right"><strong>下单时间</strong></div></td>
    <td><?php echo $this->_var['order']['order_time']; ?></td>
    <td><div align="right"><strong>送货时间</strong></div></td>
    <td><?php echo $this->_var['order']['best_time']; ?></td>
    <td><div align="right">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="right"><strong>订单状态：</strong></div></td>
    <td><?php echo $this->_var['order']['status']; ?></td>
    <td><div align="right"><strong>配送路区：</strong></div></td>
    <td><?php echo $this->_var['order']['route_name']; ?></td>
    <td><div align="right"><strong>订单批次</strong></div></td>
    <td>第<?php echo $this->_var['order']['turn']; ?>批</td>
    <td><div align="right"><strong>流水号</strong></div></td>
    <td><?php echo $this->_var['order']['print_sn']; ?></td>
    <td><div align="right"><strong>&nbsp;</strong></div></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="right"><strong>调度时间：</strong></div></td>
    <td><?php echo $this->_var['order']['add_time']; ?></td>
    <td><div align="right"><strong>打印时间：</strong></div></td>
    <td><?php echo $this->_var['order']['ptime']; ?></td>
    <td><div align="right"><strong>生产时间</strong></div></td>
    <td><?php echo $this->_var['order']['stime']; ?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="right"><strong>商品信息：</strong></div></td>
    <td colspan="9"><?php echo $this->_var['order']['goods']; ?></td>
  </tr>
  <tr>
    <td><div align="right"><strong>餐具数目：</strong></div></td>
    <td><?php echo $this->_var['order']['canju']; ?></td>
    <td><div align="right"><strong>蜡烛数目：</strong></div></td>
    <td><?php echo $this->_var['order']['candle']; ?></td>
    <td><div align="right"><strong>生日牌</strong></div></td>
    <td colspan="2"><?php echo $this->_var['order']['card_name']; ?></td>
    <td><div align="right"><strong>内容</strong></div></td>
    <td colspan="2"><?php echo $this->_var['order']['card_message']; ?></td>
  </tr>
  <tr>
    <td><div align="right"><strong>生产提示：</strong></div></td>
    <td colspan="9"><?php echo $this->_var['order']['scts']; ?></td>
  </tr>
</table>
</div>
</body>
</html>
