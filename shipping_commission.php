<?php
/**
 * 配送提成
 * $Author: bisc $
 * $Id: shipping_commission.php 
*/

require(dirname(__FILE__) . '/includes/init.php');
admin_priv('st_com');
$_REQUEST['act'] = empty($_REQUEST['act']) ? 'list' : trim($_REQUEST['act']);
//初始化城市编号
$city_code =db_create_in(array_keys($_SESSION['city_arr']));
if ($_REQUEST['act'] == 'list')
{
	$_REQUEST['station'] = empty($_REQUEST['station']) ? intval($_SESSION['station']) : $_REQUEST['station'];	

	$list = order_list();
	
    $smarty->assign('record_count', 		$list['record_count']);
    $smarty->assign('page_count',   		$list['page_count']);
    $smarty->assign('filter',       		$list['filter']);	
	$smarty->assign('list',   		        $list['orders']);  
	$smarty->assign('ur_here',     '配送提成');
    $smarty->assign('full_page',   1);
	$smarty->display('shipping_commission.html');
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
	$smarty->assign('list',   		        $list['orders']); 
	 
    make_json_result($smarty->fetch('shipping_commission.html'), '', array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}
elseif ($_REQUEST['act'] == 'retry')
{
    $stations = $db_read->getAll("SELECT station_id,station_name FROM ship_station where city_code $city_code ");
	$smarty->assign('stations',   $stations);
	$smarty->assign('ur_here',     '配送提成复核');
	$smarty->display('shipping_commission_retry.html');
}
elseif ($_REQUEST['act'] == 'reset')
{
	$station_id = intval($_POST['station']);
	$station = $db_read->getOne("select station_name from ship_station where station_id=".$station_id);
	$bdate = $_POST['bdate'];
	$sql = "delete from ship_commit where station = '$station' and bdate = '$bdate'";
	$stations = $db_write->query($sql);
    los_header("Location: shipping_commission.php?station=".$station_id."&bdate=".$bdate."\n");exit;
}

function order_list()
{
	$filter['bdate']    = empty($_REQUEST['bdate'])    ? date('Y-m-d') : trim($_REQUEST['bdate']);
	$filter['station']  = empty($_REQUEST['station'])  ? 7 : intval($_REQUEST['station']);
	$reset = intval($_REQUEST['reset']);

	$sql = "SELECT station_name FROM ship_station  where station_id = '".$filter['station']."'";
	$station = $GLOBALS['db_read']->getOne($sql);	
	
	$sql = "select count(1) from ship_commit where station = '". $station ."' and bdate = '".$filter['bdate']."' ";	
	$count = $GLOBALS['db_read']->getOne($sql);	
	$stat = array();
	if($count)
	{
		$sql =  "select * from ship_commit where station = '". $station ."' and bdate = '".$filter['bdate']."' ";	
	    $res = $GLOBALS['db_read']->getAll($sql);
		$stat['station'] = $station;
		$stat['rowspan'] = count($res)+1;
		foreach($res as $kk =>$val)
		{
		   $stat['children'][$kk]['name'] = $val['name'];
		   $stat['children'][$kk]['p25'] = $val['p25'];
		   $stat['children'][$kk]['p10'] = $val['p10'];
		   $stat['children'][$kk]['p15'] = $val['p15'];
		   $stat['children'][$kk]['p20'] = $val['p20'];
		   $stat['children'][$kk]['p30'] = $val['p30'];
		   $stat['children'][$kk]['p50'] = $val['p50'];
		   $stat['children'][$kk]['orders'] = $val['orders'];
		   $stat['children'][$kk]['goods'] = $val['goods'];
		   $stat['children'][$kk]['sums'] = $val['sums'];
		   $stat['children'][$kk]['fee'] = $val['fee'];
		   $stat['turn1_025_x'] += $val['p25'];
		   $stat['turn1_1_x']   += $val['p10'];
		   $stat['turn1_15_x']  += $val['p15'];
		   $stat['turn1_2_x']   += $val['p20'];
		   $stat['turn1_3_x']   += $val['p30'];
		   $stat['turn1_5_x']   += $val['p50'];
		   $stat['turn1_gcount_x'] += $val['goods'];
		   $stat['turn1_ocount_x'] += $val['orders'];
		   $stat['total_p_x'] += $val['sums'];
		   $stat['shipping_fee_x'] += $val['fee'];
		}	
	}
	else
	{
		$sql =  "SELECT o.order_id, a.employee_id,s.station_id, g.goods_attr, g.goods_number ".
				"FROM order_delivery AS a ".
				"LEFT JOIN order_dispatch AS b ON a.order_id = b.order_id ".
				"LEFT JOIN ship_route AS r ON r.route_id = b.route_id ".
				"LEFT JOIN hr_employees AS c ON a.employee_id = c.id ".
				"LEFT JOIN ecs_order_info AS o ON a.order_id = o.order_id ".
				"LEFT JOIN ship_station AS s ON r.station_id = s.station_id ".
				"LEFT JOIN ecs_order_goods AS g ON o.order_id = g.order_id ".
				"WHERE a.status >1 and s.station_id ='".$filter['station']."' ".
				"AND o.best_time > '".$filter['bdate']."' ".
				"AND o.best_time < '".$filter['bdate']." 23:30:00' ".
				"AND g.goods_price >40 ";
		
		$goods = $GLOBALS['db_read']->getAll($sql);

		$sql= "SELECT a.employee_id,c.name,sum(if(o.shipping_fee >10, o.shipping_fee, 0)) as shipping_fee ".
		   	 "FROM order_delivery AS a ".
			 "LEFT JOIN order_dispatch AS b ON a.order_id = b.order_id ".
			 "LEFT JOIN ship_route AS r ON r.route_id = b.route_id ".
			 "LEFT JOIN hr_employees AS c ON a.employee_id = c.id ".
			 "LEFT JOIN ecs_order_info AS o ON a.order_id = o.order_id ".
			 "LEFT JOIN ship_station AS s ON r.station_id = s.station_id ".
			 "WHERE a.status >1 ".
			 "AND o.best_time > '".$filter['bdate']."' ".
			 "AND o.best_time < '".$filter['bdate']." 23:30:00' ".
			 "AND s.station_id = '".$filter['station']."' ".
			 "GROUP BY a.employee_id";
		$employees = $GLOBALS['db_read']->getAll($sql);
		
		$stat['station'] = $station;
		$stat['rowspan'] = count($employees)+1;  
		
		foreach($employees as $ekey => $em)
		{
		   $stat['children'][$ekey]['name'] = $em['name'];	
		   $stat['children'][$ekey]['station'] = $station;
		   $stat['children'][$ekey]['bdate'] = $filter['bdate'];
           $stat['children'][$ekey]['fee'] = intval($em['shipping_fee']);
		   $order = array();		   
		   foreach($goods as $val)
		   { 
				 if($val['employee_id'] == $em['employee_id'])
				 {
					if($val['goods_attr'] == '0.25磅')
					{
						$stat['children'][$ekey]['p25'] += $val['goods_number'];
						$stat['turn1_025_x'] += $val['goods_number'];
					}
					if($val['goods_attr'] == '1.0磅')
					{
						$stat['children'][$ekey]['p10'] += $val['goods_number'];
						$stat['turn1_1_x'] += $val['goods_number'];
					}
					if($val['goods_attr'] == '1.5磅')
					{
						$stat['children'][$ekey]['p15'] += $val['goods_number'];
						$stat['turn1_15_x'] += $val['goods_number'];
					}
					if($val['goods_attr'] == '2.0磅')
					{
						$stat['children'][$ekey]['p20'] += $val['goods_number'];
						$stat['turn1_2_x'] += $val['goods_number'];
					}
					if($val['goods_attr'] == '3.0磅')
					{
						$stat['children'][$ekey]['p30'] += $val['goods_number'];
						$stat['turn1_3_x'] += $val['goods_number'];
					}
					if($val['goods_attr'] == '5.0磅')
					{
						$stat['children'][$ekey]['p50'] += $val['goods_number'];
						$stat['turn1_5_x'] += $val['goods_number'];
					}
					$stat['children'][$ekey]['goods'] += $val['goods_number'];
					
					$stat['children'][$ekey]['sums'] += floatval($val['goods_attr']) * intval($val['goods_number']); 
					$order[] = $val['order_id'];
					$stat['turn1_gcount_x'] += $val['goods_number'];
		            $stat['total_p_x'] += floatval($val['goods_attr']) * intval($val['goods_number']);
				 }
		   }
		   $stat['children'][$ekey]['orders'] = count(array_unique($order));
		   $stat['turn1_ocount_x'] += count(array_unique($order));
		   $stat['shipping_fee_x'] += intval($em['shipping_fee']); 
	    }
		if($filter['bdate'] < date('Y-m-d'))
		{	
			foreach($stat['children'] as $vv)
			{
			   $GLOBALS['db_write']->autoExecute('ship_commit', $vv, 'INSERT');
			}	
		}
	}		

    $arr = array('orders' => $stat, 'filter' => $filter, 'page_count' => 1, 'record_count' => 1);
    return $arr;
}

?>