<?php
	//CONFIGURAZIONI
	$dbserver = "CORE-CJ84\sqlexpress";
	$ID = $_GET['id'];
	
	//MESSAGGI DI ERRORE
	$link_err = "Errore durante la connessione al database: controllare i parametri!";
	$display_err = "Errore durante il ricevimento dei dati.";
	$att_err = "Errore durante il ricevimento dei dati.";
	
	//Ottenimento credenziali da JSON
	$pass_json = file_get_contents('conf/pass.json');
	$pass_data = json_decode($pass_json, true);
	
	$dbdata = $pass_data['dbmaster'];
	
	//STABILIMENTO CONNESSIONE AL DATABASE
	$link = sqlsrv_connect($dbserver, $dbdata);
	if($link === false){
		die('<script>alert("'.$link_err.'")</script>');
	}
	
	//Query di restituzione dati
	$display_query = "SELECT *
										FROM pratica.dat
										JOIN pratica.loc
										ON loc = locID
										WHERE pratica.dat.ID = ".$ID;
	
	//Esecuzione restituzione dati
	$display_stmt = sqlsrv_query($link, $display_query);
	if($display_stmt === false){
		die('<script>alert("'.$display_err.'")</script>');
	}
	
	//Trascrizione variabili dati
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
		$timestamp = $row['timestamp'];
	}
	
	//Definizione Variabili
	$num_noslash = str_replace('/','.',$numero);
	$anno_presentazione = $data_presentazione->format("Y");
	if($anno_presentazione = 0001){$anno_presentazione = "Non datati";}
	$percorso = "../atti/".$anno_presentazione."/".$atto."_".$num_noslash."/";
	
	//Elaborazione e assegnazione valori da stampare per proprietà
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
	
	//Elaborazione e assegnazione valori da stampare per dati catastali
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

	//Query di restituzione estensioni allegati
	$att_query = "SELECT *
								FROM pratica.att
								WHERE attID = ".$attID;

	//Esecuzione restituzione estensioni allegati
	$att_stmt = sqlsrv_query($link, $att_query);
	if($att_stmt === false){
		die('<script>alert("'.$att_err.'")</script>');
	}
	
	//Creazione array di estensione
	while($atts = sqlsrv_fetch_array($att_stmt, SQLSRV_FETCH_ASSOC)){
		
		//Creazione array di presenza
		$vals['attID'] = 0;
		
		//Elaborazione ricorsiva dell'array di estensione
		foreach($atts as $key => $att){
			//Esclude il risultato numerico dell'ID
			if(!is_numeric($att)){
				//Attribuisce il valore 0 se nullo
				if(is_null($att)){
					$vals[$key] =  0;
				//In caso contrario attribuisce il valore 1
				}else{
					$vals[$key] = 1;
				}
			}
		}
		
		//Assegna all'array file il valore da stampare per ogni allegato
		$files['void'] = "<a href='/../imgs/void_key.png'>There's a void fissure!</a><br>";
		$files['presentazione'] = "<a href='".$percorso."presentazione.".$atts['presentazione']."' target='_blank'>Modulo di presentazione</a><br>";
		$files['inizio_lavori'] = "<a href='".$percorso."inizio_lavori.".$atts['inizio_lavori']."' target='_blank'>Comunicazione inizio lavori</a><br>";
		$files['relazione_tec'] = "<a href='".$percorso."relazione_tec.".$atts['relazione_tec']."' target='_blank'>Relazione Tecnica</a><br>";
		$files['rilascio'] = "<a href='".$percorso."rilascio.".$atts['rilascio']."' target='_blank'>Rilascio</a><br>";
		
		//Associa il valore da stampare alla presenza dell'allegato
		$allegati = array_combine($files, $vals);
		
	
		//================================//
		//===      STAMPA PAGINA       ===//
		//================================//
		
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
		
		//Verifica la presenza degli allegati
		if(count(array_unique($vals)) !== 1){
			//Se presente stampa la stringa attribuita per ogni file
			foreach($allegati as $file => $val){
				if($val==1){
					echo($file);
				}
			}
		//In caso non ci siano file restituisce il seguente
		}else{
			echo("Nessun allegato presente");
		}
		
		//Stampa la chiusura della tabella allegati
		echo("
					</div>
				</div>
			</div>
		</div>
		");
		
		//Include pié di pagina
		include 'D:/web/footer.php';
	}
?>