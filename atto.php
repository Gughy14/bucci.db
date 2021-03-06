<?php
	//Definisce la directory base del sito
	$path = $_SERVER['DOCUMENT_ROOT'];
	
	//Include Intestazione
	include $path.'/part/head.php';
?>

		<script src="/js/autosize.js" type="application/javascript"></script>
		
		<!-- Script ordinamento tabelle --><script>
			function sortTable(table, col, reverse) {
				var tb = table.tBodies[0],
						tr = Array.prototype.slice.call(tb.rows, 0),
						i;
				reverse = -((+reverse) || -1);
				tr = tr.sort(function (a, b) {
						return reverse // `-1 *` per ordine opposto
								* (a.cells[col].textContent.trim()
										.localeCompare(b.cells[col].textContent.trim())
									);
				});
				for(i = 0; i < tr.length; ++i) tb.appendChild(tr[i]);
			}
			
			function makeSortable(table) {
				var th = table.tHead, i;
				th && (th = th.rows[0]) && (th = th.cells);
				if (th) i = th.length;
				else return;
				while (--i >= 0) (function (i) {
						var dir = 1;
						th[i].addEventListener('click', function () {sortTable(table, i, (dir = 1 - dir))});
				}(i));
			}
			
			function makeAllSortable(parent) {
				parent = parent || document.body;
				var t = parent.getElementsByTagName('table'), i = t.length;
				while (--i >= 0) makeSortable(t[i]);
			}
			window.onload = function () {makeAllSortable();};
			
			
			function sort_ogg(){
				if(document.getElementById("ogg").innerHTML.match("arrow_downward")){
					document.getElementById("ogg").innerHTML = "arrow_upward";	
				}else{
					document.getElementById("ogg").innerHTML = "arrow_downward";	
				}
			}
			function sort_num(){
				if(document.getElementById("num").innerHTML.match("arrow_upward")){
					document.getElementById("num").innerHTML = "arrow_downward";	
				}else{
					document.getElementById("num").innerHTML = "arrow_upward";	
				}
			}
		</script>
<?php
	//Include barra di navigazione
	include $path.'/part/topbar.php';
	
	if(!isset($_GET['id'])){
		
		//Codice per pagina senza ID
		
	}elseif(!isset($_SESSION['livello']) || ($_SESSION['livello'] > 2)){
		
		//Codice di accesso negato
	
	}else{
		
		//Identifica l'atto tramite ID get
		$ID = $_GET['id'];
		
		//Stabilisce la connessione al database
		$link = sqlsrv_connect($dbserver, $dbdata);
		if($link === false){
			
			//Codice in caso di connessione fallita
			
		}else{
			
			//Query di restituzione dati
			$display_query = "SELECT *
												FROM pratica.dat
												JOIN pratica.loc
												ON locID = IDloc
												WHERE pratica.dat.ID = ".$ID;
			
			//Esecuzione restituzione dati
			$display_stmt = sqlsrv_query($link, $display_query);
			if($display_stmt === false){
				
				//Codice per errore restituzione dati
				die('<script>alert("'.$display_err.'")</script>');
				
			}elseif(sqlsrv_has_rows($display_stmt) === false){
				
				//Codice per pratica non registrata (ID non in DB)
				die("ID inesistente");
				
			}else{
				
				//Trascrive in variabili i dati della pratica
				while($risultato = sqlsrv_fetch_array($display_stmt, SQLSRV_FETCH_ASSOC)){
					foreach($risultato as $campo => $valore){
						$$campo = $valore;
					}
				}
									
				//Elabora le variabili precedentemente ottenute
				$num_noslash = str_replace('/','.',$numero);
				$anno_presentazione = $data_presentazione->format("Y");
				if($anno_presentazione == 0001){$anno_presentazione = "Non datati";}
				$percorso = "D:/atti/".$anno_presentazione."/".$atto."_".$num_noslash."/";
				$percorsoweb = "../atti/".$anno_presentazione."/".$atto."_".$num_noslash."/";
					
				//Determina il formato da utilizzare per propriet�
				if($cognome === 'X' AND $nome === 'X' AND $societa === 'X'){
					$proprieta = "<div class='col-sm-12'>Nessun riferimento presente in database</div>";
				}elseif($cognome !== 'X' AND $nome !== 'X' AND $societa === 'X'){
					$proprieta = "<div class='col-sm-6'>
								<b>Presentata da:</b>
							</div>
							<div class='col-sm-3'>".$cognome."</div>
							<div class='col-sm-3'>".$nome."</div>";
				}elseif($cognome !== 'X' AND $nome === 'X' AND $societa !== 'X'){
					$proprieta = "<div class='col-sm-4'>
								<b>Presentata da:</b>
							</div>
							<div class='col-sm-4'>".$cognome."</div>
							<div class='col-sm-4'>".$societa."</div>";
				}elseif($cognome !== 'X' AND $nome === 'X' AND $societa === 'X'){
					$proprieta = "<div class='col-sm-6'>
								<b>Presentata da:</b>
							</div>
							<div class='col-sm-6'>".$cognome."</div>";
				}elseif($cognome === 'X' AND $nome === 'X' AND $societa !== 'X'){
					$proprieta = "<div class='col-sm-6'>
								<b>Presentata da:</b>
							</div>
							<div class='col-sm-6'>".$societa."</div>";
				}else{
					$proprieta = "<div class='col-sm-3'>
								<b>Presentata da:</b>
							</div>
							<div class='col-sm-3'>".$cognome."</div>
							<div class='col-sm-3'>".$nome."</div>
							<div class='col-sm-3'>".$societa."</div>";
				}

				//Determina il formato da utilizzare per indirizzo
				if($indirizzo === 'X' AND $civico === 'X'){
					$stradali = "<div class='col-sm-12'>Nessun indirizzo presente in database</div>";
				}elseif($indirizzo !== 'X' AND $civico === 'X'){
					$stradali = "<div class='col-sm-6'>Indirizzo</div>
							<div class='col-sm-6'>".$indirizzo."</div>";
				}else{
					$stradali = "<div class='col-sm-6'>Indirizzo</div>
							<div class='col-sm-3'>".$indirizzo."</div>
							<div class='col-sm-3'>Civico ".$civico."</div>";
				}					
				//Determina il formato da utilizzare per dati catastali
				if($foglio !== 0 AND $mappale !== 0 AND $subalterno !== '0'){
					$catastali = "<div class='col-sm-6'>Dati Catastali</div>
							<div class='col-sm-2'>Foglio ".$foglio."</div>
							<div class='col-sm-2'>Mappale ".$mappale."</div>
							<div class='col-sm-2'>Subalterno ".$subalterno."</div>";
				}elseif($foglio !== 0 AND $mappale !== 0 AND $subalterno == '0'){
					$catastali = "<div class='col-sm-6'>Dati Catastali</div>
							<div class='col-sm-3'>Foglio ".$foglio."</div>
							<div class='col-sm-3'>Mappale ".$mappale."</div>";
				}elseif($foglio !== 0 AND $mappale == 0 AND $subalterno == '0'){
					$catastali = "<div class='col-sm-6'>Dati Catastali</div>
							<div class='col-sm-3'>Foglio ".$foglio."</div>
							<div class='col-sm-3'>Mappale non identificabile</div>";
				}elseif($foglio == 0 AND $mappale !== 0 AND $subalterno !== '0'){
					$catastali = "<div class='col-sm-6'>Riferimento di Foglio Mancante</div>
							<div class='col-sm-3'>Mappale ".$mappale."</div>
							<div class='col-sm-3'>Subalterno ".$subalterno."</div>";
				}elseif($foglio == 0 AND $mappale !== 0 AND $subalterno == '0'){
					$catastali = "<div class='col-sm-6'>Riferimento di Foglio Mancante</div>
							<div class='col-sm-6'>Mappale ".$mappale."</div>";
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
					
					//Codice per errore allegati (non stampati)
					
				}else{
				
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
								$files[$tipo] = "<a href='$percorsoweb$tipo.$estensione' target='_blank'>$allegabili[$tipo]</a><br>";
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
				}
				
				//Richiesta pratiche correlate
				$related_query = "SELECT ID, atto, numero, data_presentazione, oggetto
												FROM pratica.dat
												JOIN pratica.loc
												ON locID = IDloc
												WHERE locID = (?)
												AND ID <> (?)
												";
				//Esecuzione ricerca
				$related_stmt = sqlsrv_query($link, $related_query, array($locID, $ID));
				
				//================================//
				//===     ELIMINA PRATICA      ===//
				//================================//
				
				if(isset($_GET['mod']) && ($_GET['mod'] == "del") && ($_SESSION['livello'] == 1)){
					//Richiesta cancellazione dati e allegati dal db
					$del_query = "DELETE FROM pratica.dat
												WHERE ID = $ID;
												DELETE FROM pratica.att 
												WHERE IDatt = $attID";
					//Esecuzione cancellazione dati e allegati
					$del_stmt = sqlsrv_query($link, $del_query);
					if($del_stmt === false){
						
						//Codice per errore rimozione (non rimozione)
						die(print_r( sqlsrv_errors(), true));
						
					}else{
						//Identifica tutti i files presenti in percorso
						$files = glob(str_replace('..','D:',$percorso."*.*"));
						//Elimina ogni file identificato
						foreach ($files as $file){unlink($file);}
						//Rimuove la cartella
						rmdir(str_replace('..','D:',$percorso));
						//Reindirizza alla pagina di ricerca
						header("location: /cerca.php");
					}
				}
				
				//================================//
				//===     MODIFICA TITOLO      ===//
				//================================//
				
				if(isset($_POST['tit-submit']) && isset($_GET['id'])){
					//Legge il campo oggetto
					empty($_POST['oggetto']) ? $oggetto = "Nessun Oggetto" : $oggetto = $_POST['oggetto'];
					
					//Richiesta modifica oggetto
					$tit_query = "UPDATE pratica.dat 
												SET oggetto=(?), editstamp=Getdate()
												WHERE ID = ".$ID;
					
					//Esegue la modifica dell'oggetto
					$tit_stmt = sqlsrv_query($link, $tit_query, array($oggetto));
					if($tit_stmt === false){
						
						//Codice per errore modifica titolo (non modifica)
						die(print_r( sqlsrv_errors(), true));
						
					}else{
						//Ricarica la pagina
						header("Refresh:0");
					}
				}
				
				//================================//
				//===      MODIFICA DATI       ===//
				//================================//
				
				if(isset($_POST['dat-submit']) && isset($_GET['id'])){
					
					//Legge ed elabora i valori da modificare
					empty($_POST['nome']) ? $nome = "X" : $nome = $_POST['nome'];
					empty($_POST['cognome']) ? $cognome = "X" : $cognome = $_POST['cognome'];
					empty($_POST['societa']) ? $societa = "X" : $societa = $_POST['societa'];
					
					empty($_POST['indirizzo']) ? $indirizzo = "X" : $indirizzo = $_POST['indirizzo'];
					empty($_POST['civico']) ? $civico = "X" : $civico = $_POST['civico'];
					empty($_POST['foglio']) ? $foglio = "0" : $foglio = $_POST['foglio'];			
					empty($_POST['mappale']) ? $mappale = "0" : $mappale = $_POST['mappale'];
					empty($_POST['subalterno']) ? $subalterno = "0" : $subalterno = $_POST['subalterno'];

				
					//Crea il valore .loc + hash
					$loc = $foglio."&".$mappale;
					$locmd5 = md5($loc);
					
					//Richiesta di controllo hash .loc
					$hash_query = " SELECT IDloc
													FROM pratica.loc
													WHERE hash = ? ";
					
					//Esegue il controllo dell'hash .loc
					$hash_stmt = sqlsrv_query($link, $hash_query, array($locmd5));
					if($hash_stmt === false){
						
						//Codice per errore controllo hash
						die('<script>alert("'.$hash_err.'")</script>');
						
					}elseif(sqlsrv_has_rows($hash_stmt) === true){
							//Ottieni il IDloc corrispondente
							sqlsrv_fetch($hash_stmt);
							$IDloc = sqlsrv_get_field($hash_stmt, 0);
						//Se l'hash non � presente, inserisci la nuova loc
					}else{
						//Richiesta di inserimento loc
						$loc_query = "INSERT INTO pratica.loc (foglio,
																									mappale,
																									hash)
													VALUES (?,?,?,?,?);
													SELECT SCOPE_IDENTITY() as IDloc";
						//Parametri di inserimento loc
						$loc_params = array(
														$foglio,
														$mappale,
														$locmd5
														);
						//Esecuzione inserimento loc
						$loc_stmt = sqlsrv_query($link, $loc_query, $loc_params);
						if($loc_stmt === false){
							
							//Codice per errore inserimento Hash
							die('<script>alert("'.$loc_err.'")</script>');
							
						}else{
							//Ottenimento IDloc su nuovo inserimento
							sqlsrv_next_result($loc_stmt);
							sqlsrv_fetch($loc_stmt);
							$IDloc = sqlsrv_get_field($loc_stmt, 0);
						}
					}
					
					//Richiesta di modifica dati
					$dat_query = "UPDATE pratica.dat 
												SET nome=(?), cognome=(?), societa=(?), indirizzo=(?), civico=(?), subalterno=(?), locID=(?), editstamp=Getdate()
												WHERE ID=".$ID;
					//Parametr di modifica dati
					$dat_params = array($nome, $cognome, $societa, $indirizzo, $civico, $subalterno, $IDloc);
					
					//Esegue la modifica dei dati
					$dat_stmt = sqlsrv_query($link, $dat_query, $dat_params);
					if($dat_stmt === false){
						
						//Codice errore modifica dati
						die( print_r( sqlsrv_errors(), true));
						
					}else{
						//Ricarica la pagina
						header("Refresh:0");
					}
				}
				
				//================================//
				//===    MODIFICA ALLEGATI     ===//
				//================================//
				
				if(isset($_POST['att-submit']) && isset($_GET['id'])){
					
					//Percorso archiviazione files
					$percorso_file = $percorso."/";
					
					//Elabora ricorsivamente gli allegati
					foreach($_FILES as $att => $file){
						//Controlla la presenza file
						if(!empty($file['name'])){
							//Rimuove il suffisso "_up" dal nome
							$att_trunc = str_replace('_up','',$att);
							//Controlla dimensioni file (backup del JS)
							if($file['size'] > $max_att_size){
								die('
				<script>
					window.alert("'.$size_err." ".$file['name'].'")
					window.location.replace("'.htmlentities($_SERVER['PHP_SELF']).'");
				</script>');
							}else{
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
								rename($filename, $percorso_file.$att_trunc.".".$infofile['extension']);
								$$att_trunc = $infofile['extension'];
							}
						}
					}
					
					//Richiesta di aggiornamento allegati
					$att_query = "UPDATE pratica.att
												SET pratica=(?), presentazione=(?), inizio_lavori=(?), relazione_tec=(?), rilascio=(?)
												WHERE IDatt = $attID";

					//Parametri di aggiornamento allegati
					$att_params = array(
															$pratica,
															$presentazione,
															$inizio_lavori,
															$relazione_tec,
															$rilascio
															);
					
					//Esegue l'aggiornamento degli allegati
					$att_stmt = sqlsrv_query($link, $att_query, $att_params);
					if($att_stmt === false){
						
						//Codice per errore modifica allegati (errore mismatch)
						die('<script>alert("'.$att_err.'")</script>');
						
					}else{
						//Ricarica la pagina
						header("Refresh:0");
					}
				}
				
				//================================//
				//===      STAMPA PAGINA       ===//
				//================================//
				
				//Stampa l'intestazione della pratica
				echo("
		<div class='section material'></div>
		<div class='section'>
			<div class='form container'>
				<div class='dp2 panel panel-default'>
					<div class='panel-heading'>
						<h3 class='panel-title' style='text-align: center;'>");
				
				//Se � in corso la modifica del titolo
				if(isset($_GET['mod']) && ($_GET['mod'] == "tit") && ($_SESSION['livello'] == 1)){
					//Stampa il pulsante di uscita
					echo(
							$atto."&nbsp;".$numero."
							<a style='float: right;' href='?id=$ID'>
								<i class='material-icons' style=' vertical-align: middle;'>clear</i>
							</a>");
				//Se invece non � in corso ma ci sono i permessi per farlo
				}elseif(isset($_SESSION['livello']) && ($_SESSION['livello'] == 1)){
					//Stampa il pulsante di modifica dati
					echo("
							<a style='float: left;' href='?id=$ID&mod=del' onclick='return confirm(\"Vuoi eliminare definitivamente tutti i dati e gli allegati di questa pratica?\");'>
								<i class='material-icons' style='vertical-align: middle;'>delete_forever</i>
							</a>
							".$atto."&nbsp;".$numero."
							<a style='float: right;' href='?id=$ID&mod=tit'>
								<i class='material-icons' style='vertical-align: middle;'>mode_edit</i>
							</a>");
				//Se invece non si hanno permessi
				}else{
					//Stampa solo il titolo
					echo($atto."&nbsp;".$numero);
				}
				//Stampa la chiusura del titolo in intestazione e l'apertura dell'oggetto
				echo("
						</h3>
					</div>
					<div class='panel-body'>");
				//Se � attivata la modifica del titolo
				if(isset($_GET['mod']) && ($_GET['mod'] == "tit") && ($_SESSION['livello'] == 1)){
					echo("
						<form action='".htmlentities($_SERVER['PHP_SELF'])."?id=$ID' method='post' enctype='multipart/form-data'>
							<div class='form-group col-sm-12' style='padding: 0px;'>
								<textarea name='oggetto' id='oggetto' class='form-control' style='padding: 6px 0px; overflow: hidden; overflow-wrap: break-word; height: 34px;'>".$oggetto."</textarea>
							</div>
							<div style='text-align: center; margin: 10px auto;'>
								<button class='btn btn-primary' type='submit' name='tit-submit'>Modifica</button>
								<button class='btn btn-default' type='reset' name='reset'>Reimposta</button>
							</div>
						</form>
						<script>
							autosize(document.querySelectorAll('#oggetto'));
						</script>");
				//In caso contrario...
				}else{
					//Stampa il titolo
					echo("
						<span>".$oggetto."</span>");
				}
				//Stampa la chiusura del titolo
				echo("
					</div>
				</div>");			
				//Stampa l'apertura del pannello dati
				echo("
				<div class='dp2 panel panel-default'>
					<div class='panel-heading'>
						<h3 class='panel-title'>Dati");

				//Se � in corso la modifica dei dati
				if(isset($_GET['mod']) && ($_GET['mod'] == "dat") && ($_SESSION['livello'] == 1)){
					//Stampa il pulsante di uscita
					echo("
							<a style='float: right;' href='?id=$ID'>
								<i class='material-icons' style=' vertical-align: middle;'>clear</i>
							</a>");

				//Se invece non � in corso ma ci sono i permessi per farlo
				}elseif(isset($_SESSION['livello']) && ($_SESSION['livello'] == 1)){
					//Stampa il pulsante di modifica dati
					echo("
							<a style='float: right;' href='?id=$ID&mod=dat'>
								<i class='material-icons' style=' vertical-align: middle;'>mode_edit</i>
							</a>");
				}
						
				//Stampa la chiusura del titolo in intestazione e l'apertura dell'oggetto
				echo("
						</h3>
					</div>
					<div class='panel-body'>");
				
				//Se � attivata la modifica dei dati
				if(isset($_GET['mod']) && ($_GET['mod'] == "dat") && ($_SESSION['livello'] == 1)){
					//Stampa l'area di modifica dati
					echo("
						<form action='".htmlentities($_SERVER['PHP_SELF'])."?id=$ID' method='post' enctype='multipart/form-data'>
							<div class='row'><!--Linea #4| Inerimento dati anagrafici -->
								<div class='form-group col-sm-4'><!-- Nome -->
									<label for='nome'>Nome</label>
									<input type='text' name='nome' class='form-control' value='$nome'>
								</div>
								<div class='form-group col-sm-4'><!-- Cognome -->
									<label for='cognome'>Cognome</label>
									<input type='text' name='cognome' class='form-control' value='$cognome'>
								</div>
								<div class='form-group col-sm-4'><!-- Societ� -->
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
								<button class='btn btn-primary' type='submit' name='dat-submit'>MODIFICA</button>
								<button class='btn btn-default' type='reset' name='reset'>REIMPOSTA</button>
							</div>
						</form>");
				
				//In caso contrario..
				}else{
					//Stampa l'area di visualizzazione dati
					echo("
						<div class='row'>
							".$proprieta."
						</div>
						<br>
						<div class='row'>
							".$stradali."
						</div>
						<br>
						<div class='row'>
							".$catastali."
						</div>");
				}
				//Stampa la chiusura dei dati
				echo("
					</div>
				</div>");			
				


				//Stampa l'apertura del pannello dati
				echo("
				<div class='dp2 panel panel-default'>
					<div class='panel-heading'>
						<h3 class='panel-title'>Allegati");

				//Se � in corso la modifica degli allegati
				if(isset($_GET['mod']) && ($_GET['mod'] == "att") && ($_SESSION['livello'] == 1)){
					//Stampa il pulsante di uscita
					echo("
						<a style='float: right;' href='?id=$ID'>
							<i class='material-icons' style=' vertical-align: middle;'>clear</i>
						</a>");
				//Se invece non � in corso ma ci sono i permessi per farlo
				}elseif(isset($_SESSION['livello']) && ($_SESSION['livello'] == 1)){
					//Stampa il pulsante di modifica dati
					echo("
						<a style='float: right;' href='?id=$ID&mod=att'>
							<i class='material-icons' style=' vertical-align: middle;'>attach_file</i>
						</a>");
				}
						
				//Stampa la chiusura del titolo in intestazione e l'apertura dell'oggetto
				echo("
					</h3>
				</div>
				<div class='panel-body'>");				
				
				//Se � attivata la modifica degli allegati
				if(isset($_GET['mod']) && ($_GET['mod'] == "att") && ($_SESSION['livello'] == 1)){
					//Stampa l'area di modifica dati
					echo("
					<form action='".htmlentities($_SERVER['PHP_SELF'])."?id=$ID' method='post' enctype='multipart/form-data'>
						<script src='/js/carica_file.js'></script>
						<script>
							function cestina(id){
								var filename = id.replace('_del', '');
								var ajaxurl = '/part/cestina.php';
								data =  {'attID': '$attID', 'percorso': '$percorso', 'att': filename};
								$.post(ajaxurl, data, function (response){
									$('#'+ filename + '_label').val('Eliminato');
								});
							};
						</script>
						<div class='row'>");
					
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
										<label id="'.$allegato.'_del" class="no-btn btn-danger" onclick="rimuoviFile(this.id);cestina(this.id)">
											<i class="material-icons" style="margin: 5px 10px;">delete_forever</i>
										</label>
									</div>
								</div>
							</div>');
					}
						//Stampa la chiusura dell'area di modifica
						echo('
						</div>
						<div style="text-align: center; margin: 10px auto;"><!-- Pannello Pulsanti -->
							<button class="btn btn-primary" name="att-submit" type="submit">MODIFICA</button>
						</div>			
					</form>');
					
					//In caso contrario...
					}else{
						//Se il file ha almeno 1 allegato
						if(count(array_unique($presente)) !== 1){
							//Per ognuno di essi...
							foreach($allegati as $file => $val){
								//Se � presente
								if($val==1){
									//Stampa il relativo collegamento
									echo("
					".$file);
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
				</div>");
						
						
						
						
						
						
						
						
						
						
						
				//Stampa le pratuche correlate (Se presenti)
				if(sqlsrv_has_rows($related_stmt) === true){
					//Stampa l'apertura del pannello correlati
					echo("
				<div class='dp2 panel panel-default'>
					<div class='panel-heading'>
						<h3 class='panel-title'>Pratiche Correlate</h3>
					</div>
					<div class='panel-body' style='padding: 16px 32px;'>");

					//Stampa della tabella di contenimento
					echo("
						<table class='table table-responsive table-hover table-striped' style='cursor: default;'>
							<!-- Intestazione Tabella --><thead>
								<tr>
									<th style='width: 32px;'>File</th>
									<th style='width: 32px;'>Atto</th>
									<th style='width: 80px;' onclick='sort_num()'>
										<i id='num' class='material-icons' style='font-size: 16px; float: left;'>arrow_downward</i>
										<span style='float: left;'>&nbsp; Num.</span>
									</th>		
									<th onclick='sort_ogg()'>
										<i id='ogg' class='material-icons' style='font-size: 16px;'>arrow_upward</i>
										&nbsp; Oggetto
									</th>				
								</tr>
							</thead>
							<!-- Corpo Tabella --><tbody>");
							
					//Elaborazione ricorsiva dei risultati
					while($row = sqlsrv_fetch_array($related_stmt, SQLSRV_FETCH_ASSOC)){
						
						//Assegnazione ed elaborazione variabili
						$ID = $row['ID'];
						$atto = $row['atto'];
						$numero = $row['numero'];
						$num_noslash = str_replace('/','.',$numero);
						$data_presentazione = $row['data_presentazione'];
						$anno_presentazione = $data_presentazione->format("Y");
						if($anno_presentazione = 0001){
							$anno_presentazione = "Non datati";
						}
						$oggetto = $row['oggetto'];
						
						//Stampa delle variabili in tabella
						echo("
								<!-- Pratica Numero ".$ID." --><tr>
									<td style='text-align: center;'>
										<a href='/atto.php?id=".$ID."' target='_blank'>
											<i class='material-icons'>description</i>
										</a>
									</td>
									<td style='text-align: center;'>".$atto."</td>
									<td style='text-align: center;'>".$numero."</td>
									<td>".$oggetto."</td>
								</tr>");
					}
					
					//Stampa chiusura tabella
					echo("
							</tbody>
						</table>
					</div>");
					
					//Stampa la chiusura della sezione
				echo("
				</div>
			</div>
		</div>");
				}						
			}
		}
	}

	//Include pi� di pagina
	include $path.'/part/footer.php';	
?>