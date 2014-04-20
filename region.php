<?php

require(dirname(__FILE__) . '/includes/init.php');
$_REQUEST['act'] = empty($_REQUEST['act']) ? 'list' : trim($_REQUEST['act']);

if ($_REQUEST['act'] == 'list')
{

    $region_id = empty($_REQUEST['pid']) ? 0 : intval($_REQUEST['pid']);
    $smarty->assign('parent_id',    $region_id);

    /* 取得列表显示的地区的类型 */
    if ($region_id == 0)
    {
        $region_type = 0;
    }
    else
    {
        $region_type = $db_read->getOne("select region_type from ship_region where region_id = ".$region_id) + 1;
    }
    $smarty->assign('region_type',  $region_type);

    /* 获取地区列表 */
    $region_arr = region_list($region_id);
    $smarty->assign('region_arr',   $region_arr);

    /* 当前的地区名称 */
    if ($region_id > 0)
    {
        $area_name = $db_read->getOne("select region_name from ship_region where region_id = ".$region_id);
        $area = '[ '. $area_name . ' ] ';
        if ($region_arr)
        {
            $area .= $region_arr[0]['type'];
        }
    }
    else
    {
        $area = $_LANG['country'];
    }
    $smarty->assign('area_here',    $area);

    /* 返回上一级的链接 */
    if ($region_id > 0)
    {
        $parent_id = $db_read->getOne("select parent_id from ship_region where region_id = ".$region_id);
        $action_link = array('text' => '返回上一级', 'href' => 'region.php?act=list&pid=' . $parent_id);
    }
    else
    {
        $action_link = '';
    }
    $smarty->assign('action_link',  $action_link);

    /* 赋值模板显示 */
    $smarty->assign('ur_here',      '地区管理');
    $smarty->assign('full_page',    1);

    $smarty->display('region_list.html');
}

/*------------------------------------------------------ */
//-- 添加新的地区
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'add_area')
{
    $parent_id      = intval($_POST['parent_id']);
    $region_name    = json_str_iconv(trim($_POST['region_name']));
    $region_type    = intval($_POST['region_type']);

    if (empty($region_name))
    {
        make_json_error('地区名为空');
    }

    $sql = "INSERT INTO ship_region (parent_id, region_name, region_type) ".
           "VALUES ('$parent_id', '$region_name', '$region_type')";
    if ($GLOBALS['db_write']->query($sql, 'SILENT'))
    {
        admin_log($region_name, 'add','area');

        /* 获取地区列表 */
        $region_arr = region_list($parent_id);
        $smarty->assign('region_arr',   $region_arr);

        $smarty->assign('region_type', $region_type);

        make_json_result($smarty->fetch('region_list.html'));
    }
    else
    {
        make_json_error('添加错误');
    }
}

/*------------------------------------------------------ */
//-- 编辑区域名称
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'edit_area_name')
{
    check_authz_json('area_manage');

    $id = intval($_POST['id']);
    $region_name = json_str_iconv(trim($_POST['val']));

    if (empty($region_name))
    {
        make_json_error($_LANG['region_name_empty']);
    }

    $msg = '';

    /* 查看区域是否重复 */
    $parent_id = $exc->get_name($id, 'parent_id');
    if (!$exc->is_only('region_name', $region_name, $id, "parent_id = '$parent_id'"))
    {
        make_json_error($_LANG['region_name_exist']);
    }

    if ($exc->edit("region_name = '$region_name'", $id))
    {
        admin_log($region_name, 'edit', 'area');
        make_json_result(stripslashes($region_name));
    }
    else
    {
        make_json_error($db_read->error());
    }
}

/*------------------------------------------------------ */
//-- 删除区域
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_area')
{
    check_authz_json('area_manage');

    $id = intval($_REQUEST['id']);

    $sql = "SELECT * FROM " . $ecs->table('region') . " WHERE region_id = '$id'";
    $region = $db_read->getRow($sql);

    /* 如果底下有下级区域,不能删除 */
    $sql = "SELECT COUNT(*) FROM " . $ecs->table('region') . " WHERE parent_id = '$id'";
    if ($db_read->getOne($sql) > 0)
    {
        make_json_error($_LANG['parent_id_exist']);
    }

    if ($exc->drop($id))
    {
        admin_log(addslashes($region['region_name']), 'remove', 'area');

        /* 获取地区列表 */
        $region_arr = region_list($region['parent_id']);
        $smarty->assign('region_arr',   $region_arr);
        $smarty->assign('region_type', $region['region_type']);

        make_json_result($smarty->fetch('area_list.htm'));
    }
    else
    {
        make_json_error($db_read->error());
    }
}
?>