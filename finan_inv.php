<?php
/**
 * 财务开发票
 * $Author: bisc $
 * $Id: finan_inv.php 
*/

require(dirname(__FILE__) . '/includes/init.php');

$_REQUEST['act'] = empty($_REQUEST['act']) ? 'list' : trim($_REQUEST['act']);

if ($_REQUEST['act'] == 'list')
{
    //admin_priv('34');

    $smarty->assign('ur_here',     '财务开发票');
    $smarty->assign('full_page',   1);
	
	$stations = $db_read->getAll("SELECT station_id,station_name FROM ship_station where station_id <10 ");
	$smarty->assign('stations',   $stations);
	
	$_REQUEST['sdate'] = date('Y-m-d');
    $_REQUEST['status'] = '2';
	$_REQUEST['pay'] = '1';
	$_REQUEST['inv_f'] = 2;
	$_REQUEST['orderstatus'] = 1;
	$list = order_list();
    //echo '<pre>';print_r($list['cake_type']);echo '</pre>';

    $smarty->assign('record_count', 		$list['record_count']);
    $smarty->assign('page_count',   		$list['page_count']);
    $smarty->assign('filter',       		$list['filter']);	
	$smarty->assign('order_list',   		$list['orders']);  
	$smarty->display('finan_inv_list.html');
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
    //make_json_error($list['sql']);
    make_json_result($smarty->fetch('finan_inv_list.html'), '', array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}
elseif ($_REQUEST['act'] == 'batch_operate')
{
    $orders = explode(',', trim($_REQUEST['order_id']));	

    foreach ($orders as $order)
	{
		$sql = "insert into order_inv (order_id,mount,ctime) values ($order,$mount,'".time()."')";
		$db_write->query($sql);	
	}
	exit;		   
}
elseif ($_REQUEST['act'] == 'ud')
{
	$order = intval($_REQUEST['id']);
	$mount = intval($_REQUEST['inv']);
	$sql = "insert into order_inv (order_id,mount,ctime) values ($order,$mount,'".time()."')";
	$db_write->query($sql);	
	
    $url = 'finan_inv.php?act=query&' . str_replace('act=ud', '', $_SERVER['QUERY_STRING']);
    los_header("Location: $url\n");
    exit;
}

function order_list()
{
	$filter['inv_f']    = empty($_REQUEST['inv_f'])   ? '' : trim($_REQUEST['inv_f']);
	$filter['otatus']   = empty($_REQUEST['otatus'])   ? 9  : intval($_REQUEST['otatus']);				
	$filter['sdate']    = empty($_REQUEST['sdate'])    ? '' : trim($_REQUEST['sdate']);
	$filter['station']  = empty($_REQUEST['station'])    ? '' : trim($_REQUEST['station']);
	$filter['turn']     = empty($_REQUEST['turn'])    ? '' : trim($_REQUEST['turn']);
    $filter['order_sn'] = empty($_REQUEST['order_sn']) ? '' : trim($_REQUEST['order_sn']);
	$filter['print_sn'] = empty($_REQUEST['print_sn']) ? '' : trim($_REQUEST['print_sn']);
	$filter['turns']     = empty($_REQUEST['turns'])     ? 0  : trim($_REQUEST['turns']);
    $filter['page']     = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);	
		
	$where = " where agency_id=3 and a.order_status=1 and  a.inv_content > '' ";
	if($filter['sdate'])
	{
		$where .= " and best_time > '".$filter['sdate']."' and best_time < '".$filter['sdate']." 23:30:30' ";
	}
	if($filter['inv_f'] == 1)
	{
	   $where .= "and c.id >0 ";
	}
	if($filter['inv_f'] == 2)
	{
	   $where .= "and c.id is null ";
	}	
	if($filter['turns'] == 1)
	{
	   $where .= "and a.inv_content ='蛋糕' ";
	}
	if($filter['turns'] == 2)
	{
	   $where .= "and a.inv_content ='食品' ";
	}
	if($filter['turn'])
	{
	   $where .= "and d.turn ='".$filter['turn']."' ";
	}
	if($filter['station'])
	{
	   $where .= "and r.station_id ='".$filter['station']."' ";
	}
	
	if($filter['print_sn'])
	{
	   $where .= " and b.print_sn = '".$filter['print_sn']."'";
	
	}
	if($filter['order_sn'])
	{
	   $where = " where a.order_sn like '%".$filter['order_sn']."' and b.bdate = '".$filter['sdate']."' ";
	}
	
	$size = 30;	
	$sql = "select count(1) ".
	       "from order_genid as g ".
		   "left join ecs_order_info as a on a.order_id=g.order_id ".
		   "left join order_dispatch as d on a.order_id=d.order_id ".
		   "left join ship_route as r on r.route_id=d.route_id ".
		   "left join print_log_bt as b on a.order_id=b.order_id ".
	       "left join order_inv as c on a.order_id=c.order_id ".$where;

    $record_count   = $GLOBALS['db_read']->getOne($sql);
	
    $page_count     = $record_count > 0 ? ceil($record_count / $size) : 1;

	$sql = "select a.order_id, a.order_sn, a.best_time,(a.order_amount + a.money_paid) AS total, a.to_buyer,".
	       "a.inv_content, a.inv_payee, a.pay_name, a.pay_note, c.id,b.print_sn,d.turn,r.route_name ".
	       "from order_genid as g ".
		   "left join ecs_order_info as a on a.order_id=g.order_id ".
		   "left join order_dispatch as d on a.order_id=d.order_id ".
		   "left join ship_route as r on r.route_id=d.route_id ".
		   "left join print_log_bt as b on a.order_id=b.order_id ".
	       "left join order_inv as c on a.order_id=c.order_id ".$where.
		   " LIMIT " . ($filter['page'] - 1) * $size . ",$size";

	$res = $GLOBALS['db_read']->GetAll($sql);
    foreach($res as $key => $val)
	{
		$res[$key]['total'] =  floatval($val['total']);
		$res[$key]['money_paid'] = floatval($val['money_paid']);
	}
    $arr = array('orders' => $res, 'filter' => $filter, 'page_count' => $page_count,'record_count' => $record_count,'sql' => $sql);

    return $arr;
}

?>