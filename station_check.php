<?php
/**
 * 配送结算
 * $Author: bisc $
 * $Id: finance.php 
*/

require(dirname(__FILE__) . '/includes/init.php');
admin_priv("st_set");
$_REQUEST['act'] = empty($_REQUEST['act']) ? 'list' : trim($_REQUEST['act']);
//初始化城市编号
$city_code =db_create_in(array_keys($_SESSION['city_arr']));

if ($_REQUEST['act'] == 'list')
{
    $smarty->assign('ur_here',     '配送结算');
    $smarty->assign('full_page',   1);
	
	$sql = "SELECT station_id,station_name FROM ship_station  where station_id = '".intval($_SESSION['station'])."'";
	$stations = $db_read->getAll($sql);
   
	if($stations)
	{ 
		$smarty->assign('Current','Current');
		$smarty->assign('stations',   $stations);
		$_REQUEST['station'] = $stations[0]['station_id'];
		$sql = "select id as employee_id,name as employee_name from hr_employees where station_id = '".$stations[0]['station_id']."' and flag=1";
		$arr = $db_read->getAll($sql);
		$smarty->assign('employee_list',   $arr);	
	}
	else
	{
		$stations = $db_read->getAll("SELECT station_id,station_name FROM ship_station where city_code $city_code ");
		$smarty->assign('stations',   $stations);
	}
	
	$_REQUEST['sdate'] = date('Y-m-d');
    $_REQUEST['status'] = '2';
	//$_REQUEST['pay'] = '1';
	$_REQUEST['orderstatus'] = 1;
	$list = order_list();

    $smarty->assign('record_count', 		$list['record_count']);
    $smarty->assign('page_count',   		$list['page_count']);
    $smarty->assign('filter',       		$list['filter']);	
	$smarty->assign('order_list',   		$list['orders']);  
	$smarty->assign('orders_fee_count',   	$list['ofee']);  
	$smarty->assign('cake_type',   			$list['sums']);  
	$smarty->assign('turn',getTurn());
	$smarty->display('station_check_list.htm');
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
	$smarty->assign('orders_fee_count',   	$list['ofee']);
	$smarty->assign('cake_type',   			$list['sums']);
	 
    make_json_result($smarty->fetch('station_check_list.htm'), '', array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}
elseif ($_REQUEST['act'] == 'batch_operate')
{
    $sn   = $_REQUEST['order_id'];    
    $orders_id = explode(',', $sn);	
	
    for ($i=0;$i<count($orders_id);$i++)
	{
		$sql = "UPDATE order_delivery SET status=3 WHERE order_id = '".$orders_id[$i]."'";
		$db_write->query($sql);
		//更改订单支付状态
		$sql = "update ecs_order_info set pay_status=2 where order_id='".$orders_id[$i]."'";
		$db_write->query($sql);
		
		
	}
	exit;
}
elseif ($_REQUEST['act'] == 'ud')
{
	$filter['id']  = $_REQUEST['id'];
	$sql = "update order_delivery set status=3 where order_id = '".$filter['id']."'";
	//make_json_result($sql);
	$db_write->query($sql);	

	$sql = "update ecs_order_info set pay_status=2 where order_id='".$filter['id']."'";
	$re= $db_write->query($sql);

    exit;
	
}
/*------------------------------------------------------ */
//-- 下属配送员列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'employee')
{
	require(ROOT_PATH . 'includes/cls_json.php');
	
	$stn = intval($_GET['stn']);
	$sql = "select id as employee_id,name as employee_name from hr_employees where station_id = '".$stn."' and flag=1";
	$arr = $db_read->getAll($sql);
	
	$json = new JSON;
	echo $json->encode($arr);
}


function order_list()
{
   if (!empty($_GET['is_ajax']) && $_GET['is_ajax'] == 1)
   {
        $_REQUEST['status'] = json_str_iconv($_REQUEST['status']);
        $_REQUEST['station'] = json_str_iconv($_REQUEST['station']);
        $_REQUEST['sender'] = json_str_iconv($_REQUEST['sender']);
   }    
		    
	$filter['status']   = empty($_REQUEST['status'])   ? '' : trim($_REQUEST['status']);
	$filter['otatus']   = empty($_REQUEST['otatus'])   ? 9  : intval($_REQUEST['otatus']);		
	$filter['sender']   = empty($_REQUEST['sender'])   ? '' : trim($_REQUEST['sender']);		
	$filter['pay']   	= empty($_REQUEST['pay'])      ? '' : trim($_REQUEST['pay']);		
	$filter['sdate']    = empty($_REQUEST['sdate'])    ? '' : trim($_REQUEST['sdate']);
    $filter['order_sn'] = empty($_REQUEST['order_sn']) ? '' : trim($_REQUEST['order_sn']);
    $filter['print_sn'] = empty($_REQUEST['print_sn']) ? '' : trim($_REQUEST['print_sn']);
	$filter['turn']     = empty($_REQUEST['turn'])     ? 0  : intval($_REQUEST['turn']);
	$filter['station']  = empty($_REQUEST['station'])  ? '' : trim($_REQUEST['station']);
    $filter['page']     = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);	
		
    $filter['sort_by']  = empty($_REQUEST['sort_by']) ? 'best_time' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'ASC' : trim($_REQUEST['sort_order']);

			
	//$where = " where agency_id=3 and order_amount >0 and pay_id>1 and pay_id<5 and order_status=1 ";
	$where = " where country ".$GLOBALS['city_code']." and  order_status=1 ";
	if($filter['sdate'])
	{
		//$where .= " and left(best_time,10) =  '".$filter['sdate']."'";
		$where .= " and best_time > '".$filter['sdate']."' and best_time < '".$filter['sdate']." 23:30:00' ";
	}
	if($filter['order_sn'])
	{
	   $where .= "and order_sn = '".$filter['order_sn']."' ";
	}
	if($filter['print_sn'])
	{
	   $where .= "and p.print_sn = '".$filter['print_sn']."' ";
	}
	if($filter['status'])
	{
	   $where .= " and a.status= '".$filter['status']."' ";
	}
	if($filter['pay'])
	{
	   $where .= "and pay_id = '".$filter['pay']."' ";
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
    if($_SESSION['station'] >0)
    {
       $where .= " and d.station_id = '".$_SESSION['station']."' ";	
    }		

	$size = 30;	
	$sql = "select count(1) ".
	       "from order_delivery as a ".
		   "left join ecs_order_info as o on a.order_id=o.order_id ".
	       "left join order_dispatch as c on a.order_id=c.order_id ".
		   "left join print_log_x as p on a.order_id=p.order_id ".
		   "left join hr_employees as h on a.employee_id=h.id ".
		   "left join ship_route as d on c.route_id=d.route_id ".
		   "left join ship_station as s on d.station_id=s.station_id ".$where;

    $record_count   = $GLOBALS['db_read']->getOne($sql);

	$sql = "select o.order_amount,o.pay_note ".
	       "from order_delivery as a ".
		   "left join ecs_order_info as o on a.order_id=o.order_id ".
	       "left join order_dispatch as c on a.order_id=c.order_id ".
		   "left join print_log_x as p on a.order_id=p.order_id ".
		   "left join hr_employees as h on a.employee_id=h.id ".
		   "left join ship_route as d on c.route_id=d.route_id ".
		   "left join ship_station as s on d.station_id=s.station_id ".$where
		   ." and o.pay_id>1 and o.pay_id < 6";
		   //." and o.pay_id>1 and o.pay_id < 5";

    $pos = $GLOBALS['db_read']->getAll($sql);
	$ofee = $sums = 0;
	foreach($pos as $vl)
	{
		if(strpos($vl['pay_note'],'OS'))
		{
		   $ofee += floatval(substr($vl['pay_note'],7,8));
		}
		if($vl['order_amount']<0){
			$vl['order_amount']=0;
		}
		$sums += floatval($vl['order_amount']);  
	}
	
	$sums = $sums - $ofee;
	
    $page_count     = $record_count > 0 ? ceil($record_count / $size) : 1;

	$sql = "select *,o.order_id,a.status as status,p.print_sn ".
	       "from order_delivery as a ".
		   "left join ecs_order_info as o on a.order_id=o.order_id ".
	       "left join order_dispatch as c on a.order_id=c.order_id ".
		   "left join print_log_x as p on a.order_id=p.order_id ".
		   "left join hr_employees as h on a.employee_id=h.id ".
		   "left join ship_route as d on c.route_id=d.route_id ".
		   "left join ship_station as s on d.station_id=s.station_id ".$where.
		   " order by pay_name ".
		   " LIMIT " . ($filter['page'] - 1) * $size . ",$size";
	$res = $GLOBALS['db_read']->GetAll($sql);           
    foreach($res as $key => $val)
	{
		if($val['order_amount']<0){
			$val['order_amount']=0;
		}
	    //$res[$key]['order_amount'] = ($val['pay_id'] == 1 || $val['pay_id'] >4) ? 0 : floatval($val['order_amount']);
	    $res[$key]['order_amount'] = ($val['pay_id'] == 1) ? 0 : floatval($val['order_amount']);
	    
	}
	
    $arr = array('orders' => $res, 'filter' => $filter, 'page_count' => $page_count,'record_count' => $record_count,'ofee' => $ofee,'sums' => $sums);

    return $arr;
}

?>