<?php
require dirname(__FILE__) . '/includes/init.php';
$_REQUEST['act'] = empty($_REQUEST['act']) ? 'list' : trim($_REQUEST['act']);
$act = array('list', 'query');

if (in_array($_REQUEST['act'], $act)) {
	switch ($_REQUEST['act']) {
		case 'query':
			$temp_list = get_temp_list();
			$smarty->assign('temp_list', $temp_list['list']);
			$smarty->assign('filter', $temp_list['filter']);
			$smarty->assign('page_count', $temp_list['page_count']);
			$smarty->assign('record_count', $temp_list['record_count']);
			make_json_result($smarty->fetch('temp.html'), '', array(''));
			break;
		default:
			$temp_list = get_temp_list();
			$smarty->assign('ur_here', '站点蛋糕临时统计');
			$smarty->assign('full_page', 1);
			$smarty->assign('temp_list', $temp_list['list']);
			$smarty->assign('filter', $temp_list['filter']);
			$smarty->assign('page_count', $temp_list['page_count']);
			$smarty->assign('record_count', $temp_list['record_count']);
			$smarty->display('temp.html');
	}
} else {
	sys_msg('页面不存在！', 1, array(array('text' => '返回临时统计'), array('href' => 'temp.php?act=list')));
}

function get_temp_list() { // Obtaion to the temp list
	$result = get_filter();
	
	if ($result === false) {
		$filter = array();
		$where = "WHERE 1";
		$filter['bdate'] = empty($_REQUEST['bdate']) ? date('Y-m-d') : trim($_REQUEST['bdate']);
		
		if (!empty($_REQUEST['city'])) {
			$where .= " AND ";
		} else {
			$where .= " AND a.country" . db_create_in(array_keys($_SESSION['city_arr']));
		}
		$where .= " AND a.order_status=1 AND b.goods_price>100";
		
		if ($filter['bdate'])			$where .= " AND a.best_time BETWEEN '" . $filter['bdate'] . " 00:00:00' AND '" . $filter['bdate'] . " 23:59:59'";
		$query = "SELECT DISTINCT COUNT(*) FROM  ecs_order_info AS a 
			LEFT JOIN ecs_order_goods AS b ON a.order_id=b.order_id 
			LEFT JOIN order_dispatch AS c ON c.order_id=a.order_id 
			LEFT JOIN ship_route AS d ON d.route_id=c.route_id $where";
		$filter['record_count'] = $GLOBALS['db_read']->getOne($query);
		$filter = page_and_size($filter);
		
		$limit = "LIMIT " . $filter['start'] . "," . $filter['page_size']. "";
		$sql = "SELECT SUM(b.goods_number) AS gnum,c.turn,d.station_id 
			FROM ecs_order_info AS a 
			LEFT JOIN ecs_order_goods AS b ON a.order_id=b.order_id 
			LEFT JOIN order_dispatch AS c ON c.order_id=a.order_id 
			LEFT JOIN ship_route AS d ON d.route_id=c.route_id $where 
			GROUP BY c.turn,d.station_id $limit";
		set_filter($filter, $sql);
	} else {
		$sql = $result['sql'];
		$filter = $result['filter'];
	}
	$list = $GLOBALS['db_read']->getAll($sql);
	return array('list' => $list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}