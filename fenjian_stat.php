<?php
/**
 *  分拣查询
 * $Author: bisc $
 * $Id: fenjian_stat.php 
*/

require(dirname(__FILE__) . '/includes/init.php');

$_REQUEST['act'] = empty($_REQUEST['act']) ? 'list' : trim($_REQUEST['act']);

if ($_REQUEST['act'] == 'list')
{
    //admin_priv('34');

    $smarty->assign('ur_here',     '分拣订单蛋糕统计');
    $smarty->assign('full_page',   1);
	
	$sql = "SELECT station_id,station_name FROM ship_station  where station_id = '".intval($_SESSION['station'])."'";
	$stations = $db_read->getAll($sql);
   
	if($stations)
	{ 
		$smarty->assign('Current','Current');
		$smarty->assign('stations',   $stations);
		$_REQUEST['station'] = $stations[0]['station_id'];
		$sql = "select id as employee_id,name as employee_name from hr_employees where station_id = '".$stations[0]['station_id']."'";
		$arr = $db_read->getAll($sql);
		$smarty->assign('employee_list',   $arr);	
	}
	else
	{
		$stations = $db_read->getAll("SELECT station_id,station_name FROM ship_station where station_id <10 ");
		$smarty->assign('stations',   $stations);
	}
	
	$_REQUEST['sdate'] = $_REQUEST['edate'] = date('Y-m-d');
	$list = order_list();
//print_r($list);
    $smarty->assign('filter',       		$list['filter']);	
	$smarty->assign('order_list',   		$list['list']);  
	$smarty->display('ship_stat_list.html');
}
/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $list = order_list();

    $smarty->assign('filter',       		$list['filter']);	
	$smarty->assign('order_list',   		$list['list']); 
	 
    make_json_result($smarty->fetch('ship_stat_list.html'), '', array('filter' => $list['filter'], 'page_count' => 1));
}
elseif ($_REQUEST['act'] == 'stn')
{
	require(ROOT_PATH . 'includes/cls_json.php');
	
	$arr = $db_read->getAll("select route_name,route_id from ship_route where flag=1 and station_id = ".intval($_GET['stn']));
	
	$json = new JSON;
	echo $json->encode($arr);
}

function order_list()
{		    		
	$filter['route']    = empty($_REQUEST['route'])    ? 0 : intval($_REQUEST['route']);		
	$filter['goods']   	= empty($_REQUEST['goods'])    ? 0 : intval($_REQUEST['goods']);		
	$filter['sdate']    = empty($_REQUEST['sdate'])    ? '' : trim($_REQUEST['sdate']);
	$filter['edate']    = empty($_REQUEST['edate'])    ? '' : trim($_REQUEST['edate']);
	$filter['turn']     = empty($_REQUEST['turn'])     ? 0 : intval($_REQUEST['turn']);
	$filter['station']  = empty($_REQUEST['station'])  ? 0 : trim($_REQUEST['station']);
    $filter['page']     = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);	
			
	$where = " where agency_id=3 and order_status=1 and goods_price>100 ";
	if($filter['sdate'])
	{
		$where .= " and best_time > '".$filter['sdate']."'";
	}
	if($filter['edate'])
	{
		$where .= " and best_time < '".$filter['edate']." 23:23:00'";
	}
	if($filter['turn'])
	{
	   $where .= " and c.turn = '".$filter['turn']."' ";
	}
	if($filter['route'])
	{
	   $where .= " and c.route_id = '".$filter['route']."' ";
	}
	if($filter['station'])
	{
	   $where .= " and d.station_id = '".$filter['station']."' ";
	}		

	$sql = "select g.goods_number, o.order_id ".
		   "from ecs_order_info as o ".
		   "left join ecs_order_goods as g on o.order_id=g.order_id ".
	       "left join order_dispatch as c on o.order_id=c.order_id ".
		   "left join ship_route as d on c.route_id=d.route_id ".
		   "left join ship_station as s on d.station_id=s.station_id ".$where;
		   //echo $sql;
	$res = $GLOBALS['db_read']->GetAll($sql);
    $cake_count = 0;    
	$order = array();        
    foreach($res as $key => $val)
	{
	    $order[] = $val['order_id'];
		$cake_count += $val['goods_number'];
	}
	
    $arr['list']['0']['order'] = count(array_unique($order));
    $arr['list']['0']['cakes'] = $cake_count;
	$arr['filter'] = $filter;
    return $arr;
}

?>