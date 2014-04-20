<?php
/**
 * 流水号查询
 * @author:anlong
 */
require(dirname(__FILE__) . '/includes/init.php');
admin_priv('s_num');
//获取城市编号,格式为:IN (441,443)
$city_code =db_create_in(array_keys($_SESSION['city_arr']));

if($_REQUEST['act']== 'query')
{
	$order_info = get_order_info();
	$smarty->assign("datetime",$_GET['datetime']);
	$smarty->assign("order_sn",$_GET['order_sn']);
	$smarty->assign("print_sn",$_GET['print_sn']);
	$smarty->assign("arr",$order_info);
	$smarty->assign('ur_here',     '流水号查询');
	$smarty->display("order_check2.html");
	exit;
}else{
	
	$smarty->assign("datetime",date('Y-m-d'));
	$smarty->assign('ur_here',     '流水号查询');
	$smarty->display("order_check2.html");
	exit;
}

function get_order_info(){
	$datetime=empty($_GET['datetime'])?date("Y-m-d"):$_GET['datetime'];
	$order_sn=empty($_GET['order_sn'])?"":trim($_GET['order_sn']);
	$print_sn=empty($_GET['print_sn'])?"":trim($_GET['print_sn']);
	$where="where i.country ".$GLOBALS['city_code'];
	
	if($datetime){
		$where.=" and l.bdate='".$datetime."'";
	}
	if($print_sn){
		$where.=" and l.print_sn='".$print_sn."' ";
	}
	if($order_sn){
		if(str_len($order_sn) ==5){
			$where .= " and i.order_sn like '%$order_sn' ";
		}else{
			$where ="where i.order_sn='".$order_sn."'";
		}
		
	}
	$sql="SELECT i.order_sn,i.order_id, i.best_time, l.ptime, l.stime, l.print_sn,bdate  
	FROM ecs_order_info AS i
	LEFT JOIN order_genid AS g ON g.order_id = i.order_id
	LEFT JOIN order_dispatch AS dis ON dis.order_id = i.order_id
	LEFT JOIN print_log_x AS l ON l.order_id = dis.order_id 
	LEFT JOIN order_delivery AS del ON del.order_id = i.order_id
	LEFT JOIN ship_route AS rou ON rou.route_id = dis.route_id
	LEFT JOIN ship_station AS s ON rou.station_id = s.station_id ".
	$where." limit 1";
	
	$arr=$GLOBALS['db_read']->getRow($sql);

	return $arr;
}
?>