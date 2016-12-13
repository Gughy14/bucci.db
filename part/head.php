<!DOCTYPE html>
<html lang="it">
<head>
    <title>DB v0.2.0</title>
	
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="keywords" content="Atti Edilizi, Pratiche, Archivio, DB, Database">
	<meta name="description" content="Sistema di archivio atti edilizi del Comune di Buccinasco">
	<meta name="author" content="Guglielmone Fabio">
	
	<link rel="icon" href="/imgs/icon.png" sizes="32x32"/>
	
	<!-- Fogli di stile-->
	<link href="/css/bootstrap.min.css" rel="stylesheet">
	<link href="/css/jquery-ui.css" rel="stylesheet">
	<link href="/css/font-awesome.min.css" rel="stylesheet">
	<link href="/css/stylesheet.css" rel="stylesheet">
	
	<!-- Script -->
	<script src="/js/jquery-1.12.4.js"></script>
	<script src="/js/jquery-ui.js"></script>
	<script src="/js/bootstrap.min.js"></script>
	
	<?php
		//Definizione delle costanti
		define('KB', 1024);
		define('MB', 1048576);
		define('GB', 1073741824);
		
		//File di Configurazione
		$dbserver = "CORE-CJ84\sqlexpress";
		$pass_data = json_decode(file_get_contents('conf/pass.json'), true);
		
		//Messaggi di errore
		$link_err = "Errore durante la connessione al database: controllare i parametri!";
		
		//Inizia la sessione
		session_start();


	?>