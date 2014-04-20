<?php

require(dirname(__FILE__) . '/includes/init.php');
if ($_REQUEST['act'] == '')
{
    $smarty->display('index.html');
}
elseif ($_REQUEST['act'] == 'top')

{
	$date = date('Y-m-d');
	$smarty->assign('date',$date);
	$week =array('日','一','二','三','四','五','六');
	foreach($week as $k=>$v){
		if(date('w') == $k){
			$smarty->assign('week', $v);
		}
	}
	if($_SESSION['admin_city'] == 998){
		$sql = "select city_name,city_code from order_city group by city_group";
		$city = $db_read->getAll($sql);
	}else{
		$sql = "select city_name,city_code from order_city where city_code='".$_SESSION['city_group']."'";
		$city = $db_read->getAll($sql);
	}

	$smarty->assign('city',$city);
	$smarty->assign('city_group',$_SESSION['city_group']);
	$smarty->assign('admin_name', $_SESSION['admin_name']);
	$smarty->display('top.htm');
}
elseif ($_REQUEST['act'] == 'menu')
{
	include(dirname(__FILE__)."/includes/inc_menu.php");
	// 权限对照表
	include(dirname(__FILE__).'/includes/inc_priv.php');
	include(dirname(__FILE__).'/includes/common.php');
	//$modules['07_print']['31_pri_check']='change_order.php?step=check';
	 
	//print_r($modules);
	foreach ($modules AS $key => $value)
	    {
	        ksort($modules[$key]);
	    }
	    ksort($modules);

	    foreach ($modules AS $key => $val)
	    {
	        $menus[$key]['label'] = $_LANG[$key];

	        if (is_array($val))
	        {
	            foreach ($val AS $k => $v)
	            {
	                if ( isset($purview[$k]))
	                {
	                    if (is_array($purview[$k]))
	                    {
	                        $boole = false;
	                        foreach ($purview[$k] as $action)
	                        {
	                             $boole = $boole || admin_priv($action, '', false);
	                        }
	                        if (!$boole)
	                        {
	                            continue;
	                        }
	
	                    }
	                    else
	                    {
	                        if (! admin_priv($purview[$k], '', false))
	                        {
	                            continue;
	                        }
	                    }
	                }
	                
	                if ($k == 'ucenter_setup' && $_CFG['integrate_code'] != 'ucenter')
	                {
	                    continue;
	                }
	
	                $menus[$key]['children'][$k]['label']  = $_LANG[$k];
	                $menus[$key]['children'][$k]['action'] = $v;
	            }
	        }
	        else
	        {
	            $menus[$key]['action'] = $val;
	        }
	
	        // 如果children的子元素长度为0则删除该组
	        if(empty($menus[$key]['children']))
	        {
	            unset($menus[$key]);
	        }
	
	    }
	//$menus['07_print']['children']['31_pri_check']['label']  = '改单查询';
	//$menus['07_print']['children']['31_pri_check']['action'] = 'change_order.php?step=check';
	//print_r($menus);
	$smarty->assign('menus',     $menus);
    $smarty->display('menu.htm');
}
elseif ($_REQUEST['act'] == 'clear_cache')
{
    clear_all_files();

    sys_msgn('done!');
}
elseif ($_REQUEST['act'] == 'main')
{
	/*if($_SESSION['flag'] ==1)
	{
        los_header("Location:shipping_dispatch.php\n");     	   
	}
	elseif($_SESSION['flag'] ==2)
	{
        los_header("Location:shipping_delivery.php\n");     	
	}
	exit;*/
}

elseif ($_REQUEST['act'] == 'drag')
{
    $smarty->display('drag.htm');
}
elseif($_REQUEST['act'] == 'editpassword')
{
	/* 获取管理员信息 */
    $sql = "SELECT * FROM order_admin WHERE id = '".$_SESSION['admin_id']."'";
    $user_info = $db_read->getRow($sql);
	$smarty->assign('ur_here','个人中心');
	$smarty->assign('user',   $user_info);
	$smarty->display("editpassword.html");
	
}elseif($_REQUEST['act'] == 'saveedit'){

	$admin_id = intval($_SESSION['admin_id']);
	$pw = md5($_REQUEST['old_password']);
	$new_password =$_REQUEST['new_password'];
	$sql = 'select password from order_admin where id='.$admin_id;
	$old_password = $db_read->getOne($sql);
	if($old_password == $pw){
		$sql = "update order_admin set password='".md5($new_password)."' where id=$admin_id";
		$re = $db_write->query($sql);
		if($re){
			$links[0]['text'] = '返回上一页！';
        	$links[0]['href'] = 'javascript:history.go(-1)';
			sys_msg('密码修改成功', $links);
			
		}
	}else{
		sys_msg('旧密码错误', 1);
	}
}

?>
