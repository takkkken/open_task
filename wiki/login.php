<?php include('language.php'); ?>
<div class="header"><?php echo $language->menu->login; ?></div>

<div class="label" id="username_email"><?php echo $language->username; ?>:</div>
<div class="field"><input name="user" id="login_user" /></div>

<div class="clear"></div>

<div id="forgotpassword">
    <div class="label"><?php echo $language->password; ?>:</div>
    <div class="field"><input type="password" name="pass" /></div>
</div>

<div style="height:20px;clear:both;"></div>
<div style="text-align:center;">
<input id="loginmode" type="hidden" name="mode" value="login" />
    <input id="loginsubmit" type="submit" value="<?php echo $language->menu->login; ?>"/>

    <div id="forgotpasslink"><a href="javascript:forgotlink();">Forgot Password?</a></div>
</div>


