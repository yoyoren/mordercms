<?php
/**
 * Employee Management
 * @copyright Copyright (c) 2012, 21cake food co.ltd
 * @author 21Cake Dev Team
 */

require(dirname(__FILE__) . '/includes/init.php');
$_REQUEST['act'] = empty($_REQUEST['act']) ? 'list' : trim($_REQUEST['act']);
$act = array('list', 'query', 'add', 'insert', 'edit', 'update', 'remove','batchAdd');
$excg1 = new exchange('sh_address', $db_write, 'id', 'area');
$excg2 = new exchange('ship_area', $db_write, 'area_id', 'area_name');

if (in_array($_REQUEST['act'], $act)) {
	switch ($_REQUEST['act']) {
		case 'query':
			$area_list = get_area_list();
			$smarty->assign('area_list', $area_list['list']);
			$smarty->assign('filter', $area_list['filter']);
			$smarty->assign('record_count', $area_list['record_count']);
			$smarty->assign('page_count', $area_list['page_count']);
			$html = city_location() ? 'area_sh_list.html' : 'area_bj_list.html';
			make_json_result($smarty->fetch($html), '', array('filter' => $area_list['filter'], 'page_count' => $area_list['page_count'], 'sql' => $area_list['sql']));
			break;
		case 'add':
			if (city_location()) {
				$smarty->assign('route_list', get_route_list("station_id>9"));
			} else {
				$smarty->assign('region_list', get_region_list());
				$smarty->assign('route_list', get_route_list("station_id<10"));
			}
			$smarty->assign('ur_here', '地址点添加');	
			$smarty->assign('action_link', array('href' => 'area.php?act=list', 'text' => '地址点管理'));
			$smarty->assign('form_act', 'insert');
			$html = city_location() ? 'area_sh.html' : 'area_bj.html';
			$smarty->display($html);
			break;
		case 'insert':
			$data = array();
			
			if (city_location()) {
				$data['area'] = empty($_POST['area']) ? '' : trim($_POST['area']);
				$data['road'] = empty($_POST['area_name']) ? '' : trim($_POST['area_name']);
				$data['omax'] = empty($_POST['omax']) ? '' : intval($_POST['omax']);
				$data['omin'] = empty($_POST['omin']) ? '' : intval($_POST['omin']);
				$data['emax'] = empty($_POST['emax']) ? '' : intval($_POST['emax']);
				$data['emin'] = empty($_POST['emin']) ? '' : intval($_POST['emin']);
				$data['route_id'] = empty($_POST['route_id']) ? '' : intval($_POST['route_id']);
				$db_write->autoExecute('sh_address', $data, 'INSERT');
				$links[0]['text'] = '继续添加';
				$links[0]['href'] = 'area.php?act=add';
				$links[1]['text'] = '返回地址点管理';
				$links[1]['href'] = 'area.php?act=list';
			} else {
				$data['area_name'] = empty($_REQUEST['area_name']) ? '' : trim($_REQUEST['area_name']);
				$data['route_id'] = empty($_REQUEST['route_id']) ? '' : trim($_REQUEST['route_id']);
				$data['region_id'] = empty($_REQUEST['region_id']) ? '' : trim($_REQUEST['region_id']);
				$db_write->autoExecute('ship_area', $data, 'INSERT');
				$links[0]['text'] = '继续添加';
				$links[0]['href'] = 'area.php?act=add';
				$links[1]['text'] = '返回员工管理';
				$links[1]['href'] = 'area.php?act=list';
			}
    		sys_msg('地址点添加成功！', 0, $links);
			break;
		case 'edit':
			$data = array();
			
			if (city_location()) {
				$sql = "SELECT * FROM sh_address WHERE id=" . intval($_REQUEST['id']);
				$route_info = $db_read->getRow($sql);
				$smarty->assign('route_info', $route_info);
				$smarty->assign('route_list', get_route_list('station_id>9'));
				$html = 'area_sh.html';
			} else {
				$smarty->assign('region_list', get_region_list());
				$smarty->assign('route_list', get_route_list('station_id<10'));
				$area_info = $db_read->getRow("SELECT * FROM ship_area WHERE area_id=" . intval($_REQUEST['id']));
				$smarty->assign('area_info', $area_info);
				$html = 'area_bj.html';
			}
			$smarty->assign('ur_here', '编辑地址点');
			$smarty->assign('action_link', array('href' => 'area.php?act=list', 'text' => '返回地址点管理列表'));
			$smarty->assign('form_act', 'update');
			$smarty->display($html);
			break;
		case 'update':
			$data = array();
			
			if (city_location()) {
				$data['area'] = empty($_POST['area']) ? '' : trim($_POST['area']);
				$data['road'] = empty($_POST['area_name']) ? '' : trim($_POST['area_name']);
				$data['omax'] = empty($_POST['omax']) ? '' : intval($_POST['omax']);
				$data['omin'] = empty($_POST['omin']) ? '' : intval($_POST['omin']);
				$data['emax'] = empty($_POST['emax']) ? '' : intval($_POST['emax']);
				$data['emin'] = empty($_POST['emin']) ? '' : intval($_POST['emin']);
				$data['route_id'] = empty($_POST['route_id']) ? '' : intval($_POST['route_id']);
				$db_write->autoExecute('sh_address', $data, 'UPDATE', 'id=' . intval($_REQUEST['id']));
				$links[0]['text'] = '返回地址点管理列表';
				$links[0]['href'] = 'area.php?act=list';
			} else {
				$data['area_name'] = empty($_POST['area_name']) ? '' : trim($_POST['area_name']);
				$data['region_id'] = empty($_POST['region_id']) ? '' : trim($_POST['region_id']);
				$data['route_id'] = empty($_POST['route_id']) ? '' : trim($_POST['route_id']);
				$db_write->autoExecute('ship_area', $data, 'UPDATE', 'area_id=' . intval($_REQUEST['id']));
				$links[0]['text'] = '返回地址点管理列表';
				$links[0]['href'] = 'area.php?act=list';
			}
			sys_msg('地址点编辑成功！', 0, $links);
			break;
		case 'remove':
			$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : '0';
			city_location() ? $excg1->drop($id) : $excg2->drop($id);
			$url = "area.php?act=query&" . str_replace('act=remove', '', $_SERVER['QUERT_STRING']);
			los_header("Location: $url\n");
			break;
		case 'batchAdd':
			//print_r($_REQUEST);
			//print_r($_FILES);
			batchInsertAdress('text/plain','120000',$_SESSION['city_group']);
			break;
		default:
			$region_list = get_region_list();
			$station_list = get_station_list();
			$area_list = get_area_list();
			$smarty->assign('full_page', 1);
			$smarty->assign('ur_here', '地址点管理');
			$smarty->assign('action_link', array('href' => 'area.php?act=add', 'text' => '添加地址点'));
			$smarty->assign('region_list', $region_list);
			$smarty->assign('station_list', $station_list);
			$smarty->assign('area_list', $area_list['list']);
			$smarty->assign('filter', $area_list['filter']);
			$smarty->assign('record_count', $area_list['record_count']);
			$smarty->assign('page_count', $area_list['page_count']);
			$html = city_location() ? 'area_sh_list.html' : 'area_bj_list.html';
			$smarty->display($html);
	}
} else {
	sys_msg('页面不存在！', 1, array(array('text' => '返回地址点管理列表'), array('href' => 'area.php?act=list')));
}

function get_route_list($param) { // Obtaion to the route list which you select city
	$route_list = array();
	$sql = "SELECT route_id,route_name FROM view_ship_route WHERE flag=1 AND $param";
	$result = $GLOBALS['db_read']->getAll($sql);
	foreach ($result as $key => $val) {
		$route_list[$val['route_id']] = $val['route_name'];
	}
	return $route_list;
}

function get_region_list() { // Obtaion to the region list
	$array = array();
	$where = "WHERE a.parent_id" . db_create_in(array_keys($_SESSION['city_arr'])) . "GROUP BY b.region_name";
	$sql = "SELECT b.region_id,b.region_name FROM `ship_region` AS a LEFT JOIN `ship_region` AS b ON a.region_id=b.parent_id $where";
	$result = $GLOBALS['db_read']->getAll($sql);
	foreach ($result as $key => $val) {
		$array[$val['region_id']] = $val['region_name'];
	}
	return $array;
}

function get_station_list() { // Obtaion to the station list
	$array = array();
	$sql = "SELECT station_id,station_name FROM ship_station WHERE city_code" . db_create_in(array_keys($_SESSION['city_arr']));
	$result = $GLOBALS['db_read']->getAll($sql);
	foreach ($result as $key => $val) {
		$array[$val['station_id']] = $val['station_name'];
	}
	return $array;
}

function get_area_list() { // Obtaion to the area list
	$result = get_filter();
	$flag = city_location();
	
	if ($result === false) {
		$filter = array();
		$where = " WHERE 1";
		
		if ($flag) { // Shanghai
			$filter['area_name'] = empty($_REQUEST['area_name']) ? '' : trim($_REQUEST['area_name']);
			$filter['code'] = empty($_REQUEST['code']) ? '' : trim($_REQUEST['code']);
			$filter['fee'] = empty($_REQUEST['fee']) ? '' : intval($_REQUEST['fee']);
			$filter['stan'] = empty($_REQUEST['stan']) ? '' : intval($_REQUEST['stan']);
	
			if (!empty($_GET['is_ajax']) && $_GET['is_ajax'] == 1)			$filter['area_name'] = !empty($filter['area_name']) ? json_str_iconv($filter['area_name']) : '';
			if ($filter['area_name'])	$where .= " AND (area LIKE '%" . mysql_like_quote($filter['area_name']) . "%' OR road LIKE '%" . mysql_like_quote($filter['area_name']) . "%')";
			if ($filter['code'])			$where .= " AND route_name LIKE '%" . mysql_like_quote($filter['code']) . "%'";
			if ($filter['fee'])				$where .= " AND fee='" . $filter['fee'] . "'";
			if ($filter['stan'])				$where .= " AND station_id='". $filter['stan'] . "'";
			
			$query = "SELECT COUNT(1) FROM view_sh_area $where";
			$filter['record_count'] = $GLOBALS['db_read']->getOne($query);
			$filter = page_and_size($filter);
			$limit = "LIMIT " . $filter['start'] . ", " . $filter['page_size'];
			$sql = "SELECT * FROM view_sh_area $where $limit";
		} else { // Beijing
			$filter['area'] = empty($_REQUEST['area']) ? '' : trim($_REQUEST['area']);
			$filter['code'] = empty($_REQUEST['code']) ? '' : trim($_REQUEST['code']);
			$filter['stan'] = empty($_REQUEST['stan']) ? '' : intval($_REQUEST['stan']);
			$filter['city'] = empty($_REQUEST['city']) ? '' : intval($_REQUEST['city']);
			$filter['fee'] = empty($_REQUEST['fee']) ? '' : intval($_REQUEST['fee']);
			
			if (!empty($_GET['is_ajax']) && $_GET['is_ajax'] == 1)			$filter['area'] = !empty($filter['area']) ? json_str_iconv($filter['area']) : '';
			if ($filter['area'])			$where .= " AND area_name LIKE '%" . mysql_like_quote($filter['area']) . "%'";
			if ($filter['code'])		$where .= " AND route_name LIKE '%" . mysql_like_quote($filter['code']) . "%'";
			if ($filter['stan'])			$where .= " AND station_id='" . $filter['stan'] . "'";
			if ($filter['city'])			$where .= " AND region_id='" . $filter['city'] . "'";
			if ($filter['fee'])			$where .= " AND fee='" . $filter['fee'] . "'";
			
			$query = "SELECT COUNT(1) FROM view_bj_area $where";
			$filter['record_count'] = $GLOBALS['db_read']->getOne($query);
			$filter = page_and_size($filter);
			$limit = "LIMIT " . $filter['start'] . ", " . $filter['page_size'];
			$sql = "SELECT * FROM view_bj_area $where $limit";
		}
		set_filter($filter, $sql);
	} else {
		$sql = $result['sql'];
		$filter = $result['filter'];
	}
	
	if ($flag) { // Shanghai
		$rows = $GLOBALS['db_read']->getAll($sql);
		foreach ($rows as $k => $v) {
			$row[$k]['i'] = $k + 1;
			$rows[$k]['station'] = substr($v['route_name'], 0, 2) . '号站';
		}
	} else { // Beijing
		$rows = $GLOBALS['db_read']->getAll($sql);
	}
	return array('list' => $rows, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count'], 'sql' => $sql);
}

function batchInsertAdress($type,$size,$city_code){
	$files = $_FILES['upfile'];
	$error= $files['error'];
	$fileSize=$files['size'];
	$fileType=$files['type'];
	$fileDir = $files['tmp_name'];
	
	if($error===0){
		if($fileType == $type){
			if($fileSize < $size){
				$file = fopen($fileDir,'r') or die('文件打开失败');
				fgets($file);
				while(!feof($file)){
					$val=fgets($file);
					$values = explode('	',trim($val));
					if(!empty($values[0])){
						if($city_code == 441){
							$sql = "insert into ship_area(area_name,route_id,region_id) values ('$values[0]','$values[1]','$values[2]')";
						
						}else{
							$sql = "insert into sh_address (route_id,area,road,emin,emax,omin,omax) values ($values[0],'$values[1]','$values[2]','$values[3]','$values[4]','$values[5]','$values[6]')";
						
						}
						$GLOBALS['db_write'] -> query($sql);
					}
					
				}
				fclose($file);
				$links[0]['text'] = '返回列表';
				$links[0]['href'] = 'area.php?act=list';
				sys_msg('地址导入成功',1,$links);
				
			}else{
				sys_msg('文件太大', 1);
			}
		}else{
			sys_msg('文件类型错误',1);
		}

	}elseif($error ===1 || $error ===2){

		sys_msg('上传的文件太大',1);
	}else{

		sys_msg('上传文件失败！',1);
	}

	
}