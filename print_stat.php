<?php
/**
 * pei song da yin 
 * $Author: bisc $
 * $Id: order_print.php 
*/

//require(dirname(__FILE__) . '/includes/init2.php');
require(dirname(__FILE__) . '/includes/init.php');
$_REQUEST['act'] = empty($_REQUEST['act']) ? 'list' : trim($_REQUEST['act']);
if ($_REQUEST['act'] == 'list')
{
  
	$list = stat_list();
   // print_r($list);
    $smarty->assign('ur_here','打印结果查询');
    $smarty->assign('action_link',array('href'=>'order_print.php','text'=>'配送打印'));
    $smarty->assign('orders',      $list); 
    $smarty->assign('bdate',$_REQUEST['bdate']);
    $smarty->assign('status',$_REQUEST['status']);
	$smarty->assign('count',      count($list));
    $smarty->display('print_stat_list.html');
}

function stat_list()
{
    $filter['bdate']  = empty($_REQUEST['bdate']) ? date('Y-m-d') :trim($_REQUEST['bdate']);
$st = intval($_REQUEST['status']);
		
	$page = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);
	$size = 30;	
		
$aa = $st == 9 ? '' : " and o.order_status='$st' ";	
	$group=$_SESSION['city_group'];
	$sql = "select o.order_sn,o.order_status,b.* from  print_log_x  as b left join ecs_order_info as o on o.order_id=b.order_id ".
		   "where city_group = '$group' and bdate = '".$filter['bdate']."' ".$aa; 
		   $rs = $GLOBALS['db_read']->getAll($sql);
		  
       foreach($rs as $key=>$val){
	
			if($val['admin_id']){
				
				$sql = "select sname from order_admin where id=".$val['admin_id'];
				$rs[$key]['sname'] = $GLOBALS['db_read']->getOne($sql);
			}
			if($val['admin_id2']){
				
				$sql = "select sname from order_admin where id=".$val['admin_id2'];
				$rs[$key]['sname2'] = $GLOBALS['db_read']->getOne($sql);
			}
		
    }  
       
		  
    return $rs;
}

?>