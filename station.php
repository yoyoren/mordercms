<?php
/**
 * Station Management
 * @copyright Copyright (c) 2012, 21cake food co.ltd
 * @author 21Cake Dev Team
 */

require(dirname(__FILE__) . '/includes/init.php');
$_REQUEST['act'] = empty($_REQUEST['act']) ? 'list' : trim($_REQUEST['act']);
$act = array('list', 'query', 'add', 'insert', 'edit', 'update', 'remove');
$excg = new exchange('ship_station', $db_write, 'station_id', 'station_name');

if (in_array($_REQUEST['act'], $act)) {
	switch ($_REQUEST['act']) {
		case 'query':
			$station_list = get_station_list();
			$smarty->assign('station_list', $station_list['list']);
			$smarty->assign('filter', $station_list['filter']);
			$smarty->assign('record_count', $station_list['record_count']);
			$smarty->assign('page_count', $station_list['page_count']);
			make_json_result($smarty->fetch('station_list.html'), '', array('filter' => $station_list['filter'], 'page_count' => $station_list['page_count'],));
			break;	
		case 'add':
			$city = get_city_list();
			$smarty->assign('ur_here', '添加站点');
			$smarty->assign('action_link', array('text' => '站点管理', 'href' => 'station.php?act=list'));
			$smarty->assign('city', $city);
			$smarty->assign('form_act', 'insert');
			$smarty->display('station.html');
			break;
		case 'insert':
			$data = array();
			$data['station_name'] = empty($_REQUEST['station_name']) ? '' : trim($_REQUEST['station_name']);
			$data['station_code'] = empty($_REQUEST['station_code']) ? '' : trim($_REQUEST['station_code']);
			$data['address'] = empty($_REQUEST['address']) ? '' : trim($_REQUEST['address']);
			$data['city_code'] = empty($_REQUEST['city_code']) ? '' : trim($_REQUEST['city_code']);
			$data['flag'] = '-1';
			$data['ziti'] = empty($_REQUEST['ziti']) ? '-1' : $_REQUEST['ziti'];
			$db_write->autoExecute('ship_station', $data, 'INSERT');
			
			$links[0]['text'] = '继续添加站点';
			$links[0]['href'] = 'station.php?act=add';
			$links[1]['text'] = '返回站点管理列表';
			$links[1]['href'] = 'station.php?act=list';
			sys_msg('站点添加成功！', 1, $links);
			break;
		case 'edit':
			$_REQUEST['id'] = empty($_REQUEST['id']) ? '0' : intval($_REQUEST['id']);
			$city = get_city_list();
			$station_info = $db_read->getRow("SELECT * FROM ship_station WHERE station_id=" . $_REQUEST['id']);
			
			$smarty->assign('ur_here', '编辑站点');
			$smarty->assign('action_link', array('text' => '站点管理', 'href' => 'station.php?act=list'));
			$smarty->assign('city', $city);
			$smarty->assign('city_code', $station_info['city_code']);
			$smarty->assign('station', $station_info);
			$smarty->assign('form_act', 'update');
			$smarty->display('station.html');
			break;
		case 'update':
			$data = array();
			$data['id'] = empty($_REQUEST['id']) ? '' : trim($_REQUEST['id']);
			$data['station_name'] = empty($_REQUEST['station_name']) ? '' : trim($_REQUEST['station_name']);
			$data['station_code'] = empty($_REQUEST['station_code']) ? '' : trim($_REQUEST['station_code']);
			$data['address'] = empty($_REQUEST['address']) ? '' : trim($_REQUEST['address']);
			$data['city_code'] = empty($_REQUEST['city_code']) ? '' : trim($_REQUEST['city_code']);
			$data['flag'] = empty($_REQUEST['flag']) ? '-1' : trim($_REQUEST['flag']);
			$data['ziti'] = empty($_REQUEST['ziti']) ? '-1' : $_REQUEST['ziti'];
			$db_write->autoExecute('ship_station', $data, 'UPDATE', "station_id='" . $data['id'] . "'");
			sys_msg('站点编辑成功！', 1, array(array('text' => '返回站点管理列表', 'href' => 'station.php?act=list')));
			break;
		case 'remove':
			$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : '0';
			$excg->drop($id);
			$url = "station.php?act=query&" . str_replace('act=remove', '', $_SERVER['QUERT_STRING']);
			los_header("Location: $url\n");
			exit();
			break;
		default:
			$station_list = get_station_list();
			$smarty->assign('full_page', 1);
			$smarty->assign('ur_here', '站点管理');
			$smarty->assign('action_link', array('text' => '站点添加', 'href' => 'station.php?act=add'));
			$smarty->assign('station_list', $station_list['list']);
			$smarty->assign('filter', $station_list['filter']);
			$smarty->assign('record_count', $station_list['record_count']);
			$smarty->assign('page_count', $station_list['page_count']);
			$smarty->display('station_list.html');
	}
} else {
	sys_msg('坑爹呀，页面不存在！', 1, array(array('text' => '返回站点管理'), array('href' => 'station.php?act=list')));
}

function get_station_list() { // Obtaion to the station list
	$result = get_filter();
	
	if ($result === false) {
		$filter = array();
		$filter['code'] = empty($_REQUEST['code']) ? '' : trim($_REQUEST['code']);
		$filter['flag'] = empty($_REQUEST['flag']) ? '0' : intval($_REQUEST['flag']);
		$filter['ziti'] = empty($_REQUEST['ziti']) ? '0' : intval($_REQUEST['ziti']);
		
		$where = " WHERE 1  AND city_code" . db_create_in(array_keys($_SESSION['city_arr'])) . " ";
		if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax']  == 1)			$filter['code'] = json_str_iconv($filter['code']);
		if ($filter['code'])		$where .= " AND station_name LIKE '%" . $filter['code'] . "%'";
		if ($filter['flag'])			$where .= " AND flag=" . $filter['flag'] . "";
		if ($filter['ziti'])			$where .= " AND ziti=" . $filter['ziti'] . "";
		$query = "SELECT COUNT(*) FROM ship_station $where";
		$filter['record_count'] = $GLOBALS['db_read']->getOne($query);
		$filter = page_and_size($filter);
		
		$sql = "SELECT * FROM ship_station $where";
		set_filter($filter, $sql);
	} else {
		$filter = $result['filter'];
		$sql = $result['sql'];
	}
	$list = array();
	$res = $GLOBALS['db_read']->selectLimit($sql, $filter['page_size'], $filter['start']);
	while ($rows = $GLOBALS['db_read']->fetchRow($res)) {
		$list[] = $rows;
	}
	return array('list' => $list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count'],);
}

function get_city_list() { // Obtain to the city list
	$sql = "SELECT city_code,city_name FROM order_city WHERE city_code " . db_create_in(array_keys($_SESSION['city_arr']));
	$result = $GLOBALS['db_read']->getAll($sql);
	foreach ($result as $key => $val) {
		$array[$val['city_code']] = $val['city_name'];
	}
	return $array;
}