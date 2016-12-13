<?php
	//Carica la sessione
	session_start();
	
	//Distrugge la sessione
	session_destroy();
	
	//Reindirizza all'homepage
	header("Location: /index.php"); 
?>