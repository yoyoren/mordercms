<?php
include_once (dirname(__FILE__).'/includes/init.php');
admin_priv('m_cit');
$act = empty($_REQUEST['act']) ? 'list' : $_REQUEST['act'];
if($act == 'list')
{
	$list = get_city_list();
	$sql = "select city_name,city_code from order_city group by city_group";
	$city_group = $db_read -> getAll($sql);
	
	$smarty->assign('city_group',$city_group);
	$smarty->assign('city_list',$list['city_list']);
	$smarty->assign('record_count',$list['record_count']);
	$smarty->assign('page_count',$list['page_count']);
	$smarty->assign('ur_here','城市管理');
	$smarty->assign('action_link',array('text'=>'添加城市','href'=>'city.php?act=addcity'));
	$smarty->assign('full_page',1);
	$smarty->display('city.html');
}
elseif($act == 'query')
{
	$list = get_city_list();
	$smarty->assign('city_list',$list['city_list']);
	$smarty->assign('record_count',$list['record_count']);
	$smarty->assign('page_count',$list['page_count']);
	$smarty->assign('filter',$list['filter']);
	make_json_result($smarty->fetch('city.html'));
	
}
elseif($act == 'addcity')
{
	$sql = "select city_name,city_code from order_city group by city_group";
	$city_group = $db_read -> getAll($sql);
	
	$smarty->assign('city_group',$city_group);
	$smarty->assign('actions','saveadd');
	$smarty->assign('ur_here','添加城市');
	$smarty->assign('action_link',array('text'=>'城市列表','href'=>'city.php'));
	$smarty->display('city_add.html');
}
elseif($act == 'saveadd')
{
	$city_name = trim($_REQUEST['city_name']);
	$city_code = trim($_REQUEST['city_code']);
	$city_group = empty($_REQUEST['city_group']) ? $city_code : intval($_REQUEST['city_group']);
	$turn = intval($_REQUEST['turn']);
	$sql = "insert into order_city (city_name,city_code,city_group,turn) values ('$city_name','$city_code','$city_group','$turn')";
	$re = $db_write ->query($sql);
	if($re){
		$links[0]['text'] = '返回列表';
		$links[0]['href'] = 'city.php?act=list';
		sys_msg('添加成功', '', $links);
	}else{
		sys_msg('添加失败');
	}
}
elseif($act == 'editcity')
{
	$id = intval($_REQUEST['id']);
	$sql = "select * from order_city where id = $id";
	$city = $db_read->getRow($sql);
	$sql = "select city_name,city_code from order_city group by city_group";
	$city_group = $db_read -> getAll($sql);
	
	$smarty->assign('city',$city);
	$smarty->assign('city_group',$city_group);
	$smarty->assign('actions','saveedit');
	$smarty->assign('ur_here','编辑城市');
	$smarty->assign('action_link',array('text'=>'城市列表','href'=>'city.php'));
	$smarty->display('city_add.html');
}
elseif($act == 'saveedit')
{
	$id = intval($_REQUEST['id']);
	$city_name = trim($_REQUEST['city_name']);
	$city_code = trim($_REQUEST['city_code']);
	$city_group = empty($_REQUEST['city_group']) ? $city_code : intval($_REQUEST['city_group']);
	$turn = intval($_REQUEST['turn']);
	$sql = "update order_city set city_name='$city_name',city_code='$city_code',city_group='$city_group',turn='$turn' where id=$id limit 1";
	$re = $db_write ->query($sql);
	if($re){
		$links[0]['text'] = '返回列表';
		$links[0]['href'] = 'city.php?act=list';
		sys_msg('修改成功', '', $links);
	}else{
		sys_msg('修改失败');
	}
}
elseif($act == 'deletecity')
{
	$id = intval($_REQUEST['id']);
	$sql = "delete from order_city where id=$id limit 1";
	$re = $db_write->query($sql);
	if($re){
		$url = "city.php?act=query&".str_replace('act=deletecity', '', $_SERVER['QUERY_STRING']);
		los_header("Location:$url");
		exit;
	}else{
		make_json_error('删除错误！');
	}
}


function get_city_list(){
	$filter['city_group'] = empty($_REQUEST['city_group']) ? '' : intval($_REQUEST['city_group']);
	$filter['page'] = empty($_REQUEST['page']) ? 1 : intval($_REQUEST['page']);
	$filter['pageSize'] = empty($_REQUEST['pageSize']) ? 30 : intval($_REQUEST['pageSize']);
	$where = " where 1 ";
	if($filter['city_group']){
		$where .="and city_group = '".$filter['city_group']."' ";
	}
	$sql = "select count(id) from order_city $where";
	$record_count = $GLOBALS['db_read']->getOne($sql);
	$page_count = $record_count > 0 ? ceil($record_count/$filter['pageSize']) : 1;
	$sql = "select * from order_city $where";
	$list = $GLOBALS['db_read']->getAll($sql);
	foreach($list as $k=>$v){
		$sql = 'select city_name from order_city where city_code='.$v['city_group'];
		$list[$k]['city_group'] = $GLOBALS['db_read']->getOne($sql);
	}
	$arr = array('city_list'=>$list,'record_count'=>$record_count,'page_count'=>$page_count,'filter'=>$filter);
	return $arr;
	
	
}