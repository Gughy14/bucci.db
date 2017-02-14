<form method="post">
	<input name="pass" id="pass">
	<input type="submit" name="hash-submit" value="hash">
</form>
<form method="post">
	<input name="loc" id="loc">
	<input type="submit" name="md5-submit" value="MD5">
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
	if(isset($_POST['md5-submit'])){
		if(!empty($_POST['loc'])){
			print(md5($_POST['loc']));
		}
	}
	if(isset($_POST['session-submit'])){
		session_start();
		session_destroy();
	}
?>