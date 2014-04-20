<?php
/**
 * Today Products Statistics
 * @copyright Copyright (c) 2012, 21cake food co.ltd
 * @author 21Cake Dev Team
 */

require dirname(__FILE__) . '/includes/init.php';
$_REQUEST['act'] = empty($_REQUEST['act']) ? 'list' : trim($_REQUEST['act']);
$act = array('list', 'query');

if (in_array($_REQUEST['act'], $act)) {
	switch ($_REQUEST['act']) {
		case 'query':
                        ob_clean();
			$stat_list = get_stat_list();
			$smarty->assign('stat_list', $stat_list['list']);
			$smarty->assign('filter', $stat_list['filter']);
			$smarty->assign('record_count', $stat_list['record_count']);
			$smarty->assign('page_count', $stat_list['page_count']);
			make_json_result($smarty->fetch('today.html'), '', array('filter' => $stat_list['filter'], 'page_count' => $stat_list['page_count'], 'sql' => $stat_list['sql']));
			break;
		default:
			
			$stat_list = get_stat_list();
			$smarty->assign('ur_here', '当日蛋糕统计');
			$smarty->assign('full_page', 1);
			$smarty->assign('lot_list', getTurn());
			$smarty->assign('city_list', $_SESSION['city_arr']);
			$smarty->assign('stat_list', $stat_list['list']);
			$smarty->assign('filter', $stat_list['filter']);
			$smarty->assign('record_count', $stat_list['record_count']);
			$smarty->assign('page_count', $stat_list['page_count']);
			$smarty->display('today.html');
	}
} else {
	sys_msg('坑爹呀，页面不存在！', 1, array(array('text' => '返回当日生产统计'), array('href' => 'today.php?act=list')));
}

function get_goods_name($goods_id) { // Obtaion to the goods name
	$sql = "SELECT CONCAT(goods_sn,'--',goods_name) FROM ecs_goods WHERE goods_id='$goods_id'";
	return $GLOBALS['db_read']->getOne($sql);
}

function get_stat_list() { // Obtaion to the statistics list
	$filter = array();
	$condition = $where = "WHERE 1";
	
	if (empty($_REQUEST['city'])) {
		$city = array_keys($_SESSION['city_arr']);
		$condition .= " AND city='$city[0]'";
		$where .= " AND city='$city[0]'";
//		$condition .= " AND city" . db_create_in(array_keys($_SESSION['city_arr'])); // 灵活选择城市的搜索方法 
	} else {
		$condition .= " AND city='" . intval($_REQUEST['city']) . "'";
		$where .= " AND city='" . intval($_REQUEST['city']) . "'";
	}
	$filter['bdate'] = empty($_REQUEST['bdate']) ? date('Y-m-d') : trim($_REQUEST['bdate']);
	$filter['turn'] = empty($_REQUEST['turn']) ? '' : intval($_REQUEST['turn']);
	if ($filter['bdate'])	$condition .= " AND bdate='" . $filter['bdate'] . "'";
	$sql = "SELECT COUNT(*) FROM cake_stat $condition";
	$count = $GLOBALS['db_read']->getOne($sql);
	
	if ($count) {
		if ($filter['bdate'])	$where .= " AND bdate='" . $filter['bdate'] . "'";
		$query = "SELECT COUNT(*) FROM cake_stat $where";
		$filter['record_count'] = $GLOBALS['db_read']->getOne($query);	
		$filter = page_and_size($filter);
		$total = $GLOBALS['db_read']->getAll("SELECT goods_attr,goods_sum FROM cake_stat $where");
		foreach ($total as $val) {
			$list['weight_total'] += $val[goods_sum] * floatval($val['goods_attr']);
			$list['num_total'] += $val['goods_sum'];
		}
		
		$limit = "LIMIT " .$filter['start'] . "," . $filter['page_size'];
		$sql = "SELECT id,city,bdate,goods_id,goods_attr,goods_sum,flag FROM cake_stat $where $limit";
		$goods = $GLOBALS['db_read']->getAll($sql);
		foreach ($goods as $key => $val) {
			$list['stat'][$key]['city'] = $filter['city'];
			$list['stat'][$key]['goods_id'] = $val['goods_id'];
			$list['stat'][$key]['goods_name'] = get_goods_name($val['goods_id']);
			$list['stat'][$key]['bdate'] = $filter['bdate'];
			$list['stat'][$key]['goods_attr'] = empty($val['goods_attr']) ? '0.25' : floatval($val['goods_attr']);
			$list['stat'][$key]['goods_sum'] = $val['goods_sum'];
			$list['stat'][$key]['order_group'] = $val['order_group'];
		}
	} else {
		$term = "WHERE 1 AND b.order_status='1' AND c.goods_price>40";
		
		if (empty($_REQUEST['city'])) {
//			$term .= " AND b.country" . db_create_in(array_keys($_SESSION['city_arr'])); // 灵活选择城市的搜索方法
			foreach (array_keys($_SESSION['city_arr']) as $val) {
				if (isset($val) && $val == '441') {
					$term .= " AND b.country='441'";
				} elseif (isset($val) && $val == '442') {
					$term .= " AND b.country='442'";
				}
			}
		} else {
			$term .= " AND b.country IN ('" . intval($_REQUEST['city']) . "')";
			$filter['city'] = intval($_REQUEST['city']);
		}
		
		if ($filter['bdate'])	$term .= " AND b.best_time BETWEEN '" . $filter['bdate'] . " 00:00:00' AND '" . $filter['bdate'] . " 23:59:59'";
		if ($filter['turn'])		$term .= " AND d.turn='" . $filter['turn'] . "'";
		if (!empty($filter['turn']))			$join = "LEFT JOIN order_dispatch AS d ON d.order_id=b.order_id";
		$query = "SELECT COUNT(*) FROM order_genid AS a 
			LEFT JOIN ecs_order_info AS b ON b.order_id=a.order_id 
			LEFT JOIN ecs_order_goods AS c ON c.order_id=b.order_id " . $join . " $term";
		$filter['record_count'] = $GLOBALS['db_read']->getOne($query);
		$filter = page_and_size($filter);
		
		$limit = "LIMIT " . $filter['start'] . "," . $filter['page_size'];		
		$sql = "SELECT b.best_time,c.goods_id,c.goods_attr,SUM(c.goods_number) AS gnum,GROUP_CONCAT(c.order_id) AS order_group 
			FROM order_genid AS a 
			LEFT JOIN ecs_order_info AS b ON b.order_id=a.order_id 
			LEFT JOIN ecs_order_goods AS c ON c.order_id=b.order_id " . $join . " $term 
			GROUP BY c.goods_id,c.goods_attr ASC";
		$goods = $GLOBALS['db_read']->getAll($sql);
		foreach ($goods as $key => $val) {
			$list['stat'][$key]['city'] = $filter['city'];
			$list['stat'][$key]['goods_id'] = $val['goods_id'];
			$list['stat'][$key]['goods_name'] = get_goods_name($val['goods_id']);
			$list['stat'][$key]['bdate'] = $filter['bdate'];
			$list['stat'][$key]['goods_attr'] = empty($val['goods_attr']) ? '0.25' : floatval($val['goods_attr']);
			$list['stat'][$key]['goods_sum'] = $val['gnum'];
			$list['stat'][$key]['order_group'] = $val['order_group'];
			$list['weight_total'] += $val['gnum'] * floatval($val['goods_attr']);
			$list['num_total'] += $val['gnum'];
			
			if (!empty($_REQUEST['city'])) {
				if ($filter['bdate'] < date('Y-m-d'))			$GLOBALS['db_write']->autoExecute('cake_stat', $list['stat'][$key], 'INSERT');
			}
		}
	}
	return array('list' => $list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count'], 'sql' => $sql);
}
