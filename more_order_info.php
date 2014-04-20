<?php
require(dirname(__FILE__) . '/includes/init.php');

$order_id=empty($_GET['order_id'])?"":$_GET['order_id'];

if(!empty($order_id)){

	$sql="SELECT i.order_sn,i.goods_amount,i.shipping_fee,i.pay_fee,i.money_paid,i.integral_money,i.bonus,i.order_amount,i.inv_payee,i.inv_content,i.postscript,i.money_address,i.scts,i.wsts,i.card_name,".
	"i.card_message,i.order_id, i.order_status, i.pay_status, i.shipping_status, i.orderman, i.consignee, i.mobile, i.tel, i.add_time as time1, ".
	"i.pay_name, i.pay_note, i.best_time, i.province, i.address, i.to_buyer, g.remark, dis.status as sta1, dis.route_id, dis.turn, ".
	"dis.add_time as time2, l.ptime, l.stime, l.print_sn,l.bdate, del.status as sta2, e.name, rou.route_code, s.station_name
	FROM ecs_order_info AS i
	LEFT JOIN order_genid AS g ON g.order_id = i.order_id
	LEFT JOIN order_dispatch AS dis ON dis.order_id = i.order_id
	LEFT JOIN print_log_x AS l ON l.order_id = dis.order_id
	LEFT JOIN order_delivery AS del ON del.order_id = i.order_id
	LEFT JOIN hr_employees AS e ON e.id=del.employee_id
	LEFT JOIN ship_route AS rou ON rou.route_id = dis.route_id
	LEFT JOIN ship_station AS s ON rou.station_id = s.station_id WHERE i.country ".db_create_in(array_keys($_SESSION['city_arr']))." AND i.order_id='".$order_id."' LIMIT 1";
	
	//基本信息
	$arr=$GLOBALS['db_read']->getRow($sql);

	//商品信息
	$sql="select goods_sn,goods_name,goods_attr,goods_number,goods_price,floor(goods_number*goods_price*goods_discount) as j,goods_discount from ecs_order_goods where order_id='".$order_id."'";
	$arr2=$GLOBALS['db_read']->getAll($sql);

	//合计
	$heji=0;
	foreach($arr2 as $k=>$v){
			$heji+=$v['j'];
	}
	
	
	$sql="select sum(goods_number*goods_price) as c_money from ecs_order_goods as goo where order_id='".$order_id."' and goods_id in (60,61)";
	$c_arr=$GLOBALS['db_read']->getOne($sql);
	
	$sql="select max(goods_number) as l_num from ecs_order_goods as goo where order_id='".$order_id."' and goods_id='61'";
	$l_arr=$GLOBALS['db_read']->getOne($sql);
	if($order_sn!=''){
		$datetime=$arr[0]['bdate'];
	}
	if(empty($arr)){
		$smarty->assign("no_message","无记录");
	}
	$smarty->assign("heji",$heji);
	$smarty->assign("order_sn",$order_sn);
	$smarty->assign("print_sn",$print_sn);
	$smarty->assign("datetime",$datetime);
	$smarty->assign("arr",$arr);
	$smarty->assign("c_money",$c_arr);
	$smarty->assign("l_num",$l_arr);
	$smarty->assign("arr2",$arr2);
	$smarty->display("more_order_info.html");
		
}

?>