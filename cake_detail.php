<?php
/**
 * Cake Detail
 * @copyright Copyright (c) 2012, 21cake food co.ltd
 * @author 21Cake Dev Team
 */

require(dirname(__FILE__) . '/includes/init.php');
$_REQUEST['act'] = empty($_REQUEST['act']) ? 'list' : trim($_REQUEST['act']);

if ($_REQUEST['act'] == 'list') {
	$ids = trim($_REQUEST['id']);	
	$sql = "SELECT order_id,order_sn,scts,add_time,best_time FROM ecs_order_info WHERE order_id ".db_create_in($ids);
	$r=$db_read->getAll($sql);	
	$cake_list = array();
	foreach($r as $key=>$val){	  
		$cake_list[$key]['i'] = $key + 1;
		$cake_list[$key]['order_id'] = $val['order_id'];
		$cake_list[$key]['order_sn'] = $val['order_sn'];
		$cake_list[$key]['best_time'] = $val['best_time'];		
		$cake_list[$key]['done_time'] = date('Y-m-d H:i',strtotime($val['best_time'])-4*3600);
		$cake_list[$key]['add_time'] = date('Y-m-d H:i', $val['add_time']);
	}	
	$smarty->assign('ur_here', '蛋糕统计详情');
	$smarty->assign('full_page', 1);
	$smarty->assign('ids', $ids);
	$smarty->assign('cake_list', $cake_list);	
	$smarty->display('cake_detail.html');
} elseif ($_REQUEST['act'] == 'info') {
    $ids = trim($_REQUEST['id']);
	$order_id = intval($_REQUEST['id']);
	$order_info = get_order_info($order_id);
	$smarty->assign('order',  $order_info); 
    $smarty->display('order_info.html');
}elseif($_REQUEST['act'] == 'query'){
    ob_clean();
    $cake_list= cake_list();
	//print_r($cake_list);	
	$smarty->assign('cake_list', $cake_list['cake_list']);
	$smarty->assign('filter', $cake_list['filter']);
    
    make_json_result($smarty->fetch('cake_detail.html'), '', array('filter' => $cake_list['filter']));
}

function cake_list(){
    $filter['ids']    = empty($_REQUEST['ids']) ? '' : trim($_REQUEST['ids']);
    $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'best_time' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
    $sql = "SELECT order_id,order_sn,scts,add_time,best_time FROM ecs_order_info WHERE order_id ".db_create_in($filter['ids'])." ORDER by ". $filter['sort_by']. " ".$filter['sort_order'];
	$r=$GLOBALS['db_read']->getAll($sql);	
	$cake_list = array();
	foreach($r as $key=>$val){	  
		$cake_list[$key]['i'] = $key + 1;
		$cake_list[$key]['order_id'] = $val['order_id'];
		$cake_list[$key]['order_sn'] = $val['order_sn'];
		$cake_list[$key]['best_time'] = $val['best_time'];		
		$cake_list[$key]['done_time'] = date('Y-m-d H:i',strtotime($val['best_time'])-4*3600);
		$cake_list[$key]['add_time'] = date('Y-m-d H:i', $val['add_time']);
	}
	return array('cake_list' => $cake_list, 'filter' => $filter);
  
}
function get_order_info($order_id) {
	$status['i'][0] = '未确认';
	$status['i'][1] = '已确认';
	$status['i'][2] = '取消';
	$status['i'][3] = '无效';
	$status['i'][4] = '退货';
	
	$fields = "c.turn,c.add_time,c.status,e.route_name,d.ptime,d.stime,d.print_sn";
	$sql = "SELECT b.order_sn,b.add_time AS atime,b.best_time,b.kfgh,b.order_status,b.scts,b.card_name,b.card_message," . $fields . " 
		FROM order_genid AS a 
		LEFT JOIN ecs_order_info AS b ON b.order_id=a.order_id 
		LEFT JOIN order_dispatch AS c ON c.order_id=a.order_id 
		LEFT JOIN print_log_x AS d ON d.order_id=a.order_id 
		LEFT JOIN ship_route AS e ON e.route_id=c.route_id
		WHERE a.order_id='" . $order_id . "'";
	$order = $GLOBALS['db_read']->getRow($sql);
	
	$str = "";
	$query = "SELECT goods_id,goods_name,goods_attr,goods_number FROM ecs_order_goods WHERE order_id='" . $order_id . "'";
	$goods = $GLOBALS['db_read']->getAll($query);
	foreach ($goods as $val) {
		if ($val['goods_id'] == 60) { // 餐具套装
			$order['dinnerware'] += $val['goods_number'];
		} elseif ($val['goods_id'] == 61) { // 方形蜡烛
			$order['candle'] += $val['goods_number'];
		} else { // 蛋糕
			$str .= $val['goods_attr'] . '-' . $val['goods_name'] . $val['goods_number'] . '个';
		}
	}
	$order['goods'] = $str;
	$order['order_time'] = date('Y-m-d H:i:s', $order['atime']);
	$order['add_time'] = date('Y-m-d H:i:s', $order['add_time']);
	$order['status'] = $status['i'][$order['order_status']];
	return $order;
	
}