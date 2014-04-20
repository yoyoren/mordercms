<?php
$uid=isset($_GET['agentuid'])?trim($_GET['agentuid']):'';
require(dirname(__FILE__) . '/includes/init.php');
require('includes/lib_order.php');
//require('includes/lib_user.php');

$act=$_REQUEST['step'];
if($act=='sousuo')
{
	//搜索
	$agentuid=$_SESSION['agentuid'];
	$order_num=$_POST['order_num'];
	$result=$db_read->getAll("select * from order_log where order_id={$order_num}");
	if($result==null)
	{$smarty->assign('kong','kong');}
	$smarty->assign("result",$result);
	$smarty->assign('act','sousuo');
}
elseif($act=='shijian')
{//按时间搜索
	$time1=$_REQUEST['order_time1'];	
	$time2=$_REQUEST['order_time2'];
	$time1=explode('-',$time1);
	$time2=explode('-',$time2);
	
	$time1=mktime(0, 0, 0, $time1[1], $time1[2], $time1[0]);
	$time2=mktime(23, 59, 59, $time2[1], $time2[2], $time2[0]);
	$res=$db_read->getAll("select * from order_log where editime>=$time1 and editime<=$time2");
	
	if($res==null)
	{$smarty->assign('kong1','kong1');}
	$smarty->assign('res',$res);
	$smarty->assign('act','shijian');
}
else
{
	//不搜索
	//$list=order_list();
	//print_r($list);
	$printed=$_REQUEST['printed'];
	$agentuid=$_SESSION['agentuid'];
	$smarty->assign("agentuid", $uid);
	$currentPage = $_GET["currentPage"];
	$currentPage = $currentPage==NULL?1:$currentPage;
	$pageSize = 10;
	$totalRow = 0;
	$totalPage = 0;
	$first = ($currentPage-1)*$pageSize;//循环起始值
	$last = $first + $pageSize;//循环结束值
		//$order_id=$db_read->getAll("select order_id from print_log_x where ptime>0 and stime>0");
		//print_r($order_id);
	if($printed==1)//等于1生产打印
	{	
		$rows=$db_read->getAll("select * from order_log where admin_id not in(1000,1012,9001) and order_id in(select order_id from print_log_x where stime>0) order by editime desc limit $first,$pageSize");
		//求总记录数
		$totalRow = $db_read->getOne("select count(*) from order_log where admin_id not in(1000,1012,9001) and order_id in(select order_id from print_log_x where stime>0) order by editime desc");
	}
	else
	{
			$rows=$db_read->getAll("select * from order_log where admin_id not in(1000,1012,9001) and order_id in(select order_id from print_log_x where ptime>0) order by editime desc limit $first,$pageSize");
		//求总记录数
		$totalRow = $db_read->getOne("select count(*) from order_log where admin_id not in(1000,1012,9001) and order_id in(select order_id from print_log_x where ptime>0) order by editime desc");
	}
	//求总页数
	$totalPage = ceil($totalRow / $pageSize);
	//对$last做判断，不能超过总记录数
	$last = $last>$totalRow?$totalRow:$last;

	$t=date('Y-m-d H:i:s', 1388505600);
	foreach($rows as $key=>$value)
	{
		$rows[$key]['editime']=date('Y-m-d H:i:s', $value['editime']);
	}
	$prepage=$currentPage-1;
	if($prepage<1)
	{
		$prepage=1;
	}
	$nextpage=$currentPage+1;
	if($nextpage>$totalPage)
	{
		$nextpage=$totalPage;
	}
	$smarty->assign('rows',$rows);
	$smarty->assign('prepage',$prepage);
	$smarty->assign('nextpage',$nextpage);
	$smarty->assign('last',$totalPage);
}
$smarty->display("change_order.html");

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