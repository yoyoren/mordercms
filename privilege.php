<?php

/**
 * 管理员信息以及权限管理程序
 * ============================================================================
 * $Author: bisc $
 * $Id: privilege.php 0014
*/

require(dirname(__FILE__) . '/includes/init.php');

$_REQUEST['act'] = empty($_REQUEST['act']) ? 'list' : trim($_REQUEST['act']);
/* 初始化 $exc 对象 */
$exc = new exchange('order_admin', $db_read, 'id', 'user_name');

if ($_REQUEST['act'] == 'logout')
{
    /* 清除cookie */
    setcookie('LOS[admin_id]',   '', 1);
    setcookie('LOS[admin_pass]', '', 1);

    session_destroy();

    $_REQUEST['act'] = 'login';
}
if ($_REQUEST['act'] == 'login')
{
    $smarty->display('login.htm');
}
elseif ($_REQUEST['act'] == 'signin')
{
	
    $_POST['username'] = isset($_POST['username']) ? trim($_POST['username']) : '';
    $_POST['password'] = isset($_POST['password']) ? trim($_POST['password']) : '';

    $sql = "SELECT * FROM order_admin " .
            " WHERE user_name = '" . $_POST['username']. 
			"' AND password = '" . md5($_POST['password']) . 
			"' and flag >0 ";
    $row = $db_read->getRow($sql);

    if ($row)
    {
        $_SESSION['admin_id']    	= $row['id'];
        $_SESSION['admin_name']  	= $row['sname'];
        $_SESSION['admin_city']		= $row['city_group'];
        $_SESSION['station'] 	    = $row['remark'];
        $_SESSION['flag'] 	        = $row['flag'];
        $_SESSION['city_group'] 	= $row['city_group']==998 ? 441 : $row['city_group'];
        $_SESSION['action_list']    = $row['actions'];

        $sql = "select * from order_city where city_group='".$_SESSION['city_group']."'";
        $order_city = $db_read->getAll($sql);
        $city_arr = array();
		foreach($order_city as $k=>$v){

			$city_arr[$v['city_code']] = $v['city_name'];
		}
		$_SESSION['city_arr'] = $city_arr;

		$sql = "update order_admin set last_time = ".time().",last_ip = '".real_ip()."' where id = ".$row['id'];
		$db_write->query($sql);
        if (isset($_POST['remember']))
        {
            $time = gmtime() + 3600 * 24 * 7;
            setcookie('LOS[admin_id]',   $row['id'],       $time);
            setcookie('LOS[admin_pass]', md5($row['password']), $time);
        }
        los_header("Location:index.php\n");     
        exit;
    }
    else
    {
    	$link[0]=array('text'=>'重新登录','link'=>'/');
        sys_msg('登录失败!', 1,$link);
    }
}
elseif ($_REQUEST['act'] == 'list')
{
    /* 模板赋值 */
    $smarty->assign('ur_here',     "管理员列表");
    $smarty->assign('action_link', array('href'=>'privilege.php?act=add', 'text' => "添加管理员"));
    $smarty->assign('full_page',   1);
    $smarty->assign('admin_list',  get_admin_userlist());
    //print_r(get_admin_userlist());
    /* 显示页面 */
    assign_query_info();

    $smarty->display('privilege_list.htm');
}

/*------------------------------------------------------ */
//-- 查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $smarty->assign('admin_list',  get_admin_userlist());

    make_json_result($smarty->fetch('privilege_list.htm'));
}

/*------------------------------------------------------ */
//-- 添加管理员页面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
    
    /* 检查权限 */
    $sql = "select city_name,city_code from order_city where city_code=city_group ";
	$city = $db_read->getAll($sql);


     /* 模板赋值 */
	$smarty->assign('city',$city);
    $smarty->assign('ur_here',     '添加管理员');
    $smarty->assign('action_link', array('href'=>'privilege.php?act=list', 'text' => '管理员列表'));
    $smarty->assign('form_act',    'insert');
    $smarty->assign('action',      'add');
    $smarty->assign('select_role',  get_role_list());

    /* 显示页面 */
    //assign_query_info();
    $smarty->display('privilege_info.htm');

}
/*------------------------------------------------------ */
//-- 获取站点
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'getstation')
{
    $city_code = intval($_REQUEST['city_code']);
    if($city_code){
    	$sql = "select city_code from order_city where city_group = $city_code";
	    $city_code_arr = $db_read->getAll($sql);
	   
	    $city_code_str = '';
	    foreach($city_code_arr as $v){
	    	$city_code_str .= empty($city_code_str) ? $v['city_code'] : ','.$v['city_code'];
	    }
	    
	    $sql = "select * from ship_station where city_code in ($city_code_str)";
	    $stations = $db_read -> getAll($sql);
    }
    echo json_encode($stations);
}
/*------------------------------------------------------ */
//-- 添加管理员的处理
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert')
{
    //admin_priv('admin_manage');

    /* 判断管理员是否已经存在 */
    if (!empty($_POST['user_name']))
    {
        
        $sql = "select id from order_admin where user_name='".stripslashes($_POST['user_name'])."'";
        $re = $db_read -> getAll($sql);

        if ($re)
        {
            sys_msg(sprintf('用户名已存在', stripslashes($_POST['user_name'])), 1);
        }
    }

    /* 获取添加日期及密码 */
    $add_time = gmtime();
    $password  = md5($_POST['password']);
    $turename      = $_REQUEST['truename'];
    $city_group       = $_REQUEST['city'];
    $station = $_REQUEST['station'];
    
	if (!empty($_POST['select_role']))
    {
        $sql = "SELECT action_list FROM order_role WHERE role_id = '".$_POST['select_role']."'";
        $actions = $db_read->getOne($sql);

    }

    $sql = "INSERT INTO order_admin (user_name, password, remark,flag,last_time, actions,sname,city_group) ".
           "VALUES ('".trim($_POST['user_name'])."', '$password', '$station', '1','0','$actions','$turename',$city_group)";

    $db_write->query($sql);
   $url = 'privilege.php?act=list';

    los_header("Location: $url\n");

    /* 记录管理员操作 */
    admin_log($_POST['user_name'], 'add', 'privilege');
}
elseif ($_REQUEST['act'] == 'edit')
{
    $_REQUEST['id'] = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

    /* 获取管理员信息 */
    $sql = "SELECT * FROM order_admin WHERE id = '".$_REQUEST['id']."'";
    $user_info = $db_read->getRow($sql);
	
    $sql = "select city_name,city_code from order_city where city_code=city_group ";
	$city = $db_read->getAll($sql);
	//站点
	$sql = "select city_code from order_city where city_group = ".$user_info['city_group'];
	    $city_code_arr = $db_read->getAll($sql);
	   
	    $city_code_str = '';
	    foreach($city_code_arr as $v){
	    	$city_code_str .= empty($city_code_str) ? $v['city_code'] : ','.$v['city_code'];
	    }
	if($city_code_str){
		$sql = "select * from ship_station where city_code in ($city_code_str)";
		$station = $db_read->getAll($sql);
	}

    /* 模板赋值 */
    $smarty->assign('ur_here',     "编辑管理员");
    $smarty->assign('action_link', array('text' => "管理员列表", 'href'=>'privilege.php?act=list'));
    $smarty->assign('user',        $user_info);
    $smarty->assign('city',$city);
    $smarty->assign('station',$station);

    $smarty->assign('form_act',    'update_self');
    $smarty->assign('action',      'edit');

    assign_query_info();
    $smarty->display('privilege_info.htm');
}

/*------------------------------------------------------ */
//-- 更新管理员信息
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'update_self')
{
    $admin_id    = !empty($_REQUEST['id'])        ? intval($_REQUEST['id'])  : 0;
    $user_name = trim($_REQUEST['user_name']);
    $truename = trim($_REQUEST['truename']);
    $city_group = $_REQUEST['city'];
    $remark = $_REQUEST['station'];

    if (!empty($_POST['new_password']))
    {
        $sql = "SELECT password FROM order_admin WHERE id = '$admin_id'";
        $old_password = $db_read->getOne($sql);
        if ($old_password <> (md5($_POST['old_password'])) && $_SESSION['action_list'] != 'all')
        {
           die('原密码错误');
        }
        if ($_POST['new_password'] <> $_POST['pwd_confirm'])
        {
           die('2次输入的密码不一致');
        }
        else
        {
            $sql = "UPDATE order_admin SET password = '".md5($_POST['new_password'])."',user_name='$user_name',sname='$truename',city_group='$city_group',remark='$remark' WHERE id = '$admin_id'";
			$db_write->query($sql);
			//session_destroy();

            
        }
    }else{
    	$sql ="update order_admin set user_name='$user_name',sname='$truename',city_group='$city_group',remark='$remark' where id=$admin_id limit 1";
    	$re= $db_write->query($sql);
    	
    }
    los_header("Location:privilege.php?act=list");
}

/*------------------------------------------------------ */
//-- 编辑个人资料
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'modif')
{
    $sql = "SELECT * FROM order_admin WHERE id = '".$_SESSION['admin_id']."'";
    $user_info = $db_read->getRow($sql);

    $smarty->assign('ur_here',     '管理员设置');
    $smarty->assign('user',        $user_info);

    $smarty->assign('form_act',    'update_self');
    $smarty->assign('action',      'modif');

    $smarty->display('privilege_info.htm');
}

/*------------------------------------------------------ */
//-- 为管理员分配权限
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'allot')
{
    include_once(ROOT_PATH . 'includes/priv_action.php');
    include_once(ROOT_PATH . 'includes/lib_common.php');

    //admin_priv('allot_priv');
    if ($_SESSION['admin_id'] == $_GET['id'])
    {
        admin_priv('all');
    }

    /* 获得该管理员的权限 */
    $priv_str = $db_read->getOne("SELECT actions FROM order_admin WHERE id = '$_GET[id]'");

    /* 如果被编辑的管理员拥有了all这个权限，将不能编辑 */
    if ($priv_str == 'all')
    {
       $link[] = array('text' => $_LANG['back_admin_list'], 'href'=>'privilege.php?act=list');
       sys_msg('无权操作', 1);
    }

    /* 获取权限的分组数据 */
    $sql_query = "SELECT action_id, parent_id, action_code FROM order_admin_action WHERE parent_id = 0";
    $res = $db_read->query($sql_query);
    while ($rows = $db_read->FetchRow($res))
    {
        $priv_arr[$rows['action_id']] = $rows;
    }

    /* 按权限组查询底级的权限名称 */
    $sql = "SELECT action_id, parent_id, action_code FROM order_admin_action WHERE parent_id " .db_create_in(array_keys($priv_arr));
    $result = $db_read->query($sql);
    while ($priv = $db_read->FetchRow($result))
    {
        $priv_arr[$priv["parent_id"]]["priv"][$priv["action_code"]] = $priv;
    }

    // 将同一组的权限使用 "," 连接起来，供JS全选
    foreach ($priv_arr AS $action_id => $action_group)
    {
        $priv_arr[$action_id]['priv_list'] = join(',', @array_keys($action_group['priv']));

        foreach ($action_group['priv'] AS $key => $val)
        {
            $priv_arr[$action_id]['priv'][$key]['cando'] = (strpos($priv_str, $val['action_code']) !== false || $priv_str == 'all') ? 1 : 0;
        }
    }

    /* 赋值 */
    $smarty->assign('lang',        $_LANG);
    $smarty->assign('ur_here',     $_LANG['allot_priv'] . ' [ '. $_GET['user'] . ' ] ');
    $smarty->assign('action_link', array('href'=>'privilege.php?act=list', 'text' => '管理员列表'));
    $smarty->assign('priv_arr',    $priv_arr);
    $smarty->assign('form_act',    'update_allot');
    $smarty->assign('user_id',     $_GET['id']);

    /* 显示页面 */
    assign_query_info();
    $smarty->display('privilege_allot.htm');
}

/*------------------------------------------------------ */
//-- 更新管理员的权限
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'update_allot')
{
    //admin_priv('admin_manage');

    /* 取得当前管理员用户名 */
    $admin_name = $db_read->getOne("SELECT user_name FROM order_admin WHERE id = '$_POST[id]'");

    /* 更新管理员的权限 */
    $act_list = @join(",", $_POST['action_code']);
    $sql = "UPDATE order_admin SET actions = '$act_list' ".
           "WHERE id = '$_POST[id]'";

    $db_write->query($sql);
    /* 动态更新管理员的SESSION */
    if ($_SESSION["admin_id"] == $_POST['id'])
    {
        $_SESSION["action_list"] = $act_list;
    }

    /* 记录管理员操作 */
    //admin_log(addslashes($admin_name), 'edit', 'privilege');

    /* 提示信息 */
    $link[] = array('text' => '返回列表', 'href'=>'privilege.php?act=list');
    sys_msg('编辑' . "&nbsp;" . $admin_name . "&nbsp;" . '操作成功', 0, $link);

}

/*------------------------------------------------------ */
//-- 删除一个管理员
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
	
    check_authz_json('admin_drop');

    $id = intval($_GET['id']);

    /* 获得管理员用户名 */
    $admin_name = $db_read->getOne("SELECT user_name FROM order_admin WHERE id='$id'");

    /* demo这个管理员不允许删除 */
    if ($admin_name == 'demo')
    {
       sys_msg('此管理员不能删除',1);
    }

    /* ID为1的不允许删除 */
    if ($id == 1)
    {
        sys_msg('此管理员不能删除',1);
    }

    /* 管理员不能删除自己 */
    if ($id == $_SESSION['admin_id'])
    {
        sys_msg('此管理员不能删除',1);
    }



    $sql = "update order_admin set flag = 0 where id=$id limit 1";
    $db_write->query($sql);

    $url = 'privilege.php?act=list';

    los_header("Location: $url\n");
    exit;
}
/*------------------------------------------------------ */
//-- 管理员切换城市
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'changecity')
{
	$city_code = intval($_REQUEST['city_code']);
	$_SESSION['city_group'] 	= $city_code;

    $sql = "select * from order_city where city_group='".$_SESSION['city_group']."'";
    $order_city = $db_read->getAll($sql);
    $city_arr = array();
	foreach($order_city as $k=>$v){

		$city_arr[$v['city_code']] = $v['city_name'];
	}
	$_SESSION['city_arr'] = $city_arr;
	
}
/* 获取管理员列表 */
function get_admin_userlist()
{
    global $priv;
	$list = array();
    $sql  = 'SELECT * '.
            'FROM order_admin where flag=1 ORDER BY id DESC';
    $list = $GLOBALS['db_read']->getAll($sql);

    foreach ($list AS $key=>$val)
    {
    	$sql ="select city_name from order_city where city_code='".$val['city_group']."'";
    	$city = $GLOBALS['db_read']->getOne($sql);
        $list[$key]['city_group'] = $city;
        if($val['city_group'] == 998){
        	$list[$key]['city_group'] = '全局';
        }
    }

    return $list;
}

/* 获取角色列表 */
function get_role_list()
{
    $list = array();
    $sql  = 'SELECT role_id, role_name, action_list '.
            'FROM order_role order by role_id desc' ;
    $list = $GLOBALS['db_read']->getAll($sql);
    return $list;
}

?>
