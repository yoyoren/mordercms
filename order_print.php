<?php
/**
 * 配送打印
 * @author: bisc
 */

require(dirname(__FILE__) . '/includes/init.php');
$_REQUEST['act'] = empty($_REQUEST['act']) ? 'list' : trim($_REQUEST['act']);
$city_code = db_create_in(array_keys($_SESSION['city_arr']));
if ($_REQUEST['act'] == 'list') {
    
	$_REQUEST['psize']=30;
    $smarty->assign('tdm',     date('Y-m-d'));
	$smarty->assign('ur_here', '配送打印');
	$smarty->assign('action_link', array('href'=>'print_stat.php', 'text' => '打印结果查询'));
    $smarty->assign('full_page', 1);
	$smarty->assign('city_arr', $_SESSION['city_arr']);
	$sql="select station_id,station_name from ship_station where city_code $city_code AND flag =1 " ;
	$stations=$db_read->getAll($sql);
	//print_r($stations);exit;	
	$smarty->assign('stations',   $stations);
	$turn=getTurn();	
	$smarty->assign('turn', $turn);
	$smarty->assign('cakes',   $goods_array);
	
	$_REQUEST['prints'] = 2;
    $list = order_list();
	
	$smarty->assign('record_count',     $list['record_count']);
    $smarty->assign('page_count',       $list['page_count']);
    
    $smarty->assign('filter',           $list['filter']);	
    $smarty->assign('orders',           $list['list']); 
    
    $smarty->display('order_print_list.htm');
    
    
}
/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
	
    $list = order_list();	
    $smarty->assign('record_count',     $list['record_count']);
    $smarty->assign('page_count',       $list['page_count']);
    $smarty->assign('filter',           $list['filter']);	
    $smarty->assign('orders',           $list['list']); 

	$str = '';
	foreach($list['filter'] as $key => $val)
	{
	   $str .= '&'.$key.'='.$val;
	}  
    $smarty->assign('querystr', 	$str);
	$smarty->assign('td', 	date('Y-m-d'));	
    make_json_result($smarty->fetch('order_print_list.htm'), '', array('filter' => $list['filter'], 'page_count' => $list['page_count']));
} 
elseif ($_REQUEST['act'] == 'print')
{
	  
	if (empty($_REQUEST['order_id'])) die('wrong orders!');
    if (empty($_REQUEST['sdate'])) die('wrong best_time!');	
    $order_count = $goods_count = 0;
	$html = '';
	$bdate = trim($_REQUEST['sdate']);
    $order_sn_list = explode(',', $_REQUEST['order_id']);
	$n = in_array('all',$order_sn_list) ? count($order_sn_list)-2 : count($order_sn_list)-1;
    foreach ($order_sn_list as $key => $order_id)
    {	
        if($order_id != 'all')
		{
		      $admin_id = $_SESSION['admin_id'];
			  $group    =	$_SESSION['city_group'];
			  $psn = get_print_sn($order_id,$bdate,$admin_id,$group,2);
			
			@$order = order_detail($order_id);
			//print_r($order);
			@$pay  =  pay_info($order_id);
			
			//print_r($pay);
			@$goods = print_goods($order_id);
			//print_r($goods);
			$sql = "select a.turn,b.route_name from order_dispatch as a,ship_route as b where a.route_id=b.route_id and a.order_id=".$order_id;
			
			$pack = $db_read->getRow($sql);
			//print_r($pack);
			//获取冰包号
			$sql = "select ice_bag_num from order_ice_bag where order_id = $order_id limit 1";
			$ice_bag_arr = $db_read->getAll($sql);
			//print_r($ice_bag_arr);
			$ice_bage_str = '';
			foreach($ice_bag_arr as $v){
				if($v['ice_bag_num'] <10) $v['ice_bag_num'] = '0'.$v['ice_bag_num'];
				$ice_bage_str .= empty($ice_bage_str) ? $v['ice_bag_num'] : ','.$v['ice_bag_num'];
			}
			$array=array();
	        foreach($ice_bag_arr as $k=>$v){
				$array[$k]=$pack['route_name']."-0".$pack['turn']."-0".$ice_bag_arr[$k]['ice_bag_num'];
			}
			//print_r($array);
			//print_r($array);
			$smarty->assign('array',    $array);
			//exit;
			$bdate = substr($order['best_time'],0,10);
			$ad = substr($order['best_time'],11,5) >= '19:00' ? '晚' :'';
		
			$smarty->assign('order',    $order);
			$smarty->assign('pay',    $pay);
			$smarty->assign('tips',    $bdate.$ad);
			$smarty->assign('pack',    $pack);
			$smarty->assign('ice_bag_str',$ice_bage_str);
			$smarty->assign('goods',    $goods);
			$smarty->assign('psn', $psn);
			
			
			if($_SESSION['city_group'] == '441'){
				$html .= $smarty->fetch('order_print_bj.htm');
			}else{
				$html .= $smarty->fetch('order_print_sh.html'); 
			}
			
			$html .= $key == $n ? '' : '<div style="PAGE-BREAK-AFTER:always"></div>';	
	
		}
    }
	echo $html;exit;
}


/*
* 取得订单列表
*/
function order_list()
{
    $os[0]='未确认';
    $os[1]='已确认';
    $os[2]='已取消';
    $os[3]='无效';
    $os[4]='退货';

    $ps[0]='未付款';
    $ps[1]='付款中';
    $ps[2]='已付款';
   
	$city_arr=$_SESSION['city_arr'];
	$city_code=array_keys($city_arr);
	$filter['station']  = empty($_REQUEST['station'])  ? '' : intval($_REQUEST['station']);
	$filter['turn']   = intval($_REQUEST['turn']);	
    $filter['city']   = empty($_REQUEST['city']) ? $city_code[0] : intval($_REQUEST['city']);		
    $filter['diao']   = trim($_REQUEST['diaodu']);
    $filter['bdate']  = empty($_REQUEST['bdate']) ? date('Y-m-d') :trim($_REQUEST['bdate']);
    $filter['status'] = empty($_REQUEST['order_status']) ? 1 : intval($_REQUEST['order_status']);
    $filter['prints']  = trim($_REQUEST['prints']);
	$filter['ordsn']  = trim($_REQUEST['order_sn']);
	
	$filter['page'] = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);
	
  
	$size = $filter['psize']= intval($_REQUEST['psize']);	
	
	
	$where = "where o.order_status = ".$filter['status']." and d.status >0 ";
    

	
	if($filter['turn'])
	{
	   $where .= " and d.turn = ".$filter['turn'];
	}
	if($filter['prints'] == 1)
	{
	   $where .= " and c.ptime > 0 ";
	}
	if($filter['prints'] == 2)
	{
	   $where .= " and (c.ptime = '' or c.ptime is null) ";
	}
	if($filter['city'])
	{
	   $where .= " and o.country = ".$filter['city'];
	}
	if($filter['bdate'])
	{
	   $where .= " and o.best_time > '".$filter['bdate']."' and o.best_time < '".$filter['bdate']." 23:01:01' ";
	}
	if($filter['ordsn'])
	{
	   $where = " where o.order_sn = '".$filter['ordsn'] ."' ";
	}
    if($filter['station'])
	{
	   $where .= " and s.station_id = '".$filter['station'] ."' ";
	}
   
   /* $sql = "SELECT COUNT(*) FROM order_genid as a left join ecs_order_info AS o on a.order_id=o.order_id ".
	        "left join order_dispatch as d on o.order_id=d.order_id left join  print_log  as c on o.order_id = c.order_id ". $where;*/
	$sql = "SELECT COUNT(*) FROM order_genid as a left join ecs_order_info AS o on a.order_id=o.order_id left join order_dispatch as d on o.order_id=d.order_id left join print_log_x as c on o.order_id = c.order_id left join ship_route as r on d.route_id=r.route_id left join ship_station as s on r.station_id=s.station_id ". $where;
   
    $record_count   = $GLOBALS['db_read']->getOne($sql);
	
    $page_count     = $record_count > 0 ? ceil($record_count / $size) : 1;
    
   
	/*$sql = "select o.order_id,o.order_sn,o.order_status,o.orderman,o.ordertel,o.consignee,o.mobile,o.best_time,o.order_amount,o.kfgh,o.add_time,o.pay_status, "
	     . "o.country,o.city,o.district,o.address,c.ptime,c.print_sn,c.pt,c.stime,group_concat(g.goods_name) as goods ".
	       "from order_genid as a left join ecs_order_info as o on a.order_id=o.order_id ".
		   "left join ecs_order_goods as g on o.order_id=g.order_id ".
		   "left join order_dispatch as d on o.order_id=d.order_id ".
		   "left join print_log as c on o.order_id = c.order_id ".$where.
	       "and g.goods_price>40 group by o.order_id order by o.best_time ".
           "LIMIT " . ($filter['page'] - 1) * $size . ",$size"; */
	$sql = "select o.order_id,o.order_sn,o.order_status,o.orderman,o.ordertel,o.consignee,o.mobile,o.best_time,o.order_amount,o.kfgh,o.add_time,o.pay_status, "
	     . "o.country,o.city,o.district,o.address,c.ptime,c.print_sn,c.pt,c.stime,group_concat(g.goods_name) as goods ".
	       "from order_genid as a left join ecs_order_info as o on a.order_id=o.order_id ".
		   "left join ecs_order_goods as g on o.order_id=g.order_id ".
	       "left join order_dispatch as d on o.order_id=d.order_id ".
		   "left join print_log_x as c on o.order_id = c.order_id ".
		   "left join ship_route as r on d.route_id=r.route_id ".
		   "left join ship_station as s on r.station_id=s.station_id ".$where.
	       "and g.goods_price>40 group by o.order_id order by o.best_time ".
           "LIMIT " . ($filter['page'] - 1) * $size . ",$size";
	//echo $sql;
    $rs = $GLOBALS['db_read']->getAll($sql);
	
	foreach ($rs as $key => $val)
	{
	   $sql = "select status from order_dispatch where order_id=".$val['order_id'];
	   $rs[$key]['status'] = $GLOBALS['db_read']->getOne($sql);
	   
	   $sql = "select b.route_name from order_dispatch as a,ship_route as b where a.route_id=b.route_id and a.order_id=".$val['order_id'];
	   $rs[$key]['route_name'] = $GLOBALS['db_read']->getOne($sql);
	   
	   $rs[$key]['add_time'] = date('Y-m-d H:i',$val['add_time']);
	   $rs[$key]['best_time'] = substr($val['best_time'],0,16);
	   $rs[$key]['order_status'] = $os[$val['order_status']].','.$ps[$val['pay_status']];
	   $rs[$key]['i'] = $key + 1;
	   $rs[$key]['address'] = region_name($val['city']).region_name($val['district']).$val['address'];
	   if($filter['ordsn'])
	   {
	     $filter['bdate'] = substr($val['best_time'],0,10);
	   }
	}

  //print_r($filter);
    $arr = array('list' => $rs, 'filter' => $filter, 'page_count' => $page_count, 'record_count' => $record_count,'cake_count' => $n);
	
    return $arr;
}

?>
