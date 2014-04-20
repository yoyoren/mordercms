<?php
include(dirname(__FILE__).'/includes/init.php');
ini_set('memory_limit', "800M");
ini_set('max_execution_time','120');
//admin_priv('');
$act = empty($_REQUEST['act']) ? 'export_view' : $_REQUEST['act'];

if($act == 'export_view')
{
	$sql = "select * from ecs_order_info as o left join ecs_order_goods as g on o.order_id=g.order_id limit 1";
	$result = $db_read->query($sql);
	$fields_arr = array();
	
	for($i=0;$i<mysql_num_fields($result);$i++){
		if(!in_array(mysql_field_name($result,$i), $fields_arr)){
			$fields_arr[] = mysql_field_name($result,$i);
		}
		
	}

	$smarty->assign('date',date("Y-m-d"));
	$smarty->assign('full_page',1);
	$smarty->assign('fields_arr',$fields_arr);
	$smarty->assign('ur_here','数据导出');
	$smarty->display('export_view.html');
}
elseif($act == 'export') {
	
	/** Error reporting */
	/*error_reporting(E_ALL);
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);*/
	
	if (PHP_SAPI == 'cli')
		die('This example should only be run from a Web Browser');
	
	/** Include PHPExcel */
	require_once './includes/Classes/PHPExcel.php';
	
	$field_str = trim($_REQUEST['field_str']);
	
	$field_arr = explode(',',$field_str);

	foreach($field_arr as $k=>$v){
		switch ($v) {
			case 'order_id':
				$field_arr[$k]='o.order_id';
			break ;
			case 'parent_id':
				$field_arr[$k]='o.parent_id';
			break ;
			case 'ExchangeState':
				$field_arr[$k]='o.ExchangeState';
			break ;
			case 'ExchangeState2':
				$field_arr[$k]='o.ExchangeState2';
			break;
			default:
				;
			break;
		}
	}

	$field_str = implode(',',$field_arr);
	$time = $_REQUEST['date'];

	$sql = "select $field_str from ecs_order_info as o left join ecs_order_goods as g on o.order_id=g.order_id where best_time > '$time' and best_time < '$time 23:30:00'";
	$result = $db_read->getAll($sql);

	function get_instance(){
		return new CI_Excel();
	}

	
	class Read_write{   
	    /**
	     * $name:选择的类型(CSV,EXCEL2003,2007)
	     * $titles:标题数组
	     * $querys:查询返回的数组 $query->result_array();
	     * $filename:保存的文件名称 
	     */ 
	    function write_Factory($titles,$querys,$filename,$name="EXCEL2007"){  
	
	      $CI = get_instance();
	      $filename=mb_convert_encoding($filename, "GBK","UTF-8");
	
	      switch ($name) {
	        case "CSV":
	            $CI->write_CSV($titles,$querys,$filename);
	            break;
	        case "EXCEL2003":
	            $CI->write_EXCEL2003($titles,$querys,$filename);
	            break;
	        case "EXCEL2007":
	            $CI->write_EXCEL2007($titles,$querys,$filename);
	            break;
	      }
	    }
	
	
	    /**
	     * $name:
	     */ 
	    function read_Facotry($filePath,$sql,$sheet=0,$curRow=2,$riqi=TRUE,$merge=FALSE,$mergeCol="B"){
	       $CI = get_instance();
	       $name=$this->_file_extend($filePath);
	       switch ($name) {
	        case "csv":
	            $CI->read_CSV($filePath,$sql,$sheet,$curRow,$riqi,$merge,$mergeCol);
	            break;
	        case "xls":
	            $CI->read_2003Excel($filePath,$sql,$sheet,$curRow,$riqi,$merge,$mergeCol);
	            break;
	        case "xlsx":
	            $CI->read_EXCEL2007($filePath,$sql,$sheet,$curRow,$riqi,$merge,$mergeCol);
	            break;
	      } 
	       $CI->mytool->import_info("filePath=$filePath,sql=$sql");
	    }
	    /**
	     * 2012-1-14 读取工作薄名称(sheetnames)
	     */ 
	     function read_sheetNames($filePath){
	       $CI = get_instance();
	       $name=$this->_file_extend($filePath);
	       $sheetnames;
	       switch ($name) {
	            case "csv":
	                $sheetnames=$CI->excel->read_CSV_Sheet($filePath);
	                break;
	            case "xls":
	                $sheetnames=$CI->excel->read_2003Excel_Sheet($filePath);
	                break;
	            case "xlsx":
	                $sheetnames=$CI->excel->read_EXCEL2007_Sheets($filePath);
	                break;
	       }    
	      return $sheetnames;    
	     }
	    //读取文件后缀名
	     function _file_extend($file_name){
	        $extend =explode("." , $file_name);
	        $last=count($extend)-1;
	        return $extend[$last];
	    }
	  //-----------------------------------------------预备保留  
	    //2011-12-21新增CVS导出功能
	     public function export_csv($filename,$title,$datas, $delim = ",", $newline = "\n", $enclosure = '"'){
	       $CI = get_instance();
	       $cvs= $this->_csv_from_result($title,$datas,$delim,$newline,$enclosure); 
	       $CI->load->helper('download');
	       $name=mb_convert_encoding($filename, "GBK","UTF-8");
	       force_download($name, $cvs); 
	    }
	    /**
	     * @param $titles:标题
	     * @param $datas:数据
	     */ 
	   	function _csv_from_result($titles,$datas, $delim = ",", $newline = "\n", $enclosure = '"'){
			$out = '';
			// First generate the headings from the table column names
			foreach ($titles as $name){
			    $name=mb_convert_encoding($name, "GBK","UTF-8");
				$out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $name).$enclosure.$delim;
			}
	
			$out = rtrim($out);
			$out .= $newline;
			// Next blast through the result array and build out the rows
			foreach ($datas as $row)
			{
				foreach ($row as $item)
				{
				 $item=mb_convert_encoding($item, "GBK","UTF-8");
					$out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $item).$enclosure.$delim;
				}
				$out = rtrim($out);
				$out .= $newline;
			}
	
			return $out;
		}   
	
	
	}
	
	/**
	 * 输出到页面上的EXCEL
	 */ 
	/**
	 * CI_Excel
	 * 
	 * @package ci
	 * @author admin
	 * @copyright 2011
	 * @version $Id$
	 * @access public
	 */
	class CI_Excel
	{   
	    //列头,Excel每列上的标识
	   private $cellArray = array(
	                        1=>'A', 2=>'B', 3=>'C', 4=>'D', 5=>'E',
	                        6=>'F', 7=>'G', 8=>'H', 9=>'I',10=>'J',
	                        11=>'K',12=>'L',13=>'M',14=>'N',15=>'O',
	                        16=>'P',17=>'Q',18=>'R',19=>'S',20=>'T',
	                        21=>'U',22=>'V',23=>'W',24=>'X',25=>'Y',
	                        26=>'Z',
	                        27=>'AA', 28=>'AB', 29=>'AC', 30=>'AD', 31=>'AE',
	                        32=>'AF', 33=>'AG', 34=>'AH', 35=>'AI',36=>'AJ',
	                        37=>'AK',38=>'AL',39=>'AM',40=>'AN',41=>'AO',
	                        42=>'AP',43=>'AQ',44=>'AR',45=>'AS',46=>'AT',
	                        47=>'AU',48=>'AV',49=>'AW',50=>'AX',51=>'AY',
	                        52=>'AZ', 53=>'BA', 54=>'BB', 55=>'BC', 56=>'BD', 57=>'BE',
	                        58=>'BF', 59=>'BG', 60=>'BH', 61=>'BI', 62=>'BJ', 63=>'BK', 64=>'BL',
	                        65=>'BM', 66=>'BN', 67=>'BO', 68=>'BP', 69=>'BQ', 70=>'BR',71=>'BS',
	                        72=>'BT', 73=>'BU', 74=>'BV', 75=>'BW', 76=>'BX', 77=>'BY',78=>'BZ',
	                        79=>'CA', 80=>'CB', 81=>'CD', 82=>'CE', 83=>'CF', 84=>'CG',85=>'CH',
	                        86=>'CI'
	                        );
	     private $E2003	= 'E2003';
	     private $E2007	= 'E2007';
	     private $ECSV	= 'ECSV';
	     private $tempName;         //当读取合并文件时,如果第二行为空,则取第一行的名称
	/*********************************导出数据开始****************************************************/
	    /**
	     * 生成Excel2007文件
	     */ 
	    function write_EXCEL2007($title='',$data='',$name='')
	    {   
	       $objPHPExcel=$this->_excelComm($title,$data,$name);
	        // Redirect output to a client’s web browser (Excel2007)
	        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8');
	        header("Content-Disposition: attachment;filename=$name.xlsx");
	        header('Cache-Control: max-age=0');
	        
	        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
	        $objWriter->save('php://output');  //output 允许向输出缓冲机制写入数据，和 print() 与 echo() 的方式相同。
	        exit;
	    }
	    /**
	     * 生成Excel2003文件
	     */ 
	    function write_EXCEL2003($title='',$data='',$name=''){
	
	       $objPHPExcel=$this->_excelComm($title,$data,$name);
	       //Redirect output to a client’s web browser (Excel5)
	       header('Content-Type: application/vnd.ms-excel;charset=UTF-8');
	       header("Content-Disposition: attachment;filename=$name.xls");
	       header('Cache-Control: max-age=0');
	        
	       $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	       $objWriter->save('php://output');
	  
	    }

	    /**
	     * 生成CSV文件
	     */ 
	    function write_CSV($title='',$data='',$name=''){
	         $objPHPExcel=$this->_excelComm($title,$data,$name);
	      
	         header("Content-Type: text/csv;charset=UTF-8");   
	         header("Content-Disposition: attachment; filename=$name.csv");   
	         header('Cache-Control:must-revalidate,post-check=0,pre-check=0');   
	         header('Expires:0');   
	         header('Pragma:public');  
	         $objWriter = new PHPExcel_Writer_CSV($objPHPExcel,'CSV');
	         $objWriter->save("php://output");
	         exit;
	    }
	   
	    
	    function _excelComm($title,$data,$name){
	         // Create new PHPExcel object
	        $objPHPExcel = new PHPExcel();
	        $objPHPExcel=$this->_writeTitle($title,$objPHPExcel);
	       $objPHPExcel=$this->_writeDatas($data,$objPHPExcel);
	       $objPHPExcel=$this->_write_comm($name,$objPHPExcel);
	        return $objPHPExcel; 
	    }
	    
	    
	    //输出标题
	    function _writeTitle($title,$objPHPExcel){
	         //表头循环(标题)
	        foreach ($title as $tkey => $tvalue){
	            $tkey = $tkey+1;                         
	            $cell  = $this->cellArray[$tkey].'1';     //第$tkey列的第1行,列的标识符(a..z)
	            // Add some data  //表头
	          //  $tvalue=mb_convert_encoding($tvalue, "UTF-8","GBK");
	            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cell, $tvalue);  //设置第$row列的值(标题)
	        }
	        return $objPHPExcel;
	    }
	    //输出内容
	    function _writeDatas($data,$objPHPExcel){
	         //内容循环(数据库查询的返回值)            
	        foreach($data as $key =>$value) {   
	            $i = 1;
	            foreach ($value as $mkey =>$mvalue){   //返回的类型是array([0]=>array());,所以此处要循环它的value,也就是里面的array
	              
	            
	                $rows = $key+2; //开始是第二行
	                $mrow = $this->cellArray[$i].$rows;   //第$i列的第$row行
	             //   $mvalue=mb_convert_encoding($mvalue, "GBK","UTF-8");
	              // print_r($mrow."--->".$mvalue);
	             
	                $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit($mrow, $mvalue);  
	                $i++; 
	            }
	        }
	        return $objPHPExcel;
	    }
	    function _write_comm($name,$objPHPExcel){
	               // Rename sheet(左下角的标题)
	       //$objPHPExcel->getActiveSheet()->setTitle($name);
	        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
	        $objPHPExcel->setActiveSheetIndex(0);   //默认显示
	        return $objPHPExcel;
	    }
		
	}

	$d = new Read_write();
	$d->write_Factory($field_arr, $result, $time);
}
