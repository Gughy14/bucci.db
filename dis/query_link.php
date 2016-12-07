<?php
	//Credenziali e dati di accesso
	$hostname = "localhost";
	$utente = "query_edilizia";
	$password = "B2a?\Dwefe=b@TpUX5gC&Q)*";
	$database = "edilizia";
	$tabella = "atti";
	
	//Avvio della connessione al server SQL
	$link = mysql_connect($hostname, $utente, $password);
	if(!$link){die("Impossibile connettersi a " . $hostname . ": " . mysql_error());}
	
	//Selezione del database SQL
	$db_selected = mysql_select_db($database, $link);
	if(!$db_selected){die ("Impossibile utilizzare il database " . $database . ": " . mysql_error());}
?>