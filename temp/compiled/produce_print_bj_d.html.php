<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>生产打印</title>
<style type='text/css'>
body{
margin:0px;

}
.ztdiv{
border:1px white solid;
}
.container{
padding-top:3px;
width:358px;
height:265px;
font:黑体;
overflow:hidden;
}
.container1{
padding-top:1px;
width:358px;
height:263px;
font:黑体;
overflow:hidden;
}

/*.container1{

border:0px black solid;
width:518px;
height:344px;
margin-bottom:0px;

}
.container2{

border:0px red solid;
margin-bottom:200px;
padding-top:10px;
width:518px;
height:344px;


}*/
.con{
width:260px;

margin-left:53px;



}
p{margin:0;padding:0;}
#left{
width:150px;
height:300px;
float:left;
}
#right{

height:310px;
float:left;
border:1px solid blue;
}
.left-content{
margin-top:0px;
margin-left:23px;
margin-right:0px;
height:260px;
border:1px solid purple;

}
.left-bottom{
margin-left:53px;
height:0px;
}
.content_text{

font-size:12px;
line-height:10px;


}
.content_14{

font-size:14px;
font-weight:bold;
}
p{
margin:0;
margin-bottom:5px;
}
.left-bottom-text{
width:80px;
float:left;

}
.left-content-container{
border-left:dashed 1px #000000;
margin-top:0px;
margin-right:0px;
height:250px;

}
.left-content-text{
width:155px;
margin-left:0px;

}
.right-title{

font-size:5px;
font-weight:bold;
text-align:center;

}
.font_22{
font-size:1px;
font-weight:bold;
line-height:5px;
}
.xm{
width:55px;
float:left;
text-align:left;

}
.mk{
width:47px;
float:left;
text-align:right;


}
.bf{
width:40px;
float:left;
text-align:right;



}
.right-content{
font-size:10px;
font:"宋体";
}
.hx3{
border-bottom:3px #000000 solid;
margin:3px 0px;
clear:both;
}
.conhx{
border-bottom:2px #000000 solid;
margin:3px 0px;
clear:both;
}
.conhx1{
border-bottom:1px #000000 solid;
margin:3px 0px;
clear:both;
}
@media print{
.noprint{
display:none;
}

}
*{
	padding:0px;
	margin:0px;
}
</style>
</head>
<body>
<div class="ztdiv">
	<div class="container">
		<div class="con">
			<div id="table" style="width:100%; border-top:solid 0px #ccc; border-left:solid 0px #ccc;"> 
				<div id="tr">	    
					<div id="td" style="width:45%; float:left; border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-2">流水号：</font></b><b><font size="-1"><?php echo $this->_var['print_sn']; ?>-<?php echo $this->_var['cakesum']; ?>-<?php echo $this->_var['cake_sn']; ?></font></b></div>				
					<table height="15">
					<tr>
					 <td></td>
					</tr>
					</table>  
				</div>
				<div id="tr">         
				  <div id="td" style="width:48%; float:left; border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-2">工单号：</font></b><b><font size=""><?php echo $this->_var['pack']['route_name']; ?>-<?php echo $this->_var['pack']['turn']; ?>-<?php if ($this->_var['order']['ice_bag_num'] < 10): ?>0<?php endif; ?><?php echo $this->_var['order']['ice_bag_num']; ?></font></b></div>     
				   <!--<div id="td" style="width:25%; float:left; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-1"><?php echo $this->_var['pack']['route_name']; ?>-<?php echo $this->_var['pack']['turn']; ?>-<?php if ($this->_var['order']['ice_bag_num'] < 10): ?>0<?php endif; ?><?php echo $this->_var['order']['ice_bag_num']; ?></font></b></div>  -->
				  <div id="td" style="width:50%; float:left; border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-2">订单号：</font></b><b><font size="-2"><?php echo $this->_var['order']['order_sn']; ?></font></b></div>      
				  <!--<div id="td" style="width:30%; float:left; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-1"><?php echo $this->_var['order']['order_sn']; ?></font></b></div> --> 
				  <table height="10">
					<tr>
					 <td></td>
					</tr>
				  </table>   
				</div>
				<div id="tr">
				 <div class="conhx"></div>
				</div>
				<div id="tr"> 
				
					<div id="td" style="width:27%; float:left; border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-1">蛋糕名称：</font></b></div>        
					<div id="td" style="width:30%; float:left; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-1"><?php if ($this->_var['order']['gname'] == '猫爪蛋糕'): ?><font size="-2"><?php echo $this->_var['order']['gname']; ?>+花环</font><?php else: ?><?php echo $this->_var['order']['gname']; ?><?php endif; ?></font> </b></div>
					<div id="td" style="width:40%; float:left; border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-2">蛋糕规格：</font></b><font size=""><b><?php if ($this->_var['order']['gname'] == '猫爪蛋糕'): ?>1  套<?php else: ?><?php echo $this->_var['order']['goods_attr']; ?><?php endif; ?></b></font></div>  
					<!--<div id="td" style="width:10%; float:left; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><font size="-1"><b><?php echo $this->_var['order']['goods_attr']; ?></b></font></div>-->				
				</div>
				<div id="tr">
					<div class="conhx1"></div>
				</div> 
				<div id="tr"> 				 
					<div id="td" style="width:65%; float:left; border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-2">生日牌信息：</font><font size="-1"><?php if ($this->_var['order']['card_name'] == '其它'): ?><?php echo $this->_var['order']['cmessage']; ?><?php else: ?><?php echo $this->_var['order']['card_name']; ?><?php endif; ?></font></b><br/><b><font size="-2">数字蜡烛信息：</font></b><b><font size="-1"><?php if ($this->_var['order']['sum_num_candle'] > 0): ?><?php echo $this->_var['order']['sum_num_candle']; ?>个<?php echo $this->_var['order']['num_candle_attr']; ?><?php else: ?>无<?php endif; ?></font></b></div> 
					<div id="td" style="width:30%; float:left; border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-2">餐具数量：</font></b><b><font size=""><?php echo $this->_var['order']['cj']; ?></font></b><br/><b><font size="-2">蜡烛数量：</font></b><b><font size=""><?php if ($this->_var['order']['candle'] == ''): ?>0<?php else: ?><?php echo $this->_var['order']['candle']; ?><?php endif; ?></font></b></div>        
					<!--<div id="td" style="width:20%; float:left; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><?php echo $this->_var['order']['cj']; ?></b> </div>-->					  
					<table height="15">
					<tr>
					 <td></td>
					</tr>
					</table>  
				</div>
				<div id="tr">
					<div class="conhx1"></div>
				</div>  
				<div id="tr">  
					<div id="td" style="width:100%; border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-2">备注: &nbsp;<?php echo $this->_var['order']['scts']; ?></b></div>        
					<!--<div id="td" style="width:30%; float:left; border-right:solid 2px #ccc; border-bottom:solid 2px #ccc;"></div>
					<table height="10" border="2">
					<tr>
					 <td></td>
					</tr>
				  </table>-->				
				</div>
				<div id="tr">
					<div class="conhx1"></div>
				</div>			 
				<div id="tr">  			   
					<div id="td" style="width:40%; float:left; border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-2">要求完成时间：<?php echo $this->_var['order']['end_time']; ?></font></b></div>        
					<div id="td" style="width:60%; float:left; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-1"></font></b></div>
					<div id="td" style="width:50%; float:left; border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-2">实际完成时间：</font></b></div>        
					<div id="td" style="width:10%; float:left; border-right:solid 0px #ccc; border-bottom:solid 1px #ccc;display:none;"></div>
					<table height="10">
					  <tr>
						<td></td>
					  </tr>
					</table>     
				</div>			 
				<div id="tr">
					<div class="conhx1"></div>
				</div>
				<div id="tr">				
					<div id="td" style="width:40%; float:left; border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-2">制作人：</font></b></div>        
					<div id="td" style="width:50%; float:left; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-1"></font></b></div>
					<div id="td" style="width:20%; float:left; border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-2">装饰人：</font></b></div>        
					<div id="td" style="width:25%; float:left; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-1"></font></b></div>        
					<table height="10">
					  <tr>
						<td></td>
					  </tr>
					</table>   
				</div>
				<div id="tr">
					<div class="conhx1"></div>
				</div>
				<div id="tr">			   
					<div id="td" style="width:40%; float:left; border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-2">包装人：</font></b></div>        
					<div id="td" style="width:50%; float:left; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-1"></font></b></div>
					<div id="td" style="width:40%; float:left; border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-2">质检人：</font></b></div>        
					<div id="td" style="width:0%; float:left; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-1"></font></b></div>        
					<table height="10">
					  <tr>
						<td></td>
					  </tr>
					</table> 
				</div>
				<div id="tr">
					<div class="conhx1"></div>
				</div>
				<div id="tr"> 			
					<div id="td" style="width:40%; float:left; border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-2">包装完成时间：</font></b></div>        
					<div id="td" style="width:50%; float:left; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-1"></font></b></div>
					<div id="td" style="width:30%; float:left; border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-2">磅重：</font></b></div>        
					<div id="td" style="width:0%; float:left; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-1"></font></b></div> 
					<table height="10">
					  <tr>
						<td></td>
					  </tr>
					</table>     
				</div>			
				<div id="tr">
					<div class="conhx1"></div>
				</div>
				<div id="tr"> 				 
					<div id="td" style="width:1%; float:left; border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-2"></font></b></div>        
					<div id="td" style="width:43%; float:left; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-2"><?php echo $this->_var['pack']['add_time']; ?></font></b></div>
					<div id="td" style="width:3%; float:left; border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-2"></font></b></div>        
					<div id="td" style="width:40%; float:left; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-2"><?php echo $this->_var['order']['ptime2']; ?></font></b></div> 
					<table height="10">
					  <tr>
						<td></td>
					  </tr>
					</table>     
				</div>
				 <div id="tr">
					<div class="conhx"></div>
				 </div>
			</div>					
		</div>
	</div>
  
  
	<div class="container" >
		<div class="con">
		   <div id="table" style="width:100%; border-top:solid 0px #ccc; border-left:solid 0px #ccc;"> 
				  <div id="tr">			   
					<div id="td" style="width:45%; float:left; border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-2">流水号：</font></b><b><font size="-1"><?php echo $this->_var['print_sn']; ?>-<?php echo $this->_var['cakesum']; ?>-<?php echo $this->_var['cake_sn']; ?></font></b></div>				
					 <table height="15">
						<tr>
						 <td></td>
						</tr>
					</table>  
				  </div>
				  <div id="tr">         
					<div id="td" style="width:48%; float:left; border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-2">工单号：</font></b><b><font size=""><?php echo $this->_var['pack']['route_name']; ?>-<?php echo $this->_var['pack']['turn']; ?>-<?php if ($this->_var['order']['ice_bag_num'] < 10): ?>0<?php endif; ?><?php echo $this->_var['order']['ice_bag_num']; ?></font></b></div>     
				   <!--<div id="td" style="width:25%; float:left; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-2"><?php echo $this->_var['pack']['route_name']; ?>-<?php echo $this->_var['pack']['turn']; ?>-<?php if ($this->_var['order']['ice_bag_num'] < 10): ?>0<?php endif; ?><?php echo $this->_var['order']['ice_bag_num']; ?></font></b></div>-->  
					<div id="td" style="width:50%; float:left; border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-2">订单号：</font></b><b><font size="-2"><?php echo $this->_var['order']['order_sn']; ?></font></b></div>      
					<!-- <div id="td" style="width:30%; float:left; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-2"><?php echo $this->_var['order']['order_sn']; ?></font></b></div>--> 			  
				  </div>
			   <div id="tr">
				 <div class="conhx"></div>
			   </div>     
				 
				  
				 <div id="tr"> 
				  <table width="50">
					<tr>
					 <td></td>
					</tr>
				  </table>     
					<div id="td" style="width:90%; float:left; border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-1">蛋糕名称：</font></b><b><font size="-1"><?php if ($this->_var['order']['gname'] == '猫爪蛋糕'): ?><?php echo $this->_var['order']['gname']; ?>+花环<?php else: ?><?php echo $this->_var['order']['gname']; ?><?php endif; ?> </font></b></div>      
					<!--<div id="td" style="width:30%; float:left; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-1"><?php echo $this->_var['order']['gname']; ?><?php if ($this->_var['order']['ms'] == 'm'): ?><span style="font-size:12px;">(裱花蛋糕-冰点心)</span> <?php else: ?>(裱花蛋糕)<?php endif; ?> </font></b></div>-->
					<div id="td" style="width:0%; float:left; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"></div>
					<table height="10">
					<tr>
					 <td></td>
					</tr>
				  </table>  
				 </div>
				 <div id="tr">
					<div class="conhx1"></div>
				 </div>
				 <div id="tr">
				  <table width="50">
					<tr>
					 <td></td>
					</tr>
				  </table>      
					<div id="td" style="width:40%; float:left; border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-2">规格：</font></b><b><font size=""><?php if ($this->_var['order']['gname'] == '猫爪蛋糕'): ?>1  套<?php else: ?><?php echo $this->_var['order']['goods_attr']; ?><?php endif; ?></font></b></div>        
					<!--<div id="td" style="width:20%; float:left; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="+1"><?php echo $this->_var['order']['goods_attr']; ?></font></b></div>-->
					<div id="td" style="width:20%; float:left; border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"></div>        <div id="td" style="width:20%; float:left; border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"></div>
					<table height="10">
					<tr>
					 <td></td>
					</tr>
				  </table> 
				 </div>
				  <div id="tr">
					<div class="conhx1"></div>
				 </div>
				  
				 <div id="tr"> 				 
					<div id="td" style="width:30%; float:left; border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-2">餐具数量：</font></b><b><font size=""><?php echo $this->_var['order']['cj']; ?></font></b></div>        
					<div id="td" style="width:35%; float:left; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-2">数字蜡烛：</font></b><b><font size="-1"><?php if ($this->_var['order']['sum_num_candle'] > 0): ?><?php echo $this->_var['order']['sum_num_candle']; ?>个<?php else: ?>无<?php endif; ?></font></b> </div>
					<div id="td" style="width:30%; float:left; border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-2">蜡烛数量：</font></b><b><font size=""><?php if ($this->_var['order']['candle'] == ''): ?>0<?php else: ?><?php echo $this->_var['order']['candle']; ?><?php endif; ?></font></b></div>
					<!--<div id="td" style="width:30%; float:left; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><?php echo $this->_var['candle']; ?></b></div>--> 
					<table height="20">
					<tr>
					 <td></td>
					</tr>
					</table>    
				 </div>
				  <div id="tr">
					<div class="conhx1"></div>
				 </div> 
				 <div id="tr">
					<table width="50">
					<tr>
					 <td></td>
					</tr>
					</table>     
					<div id="td" style="width:100%;  border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-2">备注:&nbsp;<?php echo $this->_var['order']['scts']; ?></font></b></div>        
					<div id="td" style="width:100%; height:20px;border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-1"></font></b></div>
					<!--<div id="td" style="width:40%; float:left; border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-1"></font></b></div>        
					<div id="td" style="width:40%; float:left; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"></div> 
					<table height="30">
					  <tr>
						<td></td>
					  </tr>
					</table>-->     
				 </div>
				  
				  
				  <div id="tr">
					<div class="conhx1"></div>
				 </div>
				 <div id="tr"> 
					 <table width="50">
					<tr>
					 <td></td>
					</tr>
				  </table>      
					<div id="td" style="width:25%; float:left; border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-2">出库人：</font></b></div>        
					<div id="td" style="width:24%; float:left; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-1"></font></b></div>
					<div id="td" style="width:40%; float:left; border-left:solid 0px #ccc; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"><b><font size="-1"></font></b></div>        
					<div id="td" style="width:40%; float:left; border-right:solid 0px #ccc; border-bottom:solid 0px #ccc;"></div>  
					<table height="30">
					  <tr>
						<td></td>
					  </tr>
					</table>    
				 </div>
				  
				 
				  <div id="tr">
					<div class="conhx"></div>
				 </div>
			</div>
		</div>
	</div> 
  
	<div class="container1">
		<div style="width:297px;margin-left:27px;">
			<p style='margin:0;padding:0;margin-left:2px;' class="content_14"><b><font size="-4">品名:<?php if ($this->_var['order']['gname'] == '猫爪蛋糕'): ?><font size="-2"><?php echo $this->_var['order']['gname']; ?>+花环</font><?php else: ?><?php echo $this->_var['order']['gname']; ?><?php endif; ?></font><b style='margin-left:65px;'><font size="-1"><?php echo $this->_var['print_sn']; ?>&nbsp;&nbsp;<?php echo $this->_var['pack']['route_name']; ?>-<?php echo $this->_var['pack']['turn']; ?>-<?php if ($this->_var['order']['ice_bag_num'] < 10): ?>0<?php endif; ?><?php echo $this->_var['order']['ice_bag_num']; ?></font></b></p>
			<div style='margin-left:2px;'>
			<b class="content_text"><font size="-4">配料：<?php echo $this->_var['order']['goods_pl']; ?></font></b><br/>
				致敏信息提示：本产品配料中含乳制品、小麦制品、蛋制品<?php if ($this->_var['order']['gname'] != '猫爪蛋糕'): ?>、杏仁制品<?php endif; ?>等。
			</div>
			<table cellspacing='0' cellpadding='0'style='margin:0;padding:0;' border='0' width="308">
				<tr>
					<td><b>执行标准:SB/T 10329-2000</b><br/>
						<b>食品生产许可证号:<br/>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;QS1113 2401 0589</b><br/>
						<b class="content_text"><font size="-4">保质期:一天(12小时内最佳)<br/>
						储存条件：0~4℃冷藏<br/>
						质量等级:合格品<br/>
						加工方式:冷加工&nbsp;<br/>
						食用方法:<?php echo $this->_var['order']['goods_eat']; ?> <br/>
						生产日期: <?php echo $this->_var['order']['end_time']; ?>	<br/>
						服务热线: 4000 600 700
						</font></b>
					</td>
					<td style="padding-left:0px;">
						<div class="left-content-text"  >
							<div class="right-title"><font size="-2"><b>营养成分表</b></font></div>
							<!-- <div class="hx3"></div> -->
							<div style="border:1px solid black;">
								<div class="xm "><font size="-3">项目</font></div><div class="mk right-title"><font size="-3">每100g</font></div><div class="bf right-title"><font size="-3">NRV%</font></div>
								<div style="clear:both"></div>
								<div style="border-bottom:1px #000000 solid;margin:2px 0;"></div>
								<div class="xm right-content" ><font size="-2">能量</font></div><div class="mk right-content"><font size="-2"><?php echo $this->_var['order']['energy_g']; ?>KJ</font></div><div class="bf right-content"><?php echo $this->_var['order']['energy_r']; ?>%</div><div style="clear:both"></div>
								<div class="xm right-content" ><font size="-2">蛋白质</font></div><div class="mk right-content" ><font size="-2"><?php echo $this->_var['order']['protein_g']; ?>g</font></div><div class="bf right-content"><?php echo $this->_var['order']['protein_r']; ?>%</div><div style="clear:both"></div>
								<div class="xm right-content" ><font size="-2">脂肪</font></div><div class="mk right-content" ><font size="-2"><?php echo $this->_var['order']['fat_g']; ?>g</div><div class="bf right-content"><?php echo $this->_var['order']['fat_r']; ?>%</div><div style="clear:both"></div>
								<div class="xm right-content" style="width:80px" ><font size="-2">-反式脂肪（酸）</font></div><div class="mk right-content" style="width:22px" ><font size="-2">0g</font></div><div style="clear:both"></div>
								
								<div class="xm right-content" ><font size="-2">碳水化合物</font></div><div class="mk right-content" ><font size="-2"><?php echo $this->_var['order']['carb_g']; ?>g</font></div><div class="bf right-content"><?php echo $this->_var['order']['carb_r']; ?>%</div><div style="clear:both"></div>
								<div class="xm right-content"  ><font size="-2">钠</font></div><div class="mk right-content" ><font size="-2"><?php echo $this->_var['order']['na_g']; ?>mg </font></div><div class="bf right-content"><font size="-2"><?php echo $this->_var['order']['na_r']; ?>%</font></div>
								<div style="clear:both"></div>
								<!-- <div class="hx3" style='margin:0;padding:0;'></div>	 -->
							</div>	
						</div>	
					</td>
				</tr>
				<tr>
					<td style="vertical-align:top;width:134px;">
						公司网址：www.mescake.com<br/>
						本品仅在MES每实官网售卖<br/>
						<?php if ($this->_var['order']['gname'] == '猫爪蛋糕'): ?>
						<div style="font-size:18px;">净含量:230g</div>
						<?php elseif ($this->_var['order']['weight'] == "454g"): ?>
						<div style="font-size:18px;">净含量:<?php echo $this->_var['order']['weight']; ?></div>
						<?php elseif ($this->_var['order']['weight'] == "908g"): ?>
						<div style="font-size:18px;">净含量:<?php echo $this->_var['order']['weight']; ?></div>
						<?php else: ?>
						<div style="font-size:21px;">净含量:<?php echo $this->_var['order']['weight']; ?></div>
						<?php endif; ?>
					</td>
					<td>
						<table cellspacing='0' cellpadding='0'>
							<tr>
								<td>
									<p class="content_text"><font size="-4">
									生产商:北京艾平弗雷食品有限公司<br/>
									生产地:北京市顺义区李桥镇李桥西<br/>路1号<br/>
									电话：010-61497366<br/>
									产地：北京市•顺义区 
									</font></p>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
	