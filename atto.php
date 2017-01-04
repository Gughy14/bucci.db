<?php
	//Definisce la directory base del sito
	$path = $_SERVER['DOCUMENT_ROOT'];
	
	//Include Intestazione
	include $path.'/part/head.php';
	
	//IDENTIFICA L'ATTO MEDIANTE ID GET
	$ID = $_GET['id'];
		
	//Controllo livello di autorizzazione
	if(!isset($_SESSION['livello'])){
		//Codice di mancata autenticazione
		die("ACCESSO NEGATO");
	}
		
	//STABILIMENTO CONNESSIONE AL DATABASE
	$link = sqlsrv_connect($dbserver, $dbdata);
	if($link === false){
		die('<script>alert("'.$link_err.'")</script>');
	}
	
	//Query di restituzione dati
	$display_query = "SELECT *
										FROM pratica.dat
										JOIN pratica.loc
										ON locID = IDloc
										WHERE pratica.dat.ID = ".$ID;
	
	//Esecuzione restituzione dati
	$display_stmt = sqlsrv_query($link, $display_query);
	if($display_stmt === false){
		die('<script>alert("'.$display_err.'")</script>');
	}
	
	//Controlla se l'ID è registrato in database
	if(sqlsrv_has_rows($display_stmt) === false){
		die("ID inesistente");
	}
	
	//Trascrive in variabili i dati della pratica
	while($risultato = sqlsrv_fetch_array($display_stmt, SQLSRV_FETCH_ASSOC)){
		foreach($risultato as $campo => $valore){
			$$campo = $valore;
		}
	}
		
	//Elabora le variabili precedentemente ottenute
	$num_noslash = str_replace('/','.',$numero);
	$anno_presentazione = $data_presentazione->format("Y");
	if($anno_presentazione = 0001){$anno_presentazione = "Non datati";}
	$percorso = "../atti/".$anno_presentazione."/".$atto."_".$num_noslash."/";
		
	//Determina il formato da utilizzare per proprietà
	if($cognome === 'X' AND $nome === 'X' AND $societa === 'X'){
		$proprieta = "<div class='col-sm-12'>Nessun riferimento di propriet&agrave; presente in database</div>";
	}elseif($cognome !== 'X' AND $nome !== 'X' AND $societa === 'X'){
		$proprieta = "<div class='col-sm-6'>Propriet&agrave;</div><div class='col-sm-3'>".$cognome."</div><div class='col-sm-3'>".$nome."</div>";
	}elseif($cognome !== 'X' AND $nome === 'X' AND $societa !== 'X'){
		$proprieta = "<div class='col-sm-6'>Propriet&agrave;</div><div class='col-sm-3'>".$cognome."</div><div class='col-sm-3'>".$societa."</div>";
	}elseif($cognome !== 'X' AND $nome === 'X' AND $societa === 'X'){
		$proprieta = "<div class='col-sm-6'>Propriet&agrave;</div><div class='col-sm-6'>".$cognome."</div>";
	}elseif($cognome === 'X' AND $nome === 'X' AND $societa !== 'X'){
		$proprieta = "<div class='col-sm-6'>Propriet&agrave;</div><div class='col-sm-6'>".$societa."</div>";
	}else{
		$proprieta = "<div class='col-sm-6'>Propriet&agrave;</div><div class='col-sm-2'>".$cognome."</div><div class='col-sm-2'>".$nome."</div><div class='col-sm-2'>".$societa."</div>";
	}

	//Determina il formato da utilizzare per indirizzo
	if($indirizzo === 'X' AND $civico === 'X'){
		$stradali = "<div class='col-sm-12'>Nessun indirizzo presente in database</div>";
	}elseif($indirizzo !== 'X' AND $civico === 'X'){
		$stradali = "<div class='col-sm-6'>Indirizzo</div><div class='col-sm-6'>".$indirizzo."</div>";
	}else{
		$stradali = "<div class='col-sm-6'>Indirizzo</div><div class='col-sm-4'>".$indirizzo."</div><div class='col-sm-2'>Civico ".$civico."</div>";
	}
		
	//Determina il formato da utilizzare per dati catastali
	if($foglio !== 0 AND $mappale !== 0 AND $subalterno !== 0){
		$catastali = "<div class='col-sm-6'>Dati Catastali</div><div class='col-sm-2'>Foglio ".$foglio."</div><div class='col-sm-2'>Mappale ".$mappale."</div><div class='col-sm-2'>Subalterno ".$subalterno."</div>";
	}elseif($foglio !== 0 AND $mappale !== 0 AND $subalterno == 0){
		$catastali = "<div class='col-sm-6'>Dati Catastali</div><div class='col-sm-3'>Foglio ".$foglio."</div><div class='col-sm-3'>Mappale ".$mappale."</div>";
	}elseif($foglio !== 0 AND $mappale == 0 AND $subalterno == 0){
		$catastali = "<div class='col-sm-6'>Dati Catastali</div><div class='col-sm-6'>Foglio ".$foglio."</div>";
	}elseif($foglio == 0 AND $mappale !== 0 AND $subalterno !== 0){
		$catastali = "<div class='col-sm-6'>Riferimento di Foglio Mancante</div><div class='col-sm-3'>Mappale ".$mappale."</div><div class='col-sm-3'>Subalterno ".$subalterno."</div>";
	}elseif($foglio == 0 AND $mappale !== 0 AND $subalterno == 0){
		$catastali = "<div class='col-sm-6'>Riferimento di Foglio Mancante</div><div class='col-sm-6'>Mappale ".$mappale."</div>";
	}else{
		$catastali = "<div class='col-sm-12'>Nessun riferimento catastale presente in database</div>";
	}
		
	//Query di restituzione estensioni allegati
	$att_query = "SELECT *
								FROM pratica.att
								WHERE IDatt = ".$attID;

	//Esegue la restituzione delle estensioni degli allegati
	$att_stmt = sqlsrv_query($link, $att_query);
	if($att_stmt === false){
		die('<script>alert("'.$att_err.'")</script>');
	}
	
	//Elabora le estensioni ottenute
	while($allegato = sqlsrv_fetch_array($att_stmt, SQLSRV_FETCH_ASSOC)){
		
		//Aggira il problema della funzione seguente definendo la chiave void per l'ID SEMPRE = 0
		$presente['IDatt'] = 0;
		$files['void'] = "<a href='/imgs/void_key.png'>There's a void fissure!</a><br>";
		
		//Elabora in modo ricorsivo la lista di estensioni
		foreach($allegato as $tipo => $estensione){
			//Crea una variabile con il nome di ogni allegato contenente la propria estensione
			$$tipo = $estensione;		
			//Esclude il risultato numerico dell'ID
			if(!is_numeric($estensione)){
				//Attribuisce la linea da stampare per ogni allegato
				$files[$tipo] = "<a href='$percorso$tipo.$estensione' target='_blank'>$allegabili[$tipo]</a><br>";
				//Attribuisce il valore 0 se nullo
				if(is_null($estensione)){
					$presente[$tipo] =  0;
				//In caso contrario attribuisce il valore 1
				}else{
					$presente[$tipo] = 1;
				}
			}
		}
	}
	
	//Associa il valore da stampare alla presenza dell'allegato
	$allegati = array_combine($files, $presente);
	
	//================================//
	//===     ELIMINA PRATICA      ===//
	//================================//
	
	if(isset($_GET['mod']) && ($_GET['mod'] == "del") && ($_SESSION['livello'] == 1)){
	
		//Cancella i record dei dati
		$deldat_query = "DELETE FROM pratica.dat
										 WHERE ID=$ID";
		$deldat_stmt = sqlsrv_query($link, $deldat_query);
		if($deldat_stmt === false){
			die(print_r( sqlsrv_errors(), true));
		}		
		//Cancella i record degli allegati
		$delatt_query = "DELETE FROM pratica.att 
										 WHERE IDatt=$attID";
		$delatt_stmt = sqlsrv_query($link, $delatt_query);
		if($delatt_stmt === false){
			die(print_r( sqlsrv_errors(), true));
		}				 
		
		//Rimuove i files
		$files = glob(str_replace('..','D:',$percorso."*.*"));
		//Per ogni estensione che esiste
		foreach ($files as $file){
			//Elimina il file
			unlink($file);
		}
		//Rimuove la cartella
		rmdir(str_replace('..','D:',$percorso));
		
		//Reindirizza alla pagina di ricerca
		header("location: /cerca.php");
	}
	
	//================================//
	//===     MODIFICA TITOLO      ===//
	//================================//
	
	if(isset($_POST['tit-submit']) && isset($_GET['id'])){
		//Lettura dell'oggetto
		if(empty($_POST['oggetto'])){
			$oggetto = "Nessun Oggetto";
		}else{
			$oggetto = $_POST['oggetto'];
		}
		
	$tit_query = "UPDATE pratica.dat 
								SET oggetto=(?), editstamp=Getdate()
								WHERE ID=".$ID;
		
	$tit_params = array($oggetto);
	
	//Statement di inserimento dati
	$tit_stmt = sqlsrv_query($link, $tit_query, $tit_params);
	if($tit_stmt === false){
		die(print_r( sqlsrv_errors(), true));
	}
	header("Refresh:0");
	}
	
	//================================//
	//===      MODIFICA DATI       ===//
	//================================//
	
	if(isset($_POST['dat-submit']) && isset($_GET['id'])){
		//Lettura valori form
		if(empty($_POST['nome'])){
			$nome = "X";
		}else{
			$nome = $_POST['nome'];
		}
		if(empty($_POST['cognome'])){
			$cognome = "X";
		}else{
			$cognome = $_POST['cognome'];
		}
		if(empty($_POST['societa'])){
			$societa = "X";
		}else{
			$societa = $_POST['societa'];
		}
		if(empty($_POST['indirizzo'])){
			$indirizzo = "X";
		}else{
			$indirizzo = $_POST['indirizzo'];
		}
		if(empty($_POST['civico'])){
			$civico = "X";
		}else{
			$civico = $_POST['civico'];
		}
		if(empty($_POST['foglio'])){
			$foglio = "0";
		}else{
			$foglio = $_POST['foglio'];
		}
		if(empty($_POST['mappale'])){
			$mappale = "0";
		}else{
			$mappale = $_POST['mappale'];
		}
		if(empty($_POST['subalterno'])){
			$subalterno = "0";
		}else{
			$subalterno = $_POST['subalterno'];
		}
	
		// INSERIMENTO LOCALIZZAZIONE
		//Creazione del valore .loc + hash
		$loc = $indirizzo."&".$civico."_".$foglio."&".$mappale;
		$locmd5 = md5($loc);
		//Query di controllo hash
		$hash_query = "SELECT IDloc
									FROM pratica.loc
									WHERE hash = ? ";
		//Statement di controllo hash + parametri
		$hash_stmt = sqlsrv_query($link, $hash_query, array($locmd5));
		//Esecuzione controllo hash
		if($hash_stmt === false){
			die('<script>alert("'.$hash_err.'")</script>');
		}else{
			//Se l'hash è già presente
			if(sqlsrv_has_rows($hash_stmt) === true){
				//Ottieni il IDloc corrispondente
				sqlsrv_fetch($hash_stmt);
				$IDloc = sqlsrv_get_field($hash_stmt, 0);
				//Se l'hash non è presente, inserisci la nuova loc
			}else{
				//Query di inserimento loc
				$loc_query = "INSERT INTO pratica.loc (indirizzo,
																							civico,
																							foglio,
																							mappale,
																							hash)
											VALUES (?,?,?,?,?);
											SELECT SCOPE_IDENTITY() as IDloc";
				//Parametri di inserimento loc
				$loc_params = array(
												$indirizzo,
												$civico,
												$foglio,
												$mappale,
												$locmd5
												);
				//Esecuzione inserimento loc
				$loc_stmt = sqlsrv_query($link, $loc_query, $loc_params);
				if($loc_stmt === false){
					die('<script>alert("'.$loc_err.'")</script>');
				}else{
					//Ottenimento IDloc su nuovo inserimento
					sqlsrv_next_result($loc_stmt);
					sqlsrv_fetch($loc_stmt);
					$IDloc = sqlsrv_get_field($loc_stmt, 0);
				}
			}
		}
	
		$dat_query = "UPDATE pratica.dat 
									SET nome=(?), cognome=(?), societa=(?), subalterno=(?), locID=(?), editstamp=Getdate()
									WHERE ID=".$ID;
		
		$dat_params = array($nome, $cognome, $societa, $subalterno, $IDloc);
		
		//Statement di inserimento dati
		$dat_stmt = sqlsrv_query($link, $dat_query, $dat_params);
		if($dat_stmt === false){
			die( print_r( sqlsrv_errors(), true));
		}
		header("Refresh:0");
	}
	
	//================================//
	//===    MODIFICA ALLEGATI     ===//
	//================================//
	
	if(isset($_POST['att-submit']) && isset($_GET['id'])){
		//Controllo esistenza
		if(!file_exists($percorso)){
			//Creazione Cartella
			if(!mkdir($percorso, 0777, true)){
				die('<script>alert("'.$mkdir_failed.'")</script>');
			}
		}
		
		//Elaborazione ricorsiva degli allegati
		foreach($_FILES as $att => $file){
			//Controllo presenza file
			if(!empty($file['name'])){
				//Controllo dimensione file
				if($file['size'] > $max_att_size){
					die('<script>alert("'.$size_err.$file['name'].'")</script>');
				}else{
					//Rimuove il suffisso "_up" dal nome
					$att_trunc = str_replace('_up','',$att);
					
					//Rimuove il vecchio file se presente
					if(file_exists($percorso.$att_trunc.".".$$att_trunc)){
						unlink($percorso.$att_trunc.".".$$att_trunc);
					}

					//Attribuisce percorso ed estensione
					$filename = $percorso.basename($file['name']);
					$infofile = pathinfo($filename);
					
					//Sposta il file in archivio
					move_uploaded_file($file['tmp_name'], $filename);
					//Rinomina il file in base alla categoria
					rename($filename, $percorso.$att_trunc.".".$infofile['extension']);
					$$att_trunc = $infofile['extension'];					
				}
			}
		}
				
		//INSERIMENTO ALLEGATI
		//Query di inserimento allegati
		$att_query = "UPDATE pratica.att
									SET presentazione=(?), inizio_lavori=(?), relazione_tec=(?), rilascio=(?)
									WHERE IDatt=$attID";

		//Parametri di inserimento allegati
		$att_params = array(
												$presentazione,
												$inizio_lavori,
												$relazione_tec,
												$rilascio
												);
		//Statement di inserimento allegati
		$att_stmt = sqlsrv_query($link, $att_query, $att_params);
		//Esecuzione inserimento allegati
		if($att_stmt === false){
			die('<script>alert("'.$att_err.'")</script>');
		}
		header("Refresh:0");
	}
	
	//================================//
	//===      STAMPA PAGINA       ===//
	//================================//
			
	//Include barra di navigazione
	include $path.'/part/topbar.php';
	
	//Stampa l'intestazione della pratica
	echo("
	<section style='height: 192px; background: #2196F3'></section>
	<section>
		<div class='container' style='margin-top: -96px;'>
			<div style='margin-top: 40px; background: #FFF;' class='dp2 panel panel-default'>
				<div class='panel-heading'>
					<h3 class='panel-title' style='text-align: center;'>
						
			");
				
	//Se è in corso la modifica del titolo
	if(isset($_GET['mod']) && ($_GET['mod'] == "tit") && ($_SESSION['livello'] == 1)){
		//Stampa il pulsante di uscita
		echo("	
						".$atto."&nbsp;".$numero."
						<a style='float: right;' href='?id=$ID'>
							<i class='material-icons' style=' vertical-align: middle;'>clear</i>
						</a>
				");
	//Se invece non è in corso ma ci sono i permessi per farlo
	}elseif(isset($_SESSION['livello']) && ($_SESSION['livello'] == 1)){
		//Stampa il pulsante di modifica dati
		echo("
						<a style='float: left;' href='?id=$ID&mod=del' onclick='return confirm(\"Vuoi eliminare definitivamente tutti i dati e gli allegati di questa pratica?\");'>
							<i class='material-icons' vertical-align: middle;'>delete_forever</i>
						</a>
						".$atto."&nbsp;".$numero."
						<a style='float: right;' href='?id=$ID&mod=tit'>
							<i class='material-icons' vertical-align: middle;'>mode_edit</i>
						</a>
				");
	//Se invece non si hanno permessi
	}else{
		//Stampa solo il titolo
		echo($atto."&nbsp;".$numero);
	}
	
	//Stampa la chiusura del titolo in intestazione e l'apertura dell'oggetto
	echo("
					</h3>
				</div>
				<div class='panel-body'>
			");
	//Se è attivata la modifica del titolo
	if(isset($_GET['mod']) && ($_GET['mod'] == "tit") && ($_SESSION['livello'] == 1)){
		echo("
					<form action='?id=$ID' method='post' enctype='multipart/form-data'>
						<div class='form-group col-sm-12' style='padding: 0px;'>
							<textarea name='oggetto' id='oggetto' class='form-control' style='padding: 6px 0px; overflow: hidden; overflow-wrap: break-word; height: 34px;'>".$oggetto."</textarea>
						</div>
						<div style='text-align: center; margin: 10px auto;'>
							<input class='btn btn-primary' type='submit' name='tit-submit' value='Modifica'>
							<input class='btn btn-default' type='reset' name='reset'>
						</div>
					</form>
					<script>
						autosize(document.querySelectorAll('#oggetto'));
					</script>
				");
	//In caso contrario...
	}else{
		//Stampa il titolo
		echo("
					<span>".$oggetto."</span>
				");
	}
	//Stampa la chiusura del titolo
	echo("
				</div>
			</div>
	");			
	
	
	
	//Stampa l'apertura del pannello dati
	echo("
			<div style='margin-top: 20px; background: #FFF;' class='dp2 panel panel-default'>
				<div class='panel-heading'>
					<h3 class='panel-title'>Dati dell'Atto
			");

	//Se è in corso la modifica dei dati
	if(isset($_GET['mod']) && ($_GET['mod'] == "dat") && ($_SESSION['livello'] == 1)){
		//Stampa il pulsante di uscita
		echo("
						<a style='float: right;' href='?id=$ID'>
							<i class='material-icons' style=' vertical-align: middle;'>clear</i>
						</a>
				");

	//Se invece non è in corso ma ci sono i permessi per farlo
	}elseif(isset($_SESSION['livello']) && ($_SESSION['livello'] == 1)){
		//Stampa il pulsante di modifica dati
		echo("
						<a style='float: right;' href='?id=$ID&mod=dat'>
							<i class='material-icons' style=' vertical-align: middle;'>mode_edit</i>
						</a>
				");

	}
			
	//Stampa la chiusura del titolo in intestazione e l'apertura dell'oggetto
	echo("
					</h3>
				</div>
				<div class='panel-body'>
			");
	
	//Se è attivata la modifica dei dati
	if(isset($_GET['mod']) && ($_GET['mod'] == "dat") && ($_SESSION['livello'] == 1)){
		//Stampa l'area di modifica dati
		echo("
					<form action='?id=$ID' method='post' enctype='multipart/form-data'>
						<div class='row'><!--Linea #4| Inerimento dati anagrafici -->
							<div class='form-group col-sm-4'><!-- Nome -->
								<label for='nome'>Nome</label>
								<input type='text' name='nome' class='form-control' value='$nome'>
							</div>
							<div class='form-group col-sm-4'><!-- Cognome -->
								<label for='cognome'>Cognome</label>
								<input type='text' name='cognome' class='form-control' value='$cognome'>
							</div>
							<div class='form-group col-sm-4'><!-- Società -->
								<label for='societa'>Societ&agrave;</label>
								<input type='text' name='societa' class='form-control' value='$societa'>
							</div>
						</div>
						<div class='row'><!--Linea #2 | Inserimento indirizzo -->
							<div class='form-group col-sm-6'><!-- Indirizzo -->
								<label for='indirizzo'>Via/Piazza/Altro</label>
								<input type='text' name='indirizzo' class='form-control' value='$indirizzo'>
							</div>
							<div class='form-group col-sm-2'><!-- Civico -->
								<label for='civico'>Civico</label>
								<input type='text' name='civico' class='form-control' value='$civico'>
							</div>
						</div>
						<div class='row'><!--Linea #3 | Inerimento dati catastali -->
							<div class='form-group col-sm-2'><!-- Foglio -->
								<label for='foglio'>Foglio</label>
								<input type='text' name='foglio' class='form-control' value='$foglio'>
							</div>
							<div class='form-group col-sm-2'><!-- Mappale -->
								<label for='mappale'>Mappale</label>
								<input type='text' name='mappale' class='form-control' value='$mappale'>
							</div>
							<div class='form-group col-sm-2'><!-- Subalterno -->
								<label for='subalterno'>Subalterno</label>
								<input type='text' name='subalterno' class='form-control' value='$subalterno'>
							</div>
						</div>
						<div style='text-align: center; margin: 10px auto;'><!-- Pannello Pulsanti -->
							<input class='btn btn-primary' type='submit' name='dat-submit' value='Modifica'>
							<input class='btn btn-default' type='reset' name='reset'>
						</div>
					</form>
		");
	
	//In caso contrario..
	}else{
		//Stampa l'area di visualizzazione dati
		echo("
					<div class='row'>".$proprieta."</div>
					<br>
					<div class='row'>".$stradali."</div>
					<br>
					<div class='row'>".$catastali."</div>
		");
	}
	//Stampa la chiusura dei dati
	echo("
				</div>
			</div>
	");			
	


	//Stampa l'apertura del pannello dati
	echo("
			<div style='margin-top: 20px; background: #FFF;' class='dp2 panel panel-default'>
				<div class='panel-heading'>
					<h3 class='panel-title'>Allegati
			");

	//Se è in corso la modifica degli allegati
	if(isset($_GET['mod']) && ($_GET['mod'] == "att") && ($_SESSION['livello'] == 1)){
		//Stampa il pulsante di uscita
		echo("
						<a style='float: right;' href='?id=$ID'>
							<i class='material-icons' style=' vertical-align: middle;'>clear</i>
						</a>
				");
	//Se invece non è in corso ma ci sono i permessi per farlo
	}elseif(isset($_SESSION['livello']) && ($_SESSION['livello'] == 1)){
		//Stampa il pulsante di modifica dati
		echo("
						<a style='float: right;' href='?id=$ID&mod=att'>
							<i class='material-icons' style=' vertical-align: middle;'>attach_file</i>
						</a>
				");

	}
			
	//Stampa la chiusura del titolo in intestazione e l'apertura dell'oggetto
	echo("
					</h3>
				</div>
				<div class='panel-body'>
			");				
	
	//Se è attivata la modifica degli allegati
	if(isset($_GET['mod']) && ($_GET['mod'] == "att") && ($_SESSION['livello'] == 1)){
		//Stampa l'area di modifica dati
		echo("
					<form action='?id=$ID' method='post' enctype='multipart/form-data'>
						<script src='/js/carica_file.js'></script>
						<script>
							function cestina(clicked_id){
								var id = (clicked_id);
								var ajaxurl = '/part/cestina.php';
								data =  {'attID': '$attID', 'percorso': '$percorso', 'att': id};
								$.post(ajaxurl, data, function (response){
									$('#'+id+ '_label').val('Eliminato');
								});
							};
						</script>
						<div class='row'>
					");
		
		//Controlla lista allegati in config ed esegue per ogni elemento
		foreach($allegabili as $allegato => $desc){
			//Stampa la sezione di caricamento
			echo('
							<div class="col-sm-6">
								<span class="help-block">'.$desc.'</span>
								<div class="row input-group">
									<div class="col-sm-2" style="height:34px;">
										<label class="no-btn btn-primary">
												<i class="material-icons" style="margin: 5px 10px;">file_upload</i>
												<input type="file" name="'.$allegato.'_up" id="'.$allegato.'_up" style="display: none;">
										</label>
									</div>
									<div class="col-sm-8" style="padding: 0;">
										<input type="text" id="'.$allegato.'_label" value="'.$$allegato.'" class="form-control" disabled>
									</div>
									<div class="col-sm-2" style="height:34px;">
										<label id="'.$allegato.'" class="no-btn btn-danger" onclick="rimuovifile(this.id);cestina(this.id)">
											<i class="material-icons" style="margin: 5px 10px;">delete_forever</i>
										</label>
									</div>
								</div>
							</div>
						');
		}
			//Stampa la chiusura dell'area di modifica
			echo('
						</div>
						<div style="text-align: center; margin: 10px auto;"><!-- Pannello Pulsanti -->
							<input class="btn btn-primary" name="att-submit" value="Modifica" type="submit">
						</div>			
					</form>
					');
		
		//In caso contrario...
		}else{
			//Se il file ha almeno 1 allegato
			if(count(array_unique($presente)) !== 1){
				//Per ognuno di essi...
				foreach($allegati as $file => $val){
					//Se è presente
					if($val==1){
						//Stampa il relativo collegamento
						echo($file);
					}
				}
			//Altrimenti se non ce ne sono
			}else{
				//Stampa il seguente messaggio
				echo("Nessun allegato presente");
			}	
		}
	
	//Stampa la chiusura della tabella allegati
	echo("
				</div>
			</div>
	");
	
		//Stampa la chiusura della sezione
	echo("
		</div>
	</section>
			");
	//-------------------------------------------
	
	
	//Include pié di pagina
	include $path.'/part/footer.php';	
?>