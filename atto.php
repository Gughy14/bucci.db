<?php
	//CONFIGURAZIONI
	$dbserver = "CORE-CJ84\sqlexpress";
	$dbdata = array( "Database"=>"edilizia", "UID"=>"dbmaster", "PWD"=>"6X!PdYncts#n%-jP2PxR4wBN" );
	
	//MESSAGGI DI ERRORE
	$link_err = "Errore durante la connessione al database: controllare i parametri!";
	$display_err = "Errore durante il ricevimento dei dati.";
	$att_err = "Errore durante il ricevimento dei dati.";
	
	//STABILIMENTO CONNESSIONE AL DATABASE
	$link = sqlsrv_connect($dbserver, $dbdata);
	if($link === false){
		die('<script>alert("'.$link_err.'")</script>');
	}
	
	$display_query = "SELECT *
										FROM pratica.dat
										JOIN pratica.loc
										ON loc = locID
										WHERE pratica.dat.ID = ".$ID;
	
	//Esecuzione inserimento loc
	$display_stmt = sqlsrv_query($link, $display_query);
	
	if($display_stmt === false){
		die('<script>alert("'.$display_err.'")</script>');
	}
	
	while($row = sqlsrv_fetch_array($display_stmt, SQLSRV_FETCH_ASSOC)){
		$atto = $row['atto'];
		$numero = $row['numero'];
		$data_presentazione = $row['data_presentazione'];
		$data_rilascio = $row['data_rilascio'];
		$nome = $row['nome'];
		$cognome = $row['cognome'];
		$societa = $row['societa'];
		$subalterno = $row['subalterno'];
		$oggetto = $row['oggetto'];
		$indirizzo = $row['indirizzo'];
		$civico = $row['civico'];
		$foglio = $row['foglio'];
		$mappale = $row['mappale'];
		$attID = $row['att'];
	}
		
	//Valori da stampare per proprietà;
	if($cognome === 'X' AND $nome === 'X' AND $societa === 'X'){
		$proprieta = "<span style='float: left'>Nessun riferimento di propriet&agrave; presente in database";
	}elseif($cognome !== 'X' AND $nome !== 'X' AND $societa === 'X'){
		$proprieta = "<span style='float: left'>Propriet&agrave;: </span><span style='float: right'><i class='fa fa-user' aria-hidden='true'></i> ".$cognome." ".$nome."</span>";
	}elseif($cognome !== 'X' AND $nome === 'X' AND $societa !== 'X'){
		$proprieta = "<span style='float: left'>Propriet&agrave;: </span><span style='float: right'><i class='fa fa-user' aria-hidden='true'></i> ".$cognome." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <i class='fa fa-users' aria-hidden='true'></i> ".$societa."</span>";
	}elseif($cognome !== 'X' AND $nome === 'X' AND $societa === 'X'){
		$proprieta = "<span style='float: left'>Propriet&agrave;: </span><span style='float: right'><i class='fa fa-user' aria-hidden='true'></i> ".$cognome."</span>";
	}elseif($cognome === 'X' AND $nome === 'X' AND $societa !== 'X'){
		$proprieta = "<span style='float: left'>Propriet&agrave;: </span><span style='float: right'><i class='fa fa-users' aria-hidden='true'></i> ".$societa."</span>";
	}else{
		$proprieta = "<span style='float: left'>Propriet&agrave;: </span><span style='float: right'><i class='fa fa-user' aria-hidden='true'></i> ".$cognome." ".$nome." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <i class='fa fa-users' aria-hidden='true'></i> ".$societa."</span>";
	}
	
	//Valori da stampare per dati catastali
	if($foglio !== 0 AND $mappale !== 0 AND $subalterno !== 0){
		$catastali = "<span style='float: left'>Dati Catastali: </span><span style='float: right'>Foglio ".$foglio." Mappale ".$mappale." Subalterno ".$subalterno."</span>";
	}elseif($foglio !== 0 AND $mappale !== 0 AND $subalterno === 0){
		$catastali = "<span style='float: left'>Dati Catastali: </span><span style='float: right'>Foglio ".$foglio." Mappale ".$mappale."</span>";
	}elseif($foglio !== 0 AND $mappale === 0 AND $subalterno === 0){
		$catastali = "<span style='float: left'>Dati Catastali: </span><span style='float: right'>Foglio ".$foglio."</span>";
	}elseif($foglio === 0 AND $mappale === 0 AND $subalterno === 0){
		$catastali = "<span style='float: left'>Nessun riferimento catastale presente in database";
	}else{
		$catastali = "<span style='float: left'>Riferimento di Foglio Mancante: </span><span style='float: right'>Mappale ".$mappale." Subalterno ".$subalterno."</span>";
	}

	$att_query = "SELECT *
								FROM pratica.att
								WHERE attID = ".$attID;
	
	$att_stmt = sqlsrv_query($link, $att_query);
	
	if($att_stmt === false){
		die('<script>alert("'.$att_err.'")</script>');
	}
	
	while($atts = sqlsrv_fetch_array($att_stmt, SQLSRV_FETCH_ASSOC)){
		
		$vals['attID'] = 0;
		
		foreach($atts as $key => $att){
			if(!is_numeric($att)){
				if(is_null($att)){
					$vals[$key] =  0;
				}else{
					$vals[$key] = 1;
				}
			}
		}

		//Valore da stampare per allegato

		$files['void'] = "<a href='/../imgs/void_key.png'>There's a void fissure!</a><br>";
		$files['presentazione'] = "<a href='presentazione.".$atts['presentazione']."' target='_blank'>Modulo di presentazione</a><br>";
		$files['inizio_lavori'] = "<a href='inizio_lavori.".$atts['inizio_lavori']."' target='_blank'>Comunicazione inizio lavori</a><br>";
		$files['relazione_tec'] = "<a href='relazione_tecnica.".$atts['relazione_tec']."' target='_blank'>Relazione Tecnica</a><br>";
		$files['rilascio'] = "<a href='rilascio.".$atts['rilascio']."' target='_blank'>Rilascio</a><br>";
		
		//Associazione chiave => valore
		$allegati = array_combine($files, $vals);
		
		/*===Stampa della pagina===*/
		
		//Include Intestazione HTML
		include 'D:/web/head.html';
		
		//Include barra superiore di navigazione
		include 'D:/web/topbar.html';
		
		//Intestazione della pratica
		echo("
		<div class='full-width' style='margin-top: 34px; background: #11283b; min-height: 190px;'>
			<div class='container' style='text-align: center;'>
				<h1 style='font-size: 60px; color: #f0f0f0;'>".$atto." &nbsp; ".$numero."</h1>
				<h4 style='color: #f0f0f0;'>".$oggetto."</h4>
			</div>
		</div>
		<div class='full-width' style='background: #38414A;'>&nbsp;</div>
		");
			
		//Dati della pratica
		print("
		<div class='full-width' style='background: #f0f0f0;'>
			<div class='container'>
				<div class='panel panel-default cover'>
					<div class='panel-heading'>
						<h3 class='panel-title'>Dati dell'atto</h3>
					</div>
					<div class='panel-body cover'>
						<h4>".$proprieta."</h4>
						<br>
						<h4><span style='float: left'>Indirizzo: </span><span style='float: right'>".$indirizzo.", ".$civico."</span></h4>
						<br>
						<h4>".$catastali."</h4>
					</div>
				</div>
			</div>
		</div>
		");
	
		//Allegati
		echo("
		<div class='full-width' style='background: #f0f0f0;'>
			<div class='container'>
				<div class='panel panel-default cover'>
					<div class='panel-heading'>
						<h3 class='panel-title'>Allegati</h3>
					</div>
				<div class='panel-body cover'>
		");
		
		if(count(array_unique($vals)) !== 1){
			foreach($allegati as $file => $val){
				if($val==1){
					echo($file);
				}
			}
		}else{
			echo("Nessun allegato presente");
		}
		
		echo("
					</div>
				</div>
			</div>
		</div>
		");
		
		//Include pié di pagina
		include 'D:/web/footer.html';
	}
?>