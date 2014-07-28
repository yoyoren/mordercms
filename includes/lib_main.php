<?php

/**
 * 获得所有模块的名称以及链接地址
 *
 * @access      public
 * @param       string      $directory      插件存放的目录
 * @return      array
 */
function read_modules($directory = '.')
{
    global $_LANG;

    $dir         = @opendir($directory);
    $set_modules = true;
    $modules     = array();

    while (false !== ($file = @readdir($dir)))
    {
        if (preg_match("/^.*?\.php$/", $file))
        {
            include_once($directory. '/' .$file);
        }
    }
    @closedir($dir);
    unset($set_modules);

    foreach ($modules AS $key => $value)
    {
        ksort($modules[$key]);
    }
    ksort($modules);

    return $modules;
}
/**
 *  清除指定后缀的模板缓存或编译文件
 *
 * @access  public
 * @param  bool       $is_cache  是否清除缓存还是清出编译文件
 * @param  string     $ext       需要删除的文件名，不包含后缀
 *
 * @return int        返回清除的文件个数
 */
function clear_tpl_files($is_cache = true, $ext = '')
{
    $dirs = array();

    if ($is_cache)
    {
        $dirs[] = ROOT_PATH . 'templates/caches/';
    }
    else
    {
        $dirs[] = ROOT_PATH . 'templates/compiled/';
        $dirs[] = ROOT_PATH . 'templates/compiled/admin/';
    }

    $str_len = strlen($ext);
    $count   = 0;

    foreach ($dirs AS $dir)
    {
        $folder = @opendir($dir);

        if ($folder === false)
        {
            continue;
        }

        while ($file = readdir($folder))
        {
            if ($file == '.' || $file == '..' || $file == 'index.htm' || $file == 'index.html')
            {
                continue;
            }
            if (is_file($dir . $file))
            {
                /* 如果有文件名则判断是否匹配 */
                $pos = ($is_cache) ? strrpos($file, '_') : strrpos($file, '.');

                if ($str_len > 0 && $pos !== false)
                {
                    $ext_str = substr($file, 0, $pos);

                    if ($ext_str == $ext)
                    {
                        if (@unlink($dir . $file))
                        {
                            $count++;
                        }
                    }
                }
                else
                {
                    if (@unlink($dir . $file))
                    {
                        $count++;
                    }
                }
            }
        }
        closedir($folder);
    }

    return $count;
}

/**
 * 清除模版编译文件
 *
 * @access  public
 * @param   mix     $ext    模版文件名， 不包含后缀
 * @return  void
 */
function clear_compiled_files($ext = '')
{
    return clear_tpl_files(false, $ext);
}

/**
 * 清除缓存文件
 *
 * @access  public
 * @param   mix     $ext    模版文件名， 不包含后缀
 * @return  void
 */
function clear_cache_files($ext = '')
{
    return clear_tpl_files(true, $ext);
}

/**
 * 清除模版编译和缓存文件
 *
 * @access  public
 * @param   mix     $ext    模版文件名后缀
 * @return  void
 */
function clear_all_files($ext = '')
{
    return clear_tpl_files(false, $ext) + clear_tpl_files(true,  $ext);
}

/**
 * 清除 SQL 缓存文件
 *
 * @access  public
 * @param   mix     $clearall    是否一次性清理所有缓存文件
 * @return  void
 */
function clear_sqlcache_files($clearall = false)
{
    $dir = ROOT_PATH . 'templates/caches/';
    $folder = @opendir($dir);
    if ($folder === false)
    {
        return false;
    }

    $count = 0;
    $time = time();
    while ($file = readdir($folder))
    {
        if (substr($file, 0, 9) != 'sqlcache_')
        {
            continue;
        }
        if (filemtime($dir . $file) < $time - 300)
        {
            @unlink($dir . $file);

            if ($clearall == false && $count++ > 3000)
            {
                break;
            }
        }
    }
    closedir($folder);

    return true;
}
/**
 * 截取UTF-8编码下字符串的函数
 *
 * @param   string      $str        被截取的字符串
 * @param   int         $length     截取的长度
 * @param   bool        $append     是否附加省略号
 *
 * @return  string
 */
function sub_str($str, $length = 0, $append = true)
{
    $str = trim($str);
    $strlength = strlen($str);

    if ($length == 0 || $length >= $strlength)
    {
        return $str;
    }
    elseif ($length < 0)
    {
        $length = $strlength + $length;
        if ($length < 0)
        {
            $length = $strlength;
        }
    }

    if (function_exists('mb_substr'))
    {
        $newstr = mb_substr($str, 0, $length, EC_CHARSET);
    }
    elseif (function_exists('iconv_substr'))
    {
        $newstr = iconv_substr($str, 0, $length, EC_CHARSET);
    }
    else
    {
        //$newstr = trim_right(substr($str, 0, $length));
        $newstr = substr($str, 0, $length);
    }

    if ($append && $str != $newstr)
    {
        $newstr .= '...';
    }

    return $newstr;
}
/**
 * 计算字符串的长度（汉字按照两个字符计算）
 *
 * @param   string      $str        字符串
 *
 * @return  int
 */
function str_len($str)
{
    $length = strlen(preg_replace('/[\x00-\x7F]/', '', $str));

    if ($length)
    {
        return strlen($str) - $length + intval($length / 3) * 2;
    }
    else
    {
        return strlen($str);
    }
}

/**
 * 将JSON传递的参数转码
 *
 * @param string $str
 * @return string
 */
function json_str_iconv($str)
{
    if (EC_CHARSET != 'utf-8')
    {
        if (is_string($str))
        {
            return ecs_iconv('utf-8', EC_CHARSET, $str);
        }
        elseif (is_array($str))
        {
            foreach ($str as $key => $value)
            {
                $str[$key] = json_str_iconv($value);
            }
            return $str;
        }
        elseif (is_object($str))
        {
            foreach ($str as $key => $value)
            {
                $str->$key = json_str_iconv($value);
            }
            return $str;
        }
        else
        {
            return $str;
        }
    }
    return $str;
}
function ecs_iconv($source_lang, $target_lang, $source_string = '')
{
    static $chs = NULL;

    /* 如果字符串为空或者字符串不需要转换，直接返回 */
    if ($source_lang == $target_lang || $source_string == '' || preg_match("/[\x80-\xFF]+/", $source_string) == 0)
    {
        return $source_string;
    }

    if ($chs === NULL)
    {
        require_once(ROOT_PATH . 'includes/cls_iconv.php');
        $chs = new Chinese(ROOT_PATH);
    }

    return $chs->Convert($source_lang, $target_lang, $source_string);
}
function sys_msg($msg_detail, $msg_type = 0, $links = array(), $auto_redirect = false)
{
    if (count($links) == 0)
    {
        $links[0]['text'] = '返回上一页！';
        $links[0]['href'] = 'javascript:history.go(-1)';
    }

    assign_query_info();

    $GLOBALS['smarty']->assign('ur_here',     '系统提示：');
    $GLOBALS['smarty']->assign('msg_detail',  $msg_detail);
    $GLOBALS['smarty']->assign('msg_type',    $msg_type);
    $GLOBALS['smarty']->assign('links',       $links);
    $GLOBALS['smarty']->assign('default_url', $links[0]['href']);
    $GLOBALS['smarty']->assign('auto_redirect', $auto_redirect);

    $GLOBALS['smarty']->display('message.htm');

    exit;
}
function sys_msgn($msg_detail, $msg_type = 0, $links = array(), $auto_redirect = false)
{
    if (count($links) == 0)
    {
        $links[0]['text'] = '返回上一页！';
        $links[0]['href'] = 'javascript:history.go(-1)';
    }

    assign_query_info();

    $GLOBALS['smarty']->assign('ur_here',     '系统提示：');
    $GLOBALS['smarty']->assign('msg_detail',  $msg_detail);
    $GLOBALS['smarty']->assign('msg_type',    $msg_type);
    $GLOBALS['smarty']->assign('links',       $links);
    $GLOBALS['smarty']->assign('default_url', $links[0]['href']);
    $GLOBALS['smarty']->assign('auto_redirect', $auto_redirect);

    $GLOBALS['smarty']->display('message.htm');

    exit;
}
/**
 * 记录管理员的操作内容
 *
 * @access  public
 * @param   string      $sn         数据的唯一值
 * @param   string      $action     操作的类型
 * @param   string      $content    操作的内容
 * @return  void
 */
function admin_log($sn = '', $action, $content)
{
    $log_info = $GLOBALS['_LANG']['log_action'][$action] . $GLOBALS['_LANG']['log_action'][$content] .': '. addslashes($sn);

    $sql = 'INSERT INTO ' . $GLOBALS['ecs']->table('admin_log') . ' (log_time, user_id, log_info, ip_address) ' .
            " VALUES ('" . gmtime() . "', $_SESSION[admin_id], '" . stripslashes($log_info) . "', '" . real_ip() . "')";
    $GLOBALS['db_write']->query($sql);
}

/**
 * 将通过表单提交过来的年月日变量合成为"2004-05-10"的格式。
 *
 * 此函数适用于通过smarty函数html_select_date生成的下拉日期。
 *
 * @param  string $prefix      年月日变量的共同的前缀。
 * @return date                日期变量。
 */
function sys_joindate($prefix)
{
    /* 返回年-月-日的日期格式 */
    $year  = empty($_POST[$prefix . 'Year']) ? '0' :  $_POST[$prefix . 'Year'];
    $month = empty($_POST[$prefix . 'Month']) ? '0' : $_POST[$prefix . 'Month'];
    $day   = empty($_POST[$prefix . 'Day']) ? '0' : $_POST[$prefix . 'Day'];

    return $year . '-' . $month . '-' . $day;
}

/**
 * 设置管理员的session内容
 *
 * @access  public
 * @param   integer $user_id        管理员编号
 * @param   string  $username       管理员姓名
 * @param   string  $action_list    权限列表
 * @param   string  $last_time      最后登录时间
 * @return  void
 */
function set_admin_session($user_id, $username, $action_list, $last_time)
{
    $_SESSION['admin_id']    = $user_id;
    $_SESSION['admin_name']  = $username;
    $_SESSION['action_list'] = $action_list;
    $_SESSION['last_check']  = $last_time; // 用于保存最后一次检查订单的时间
}

/**
 * 判断管理员对某一个操作是否有权限。
 *
 * 根据当前对应的action_code，然后再和用户session里面的action_list做匹配，以此来决定是否可以继续执行。
 * @param     string    $priv_str    操作对应的priv_str
 * @param     string    $msg_type       返回的类型
 * @return true/false
 */
function admin_priv($priv_str, $msg_type = '' , $msg_output = true)
{
    if ($_SESSION['action_list'] == 'all')
    {
        return true;
    }

    if (strpos(',' . $_SESSION['action_list'] . ',', ',' . $priv_str . ',') === false)
    {
        $link[] = array('text' => '返回上一页', 'href' => 'javascript:history.back(-1)');
        if ( $msg_output)
        {
            sys_msg('您没有该权限！', 0, $link);
        }
        return false;
    }
    else
    {
        return true;
    }
}

/**
 * 检查管理员权限
 *
 * @access  public
 * @param   string  $authz
 * @return  boolean
 */
function check_authz($authz)
{
    return (preg_match('/,*'.$authz.',*/', $_SESSION['action_list']) || $_SESSION['action_list'] == 'all');
}

/**
 * 检查管理员权限，返回JSON格式数剧
 *
 * @access  public
 * @param   string  $authz
 * @return  void
 */
function check_authz_json($authz)
{
    if (!check_authz($authz))
    {
        make_json_error('无此权限！');
    }
}



/**
 *  返回字符集列表数组
 *
 * @access  public
 * @param
 *
 * @return void
 */
function get_charset_list()
{
    return array(
        'UTF8'   => 'UTF-8',
        'GB2312' => 'GB2312/GBK',
        'BIG5'   => 'BIG5',
    );
}


/**
 * 创建一个JSON格式的数据
 *
 * @access  public
 * @param   string      $content
 * @param   integer     $error
 * @param   string      $message
 * @param   array       $append
 * @return  void
 */
function make_json_response($content='', $error="0", $message='', $append=array())
{
    include_once(ROOT_PATH . 'includes/cls_json.php');

    $json = new JSON;
    $res = array('error' => $error, 'message' => $message, 'content' => $content);

    if (!empty($append))
    {
        foreach ($append AS $key => $val)
        {
            $res[$key] = $val;
        }
    }
   // print_r($val); 
    $val = $json->encode($res);   
    exit($val);
}

/**
 *
 *
 * @access  public
 * @param
 * @return  void
 */
function make_json_result($content, $message='', $append=array())
{
	make_json_response($content, 0, $message, $append);
}

/**
 * 创建一个JSON格式的错误信息
 *
 * @access  public
 * @param   string  $msg
 * @return  void
 */
function make_json_error($msg)
{
    make_json_response('', 1, $msg);
}


/**
 * 分页的信息加入条件的数组
 *
 * @access  public
 * @return  array
 */
function page_and_size($filter)
{
    if (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0)
    {
        $filter['page_size'] = intval($_REQUEST['page_size']);
    }
    elseif (isset($_COOKIE['LOS']['page_size']) && intval($_COOKIE['LOS']['page_size']) > 0)
    {
        $filter['page_size'] = intval($_COOKIE['LOS']['page_size']);
    }
    else
    {
        $filter['page_size'] = 15;
    }

    /* 每页显示 */
    $filter['page'] = (empty($_REQUEST['page']) || intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);

    /* page 总数 */
    $filter['page_count'] = (!empty($filter['record_count']) && $filter['record_count'] > 0) ? ceil($filter['record_count'] / $filter['page_size']) : 1;

    /* 边界处理 */
    if ($filter['page'] > $filter['page_count'])
    {
        $filter['page'] = $filter['page_count'];
    }

    $filter['start'] = ($filter['page'] - 1) * $filter['page_size'];

    return $filter;
}

/**
 *  将含有单位的数字转成字节
 *
 * @access  public
 * @param   string      $val        带单位的数字
 *
 * @return  int         $val
 */
function return_bytes($val)
{
    $val = trim($val);
    $last = strtolower($val{strlen($val)-1});
    switch($last)
    {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}

/**
 * 生成链接后缀
 */
function list_link_postfix()
{
    return 'uselastfilter=1';
}

/**
 * 保存过滤条件
 * @param   array   $filter     过滤条件
 * @param   string  $sql        查询语句
 * @param   string  $param_str  参数字符串，由list函数的参数组成
 */
function set_filter($filter, $sql, $param_str = '')
{
    $filterfile = basename(PHP_SELF, '.php');
    if ($param_str)
    {
        $filterfile .= $param_str;
    }
    setcookie('LOS[lastfilterfile]', sprintf('%X', crc32($filterfile)), time() + 600);
    setcookie('LOS[lastfilter]',     urlencode(serialize($filter)), time() + 600);
    setcookie('LOS[lastfiltersql]',  urlencode($sql), time() + 600);
}

/**
 * 取得上次的过滤条件
 * @param   string  $param_str  参数字符串，由list函数的参数组成
 * @return  如果有，返回array('filter' => $filter, 'sql' => $sql)；否则返回false
 */
function get_filter($param_str = '')
{
    $filterfile = basename(PHP_SELF, '.php');
    if ($param_str)
    {
        $filterfile .= $param_str;
    }
    if (isset($_GET['uselastfilter']) && isset($_COOKIE['LOS']['lastfilterfile'])
        && $_COOKIE['LOS']['lastfilterfile'] == sprintf('%X', crc32($filterfile)))
    {
        return array(
            'filter' => unserialize(urldecode($_COOKIE['LOS']['lastfilter'])),
            'sql'    => urldecode($_COOKIE['LOS']['lastfiltersql'])
        );
    }
    else
    {
        return false;
    }
}

/**
 * URL过滤
 * @param   string  $url  参数字符串，一个urld地址,对url地址进行校正
 * @return  返回校正过的url;
 */
function sanitize_url($url , $check = 'http://')
{
    if (strpos( $url, $check ) === false)
    {
        $url = $check . $url;
    }
    return $url;
}

/**
 * 取得批次信息
 * @param   int  $type 返回方式
 * @return  返回校正过的url;
 */
function get_timeplan($type)
{
	$sql = "SELECT id, shipping_timeplan_name FROM view_shipping_timeplan ORDER BY id ASC ";
    $rs = $GLOBALS['db_read']->query($sql);

    $res = array();
    while ($row = $GLOBALS['db_read']->FetchRow($rs))
    {
        if ($type == 1)
		{
		    $res[$row['id']] = $row['shipping_timeplan_name'];		
		}
		else
		{
		    $res[$row['shipping_timeplan_name']] = $row['shipping_timeplan_name'];		
		}
    }
    return $res;
}
/**
 * 取得配送站信息
 * @param   int  $type 返回方式
 * @return  返回校正过的url;
 */
function get_station($type)
{
	$sql = "SELECT id,shipping_station_name FROM view_shipping_station ORDER BY id ASC ";
    $rs = $GLOBALS['db_read']->query($sql);

    $res = array();
    while ($row = $GLOBALS['db_read']->FetchRow($rs))
    {
        if ($type == 1)
		{
		    $res[$row['id']] = $row['shipping_station_name'];		
		}
		elseif($type ==2)
		{
		    $res[$row['shipping_station_name']] = $row['shipping_station_name'];		
		}
		else
		{
		    $res[] = $row;
		}
    }
    return $res;
}
/**
 * 取得配送站配送员信息
 * @param   int  $st 返回方式
 * @return  返回校正过的url;
 */
function get_sender($st,$tp)
{
	$sql = "SELECT id,shipping_station_id,shipping_station_name,employee_name FROM view_shipping_deliveryplan where date = '".date('Y-m-d')."' ";
	if($tp ==1)
	{
	   $sql .= $st ? " and shipping_station_id = '$st'" : '';	
	}
	else
	{
	   $sql .= $st ? " and shipping_station_name = '$st'" : '';	
	}
    $rs = $GLOBALS['db_read']->getAll($sql);

    return $rs;
}
/*取得订单打印信息*/
function print_info($order_id)
{
    $rs = $cake = $gift = array();
	$sql = "select * from view_tdm_orders where order_id = ". $order_id;
	$rs  = $GLOBALS['db_read']->getRow($sql);
	$rs['cake_number']=$rs['cake_list']=$rs['sum']=$rs['cnj']=$rs['cjfee']=$rs['gift']=$rs['cdfee']=$rs['serv_fee']=$rs['cup']=$rs['bag']=$rs['kjsum']='';
    $rs['cdl'] ='';
	$bdate = substr($rs['best_time'],0,10);
	$order['print_sn'] = get_print_sn($order_id,$rs['order_sn'],$bdate);
	$gsql = "select goods_sn,goods_name,goods_attr,goods_number,goods_price,goods_discount,is_integral ".
	        " from ecs_order_goods where order_id = ".$order_id;
	$goods = $GLOBALS['db_read']->getAll($gsql);
	
	foreach($goods as  $key=> $val)
	{
	   if($val['goods_price'] > 47 && $val['goods_sn']<>'K1' &&$val['goods_sn']<>'K2' &&$val['goods_sn']<>'K3' && $val['goods_sn']<>'K4' )
       {
	      $rs['cake_number'] += $val['goods_number'];
		  $rs['cake_list'] .= $val['goods_attr'].'*'.$val['goods_name'].'*'.$val['goods_number'].',';
		  $cake[$key]['gsname'] = $val['goods_sn'] == 'D3' ? $val['goods_name'] : $val['goods_sn'].$val['goods_name'];
		  $cake[$key]['attr']   = $val['goods_attr'];
		  $cake[$key]['number'] = $val['goods_number'];
		  $cake[$key]['pern']   = $val['goods_sn'] == 'D3' ? '套' : '个';
		  $cake[$key]['sprice'] = $val['goods_price'];
		  if($val['is_integral'])
		  {
		     $cake[$key]['disct']  = 'K金兑礼';
			 $rs['kjsum'] += $val['goods_price'] * $val['goods_number'];
		  }
		  elseif($val['goods_discount'] == 1)
		  {
		    $cake[$key]['disct']  = '正常付费';
			$cake[$key]['sum'] = $val['goods_price'] * $val['goods_number'];
			$rs['sum'] += $val['goods_price'] * $val['goods_number'];
		  }
		  elseif(floatval($val['goods_discount']) < 1)
		  {
		     $cake[$key]['disct']  = '*'.$val['goods_discount'];
			 $cake[$key]['sum'] = $val['goods_price'] * $val['goods_number'] * $val['goods_discount'];
			 $rs['sum'] += $val['goods_price'] * $val['goods_number'] * $val['goods_discount'];
		  }
		  else
		  {
		     $cake[$key]['disct']  = $val['goods_discount'];
			 $cake[$key]['sum'] = ($val['goods_price'] - $val['goods_discount']) * $val['goods_number'];
			 $rs['sum'] += ($val['goods_price'] - $val['goods_discount']) * $val['goods_number'];
		  }
	   }
	   
       if($val['goods_sn'] == 'K4')
	   {
	      $rs['cup'] += $val['goods_number'];
		  $gift[$key]['goods_name'] = $val['goods_name']; 
		  $gift[$key]['goods_attr'] = $val['goods_attr']; 
		  $gift[$key]['goods_number'] = $val['goods_number']; 
		  $rs['kjsum'] += $val['goods_number'] * $val['goods_price'];
	   }
       if($val['goods_sn'] == 'K3')
	   {
	      $rs['bag'] += $val['goods_number'];
		  $gift[$key]['goods_name'] = $val['goods_name']; 
		  $gift[$key]['goods_attr'] = $val['goods_attr']; 
		  $gift[$key]['goods_number'] = $val['goods_number']; 
		  $rs['kjsum'] += $val['goods_number'] * $val['goods_price'];
	   }
       if($val['goods_sn'] == 'K2')
	   {
	      $rs['cdl'] += $val['goods_number'];
		  if($val['is_integral'])
		  {
		     $rs['kjsum'] += $val['goods_number'] * $val['goods_price'];	
		  }
		  else
		  {
		     $rs['cdfee'] += $val['goods_number'] * $val['goods_price'];		  
		  }
	   }
       if($val['goods_sn'] == 'K1'||$val['goods_sn'] == '00' )
	   {
	      $rs['cnj'] += $val['goods_number'];
		  if($val['is_integral'])
		  {
		     $rs['kjsum'] += $val['goods_number'] * $val['goods_price'];	
		  }
		  else
		  {
		     $rs['cjfee'] += $val['goods_number'] * $val['goods_price'];		  
		  }
	   }
       if($val['goods_sn'] == '09')
	   {
	      $rs['liulian'] =1;
	   }
       if($val['goods_sn'] == '29')
	   {
	      $rs['ice'] = 1;
	   }
       if($val['goods_name'] != '松仁淡奶' && $val['goods_sn'] == '06')
	   {
	      $rs['sug'] = 1;
	   }
       if($val['goods_name'] != '卡布其诺' && $val['goods_sn'] == '10')
	   {
	      $rs['sug'] = 1;
	   }	
	}
	
	$rs['addtime'] = date('Y-m-d H:i',$rs['add_time']);
	$rs['bdtime'] = substr($rs['best_time'],5,11);
	if(!strpos('1'.$rs['address'],'北京'))
	{
	   $rs['address'] = '北京市'.regionget($rs['province']).regionget($rs['city']).$rs['address'];
	}
	if($rs['user_id'] < 462918 || ($rs['user_id'] > 6000000 && $rs['user_id'] < 9237923))
	{
	   $rs['kj'] = $rs['give_integral'];
	}
	else
	{
	   $rs['kj'] = '';
	}
    if(strpos('1'.$rs['pay_name'],'其') && $rs['pay_note'] == "正常付费")
    {
       $rs['pay_name'] = "其他";
    }
    if($rs['pay_note'] == "代金卡" || $rs['pay_note'] == "销售活动" || $rs['pay_note'] == "免费赠送")
    {
       $rs['pay_name'] = $rs['pay_note'];
    }

    $rs['order_amount'] = round($rs['money_paid'],1)+round($rs['order_amount'],1)==1 ? 0 : round($rs['money_paid'],1)+round($rs['order_amount'],1);
    $rs['serv_fee'] = $rs['shipping_fee'] + $rs['pay_fee'];	
	$rs['cake'] = $cake;
	$rs['gift'] = $gift;
    $rs['fjfee'] = $rs['cdfee'] + $rs['cjfee'];
	$rs['gamount'] = $rs['goods_amount'] - $rs['fjfee'];
    return $rs;	
}
/*流水号生成*/
/*function get_print_sn($order_id,$sn,$bdate)
{
   $sql = "select print_sn from pack_order_list where order_id = '$order_id' and bdate = '$bdate'";
   $psn = $GLOBALS['db_read']->getOne($sql);
   if(empty($psn))
   {
      $sql = "select max(print_sn) from pack_order_list where bdate = '$bdate'";
	  $maxn = $GLOBALS['db_read']->getOne($sql);
	  $psn = empty($maxn) ? '1001' : intval($maxn)+1;
	  $sql2 = "insert into pack_order_list(order_id,order_sn,print_sn,bdate,ptime) values (".$order_id.",'$sn','$psn','$bdate','".
	          date('Y-m-d H:i:s')."') ";
	  $GLOBALS['db_write']->query($sql2);
   }
   return $psn;
}*/
/*财务单号生成*/
function check_finance($order_id)
{
   $sql = "select id from finance_orders where order_id = '$order_id' ";
   $res = $GLOBALS['db_read']->getOne($sql);
   if(empty($res))
   {
	  $sql = "insert into finance_orders (order_id,check_status,settle_status,printtimes,account_id,operate_time) values "
	        ."(".$order_id.",'未审核','未结算','1','".$_SESSION['admin_id']."','".date('Y-m-d H:i:s')."') ";
   }
   else
   {
      $sql = "update finance_orders set printtimes=1 where order_id = " .$order_id;
   }
   $GLOBALS['db_write']->query($sql);
}
function regionget($rid)
{
   $sql = "select region_name from ecs_region where region_id = '$rid'";
   $res =  $GLOBALS['db_read']->getOne($sql);
   $str = $res ? $res : '';
   return $str;
}


/*打印模块*/
function array_sum_rec($arr,$k)
{
    $a= 0;   
    foreach ($arr as $val)
	{
	   $a += intval($val[$k]);
	}
	return $a;
}
function get_turn($btime,$city)
{
   $res = array('turn' =>1,'outime'=>'');
   $time = substr($btime,11,5);
   if($city == '443')
   {
	   if($time < '15:30')
	   {
		  $res['turn'] = 1;
		  $res['outime'] = '6:00';
	   }
	   else
	   {
		  $res['turn'] = 2;
		  $res['outime'] = '12:00';
	   }
   }
   elseif($city == '441')
   {
	   if($time < '14:00')
	   {
		  $res['turn'] = 1;
		  $res['outime'] = '7:30';
	   }
	   elseif($time >='14:00' && $time <'16:30')
	   {
		  $res['turn'] = 2;
		  $res['outime'] = '11:30';
	   }
	   elseif($time >='16:30' && $time <'19:30')
	   {
		  $res['turn'] = 3;
		  $res['outime'] = '14:30';
	   }
	   elseif($time >='19:30' && $time <='23:00')
	   {
		  $res['turn'] = 4;
		  $res['outime'] = '17:30';
	   }
   }
   if($city == '440')
   {
	   if($time < '16:00')
	   {
		  $res['turn'] = 1;
		  $res['outime'] = '6:00';
	   }
	   else
	   {
		  $res['turn'] = 2;
		  $res['outime'] = '12:00';
	   }
   }
   elseif($city == '442')
   {
	   if($time < '13:30')
	   {
		  $res['turn'] = 1;
		  $res['outime'] = '7:00';
	   }
	   elseif($time >='13:30' && $time <='15:40')
	   {
		  $res['turn'] = 2;
		  $res['outime'] = '11:30';
	   }
	   elseif($time >'15:40' && $time <='17:50')
	   {
		  $res['turn'] = 3;
		  $res['outime'] = '13:40';
	   }
	   elseif($time >'17:50' && $time <='20:00')
	   {
		  $res['turn'] = 4;
		  $res['outime'] = '15:50';
	   }
	   elseif($time >'20:00')
	   {
		  $res['turn'] = 5;
		  $res['outime'] = '18:00';
	   }   
   }
   elseif($city == '444'){
   	   if($time < '16:00')
	   {
		  $res['turn'] = 1;
		  $res['outime'] = '6:00';
	   }
	   else
	   {
		  $res['turn'] = 2;
		  $res['outime'] = '12:00';
	   }
   }
   return $res;
}
function get_free_count($weight) 
{ 
    $sum = 0;
if(intval($weight)>=5)
{
   $sum = intval($weight) * 4;
}
elseif(intval($weight)<5 && intval($weight)>=1)
{
   $sum = intval($weight) * 5;
}
elseif(intval($weight)<1 && intval($weight)>0)
{
   $sum = 1;
}
else
{
   $sum = 0;
}
if(intval($weight)==15)
{
   $sum = 50;
}
    return $sum;
}


function get_goods_weight($attr)
{
	$weight = '';
	if(floatval($attr) == 1)
	{
	   $weight = "454g";
	}
	elseif(floatval($attr) == 1.5)
	{
	   $weight = "681g";
	}
	elseif(floatval($attr) == 2)
	{
	   $weight = "908g";
	}
	elseif(floatval($attr) == 3)
	{
	   $weight = "1.362kg";
	}
	elseif(floatval($attr) == 5)
	{
	   $weight = "2.27kg";
	}
	elseif(floatval($attr) == 10)
	{
	   $weight = "4.54kg";
	}
	elseif(floatval($attr) == 15)
	{
	   $weight = "6.81kg";
	}
	elseif(floatval($attr) == 20)
	{
	   $weight = "9.08kg";
	}
	elseif(floatval($attr) == 25)
	{
	   $weight = "1.134kg";
	}
	elseif(floatval($attr) == 30)
	{
	   $weight = "13.6kg";
	}
	elseif(floatval($attr) == 0.68)
	{
	   $weight = "308g";
	}
	elseif(floatval($attr) == 0.27)
	{
	   $weight = "121g";
	}
	elseif(floatval($attr) == 0.25)
	{
	   $weight = "115g";
	}
	elseif(floatval($attr) == 121)
	{
	   $weight = "121g";
	}
	else
	{
	   $weight = "110g";
	}	
	return $weight;
}
function get_goods_canju($prints,$canju)
{
   $biaopei_sum = $biaopei_out = $i = $count = 0;
   foreach($prints as $key => $val)
   {
      $attr = intval($val['goods_attr']);
	  if($attr >=1 && $attr < 5 )
	  {
	     $biaopei_sum += $attr * 5;
		 $biaopei_out += $attr * 5 + 5;
		 $count++;
	  }
	  elseif($attr >=5 && $attr <=30)
	  {
	     $biaopei_sum += $attr * 4;
		 $biaopei_out += $attr * 4 + 5;	 
		 $count++; 
	  }
   }
   if($canju <= $biaopei_sum)
   { 
	   /*foreach($prints as $key => $val)
	   {
		  $prints[$key]['canju'] = get_biaopei($val['goods_attr']);
	   }*/
	   $prints[$key]['canju']=empty($canju)?0:$canju;
   }
   elseif($canju > $biaopei_sum && $canju <= $biaopei_out )
   {
	   $t = ($canju - $biaopei_sum)%5;
	   $n = ($canju - $biaopei_sum - $t) / 5;  
	   foreach($prints as $key => $val)
	   {	   
		   if(intval($val['goods_attr']) >=1 && intval($val['goods_attr']) <= 30)
		   {
		      if($i < $n)
			  {
			     $prints[$key]['canju'] = get_biaopei($val['goods_attr'])+5;
			  }
			  elseif($i == $n)
			  {
			     $prints[$key]['canju'] = get_biaopei($val['goods_attr'])+$t;
			  }
			  else
			  {
			     $prints[$key]['canju'] = get_biaopei($val['goods_attr']);
			  }
			  $i++;
		   }
		   else
		   {
		      $prints[$key]['canju'] = get_biaopei($val['goods_attr']);
		   }
	   }
   }
   else
   {
       $out = $canju - $biaopei_out;
	   foreach($prints as $key => $val)
	   {	   
		   if(intval($val['goods_attr']) >=1 && intval($val['goods_attr']) <= 30)
		   {
		      if($i < $count-1)
			  {
			     $prints[$key]['canju'] = get_biaopei($val['goods_attr']) + 5;
			  }
			  else
			  {
			     $prints[$key]['canju'] = get_biaopei($val['goods_attr']) + 5 + $out;
			  }
			  $i++;
		   }
		   else
		   {
		      $prints[$key]['canju'] = get_biaopei($val['goods_attr']);
		   }
	   }
   }   
   return $prints;
}


/*function order_detail($order_id)
{
	$sql = "select * "
	     . "from ecs_order_info as o "
         . "where o.order_id = '$order_id' and o.order_id >0 ";
	$res = $GLOBALS['db_read']->getRow($sql);
	//$res = $GLOBALS['db110']->getRow($sql);
    $res['add_date'] = date('Y-m-d H:i',$res['add_time']);
	$res['btime'] = substr($res['best_time'],5,11);
	$res['goods_amount'] = intval($res['goods_amount']);
	$res['fw_fee'] = intval($res['pay_fee'] + $res['shipping_fee']);
	$res['amount'] = floatval($res['order_amount'] + $res['money_paid']);
	$res['money_paid'] = intval($res['money_paid']);
	$res['address'] = region_name($res['country']).region_name($res['city']).$res['address'];
    return $res;   	   
}*/
function order_detail($order_id)
{
	$sql = "select * "
	     . "from ecs_order_info as o "
         . "where o.order_id = '$order_id' and o.order_id >0 ";
	$res = $GLOBALS['db_read']->getRow($sql);
	//$res = $GLOBALS['db110']->getRow($sql);
    $res['add_date'] = date('Y-m-d H:i',$res['add_time']);
	$res['btime'] = substr($res['best_time'],5,11);
	$res['goods_amount'] = intval($res['goods_amount']);
	$res['fw_fee'] = intval($res['pay_fee'] + $res['shipping_fee']);
	$r=print_goods($order_id);
	$r=$r['fj_fee'];
	$res['fj_fee']=$r;//附件费
	//$res['fj_fee']=$res['pack_fee'];//附件费
	$res['amount'] = floatval($res['order_amount'] + $res['money_paid']);
	$res['money_paid'] = intval($res['money_paid']);
	$res['address'] = region_name($res['country']).region_name($res['city']).$res['address'];
	//$res['wsts']=str_replace(chr(13),'',$res['wsts']);
	//$res['wsts']=str_replace(chr(10),'',$res['wsts']);
	$card_name=explode(";",$res['card_name']);
	array_pop($card_name);//生日牌标识
	
	$card_m=explode(";",$res['card_message']);
	array_pop($card_m);//生日牌内容
	//print_r($card_m);	
	//print_r($card_name);	
	
	$card_message=array();//重新定义生日牌内容数组
	foreach($card_name as $key=>$val){
	  if($val=='中文'){//生日牌标识：中文
	    array_push($card_message,'生日快乐');
	  }elseif($val=='英文'){//生日牌标识：英文
	    array_push($card_message,'Happy Birthday');
	  }elseif($val=='无'){//生日牌标识：无
	    array_push($card_message,'无');
	  }elseif($val=='其它'){	//生日牌标识：其他	    
	    foreach($card_m as $k=>$v){
		  if(!empty($v)){		  
		   if($key==$k)  array_push($card_message,$card_m[$key]);  
		  }		     
		}	       
	   // array_push($card_message,$card_m[$key]);
	  }
	}
	
	
	//print_r($card_message);
	foreach($card_message as $k=>$v){
	  if($k!=count($card_message)-1){
	    $card_mess.=$v.'，';
	  }else{
	    $card_mess.=$v;
	  }
	}
	//echo $card_mess;
	$res['card_mess']=$card_mess;//生日牌内容字符串	
    return $res;   	   
}
/*function print_goods($order_id)
{
	$sql = "select goods_sn,goods_name,goods_attr,goods_price,goods_number,goods_discount,is_integral,(goods_price * goods_number * goods_discount) as goods_sub "
	     . "from ecs_order_goods "
         . "where order_id = '$order_id' ";
	$goods = $GLOBALS['db_read']->getAll($sql);
	//$goods = $GLOBALS['db110']->getAll($sql);
	$res = $a = $b = array();
	$canju = $candle = $canju_sum = $candle_sum = 0;
	foreach($goods as $key => $val)
	{
	   $val['goods_sub'] = intval($val['goods_sub']);
	   $val['goods_price'] = $val['goods_price'];
	   if($val['goods_sn'] == '00' || $val['goods_sn'] == 'K1')
	   {
	      $canju += $val['goods_number'];
		  $canju_sum += $val['goods_number'] * $val['goods_price']; 
	   }
	   elseif($val['goods_sn'] == 'K2')
	   {
	      $candle += $val['goods_number'];
		  $candle_sum += $val['goods_number'] * $val['goods_price']; 
	   }
	   elseif($val['goods_sn'] == 'K3' || $val['goods_sn'] == 'K4')
	   {
	      $a[] = $val;
	   }
	   else
	   {
	      $b[] = $val;
	   }
	}
    $res['canju'] = $canju;
	$res['candle'] =$candle;
    $res['canju_sum'] = $canju_sum;
	$res['candle_sum'] =$candle_sum;
	$res['gifts'] = $a;
	$res['goods'] = $b;
    return $res;   	   
}*/

function print_goods($order_id)
{
	
	$sql = "select goods_id,goods_sn,goods_name,goods_attr,goods_price,goods_number,goods_discount,is_integral,(goods_price * goods_number * goods_discount) as goods_sub "
	     . "from ecs_order_goods "
         . "where order_id = '$order_id' ";
	$goods = $GLOBALS['db_read']->getAll($sql);
	//$goods = $GLOBALS['db110']->getAll($sql);
	$res = $a = $b = array();
	$canju = $candle = $canju_sum = $candle_sum =$goods_attr =$goods_number =$freecanju = $cakenum=0;
	//print_r($goods);
	$cake=count($goods);
	foreach($goods as $key => $val)
	{
	   $val['goods_sub'] = intval($val['goods_sub']);
	   $val['goods_price'] = intval($val['goods_price']);	    
	   $goods_attr+=$val['goods_attr'];
	   $goods_attr=floor($goods_attr);
	   $goods_number+=$val['goods_number'];
	   
	   
	   if($val['goods_sn'] == '00' || $val['goods_sn'] == 'K1'|| $val['goods_sn'] == 'A0100')
	   {
	      $canju += $val['goods_number'];
		  
		   
	   }
	   elseif($val['goods_sn'] == 'K2'||$val['goods_sn'] == 'A0102')
	   {
	      $candle += $val['goods_number'];
		  $candle_sum += $val['goods_number'] * $val['goods_price']; 
	   }
	   elseif($val['goods_sn'] == 'K3' || $val['goods_sn'] == 'K4')
	   {
	      $a[] = $val;
	   }
	   else
	   {
	      
		  $cakenum+=$val['goods_number'];
	      $b[] = $val;
		 
	   }
	   $goods=$goods_number-$canju;
	   $goods_n=$goods-$candle;
	   
	   
	}
	$freecanju = '';
	foreach($b as $k=>$v){
		if($v['goods_id']!=67)//数字蜡烛没有餐具
		{
			if(floor($v['goods_attr'])<=3&&floor($v['goods_attr'])>=1)
			{
			   $freecanju+=floor($v['goods_attr'])*$v['goods_number']*5;
			}
			else if($v['goods_attr']==0.25)
			{
			   $freecanju+=$v['goods_number'];
			}
			else
			{
				$freecanju+=floor($v['goods_attr'])*$v['goods_number']*4;
			}
		}
	}
	$add=$canju-$freecanju;	
	if($add>0){
	  $canju_sum = $add * 0.5;
	}
	$res['freecanju'] = $freecanju; 	
	$res['add'] = $add;
    $res['canju'] = $canju;
	$res['candle'] =$candle;
    $res['canju_sum'] = $canju_sum;
	$res['candle_sum'] =$candle_sum;
	$res['fj_fee']=$canju_sum+$candle_sum;
	$res['gifts'] = $a;
	$res['goods'] = $b;
	$res['cakenum'] = $cakenum;
    return $res;   	   
}
function region_name($reid)
{
    $sql = "SELECT region_name FROM ship_region WHERE region_id = '$reid'";

    return $GLOBALS['db_read']->GetOne($sql);
}

function get_print_sn($order_id,$bdate,$admin_id,$group,$t)
{
	$sql = "select print_sn as print_sn from print_log_x where order_id = '$order_id'";

	$res = $GLOBALS['db_write']->getOne($sql);
	$time = date('Y-m-d H:i:s');
	
	if(!$res)
	{   	    
        $rs = $GLOBALS['db_write']->getOne("select max(print_sn) from print_log_x where bdate = '$bdate' and city_group='$group'");

		$psn = $rs > 0 ? intval($rs)+1 : 1001;
		if($t == 1){
		    if($group==441){
              $sql = "insert into  print_log_x(order_id,bdate,stime,print_sn,admin_id,st,city_group) values ('$order_id','$bdate','$time','$psn','$admin_id',1,'$group')";
	          $sq = "insert into print_log_bt (order_id,bdate,stime,print_sn,stimes) values ('$order_id','$bdate','$time','$psn',1)";
	        }else if($group==442){
	           $sql = "insert into  print_log_x(order_id,bdate,stime,print_sn,admin_id,st,city_group) values ('$order_id','$bdate','$time','$psn','$admin_id',1,'$group')";
	          $sq="insert into print_log (order_id,bdate,stime,print_sn,admin_id)values('$order_id','$bdate','$time','$psn','$admin_id')";
	        }
			
			
		}else{
		     if($group==441){
              $sql = "insert into  print_log_x (order_id,bdate,ptime,print_sn,admin_id2,pt,city_group) values ('$order_id','$bdate','$time','$psn','$admin_id',1,$group)";
	          $sq = "insert into print_log_bt (order_id,bdate,ptime,print_sn) values ('$order_id','$bdate','$time','$psn')";
	        }else if($group==442){
	          $sql = "insert into  print_log_x (order_id,bdate,ptime,print_sn,admin_id2,pt,city_group) values ('$order_id','$bdate','$time','$psn','$admin_id',1,$group)";
	          $sq="insert into print_log (order_id,bdate,ptime,print_sn,admin_id2)values('$order_id','$bdate','$time','$psn','$admin_id')";
	        }
			

		}
		$GLOBALS['db_write']->query($sql);
		$GLOBALS['db_write']->query($sq);
	}
	else
	{
	    $psn = $res;
		if($group==441){
		  $st = $t == 1 ? " stime = '".$time."',stimes=stimes+1 where 1 " : " ptime = '".$time."' where ptime = '' ";
		  $GLOBALS['db_write']->query("update print_log_bt set ".$st." and order_id = '$order_id'");
		  $str = $t == 1 ? " stime = '".$time."', admin_id='".$admin_id."' ,st=st+1 where 1 " : " ptime = '".$time."', admin_id2='".$admin_id."',pt=pt+1 where 1 ";
		  $re = $GLOBALS['db_write']->query("update print_log_x set ".$str." and order_id = '$order_id'");
		}else if($group==442){
		  $st = $t == 1 ? " stime = '".$time."', admin_id='".$admin_id."' where stime = '' " : " ptime = '".$time."', admin_id2='".$admin_id."' where ptime = '' ";
		  $GLOBALS['db_write']->query("update print_log set ".$st." and order_id = '$order_id'");
		  $str = $t == 1 ? " stime = '".$time."', admin_id='".$admin_id."' ,st=st+1 where 1 " : " ptime = '".$time."', admin_id2='".$admin_id."',pt=pt+1 where 1 ";
		  $re = $GLOBALS['db_write']->query("update print_log_x set ".$str." and order_id = '$order_id'");
		
		}
		
	}
	return $psn;
}

function get_print_sn1($order_id,$bdate,$admin_id,$group,$t)
{
    $sql = "select print_sn as print_sn from print_log where order_id = '$order_id'";
	$res = $GLOBALS['db_read']->getOne($sql);
	$time = date('Y-m-d H:i:s');
	
	if(!$res)
	{
	    $rs = $GLOBALS['db_read']->getOne("select max(print_sn) from print_log where bdate = '$bdate' and city_group='$group'"); 
		$psn = $rs > 0 ? intval($rs)+1 : 1001;
		if($t == 1){
			
			$sql = "insert into  print_log_x (order_id,bdate,stime,print_sn,admin_id,st,city_group) values ('$order_id','$bdate','$time','$psn','$admin_id',1,'$group')";
		}else{
			$sql = "insert into  print_log_x (order_id,bdate,ptime,print_sn,admin_id2,pt,city_group) values ('$order_id','$bdate','$time','$psn','$admin_id',1,$group)";

		}
		$GLOBALS['db_write']->query($sql);
	}
	else
	{
	    $psn = $res;
		$str = $t == 1 ? " stime = '".$time."', admin_id='".$admin_id."' ,st=st+1 where 1 " : " ptime = '".$time."', admin_id2='".$admin_id."',pt=pt+1 where 1 ";
		$re = $GLOBALS['db_write']->query("update print_log set ".$str." and order_id = '$order_id'");
	}
	return $psn;
}
function pro_print1($order_id)
{
	$sql = "select o.order_sn,o.best_time,o.scts,o.card_name,o.card_message,g.goods_name as gname,g.goods_sn,g.goods_attr,g.goods_number,c.*, "
	     . "o.country,o.address from ecs_order_info as o "
	     . "left join ecs_order_goods as g on o.order_id=g.order_id "
	     . "left join print_goods as c on g.goods_id=c.goods_id "
         . "where o.order_id = '$order_id' and (g.goods_price>100 or g.goods_sn='34')";	
	$goods = $GLOBALS['db_read']->getAll($sql);
	$sql2  = "select sum(goods_number) from ecs_order_goods where order_id = '$order_id' and goods_id=60 and goods_price != 0";
	$canju = $GLOBALS['db_read']->getOne($sql2);
	$sql3  = "select sum(goods_number) from ecs_order_goods where order_id = '$order_id' and goods_id=61";
	$candle= $GLOBALS['db_read']->getOne($sql3);
	$sql4="select goods_number,goods_attr from ecs_order_goods where order_id = '$order_id' and goods_id=67";
	$num_candle_arr=$GLOBALS['db_read']->getAll($sql4);
	$sum_num_candle=0;
	$num_candle_attr="";
	foreach($num_candle_arr as $val)
	{
		for($i=0;$i<$val['goods_number'];$i++)
		{
			if($val['goods_attr']>9)
			{
				$sum_num_candle+=2;
			}
			else if(($val['goods_attr']>0 && $val['goods_attr']<=9)||$val['goods_attr']===0)
			{
				$sum_num_candle+=1;
			}
			$num_candle_attr.=$val['goods_attr'].",";
		}
	}
	$res = $a = array();
	$count = array_sum_rec($goods,'goods_number'); 
	//echo $count;
	@$cards = explode(';',$goods['0']['card_name']);
	@$cmsgs = explode(';',$goods['0']['card_message']);
	//上海八款蛋糕，蛋糕标签修改--（裱花蛋糕-冰点心）...
	$msArray = array(51,52,55,58,63,64,92,93);
	
	foreach ($goods as $key => $val)
	{   
		//取得冰包编号
		$sql = "select ice_bag_num from order_ice_bag where order_id=$order_id and goods_attr='".$val['goods_attr']."'";
		$ice_bag_arr = $GLOBALS['db_read']->getAll($sql);
	   if ($val['goods_number']>1)
	   {
          for($i=0;$i<$val['goods_number'];$i++)
		  {
	             $turn = get_turn($val['best_time'],$val['country']);
				 $a['order_sn']    = $val['order_sn'];
	             $a['country']     = $val['country'];
				 $a['end_time']    = date('Y-m-d H:i',strtotime($val['best_time'])-4*3600);
				 //$a['end_time']    = date('H:i',strtotime($val['best_time'])-4*3600);
				$a['scts']        = $val['scts'];
				 
	             $a['card_name']   = empty($cards[$key]) ? '无' : $cards[$key];
				 $a['cmessage']    = empty($cmsgs[$key]) ? '无' : $cmsgs[$key];
				 $a['goods_attr']  = $val['goods_attr'];
	             $a['goods_sn']    = $val['goods_sn'];
				 $a['goods_name']  = $val['goods_name'];
				 $a['ice_bag_num'] = $ice_bag_arr[$i]['ice_bag_num'];
				 
				 $a['cj'] = get_free_count($val['goods_attr']);	//???????
				 
				 $a['gname']       = $val['gname'];
				 $a['bdate']       = substr($val['best_time'],0,4).'/'.substr($val['best_time'],5,2).'/'.substr($val['best_time'],8,2);
				 //$a['bdate']       = substr($val['best_time'],0,4).'&nbsp;/&nbsp;'.substr($val['best_time'],5,2).'&nbsp;/&nbsp;'.substr($val['best_time'],8,2);
				 $a['picitime']    = $turn['outime'];
				 $a['i']           = $key + $i + 1;
				 $a['goods_count'] = $count;
				 $a['goods_pl']    = $val['raw'];
				 $a['goods_tjj']   = $val['adds'];
				 $a['goods_sav']   = $val['saves'];
				 $a['goods_eat']   = $val['eats'];
				 
				 $a['energy_g']    = round($val['energy_g']);
				 $a['energy_r']    = $val['energy_r'];
				 $a['protein_g']   = $val['protein_g'];
				 $a['protein_r']   = $val['protein_r'];
				 $a['fat_g']       = $val['fat_g'];
				 $a['fat_r']       = $val['fat_r'];
				 $a['na_g']        = round($val['na_g']);
				 $a['na_r']        = $val['na_r'];
				 $a['carb_g']      = $val['carb_g'];
				 $a['carb_r']      = $val['carb_r'];
				 
				 $a['weight']      = get_goods_weight($val['goods_attr']);
				 $a['ptime']       = $GLOBALS['db_read']->getOne("select ptime from print_log_x where order_id = ".$order_id);
				 $a['ptime2']      = date('Y-m-d H:i:s');
				 //$a['candle']      = $candle ? $candle : 0;
		  		 if($_SESSION['city_group'] == '442'){
		  		 	$a['cit']     = 's';
					 if(in_array($val['goods_id'],$msArray)){
					 	$a['ms']     = 'm';
					 }
				 }
				 $a['best_time']      = $val['best_time'];
				 $res[] =$a; 
		  }
	   }
	   else
	   {
	             $turn = get_turn($val['best_time'],$val['country']);
				 $a['order_sn']    = $val['order_sn'];
	             $a['country']     = $val['country'];
				 $a['end_time']    = date('Y-m-d H:i',strtotime($val['best_time'])-4*3600);
				 //$a['end_time']    = date('H:i',strtotime($val['best_time'])-4*3600);
				 $a['scts']        = $val['scts'];
	             $a['card_name']   = empty($cards[$key]) ? '无' : $cards[$key];
				 $a['cmessage']    = empty($cmsgs[$key]) ? '无' : $cmsgs[$key];
				 $a['goods_attr']  = $val['goods_attr'];
				 @$a['ice_bag_num'] = $ice_bag_arr[0]['ice_bag_num'];
				 
				 $a['cj'] = get_free_count($val['goods_attr']);	//???????
				 
	             $a['goods_sn']    = $val['goods_sn'];
				 $a['goods_name']  = $val['goods_name'];
				 $a['gname']       = $val['gname'];
				 $a['bdate']       = substr($val['best_time'],0,4).'/'.substr($val['best_time'],5,2).'/'.substr($val['best_time'],8,2);
				 $a['picitime']    = $turn['outime'];
				 $a['i']           = $key + 1;

				 $a['goods_count'] = $count;
				 $a['goods_pl']    = $val['raw'];
				 $a['goods_tjj']   = $val['adds'];
				 $a['goods_sav']   = $val['saves'];
				 $a['goods_eat']   = $val['eats'];
				 
				 $a['energy_g']    = round($val['energy_g']);
				 $a['energy_r']    = $val['energy_r'];
				 $a['protein_g']   = $val['protein_g'];
				 $a['protein_r']   = $val['protein_r'];
				 $a['fat_g']       = $val['fat_g'];
				 $a['fat_r']       = $val['fat_r'];
				 $a['na_g']        = round($val['na_g']);
				 $a['na_r']        = $val['na_r'];
				 $a['carb_g']      = $val['carb_g'];
				 $a['carb_r']      = $val['carb_r'];
				 
				 $a['weight']      = get_goods_weight($val['goods_attr']);
				 $a['ptime']       = $GLOBALS['db_read']->getOne("select ptime from print_log_x where order_id = ".$order_id);
				 $a['ptime2']      = date('Y-m-d H:i:s');
				 //$a['candle']      = $candle ? $candle : 0;
	   			if($_SESSION['city_group'] == '442'){
	   				 $a['cit']     = 's';
					 if(in_array($val['goods_id'],$msArray)){
					 	$a['ms']     = 'm';
					 }
				 }
				 $a['best_time']      = $val['best_time'];
				 $res[] =$a; 
	   }  
	   
	   
	}

	@$res[0]['cj'] = $res[0]['cj']+$canju;
	$res[0]['candle'] = $candle;
	$res[0]['sum_num_candle']=$sum_num_candle;
	$res[0]['num_candle_attr']="(".$num_candle_attr.")";
	//return get_goods_canju($res,$canju);	
	return $res;   	   
}
/*打印模块--end*/


//新增函数
/**
 * 创建像这样的查询: "IN('a','b')";
 *
 * @access   public
 * @param    mix      $item_list      列表数组或字符串
 * @param    string   $field_name     字段名称
 *
 * @return   void
 */
function db_create_in($item_list, $field_name = '')
{
    if (empty($item_list))
    {
        return $field_name . " IN ('') ";
    }
    else
    {
        if (!is_array($item_list))
        {
            $item_list = explode(',', $item_list);
        }
        $item_list = array_unique($item_list);
        $item_list_tmp = '';
        foreach ($item_list AS $item)
        {
            if ($item !== '')
            {
                $item_list_tmp .= $item_list_tmp ? ",'$item'" : "'$item'";
            }
        }
        if (empty($item_list_tmp))
        {
            return $field_name . " IN ('') ";
        }
        else
        {
            return $field_name . ' IN (' . $item_list_tmp . ') ';
        }
    }
}


function city_location() { // To judge where is the city
	$flag = false;
	$city = array('440', '442'); // This is Shanghai group
	foreach ($_SESSION['city_arr'] as $key => $val) {
		if (in_array($key, $city))			$flag = true;
	}
	return $flag ? true : false;
}

/**
 * 作用：得到城市批次
 * 返回：array 
 * Enter description here ...
 */
function getTurn(){
	$sql = "select turn from order_city where city_group=".$_SESSION['city_group'];
   	$turn = $GLOBALS['db_read']->getOne($sql);
   	for($i=1;$i<=$turn;$i++){
   		$res[$i] = '第'.$i.'批';
   	}
   	return $res;
}


/*
*查询订单相关支付信息
*/
function pay_info($order_id){
  $pay_info=array();
  $sql1="select bonus,money_paid from ecs_order_info where order_id='$order_id'";
  $paid_list=$GLOBALS['db_read']->getRow($sql1);
  $paid_bonus=$paid_list['bonus'];
  $paid_money_paid=$paid_list['money_paid'];
  $sql="select * from tender_info where order_id='$order_id'";
  $rs=$GLOBALS['db_read']->getAll($sql);
  //print_r($rs); exit;
  //echo count($rs); 
  foreach($rs as $k=>$v){
   if($k!=count($rs)-1){
   $typestr.=$v['type'].",";
   }else{
   $typestr.=$v['type'];
   }
   $zamount+=$v['amount']; 
  	if($v['type']==4)
	{
		$v['pay_name']=mb_substr($v['pay_name'],9,strlen($v['pay_name']),'utf-8');
	}
   if($k!=count($rs)-1){
   $paynamestr.=$v['pay_name']."<br>";
   }else{
   $paynamestr.=$v['pay_name'];
   }
   //$card_no=$v['card_no'];
  }
 // echo $typestr;
 //echo $zamount;
  $typearr=explode(',',$typestr);
  $paynamearr=explode(',',$paynamestr);
  //print_r($typearr);
  //print_r(array_diff(array(1,4),$typearr));
  //现金券+礼金卡+POS
  if(array_diff(array(1,4),$typearr)!=true){
    $sql="select amount from tender_info where order_id='$order_id' and type=1";
	$rs1= $GLOBALS['db_read']->getAll($sql);
	//print_r($rs);
	foreach($rs1 as $k=>$v){
	  $unpaid1n+=$v['amount'];
	  @$unpaid1.=$v['amount']."元<br>";
	}
	//$paid=$zamount-$unpaid1;
	$sql="select amount,right(remark,4) as remark1 from tender_info where order_id='$order_id' and type=4";		
	$rs2= $GLOBALS['db_read']->getAll($sql);
	
	//echo $sql;exit;
	//$attr=$GLOBALS['db_read']->getAll($sql);
	foreach($rs2 as $k=>$v){
	  $unpaid2n+=$v['amount'];
	  $unpaid2n=intval($unpaid2n);
	  @$unpaid2.=$v['amount']."元<br>";
	  if($k!=count($rs2)-1){
	    $card_no.=$v['remark1'].",";
	  }else{
	    $card_no.=$v['remark1'];	 
	  }
   }
   $unpaid3=array($unpaid1);
   foreach($unpaid3 as $k=>$v){
    $unpaid.=$v;
   }
   $paid= $paid_bonus+$paid_money_paid;
   //print_r($unpaid);
   //print_r($attr);
  }
  //礼金卡+现金+POS机
  else if(array_diff(array(1,6),$typearr)!=true){
    $sql="select amount from tender_info where order_id='$order_id' and type=1";
	$rs1= $GLOBALS['db_read']->getAll($sql);
	//print_r($rs);
	foreach($rs1 as $k=>$v){
	  $unpaid1n+=$v['amount'];
	  @$unpaid1.=$v['amount']."元<br>";
	}
	//$paid=$zamount-$unpaid1;
	$sql="select amount from tender_info where order_id='$order_id' and type=6";		
	$rs2= $GLOBALS['db_read']->getOne($sql);
    $paid2=$rs2;
    $unpaid3=array($unpaid1);
    foreach($unpaid3 as $k=>$v){
		 $unpaid.=$v;
    }
   $paid= $paid_bonus+$paid_money_paid+$paid2;
   //print_r($unpaid);
   //print_r($attr);
  }else if(in_array('1',$typearr)){
    $sql="select amount from tender_info where order_id='$order_id' and type=1";
	$rs= $GLOBALS['db_read']->getAll($sql);
	//print_r($rs);
	foreach($rs as $k=>$v){
	  $unpaidn+=$v['amount'];
	  $unpaid.=$v['amount']."元<br>";
	}
	$paid=$zamount-$unpaid+$paid_money_paid;
	//echo $paid;
  }else if(in_array('4',$typearr)){
    $sql="select pay_name,amount,right(remark,4) as remark1 from tender_info where order_id='$order_id' and type=4";	
	$rs= $GLOBALS['db_read']->getAll($sql);
	foreach($rs as $k=>$v){
	  $unpaidn+=$v['amount'];
	  $unpaid.=$v['amount']."元<br>";
	  $paid=$zamount-$unpaidn+$paid_bonus+$paid_money_paid;
	  //$unpaid=intval($unpaid);
	  $unpaid=0.00;
	  if($k!=count($rs)-1){
	    $card_no.=$v['remark1'].",";
	  }else{
	    $card_no.=$v['remark1'];
	  }
	  
	 
   }
   //echo $unpaid."123";exit;
  
  }else if(in_array('2',$typearr)){
    $sql="select pay_name,amount,remark from tender_info where order_id='$order_id' and type=2";	
	$rs= $GLOBALS['db_read']->getAll($sql);
	foreach($rs as $k=>$v){
	  $unpaidn+=$v['amount'];
	  $unpaid.=$v['amount']."元<br>";
	  $paid=$zamount-$unpaid+$paid_money_paid;
	 
   }
   }
   else{
     $paid=$zamount+$paid_money_paid;
	 
	 $unpaid='0.00元';
  }
  return $pay_info=array('unpaid'=>$unpaid,'paid'=>$paid,'payname'=>$paynamestr,'card_no'=>$card_no);
  
}
?>