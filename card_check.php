<?php
/**
 * 财务审核订单
 * $Author: bisc $
 * $Id: finan_check.php 
*/

require(dirname(__FILE__) . '/includes/init.php');

$_REQUEST['act'] = empty($_REQUEST['act']) ? 'list' : trim($_REQUEST['act']);

if ($_REQUEST['act'] == 'list')
{
    //admin_priv('34');

    $smarty->assign('ur_here',     '审核代金卡');
    $smarty->assign('full_page',   1);
	
	$stations = $db_read->getAll("SELECT station_id,station_name FROM ship_station where station_id <10 ");
	$smarty->assign('stations',   $stations);

	
	$_REQUEST['sdate'] = date('Y-m-d');
    //$_REQUEST['status'] = '2';
	$_REQUEST['pay'] = '1';
	$_REQUEST['otatus'] = 1;
	$list = order_list();
    //echo '<pre>';print_r($list['cake_type']);echo '</pre>';

    $smarty->assign('record_count', 		$list['record_count']);
    $smarty->assign('page_count',   		$list['page_count']);
    $smarty->assign('filter',       		$list['filter']);	
	$smarty->assign('order_list',   		$list['orders']);  
	$smarty->assign('orders_fee_count',   	$list['orders_fee_count']);  
	$smarty->assign('cake_type',   			$list['cake_type']);  
	$smarty->display('finan_card_list.html');
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
	$smarty->assign('orders_fee_count',   	$list['orders_fee_count']);
	$smarty->assign('cake_type',   			$list['cake_type']);
	 
    make_json_result($smarty->fetch('finan_card_list.html'), '', array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}
elseif ($_REQUEST['act'] == 'batch_operate')
{
    //admin_priv('order_check');
    $sn   = $_REQUEST['order_id'];    
    $orders_id = explode(',', $sn);	

    foreach($orders_id as $order)
	{
		$sql = "insert into order_finance (order_id,ctatus,cadmin) values ($order,2,'".$_SESSION['admin_id']."')";
		$db_write->query($sql);
	}
	exit;		   
	

}
elseif ($_REQUEST['act'] == 'ud')
{
	$order = $_REQUEST['id'];
    $sql = "insert into order_finance (order_id,ctatus,cadmin) values ($order,2,'".$_SESSION['admin_id']."')";
	//make_json_result($sql);
	$db_write->query($sql);	
    exit;
	
}



function order_list()
{
	$filter['status']   = empty($_REQUEST['status'])   ? '' : intval($_REQUEST['status']);
	$filter['otatus']   = empty($_REQUEST['otatus'])   ? '' : intval($_REQUEST['otatus']);				
	$filter['pay']   	= empty($_REQUEST['pay'])      ? '' : trim($_REQUEST['pay']);		
	$filter['sdate']    = empty($_REQUEST['sdate'])    ? '' : trim($_REQUEST['sdate']);
    $filter['order_sn'] = empty($_REQUEST['order_sn']) ? '' : trim($_REQUEST['order_sn']);
    $filter['print_sn'] = empty($_REQUEST['print_sn']) ? '' : trim($_REQUEST['print_sn']);
	$filter['turn']     = empty($_REQUEST['turn'])     ? 0  : intval($_REQUEST['turn']);
	$filter['station']  = empty($_REQUEST['station'])  ? '' : trim($_REQUEST['station']);
	$filter['card_id']  = empty($_REQUEST['card_id'])  ? '' : trim($_REQUEST['card_id']);
    $filter['page']     = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);	
		
    $filter['sort_by']  = empty($_REQUEST['sort_by']) ? 'print_sn' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'ASC' : trim($_REQUEST['sort_order']);
			
	//$where = " where agency_id=3 and order_amount >0 and pay_id>1 and pay_id<5 and order_status=1 ";
	$where = " where agency_id=3 and  order_status=1 and bonus>160 ";
	if($filter['sdate'])
	{
		$where .= " and best_time > '".$filter['sdate']."' and best_time < '".$filter['sdate']." 23:30:30' ";
	}
	
	if($filter['print_sn'])
	{
	   $where .= "and p.print_sn = '".$filter['print_sn']."' ";
	}
	if($filter['status'] == 2)
	{
	   $where .= " and c.ctatus= 2 ";
	}
	if($filter['status'] == 1)
	{
	   $where .= " and (c.ctatus= 1 or c.ctatus is null) ";
	}
	if($filter['turn'])
	{
	   $where .= " and c.turn = '".$filter['turn']."' ";
	}
	if($filter['otatus'])
	{
	   $where .= " and o.order_status = '".$filter['otatus']."' ";
	}
	if($filter['station'] && $filter['station'] != 100 )
	{
	   $where .= " and d.station_id = '".$filter['station']."' ";
	}
    if($_SESSION['station'] >0)
    {
       $where .= " and d.station_id = '".$_SESSION['station']."' ";	
    }
	if($filter['order_sn'])
	{
		$where = " where agency_id=3 and  order_status=1 and bonus>160 ";
		$where .= "and order_sn like '%".$filter['order_sn']."' ";
		$where .= " and best_time > '".$filter['sdate']."' and best_time < '".$filter['sdate']." 23:30:30' ";
	}
	if($filter['card_id']){
		$where = " where agency_id=3 and  order_status=1 and bonus>160 ";
		$where .= "and to_buyer like '%".$filter['card_id']."%' ";

	}		

	$size = 30;	
	$sql = "select count(1) ".
	       "from order_genid as a ".
		   "left join ecs_order_info as o on a.order_id=o.order_id ".
	       "left join order_finance as c on a.order_id=c.order_id ".
		   "left join print_log_bt as p on a.order_id=p.order_id ".$where;

    $record_count   = $GLOBALS['db_read']->getOne($sql);
    $page_count     = $record_count > 0 ? ceil($record_count / $size) : 1;

	$sql = "select o.order_id,o.order_sn,o.pay_name,o.to_buyer,".
	       "o.bonus,p.print_sn,c.ctatus ".
	       "from order_genid as a ".
		   "left join ecs_order_info as o on a.order_id=o.order_id ".
	       "left join order_finance as c on a.order_id=c.order_id ".
		   "left join print_log_bt as p on a.order_id=p.order_id ".$where.
		   " order by print_sn ".
		   " LIMIT " . ($filter['page'] - 1) * $size . ",$size";


	$res = $GLOBALS['db_read']->GetAll($sql);
    $orders_fee_count = 0;            
    foreach($res as $key => $val)
	{
		$res[$key]['bonus'] = floatval($val['bonus']);
		$preg = preg_match_all("/\d+/", $val['to_buyer'],$arr);
		//echo "<pre>";print_r($arr);
	
		
	}
    $arr = array('orders' => $res, 'filter' => $filter, 'page_count' => $page_count,'record_count' => $record_count);

    return $arr;
}

?>