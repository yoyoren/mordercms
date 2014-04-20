<?php
/**
 * 桂圆冰激凌统计
 * $Author: bisc $
 * $Id: cake_stat.php 
*/

require(dirname(__FILE__) . '/includes/init.php');

$_REQUEST['act'] = empty($_REQUEST['act']) ? 'list' : trim($_REQUEST['act']);

if ($_REQUEST['act'] == 'list')
{
	$smarty->assign('ur_here',     '蛋糕统计');
    $smarty->assign('full_page',   1);
	
	$res = getTurn();
	$smarty->assign('timeplan',   $res);
	$smarty->assign('city_arr',$_SESSION['city_arr']);
		
    $_REQUEST['bdate'] = date('Y-m-d');
	//$_REQUEST['city']  = 441;
	
	$list = stat_list();
	
    $smarty->assign('record_count', 		$list['record_count']);
    $smarty->assign('page_count',   		$list['page_count']);
    $smarty->assign('filter',       		$list['filter']);	
	$smarty->assign('list',   		        $list['stat']);  
	$smarty->assign('ur_here','桂圆蛋糕统计');
	$smarty->display('cake_stat_g.html');
}
/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $list = stat_list();

    $smarty->assign('record_count', 		$list['record_count']);
    $smarty->assign('page_count',   		$list['page_count']);
    $smarty->assign('filter',       		$list['filter']);
	$smarty->assign('list',   		        $list['stat']); 
	 
    make_json_result($smarty->fetch('cake_stat_g.html'), '', array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}
function stat_list()
{
	$filter['bdate'] = empty($_REQUEST['bdate']) ? date('Y-m-d') : trim($_REQUEST['bdate']);
	$filter['city']  = intval($_REQUEST['city']);
	
	$filter['turn']  = intval($_REQUEST['turn']);
	$reset = intval($_REQUEST['reset']);

        $city_code=array_keys($_SESSION['city_arr']);	
	    $str=implode(',',$city_code);	
	    $where = " where o.order_status = 1 and o.country in (".$str.")  ";
		/*$turn = $filter['turn'] ? "AND d.turn = '".$filter['turn']."' " : '';
		$join = $filter['turn'] ? "LEFT JOIN order_dispatch AS d ON o.order_id = d.order_id " : '';*/
		if($filter['turn'])
	   {
	   $where .= " and d.turn = ".$filter['turn'];
	   }
		if($filter['city'])
	   {
	    $where .= " and o.country = ".$filter['city'];
	   }
	   if($filter['bdate'])
	  {
	   $where .= " and o.best_time > '".$filter['bdate']."' and o.best_time < '".$filter['bdate']." 23:01:01' ";
	  }
		/*$sql = "SELECT g.goods_id,g.goods_attr, sum(g.goods_number) as gsum,group_concat(o.order_id) as orders ".
			   "FROM order_genid AS a ".
				"LEFT JOIN ecs_order_info AS o ON a.order_id = o.order_id ".
				"LEFT JOIN ecs_order_goods AS g ON o.order_id = g.order_id ".
				$join.
				$where.
				"AND o.best_time > '".$filter['bdate']."' ".
				"AND o.best_time < '".$filter['bdate']." 23:30:00' ".
				$turn.
				"AND g.goods_price >40 and g.goods_id=75 group by g.goods_id,g.goods_attr ";*/
		$sql = "SELECT sta.station_name,g.goods_id,sum(g.goods_number) as gsum,group_concat(o.order_id) as orders ".
			   "FROM order_genid AS a ".
				"LEFT JOIN ecs_order_info AS o ON a.order_id = o.order_id ".
				"LEFT JOIN ecs_order_goods AS g ON o.order_id = g.order_id ".				
				"LEFT JOIN order_dispatch AS d ON o.order_id = d.order_id  ".
				"LEFT JOIN ship_route AS s ON s.route_id = d.route_id ".
				"LEFT JOIN ship_station AS sta ON sta.station_id = s.station_id ".				
				$where.
				"AND g.goods_price >40 and g.goods_id=75 group by sta.station_id ";
       //echo $sql;
		$goods = $GLOBALS['db_read']->getAll($sql);

		foreach($goods as $key => $val)
		{
		   $stat['ch'][$key]['station_name']    = $val['station_name'];	
		   //$stat['ch'][$key]['goods_id']   = $val['goods_id'];
		   $stat['ch'][$key]['goods_name'] = goods_name($val['goods_id']);
		   $stat['ch'][$key]['bdate']      = $filter['bdate'];
		   $attr = empty($val['goods_attr']) ? 0.25 : floatval($val['goods_attr']);
           //$stat['ch'][$key]['goods_attr'] = $attr;
		   $stat['ch'][$key]['goods_sum']  = $val['gsum'];
		   $stat['ch'][$key]['orders']     = $val['orders'];

		   $stat['totalw'] += $attr * $val['gsum'];
		   $stat['totalc'] += $val['gsum'];
		   

	    }	
	

    $arr = array('stat' => $stat, 'filter' => $filter, 'page_count' => 1, 'record_count' => 1);
    return $arr;
}

function goods_name($gid)
{
   return $GLOBALS['db_read']->getOne("select concat(goods_sn,'--',goods_name_style) from ecs_goods where goods_id = ".$gid);
}

?>