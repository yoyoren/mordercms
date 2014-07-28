<?php echo $this->fetch('header.html'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'datepicker/WdatePicker.js')); ?>
<div class="text_title">
<h3 style="float:left;display:inline;">--<?php echo $this->_var['ur_here']; ?></h3>
<?php if ($this->_var['action_link']): ?>
<div class="action-span"><a href="<?php echo $this->_var['action_link']['href']; ?>"><?php echo $this->_var['action_link']['text']; ?></a></div>
<?php endif; ?>
<div style="clear:both"></div>
</div>

<div class="form-div">
    
    <form name="form1" action="" method="get">
    <div id="search_box" class="ziti1">
    送货日期：
 <input name="datetime" type="text" id="sdate" value="<?php echo $this->_var['datetime']; ?>" onClick="javascript:WdatePicker()" readonly="true" size="10" />
    订单号：<input type="text" name="order_sn" value="<?php echo $this->_var['order_sn']; ?>" />
    流水号：<input size="6" type="text" name="print_sn" value="<?php echo $this->_var['print_sn']; ?>" maxlength="4" />
    <input type="submit" name="sub" class="button" value="查询" /> <input type="hidden" value="query" name="act" />
     </div>
     </form>
    
    
</div>

    <div class="list-div" id="listDiv">
   	  <table width="100%" border="0" cellspacing="1" cellpadding="5">
<tr  class="ziti1" >
    			<th >送货日期</th>
    			<th>订单号</th>
    			<th>流水号</th>
    			<th>配送打印时间</th>
    			<th>生产打印时间</th>
  			</tr>
  			<tr bgcolor="#ffffff" align="center">
   			  <td><?php echo $this->_var['arr']['best_time']; ?></td>
   			  <td><?php echo $this->_var['arr']['order_sn']; ?></td>
   			  <td><?php echo $this->_var['arr']['print_sn']; ?></td>
   			  <td><?php echo $this->_var['arr']['ptime']; ?></td>
   			  <td><?php echo $this->_var['arr']['stime']; ?></td>
  			</tr>
		</table>
		<table width="100%" border="0" cellspacing="1" cellpadding="5">
			<tr  >
            	<th colspan="4" class="ziti3">基本信息</th>
            </tr>
            <?php if ($this->_var['arr']): ?>
            <tr bgcolor="#FFFFFF">
            	<td width="7%" align="right" class="ziti2" >工号</td>
                <td colspan="3" class="ziti1"><?php if ($this->_var['arr']['remark'] == '9999'): ?>web<?php else: ?><?php echo $this->_var['arr']['remark']; ?><?php endif; ?></td>
          </tr>
            <tr bgcolor="#FFFFFF">
           	  <td align="right" class="ziti2">订单号</td>
                <td width="18%" class="ziti1"><?php echo $this->_var['arr']['order_sn']; ?></td>
              <td width="7%" align="right" class="ziti2">订单状态</td>
                <td class="ziti1">
                <?php if ($this->_var['arr']['order_status'] == '0'): ?>
                	未确认
                <?php elseif ($this->_var['arr']['order_status'] == '1'): ?>
                	已确认
                <?php elseif ($this->_var['arr']['order_status'] == '2'): ?>
                	取消
                <?php elseif ($this->_var['arr']['order_status'] == '3'): ?>
                	无效
                <?php elseif ($this->_var['arr']['order_status'] == '4'): ?>
                	退订
                <?php endif; ?>
                <?php if ($this->_var['arr']['pay_status'] == '0'): ?>
                	未付款
                <?php elseif ($this->_var['arr']['pay_status'] == '1'): ?>
                	付款中
                <?php elseif ($this->_var['arr']['pay_status'] == '2'): ?>
                	已付款
                <?php endif; ?>
                </td>
          </tr>
            
            <tr bgcolor="#FFFFFF">
           	  <td align="right" class="ziti2">订货人</td>
                <td class="ziti1"><?php echo $this->_var['arr']['orderman']; ?></td>
                <td align="right" class="ziti2">下单时间</td>
                <td class="ziti1"><?php echo date("Y-m-d H:i:s",$this->_var['arr']['time1']); ?></td>
          </tr>
            <tr bgcolor="#FFFFFF">
           	  <td align="right" class="ziti2">支付方式</td>
                <td class="ziti1"><?php echo $this->_var['arr']['pay_name']; ?></td>
                <td align="right" class="ziti2">送货时间</td>
                <td class="ziti1"><?php echo $this->_var['arr']['best_time']; ?></td>
          </tr>
            <tr bgcolor="#FFFFFF">
           	  <td align="right" class="ziti2">收货人</td>
              <td colspan="3" class="ziti1"><?php echo $this->_var['arr']['consignee']; ?></td>
          </tr>
            <tr bgcolor="#FFFFFF">
           	  <td align="right" class="ziti2">送货地址</td>
              <td colspan="3" class="ziti1"><?php echo $this->_var['arr']['address']; ?></td>
          </tr>
            <tr bgcolor="#FFFFFF">
           	  <td align="right" class="ziti2">收款地址</td>
              <td colspan="3" class="ziti1"><?php echo $this->_var['arr']['money_address']; ?></td>
          </tr>
            <tr bgcolor="#FFFFFF">
           	  <td align="right" class="ziti2">批次</td>
                <td class="ziti1">
                	<?php if ($this->_var['arr']['turn']): ?>
                	第<?php echo $this->_var['arr']['turn']; ?>批
                	<?php endif; ?>
                </td>
                <td align="right" class="ziti2">配送站</td>
                <td class="ziti1">
                <?php if ($this->_var['arr']['station_name']): ?>
                <?php echo $this->_var['arr']['station_name']; ?>-<?php echo $this->_var['arr']['route_code']; ?>
                <?php endif; ?>
                </td>
          </tr>
            <tr bgcolor="#FFFFFF">
           	  <td align="right" class="ziti2">调度状态</td>
                <td class="ziti1">
                <?php if ($this->_var['arr']['sta1'] == '0'): ?>
                	调度中
                <?php elseif ($this->_var['arr']['sta1'] == '1'): ?>
                	完成
                <?php endif; ?>
                </td>
                <td align="right" class="ziti2">调度时间</td>
                <td class="ziti1">
                <?php if ($this->_var['arr']['sta1'] == '1'): ?>
                	<?php echo $this->_var['arr']['time2']; ?>         
                <?php endif; ?>
                </td>
          </tr>
            <tr bgcolor="#FFFFFF">
           	  <td align="right" class="ziti2">商品</td>
              <td colspan="3" class="ziti1"><?php echo $this->_var['arr2']['0']['goods_name']; ?></td>
          </tr>
            <tr bgcolor="#FFFFFF">
           	  <td align="right" class="ziti2">餐具</td>
                <td class="ziti1"><?php echo $this->_var['c_num']; ?></td>
                <td align="right" class="ziti2">蜡烛</td>
                <td class="ziti1"><?php echo $this->_var['l_num']; ?></td>
          </tr>
            <tr bgcolor="#FFFFFF">
           	  <td align="right" class="ziti2">生产提示</td>
                <td class="ziti1"><?php echo $this->_var['arr']['scts']; ?></td>
                <td align="right" class="ziti2">外送提示</td>
                <td class="ziti1"><?php echo $this->_var['arr']['wsts']; ?></td>
          </tr>
            <tr bgcolor="#FFFFFF">
           	  <td align="right" class="ziti2">抬头</td>
                <td class="ziti1"><?php echo $this->_var['arr']['inv_payee']; ?></td>
                <td align="right" class="ziti2">内容</td>
                <td class="ziti1"><?php echo $this->_var['arr']['inv_content']; ?></td>
          </tr>
            <tr bgcolor="#FFFFFF">
           	  <td align="right" class="ziti2">客户要求</td>
              <td colspan="3" class="ziti1"><?php echo $this->_var['arr']['postscript']; ?></td>
          </tr>
            <tr bgcolor="#FFFFFF">
           	  <td align="right" class="ziti2">生日牌</td>
                <td class="ziti1"><?php echo $this->_var['arr']['card_name']; ?></td>
                <td align="right" class="ziti2">内容</td>
                <td class="ziti1"><?php echo $this->_var['arr']['card_message']; ?></td>
          </tr>
          <?php else: ?>
          </tr>
            <tr bgcolor="#FFFFFF">
           	  <td  colspan="4" class="ziti2">无记录</td>
          </tr>
          <?php endif; ?>

        </table>
  </div>
    

</body>
</html>