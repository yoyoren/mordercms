<?php
function sendsms($mobile,$gmsg)
{
	$cont =$gmsg;
  	$ct = mb_convert_encoding($cont,'gb2312','utf-8');
	$mobiles=explode(",",$mobile);
	for($i=0;$i<count($mobiles);$i++){
		file_get_contents('http://221.179.180.158:9000/QxtSms/QxtFirewall?OperID=21cake&OperPass=123456&SendTime=&ValidTime=&DesMobile='.$mobiles[$i].'&Content='.$ct.'&ContentType=15');
	}
   	
   	//file_get_contents('http://221.179.180.158:9000/QxtSms/QxtFirewall?OperID=21cake&OperPass=123456&SendTime=&ValidTime=&DesMobile='.$mobile.'&Content='.$ct.'&ContentType=15');
}