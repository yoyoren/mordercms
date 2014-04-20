<?php
/**
 * 配送提成
 * $Author: bisc $
 * $Id: shipping_commission.php 
*/

require(dirname(__FILE__) . '/includes/init.php');
admin_priv('sta_comm');
$_REQUEST['act'] = empty($_REQUEST['act']) ? 'list' : trim($_REQUEST['act']);
//初始化城市编号
$city_code =db_create_in(array_keys($_SESSION['city_arr']));

if ($_REQUEST['act'] == 'list')
{
    $smarty->assign('ur_here',     '配送提成');
    $smarty->assign('full_page',   1);
	
	$sql = "SELECT station_id,station_name FROM ship_station  where station_id = '".trim($_SESSION['station'])."'";
	$stations = $db_read->getAll($sql);
   
	if($stations)
	{ 
		$smarty->assign('Current','Current');
		$smarty->assign('stations',   $stations);
		$_REQUEST['station'] = $stations[0]['station_id'];	
	}
	else
	{
		$stations = $db_read->getAll("SELECT station_id,station_name FROM ship_station where city_code $city_code ");
		$smarty->assign('stations',   $stations);
	}
    $_REQUEST['edate'] = date('Y-m-d',strtotime('yesterday'));
	$_REQUEST['sdate'] = date('Y-m',strtotime('yesterday')).'-01';
	$list = order_list();
	
    $smarty->assign('record_count', 		$list['record_count']);
    $smarty->assign('page_count',   		$list['page_count']);
    $smarty->assign('filter',       		$list['filter']);	
	$smarty->assign('order_list',   		$list['orders']);  
	$smarty->assign('total',   				$list['total']);  
	$smarty->display('shipping_commit.html');
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
	 
    make_json_result($smarty->fetch('shipping_commit.html'), '', array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}

function order_list()
{
	$filter['sdate']    = empty($_REQUEST['sdate'])    ? date('Y-m-d') : trim($_REQUEST['sdate']);
	$filter['edate']    = empty($_REQUEST['edate'])    ? date('Y-m-d') : trim($_REQUEST['edate']);
	$filter['station']  = empty($_REQUEST['station'])  ? 0 : intval($_REQUEST['station']);
	
	$sql = "SELECT station_name FROM ship_station  where station_id = '".$filter['station']."'";
	$station = $GLOBALS['db_read']->getOne($sql);		
			
	$where = '';				
	if($filter['station'] && $filter['station'] < 100 )
	{
		$where .= " and station = '".$station."' ";
	}
	if($filter['station'] == 100)
	{
	   $where .= " and station ='' ";
	}

    $sql = "select station from ship_commit where bdate >= '".$filter['sdate']."' and bdate <= '".$filter['edate']."' ".$where." group by station ";
    $stations = $GLOBALS['db_read']->getCol($sql);

	$sql =  "select station,name, sum(p25) as p25 , sum(p10) as p10, sum(p15) as p15, sum(p20) as p20,".
	        "sum(p30) as p30, sum(p50) as p50, sum(orders) as orders, sum( goods ) as goods,".
			"sum( sums ) as sums, sum( fee ) as fee from ship_commit ".
			"where bdate >= '".$filter['sdate']."' and bdate <= '".$filter['edate']."' ".$where.
			" group by station,name";
	$res = $GLOBALS['db_read']->getAll($sql);
	
	foreach($stations as $key => $vall)
	{
		$stat[$key]['station'] = $vall;
		$n = 1;
		foreach($res as $kk =>$val)
		{
		   if($val['station'] == $vall)
		   {
			   $stat[$key]['children'][$kk]['name'] = $val['name'];
			   $stat[$key]['children'][$kk]['p25'] = $val['p25'];
			   $stat[$key]['children'][$kk]['p10'] = $val['p10'];
			   $stat[$key]['children'][$kk]['p15'] = $val['p15'];
			   $stat[$key]['children'][$kk]['p20'] = $val['p20'];
			   $stat[$key]['children'][$kk]['p30'] = $val['p30'];
			   $stat[$key]['children'][$kk]['p50'] = $val['p50'];
			   $stat[$key]['children'][$kk]['orders'] = $val['orders'];
			   $stat[$key]['children'][$kk]['goods'] = $val['goods'];
			   $stat[$key]['children'][$kk]['sums'] = $val['sums'];
			   $stat[$key]['children'][$kk]['fee'] = $val['fee'];
			   $stat[$key]['turn1_025_x'] += $val['p25'];
			   $stat[$key]['turn1_1_x']   += $val['p10'];
			   $stat[$key]['turn1_15_x']  += $val['p15'];
			   $stat[$key]['turn1_2_x']   += $val['p20'];
			   $stat[$key]['turn1_3_x']   += $val['p30'];
			   $stat[$key]['turn1_5_x']   += $val['p50'];
			   $stat[$key]['turn1_gcount_x'] += $val['goods'];
			   $stat[$key]['turn1_ocount_x'] += $val['orders'];
			   $stat[$key]['total_p_x'] += $val['sums'];
			   $stat[$key]['shipping_fee_x'] += $val['fee'];
			   $n++;		   
		   }
		}
		$stat[$key]['rowspan'] = $n;		
	}
	
	foreach($res as $val)
	{
	   $total['turn1_025_t'] += $val['p25'];
	   $total['turn1_1_t']   += $val['p10'];
	   $total['turn1_15_t']  += $val['p15'];
	   $total['turn1_2_t']   += $val['p20'];
	   $total['turn1_3_t']   += $val['p30'];
	   $total['turn1_5_t']   += $val['p50'];
	   $total['turn1_goodstotal'] += $val['goods'];
	   $total['turn1_orderstotal'] += $val['orders'];
	   $total['total_p'] += $val['sums'];
	   $total['shipping_fee'] += $val['fee'];
	}	
	
	$arr = array('orders' => $stat, 'filter' => $filter, 'page_count' => $pages['pagecount'], 'record_count' => $pages['recordcount'],'total' => $total);
    return $arr;
}

?>