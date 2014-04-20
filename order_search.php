<?php
/**
 * 查询订单详情
 * @Author:anlong
 */
require(dirname(__FILE__) . '/includes/init.php');
admin_priv("s_os");
//初始化城市编号
$city_code =db_create_in(array_keys($_SESSION['city_arr']));

if($_REQUEST['act'] == 'query')
{
	$order_info = get_order_info();

	$smarty->assign("order_sn",$_GET['order_sn']);
	$smarty->assign("print_sn",$_GET['print_sn']);
	$smarty->assign("datetime",$_GET['datetime']);
	$smarty->assign("arr",$order_info['order_info']);
	$smarty->assign("c_num",$order_info['cj']);
	$smarty->assign("l_num",$order_info['lz']);
	$smarty->assign("arr2",$order_info['goods_info']);
	$smarty->assign('ur_here',     '订单查询');
	$smarty->display("order_search.html");
	exit;
}
else
{

	$smarty->assign("datetime",date("Y-m-d"));
	$smarty->assign('ur_here',     '订单查询');
	$smarty->display("order_search.html");
	exit;
}
function get_order_info(){
	$datetime=$_GET['datetime'];
	$order_sn=trim($_GET['order_sn']);
	$print_sn=trim($_GET['print_sn']);
	$where="where i.country ".$GLOBALS['city_code'];

	if($datetime){
		$where.=" and l.bdate='".$datetime."'";
	}
	if($print_sn){
		$where.=" and l.print_sn='".$print_sn."'";
	}
	if($order_sn){
		if(str_len($order_sn) == 5){
			$where .= " and i.order_sn like '%$order_sn'";
		}else{
			$where="where i.country ".$GLOBALS['city_code']." and i.order_sn='".$order_sn."'";
		}
		
	}
	$sql="SELECT i.order_sn,i.inv_payee,i.inv_content,i.postscript,i.money_address,i.scts,i.wsts,i.card_message,i.order_id, i.order_status, i.pay_status, i.orderman, i.consignee, i.add_time as time1, i.pay_name, i.best_time, i.province, i.address, g.remark, dis.status as sta1, dis.route_id, dis.turn, dis.add_time as time2, l.ptime, l.stime, l.print_sn,l.bdate, del.status as sta2, rou.route_code, s.station_name
	FROM ecs_order_info AS i
	LEFT JOIN order_genid AS g ON g.order_id = i.order_id
	LEFT JOIN order_dispatch AS dis ON dis.order_id = i.order_id
	LEFT JOIN print_log_x AS l ON l.order_id = dis.order_id 
	LEFT JOIN order_delivery AS del ON del.order_id = i.order_id
	LEFT JOIN ship_route AS rou ON rou.route_id = dis.route_id
	LEFT JOIN ship_station AS s ON rou.station_id = s.station_id "
	.$where." limit 1";
	
	$arr=$GLOBALS['db_read']->getRow($sql);
	
	if($arr['order_id']){
		$sql="select goods_name,goods_attr,goods_number from ecs_order_goods as goo where order_id='".$arr['order_id']."' and goods_attr!=''";
		$arr2=$GLOBALS['db_read']->getAll($sql);
	}else{
		$arr2 =array();
	}
	
	foreach($arr2 as $k=>$v){
		$arr2[$k]['goods_name']=$v['goods_name']."--".$v['goods_attr']."--".$v['goods_number'];
		if($k!=0){
			$arr2[0]['goods_name'].=" | ".$arr2[$k]['goods_name'];
		}
		
	}
	
	
	$sql="select sum(goods_number) as c_num from ecs_order_goods as goo where order_id='".$arr['order_id']."' and goods_id='60'";
	$c_arr=$GLOBALS['db_read']->getOne($sql);
	
	$sql="select sum(goods_number) as l_num from ecs_order_goods as goo where order_id='".$arr['order_id']."' and goods_id='61'";
	$l_arr=$GLOBALS['db_read']->getOne($sql);

	return array("order_info"=>$arr,"goods_info"=>$arr2,"cj"=>$c_arr,"lz"=>$l_arr);
}
?>