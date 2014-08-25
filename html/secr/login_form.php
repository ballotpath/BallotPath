  <form method="post">
	<div class="intro-header">
        <div class="container">
			<div	class="panel panel-default" >
				<div class="panel-heading">
					<h3 class="panel-title">Please enter password to access this page</h3>
				</div>
				<div  class="panel-body" >
					<font color="red"><?php echo $error_msg; ?></font><br />
					<?php if (USE_USERNAME) echo 'Login:<br /><input type="input" name="access_login" /><br />Password:<br />'; ?>
					<input type="password" name="access_password" /><p></p><input type="submit" name="Submit" value="Submit" />
</form>
					<a href="secr/reminder.php">Password Reminder</a><br><br>
					<br />
					<a style="font-size:9px; color: #B0B0B0; font-family: Verdana, Arial;" href="http://www.zubrag.com/scripts/password-protect-advanced.php" title="Download Password Protect">Powered by Password Protect</a>
				</div>
			</div>
		</div>