<?php
require(dirname(__FILE__).'/includes/init.php');
admin_priv('da_sms');
$act = empty($_REQUEST['act']) ? 'addSms' : $_REQUEST['act'];

if($act == 'addSms')
{
	$smarty->assign('content',$content);
	$smarty->assign('ur_here','发送短信');
	$smarty->assign('action_link',array('href'=>'?act=list','text'=>'短信发送记录'));
	$smarty->display('data_sms.html');
}
elseif($act == 'sendSms')
{
	$filename = $_FILES['upfile']['tmp_name'];
	$content = trim($_REQUEST['content']);
	if($filename){
		require_once './includes/Classes/PHPExcel.php';
		
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		
		
		$objPHPExcel = $objReader->load($filename); //$filename可以是上传的文件，或者是指定的文件
		$sheet = $objPHPExcel->getSheet(0);
		$highestRow = $sheet->getHighestRow(); // 取得总行数
		$highestColumn = $sheet->getHighestColumn(); // 取得总列数
		$k = 0;

		//循环读取excel文件,读取一条,插入一条
		for($j=1;$j<=$highestRow;$j++)
		{

		
			$a[] = $objPHPExcel->getActiveSheet()->getCell("A".$j)->getValue();//获取A列的值
			
			$b[] = $objPHPExcel->getActiveSheet()->getCell("B".$j)->getValue();//获取B列的值
			
		
		}

		$sql = "select bonus_id,bonus_cardnum,bonus_sn from ecs_user_bonus where bonus_id >=1 and bonus_id<50";
		$result = $db_read->getAll($sql);
		foreach($a as $k=>$v){
			$content = str_replace('[姓名]', $b[$k], $content);
			$content = str_replace('[卡号]', $result[$k]['bonus_cardnum'], $content);
			$content = str_ireplace('[密码]', $result[$k]['bonus_sn'], $content);

			sendsms($v,$content);
			$sql = "insert into send_sms (bonus_id,phone,name,bonus_cardnum,bonus_sn)values(".$result[$k]['bonus_id'].",$v,'".$b[$k]."','".$result[$k]['bonus_cardnum']."','".$result[$k]['bonus_sn']."')";
			$db_write->query($sql);
			
		}
		
	}else{
		
		$mobile = trim($_REQUEST['mobile']);
		$mobile_arr = explode(',',$mobile);
		foreach($mobile_arr as $v){
			sendsms($v,$content);
		}
		
	}
	
	$links[0]['text'] = '查看发送记录';
    $links[0]['href'] = 'data_sms.php?act=list';
	sys_msg('发送成功','',$links);
	
	
	
}
elseif($act == 'list')
{
	$list = getlist();

	$smarty->assign('list',$list['list']);
	$smarty->assign('page_count',$list['page_count']);
	$smarty->assign('record_count',$list['record_count']);
	$smarty->assign('filter',      $list['filter']);
	$smarty->assign('full_page',1);	
	$smarty->assign('ur_here','短信发送记录');
	$smarty->assign('action_link',array('href'=>'?act=addSms','text'=>'发送短信'));
	$smarty->display('data_sms_list.html');
}
elseif($act == 'query')
{
	$list = getlist();

    $smarty->assign('record_count', 		$list['record_count']);
    $smarty->assign('page_count',   		$list['page_count']);
	$smarty->assign('filter',				$list['filter']);
	$smarty->assign('list',   		$list['list']); 

	 
    make_json_result($smarty->fetch('data_sms_list.html'), '', array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}
elseif($act == 'acceptSms')
{
	
}
elseif($act == 'delete')
{
	$id = $_REQUEST['id'];
	$sql ="delete from send_sms where id=$id limit 1";
	$db_write->query($sql);
	$url = 'data_sms.php?act=query&'.str_replace('act=delete', '', $_SERVER['QUERY_STRING']);
	los_header("Location:$url\n");
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

function getlist(){
	$filter['page'] = $page = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <=0) ? 1 : intval($_REQUEST['page']);
	$filter['name'] = empty($_REQUEST['name']) ? '' : trim($_REQUEST['name']);
	$filter['phone'] = empty($_REQUEST['phone']) ? '' : trim($_REQUEST['phone']);
	$filter['bonus_id'] = empty($_REQUEST['bonus_id']) ? '' : intval($_REQUEST['bonus_id']);
	$where = " where 1";
	if($filter['name']){
		$where .= " and name='".$filter['name']."'";
	}
	if($filter['phone']) $where .= " and phone='".$filter['phone']."'";
	if($filter['bonus_id']) $where .= " and bonus_id='".$filter['bonus_id']."'";
	
	$sql = "select count(*) from send_sms".$where;
	$record_count = $GLOBALS['db_read']->getOne($sql);
	$pageSize = 20;
	$page_count = ceil($record_count/$pageSize);
	$sql = "select * from send_sms $where order by id desc limit ".($page-1)*$pageSize.",$pageSize ";
	$result = $GLOBALS['db_read']->getAll($sql);
	
	return array('list'=>$result,'page_count'=>$page_count,'record_count'=>$record_count,'filter'=>$filter);
}
