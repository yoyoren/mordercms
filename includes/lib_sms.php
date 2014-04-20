<?php
/**
*短信发送管理
*/

$smsuser = "21cake";
$smspawd = "123456";
/*确认订单短信*/
function sms_order_confirm($order_id)
{
   
}

/*短信发送调用接口*/
function sms($mobile,$content)
{
	$user = $smsuser;
	$pd   = $smspawd;
	file_get_contents('http://218.241.67.234:9000/QxtSms/QxtFirewall?OperID='.$user.'&OperPass='.$pd.'&SendTime=&ValidTime=&AppendID=1234&DesMobile='.$mobile.'&Content='.$content.'&ContentType=15');
}

?>