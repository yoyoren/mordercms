<?php
/**
 * 财务现结算
 * $Author: bisc $
 * $Id: finan_conlect.php 
*/

require(dirname(__FILE__) . '/includes/init.php');

$_REQUEST['act'] = empty($_REQUEST['act']) ? 'list' : trim($_REQUEST['act']);

if ($_REQUEST['act'] == 'list')
{
    $smarty->assign('ur_here',     '财务现结算');
    $smarty->assign('full_page',   1);
	
	$stations = $db_read->getAll("SELECT station_id,station_name FROM ship_station where station_id <10 ");
	$smarty->assign('stations',   $stations);
	
    $_REQUEST['status'] = 1;
	$_REQUEST['otatus'] = 1;
	$list = order_list();

    $smarty->assign('record_count', 		$list['record_count']);
    $smarty->assign('page_count',   		$list['page_count']);
    $smarty->assign('filter',       		$list['filter']);	
	$smarty->assign('order_list',   		$list['orders']);  
	$smarty->assign('orders_fee_count',   	$list['ofee']);  
	$smarty->assign('cake_type',   			$list['sums']);  
	$smarty->display('finan_conlect_list.html');
}
elseif ($_REQUEST['act'] == 'query')
{
    $list = order_list();

    $smarty->assign('record_count', 		$list['record_count']);
    $smarty->assign('page_count',   		$list['page_count']);
    $smarty->assign('filter',       		$list['filter']);
	$smarty->assign('order_list',   		$list['orders']); 
	$smarty->assign('orders_fee_count',   	$list['ofee']);
	$smarty->assign('cake_type',   			$list['sums']);
	 
    make_json_result($smarty->fetch('finan_conlect_list.html'), '', array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}
elseif ($_REQUEST['act'] == 'batch_operate')
{
    $orders = explode(',', trim($_REQUEST['order_id']));	
    foreach ($orders as $order)
	{
		$sql = "UPDATE order_finance SET ctatus=3,radmin='".$_SESSION['admin_id']."' WHERE order_id = '".$order."'";
		$db_write->query($sql);
	}
	exit;		   
}
elseif ($_REQUEST['act'] == 'ud')
{
	$order = intval($_REQUEST['id']);
	$sql = "UPDATE order_finance SET ctatus=3,radmin='".$_SESSION['admin_id']."' WHERE order_id = '".$order."'";
	$db_write->query($sql);
    exit;
}


function order_list()
{
	$filter['status']   = empty($_REQUEST['status'])   ? '' : trim($_REQUEST['status']);
	$filter['otatus']   = empty($_REQUEST['otatus'])   ? 9  : intval($_REQUEST['otatus']);				
	$filter['bdate']    = empty($_REQUEST['bdate'])    ? date('Y-m-d') : trim($_REQUEST['bdate']);
    $filter['order_sn'] = empty($_REQUEST['order_sn']) ? '' : trim($_REQUEST['order_sn']);
    $filter['print_sn'] = empty($_REQUEST['print_sn']) ? '' : trim($_REQUEST['print_sn']);
	$filter['turn']     = empty($_REQUEST['turn'])     ? 0  : intval($_REQUEST['turn']);
	$filter['station']  = empty($_REQUEST['station'])  ? '' : intval($_REQUEST['station']);
    $filter['page']     = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);	
	$filter['twodate']  = empty($_REQUEST['twodate'])  ? '' : trim($_REQUEST['twodate']);
	
	$where = " where agency_id=3 and order_amount >0 and pay_id>1 and pay_id<5 ";
	
	if($filter['bdate'] && $filter['twodate']=="")
	{
	   $where .= " and best_time > '".$filter['bdate']."' and best_time < '".$filter['bdate']." 23:30:30' ";
	}
	if($filter['twodate']){
		$bigtime=date("Y-m-d",(strtotime($filter['bdate'])+3600*24));
		$where .= " and best_time> '".$filter['bdate']."' and best_time < '".$bigtime." 23:30:30' ";
	}
	
	if($filter['otatus'] < 9)
	{
	   $where .= " and o.order_status = '".$filter['otatus']."'";
	}
	
	if($filter['print_sn'])
	{
	   $where .= "and p.print_sn = '".$filter['print_sn']."' ";
	}
	if($filter['status'] == 1)
	{
	   $where .= " and f.ctatus <3 ";
	}
	if($filter['status'] == 2)
	{
	   $where .= " and f.ctatus >=3 ";
	}
	if($filter['turn'])
	{
	   $where .= " and c.turn = '".$filter['turn']."' ";
	}
	if($filter['sender'])
	{
	   $where .= " and a.employee_id = '".$filter['sender']."' ";
	}
	if($filter['station'] && $filter['station'] != 100 )
	{
	   $where .= " and d.station_id = '".$filter['station']."' ";
	}
	if($filter['order_sn'])
	{
	   $where = " where order_sn = '".$filter['order_sn']."' ";
	}	

	$size = 30;	
	$sql = "select count(1) ".
	       "from order_delivery as a ".
		   "left join ecs_order_info as o on a.order_id=o.order_id ".
	       "left join order_dispatch as c on a.order_id=c.order_id ".
	       "left join order_finance as f on a.order_id=f.order_id ".
		   "left join print_log_bt as p on a.order_id=p.order_id ".
		   "left join hr_employees as h on a.employee_id=h.id ".
		   "left join ship_route as d on c.route_id=d.route_id ".
		   "left join ship_station as s on d.station_id=s.station_id ".$where;

    $record_count   = $GLOBALS['db_read']->getOne($sql);

	$sql = "select o.order_amount,o.pay_note ".
	       "from order_delivery as a ".
		   "left join ecs_order_info as o on a.order_id=o.order_id ".
	       "left join order_dispatch as c on a.order_id=c.order_id ".
		   "left join order_finance as f on a.order_id=f.order_id ".
		   "left join print_log_bt as p on a.order_id=p.order_id ".
		   "left join hr_employees as h on a.employee_id=h.id ".
		   "left join ship_route as d on c.route_id=d.route_id ".
		   "left join ship_station as s on d.station_id=s.station_id ".$where;

    $pos = $GLOBALS['db_read']->getAll($sql);
	$ofee = $sums = 0;
	foreach($pos as $vl)
	{
		if(strpos($vl['pay_note'],'OS'))
		{
		   $ofee += floatval(substr($vl['pay_note'],7,8));
		}
		$sums += floatval($vl['order_amount']);  
	}
	
	$sums = $sums - $ofee;
	
    $page_count     = $record_count > 0 ? ceil($record_count / $size) : 1;

	$sql = "select *,a.status as status,p.print_sn,a.order_id ".
	       "from order_delivery as a ".
		   "left join ecs_order_info as o on a.order_id=o.order_id ".
	       "left join order_dispatch as c on a.order_id=c.order_id ".
		   "left join order_finance as f on a.order_id=f.order_id ".
		   "left join print_log_bt as p on a.order_id=p.order_id ".
		   "left join hr_employees as h on a.employee_id=h.id ".
		   "left join ship_route as d on c.route_id=d.route_id ".
		   "left join ship_station as s on d.station_id=s.station_id ".$where.
		   " order by pay_name ".
		   " LIMIT " . ($filter['page'] - 1) * $size . ",$size";
	$res = $GLOBALS['db_read']->GetAll($sql);  
	foreach($res as $key => $val)
	{
	    $res[$key]['order_amount'] = ($val['pay_id'] == 1 || $val['pay_id'] >4) ? 0 : floatval($val['order_amount']);
	}
	
    $arr = array('orders' => $res, 'filter' => $filter, 'page_count' => $page_count,'record_count' => $record_count,'ofee' => $ofee,'sums' => $sums);

    return $arr;
}

?>