<?php
/**
 * 财务审核订单
 * $Author: bisc $
 * $Id: finan_check.php 
*/

require(dirname(__FILE__) . '/includes/init.php');

$_REQUEST['act'] = empty($_REQUEST['act']) ? 'list' : trim($_REQUEST['act']);

if ($_REQUEST['act'] == 'list')
{
    $smarty->assign('ur_here',     '每日收入核算报表');
    $smarty->assign('full_page',   1);
	$_REQUEST['sdate'] = empty($_REQUEST['sdate'])? date('Y-m-d',time()-3600*24):$_REQUEST['sdate'];
	$list = order_list();
	$order_list=$list['item'];
	foreach($order_list as $key=>$val)
	{
		$order_list[$key]['xuhao']=$key+1;
		$order_list[$key]['key']=$key%2;
		$order_list[$key]['cash']=number_format(0, 2, '.', '');
		$order_list[$key]['pos']=number_format(0, 2, '.', '');
		$order_list[$key]['zhifubao']=number_format(0, 2, '.', '');
		$order_list[$key]['kuaiqian']=number_format(0, 2, '.', '');
		$order_list[$key]['free']=number_format(0, 2, '.', '');
		$order_list[$key]['yuejie']=number_format(0, 2, '.', '');
	}
	$total=array("goods_numbers"=>0,"goods_amount"=>0,"pack_fee"=>0,"peisongfei"=>0,"totalprice"=>0,"cash"=>0,"pos"=>0,"zhifubao"=>0,"kuaiqian"=>0,"surplus"=>0,"bonus"=>0,"yuejie"=>0,"free"=>0);
	foreach($order_list as $key=>$val)
	{
		$order_list[$key]["peisongfei"] = number_format($val["shipping_fee"] + $val["pay_fee"], 2, '.', '');
		$total['peisongfei']+=$order_list[$key]["peisongfei"];
		$total['pack_fee']+=$order_list[$key]["pack_fee"];
		$order_list[$key]["totalprice"] = number_format($val["goods_amount"] + $val["pack_fee"] + $order_list[$key]["peisongfei"], 2, '.', '');
		$total['totalprice']+=$order_list[$key]["totalprice"];
		$total['goods_amount']+=$order_list[$key]["goods_amount"];
		$id = $val["payid"];
		$arr = explode(",",$id);
		$goods_number=explode(",",$order_list[$key]['goods_numbers']);
		$goods_numbers=0;
		foreach($goods_number as $val)
		{
			$goods_numbers+=$val;
		}
		$order_list[$key]['goods_numbers']=$goods_numbers;
		$total['goods_numbers']+=$order_list[$key]['goods_numbers'];
		$order_list[$key]['isorno']=0;
		foreach($arr as $v)
		{
			if(in_array(1,$arr) && in_array(2,$arr))
			{
				$amounts=explode(",",$order_list[$key]['amounts']);
				if($v==1)
				{
					$order_list[$key]['cash']=$amounts[0];		
					$total['cash']+=$order_list[$key]['cash'];
				}
				if($v==2)
				{
					$order_list[$key]['pos']=$amounts[1];
					$total['pos']+=$order_list[$key]['pos'];
				}	
			}
			if(in_array(1,$arr)&& !in_array(2,$arr)&&$v==1)
			{
				$row = $GLOBALS['db_read']->getOne("select count(*) from tender_info where order_id=".$order_list[$key]['order_id']." and pay_name like '%现金%'");
				if($row)
				{
					$order_list[$key]['cash']=$order_list[$key]['order_amount'];
					$total['cash']+=$order_list[$key]['cash'];
				}
				else
				{
					$order_list[$key]['pos']=$order_list[$key]['order_amount'];
					$total['pos']+=$order_list[$key]['pos'];
				}
			}
			if(!in_array(1,$arr)&&in_array(2,$arr) && $v==2)
			{
				$order_list[$key]['pos']=$order_list[$key]['order_amount'];
				$total['pos']+=$order_list[$key]['pos'];
			}
			if(in_array(5,$arr) && $v==5)
			{
				$order_list[$key]['zhifubao']=$order_list[$key]['money_paid'];
				$total['zhifubao']+=$order_list[$key]['zhifubao'];
				if($order_list[$key]['money_paid']==0)	
				{
					$order_list[$key]['isornoz']=1;
					$order_list[$key]['zhifubao']="支付失败";
				}	
			}		
			if(in_array(7,$arr) && $v==7)
			{
				$order_list[$key]['kuaiqian']=$order_list[$key]['money_paid'];	
				$total['kuaiqian']+=$order_list[$key]['kuaiqian'];
				if($order_list[$key]['money_paid']==0)	
				{
					$order_list[$key]['isornok']=1;
					$order_list[$key]['kuaiqian']="支付失败";
				}			
			}
			if(in_array(10,$arr) && $v==10 || in_array(14,$arr) && $v==14 )
			{
				$order_list[$key]['surplus']=$order_list[$key]['surplus'];	
				$total['surplus']+=$order_list[$key]['surplus'];		
			}
			if(in_array(9,$arr) && $v==9)
			{
				$order_list[$key]['bonus']=$order_list[$key]['bonus'];	
				$total['bonus']+=$order_list[$key]['bonus'];		
			}
			if(in_array(8,$arr) && $v==8)
			{
				$order_list[$key]['free']=$order_list[$key]["totalprice"];	
				$total['free']+=$order_list[$key]['free'];		
			}
			if(in_array(11,$arr)&&$v==11)
			{
				$order_list[$key]['yuejie']=$order_list[$key]['order_amount'];	
				$total['yuejie']+=$order_list[$key]['yuejie'];		
			}
		}
	}
	foreach($total as $key=>$val)
	{
		if($key!="goods_numbers")
		{
			$total[$key]=number_format($val, 2, '.', '');
		}
	}
	$_SESSION['tcontent']=$order_list;
    $smarty->assign('record_count', 		$list['record_count']);
	$smarty->assign('total', 		$total);
    $smarty->assign('page_count',   		$list['page_count']);
    $smarty->assign('filter',       		$list['filter']);	
	$smarty->assign('order_list',   		$order_list);    
	$smarty->display('finan_each_check.html');
}
function order_list()
{
	//查询条件
	$filter['sdate']=$_REQUEST['sdate'];
	$where=" AND best_time LIKE '".$filter['sdate']."%'";
	$sql="select count(distinct a.order_id)
		from ecs_order_info as a
		left join tender_info as b on a.order_id = b.order_id
		LEFT JOIN ecs_order_goods AS c ON b.order_id = c.order_id WHERE a.order_status =1 
				AND c.goods_price >=45 ".$where;
	$filter['record_count'] = $GLOBALS['db_read']->getOne($sql);
	/* 分页大小 */
    $filter = page_and_size($filter);
	$sql1= "select a.order_id,a.order_sn,(select print_sn from print_log_x as d where d.order_id = a.order_id) as p_sn,a.goods_amount, (select  group_concat(cast(c.goods_number as char)) from ecs_order_goods as c where c.order_id=a.order_id AND c.goods_price >=45) as goods_numbers,a.order_amount,a.money_paid,a.surplus,a.bonus,a.shipping_fee,a.pay_fee,a.pack_fee,group_concat(cast(b.pay_id as char)) as payid,group_concat(cast(b.amount as char)) as amounts from ecs_order_info as a
		left join tender_info as b on a.order_id = b.order_id where a.order_status =1  ".$where."  group by a.order_id ORDER BY best_time ";
	$row = $GLOBALS['db_read']->getAll($sql1);
	$arr = array('item' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    return $arr;
}

?>
