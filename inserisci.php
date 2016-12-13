<?php
	//Include Intestazione
	include 'part/head.php';
	
	//CONFIGURAZIONI
	$chiave = "chiave";
	$max_att_size = 10*MB;
	$dbdata = $pass_data['dbmaster'];
	
	//MESSAGGI DI ERRORE
	$null_act = "Il numero di atto non può essere omesso";
	$att_err = "Si è verificato un errore durante l'inserimento degli allegati in database";
	$hash_err = "Errore durante la comparazione dell'hash";
	$loc_err = "Errore durante l'inserimento dei dati di localizzazione";
	$mkdir_failed = "Si è verificato un errore durante la creazione della cartella in archivio";
	$size_err = "Impossibile caricare il file poiché di dimensioni superiori a ".str_replace('*','',$max_att_size)." bytes: ";
	$index_err = "Si è verificato un errore durante la creazione della copertina della pratica";
	
	//ALLEGATI INSERIBILI
	$allegabili = array(
										"presentazione" => "Modulo di Presentazione" ,
										"inizio_lavori" => "Comunicazione Inizio lavori" ,
										"relazione_tec" => "Relazione Tecnica" ,
										"rilascio" => "Documento di Rilascio"
										);
										
	//Controllo livello di autorizzazione
	if(isset($_SESSION['livello'])){
		if($_SESSION['livello'] > 2){
				//Codice di errore permessi
				die("NON AUTORIZZATO");
		}
	}else{
		//Codice di mancata autenticazione
		die("ACCESSO NEGATO");
	}
?>

	<script><!-- Ritorno nome file caricato -->
	$(function(){
		$(document).on('change', ':file', function(){
			var input = $(this),
			numFiles = input.get(0).files ? input.get(0).files.length : 1,
			label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
			input.trigger('fileselect', [numFiles, label]);
		});
		$(document).ready( function(){
			$(':file').on('fileselect', function(event, numFiles, label){
				var input = $(this).parents('.input-group').find(':text'),
				log = numFiles > 1 ? numFiles + ' files selected' : label;
				
				if(input.length){
					input.val(log);
				}else{
					if(log)alert(log);
				}
			});
		});
	});
	</script>
	<script><!-- datepicker -->
		$(function() {
			$("#data_presentazione").datepicker({
				showOtherMonths: true,
				selectOtherMonths: true,
				changeMonth: true,
				changeYear: true
			});
			$("#data_rilascio").datepicker({
				showOtherMonths: true,
				selectOtherMonths: true,
				changeMonth: true,
				changeYear: true
			});
    });
  </script>
	
<?php
	//Include barra di navigazione
	include 'part/topbar.php';
?>

<div class="full-width" style="margin-top: 34px; background: #11283b; min-height: 125px;"><!-- Cover -->
	<div class="container" style="text-align: center;">
	<h2 style="color: #f0f0f0;">Inserisci un nuovo atto</h2>
	</div>
</div>
<div class="full-width" style="background: #38414A;"><!-- Breaking -->
	&nbsp;
</div>
<div class="full-width" style="background: #f0f0f0;"><!-- Fake BG -->
	&nbsp;
</div>
<div class="full-width" style="background: #0971aa;"><!-- Banner -->
	<div class="container">
		<a href="placeholder" style="text-decoration: none;">
			<center><h4 style="color: #f0f0f0;">Aggiungi un nuovo atto al database, compilando i campi sottostanti</h4></center>
		</a>
	</div>
</div>

<section style="background: #f0f0f0;" class="full-width form"><!-- Form di inserimento dati -->
	<form action="inserisci.php?<?php print($chiave); ?>" method="post" enctype="multipart/form-data" class="container">
		<div class="panel panel-default"><!-- Pannello Dati -->
			<div class="panel-heading"><!-- Intestazione Pannello -->
				<h3 class="panel-title">Dati della pratica</h3>
			</div>
		<div class="panel-body"><!-- Corpo del Pannello -->
				<div class="row"><!--Linea #1 | Determinazione specifica/annuale -->
					<div class="form-group col-sm-6"><!-- Tipo di Atto -->
						<label for='atto'>Tipo di Atto</label>
						<select name="atto" id="atto" class="form-control">
							<option value='LE'>Licenza Edilizia</option>
							<option value='AE'>Autorizzazione Edilizia</option>
							<option value='NO'>Nulla osta alla costruzione</option>
							<option value='CE'>Concessione Edilizia</option>
							<option value='PDC'>Permesso di Costruire (PDC)</option>
							<option value='DIA.PE'>Denuncia inizio attivit&agrave; (DIA o PE)</option>
							<option value='SCIA'>Segnalazione Certificata Inizio Attivit&agrave; (SCIA)</option>
							<option value='L7310'>Comunicazione Inizio Lavori (L7310)</option>
							<option value='CIL'>Comunicazione Inizio Lavori (CIL)</option>
							<option value='CILA'>Comunicazione Inizio Lavori Asseverata (CILA)</option>
            </select>
					</div>
					<div class="form-group col-sm-2"><!-- Numero di Atto -->
						<label for='numero'>Atto n&deg;</label>
						<input type="text" class="form-control" name="numero" id="numero" size="8">
					</div>
					<div class="form-group col-sm-2"><!-- Data di Presentazione -->
						<label for='data_presentazione'>Data di pres.</label>
						<input type="date" name="data_presentazione" id="data_presentazione" class="form-control">
					</div>
					<div class="form-group col-sm-2"><!-- Data di Rilacio -->
						<label for='data_rilascio'>Data di rilascio</label>
						<input type="date" name="data_rilascio" id="data_rilascio" class="form-control">
					</div>
				</div>
				<br>
				<div class="row"><!--Linea #2 | Inserimento indirizzo -->
					<div class="form-group col-sm-6"><!-- Indirizzo -->
						<label for="indirizzo">Via/Piazza/Altro</label>
						<input type="text" name="indirizzo" class="form-control">
					</div>
					<div class="form-group col-sm-2"><!-- Civico -->
						<label for="civico">Civico</label>
						<input type="text" name="civico" class="form-control">
					</div>
				</div>
				<br>
				<div class="row"><!--Linea #3 | Inerimento dati catastali -->
					<div class="form-group col-sm-2"><!-- Foglio -->
						<label for="foglio">Foglio</label>
						<input type="text" name="foglio" class="form-control">
					</div>
					<div class="form-group col-sm-2"><!-- Mappale -->
						<label for="mappale">Mappale</label>
						<input type="text" name="mappale" class="form-control">
					</div>
					<div class="form-group col-sm-2"><!-- Subalterno -->
						<label for="subalterno">Subalterno</label>
						<input type="text" name="subalterno" class="form-control">
          </div>
				</div>
				<br>
				<div class="row"><!--Linea #4| Inerimento dati anagrafici -->
					<div class="form-group col-sm-4"><!-- Nome -->
						<label for="nome">Nome</label>
						<input type="text" name="nome" class="form-control">
					</div>
					<div class="form-group col-sm-4"><!-- Cognome -->
						<label for="cognome">Cognome</label>
						<input type="text" name="cognome" class="form-control">
					</div>
					<div class="form-group col-sm-4"><!-- Società -->
						<label for="societa">Societ&agrave;</label>
						<input type="text" name="societa" class="form-control">
					</div>
				</div>
				<br>
				<div class="form-group"><!-- Oggetto -->
					<label for="oggetto">Oggetto</label>
					<textarea name="oggetto" rows="4" cols="75" class="form-control"></textarea>
				</div>
			</div>
		</div>
		<div class="panel panel-default"><!-- Pannello Allegati -->
			<div class="panel-heading"><!-- Intestazione Pannello -->
				<h3 class="panel-title">Allegati</h3>
			</div>
			<div class="panel-body"><!-- Corpo del Pannello -->
			<?php
				//Controlla lista allegati in config
				foreach($allegabili as $allegato => $desc){
					//Imposta il valore NULL come default 
					$$allegato = NULL;
					//Stampa la sezione di caricamento
					print('
								<div class="col-lg-6 col-sm-5 col-10">
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
			?>
			</div>
		</div>
		<div style="text-align: center; margin: 50px auto;"><!-- Pannello Pulsanti -->
			<input class="btn btn-primary" type="submit" name="submit">
			<input class="btn btn-default" type="reset" name="reset">
		</div>
	</form>
</section>

	<script><!-- Rimozione Allegati-->
	function rimuovifile(clicked_id) {
		var id = (clicked_id);
		$("#"+id+"_up").replaceWith($("#"+id+"_up").val('').clone(true));
		document.getElementById(id+"_label").value = "";
	}
	</script>


<?php /* ELABORAZIONE INSERIMENTO */
	//Controllo inserimento tramite invio e chiave
	if(isset($_POST['submit']) && isset($_GET[$chiave])){
		
		//================================//
		//===   LETTURA VALORI FORM    ===//
		//================================//
		
		//Valori pratica.dat
		if(empty($_POST['atto'])){
			$atto = "NonSpec";
		}else{
			$atto = $_POST['atto'];
		}
		if(empty($_POST['numero'])){
			die('<script>alert("'.$null_act.'")</script>');
		}else{
			$numero = $_POST['numero'];
			$num_noslash = str_replace('/','.',$numero);
		}
		if(empty($_POST['data_presentazione'])){
			$data_presentazione = "01/01/0001";
			$anno_presentazione = "Non datati";
		}else{
			$data_presentazione = $_POST['data_presentazione'];
			$elab_pdata = DateTime::createFromFormat("d/m/Y", "$data_presentazione");
			$anno_presentazione = $elab_pdata->format("Y");
		}
		if(empty($_POST['data_rilascio'])){
			$data_rilascio = "01/01/0001";
		}else{
			$data_rilascio = $_POST['data_rilascio'];
		}	  
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
		if(empty($_POST['subalterno'])){
			$subalterno = "0";
		}else{
			$subalterno = $_POST['subalterno'];
		}
		if(empty($_POST['oggetto'])){
			$oggetto = "Nessun Oggetto";
		}else{
			$oggetto = $_POST['oggetto'];
		}
		
		//Valori pratica.loc
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
		
		//================================//
		//===   CARICAMENTO ALLEGATI   ===//
		//================================//
		
		//Definizione percorso cartella di archivio
		$percorso = "../atti/".$anno_presentazione."/".$atto."_".$num_noslash;
		//Controllo esistenza
		if(!file_exists($percorso)){
			//Creazione Cartella
			if(!mkdir($percorso, 0777, true)){
				die('<script>alert("'.$mkdir_failed.'")</script>');
			}
		}
		
		//Percorso archiviazione files
		$percorso_file = $percorso."/";
						
		//Elaborazione ricorsiva degli allegati
		foreach($_FILES as $att => $file){
			//Controllo presenza file
			if(!empty($file['name'])){
				//Controllo dimensione file
				if($file['size'] > $max_att_size){
					die('<script>alert("'.$size_err.$file['name'].'")</script>');
				}else{
					//Attribuisce percorso ed estensione
					$filename = $percorso_file.basename($file['name']);
					$infofile = pathinfo($filename);
					//Sposta il file in archivio
					move_uploaded_file($file['tmp_name'], $filename);
					//Rimuove il suffisso "_up" dal nome
					$att_trunc = str_replace('_up','',$att);
					//Rinomina il file in base alla categoria
					rename($filename, $percorso_file.$att_trunc.".".$infofile['extension']);
					$$att_trunc = $infofile['extension'];
				}
			}
		}
		
		//================================//
		//=== INSERIMENTO IN DATABASE  ===//
		//================================//
	
		
		//STABILIMENTO CONNESSIONE AL DATABASE
		$link = sqlsrv_connect($dbserver, $dbdata);
		if($link === false){
			die('<script>alert("'.$link_err.'")</script>');
		}
		//------------------------------
		
		//INSERIMENTO ALLEGATI
		//Query di inserimento allegati
    $att_query = "INSERT INTO pratica.att (presentazione,
																					inizio_lavori,
																					relazione_tec,
																					rilascio)
								VALUES (?, ?, ?, ?);
								SELECT SCOPE_IDENTITY() as attID";
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
		}else{
			//Ottenimento attID
			sqlsrv_next_result($att_stmt);
			sqlsrv_fetch($att_stmt);
			$attID = sqlsrv_get_field($att_stmt, 0);
		}
		//------------------------------
		
		// INSERIMENTO LOCALIZZAZIONE
		//Creazione del valore .loc + hash
		$loc = $indirizzo."&".$civico."_".$foglio."&".$mappale;
		$locmd5 = md5($loc);
		//Query di controllo hash
		$hash_query = "SELECT locID
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
				//Ottieni il locID corrispondente
				sqlsrv_fetch($hash_stmt);
				$locID = sqlsrv_get_field($hash_stmt, 0);
			//Se l'hash non è presente, inserisci la nuova loc
			}else{
				//Query di inserimento loc
				$loc_query = "INSERT INTO pratica.loc (indirizzo,
																							civico,
																							foglio,
																							mappale,
																							hash)
											VALUES (?,?,?,?,?);
											SELECT SCOPE_IDENTITY() as locID";
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
					//Ottenimento locID su nuovo inserimento
					sqlsrv_next_result($loc_stmt);
					sqlsrv_fetch($loc_stmt);
					$locID = sqlsrv_get_field($loc_stmt, 0);
				}
			}
		}
		//------------------------------
		
		//INSERIMENTO DATI
		//Query di inserimento dati
		$dat_query = "INSERT INTO pratica.dat (atto,
																					numero,
																					data_presentazione,
																					data_rilascio,
																					nome,
																					cognome,
																					societa,
																					subalterno,
																					oggetto,
																					loc,
																					att,
																					insertstamp)
									VALUES (?,?,?,?,?,?,?,?,?,?,?,Getdate());
									SELECT SCOPE_IDENTITY() as ID";
		//Parametri di inserimento pratica
		$dat_params = array(
												$atto,
												$numero,
												$data_presentazione,
												$data_rilascio,
												$nome,
												$cognome,
												$societa,
												$subalterno,
												$oggetto,
												$locID,
												$attID
												);
		//Statement di inserimento dati
		$dat_stmt = sqlsrv_query($link, $dat_query, $dat_params);
		//Esecuzione inserimento dati
		if($dat_stmt === false){
			die( print_r( sqlsrv_errors(), true));
		}else{
			//Ottenimento ID
			sqlsrv_next_result($dat_stmt); 
			sqlsrv_fetch($dat_stmt); 
			$ID = sqlsrv_get_field($dat_stmt, 0);
			echo '
	<!-- Modal -->
    <div class="modal fade" id="modalSuccess" role="dialog">
      <div class="modal-dialog" style="margin-top: 20%;">
        <div class="modal-content">
          <div class="modal-header" style="background: #2E353C; padding:15px 15px;">
            <button type="button" class="close" style="color: #f0f0f0" data-dismiss="modal">&times;</button>
            <h3 style="color: #f0f0f0;"><center><span class="fa fa-check"></span> Pratica inserita con successo</center></h3>
            <a href="atto.php?id='.$ID.'">Visualizza</a></li>";
          </div>
        </div>
      </div>
    </div>
			';
			echo '<script>$("#modalSuccess").modal();</script>';
		}
	}else{
		//FA QUALCOSA SE LA CHIAVE E' ERRATA
	}
	//Include pié di pagina
	include 'part/footer.php';
?>