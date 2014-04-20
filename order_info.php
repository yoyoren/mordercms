<?php
//require(dirname(__FILE__) . '/includes/init4.php');
require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
require_once(ROOT_PATH . 'includes/lib_common.php');

$id=intval($_REQUEST['oid']);

$total_fee = " (goods_amount - discount + tax + shipping_fee + insure_fee + pay_fee + pack_fee + card_fee) AS total_fee ";

$sql = "SELECT *, " . $total_fee . "  FROM ecs_order_info WHERE order_id = '$id'";
$order = $db_read->getRow($sql);


$user = $GLOBALS['db_read']->getRow("SELECT * FROM ecs_users WHERE user_id = '".$order['user_id']."'");
	
$_LANG['os'][OS_UNCONFIRMED] = '未确认';
$_LANG['os'][OS_CONFIRMED] = '已确认';
$_LANG['os'][OS_CANCELED] = '取消';
$_LANG['os'][OS_INVALID] = '无效';
$_LANG['os'][OS_RETURNED] = '退货';

$_LANG['ss'][SS_UNSHIPPED] = '未发货';
$_LANG['ss'][SS_PREPARING] = '配货中';
$_LANG['ss'][SS_SHIPPED] = '已发货';
$_LANG['ss'][SS_RECEIVED] = '收货确认';

$_LANG['ps'][PS_UNPAYED] = '未付款';
$_LANG['ps'][PS_PAYING] = '付款中';
$_LANG['ps'][PS_PAYED] = '已付款';

$order['order_time']    = date($_CFG['time_format'], $order['add_time']);
$order['pay_time']      = $order['pay_time'] > 0 ? date($_CFG['time_format'], $order['pay_time']) : $_LANG['ps'][PS_UNPAYED];
$order['shipping_time'] = $order['shipping_time'] > 0 ?local_date($_CFG['time_format'], $order['shipping_time']) : $_LANG['ss'][SS_UNSHIPPED];
$order['status']        = $_LANG['os'][$order['order_status']] . ',' . $_LANG['ps'][$order['pay_status']] . ',' . $_LANG['ss'][$order['shipping_status']];
$order['invoice_no']    = $order['shipping_status'] == SS_UNSHIPPED || $order['shipping_status'] == SS_PREPARING ? $_LANG['ss'][SS_UNSHIPPED] : $order['invoice_no'];
if(strpos($order['pay_note'],'代金卡'))
{
     $order['pay_name'] ='代金卡';
}
else if(strpos($order['pay_note'],'销售'))
{
   $order['pay_name'] = '销售活动';
}	

$goods_list = array();
$sql = "SELECT o.*, g.goods_number AS storage, g.integral, g.cat_id,o.goods_attr,o.goods_discount " .
        "FROM ecs_order_goods AS o ".
        "LEFT JOIN ecs_goods AS g ON o.goods_id = g.goods_id " .
       "WHERE o.order_id = '$order[order_id]' ";
$res = $db_read->query($sql);
while ($row = $db_read->fetchRow($res))
{
   if($row['is_integral']==1)
   {
		   if($row['cat_id'] != 4)
		   {
		      $row['kjtotal']   = $row['integral'] * $row['goods_number'];
		   }
		   else
		   {
		      $row['kjtotal']   = $row['goods_price'] * $row['goods_number'];
		   }
		   $row['formated_subtotal']       = $row['goods_price'] * $row['goods_number'];
   }
   else
   {
             if($row['goods_discount']<=1)
			 {
			 $row['formated_subtotal_new']       = floor($row['goods_price'] * $row['goods_number']*$row['goods_discount']);
			 }
			 else
			 {
			 $row['formated_subtotal_new']       = floor($row['goods_price'] * $row['goods_number']-$row['goods_discount']);
			 }
			 $row['formated_subtotal']       = $row['goods_price'] * $row['goods_number'];
             $row['formated_goods_price']    = price_format($row['goods_price']);
   }
		
   $goods_list[] = $row;
}
 //echo "<pre>"; print_r($order);echo "</pre>";exit;
$smarty->assign('goods_list', $goods_list);	
$smarty->assign('order',	$order);
$smarty->assign('user',		$user);
$smarty->display('order_info.html');

?>
