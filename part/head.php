<!DOCTYPE html>
<html lang="it">
<head>
	<title>DB v0.3.1</title>
	
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
		
		//Configurazioni
		$chiave = "chiave";
		$max_att_size = 10*MB;
		$dbserver = "CORE-CJ84\sqlexpress";
		$pass_data = json_decode(file_get_contents('conf/pass.json'), true);
		$dbdata = $pass_data['dbmaster'];
		$anno0 = 1946;
		
		//ALLEGATI INSERIBILI
		$allegabili = array(
												"presentazione" => "Modulo di Presentazione" ,
												"inizio_lavori" => "Comunicazione Inizio lavori" ,
												"relazione_tec" => "Relazione Tecnica" ,
												"rilascio" => "Documento di Rilascio"
												);
		
		//Messaggi di errore
		$link_err = "Errore durante la connessione al database: controllare i parametri!";
		$display_err = "Errore durante il ricevimento dei dati.";
		$att_err = "Errore durante l'elaborazione degli allegati";
		$null_act = "Il numero di atto non può essere omesso";
		$hash_err = "Errore durante la comparazione dell'hash";
		$loc_err = "Errore durante l'inserimento dei dati di localizzazione";
		$mkdir_failed = "Si è verificato un errore durante la creazione della cartella in archivio";
		$size_err = "Impossibile caricare il file poiché di dimensioni superiori a ".str_replace('*','',$max_att_size)." bytes: ";
		$index_err = "Si è verificato un errore durante la creazione della copertina della pratica";
		$search_err = "Errore durante la ricerca dei dati.";
		
		//Inizia la sessione
		session_start();


	?>