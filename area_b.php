<?php
//require(dirname(__FILE__) . '/includes/init78.php');
require(dirname(__FILE__) . '/includes/init.php');

$_REQUEST['act'] = empty($_REQUEST['act']) ? 'list' : trim($_REQUEST['act']);
if ($_REQUEST['act'] == 'list')
{
    $smarty->assign('ur_here',     '地址点管理');
    $smarty->assign('full_page',   1);
    $smarty->assign('action_link', array('href' => 'area.php?act=add', 'text' => '地址点添加'));
		
	$sql = "select station_id,station_name from ship_station where flag =1 and station_id <10 ";
	$res = $db_read->getAll($sql);
	$smarty->assign('station_list',  $res); 
	$sql = "select region_name from ship_region where region_type=2 ";
	$res = $db_read->getAll($sql);
	$smarty->assign('region_list',  $res); 	
	
	$list = area_list();
    //echo '<pre>';print_r($list);echo '</pre>';

    $smarty->assign('record_count',  $list['record_count']);
    $smarty->assign('page_count',    $list['page_count']);
    $smarty->assign('filter',        $list['filter']);	
	$smarty->assign('area',   		 $list['list']);   
	$smarty->display('area_list.html');
}
elseif ($_REQUEST['act'] == 'query')
{
    $list = area_list();
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
    $smarty->assign('filter',       $list['filter']);
	$smarty->assign('area',   		$list['list']); 
    make_json_result($smarty->fetch('area_list.html'), '', array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}
elseif ($_REQUEST['act'] == 'add')
{
    $smarty->assign('ur_here',     '地址点添加');	
    $smarty->assign('action_link', array('href' => 'area.php?act=list', 'text' => '地址点管理'));
	$sql = "select route_id,route_name from view_ship_route where flag =1 and station_id <10 ";
	$res = $db_read->getAll($sql);
	$smarty->assign('route_list',   		$res); 
	
	$smarty->assign('form_act', 'insert');
	$smarty->display('area_info.html');
}
elseif ($_REQUEST['act'] == 'insert')
{
    //print_r($_POST);exit;
	$stn['area_name'] = trim($_POST['area_name']);
	$stn['route_id']  = trim($_POST['route_id']);
	$stn['region_id'] = intval($_POST['region_id']);
	$db_write->autoExecute("ship_area",$stn,'INSERT');
	//print_r($stn);
	echo "<script>window.location = 'area.php?act=list';</script>";
	exit;
}
elseif ($_REQUEST['act'] == 'edit')
{
	$smarty->assign('ur_here',     '地址点修改');	
    $smarty->assign('action_link', array('href' => 'area.php?act=list', 'text' => '地址点管理'));
    $sid = $_REQUEST['id'];
	$area = $db_read->getRow("select * from ship_area where area_id = '$sid'");
	$smarty->assign('area', $area); 	
	$sql = "select route_id,route_name from ship_route where flag =1 ";
	$res = $db_read->getAll($sql);
	$smarty->assign('route_list',   		$res); 
	
	$sql = "select region_id,region_name from ship_region where region_type=2 ";
	$res = $db_read->getAll($sql);
	$smarty->assign('region_list',  $res);
	$smarty->assign('form_act', 'update'); 
	$smarty->display('area_info.html');
}
elseif ($_REQUEST['act'] == 'update')
{
	//print_r($_POST);exit;
	$sql = "update ship_area set area_name='".$_POST['area_name']."',route_id = ".$_POST['route_id'].
	       ",region_id = ".$_POST['region_id']." where area_id = ".intval($_POST['id']);
		   //echo $sql;exit;
	$db_write->query($sql);
	
	echo "<script>window.location = 'area.php?act=list';</script>";
	exit;
}
function area_list()
{
    if (!empty($_GET['is_ajax']) && $_GET['is_ajax'] == 1)
    {
        $_REQUEST['area'] = json_str_iconv($_REQUEST['area']);
        $_REQUEST['city'] = json_str_iconv($_REQUEST['city']);
        $_REQUEST['stan'] = json_str_iconv($_REQUEST['stan']);
    }	
	$filter['code']    = empty($_REQUEST['code']) ? '' : trim($_REQUEST['code']);
	$filter['area']    = empty($_REQUEST['area']) ? '' : trim($_REQUEST['area']);
	$filter['city']    = empty($_REQUEST['city']) ? '' : trim($_REQUEST['city']);
	$filter['stan']    = empty($_REQUEST['stan']) ? '' : trim($_REQUEST['stan']);
	$filter['fee']     = intval($_REQUEST['fee']);
    $filter['page']    = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);	
		
	$where = " where 1 ";
	if($filter['code'])
	{
	   $where .= " and route_name = '".$filter['code']."' ";
	}
	if($filter['area'])
	{
	   $where .= " and area_name like '%".$filter['area']."%'";
	}
	if($filter['fee'])
	{
	   $where .= " and fee = ".$filter['fee'];
	}	
	if($filter['stan'])
	{
	   $where .= " and station_id = '".$filter['stan']."'";
	}
	if($filter['city'])
	{
	   $where .= " and city = '".$filter['city']."'";
	}

	$size = 30;	
    $sql = "select count(1) from view_ship_area ".$where;

    $record_count   = $GLOBALS['db_read']->getOne($sql);
    $page_count     = $record_count > 0 ? ceil($record_count / $size) : 1;
	
    $sql = "select * from view_ship_area ".$where ." LIMIT " . ($filter['page'] - 1) * $size . ",$size"; 
	//echo $sql;
    $rs = $GLOBALS['db_read']->getAll($sql);

    $arr = array('list' => $rs, 'filter' => $filter, 'page_count' => $page_count, 'record_count' => $record_count);

    return $arr;

}
?>