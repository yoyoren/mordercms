<?php
/**
 * 
 * $Author: bisc $
 * $Id: shipping_commission.php 
*/

require(dirname(__FILE__) . '/includes/init.php');
//检查权限
admin_priv('s_sor');
$_REQUEST['act'] = empty($_REQUEST['act']) ? 'list' : trim($_REQUEST['act']);

//初始化城市编号
$city_code =db_create_in(array_keys($_SESSION['city_arr']));
if ($_REQUEST['act'] == 'list')
{
	$list = order_list();

    $smarty->assign('record_count', 		$list['record_count']);
    $smarty->assign('page_count',   		$list['page_count']);
    $smarty->assign('filter',       		$list['filter']);	
	$smarty->assign('order_list',   		$list['orders']);  
	$smarty->assign('total',   				$list['total']);
	$smarty->assign('turn',getTurn()); 

	$smarty->assign('ur_here',     '分拣查询');
    $smarty->assign('full_page',   1);
	$smarty->display('fenjian2.html');
}
/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $list = order_list();

    $smarty->assign('record_count', 		$list['record_count']);
    $smarty->assign('page_count',   		$list['page_count']);
    $smarty->assign('filter',       		$list['filter']);
	$smarty->assign('order_list',   		$list['orders']); 
	$smarty->assign('total',   				$list['total']); 
		 
    make_json_result($smarty->fetch('fenjian2.html'), '', array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}

function order_list()
{
	$filter['sdate']    = empty($_REQUEST['sdate']) ? date('Y-m-d') : trim($_REQUEST['sdate']);
	$filter['edate']    = empty($_REQUEST['edate']) ? date('Y-m-d') : trim($_REQUEST['edate']);
	$filter['turn']     = empty($_REQUEST['turn']) ? 0 : intval($_REQUEST['turn']);
    
	$join = empty($filter['turn']) ? " " : " and a.turn = '".$filter['turn']."' ";
	$sql = "SELECT station_name,station_id FROM ship_station  where city_code ".$GLOBALS['city_code']." ";
	$stations = $GLOBALS['db_read']->getAll($sql);	

	$sql =  "SELECT r.route_id,r.station_id, g.goods_attr, sum(g.goods_number) as cakes ".
			"FROM order_dispatch AS a ".
			"LEFT JOIN ship_route AS r ON r.route_id = a.route_id ".
			"LEFT JOIN ecs_order_info AS o ON a.order_id = o.order_id ".
			"LEFT JOIN ecs_order_goods AS g ON o.order_id = g.order_id ".
			"WHERE g.goods_price >40 and order_status =1 and a.status>0 and country ".$GLOBALS['city_code']." ".
		    "AND o.best_time > '".$filter['sdate']."' ".$join.
		    "AND o.best_time < '".$filter['edate']." 23:30:00' ".
			"group by r.route_id,r.station_id, g.goods_attr";
		
	$goods = $GLOBALS['db_read']->getAll($sql);

	$stat = array();
    foreach($stations as $key => $val)
	{		
		$sql= "SELECT count(o.order_id) as orders,r.route_id,r.route_name ".
			 "FROM order_dispatch AS a ".
			 "LEFT JOIN ship_route AS r ON r.route_id = a.route_id ".
			 "LEFT JOIN ecs_order_info AS o ON a.order_id = o.order_id ".
			 "WHERE a.status >0 and order_status =1 ".$join.
			 "AND o.best_time > '".$filter['sdate']."' ".
			 "AND o.best_time < '".$filter['edate']." 23:30:00' ".
			 "AND r.station_id = '".$val['station_id']."' ".
			 "GROUP BY a.route_id";

		$routes = $GLOBALS['db_read']->getAll($sql);
		
		$stat[$key]['station'] = $val['station_name'];
		$stat[$key]['rowspan'] = count($routes)+1;  
		
		foreach($routes as $ekey => $em)
		{
		   $stat[$key]['children'][$ekey]['name'] = $em['route_name'];	
		   $stat[$key]['children'][$ekey]['order'] = $em['orders'];
		   $order = array();		   
		   foreach($goods as $val)
		   { 
				 if($val['route_id'] == $em['route_id'])
				 {
					if($val['goods_attr'] == '0.25磅')
					{
						$stat[$key]['children'][$ekey]['p25'] += $val['cakes'];
						$stat[$key]['turn1_025_x'] += $val['cakes'];
					}
					elseif($val['goods_attr'] == '1.0磅')
					{
						$stat[$key]['children'][$ekey]['p10'] += $val['cakes'];
						$stat[$key]['turn1_1_x'] += $val['cakes'];
					}
					elseif($val['goods_attr'] == '1.5磅')
					{
						$stat[$key]['children'][$ekey]['p15'] += $val['cakes'];
						$stat[$key]['turn1_15_x'] += $val['cakes'];
					}
					elseif($val['goods_attr'] == '2.0磅')
					{
						$stat[$key]['children'][$ekey]['p20'] += $val['cakes'];
						$stat[$key]['turn1_2_x'] += $val['cakes'];
					}
					elseif($val['goods_attr'] == '3.0磅')
					{
						$stat[$key]['children'][$ekey]['p30'] += $val['cakes'];
						$stat[$key]['turn1_3_x'] += $val['cakes'];
					}
					elseif($val['goods_attr'] == '5.0磅')
					{
						$stat[$key]['children'][$ekey]['p50'] += $val['cakes'];
						$stat[$key]['turn1_5_x'] += $val['cakes'];
					}
					else
					{
						$stat[$key]['children'][$ekey]['big'] += $val['cakes'];
						$stat[$key]['turn1_b_x'] += $val['cakes'];					
					}
					$stat[$key]['children'][$ekey]['goods'] += $val['cakes'];
					
					$stat[$key]['children'][$ekey]['sums'] += floatval($val['goods_attr']) * intval($val['cakes']); 
					$stat[$key]['turn1_gcount_x'] += $val['cakes'];
					$stat[$key]['total_p_x'] += floatval($val['goods_attr']) * intval($val['cakes']);
				 }
		   }
		   $stat[$key]['turn1_ocount_x'] += $em['orders'];
		}			
	}
	foreach($stat as $vals)
	{
	   $total['turn1_025_t'] += $vals['turn1_025_x'];
	   $total['turn1_1_t']   += $vals['turn1_1_x'];
	   $total['turn1_15_t']  += $vals['turn1_15_x'];
	   $total['turn1_2_t']   += $vals['turn1_2_x'];
	   $total['turn1_3_t']   += $vals['turn1_3_x'];
	   $total['turn1_5_t']   += $vals['turn1_5_x'];
	   $total['turn1_b_t']   += $vals['turn1_b_x'];
	   $total['turn1_goodstotal']  += $vals['turn1_gcount_x'];
	   $total['turn1_orderstotal'] += $vals['turn1_ocount_x'];
	   $total['total_p'] += $vals['total_p_x'];	
	}
    $arr = array('orders' => $stat, 'filter' => $filter, 'page_count' => 1, 'record_count' => 1,'total' => $total);
    return $arr;
}

?>