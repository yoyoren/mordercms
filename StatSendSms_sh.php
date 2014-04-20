<?php
include_once 'conn.php';
include_once 'init.php';
include_once 'sendsms.php';
$send_time=date("H:i:s",time());
if(substr($send_time,0,4)=="19:2"){
	//$mobile="18600093721,13488724577,18600093908";
	$mobile="18600093721,18600093718,18600092121,18600093766,18600093910,18600093908";
	//$mobile="18600093721";
	$city="442,上海";
	$city_array=explode("|", $city);
	$msg=array($city_array[0]);
	$send_time=date("Y-m-d",time());
	
	$sql_main="select sum(g.goods_number) from ecs_order_info as o inner join ecs_order_goods as g on g.order_id=o.order_id where o.order_status<2 and g.goods_price>100";
	$sql_time=" and o.best_time like '".$send_time."%'";
	for($i=0;$i<count($city_array);$i++){
		$temp_arr=explode(",", $city_array[$i]);
		$msg[$i]="姚总:".$temp_arr[1]."今天";
		$sql=$sql_main." and o.country=".$temp_arr[0].$sql_time;
		//echo $sql;
		$res=getOne($sql);
		if($res>0){
			$msg[$i].="蛋糕共".$res."个,其中";
			$temp_count=$res;
		}
		//积分兑换
		$sql2=$sql_main." and o.country=".$temp_arr[0]." and (o.pay_name like '%K金%' or o.pay_note like '%K金%')".$sql_time;
		$res2=getOne($sql2);
		if($res2>0){
			$msg[$i].="兑换".$res2.",";
			$temp_count-=$res2;
		}
		//代金卡
		$sql3=$sql_main." and o.country=".$temp_arr[0]." and o.bonus>'168'".$sql_time;
		$res3=getOne($sql3);
		if ($res3>0){
			$msg[$i].="代金卡".$res3.",";
			$temp_count-=$res3;
		}
		//销售活动
		$sql4=$sql_main." and o.country=".$temp_arr[0]." and o.pay_note='销售活动'".$sql_time;
		$res4=getOne($sql4);
		if ($res4>0){
			$msg[$i].="促销".$res4.",";
			$temp_count-=$res4;
		}
		//免费赠送
		$sql5=$sql_main." and o.country=".$temp_arr[0]." and o.pay_note='免费赠送'".$sql_time;
		$res5=getOne($sql5);
		if ($res5>0){
			$msg[$i].="免费".$res5.",";
			$temp_count-=$res5;
		}
		//正常付费
		$msg[$i].="正常".$temp_count."。";
		//冰激凌数
		$sql6=$sql_main." and o.country=".$temp_arr[0]." and g.goods_sn=29".$sql_time;
		$res6=getOne($sql6);
		if ($res6>0){
			$msg[$i].="冰淇淋".$res6."。";
		}
		//网上总数
		$sql7=$sql_main." and o.country=".$temp_arr[0]." and left(o.order_sn,2)='sh'".$sql_time;
		$res7=getOne($sql7);
		if ($res7>0){
			$msg[$i].="今天网上".$res7."个。";
		}
		//明天蛋糕总数
		$send_time1=date("Y-m-d",strtotime("tomorrow"));
		$sql_time1=" and o.best_time like '".$send_time1."%'";
		$sql8=$sql_main." and o.country=".$temp_arr[0].$sql_time1;
		$res8=getOne($sql8);
		if($res8>0){
			$msg[$i].="明日".$res8."个。";
			$temp_count=$res8;
		}
		//后缀加上时间落款
		//$msg[$i].=date("Y-m-d H:i:s",time()+8*3600);
		//插入sendsms
		$sql9="insert into sendsms (content,mobile,class,sendtime) values ('".$msg[$i]."','".$mobile."','每日订单','".date("Y-m-d H:i:s",time()+8*3600)."')";
		mysql_query($sql9);
		//发送短信
		sendsms($mobile, $msg[$i]);
	}
}else{
	echo date("Y-m-d H:i:s",time())."<br/>还没到发送时间";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="refresh" content="600">
<title>上海蛋糕总数短信</title>
<script language="JavaScript"> 
function myrefresh() 
{ 
window.location.reload(); 
} 
setTimeout('myrefresh()',120000); //指定1秒刷新一次 
</script> 
</head>
<body>

</body>
</html>
