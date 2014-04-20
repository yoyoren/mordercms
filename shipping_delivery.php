<?php
require(dirname(__FILE__) . '/includes/init.php');
admin_priv('st_del');
$_REQUEST['act'] = empty($_REQUEST['act']) ? 'list' : trim($_REQUEST['act']);
//初始化城市编号,格式如 ：IN(441，443)
$city_code = db_create_in(array_keys($_SESSION['city_arr']));
if ($_REQUEST['act'] == 'list')
{
    

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
	
	$_REQUEST['status'] = '1';
	$_REQUEST['orderstatus'] = 1;
    $_REQUEST['sdate'] = date('Y-m-d');

	$list = order_list();
	//echo '<pre>';print_r($list['senders']);echo '</pre>';
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
    $smarty->assign('filter',       $list['filter']);	
	$smarty->assign('order_list',   $list['orders']);  
	$smarty->assign('employees',	$list['senders']);
	$smarty->assign('ur_here',     '配送任务');
    $smarty->assign('full_page',   1);
    $smarty->assign('timeplan',   getTurn());
	$smarty->display('delivery_list.htm');
}
/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $list = order_list();
	
    $smarty->assign('order_list',     $list['orders']);
    $smarty->assign('record_count',   $list['record_count']);
    $smarty->assign('page_count',     $list['page_count']);
    $smarty->assign('filter',         $list['filter']);
	$smarty->assign('employees',	  $list['senders']);
	 
    make_json_result($smarty->fetch('delivery_list.htm'), '', array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}
//手动分配 配送员
elseif ($_REQUEST['act'] == 'sender')
{
    $order_id   = $_REQUEST['order_id'];      
    $order_id_list = explode(',', $order_id);

    if (empty($_REQUEST['sender']))
	{
	    sys_msg('请选择配送员!', 1);
	}
	else
	{
		$sender = intval($_REQUEST['sender']);
		foreach ($order_id_list as $order)
		{
			$sql = "update order_delivery set employee_id = '$sender' where order_id = '$order'";
			$db_write->query($sql);
		}
	}
    $url = 'shipping_delivery.php?act=query&' . str_replace('act=sender', '', $_SERVER['QUERY_STRING']);
    los_header("Location: $url\n");
    exit;
}
//批量审核
elseif ($_REQUEST['act'] == 'print')
{
    $order_id   = $_REQUEST['order_id'];      
    $order_id_list = explode(',', $order_id);
    foreach ($order_id_list as $order)
    {
        $db_write->query("UPDATE order_delivery SET status =2,admind = '".$_SESSION['admin_id']."',out_time = '".time()."' WHERE order_id = '$order'");
    }
    
	$url = 'shipping_delivery.php?act=query&' . str_replace('act=print', '', $_SERVER['QUERY_STRING']);
	
	los_header("Location: $url\n");
    exit;

}
elseif ($_REQUEST['act'] == 'pack')
{
    /* 检查权限 
    admin_priv('26');

	$filter['bdate']      = empty($_REQUEST['date']) ? 0 : trim($_REQUEST['date']);
	$filter['turn']      = empty($_REQUEST['turn']) ? 0 : trim($_REQUEST['turn']);
	$filter['station']   = empty($_REQUEST['station']) ? '' : trim($_REQUEST['station']);
	$employee 			 = empty($_REQUEST['employee']) ? '' : trim($_REQUEST['employee']);
	//echo '<pre>';print_r($_REQUEST);echo '</pre>';
	
	$sql = "SELECT shipping_station_name FROM  view_shipping_deliveryplan WHERE shipping_station_name = '".$filter['station']."' 
			AND id = '".$employee."'";
	//echo $sql."<br>";
	$res = $db_read->getRow($sql);
	//echo '<pre>';print_r($res);echo '</pre>';
	if (!empty($res) || $filter['station'] == '未分站')
	{
		//echo '<pre>';print_r($filter);echo '</pre>';
		if ($filter['station'] == '未分站')
		{
			$where = 'and shipping_station_name is null';
		}
		else
		{
			$where = "and shipping_station_name = '".$filter['station']."'";
		}
		$sql = "select id from view_shipping_orders_delivery where bdate = '".$filter['bdate']."' 
				and shipping_timeplan_name = '".$filter['turn']."' ".$where;
		$res = $db_read->getAll($sql);
		//echo '<pre>';print_r($res);echo '</pre>';
		foreach ($res as $value)
		{
			$sql = "update shipping_orders_delivery set shipping_deliveryplan_id = '".$employee."' WHERE id = ".$value['id'];
			$result = $db_write->query($sql);
			los_header("Location: shipping_delivery.php?act=list");
		}
		
	}
	else
	{
		echo "该配送员不属于[".$filter['station']."]配送站";
	}	*/
}
/*------------------------------------------------------ */
//-- 删除订单
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove_order')
{
    //admin_priv('26');

   /* $order_id = intval($_REQUEST['id']);	
    $res = $GLOBALS['db_write']->query("update order_delivery set status=1 WHERE order_id = '$order_id'");
    if ($res)
    {
        $url = 'shipping_delivery.php?act=query&' . str_replace('act=remove_order', '', $_SERVER['QUERY_STRING']);
        los_header("Location: $url\n");
        exit;
    }
    else
    {
        make_json_error('删除出错!请检查！');
    }*/
}
//批量删除
elseif ($_REQUEST['act'] == 'delete')
{
    $order_id = $_REQUEST['order_id'];     
	$order_id_list = explode(',', $order_id);
        
	foreach ($order_id_list as $order)
    {
        $db_write->query("update order_delivery set status=1 WHERE order_id = '$order'");
    }
    $url = 'shipping_delivery.php?act=query&' . str_replace('act=delete', '', $_SERVER['QUERY_STRING']);
    los_header("Location: $url\n");
    exit;
}
//单个审核
elseif ($_REQUEST['act'] == 'check_eg')
{
    //admin_priv('26');

	$order = intval($_REQUEST['id']);
	
	$sql = "UPDATE order_delivery SET status =2,admind = '".$_SESSION['admin_id']."',out_time = '".time()."' WHERE order_id = '$order'";
	$res = $db_write->query($sql);
    if ($res)
    {
		$url = 'shipping_delivery.php?act=query&' . str_replace('act=check_eg', '', $_SERVER['QUERY_STRING']);
		los_header("Location: $url\n");
        exit;
    }
    else
    {
        make_json_error('审核出错!请检查！');
    }
	
}
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
	$filter['sdate'] 		= empty($_REQUEST['sdate']) ? 		'' : trim($_REQUEST['sdate']);
	$filter['order_sn']  	= empty($_REQUEST['order_sn']) ? 	'' : trim($_REQUEST['order_sn']);
	$filter['turn']      	= empty($_REQUEST['turn']) ? 		0  : intval($_REQUEST['turn']);
	$filter['station']   	= empty($_REQUEST['station']) ? 	'' : intval($_REQUEST['station']);
	$filter['employee']   	= empty($_REQUEST['employee']) ? 	'' : intval($_REQUEST['employee']);
	$filter['pack_no']   	= empty($_REQUEST['pack_no']) ? 	'' : trim($_REQUEST['pack_no']);
	$filter['print_sn'] = empty($_REQUEST['print_sn']) ? '' : trim($_REQUEST['print_sn']);
	$filter['status']   	= empty($_REQUEST['status']) ? 		'' : intval($_REQUEST['status']);
	$filter['orderstatus']   = empty($_REQUEST['orderstatus'])   ? ''  : intval($_REQUEST['orderstatus']);		
	
    $filter['page'] = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);	
			
	$where = " where o.country ".$GLOBALS['city_code']." ";
	
	if($filter['sdate'])
	{
	   $where .= " and best_time > '".$filter['sdate']."' and best_time < '".$filter['sdate']." 23:30:00'";
	}
	if($filter['turn'])
	{
	   $where .= " and c.turn = '".$filter['turn']."' ";
	}
	if($filter['employee'])
	{
	   $where .= " and employee_id = '".$filter['employee']."' ";
	}
	if($filter['pack_no'])
	{
	   $where .= " and d.route_name = '".$filter['pack_no']."' ";
	}
	if($filter['status'])
	{
	   $where .= " and a.status = '".$filter['status']."' ";
	}
	if($filter['orderstatus'] ==0)
	{
	   $where .= " and order_status =0 ";
	}
	if($filter['orderstatus'] ==1)
	{
	   $where .= " and order_status =1 ";
	}
	if($filter['orderstatus'] ==2)
	{
	   $where .= " and order_status =2 ";
	}
	if($filter['orderstatus'] ==3)
	{
	   $where .= " and order_status =3 ";
	}
	if($filter['orderstatus'] ==4)
	{
	   $where .= " and order_status =4 ";
	}
	if($filter['order_sn'])
	{
		if(str_len($filter['order_sn'])==5){
			$where .= " and order_sn like '%".$filter['order_sn']."' ";
		}else{
			$where = " where order_sn = '".$filter['order_sn']."' ";
		}
	   
	}
	if($filter['station'] && $filter['station'] != 100 )
	{
	   $where .= " and d.station_id = '".$filter['station']."' ";
	   
	}
	if($filter['print_sn'])
	{
	   $where .= "and p.print_sn = '".$filter['print_sn']."' ";
	}
	if($filter['station'] == 100)
	{
	   $where .= " and shipping_station_name is null ";		
	}
	if($filter['sort_by'] =='shipping_station_name')
	{
		 $orderby = "shipping_pack_no,best_time ";
	}
	elseif($filter['sort_by'] =='shipping_pack_no')
	{
		$orderby = "shipping_station_name, best_time ";
	}
	else
	{
		 $orderby = "shipping_station_name, shipping_pack_no ";		
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
    $page_count     = $record_count > 0 ? ceil($record_count / $size) : 1;
	
	$sql = "select a.order_id,p.print_sn,o.order_sn,o.city,o.address,o.best_time,c.*,d.*,s.station_name,s.station_code,a.status,h.name as employee_name ".
	       "from order_delivery as a ".
		   "left join ecs_order_info as o on a.order_id=o.order_id ".
	       "left join order_dispatch as c on a.order_id=c.order_id ".
			"left join print_log_x as p on a.order_id=p.order_id ".
		   "left join hr_employees as h on a.employee_id=h.id ".
		   "left join ship_route as d on c.route_id=d.route_id ".
		   "left join ship_station as s on d.station_id=s.station_id ".$where.
		   "order by d.route_name,c.turn,best_time ".
		   "LIMIT " . ($filter['page'] - 1) * $size . ",$size";
	$res = $GLOBALS['db_read']->GetAll($sql);
	
	
	$sql = "SELECT a.sender,b.name FROM delivery_plan as a,hr_employees as b WHERE a.sender=b.id and bdate = '".$filter['sdate']."' 
			and a.station_id = '".$filter['station']."'";
	$res2 = $GLOBALS['db_read']->GetAll($sql);	

	foreach($res as $key=> $val)
    {
	    $res[$key]['i'] = $key +1;
	    $res[$key]['address'] = region_name($val['city']).' '.$val['address'];
	}	
    $arr = array('orders' => $res, 'filter' => $filter, 'page_count' => $page_count, 'record_count' => $record_count,'senders' => $res2);

    return $arr;  
}


?>
