<?php
/**
 * Employee Management
 * @copyright Copyright (c) 2012, 21cake food co.ltd
 * @author 21Cake Dev Team
 */

require dirname(__FILE__) . '/includes/init.php';
$_REQUEST['act'] = empty($_REQUEST['act']) ? 'list' : trim($_REQUEST['act']);
$act = array('list', 'site', 'query', 'add', 'insert', 'edit', 'update', 'remove');
$excg = new exchange('hr_employees', $db_write, 'id', 'name');
$city_code = db_create_in(array_keys($_SESSION['city_arr']));
if (in_array($_REQUEST['act'], $act)) {
	switch ($_REQUEST['act']) {
		case 'site':
			include_once ROOT_PATH . 'includes/cls_json.php';
			$json = new JSON();
			$city_code = !empty($_REQUEST['city_code']) ? " IN ('" . intval($_REQUEST['city_code']) . "') " : db_create_in(array_keys($_SESSION['city_arr']));
			$sql = "SELECT station_id,station_name FROM ship_station WHERE city_code $city_code";
			$site = $db_read->getAll($sql);
			$arr['target'] = !empty($_REQUEST['station']) ? htmlspecialchars(stripslashes(trim($_REQUEST['station']))) : '';
			$arr['site'] = $site;
			echo $json->encode($arr);
			break;
		case 'query':
			$employee_list = get_employee_list();
			$smarty->assign('employee_list', $employee_list['list']);
			$smarty->assign('filter', $employee_list['filter']);
			$smarty->assign('record_count', $employee_list['record_count']);
			$smarty->assign('page_count', $employee_list['page_count']);
			make_json_result($smarty->fetch('employee_list.htm'), '', array('filter' => $employee_list['filter'], 'page_count' => $employee_list['page_count'],));
			break;
		case 'add':
			if($_SESSION['station']){
				
				$sql = "SELECT station_id,station_name FROM ship_station  where station_id = '".trim($_SESSION['station'])."'";
				$smarty->assign('sel','1');
			}else{
				$sql = "SELECT station_id,station_name FROM ship_station where city_code $city_code";
			}
			
			
			$stations = $db_read->getAll($sql);
			$smarty->assign('stations',$stations);
			$smarty->assign('ur_here', '添加新员工');
   			$smarty->assign('action_link', array('text' => '员工管理', 'href'=>'employee.php?act=list'));

			//$smarty->assign('city_list', $_SESSION['city_arr']);
			$smarty->assign('form_act', 'insert');
			$smarty->display('employee.htm');
			break;
		case 'insert':
			$data = array();
			$data['dept_id'] = !empty($_REQUEST['dept']) ? trim($_REQUEST['dept']) : '';
			$data['station_id'] = !empty($_REQUEST['station']) ? trim($_REQUEST['station']) : '';
			$data['name'] = !empty($_REQUEST['name']) ? trim($_REQUEST['name']) : '';
			$data['office_phone'] = !empty($_REQUEST['office_phone']) ? trim($_REQUEST['office_phone']) : '0';
			$data['office_mobile'] = !empty($_REQUEST['office_mobile']) ? trim($_REQUEST['office_mobile']) : '0';
			$data['remark'] = !empty($_REQUEST['remark']) ? trim($_REQUEST['remark']) : '';
			$data['flag'] = 1;
			$data['level'] = !empty($_REQUEST['posts']) ? trim($_REQUEST['posts']) : '';
			
			$db_write->autoExecute('hr_employees', $data, 'INSERT');
			$links[0]['text'] = '继续添加';
			$links[0]['href'] = 'employee.php?act=add';
			$links[1]['text'] = '返回员工管理';
			$links[1]['href'] = 'employee.php?act=list';
    		sys_msg('员工添加成功！', 0, $links);
			break;
		case 'edit': // Edit the distribution member information
			$id = intval($_REQUEST['id']);
			if($_SESSION['station']){
				
				$sql = "SELECT station_id,station_name FROM ship_station  where station_id = '".trim($_SESSION['station'])."'";
				$smarty->assign('sel','1');
			}else{
				$sql = "SELECT station_id,station_name FROM ship_station where city_code $city_code";
			}
			$stations = $db_read->getAll($sql);
			
			$sql = "select name,s.station_id,h.office_phone,h.office_mobile,h.level from ship_station as s left join hr_employees as h on s.station_id=h.station_id where h.id=$id ";
			$employees = $db_read->getRow($sql);
			
			
			$smarty->assign('stations',$stations);
			$smarty->assign('id',$id);
			$smarty->assign('ur_here', '配送员工信息修改');
			$smarty->assign('action_link', array('text' => '配送员工列表', 'href'=>'employee.php?act=list'));
			$smarty->assign('employees', $employees);
			$smarty->assign('form_act', 'update');
			$smarty->display('employee.htm');
			break;
		case 'update': // Update delivery information
			$data = array();
			$data['id'] = intval($_REQUEST['id']);
			$data['station_id'] = empty($_REQUEST['station']) ? '0' : intval($_REQUEST['station']);
			$data['name'] = empty($_REQUEST['name']) ? '' : trim($_REQUEST['name']);
			$data['office_phone'] = empty($_REQUEST['office_phone']) ? '' : trim($_REQUEST['office_phone']);
			$data['office_mobile'] = empty($_REQUEST['office_mobile']) ? '' : trim($_REQUEST['office_mobile']);
			$data['dept_id'] = empty($_REQUEST['dept']) ? '' : trim($_REQUEST['dept']);
			$data['level'] = empty($_REQUEST['posts']) ? '' : trim($_REQUEST['posts']);
			$data['remark'] = empty($_REQUEST['remark']) ? '' : trim($_REQUEST['remark']);
			$city_code = intval($_REQUEST['city']);

			$re = $db_write->autoExecute('hr_employees', $data, 'UPDATE', "id='" . $data['id'] . "'");

			$links[0]['text'] = '返回员工管理';
			$links[0]['href'] = 'employee.php?act=list';
			if($re){
				sys_msg('员工编辑成功！', 0, $links);
			}
			break;
		case 'remove':
			$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : '0';
			$excg->drop($id);
			$url = "employee.php?act=query&" . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
			
			los_header("Location: $url\n");
			exit();
			break;
		default:
			
			$sql = "SELECT station_id,station_name FROM ship_station  where station_id = '".trim($_SESSION['station'])."'";
			$stations = $db_read->getAll($sql);
		   
			if($stations)
			{ 
				$smarty->assign('Current','Current');
				$smarty->assign('stations',   $stations);
				$_REQUEST['stations'] = $stations[0]['station_id'];	
			}
			else
			{
				$stations = $db_read->getAll("SELECT station_id,station_name FROM ship_station where city_code $city_code ");
				$smarty->assign('stations',   $stations);
			}
			
			$smarty->assign('ur_here', '配送员工管理');
    		$smarty->assign('action_link', array('href'=>'employee.php?act=add', 'text' => '添加配送员工'));
			$smarty->assign('full_page', 1);
			$employee_list = get_employee_list();
			$smarty->assign('city_list', $_SESSION['city_arr']);
			$smarty->assign('employee_list', $employee_list['list']);
			$smarty->assign('filter', $employee_list['filter']);
			$smarty->assign('record_count', $employee_list['record_count']);
			$smarty->assign('page_count', $employee_list['page_count']);
			$smarty->display('employee_list.htm');
	}
} else {
	sys_msg('页面不存在！', 1, array(array('text' => '返回员工管理'), array('href' => 'employee.php?act=list')));
}

function get_employee_list() { // Obtaion to the employee list
	$result = get_filter();
	
	if ($result === false) {
		$filter = array();
		$filter['employee'] = empty($_REQUEST['employee']) ? '' : trim($_REQUEST['employee']);
		$filter['stations'] = empty($_REQUEST['stations']) ? '' : intval($_REQUEST['stations']);
		$filter['city_code'] = empty($_REQUEST['city_code']) ? '' : intval($_REQUEST['city_code']);

		$where = " WHERE a.flag=1 AND b.city_code" .$GLOBALS['city_code'];
		if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax']  == 1)			$filter['employee'] = json_str_iconv($filter['employee']);
		if ($filter['employee'])		$where .= " AND name LIKE '%" . $filter['employee'] . "%'";
		if ($filter['stations'])				$where .= " AND a.station_id='" . $filter['stations'] . "'";
		if ($filter['city_code'])	$where .= " AND b.city_code =" .$filter['city_code'] . "";

		$query = "SELECT COUNT(*) FROM hr_employees AS a LEFT JOIN ship_station AS b ON a.station_id=b.station_id $where";
		$filter['record_count'] = $GLOBALS['db_read']->getOne($query);
		$filter = page_and_size($filter);
		
		$sql = "SELECT * FROM hr_employees AS a LEFT JOIN ship_station AS b ON a.station_id=b.station_id $where";
		$filter['employee'] = stripslashes($filter['employee']);
		set_filter($filter, $sql);	
	} else {
		//$filter = $result['filter'];
		//$sql = $result['sql'];
	}
	$list = array();
	$res = $GLOBALS['db_read']->selectLimit($sql, $filter['page_size'], $filter['start']);
	while ($rows = $GLOBALS['db_read']->fetchRow($res)) {
		$list[] = $rows;
	}
	return array('list' => $list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count'],);
}