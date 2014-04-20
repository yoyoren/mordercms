<?php
/**
 * 配送调度单管理
 * @author: bisc
 */
require(dirname(__FILE__) . '/includes/init.php');
//检查权限
admin_priv('d_dis');

$_REQUEST['act'] = empty($_REQUEST['act']) ? 'list' : trim($_REQUEST['act']);
//初始化城市编号,格式如 ：IN(441，443)
$city_code = db_create_in(array_keys($_SESSION['city_arr']));
if ($_REQUEST['act'] == 'list') {
    admin_priv('d_dis');
    
    $_REQUEST['sdate'] = date('Y-m-d');
    $_REQUEST['edate'] = date('Y-m-d',time()+24*3600);
	$_REQUEST['status'] = 1;
	$_REQUEST['otatus'] = 1;
	
   	$res = getTurn();
    // 配送站（手动分包）
	$stations = $db_read->getAll("SELECT route_id, station_code FROM view_ship_route WHERE city_code $city_code AND flag =1 GROUP BY station_code");
	// 配送站（搜索）
	$stations2 = $db_read->getAll("SELECT station_id, station_name FROM ship_station WHERE city_code $city_code AND flag =1 ");
	//配送包号（即每个站点下的分区）
    $route = $db_read->getAll("SELECT route_id,route_name FROM view_ship_route where city_code $city_code and flag =1 order by route_name ");
	
    $list = order_list();

    $smarty->assign('ur_here', '调度管理');
    $smarty->assign('full_page', 1);
    $smarty->assign('stations',   $stations);						// 配送站（手动分包）
    $smarty->assign('stations2',   $stations2);						// 配送站（搜索）
    $smarty->assign('timeplan',   $res);							//配送批此
    $smarty->assign('route',   $route);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
    $smarty->assign('filter',       $list['filter']);	
	$smarty->assign('order_list',   $list['orders']);  
	$smarty->assign('city_arr',$_SESSION['city_arr']);
	$smarty->display('dispatch_list.htm');
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
    $smarty->assign('new_count',      '0');
	//make_json_error($list['orders']);
    make_json_result($smarty->fetch('dispatch_list.htm'), '', array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}
elseif ($_REQUEST['act'] == 'check_sg')
{
    //admin_priv('21');
	$order_id = intval($_REQUEST['id']);
	$result = $db_read->getRow("SELECT route_id AS num,turn FROM order_dispatch WHERE order_id='" . intval($_REQUEST['id']) . "'");
	
	
	
	
	if ($result['num'] == 0) {
		make_json_error('You don\'t have to subcontract some of the orders, please check!');
	} else {
		$sql = "update order_dispatch set status=1,admin='".$_SESSION['admin_id']."', add_time = ".time()." WHERE order_id = '$order_id'";
		$res = $GLOBALS['db_write']->query($sql);
		$count = $db_read->getOne("select count(1) from order_delivery where order_id = '$order_id'");
		$res2 = empty($count) ? $db_write->query("insert order_delivery (order_id,employee_id) values ($order_id,0)") : ''; 
		//$sql = "insert order_delivery (order_id,employee_id) values ($order_id,0)";
		//$db->query($sql);    
		
		if ($res)
		{
			$url = 'shipping_dispatch.php?act=query&' . str_replace('act=check_sg', '', $_SERVER['QUERY_STRING']);
			los_header("Location: $url\n");exit;
		}
		else
		{
			make_json_error('删除出错!请检查！');
		}
	}
} elseif ($_REQUEST['act'] == 'remove_batch') {	
	//admin_priv('21');
	
	/*$order_id   = $_REQUEST['id'];        // 订单id（逗号格开的多个订单id）
    $order_id_list = explode(',', $order_id);
	
	foreach ($order_id_list as $order)
    {
         $db_write->query("update order_dispatch set status=1,admin='".$_SESSION['admin_id']."', add_time = '".time()."' WHERE order_id = '$order'");
		 $count = $db_read->getOne("select count(1) from order_delivery where order_id = '$order'");
		
		 $res = empty($count) ? $db_write->query("insert order_delivery (order_id,employee_id) values ($order,0)") : ''; 
         
         //$sql = "insert order_delivery (order_id,employee_id) values ($order,0)";
		 //$db->query($sql);
	}

    $url = 'shipping_dispatch.php?act=query&' . str_replace('act=remove_batch', '', $_SERVER['QUERY_STRING']);
    los_header("Location: $url\n");
    exit;*/
}
elseif ($_REQUEST['act'] == 'remove_order') // @单个删除
{
	$id = intval($_REQUEST['id']);
	$res = $db_write->query("UPDATE order_dispatch SET `status`=0 WHERE order_id='$id'");
	$db_write->query("DELETE FROM order_delivery WHERE order_id='$id'");
	
	if ($res) {
		$url = "shipping_dispatch.php?act=query&" . str_replace('act=remove_order', '', $_SERVER['QUERY_STRING']);
		los_header("Location: $url\n");
		exit();
	} else {
		make_json_error('删除错误，请与管理员联系！');
	}
}
elseif ($_REQUEST['act'] == 'delete_batch')	//批量删除
{

	$order_id   = $_REQUEST['id'];
    $order_id_list = explode(',', $order_id);
	foreach ($order_id_list as $order)
    {
         $db_write->query("update order_dispatch set status=0 WHERE order_id = '$order'");
		 $sql = "delete from order_delivery where order_id = '$order' limit 1";
		 $db_write->query($sql);
	}

    $url = 'shipping_dispatch.php?act=query&' . str_replace('act=delete_batch', '', $_SERVER['QUERY_STRING']);
    los_header("Location: $url\n");
    exit;
}
//手动分包
elseif ($_REQUEST['act'] == 'batch_operate')
{
    $order_id   = $_REQUEST['order_id'];       
    $order_id_list = explode(',', $order_id);

    if (!($_REQUEST['route']))  sys_msg('请选择包号!', 1);

    $route = intval($_POST['route']);
	foreach ($order_id_list as $order)
	{
		$pcs = intval($_POST['pcs']);
		$connect = $pcs ? ",turn = '$pcs'" : "";
	    $db_write->query("update order_dispatch set route_id = '$route' ".$connect." WHERE order_id = '$order'");				
	}
}
elseif ($_REQUEST['act'] == 'sub_list')	//获取对应站点的配送包号（下拉菜单的）
{
	require(ROOT_PATH . 'includes/cls_json.php');
	
	$parent = !empty($_REQUEST['station']) ? trim($_REQUEST['station']) : '';
	
	$sql = "select station_id FROM ship_route WHERE route_id = '$parent' ";
	$station = $db_read->getOne($sql);

    $routes = $db_read->getAll("SELECT route_id,route_name FROM ship_route where station_id = '$station' and flag =1 order by route_name ");
	
	$arr['regions'] = $routes;
	$arr['target']  = !empty($_REQUEST['target']) ? stripslashes(trim($_REQUEST['target'])) : '';
	$arr['target']  = htmlspecialchars($arr['target']);
	
	$json = new JSON;
	echo $json->encode($arr);
} elseif ($_REQUEST['act'] == 'batch_check') {		// @批量审核
	$id = explode(',', $_REQUEST['id']);
	
	foreach ($id as $order_id) {
			$sql = "select id from order_ice_bag where order_id=$order_id";
			$max = $GLOBALS['db_write']->getOne($sql);
			if(!$max){
				//指定蛋糕冰包
				$result = $db_read->getRow("SELECT route_id AS num,turn FROM order_dispatch WHERE order_id='" . $order_id . "'");
			
				$sql = "select o.order_sn,o.best_time,o.scts,o.card_name,o.card_message,g.goods_name as gname,g.goods_sn,g.goods_attr,g.goods_number,c.*, "
				     . "o.country,o.address from ecs_order_info as o "
				     . "left join ecs_order_goods as g on o.order_id=g.order_id "
				     . "left join print_goods as c on g.goods_id=c.goods_id "
			         . "where o.order_id = '$order_id' and (g.goods_price>100 or g.goods_sn='34')";
				$goods = $GLOBALS['db_read']->getAll($sql);
				
				foreach($goods as $v){
					if($v['goods_number'] >1){
						for($i=0;$i<$v['goods_number'];$i++){
							
							//对应磅数的最大编号
							$sql = "select max(ice_bag_num) from order_ice_bag where addtime='".date('Y-m-d')."' and route_id=".$result['num']." and turn=".$result['turn']." and goods_attr='".$v['goods_attr']."'";
							$max = intval($GLOBALS['db_write']->getOne($sql));
							if($v['goods_attr'] == '1.0磅' || $v['goods_attr'] == '2.0磅'){
								$n = 3;
							}elseif($v['goods_attr'] == '3.0磅'){
								$n = 2;
							}else{
								$n = 1;
							}
							if($max){	//此磅数已存在
								$sql ="select count(*) from order_ice_bag where addtime='".date('Y-m-d')."' and route_id=".$result['num']." and turn=".$result['turn']." and ice_bag_num=$max"." and goods_attr='".$v['goods_attr']."'";
								$count = $GLOBALS['db_write']->getOne($sql);
								if($count < $n){
									$ice_bag_num = $max;
								}else{
									$sql = "select max(ice_bag_num) from order_ice_bag where addtime='".date('Y-m-d')."' and route_id=".$result['num']." and turn=".$result['turn'];
									$lastnum= intval($GLOBALS['db_write']->getOne($sql));
									$ice_bag_num = ++$lastnum;
								}
								
								
							}else{	//此磅数不存在
								$sql = "select max(ice_bag_num) from order_ice_bag where addtime='".date('Y-m-d')."' and route_id=".$result['num']." and turn=".$result['turn'];
								$max = intval($GLOBALS['db_write']->getOne($sql));
								$ice_bag_num = empty($max) ? 1 : ++$max;
							}
							
							$sql = "insert into order_ice_bag (order_id,route_id,turn,goods_attr,ice_bag_num,addtime) values ('$order_id','".$result['num']."','".$result['turn']."','".$v['goods_attr']."','$ice_bag_num','".date('Y-m-d')."')";
							$re = $GLOBALS['db_write']->query($sql);
						}
					}else{
				
							//对应磅数的最大编号
							$sql = "select max(ice_bag_num) from order_ice_bag where addtime='".date('Y-m-d')."' and route_id=".$result['num']." and turn=".$result['turn']." and goods_attr='".$v['goods_attr']."'";
							$max = intval($GLOBALS['db_write']->getOne($sql));
							if($v['goods_attr'] == '1.0磅' || $v['goods_attr'] == '2.0磅'){
								$n = 3;
							}elseif($v['goods_attr'] == '3.0磅'){
								$n = 2;
							}else{
								$n = 1;
							}
							if($max){	//此磅数已存在
								$sql ="select count(*) from order_ice_bag where addtime='".date('Y-m-d')."' and route_id=".$result['num']." and turn=".$result['turn']." and ice_bag_num=$max"." and goods_attr='".$v['goods_attr']."'";
								$count = $GLOBALS['db_write']->getOne($sql);
								if($count < $n){
									$ice_bag_num = $max;
								}else{
									$sql = "select max(ice_bag_num) from order_ice_bag where addtime='".date('Y-m-d')."' and route_id=".$result['num']." and turn=".$result['turn'];
									$lastnum= intval($GLOBALS['db_write']->getOne($sql));
									$ice_bag_num = ++$lastnum;
								}
								
								
							}else{	//此磅数不存在
								$sql = "select max(ice_bag_num) from order_ice_bag where addtime='".date('Y-m-d')."' and route_id=".$result['num']." and turn=".$result['turn'];
								$max = intval($GLOBALS['db_write']->getOne($sql));
								$ice_bag_num = empty($max) ? 1 : ++$max;
								
							}
							
							$sql = "insert into order_ice_bag (order_id,route_id,turn,goods_attr,ice_bag_num,addtime) values ('$order_id','".$result['num']."','".$result['turn']."','".$v['goods_attr']."','$ice_bag_num','".date('Y-m-d')."')";
							$re = $GLOBALS['db_write']->query($sql);
					}
				}
			}
		
		
		$field = "`status`=1,admin='$_SESSION[admin_id]',add_time='" . time() . "'";
		$db_write->query("UPDATE order_dispatch SET " . $field . " WHERE order_id='$order_id'");
		$count = $db_read->getOne("SELECT COUNT(1) FROM order_delivery WHERE order_id='$order_id'");
		empty($count) ? $db_write->query("INSERT INTO order_delivery (order_id,employee_id) VALUES('$order_id',0)") : '';
	}
	$url = "shipping_dispatch.php?act=query&" . str_replace('act=batch_check', '', $_SERVER['QUERY_STRING']);
	los_header("Location: $url\n");
	exit();
} 
elseif ($_REQUEST['act'] == 'single_check') {	// 单个审核订单		
	$order_id = $_REQUEST['id'];
	$result = $db_read->getRow("SELECT route_id AS num,turn FROM order_dispatch WHERE order_id='" . intval($_REQUEST['id']) . "'");
	//if ($num == 0 || empty($num))			make_json_error('审核的订单没有分包！');
	
	if (empty($result['num']))			make_json_error('审核的订单没有分包！');
	
	
			$sql = "select id from order_ice_bag where order_id=$order_id";
			$max = $GLOBALS['db_write']->getOne($sql);
			if(!$max){
				//指定蛋糕冰包
				$result = $db_read->getRow("SELECT route_id AS num,turn FROM order_dispatch WHERE order_id='" . $order_id . "'");
			
				$sql = "select o.order_sn,o.best_time,o.scts,o.card_name,o.card_message,g.goods_name as gname,g.goods_sn,g.goods_attr,g.goods_number,c.*, "
				     . "o.country,o.address from ecs_order_info as o "
				     . "left join ecs_order_goods as g on o.order_id=g.order_id "
				     . "left join print_goods as c on g.goods_id=c.goods_id "
			         . "where o.order_id = '$order_id' and (g.goods_price>100 or g.goods_sn='34')";
				$goods = $GLOBALS['db_read']->getAll($sql);
				
				foreach($goods as $v){
					if($v['goods_number'] >1){
						for($i=0;$i<$v['goods_number'];$i++){
							
							//对应磅数的最大编号
							$sql = "select max(ice_bag_num) from order_ice_bag where addtime='".date('Y-m-d')."' and route_id=".$result['num']." and turn=".$result['turn']." and goods_attr='".$v['goods_attr']."'";
							$max = intval($GLOBALS['db_write']->getOne($sql));
							if($v['goods_attr'] == '1.0磅' || $v['goods_attr'] == '2.0磅'){
								$n = 3;
							}elseif($v['goods_attr'] == '3.0磅'){
								$n = 2;
							}else{
								$n = 1;
							}
							if($max){	//此磅数已存在
								$sql ="select count(*) from order_ice_bag where addtime='".date('Y-m-d')."' and route_id=".$result['num']." and turn=".$result['turn']." and ice_bag_num=$max"." and goods_attr='".$v['goods_attr']."'";
								$count = $GLOBALS['db_write']->getOne($sql);
								if($count < $n){
									$ice_bag_num = $max;
								}else{
									$sql = "select max(ice_bag_num) from order_ice_bag where addtime='".date('Y-m-d')."' and route_id=".$result['num']." and turn=".$result['turn'];
									$lastnum= intval($GLOBALS['db_write']->getOne($sql));
									$ice_bag_num = ++$lastnum;
								}
								
								
							}else{	//此磅数不存在
								$sql = "select max(ice_bag_num) from order_ice_bag where addtime='".date('Y-m-d')."' and route_id=".$result['num']." and turn=".$result['turn'];
								$max = intval($GLOBALS['db_write']->getOne($sql));
								$ice_bag_num = empty($max) ? 1 : ++$max;
							}
							
							$sql = "insert into order_ice_bag (order_id,route_id,turn,goods_attr,ice_bag_num,addtime) values ('$order_id','".$result['num']."','".$result['turn']."','".$v['goods_attr']."','$ice_bag_num','".date('Y-m-d')."')";
							$re = $GLOBALS['db_write']->query($sql);
						}
					}else{
				
							//对应磅数的最大编号
							$sql = "select max(ice_bag_num) from order_ice_bag where addtime='".date('Y-m-d')."' and route_id=".$result['num']." and turn=".$result['turn']." and goods_attr='".$v['goods_attr']."'";
							$max = intval($GLOBALS['db_write']->getOne($sql));
							if($v['goods_attr'] == '1.0磅' || $v['goods_attr'] == '2.0磅'){
								$n = 3;
							}elseif($v['goods_attr'] == '3.0磅'){
								$n = 2;
							}else{
								$n = 1;
							}
							if($max){	//此磅数已存在
								$sql ="select count(*) from order_ice_bag where addtime='".date('Y-m-d')."' and route_id=".$result['num']." and turn=".$result['turn']." and ice_bag_num=$max"." and goods_attr='".$v['goods_attr']."'";
								$count = $GLOBALS['db_write']->getOne($sql);
								if($count < $n){
									$ice_bag_num = $max;
								}else{
									$sql = "select max(ice_bag_num) from order_ice_bag where addtime='".date('Y-m-d')."' and route_id=".$result['num']." and turn=".$result['turn'];
									$lastnum= intval($GLOBALS['db_write']->getOne($sql));
									$ice_bag_num = ++$lastnum;
								}
								
								
							}else{	//此磅数不存在
								$sql = "select max(ice_bag_num) from order_ice_bag where addtime='".date('Y-m-d')."' and route_id=".$result['num']." and turn=".$result['turn'];
								$max = intval($GLOBALS['db_write']->getOne($sql));
								$ice_bag_num = empty($max) ? 1 : ++$max;
								
							}
							
							$sql = "insert into order_ice_bag (order_id,route_id,turn,goods_attr,ice_bag_num,addtime) values ('$order_id','".$result['num']."','".$result['turn']."','".$v['goods_attr']."','$ice_bag_num','".date('Y-m-d')."')";
							$re = $GLOBALS['db_write']->query($sql);
					}
				}
			}

	$field = "`status`=1,admin='$_SESSION[admin_id]',add_time='" . time() . "'";
	$db_write->query("UPDATE order_dispatch SET " . $field . " WHERE order_id='$order_id'");
	$count = $db_read->getOne("SELECT COUNT(*) FROM order_delivery WHERE order_id='$order_id'");
	empty($count) ? $db_write->query("INSERT INTO order_delivery (order_id,employee_id) VALUES('$order_id',0)") : '';
	$url = "shipping_dispatch.php?act=query&" . str_replace('act=single_check', '', $_SERVER['QUERY_STRING']);
	los_header("Location: $url\n");
	exit();
}

/*
* 取得调度单信息列表
*/
function order_list()
{
	$filter['status']   = empty($_REQUEST['status'])   ? 0  : intval($_REQUEST['status']);		
	$filter['otatus']   = intval($_REQUEST['otatus']);		
	$filter['sdate']    = empty($_REQUEST['sdate'])    ? '' : trim($_REQUEST['sdate']);
	$filter['edate']    = empty($_REQUEST['edate'])    ? '' : trim($_REQUEST['edate']);
    $filter['order_sn'] = empty($_REQUEST['order_sn']) ? '' : trim($_REQUEST['order_sn']);
	$filter['turn']     = empty($_REQUEST['turn'])     ? 0  : intval($_REQUEST['turn']);
	$filter['station']  = empty($_REQUEST['station'])  ? '' : intval($_REQUEST['station']);
	$filter['route_id']  = empty($_REQUEST['route_s'])  ? '' : intval($_REQUEST['route_s']);
    $filter['page']     = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);	
	$filter['big_goods']= intval($_REQUEST['big_goods']);
    $filter['sort_by']  = empty($_REQUEST['sort_by']) ? 'best_time' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'ASC' : trim($_REQUEST['sort_order']);
    $filter['city'] = empty($_REQUEST['city']) ? '' : trim($_REQUEST['city']);
			
	$where = " where country ".$GLOBALS['city_code']." ";
	$join='';
	if($filter['sdate'])
	{
	   $where .= " and best_time > '".$filter['sdate']."' ";
	}
	if($filter['edate'])
	{
	   $where .= " and best_time < '".$filter['edate']." 23:30:30' ";
	}
	if($filter['order_sn'])
	{
	   $where .= " and right(a.order_id,5) ='".$filter['order_sn']."' ";
	}
	if($filter['status'] ==1)
	{
	   $where .= " and status =0";
	}
	if($filter['status'] ==2)
	{
	   $where .= " and status =1";
	}
	if($filter['otatus'] <9 )
	{
	   $where .= " and order_status = $filter[otatus]";
	}
	if($filter['turn'])
	{
	   $where .= " and turn = '".$filter['turn']."' ";
	}
	if($filter['city'])
	{
	   $where .= " and country = '".$filter['city']."' ";
	}
	if($filter['station'] && $filter['station'] != 100 )
	{
	   $where .= " and s.station_id = '".$filter['station']."' ";
	}
	if($filter['station'] == 100)
	{
	   $where .= " and c.route_id =0 ";		
	}
	if($filter['route_id'])
	{
	   $where .= " and c.route_id ='".$filter['route_id']."' ";		
	}
	if($filter['sort_by'] =='shipping_station_name')
	{
	   $orderby = " order by s.station_code desc ";
	}
	else
	{
	   $orderby = " ";		
	}
	if($filter['big_goods']){
		$join .= " left join ecs_order_goods as g on g.order_id=a.order_id ";
		$where .= " and g.goods_price > 1000 ";
	}

	$size = 20;	
	$sql = "select count(1) ".
	       "from order_genid as a ".
		   "left join ecs_order_info as o on a.order_id=o.order_id ".
	       "left join order_dispatch as c on a.order_id=c.order_id ".
		   "left join ship_route as d on c.route_id=d.route_id ".
			$join.
		   "left join ship_station as s on d.station_id=s.station_id ".$where;

    $record_count   = $GLOBALS['db_read']->getOne($sql);
    $page_count     = $record_count > 0 ? ceil($record_count / $size) : 1;
	
	$sql = "select a.order_id,o.order_sn,o.city,o.address,o.best_time,c.*,d.*,s.station_name,s.station_code ".
	       "from order_genid as a ".
		   "left join ecs_order_info as o on a.order_id=o.order_id ".
	       "left join order_dispatch as c on a.order_id=c.order_id ".
		   "left join ship_route as d on c.route_id=d.route_id ".
			$join.
		   "left join ship_station as s on d.station_id=s.station_id ".$where.$orderby.
		   " LIMIT " . ($filter['page'] - 1) * $size . ",$size";
		   
	$res = $GLOBALS['db_read']->GetAll($sql);

	foreach($res as $key=> $val)
    {
	    
    	$sql = "select count(1) from ecs_order_goods where goods_price>1000 and order_id=".$val['order_id'];
		$res[$key]['big'] = $GLOBALS['db_read']->getOne($sql) ? 1 : 0;
		$res[$key]['address'] = region_name($val['city']).' '.$val['address'];
	    
		$res[$key]['i'] = $key +1;  
		$res[$key]['sname'] = admin_name($val['admin']);
	}	
    $arr = array('orders' => $res, 'filter' => $filter, 'page_count' => $page_count, 'record_count' => $record_count);

	
    return $arr;
}

function admin_name($aid)
{
    $sql = "SELECT sname FROM order_admin WHERE id = '$aid'";

    return $GLOBALS['db_read']->GetOne($sql);
}

?>
