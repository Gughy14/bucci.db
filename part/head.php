<?php
	//Definizione delle costanti
	define('KB', 1024);
	define('MB', 1048576);
	define('GB', 1073741824);
	
	//Configurazioni
	$chiave = "chiave";
	$max_att_size = 100*MB;
	$dbserver = "CORE-CJ84\sqlexpress";
	$pass_data = json_decode(file_get_contents('conf/pass.json'), true);
	$dbdata = $pass_data['dbmaster'];
	$anno0 = 1946;
	
	//Allegati Inseribili - Formato "nome_file" => "Etichetta nei moduli" (ANCORA DA ESEGUIRE L'AGGIORNAMENTO DEL DATABASE!)
	$allegabili = array(
											"pratica" => "Pratica Completa" ,
											"presentazione" => "Modulo di Presentazione" ,
											"inizio_lavori" => "Comunicazione Inizio lavori" ,
											"relazione_tec" => "Relazione Tecnica" ,
											"rilascio" => "Documento di Rilascio"
											);
											
	//Nomi delle pagine - Formato "nome_file" => "Nome mostrato in intestazione"
	$pagine = array(
									"index.php" => "Database Atti Edilizi" ,
									"cerca.php" => "Ricerca Atti Edilizi" ,
									"inserisci.php" => "Inserisci Atto Edilizio" ,
									"atto.php" => "Visualizzazione Atto edilizio "
									);
	
	//Messaggi di errore (Da risistemare)
	$link_err = "Errore durante la connessione al database: controllare i parametri!";
	$display_err = "Errore durante il ricevimento dei dati.";
	$att_err = "Errore durante l'elaborazione degli allegati";
	$null_act = "Il numero di atto non può essere omesso";
	$hash_err = "Errore durante la comparazione dell'hash";
	$loc_err = "Errore durante l'inserimento dei dati di localizzazione";
	$mkdir_failed = "Si è verificato un errore durante la creazione della cartella in archivio";
	$size_err = "Impossibile caricare il file poiché di dimensioni superiori a ".str_replace('*','',$max_att_size)." bytes.";
	$index_err = "Si è verificato un errore durante la creazione della copertina della pratica";
	$search_err = "Errore durante la ricerca dei dati.";
	$unique_err = "Il numero di atto specificato è già registrato nel database";
	
	//Inizia la sessione
	session_start();
?>
<!DOCTYPE html>
<html lang="it-IT">
	<head>
		<!-- Titolo e icona -->
		<title>DB v1.0.0</title>
		<link rel="icon" href="/imgs/icon.png" sizes="32x32">
		
		<!-- Metadati -->
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta name="author" content="Guglielmone Fabio">
		<meta name="keywords" content="Edilizia, Database Pratiche, Comune Buccinasco">
		<meta name="description" content="Sistema di archivio atti edilizi del Comune di Buccinasco">
		
		<!-- Stili Globali -->
		<link href="NULL" rel="stylesheet" id="theme">
		<link href="/css/jquery-ui.css" rel="stylesheet">
		<link href="/css/stili.css" rel="stylesheet">
		
		<!-- Fonts Globali -->
		<link href="/css/fonts.css" rel="stylesheet">
		
		<!-- Script Globali -->
		<script src="/js/jquery-1.12.4.js" type="application/javascript"></script>
		<script src="/js/bootstrap.js" type="application/javascript"></script>
		<script src="/js/tema.js" type="application/javascript"></script>
		