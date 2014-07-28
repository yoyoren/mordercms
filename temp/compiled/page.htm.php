      <div id="turn-page" style="float:left;">
        总记录数有： <span id="totalRecords"><?php echo $this->_var['record_count']; ?></span>
        条，共有： <span id="totalPages"><?php echo $this->_var['page_count']; ?></span>
        页，当前第<span id="pageCurrent"><select id="gotoPage" onchange="listTable.gotoPage(this.value)">
            <?php echo $this->smarty_create_pages(array('count'=>$this->_var['page_count'],'page'=>$this->_var['filter']['page'])); ?>
          </select></span>
        页
	  </div>
	  <div align="center" style="float:left; width:300px;">
	  <?php if ($this->_var['filter']['money_get']): ?>共计：<?php echo $this->_var['filter']['money_get']; ?>元&nbsp;&nbsp;<?php endif; ?>
	  <?php if ($this->_var['filter']['card_get']): ?>共计：<?php echo $this->_var['filter']['card_get']; ?>张卡券&nbsp;&nbsp;<?php endif; ?>
	  </div>
	  <?php if ($this->_var['page_count'] > 1): ?>
      <div style="float:right;font-size:12px;">
          <a class="abtn" href="javascript:listTable.gotoPageLast()">最末页</a>
          <a class="abtn" href="javascript:listTable.gotoPageNext()">下一页</a>
          <a class="abtn" href="javascript:listTable.gotoPagePrev()">上一页</a>
          <a class="abtn" href="javascript:listTable.gotoPageFirst()">第一页</a>
      </div><?php endif; ?>
