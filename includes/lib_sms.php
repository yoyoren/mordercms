<?php
/**
*���ŷ��͹���
*/

$smsuser = "21cake";
$smspawd = "123456";
/*ȷ�϶�������*/
function sms_order_confirm($order_id)
{
   
}

/*���ŷ��͵��ýӿ�*/
function sms($mobile,$content)
{
	$user = $smsuser;
	$pd   = $smspawd;
	file_get_contents('http://218.241.67.234:9000/QxtSms/QxtFirewall?OperID='.$user.'&OperPass='.$pd.'&SendTime=&ValidTime=&AppendID=1234&DesMobile='.$mobile.'&Content='.$content.'&ContentType=15');
}

?>