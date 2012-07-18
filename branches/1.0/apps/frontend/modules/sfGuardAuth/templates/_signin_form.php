<?php use_helper('I18N') ?>
<div id='login_form'>
<form action="<?php echo url_for('@sf_guard_signin') ?>" method="post">
  <div id='notify'>
	<?php if ($form['username']->hasError() || $form['password']->hasError()) echo "Tên đăng nhập hoặc mật khẩu không đúng." ; ?>
  </div>
  <input type='text' name='signin[username]' id='username' value='Tên đăng nhập' onFocus="(this.value=='Tên đăng nhập')?this.value='':''" onBlur="(this.value=='')?this.value='Tên đăng nhập':''" />	
  <input type='password' name='signin[password]' id='password' />
  <div id='wrap_remember'>
	  <input type='checkbox' name='signin[remember]' id='remember' /> <label for="remember">Lưu mật khẩu</label>
  </div>
  <?php
  echo $form['_csrf_token'];
  ?>
  <input type='submit' style='' value='' />
  <table>
    <tbody>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php $routes = $sf_context->getRouting()->getRoutes() ?>
          <?php if (isset($routes['sf_guard_forgot_password'])): ?>
            <a href="<?php echo url_for('@sf_guard_forgot_password') ?>"><?php echo __('Forgot your password?', null, 'sf_guard') ?></a>
          <?php endif; ?>

          <?php if (isset($routes['sf_guard_register'])): ?>
            &nbsp; <a href="<?php echo url_for('@sf_guard_register') ?>"><?php echo __('Want to register?', null, 'sf_guard') ?></a>
          <?php endif; ?>
        </td>
      </tr>
    </tfoot>
  </table>
</form>
</div>