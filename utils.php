<form method="post">
	<label>Password hash</label>
	<input name="pass" id="pass">
	<input type="submit" name="hash-submit">
</form>
<form method="post">
	<label>Session Destroy</label>
	<input type="submit" name="session-submit">
</form>
<?php
	//Password Hash
	if(isset($_POST['hash-submit'])){
		if(!empty($_POST['pass'])){
			print(password_hash($_POST['pass'], PASSWORD_BCRYPT));
		}
	}
	if(isset($_POST['session-submit'])){
		session_start();
		session_destroy();
	}
?>