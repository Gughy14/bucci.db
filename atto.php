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

	//Determina il formato da utilizzare per indirizzo
	if($indirizzo === 'X' AND $civico === 'X'){
		$stradali = "<span style='float: left'>Nessun indirizzo presente in database";
	}elseif($indirizzo !== 'X' AND $civico === 'X'){
		$stradali = '<span style="float: left">Indirizzo: </span><span style="float: right">'.$indirizzo.'</span>';
	}else{
		$stradali = '<span style="float: left">Indirizzo: </span><span style="float: right">'.$indirizzo.', '.$civico.'</span>';
	}
		
	//Determina il formato da utilizzare per dati catastali
	if($foglio !== 0 AND $mappale !== 0 AND $subalterno !== 0){
		$catastali = "<span style='float: left'>Dati Catastali: </span><span style='float: right'>Foglio ".$foglio." Mappale ".$mappale." Subalterno ".$subalterno."</span>";
	}elseif($foglio !== 0 AND $mappale !== 0 AND $subalterno == 0){
		$catastali = "<span style='float: left'>Dati Catastali: </span><span style='float: right'>Foglio ".$foglio." Mappale ".$mappale."</span>";
	}elseif($foglio !== 0 AND $mappale == 0 AND $subalterno == 0){
		$catastali = "<span style='float: left'>Dati Catastali: </span><span style='float: right'>Foglio ".$foglio."</span>";
	}elseif($foglio == 0 AND $mappale == 0 AND $subalterno == 0){
		$catastali = "<span style='float: left'>Nessun riferimento catastale presente in database";
	}else{
		$catastali = "<span style='float: left'>Riferimento di Foglio Mancante: </span><span style='float: right'>Mappale ".$mappale." Subalterno ".$subalterno."</span>";
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
				$files[$tipo] = "<a href='$percorso/$tipo.$estensione' target='_blank'>$allegabili[$tipo]</a><br>";
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
			
	//Modifica gli allegati della pratica
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
			
	//Modifica i dati della pratica
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
		
		$dat_params = array($nome, $cognome, $societa, $subalterno, $IDloc, $editstamp);
		
		//Statement di inserimento dati
		$dat_stmt = sqlsrv_query($link, $dat_query, $dat_params);
		if($dat_stmt === false){
			die( echo_r( sqlsrv_errors(), true));
		}
		header("Refresh:0");
	}
			
	//================================//
	//===      STAMPA PAGINA       ===//
	//================================//
			
	//Include barra di navigazione
	include $path.'/part/topbar.php';
	
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
	echo("
	<div class='full-width' style='background: #f0f0f0;'>
		<div class='container'>
			<div class='panel panel-default cover'>
				<div class='panel-heading'>
					<h3 class='panel-title'>Dati dell'atto");
	if(isset($_SESSION['livello']) && ($_SESSION['livello']) <= 2){
		echo("<span style='float: right;'><a href='?id=$ID&mod=dat'><i class='fa fa-pencil-square-o' title='Modifica' aria-hidden='true'></i></a></span>");
	}
	
	if(isset($_GET['mod']) && ($_GET['mod'] == "dat")){
		echo("
					</div>
					<div class='panel-body cover'>
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
					</div>
				</div>
			</div>
		</div>
		");
	}else{
		echo("
						</h3>
					</div>
					<div class='panel-body cover'>
						<h4>".$proprieta."</h4>
						<br>
						<h4>".$stradali."</h4>
						<br>
						<h4>".$catastali."</h4>
					</div>
				</div>
			</div>
		</div>
		");
	}

	//Allegati
	echo("
	<div class='full-width' style='background: #f0f0f0;'>
		<div class='container'>
			<div class='panel panel-default cover'>
				<div class='panel-heading'>
					<h3 class='panel-title'>Allegati");
	
	if(isset($_SESSION['livello']) && ($_SESSION['livello']) <= 2){
		echo("<span style='float: right;'><a href='?id=$ID&mod=att'><i class='fa fa-pencil-square-o' title='Modifica' aria-hidden='true'></i></a></span>");
	}
	echo("
					</h3>
				</div>
			<div class='panel-body cover'>
			");
			
	if(isset($_GET['mod']) && ($_GET['mod'] == "att")){
			
		echo("
					<form action='?id=$ID' method='post' enctype='multipart/form-data'>
						<script src='/js/carica_file.js'></script>
						<div class='row'>
					");
		
		//Controlla lista allegati in config
		foreach($allegabili as $allegato => $desc){
			//Stampa la sezione di caricamento
			echo('
							<div class="form-group col-lg-6 col-sm-5 col-10">
								<span class="help-block">'.$desc.'
									<span style="float:right;" id="'.$allegato.'" onclick="rimuovifile(this.id)">Rimuovi &times;</span>
								</span>
								<div class="input-group">
									<label class="input-group-btn">
										<span class="btn btn-primary">
											Carica&hellip; <input type="file" name="'.$allegato.'_up" id="'.$allegato.'_up" style="display: none;">
										</span>
									</label>
									<input type="text" id="'.$allegato.'_label" class="form-control" readonly>
								</div>
							</div>
						');
		}
		echo('
						</div>
						<div style="text-align: center; margin: 10px auto;"><!-- Pannello Pulsanti -->
							<input class="btn btn-primary" name="att-submit" value="Modifica" type="submit">
						</div>			
					</form>
					');
		}else{
		//Verifica la presenza degli allegati
		if(count(array_unique($presente)) !== 1){
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
	}
	//Stampa la chiusura della tabella allegati
	echo("
				</div>
			</div>
		</div>
	</div>
	");
	
	//Include pié di pagina
	include $path.'/part/footer.php';	
?>