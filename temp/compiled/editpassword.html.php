<?php echo $this->fetch('header.html'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'utils.js,listtable.js,validator.js')); ?>
<div class="text_title">
<h3 style="float:left;display:inline;">--<?php echo $this->_var['ur_here']; ?></h3>
<?php if ($this->_var['action_link']): ?>
<div class="action-span"><a href="<?php echo $this->_var['action_link']['href']; ?>"><?php echo $this->_var['action_link']['text']; ?></a></div>
<?php endif; ?>
<div style="clear:both"></div>
</div>
<div class="list-div">
<form name="theForm" method="post" action="index.php?act=saveedit" enctype="multipart/form-data" onsubmit="return validate();">
<table width="100%" bgcolor="#ffffff">
 <tr>
    <td class="label">用户名</td>
    <td>
      <?php echo htmlspecialchars($this->_var['user']['user_name']); ?></td>
  </tr>
  <tr>
    <td class="label">真实姓名</td>
    <td>
     <?php echo htmlspecialchars($this->_var['user']['sname']); ?></td>
  </tr>

  <tr>
    <td class="label">旧密码</td>
    <td>
      <input type="password" name="old_password" size="34" /><?php echo $this->_var['lang']['require_field']; ?>
  </tr>
  <tr>
    <td class="label">新密码</td>
    <td>
      <input type="password" name="new_password" maxlength="32" size="34" /><?php echo $this->_var['lang']['require_field']; ?></td>
  </tr>
  <tr>
    <td class="label">确认密码</td>
    <td>
      <input type="password" name="pwd_confirm" value="" size="34" /><?php echo $this->_var['lang']['require_field']; ?></td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <input type="submit" value="修改密码" name="sub" class="button" />&nbsp;&nbsp;&nbsp;
      <input type="reset" value="重置" class="button" />
  </tr>
</table>
</form>
</div>
<script language="JavaScript"><!--
var action = "edit";


/**
* 检查表单输入的数据
*/
function validate()
{
  validator = new Validator("theForm");
  validator.password = function (controlId, msg)
  {
    var obj = document.forms[this.formName].elements[controlId];
    obj.value = Utils.trim(obj.value);
    if (!(obj.value.length >= 6 && /\d+/.test(obj.value) && /[a-zA-Z]+/.test(obj.value)))
    {
      this.addErrorMsg(msg);
    }

  }
  validator.required('old_password','旧密码不能为空');
  validator.eqaul("new_password", "pwd_confirm", '两次密码不相等');

  return validator.passed();
}


--></script>
<?php echo $this->fetch('footer.html'); ?>