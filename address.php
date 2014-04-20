<?php

require(dirname(__FILE__) . '/includes/init.php');

$_REQUEST['act'] = empty($_REQUEST['act']) ? 'list' : trim($_REQUEST['act']);
if ($_REQUEST['act'] == 'list')
{
    $smarty->assign('ur_here',     '用户地址管理');
    $smarty->assign('full_page',   1);
	$list = address_list();
    //echo '<pre>';print_r($list);echo '</pre>';

    $smarty->assign('record_count', 	$list['record_count']);
    $smarty->assign('page_count',   	$list['page_count']);
    $smarty->assign('filter',       	$list['filter']);	
	$smarty->assign('address',   		$list['list']);  
	$smarty->display('address_list.html');
}
elseif ($_REQUEST['act'] == 'query')
{
    $list = address_list();
    $smarty->assign('record_count', 	$list['record_count']);
    $smarty->assign('page_count',   	$list['page_count']);
    $smarty->assign('filter',       	$list['filter']);
	$smarty->assign('address',   		$list['list']); 
    make_json_result($smarty->fetch('address_list.html'), '', array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}

function address_list()
{
    if (!empty($_GET['is_ajax']) && $_GET['is_ajax'] == 1)
    {
       $_REQUEST['address']  = json_str_iconv($_REQUEST['address']);
       $_REQUEST['to_buyer'] = json_str_iconv($_REQUEST['to_buyer']);
       $_REQUEST['pay_note'] = json_str_iconv($_REQUEST['pay_note']);
       $_REQUEST['wsts']    = json_str_iconv($_REQUEST['wsts']);
    }    
	
	$filter['sdate']    = empty($_REQUEST['sdate'])    ? '' : trim($_REQUEST['sdate']);
	$filter['edate']    = empty($_REQUEST['edate'])    ? '' : trim($_REQUEST['edate']);
	$filter['order_sn'] = empty($_REQUEST['order_sn']) ? '' : trim($_REQUEST['order_sn']);
	$filter['sdatead']  = empty($_REQUEST['sdatead'])  ? '' : trim($_REQUEST['sdatead']);
	$filter['edatead']  = empty($_REQUEST['edatead'])  ? '' : trim($_REQUEST['edatead']);
    $filter['status']   = empty($_REQUEST['status'])   ? 9  : intval($_REQUEST['status']);	
    $filter['station']  = empty($_REQUEST['station'])  ? 0  : intval($_REQUEST['station']);	
	$filter['turn']     = empty($_REQUEST['turn'])     ? 0  : intval($_REQUEST['turn']);
	$filter['address']  = empty($_REQUEST['address'])  ? '' : trim($_REQUEST['address']);
	$filter['ordertel'] = empty($_REQUEST['ordertel']) ? '' : trim($_REQUEST['ordertel']);
	$filter['prints']   = empty($_REQUEST['prints'])   ? '' : trim($_REQUEST['prints']);
	$filter['pay_note'] = empty($_REQUEST['pay_note']) ? '' : trim($_REQUEST['pay_note']);
	$filter['pay_name'] = empty($_REQUEST['pay_name']) ? 0  : intval($_REQUEST['pay_name']);
	$filter['to_buyer'] = empty($_REQUEST['to_buyer']) ? '' : trim($_REQUEST['to_buyer']);
	$filter['wsts']     = empty($_REQUEST['wsts'])     ? '' : trim($_REQUEST['wsts']);
    $filter['page']     = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);	
		
	$where = " where country=441 ";
	
	if($filter['sdate'])
	{
		$where .= " and LEFT(a.best_time,10) >='".$filter['sdate']."' ";
	}
	if($filter['edate'])
	{
		$where .= " and LEFT(a.best_time,10) <='".$filter['edate']."' ";
	}
	if($filter['order_sn'])
	{
	   $where .= " and a.order_sn like '%".$filter['order_sn']."' ";
	}
	if($filter['sdatead'])
	{
	   $where .= " and a.add_time >= '".strtotime($filter['sdatead'])."'";
	}
	if($filter['edatead'])
	{
	   $where .= " and a.add_time <= '".(strtotime($filter['edatead'])+ 86400)."'";
	}
	if($filter['status'] !=9)
	{
	   $where .= " and a.order_status = '".$filter['status']."' ";
	}
	if($filter['station'])
	{
	   $where .= " and b.shipping_station_id = '".$filter['station']."' ";
	}
	if($filter['turn'])
	{
	   $where .= " and b.shipping_timeplan_id = '".$filter['turn']."' ";
	}
	if($filter['address'])
	{
	   $where .= " and a.address like '%".$filter['address']."%'";
	}
	if($filter['ordertel'])
	{
	   $where .= " and a.ordertel like '%".$filter['ordertel']."%' ";
	}
	if($filter['prints'] == 1)
	{
	   $where .= " and a.printtimes > 1 ";
	}
	if($filter['prints'] == 2)
	{
	   $where .= " and a.printtimes = 0 ";
	}
	if($filter['pay_name'])
	{
	   $where .= " and a.pay_id =".$filter['pay_name']." ";
	}
	if($filter['pay_note'])
	{
	   $where .= " and a.pay_note = '".$filter['pay_note']."'";
	}

    $sql = "select * from ecs_user_address ".$where ."order by address_id desc limit 15"; 
    $rs = $GLOBALS['db_read']->getAll($sql);

    $arr = array('list' => $rs, 'filter' => $filter, 'page_count' => 1, 'record_count' => 50);

    return $arr;

}

?>