  <form method="post">
    <h3>Please enter password to access this page</h3>
    <font color="red"><?php echo $error_msg; ?></font><br />
<?php if (USE_USERNAME) echo 'Login:<br /><input type="input" name="access_login" /><br />Password:<br />'; ?>
    <input type="password" name="access_password" /><p></p><input type="submit" name="Submit" value="Submit" />
  </form>
  <br />
  <a style="font-size:9px; color: #B0B0B0; font-family: Verdana, Arial;" href="http://www.zubrag.com/scripts/password-protect-advanced.php" title="Download Password Protect">Powered by Password Protect</a>
