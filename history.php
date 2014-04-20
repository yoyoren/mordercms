<?php
/**
 * History Products Statistics
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
			$smarty->assign('turn',getTurn());
			$smarty->assign('stat_list', $stat_list['list']);
			$smarty->assign('filter', $stat_list['filter']);
			$smarty->assign('record_count', $stat_list['record_count']);
			$smarty->assign('page_count', $stat_list['page_count']);
			make_json_result($smarty->fetch('history.html'), '', array('filter' => $stat_list['filter'], 'page_count' => $stat_list['page_count'], 'sql' => $stat_list['sql']));
			break;			
		default:
			$stat_list = get_stat_list();
			//print_r($stat_list);
			$smarty->assign('ur_here', '历史蛋糕统计');
			$smarty->assign('turn',getTurn());
			$smarty->assign('full_page', 1);
			$smarty->assign('stat_list', $stat_list['list']);
			$smarty->assign('filter', $stat_list['filter']);
			$smarty->assign('record_count', $stat_list['record_count']);
			$smarty->assign('page_count', $stat_list['page_count']);
			$smarty->display('history.html');

	}
} else {
	sys_msg('坑爹呀，页面不存在！', 1, array(array('text' => '返回历史生产统计'), array('href' => 'history.php?act=list')));
}

function get_goods_name($goods_id) { // Obtaion to the goods name
	$sql = "SELECT CONCAT(goods_sn,'--',goods_name) FROM ecs_goods WHERE goods_id='$goods_id'";
	return $GLOBALS['db_read']->getOne($sql);
}

function get_stat_list() { // Obtaion to the statistics list
	$result = get_filter();
	
	if ($result === false) {
		$filter = array();
		$city_group=$_SESSION['city_group'];
		$where = "WHERE 1 ";		
		$bdate = date('Y-m-d', strtotime(date('Y-m-d')) - (3600 * 24 * 2));
		$sdate = date('Y-m-d', strtotime(date('Y-m-d')) - (3600 * 24 * 1));
		$filter['turn'] = empty($_REQUEST['turn']) ? '' : intval($_REQUEST['turn']);
		$filter['bdate'] = empty($_REQUEST['bdate']) ? $bdate : trim($_REQUEST['bdate']);
		$filter['sdate'] = empty($_REQUEST['sdate']) ? $sdate : trim($_REQUEST['sdate']);
		$filter['order_sn']=empty($_REQUEST['order_sn']) ? '':trim($_REQUEST['order_sn']);
		$filter['print_sn']=empty($_REQUEST['print_sn']) ? '':trim($_REQUEST['print_sn']);
		$filter['stan']  = empty($_REQUEST['city'])? $_SESSION['city_arr'] :intval($_REQUEST['city']);
	   		$join="";
		if (!empty($filter['turn']))			$join .= " LEFT JOIN order_dispatch AS d ON d.order_id=b.order_id";	
		if (!empty($filter['print_sn']))			$join .= " LEFT JOIN print_log_x AS p ON p.order_id=b.order_id";
		$term = "WHERE 1 AND b.order_status='1' AND c.goods_price>40";
				
		if ($filter['bdate'] && $filter['sdate'])	$term .= " and b.best_time>='" . $filter['bdate'] . " 00:00:00' AND b.best_time<='" . $filter['sdate'] . " 23:59:59'";
		if ($filter['turn'])		$term .= " AND d.turn='" . $filter['turn'] . "'";
		
		if($filter['print_sn']){
			$term .=" and p.print_sn='".$filter['print_sn']."'and p.city_group=".$city_group;
		}
		if($filter['order_sn']){
			$term  .= " and b.order_sn like '%".$filter['order_sn']."'"; ;
		}
		$query = "select count(*) from 
			(SELECT b.best_time,c.goods_id,c.goods_attr,SUM(c.goods_number) AS gnum,GROUP_CONCAT(c.order_id) AS order_group 
			FROM order_genid AS a 
			LEFT JOIN ecs_order_info AS b ON b.order_id=a.order_id 
			LEFT JOIN ecs_order_goods AS c ON c.order_id=b.order_id " . $join . " $term 
			GROUP BY c.goods_id,c.goods_attr )as a  ";
		$filter['record_count'] = $GLOBALS['db_read']->getOne($query);
		$filter = page_and_size($filter);
		
		$limit = "LIMIT " . $filter['start'] . "," . $filter['page_size'];	
		$sql = "SELECT b.best_time,c.goods_id,c.goods_attr,SUM(c.goods_number) AS gnum,GROUP_CONCAT(c.order_id) AS order_group 
			FROM order_genid AS a 
			LEFT JOIN ecs_order_info AS b ON b.order_id=a.order_id 
			LEFT JOIN ecs_order_goods AS c ON c.order_id=b.order_id " . $join . " $term 
			GROUP BY c.goods_id,c.goods_attr ASC ".$limit;
			//print_r($sql);exit;
		$goods = $GLOBALS['db_read']->getAll($sql);
		
		foreach ($goods as $key => $val) {
			$list['stat'][$key]['goods_id'] = $val['goods_id'];
			$list['stat'][$key]['goods_name'] = get_goods_name($val['goods_id']);
			$list['stat'][$key]['bdate'] = $filter['bdate'];
			$list['stat'][$key]['goods_attr'] = empty($val['goods_attr']) ? '0.25' : floatval($val['goods_attr']);
			$list['stat'][$key]['goods_sum'] = $val['gnum'];
			$list['stat'][$key]['order_group'] = $val['order_group'];
			$list['weight_total'] += $val['gnum'] * floatval($val['goods_attr']);
			$list['num_total'] += $val['gnum'];
			set_filter($filter, $sql);
		} 
	}else {
		$sql = $result['sql'];
		$filter = $result['filter'];
	}
	return array('list' => $list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count'], 'sql' => $sql);
}
