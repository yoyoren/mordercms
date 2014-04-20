<?php
session_start();
$_SESSION['admin_id'] = '2';
@require(dirname(__FILE__).'/includes/init.php');

/*
	 * http://cms.21cake.com/cms_x/data_sms.php?act=acceptSms&SmsType=0&SrcMobile=15810708705&AppendID=95321
	 * &Content=%AA%B5%BD8%3A00%B2%C5%D3%D0%CA%B1%BC%E4
	 * &RecvTime=20121211175556&SendTime=20121211175556
	 * */
$phone = trim($_REQUEST['SrcMobile']);
$content = iconv('gb2312', 'utf-8', trim($_REQUEST['Content']));

if($content == 'A' || $content == 'a'){
	$sql = "update send_sms set status=2 where phone='$phone' limit 1";
	$re = $db_write->query($sql);
	$sql = "select bonus_cardnum,bonus_sn from send_sms where phone=$phone limit 1";
	$result = $db_read->getRow($sql);
	$cont = "您的卡号：".$result['bonus_sn']."，密码：".$result['bonus_sn'];
	sendsms($phone,$cont);
}
/**
 * 发送短信
 * Enter description here ...
 * @param $mobile int
 * @param $gmsg string
 */
function sendsms($mobile,$gmsg)
{
  	$ct = mb_convert_encoding($gmsg,'gb2312','utf-8');
   	
   	return file_get_contents('http://221.179.180.158:9000/QxtSms/QxtFirewall?OperID=21cake&OperPass=123456&SendTime=&ValidTime=&DesMobile='.$mobile.'&Content='.$ct.'&ContentType=8');
}
