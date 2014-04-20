<?php
/**
 * 配送排班
 * $Author: bisc $
 * $Id: delivery_plan.php 
*/
require(dirname(__FILE__) . '/includes/init.php');
admin_priv('st_sch');
$_REQUEST['act'] = empty($_REQUEST['act']) ? 'list' : trim($_REQUEST['act']);
//初始化城市编号
$city_code =db_create_in(array_keys($_SESSION['city_arr']));
if ($_REQUEST['act'] == 'list')
{
	$sql = "SELECT station_id,station_name FROM ship_station  where station_id = '".intval($_SESSION['station'])."'";
	$stations = $db_read->getAll($sql);
	if($stations)
	{
		$smarty->assign('Current','Current');
		$smarty->assign('stations',   $stations);
		$_REQUEST['station'] = $stations['0']['station_id'];	
	}
	else
	{
		$stations = $db_read->getAll("SELECT station_id,station_name FROM ship_station where city_code $city_code");
		$smarty->assign('stations',   $stations);
	}

	$_REQUEST['sdate'] = $_REQUEST['edate'] = date('Y-m-d');

	$list = plan_list();
   	
	
	$smarty->assign('delivery_list',  $list['delivery']);
    $smarty->assign('record_count',   $list['record_count']);
    $smarty->assign('page_count',     $list['page_count']);
    $smarty->assign('filter',         $list['filter']);
    $smarty->assign('full_page',   1);
    $smarty->assign('ur_here',     '配送排班');
    $smarty->assign('action_link', array('href'=>'delivery_plan.php?act=add', 'text' => '配送排班添加'));
	$smarty->display('deliveryplan_list.htm');
}
elseif ($_REQUEST['act'] == 'query')
{
    $list = plan_list();

    $smarty->assign('delivery_list',  $list['delivery']);
    $smarty->assign('record_count',   $list['record_count']);
    $smarty->assign('page_count',     $list['page_count']);
    $smarty->assign('filter',         $list['filter']);
    
    make_json_result($smarty->fetch('deliveryplan_list.htm'), '', array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}
elseif ($_REQUEST['act'] == 'add')
{
	$sql = "SELECT station_id,station_name FROM ship_station  where station_id = ".intval($_SESSION['station']);
	$station = $db_read->getAll($sql);

	if($station)
	{ 
		$smarty->assign('Current','Current');
		$smarty->assign('stations',   $station);
		$sql = "select id as employee_id,name as employee_name from hr_employees where flag=1 and station_id = '".$station['0']['station_id']."'";
		$arr = $db_read->getAll($sql);
		$smarty->assign('employee_list' ,$arr);	
		$_REQUEST['station'] = $station['0']['station_id'];	
	}
	else
	{
		$stations = $db_read->getAll("SELECT station_id,station_name FROM ship_station where city_code $city_code and flag=1 ");
		$smarty->assign('stations',   $stations);
	}

	$plan['sdate'] = $plan['edate'] = date('Y-m-d');
	$smarty->assign('plan',$plan);

     /* 模板赋值 */
    $smarty->assign('ur_here',     '配送排班添加');
    $smarty->assign('action_link', array('href'=>'delivery_plan.php?act=list', 'text' => '配送排班管理'));
    $smarty->assign('form_act',    'insert');
    $smarty->assign('action',      'add');

    $smarty->display('deliveryplan_add.htm');   
}
elseif ($_REQUEST['act'] == 'insert')
{
	$stime = strtotime($_POST['plan_sdate']);
	$etime = strtotime($_POST['plan_edate']);
	if($etime >= $stime)
	{
	   $n = ($etime - $stime)/86400;	   
	}
	else
	{
	   $n = ($stime - $etime)/86400;
	   $stime = $etime;	
	}

	$senders = explode(',',$_POST['plan_list']);
	for($i=0;$i<=$n;$i++)
	{
	    $date = date('Y-m-d',$stime + 86400 * $i);
		foreach($senders as $val)
		{
			$list = array();
			$list['bdate']       = $date;
			$list['station_id']  = intval($_POST['station']);
			$list['sender']      = $val;	
			
	        $db_write->autoExecute('delivery_plan', $list, 'INSERT');	
		}
	}	
    los_header("Location: delivery_plan.php?act=list\n");			
 
}
elseif ($_REQUEST['act'] == 'batch_operate')
{
    //admin_priv('order_os_edit');
	
    $plan_id   = $_REQUEST['plan_id'];     
    $plan_id_list = explode(',', $plan_id);	
	
    if (isset($_POST['delete']))
    {
        foreach ($plan_id_list as $id)
        {
            $db_write->query("DELETE FROM delivery_plan WHERE id = '$id'");
        }
		$url = 'delivery_plan.php';
        los_header("Location: $url\n");
        exit;  
    }
	else
	{
        echo '暂无其他批量处理服务！';exit;	
	}
}
/*------------------------------------------------------ */
//-- 删除订单
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    //admin_priv('39');

    $id = intval($_REQUEST['id']);
    $res = $GLOBALS['db_write']->query("delete from delivery_plan WHERE id = '$id'");

    if ($res)
    {
        $url = 'delivery_plan.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
        los_header("Location: $url\n");
        exit;
    }
    else
    {
        make_json_error('删除出错!请检查！');
    }
}
/*------------------------------------------------------ */
//-- 组删除订单
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'delete')
{
    /* 检查权限 */
    admin_priv('39');


    $bdate   = trim($_REQUEST['bdate']);
	$station = trim($_REQUEST['station']);
	
	$stn = $db_read->getOne("select id from shipping_station where name = '$station'");
	$sql = "delete from shipping_deliveryplan WHERE date = '$bdate' and shipping_station_id = '$stn'"; 
    //echo $sql;exit;
    $res = $GLOBALS['db_write']->query($sql);

    if ($res)
    {
        $url = 'delivery_plan.php';

        los_header("Location: $url\n");
        exit;
    }
    else
    {
        make_json_error('删除出错!请检查！');
    }
}
/*------------------------------------------------------ */
//-- 下属配送员列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'employee')
{
	require(ROOT_PATH . 'includes/cls_json.php');
	
	$station = !empty($_REQUEST['station']) ? intval($_REQUEST['station']) : 0;
	
	$sql = "select id as employee_id,name as employee_name from hr_employees where flag=1 and station_id = ".$station;
	$arr = $db_read->getAll($sql);
	
	$json = new JSON;
	echo $json->encode($arr);
}

/*
* 取得批次信息列表
*/
function plan_list()
{
	$filter['sdate'] = empty($_REQUEST['sdate']) ? '' : trim($_REQUEST['sdate']);
	$filter['edate'] = empty($_REQUEST['edate']) ? '' : trim($_REQUEST['edate']);
	$filter['station'] = $_REQUEST['station'];
	$filter['page'] = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);	
			
	$where = " where 1 ";
	if(!empty($filter['sdate']))
	{
	   $where .= " and bdate >= '".$filter['sdate']."' ";
	}
	if(!empty($filter['edate']))
	{
	   $where .= " and bdate <= '".$filter['edate']."' ";
	}
	if(!empty($filter['station']) && $filter['station'] != 100 )
	{
	   $where .= " and a.station_id = '".$filter['station']."' ";
	}

	$sql = "select a.*,b.station_name from view_delivery_plan_pack as a left join ship_station as b on a.station_id=b.station_id ".$where;
	$res = $GLOBALS['db_read']->getAll($sql);
	
	
	foreach($res as $key =>$val)
	{
	   $sql = "select a.id,a.bdate,b.name as employee_name,c.station_name from delivery_plan as a ".
	   "left join hr_employees as b on a.sender=b.id ".
	   "left join ship_station as c on a.station_id=c.station_id where a.station_id = '".$val['station_id']."' and bdate = '".$val['bdate']."'";		   
	   $res[$key]['delivery'] =  $GLOBALS['db_read']->getAll($sql);
       $res[$key]['i'] =  $key + 1;
	}	
    $arr = array('delivery' => $res, 'filter' => $filter, 'page_count' => 1, 'record_count' => count($res));

    return $arr;
	
  
}
?>