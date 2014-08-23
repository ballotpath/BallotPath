<form method="post">
	<div class="intro-header">
		<div class="container">
			<div	class="panel panel-default" >
				<div class="panel-heading">
					<h3 class="panel-title">Password Reminder</h3>
				</div>
				<div  class="panel-body" >
					<font color="red"><?php echo $this->error; ?></font><br />
					Login:<br /><input type='input' name='access_login' />
					<p></p>
					<input type="submit" name="access_submit" value="Proceed" />


				</div>
			</div>
		</div>
	</div>
</form>