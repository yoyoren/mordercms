<?php
/**
 * 生产打印
 * @author: bisc
 */

require(dirname(__FILE__) . '/includes/init.php');
$_REQUEST['act'] = empty($_REQUEST['act']) ? 'list' : trim($_REQUEST['act']);

if ($_REQUEST['act'] == 'list') {
    
	
    $smarty->assign('tdm',     date('Y-m-d'));
	$smarty->assign('ur_here', '生产打印');
    $smarty->assign('full_page', 1);	
	$smarty->assign('city_arr', $_SESSION['city_arr']);
	$turn=getTurn();
	//print_r($turn);exit;
	$smarty->assign('turn', $turn);
	$smarty->assign('cakes',   $goods_array);
	
	$_REQUEST['printp'] = 2;
    $list = order_list();
	
    $smarty->assign('record_count',     $list['record_count']);
    $smarty->assign('page_count',       $list['page_count']);
	$smarty->assign('cake_count',       $list['cake_count']);
   
    $smarty->assign('filter',           $list['filter']);	
    $smarty->assign('orders',           $list['list']); 

	
	
    $smarty->display('pro_print_list.htm');
    
    
}
/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
	
    $list = order_list();
	
    $smarty->assign('record_count',     $list['record_count']);
    $smarty->assign('page_count',       $list['page_count']);
	$smarty->assign('cake_count',       $list['cake_count']);
	
    $smarty->assign('filter',           $list['filter']);	
    $smarty->assign('orders',           $list['list']); 

	
	$smarty->assign('td', 	date('Y-m-d'));
	
    make_json_result($smarty->fetch('pro_print_list.htm'), '', array('filter' => $list['filter'], 'page_count' => $list['page_count']));
} 
elseif ($_REQUEST['act'] == 'print')
{
    
	
	if (empty($_REQUEST['order_id'])) die('wrong orders!');
    if (empty($_REQUEST['sdate'])) die('wrong best_time!');	
    $order_count = $goods_count = 0;
	$html = "";
	$bdate = trim($_REQUEST['sdate']);
    $order_sn_list = explode(',', $_REQUEST['order_id']);
	$num=0;
    foreach ($order_sn_list as $order_id)
    {	
	    if($order_id != 'all')
		{   
		    $admin_id = $_SESSION['admin_id'];	
			$group    =	$_SESSION['city_group'];
			
			$psn = get_print_sn($order_id,$bdate,$admin_id,$group,1);		
			//$psn = get_print_sn1($order_id,$bdate,$admin_id,$group,1);
			$res = pro_print1($order_id);
			$result=print_goods($order_id);
			$candle=$result['candle'];	   
		
			$sql = "select a.turn,b.route_name from order_dispatch as a,ship_route as b where a.route_id=b.route_id and a.order_id=".$order_id;
			$pack = $db_read->getRow($sql);	
			
			foreach ($res as $val)
			{
			   $smarty->assign('order',    $val);
			   $smarty->assign('pack',    $pack);
			   $smarty->assign('print_sn', $psn);
			   $smarty->assign('candle',    $candle);
			  
				   $html .= $smarty->fetch('produce_print_bj_d.html') 
						 .'<div style="PAGE-BREAK-AFTER:always"></div>';	
						 $num++;			   
			}
		}
    }
    $string='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
<title>打印结果</title><body>';
	$html =$string.'<div class="noprint"><h3 style="margin:0 20px">本次打印蛋糕数量：'.$num.'</h3><hr/></div></body></html>'.$html;
	echo $html;exit;
}
elseif($_REQUEST['act'] == 'alert'){
	$date = date('Y-m-d');
	$city_code=array_keys($_SESSION['city_arr']);	
	$str=implode(',',$city_code);	
	$where = " where o.country in (".$str.") ";
	$sql = "SELECT COUNT(*) FROM order_genid as a 
	left join ecs_order_info AS o on a.order_id=o.order_id 
	left join ecs_order_goods as g on o.order_id=g.order_id 
	left join order_dispatch as d on o.order_id=d.order_id 
	left join print_log_x as c on o.order_id = c.order_id 
	".$where." and o.order_status = 1 and d.status >0 
	and (c.stime = '' or c.stime is null) and o.best_time > '".$date."' and o.best_time < '".$date." 23:01:01' and g.goods_price >40";
	$re = $db_read->getOne($sql);
	echo $re;
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
	
	
    $filter['turn']   = intval($_REQUEST['turn']);
    $filter['stan']   = intval($_REQUEST['city']);	
    $filter['cake']   = trim($_REQUEST['cake']);
    $filter['bdate']  = empty($_REQUEST['bdate']) ? date('Y-m-d') :trim($_REQUEST['bdate']);
    $filter['status'] = empty($_REQUEST['order_status']) ? 1 : intval($_REQUEST['order_status']);    
	//$filter['prinp']  = '2';
	$filter['prinp']  = trim($_REQUEST['printp']);
	$filter['ordsn']  = trim($_REQUEST['order_sn']);	
	$filter['psn']  = trim($_REQUEST['print_sn']);	
	$filter['page']     = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);
	
    $page = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);
	
	$size = 30;	
	//$size = 2;	
	$city_code=array_keys($_SESSION['city_arr']);	
	$str=implode(',',$city_code);	
	$where = " where o.order_status = 1 and o.country in (".$str.") ";
	
	 
	 
	
	if($filter['turn'])
	{
	   $where .= " and d.turn = ".$filter['turn'];
	}	
	if($filter['stan'])
	{
	   $where .= " and o.country = ".$filter['stan'];
	}
	if($filter['status'])
	{
	   $where .= " and d.status >0 ";
	}
	if($filter['cake'])
	{
	   $where .= " and g.goods_id = '".$filter['cake']."' ";
	}
	if($filter['prinp'] == 1)
	{
	   $where .= " and c.stime >0 ";
	}
	if($filter['prinp'] == 2)
	{
	   $where .= " and (c.stime = '' or c.stime is null) ";
	}
	if($filter['bdate'])
	{
	   $where .= " and o.best_time > '".$filter['bdate']."' and o.best_time < '".$filter['bdate']." 23:01:01' ";
	}
	if($filter['ordsn'])
	{
	   $where = " where o.order_sn = '".$filter['ordsn']."' ";
	}
	if($filter['psn'])
	{
	   $where = " where o.best_time > '".$filter['bdate']."' and o.best_time < '".$filter['bdate']." 23:01:01' and c.city_group=".$_SESSION['city_group']."  and c.print_sn = '".$filter['psn']."' ";
	}	
    /*原系统的订单打印记录数*/
   /* $sql = "SELECT COUNT(*) FROM order_genid as a left join ecs_order_info AS o on a.order_id=o.order_id left join ecs_order_goods as g on o.order_id=g.order_id ".
	        "left join order_dispatch as d on o.order_id=d.order_id left join print_log as c on o.order_id = c.order_id ". $where. " and g.goods_price >40 ";*/
	/*修改后的订单打印记录数*/
	$sql = "SELECT COUNT(*) FROM order_genid as a left join ecs_order_info AS o on a.order_id=o.order_id left join ecs_order_goods as g on o.order_id=g.order_id left join order_dispatch as d on o.order_id=d.order_id left join print_log_x as c on o.order_id = c.order_id ". $where. " and g.goods_price >40";
	 

    $record_count   = $GLOBALS['db_read']->getOne($sql);
	
    $page_count     = $record_count > 0 ? ceil($record_count / $size) : 1;

   
	  $sql = "select o.order_id,o.order_sn,o.order_status,o.best_time,o.address,o.add_time,".
	       "c.stime,c.print_sn,c.st,group_concat(g.goods_name) as goods,sum(g.goods_number) as num ".
	       "from order_genid as a left join ecs_order_info as o on a.order_id=o.order_id ".
		   "left join ecs_order_goods as g on o.order_id=g.order_id ".
		   "left join order_dispatch as d on o.order_id=d.order_id ".
		   "left join  print_log_x as c on o.order_id = c.order_id ".$where.
	       "and g.goods_price>40 group by o.order_id order by o.best_time ".
           "LIMIT " . ($filter['page'] - 1) * $size . ",$size";	
	
	
    $rs = $GLOBALS['db_read']->getAll($sql);
	$n = 0;
	foreach ($rs as $key => $val)
	{
	   $sql = "select status from order_dispatch where order_id=".$val['order_id'];
	   $rs[$key]['status'] = $GLOBALS['db_read']->getOne($sql);
	   
	   $rs[$key]['add_time'] = date('Y-m-d H:i',$val['add_time']);
	   $rs[$key]['best_time'] = substr($val['best_time'],0,16);
	   $rs[$key]['order_status'] = $os[$val['order_status']];
	   $rs[$key]['i'] = $key + 1;
	   $n += $val['num'];
	}

  
	$arr = array('list' => $rs, 'filter' => $filter, 'page_count' => $page_count, 'record_count' => $record_count,'cake_count' => $n);

    return $arr;
}


?>
