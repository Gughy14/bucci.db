<!DOCTYPE html>
<html>
<head>
<title>Your Home Page</title>
<link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="profile">
<b id="welcome">Welcome : <i><?php echo $login_session; ?></i></b>

<?php

echo($_SESSION['livello']);

  if(isset($_SESSION['livello'])){
		if($_SESSION['livello'] === 0){
			print("SONO UN ADMIN");
		}else{
			print("Non sono un admin");
		}
	}else{
		print("Non sono loggato");
	}


?>



<b id="logout"><a href="logout.php">Log Out</a></b>
</div>
</body>
</html>