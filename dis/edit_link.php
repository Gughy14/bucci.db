<?php
	//Credenziali e dati di accesso
	$hostname = "localhost";
	$utente = "edit_edilizia";
	$password = "V)PwA'MvY].YPJm]8-6}'ZH2";
	$database = "edilizia";
	$tabella = "atti";
	
	//Avvio della connessione al server SQL
	$link = mysql_connect($hostname, $utente, $password);
	if(!$link){die("Impossibile connettersi a " . $hostname . ": " . mysql_error());}
	
	//Selezione del database SQL
	$db_selected = mysql_select_db($database, $link);
	if(!$db_selected){die ("Impossibile utilizzare il database " . $database . ": " . mysql_error());}
?>