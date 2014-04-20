<?php 
	session_start();
	$filename=$_REQUEST['date'];
	$filename.="报表";
	$filename = iconv("UTF-8","GB2312",$filename);
	//echo $filename;
	header("Content-type: application/vnd.ms-excel; charset=UTF-8");
	header("Content-Disposition: attachment; filename=$filename.xls");
	$data="";
	$_SESSION['ttop']=array("流水号","订单编号","数量","折后金额","附件费","配送费","订单总额","现金","POS","支付宝","快钱","礼金卡","现金券","月结","免费支付");
	foreach($_SESSION['ttop'] as $value)
	{
		$data.=$value."\t";
	}
	$data.="\n";
	foreach($_SESSION['tcontent'] as $key=>$value)
	{
		if($value['p_sn']==NULL)
		{
			$data.="未打印\t";
		}
		else
		{
			$data.=$value['p_sn']."\t";
		}
		$data.=$value['order_sn']."\t";
		$data.=$value['goods_numbers']."\t";
		$data.=$value['goods_amount']."\t";
		$data.=$value['pack_fee']."\t";
		$data.=$value['peisongfei']."\t";
		$data.=$value['totalprice']."\t";
		$data.=$value['cash']."\t";
		$data.=$value['pos']."\t";
		$data.=$value['zhifubao']."\t";
		$data.=$value['kuaiqian']."\t";
		$data.=$value['surplus']."\t";
		$data.=$value['bonus']."\t";
		$data.=$value['yuejie']."\t";
		$data.=$value['free']."\t\n";
	}
	$encode = mb_detect_encoding($data, array("ASCII","UTF-8","GB2312","GBK","BIG5")); 
	if ($encode =="UTF-8")
	{ 
		$data =mb_convert_encoding($data,"GBK","UTF-8");
	}
	if ($encode =="GBK")
	{ 
		$data = mb_convert_encoding($data,"GBK","GB2312");
	}
	echo $data. "\t";
	?>