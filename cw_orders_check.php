<?php
/**
 * 财务审核
 * $Author: bisc $
 * $Id: cw_orders_check.php 
*/

require(dirname(__FILE__) . '/includes/init.php');

$_REQUEST['act'] = empty($_REQUEST['act']) ? 'list' : trim($_REQUEST['act']);

if ($_REQUEST['act'] == 'list')
{
    //admin_priv('36');
    $smarty->assign('ur_here',     '财务现结审核');
    $smarty->assign('full_page',   1);


    $_LANG['os']['0'] = '未确认';
	$_LANG['os']['1'] = '已确认';
	$_LANG['os']['2'] = '取消';
	$_LANG['os']['3'] = '无效';
	$_LANG['os']['4'] = '退货';
	
	$_LANG['ss']['0'] = '未发货';
	$_LANG['ss']['1'] = '已发货';
	$_LANG['ss']['2'] = '收货确认';
	
	$_LANG['ps']['0'] = '未付款';
	$_LANG['ps']['1'] = '付款中';
	$_LANG['ps']['2'] = '已付款';	
	$_REQUEST['sdate'] = $_REQUEST['edate'] = date('Y-m-d');
    $_REQUEST['status'] = '2';
	$_REQUEST['orderstatus'] = 1;
	$list = order_list();

    $smarty->assign('record_count', 		$list['record_count']);
    $smarty->assign('page_count',   		$list['page_count']);
    $smarty->assign('filter',       		$list['filter']);	
	$smarty->assign('order_list',   		$list['orders']);  
	$smarty->assign('orders_fee_count',   	$list['orders_fee_count']);  
	$smarty->assign('cake_type',   			$list['cake_type']);  
	$smarty->display('cw_orders_check_list.htm');
}
/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    /* 检查权限 */
    admin_priv('36');
    $list = order_list();
	$smarty->assign('actionlink',  array('text' => '导出数据','href'=>'check_down.php?act=download&filename=财务审核数据&start_date=' . $list['filter']['sdate'] . '&order_sn='.$list['filter']['order_sn'].'&status='.$list['filter']['status'].'&pay='.$list['filter']['pay'].'&orderman='.$list['filter']['orderman'].'&turn='.$list['filter']['turn'].'&station='.$list['filter']['station'].'&orderstatus='.$list['filter']['orderstatus']));

    $smarty->assign('record_count', 		$list['record_count']);
    $smarty->assign('page_count',   		$list['page_count']);
    $smarty->assign('filter',       		$list['filter']);
	$smarty->assign('order_list',   		$list['orders']); 
	$smarty->assign('orders_fee_count',   	$list['orders_fee_count']);
	$smarty->assign('cake_type',   			$list['cake_type']);
	 
    make_json_result($smarty->fetch('cw_orders_check_list.htm'), '', array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}
elseif ($_REQUEST['act'] == 'batch_operate')
{
    /* 检查权限 */
    admin_priv('36');
	/* 取得参数 */
    $sn   = $_REQUEST['id'];        // 订单id（逗号格开的多个订单id）
    $orders_id = explode(',', $sn);	
	$remarks = json_str_iconv(trim($_REQUEST['remarks']));
	$remark = explode(',', $remarks);
	$cake_types = $_REQUEST['cake_types'];
	$cake_type = explode(',',$cake_types);
	$card_counts = $_REQUEST['card_counts'];
	$card_count = explode(',',$card_counts);

    for ($i=0;$i<count($orders_id);$i++)
	{
		$sql = "update cw_orders_process set check_status=1,check_remarks = '".$remark[$i]."',check_time='".
				date('Y-m-d H:i:s')."',check_account_id='".$_SESSION['admin_id']."' where calling_order_id = '".$orders_id[$i]."'";
		$db_write->query($sql);
		//make_json_result($sql);
		$res = $db_read->getOne("select count(*) from ecs_orders_process_type where order_id = '".$orders_id[$i]."'");
		if($res)
		{
			$update_sql = "update ecs_orders_process_type set cake_type = '".$cake_type[$i]."',card_count = '".$card_count[$i]."' where order_id = '".$orders_id[$i]."'";
			//make_json_result($update_sql);
			$db_write->query($update_sql);
		}
		else
		{
			$insert_sql = "insert into ecs_orders_process_type(order_id,cake_type,card_count) values('".$orders_id[$i]."','".$cake_type[$i]."','".$card_count[$i]."')";
			//make_json_result($insert_sql);
			$db_write->query($insert_sql);
		}
	}
    
	$url = 'cw_orders_check.php?act=query&url=url&' . str_replace('act=batch_operate', '', $_SERVER['QUERY_STRING']);
	los_header("Location: $url\n");
	exit;		   
	

}
elseif ($_REQUEST['act'] == 'ud')
{
    /* 检查权限 */
    admin_priv('36');

	$orderid  = $_REQUEST['id'];
	$cake_type =  trim($_REQUEST['cake_type']);
	$card_count = trim($_REQUEST['card_count']);
	$remarks = json_str_iconv(trim($_REQUEST['remarks']));
	$sql = "update cw_orders_process set check_status=1,check_remarks = '$remarks',check_time='".
		    date('Y-m-d H:i:s')."',check_account_id='".$_SESSION['admin_id']."' where calling_order_id = '".$orderid."'";
	$res = $db_read->getOne("select count(*) from ecs_orders_process_type where order_id = '$orderid'");
	if($res)
	{
		$update_sql = "update ecs_orders_process_type set cake_type = '$cake_type',card_count = '$card_count' where order_id = '$orderid'";
		//make_json_result($update_sql);
		$db_write->query($update_sql);
	}
	else{
		$insert_sql = "insert into ecs_orders_process_type(order_id,cake_type,card_count) values('$orderid','$cake_type','$card_count')";
		//make_json_result($insert_sql);
		$db_write->query($insert_sql);
	}
	//make_json_result($sql);
	$db_write->query($sql);	
    
	$url = 'cw_orders_check.php?act=query&url=url&' . str_replace('act=ud', '', $_SERVER['QUERY_STRING']);
    los_header("Location: $url\n");
    exit;
	
}
/*------------------------------------------------------ */
//-- 下属配送员列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'employee')
{
	require(ROOT_PATH . 'includes/cls_json.php');
	
	$station = trim($_REQUEST['station']);
	if($station)
	{
	   $sql = "select employee_id,employee_name from view_station_employee where shipping_station_id = ".$station;	
	}
	else
	{
	   $sql = "select employee_id,employee_name from view_station_employee ";
	}
	$arr = $db_read->getAll($sql);
	
	$json = new JSON;
	echo $json->encode($arr);
}

/*
* 取得调度单信息列表
*/
function order_list()
{
     if (!empty($_GET['is_ajax']) && $_GET['is_ajax'] == 1)
     {
         $_REQUEST['status'] = json_str_iconv($_REQUEST['status']);
            $_REQUEST['station'] = json_str_iconv($_REQUEST['station']);
            if(empty($_REQUEST['id']))
			{
				$_REQUEST['orderman'] = json_str_iconv(urldecode($_REQUEST['orderman']));
				$_REQUEST['pay_name'] = json_str_iconv(urldecode($_REQUEST['pay_name']));
				$_REQUEST['pay_note'] = json_str_iconv(urldecode($_REQUEST['pay_note']));
			}
			else
			{
				$_REQUEST['orderman'] = json_str_iconv($_REQUEST['orderman']);
				$_REQUEST['pay_name'] = json_str_iconv($_REQUEST['pay_name']);
				$_REQUEST['pay_note'] = json_str_iconv($_REQUEST['pay_note']);
			}
        }    

	$filter['sdate']    = empty($_REQUEST['sdate'])    ? '' : trim($_REQUEST['sdate']);
	$filter['pay']   	= empty($_REQUEST['pay'])      ? '' : trim($_REQUEST['pay']);		
    $filter['order_sn'] = empty($_REQUEST['order_sn']) ? '' : trim($_REQUEST['order_sn']);
    $filter['orderman'] = empty($_REQUEST['orderman']) ? '' : trim($_REQUEST['orderman']);
    $filter['pay_name'] = empty($_REQUEST['pay_name']) ? '' : trim($_REQUEST['pay_name']);
    $filter['pay_note'] = empty($_REQUEST['pay_note']) ? '' : trim($_REQUEST['pay_note']);
	$filter['turn']     = empty($_REQUEST['turn'])     ? 0  : intval($_REQUEST['turn']);
	$filter['station']     = empty($_REQUEST['station'])     ? 0  : intval($_REQUEST['station']);
	$filter['status']   = empty($_REQUEST['status'])   ? '' : trim($_REQUEST['status']);
	$filter['orderstatus']   = empty($_REQUEST['orderstatus'])   ? ''  : intval($_REQUEST['orderstatus']);		
    $filter['page']     = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);	
		
    $filter['sort_by']  = empty($_REQUEST['sort_by']) ? 'shipping_timeplan_name' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'ASC' : trim($_REQUEST['sort_order']);
			
	$where = " where 1 ";
	
	if($filter['sdate'])
	{
		//$where .= " and LEFT(best_time,10) ='".$filter['sdate']."' ";
		$where .= " and best_time > '".$filter['sdate']."' and best_time < '".$filter['sdate']." 23:30:00' ";
	}
	if($filter['order_sn'])
	{
	   $where .= " and order_sn like '%".$filter['order_sn']."%' ";
	}
	if($filter['orderman'])
	{
	   $where .= " and orderman like '%".$filter['orderman']."%' ";
	}
	if($filter['status'] ==2)
	{
	   //$where .= " and check_status = 2 ";
	}
	if($filter['status'] ==1)
	{
	   //$where .= " and check_status = 1 ";
	}
	if($filter['orderstatus'] ==0)
	{
	   $where .= " and order_status =0";
	}
	if($filter['orderstatus'] ==1)
	{
	   $where .= " and order_status =1";
	}
	if($filter['orderstatus'] ==2)
	{
	   $where .= " and order_status =2";
	}
	if($filter['orderstatus'] ==3)
	{
	   $where .= " and order_status =3";
	}
	if($filter['orderstatus'] ==4)
	{
	   $where .= " and order_status =4";
	}
	if($filter['turn'])
	{
	   $where .= " and shipping_timeplan_id = '".$filter['turn']."' ";
	}
	if($filter['station'])
	{
	   $where .= " and shipping_station_id = '".$filter['station']."' ";
	}
	if($filter['pay_name'])
	{
	   $where .= " and b.pay_name = '".$filter['pay_name']."' ";
	}
	if($filter['pay_note'])
	{
	   $where .= " and b.pay_note = '".$filter['pay_note']."' ";
	}
		
	if($filter['sort_by'] == 'shipping_timeplan_name' )
	{
		$orderby = "b.pay_name,b.pay_note,orderman ";
	}
	elseif($filter['sort_by'] == 'b.pay_name')
	{
		$orderby = "b.pay_note,shipping_timeplan_name,orderman";
	}
		
	$sql = "select * ".
	       "from order_genid as a ".
		   "left join ecs_order_info as o on a.order_id=o.order_id ".
	       "left join order_dispatch as c on a.order_id=c.order_id ".
		   "left join print_log_bt as d on a.order_id=d.order_id ".$where.' limit 30';
	$res = $GLOBALS['db_read']->GetAll($sql);

		



	
    $arr = array('orders' => $row, 'filter' => $filter, 'page_count' => 1, 'record_count' => 50,'orders_fee_count' => $orders_fee_count,'cake_type' => $cake_type);

    return $arr;
}

?>