<?php
require(dirname(__FILE__) . '/includes/init.php');
require(dirname(__FILE__) . '/includes/common.php');
admin_priv('pr_role');
$_REQUEST['act'] = empty($_REQUEST['act']) ? 'list' : $_REQUEST['act'];
/* 初始化 $exc 对象 */
$exc = new exchange("order_role", $db, 'role_id', 'role_name');
/*------------------------------------------------------ */
//-- 角色列表页面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
	//admin_priv('role');
    /* 模板赋值 */
    $smarty->assign('ur_here',     	'角色管理');
    $smarty->assign('action_link', array('href'=>'role.php?act=add', 'text' => '添加角色'));
    $smarty->assign('full_page',   1);
    $smarty->assign('admin_list',  get_role_list());

    /* 显示页面 */
    //assign_query_info();
    $smarty->display('role_list.html');
}

/*------------------------------------------------------ */
//-- 查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
	//admin_priv('role');
    $smarty->assign('admin_list',  get_role_list());

    make_json_result($smarty->fetch('role_list.html'));
}

/*------------------------------------------------------ */
//-- 添加角色页面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
	//admin_priv('role');
    /* 检查权限 */
    //admin_priv('admin_manage');
    include_once(dirname(__FILE__). '/includes/priv_action.php');
	include(dirname(__FILE__).'/includes/lib_common.php');
    $priv_str = '';

    /* 获取权限的分组数据 */
    $sql_query = "SELECT action_id, parent_id, action_code FROM " .'order_admin_action'.
                 " WHERE parent_id = 0";
    $res = $db_read->query($sql_query);
    while ($rows = $db_read->FetchRow($res))
    {
        $priv_arr[$rows['action_id']] = $rows;
    }


    /* 按权限组查询底级的权限名称 */
    $sql = "SELECT action_id, parent_id, action_code FROM " .'order_admin_action'.
           " WHERE parent_id " .db_create_in(array_keys($priv_arr));
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

     /* 模板赋值 */
    $smarty->assign('ur_here',     '添加角色');
    $smarty->assign('action_link', array('href'=>'role.php?act=list', 'text' => '管理员列表'));
    $smarty->assign('form_act',    'insert');
    $smarty->assign('action',      'add');
    $smarty->assign('lang',        $_LANG);
    $smarty->assign('priv_arr',    $priv_arr);

    /* 显示页面 */
    //assign_query_info();
    $smarty->display('role_info.htm');




}

/*------------------------------------------------------ */
//-- 添加角色的处理
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert')
{
	//admin_priv('role');
    $act_list = @join(",", $_POST['action_code']);
    $sql = "INSERT INTO ".'order_role'." (role_name, action_list, role_describe) ".
           "VALUES ('".trim($_POST['user_name'])."','$act_list','".trim($_POST['role_describe'])."')";

    $db_write->query($sql);
    /* 转入权限分配列表 */
    $new_id = $db_write->Insert_ID();

    /*添加链接*/

    $link[0]['text'] = '角色列表';
    $link[0]['href'] = 'role.php?act=list';

    sys_msg('添加' . "&nbsp;" .$_POST['user_name'] . "&nbsp;" . '操作成功',0, $link);

    /* 记录管理员操作 */
    admin_log($_POST['user_name'], 'add', 'role');
 }

/*------------------------------------------------------ */
//-- 编辑角色信息
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
	//admin_priv('role');
	include(dirname(__FILE__).'/includes/lib_common.php');
	include(dirname(__FILE__).'/includes/priv_action.php');
    $_REQUEST['id'] = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
        /* 获得该管理员的权限 */
    $priv_str = $db_read->getOne("SELECT action_list FROM " .'order_role'. " WHERE role_id = '$_GET[id]'");

    /* 查看是否有权限编辑其他管理员的信息 */
    /*if ($_SESSION['admin_id'] != $_REQUEST['id'])
    {
        admin_priv('admin_manage');
    }*/

    /* 获取角色信息 */
    $sql = "SELECT role_id, role_name, role_describe FROM " .'order_role'.
           " WHERE role_id = '".$_REQUEST['id']."'";
    $user_info = $db_read->getRow($sql);

    /* 获取权限的分组数据 */
    $sql_query = "SELECT action_id, parent_id, action_code FROM " .'order_admin_action'.
                 " WHERE parent_id = 0";
    $res = $db_read->query($sql_query);
    while ($rows = $db_read->FetchRow($res))
    {
        $priv_arr[$rows['action_id']] = $rows;
    }

    /* 按权限组查询底级的权限名称 */
    $sql = "SELECT action_id, parent_id, action_code FROM " .'order_admin_action'.
           " WHERE parent_id " .db_create_in(array_keys($priv_arr));
    $result = $db_read->query($sql);
    while ($priv = $db_read->FetchRow($result))
    {
        $priv_arr[$priv["parent_id"]]["priv"][$priv["action_code"]] = $priv;
    }

    // 将同一组的权限使用 "," 连接起来，供JS全选
    foreach ($priv_arr AS $action_id => $action_group)
    {
        $priv_arr[$action_id]['priv_list'] = join(',', @array_keys($action_group['priv']));
        //$priv_arr[$action_id]['priv_list'] = implode(',', @array_keys($action_group['priv']));

        foreach ($action_group['priv'] AS $key => $val)
        {
            $priv_arr[$action_id]['priv'][$key]['cando'] = (strpos($priv_str, $val['action_code']) !== false || $priv_str == 'all') ? 1 : 0;
        }
    }


    /* 模板赋值 */

    $smarty->assign('user',        $user_info);
    $smarty->assign('form_act',    'update');
    $smarty->assign('action',      'edit');
    $smarty->assign('ur_here',     '编辑角色');
    $smarty->assign('action_link', array('href'=>'role.php?act=list', 'text' => '管理员列表'));
    $smarty->assign('lang',        $_LANG);
    $smarty->assign('priv_arr',    $priv_arr);
    $smarty->assign('user_id',     $_GET['id']);

    assign_query_info();
    $smarty->display('role_info.htm');
}

/*------------------------------------------------------ */
//-- 更新角色信息
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'update')
{
	//admin_priv('role');
    /* 更新管理员的权限 */
    $act_list = @join(",", $_POST['action_code']);
    $sql = "UPDATE " .'order_role'. " SET action_list = '$act_list', role_name = '".$_POST['user_name']."', role_describe = '".$_POST['role_describe']." ' ".
           "WHERE role_id = '$_POST[id]'";
    $db_write->query($sql);
    $user_sql = "UPDATE " .'order_admin'. " SET actions = '$act_list' ".
           "WHERE id = '$_POST[id]'";
    $db_write->query($user_sql);
    /* 提示信息 */
    $link[] = array('text' => '返回列表', 'href'=>'role.php?act=list');
    sys_msg('编辑' . "&nbsp;" . $_POST['user_name'] . "&nbsp;" . '操作成功', 0, $link);
}

/*------------------------------------------------------ */
//-- 删除一个角色
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    
	//admin_priv('role');
    $id = intval($_GET['id']);
    $num_sql = "SELECT count(*) FROM " .'order_admin'. " WHERE id = '$_GET[id]'";
    $remove_num = $db_read->getOne($num_sql);
    if($remove_num > 0)
    {
        make_json_error('此角色有管理员在使用，暂时不能删除!');
    }
    else
    {
        $exc->drop($id);
        $url = 'role.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
    }

    los_header("Location: $url\n");
    exit;
}

/* 获取角色列表 */
function get_role_list()
{
    $list = array();
    $sql  = 'SELECT role_id, role_name, action_list, role_describe '.
            'FROM ' .'order_role'.' ORDER BY role_id DESC';
    $list = $GLOBALS['db_read']->getAll($sql);

    return $list;
}

?>
