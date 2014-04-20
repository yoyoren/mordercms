<?php
require dirname(__FILE__) . '/includes/init.php';
$_REQUEST['act'] = empty($_REQUEST['act']) ? 'list' : trim($_REQUEST['act']);
$act = array('list', 'query', 'add', 'insert', 'edit', 'update', 'remove', 'batch');
$excg = new exchange('ship_route', $db_write, 'route_id', 'route_name');

if (in_array($_REQUEST['act'], $act)) {
	switch ($_REQUEST['act']) {
		case 'query':
			$route_list = get_route_list();
			$smarty->assign('route_list', $route_list['list']);
			$smarty->assign('filter', $route_list['filter']);
			$smarty->assign('record_count', $route_list['record_count']);
			$smarty->assign('page_count', $route_list['page_count']);
			make_json_result($smarty->fetch('route_list.html'), '', array('filter' => $route_list['filter'], 'page_count' => $route_list['page_count']));
			break;
		case 'add':
			$station_list = get_station_list();
			$smarty->assign('ur_here', '添加路区');
			$smarty->assign('action_link', array('text' => '返回路区管理列表', 'href' => 'route.php?act=list'));
			$smarty->assign('station_list', $station_list);
			$smarty->assign('form_act', 'insert');
			$smarty->display('route.html');
			break;
		case 'insert':
			$data = array();
			$data['route_name'] = empty($_REQUEST['route_name']) ? '' : trim($_REQUEST['route_name']);
			$data['route_code'] = empty($_REQUEST['route_code']) ? '' : trim($_REQUEST['route_code']);
			$data['fee'] = empty($_REQUEST['fee']) ? '' : intval($_REQUEST['fee']);
			$data['station_id'] = empty($_REQUEST['station_id']) ? '' : intval($_REQUEST['station_id']);
			$data['flag'] = 1;
			$db_write->autoExecute('ship_route', $data, 'INSERT');
			$links[0]['text'] = '继续添加';
			$links[0]['href'] = 'route.php?act=add';
			$links[1]['text'] = '返回路区管理';
			$links[1]['href'] = 'route.php?act=list';
			sys_msg('路区添加成功！', 0, $links);
			break;
		case 'edit':
			$station_list = get_station_list();
			$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : '0';
			$route_info = $db_read->getRow("SELECT * FROM ship_route WHERE route_id='$id'");
			$smarty->assign('ur_here', '编辑路区');
			$smarty->assign('action_link', array('text' => '返回路区管理列表', 'href' => 'route.php?act=list'));
			$smarty->assign('route_info', $route_info);
			$smarty->assign('station_list', $station_list);
			$smarty->assign('form_act', 'update');
			$smarty->display('route.html');
			break;
		case 'update':
			$data = array();
			$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : '';
			$data['route_name'] = empty($_REQUEST['route_name']) ? '' : trim($_REQUEST['route_name']);
			$data['route_code'] = empty($_REQUEST['route_code']) ? '' : trim($_REQUEST['route_code']);
			$data['fee'] = empty($_REQUEST['fee']) ? '' : intval($_REQUEST['fee']);
			$data['station_id'] = empty($_REQUEST['station_id']) ? '' : intval($_REQUEST['station_id']);
			$data['flag'] = empty($_REQUEST['flag']) ? '-1' : $_REQUEST['flag'];
			$db_write->autoExecute('ship_route', $data, 'UPDATE', "route_id='$id'");
			$links[0]['text'] = '返回路区管理列表';
			$links[0]['href'] = 'route.php?act=list';
			sys_msg('路区编辑成功！', 0, $links);
			break;
		case 'remove':
			$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : '0';
			$excg->drop($id);
			$url = "route.php?act=query&" . str_replace('act=remove', '', $_SERVER['QUERT_STRING']);
			los_header("Location: $url\n");
			exit();
			break;
		default:
			$station_list = get_station_list();
			$route_list = get_route_list();
			$smarty->assign('ur_here', '路区管理列表');
			$smarty->assign('action_link', array('text' => '添加路区', 'href' => 'route.php?act=add'));
			$smarty->assign('full_page', 1);
			$smarty->assign('station_list', $station_list);
			$smarty->assign('route_list', $route_list['list']);
			$smarty->assign('filter', $route_list['filter']);
			$smarty->assign('record_count', $route_list['record_count']);
			$smarty->assign('page_count', $route_list['page_count']);
			$smarty->display('route_list.html');
	}
} else {
	sys_msg('坑爹呀，页面不存在！', 1, array(array('text' => '返回路区管理列表'), array('href' => 'route.php?act=list')));
}

function get_station_list() { // Obtaion to the station list
	$array = array();
	$sql = "SELECT station_id,station_name FROM ship_station WHERE city_code" . db_create_in(array_keys($_SESSION['city_arr']));
	$result = $GLOBALS['db_read']->getAll($sql);
	foreach ($result as $key => $val) {
		$array[$val['station_id']] = '&nbsp;' . $val['station_name'];
	}
	return $array;
}

function get_route_list() { // Obtaion to the route list
	$result = get_filter();


	if ($result === false) {
		$filter = array();
		$filter['code'] = empty($_REQUEST['code']) ? '' : trim($_REQUEST['code']);
		$filter['flag'] = empty($_REQUEST['flag']) ? '' : $_REQUEST['flag'];
		$filter['station'] = empty($_REQUEST['station']) ? '' : intval($_REQUEST['station']);
		$filter['fee'] = empty($_REQUEST['fee']) ? '' : intval($_REQUEST['fee']);
		$where = " WHERE city_code ".db_create_in(array_keys($_SESSION['city_arr']));

		
		if ($filter['code'])	$where .= " AND route_name LIKE '%" . mysql_like_quote($filter['code']) . "%'";
		if ($filter['flag'])		$where .= " AND flag='" . $filter['flag'] . "'";
		if ($filter['station'])	$where .= " AND station_id='" . $filter['station'] . "'";
		if ($filter['fee'])		$where .= " AND fee='" . $filter['fee'] . "'";
		$query = "SELECT COUNT(1) FROM view_ship_route $where";
		$filter['record_count'] = $GLOBALS['db_read']->getOne($query);
		$filter = page_and_size($filter);
		$limit = "LIMIT " . $filter['start'] . "," . $filter['page_size'];
		
		$sql = "SELECT * FROM view_ship_route $where $limit";
		set_filter($filter, $sql);
	} else {
		$sql = $result['sql'];
		$filter = $result['filter'];
	}
	$list = $GLOBALS['db_read']->getAll($sql);
	foreach ($list as $key => $val) {
		$list[$key]['flag'] = $val['flag'] == 1 ? '在用' : '未用';
	}
	return array('list' => $list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}