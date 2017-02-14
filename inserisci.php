<?php
	//Definisce la directory base del sito
	$path = $_SERVER['DOCUMENT_ROOT'];
	
	//Include Intestazione
	include $path.'/part/head.php';
?>
		
		<!-- Script locali -->
		<script src="/js/autosize.js" type="application/javascript"></script>
		<script src="/js/carica_file.js" type="application/javascript"></script>
		<script src="/js/moment.js" type="application/javascript"></script>
		<script src="/js/jquery-ui.js" type="application/javascript"></script>
		
		<!-- Controllo dimensione allegati --><script>
			function sizeCheck(id) {
				var input, file;
				input = document.getElementById(id);
				
				file = input.files[0];
					
				if(file.size > <?php echo($max_att_size); ?>) {
					alert("<?php echo($size_err); ?>");
					rimuoviFile(id);
				}
			}
		</script>
<?php
	//Include barra di navigazione
	include $path.'/part/topbar.php';
	
	//Controllo livello di autorizzazione
	if(!isset($_SESSION['livello']) || ($_SESSION['livello'] > 1)){
		//Codice di accesso negato
	
	//Se Autorizzato
	}else{
		//Controllo inserimento tramite invio e chiave
		if(isset($_POST['submit']) && isset($_GET[$chiave])){
			//Stabilisce la connessione al database
			$link = sqlsrv_connect($dbserver, $dbdata);
			if($link === false){
				//Codice in caso di connessione fallita
				
			}else{

				//Legge ed elabora i valori del modulo di inserimento
				empty($_POST['atto']) ? $atto = "NonSpec" : $atto = $_POST['atto'];
				empty($_POST['numero']) ? : ($numero = $_POST['numero']);
				$num_noslash = str_replace('/','.',$numero);
				if(empty($_POST['data_presentazione'])){
					$data_presentazione = "01/01/0001";
					$anno_presentazione = "Non datati";
				}else{
					$data_presentazione = $_POST['data_presentazione'];
					$elab_pdata = DateTime::createFromFormat("d/m/Y", "$data_presentazione");
					$anno_presentazione = $elab_pdata->format("Y");
				}
				empty($_POST['data_rilascio']) ? $data_rilascio = "01/01/0001" : $data_rilascio = $_POST['data_rilascio'];
				empty($_POST['nome']) ? $nome = "X" : $nome = $_POST['nome'];
				empty($_POST['cognome']) ? $cognome = "X" : $cognome = $_POST['cognome'];
				empty($_POST['societa']) ? $societa = "X" : $societa = $_POST['societa'];
				empty($_POST['oggetto']) ? $oggetto = "Nessun Oggetto" : $oggetto = $_POST['oggetto'];
				empty($_POST['indirizzo']) ? $indirizzo = "X" : $indirizzo = $_POST['indirizzo'];
				empty($_POST['civico']) ? $civico = "X" : $civico = $_POST['civico'];
				empty($_POST['foglio']) ? $foglio = "0" : $foglio = $_POST['foglio'];			
				empty($_POST['mappale']) ? $mappale = "0" : $mappale = $_POST['mappale'];
				empty($_POST['subalterno']) ? $subalterno = "0" : $subalterno = $_POST['subalterno'];
				
				//Richiesta verifica unicità della pratica
				$unique_query = "SELECT ID
												FROM pratica.dat
												WHERE numero = ?
												";

				//Esecuzione verifica unicità
				$unique_stmt = sqlsrv_query($link, $unique_query, array($numero,));
				if($unique_stmt === false){
					
					//Codice per errore ricerca
					die('<script>alert("'.$search_err.'")</script>');
					
				}elseif(sqlsrv_has_rows($unique_stmt) === true){
					
					//Codice per pratica già presente
					die('<script>alert("'.$unique_err.'")</script>');
					
				}else{
					
					//Definisce il percorso della cartella di archivio
					$percorso = "D:/atti/".$anno_presentazione."/".$atto."_".$num_noslash;
					//Controlla l'esistenza della cartella
					if(!file_exists($percorso)){
						//Crea la cartella se non presente
						if(!mkdir($percorso, 0777, true)){
							//Termina l'esecuzione se non si crea la cartella
							die('
				<script>
					alert("'.$mkdir_failed.'")
					window.location.replace("'.htmlentities($_SERVER['PHP_SELF']).'");
				</script>');
						}
					}
					
					//Percorso archiviazione files
					$percorso_file = $percorso."/";
									
					//Elabora ricorsivamente gli allegati
					foreach($_FILES as $att => $file){
						//Rimuove il suffisso "_up" dal nome
						$att_trunc = str_replace('_up','',$att);
						//Crea una variabile variabile vuota
						$$att_trunc = NULL;
						//Controlla la presenza file
						if(!empty($file['name'])){
							//Controlla dimensioni file (backup del JS)
							if($file['size'] > $max_att_size){
								die('
				<script>
					window.alert("'.$size_err." ".$file['name'].'")
					window.location.replace("'.htmlentities($_SERVER['PHP_SELF']).'");
				</script>');
							}else{
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
					
					//Richiesta di inserimento allegati
					$att_query = "INSERT INTO pratica.att (pratica,
																								presentazione,
																								inizio_lavori,
																								relazione_tec,
																								rilascio)
												VALUES (?, ?, ?, ?, ?);
												SELECT SCOPE_IDENTITY() as IDatt";
					
					//Parametri di inserimento allegati
					$att_params = array(
															$pratica,
															$presentazione,
															$inizio_lavori,
															$relazione_tec,
															$rilascio
															);
					
					//Statement di inserimento allegati
					$att_stmt = sqlsrv_query($link, $att_query, $att_params);
					//Esecuzione inserimento allegati
					if($att_stmt === false){
						
						//Codice per errore inserimento allegati in db
						die(print_r(sqlsrv_errors()));
						die('<script>alert("'.$att_err.'")</script>');
						
					}else{
						//Ottenimento IDatt
						sqlsrv_next_result($att_stmt);
						sqlsrv_fetch($att_stmt);
						$IDatt = sqlsrv_get_field($att_stmt, 0);

						//Creazione del valore .loc + hash
						$loc = $foglio."&".$mappale;
						$locmd5 = md5($loc);
						
						//Richiesta di controllo hash .loc
						$hash_query = " SELECT IDloc
														FROM pratica.loc
														WHERE hash = ? ";
						
						//Statement di controllo hash + parametri
						$hash_stmt = sqlsrv_query($link, $hash_query, array($locmd5));
						//Esecuzione controllo hash
						if($hash_stmt === false){
							
							//Codice per errore controllo Hash
							die('<script>alert("'.$hash_err.'")</script>');
							
						}elseif(sqlsrv_has_rows($hash_stmt) === true){
								//Ottieni il IDloc corrispondente
								sqlsrv_fetch($hash_stmt);
								$IDloc = sqlsrv_get_field($hash_stmt, 0);
							//Se l'hash non è presente, inserisci la nuova loc
						}else{
							//Query di inserimento loc
							$loc_query = "INSERT INTO pratica.loc (foglio,
																										mappale,
																										hash)
														VALUES (?,?,?);
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
						

						//Query di inserimento dati
						$dat_query = "INSERT INTO pratica.dat (atto,
																									numero,
																									data_presentazione,
																									data_rilascio,
																									nome,
																									cognome,
																									societa,
																									indirizzo,
																									civico,
																									subalterno,
																									oggetto,
																									locID,
																									attID,
																									insertstamp)
													VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,Getdate());
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
																$indirizzo,
																$civico,
																$subalterno,
																$oggetto,
																$IDloc,
																$IDatt
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
					}
				}
				//Chiude la connessione
				sqlsrv_close($link);
			}
		}
	?>
	
		<!-- Backdrop -->
		<div id="page-mask"></div>
		
		<!-- Header di calendario -->
		<div id="material-header-holder" style="display:none">
			<div class="ui-datepicker-material-header">
				<div class="ui-datepicker-material-day"></div>
				<div class="ui-datepicker-material-date">
					<div class="ui-datepicker-material-month"></div>
					<div class="ui-datepicker-material-day-num"></div>
					<div class="ui-datepicker-material-year"></div>
				</div>
			</div>
		</div>
		
		<!-- Intestazione Colore -->
		<div class="section" style="height: 192px; background: #2196F3"></div>
		
		<!-- Sezione dati della pratica -->
		<div class="section">
			<form action="<?php echo htmlentities($_SERVER['PHP_SELF']."?".$chiave); ?>" method="post" enctype="multipart/form-data" class="container" style="margin-top: -96px;">
				<!-- Pannello DATI -->
				<div style="margin-top: 40px; background: #FFF;" class="dp2 panel panel-default">
					<!-- Intestazione Pannello -->
					<div class="panel-heading">
						<h3 class="panel-title">Dati della pratica</h3>
					</div>
					<!-- Corpo del Pannello -->
					<div class="panel-body">
						<!-- Linea #1 | Determinazione specifica/annuale -->
						<div class="row">
							<!-- Tipo di Atto -->
							<div class="form-group col-sm-6">
								<label for='atto'>Tipo di Atto</label>
								<select name="atto" id="atto" class="form-control">
<!--							<option value='PP'>PP. &nbsp;&nbsp;&nbsp;&nbsp;"Piccole Pratiche"</option> -->
									<option value='LE'>LE. &nbsp;&nbsp;&nbsp;&nbsp;Licenza Edilizia</option>
									<option value='AE'>AE. &nbsp;&nbsp;&nbsp;&nbsp;Autorizzazione Edilizia</option>
									<option value='NO'>NO. &nbsp;&nbsp;&nbsp;&nbsp;Nulla osta alla costruzione</option>
									<option value='CE'>CE. &nbsp;&nbsp;&nbsp;&nbsp;Concessione Edilizia</option>
									<option value='PDC'>PDC. &nbsp;&nbsp;&nbsp;Permesso di Costruire (PDC)</option>
									<option value='DIA.PE'>DIA. &nbsp;&nbsp;&nbsp;Denuncia inizio attivit&agrave; (o PE)</option>
									<option value='SCIA'>SCIA. &nbsp;&nbsp;Segnalazione Certificata Inizio Attivit&agrave;</option>
									<option value='L7310'>L7310. &nbsp;Comunicazione Inizio Lavori</option>
									<option value='CIL'>CIL. &nbsp;&nbsp;&nbsp;Comunicazione Inizio Lavori</option>
									<option value='CILA'>CILA. &nbsp;&nbsp;Comunicazione Inizio Lavori Asseverata</option>
								</select>
							</div>
							<!-- Numero di Atto -->
							<div class="form-group col-sm-2">
								<label for='numero'>Atto n&deg;</label>
								<input type="text" class="form-control" name="numero" id="numero" size="8" required>
							</div>
							<!-- Data di Presentazione -->
							<div class="form-group col-sm-2">
								<label for='data_presentazione'>Data di pres.</label>
								<input type="text" name="data_presentazione" id="data_presentazione" class="form-control">
							</div>
							<!-- Data di Rilacio -->
							<div class="form-group col-sm-2">
								<label for='data_rilascio'>Data di rilascio</label>
								<input type="text" name="data_rilascio" id="data_rilascio" class="form-control">
							</div>
						</div>
						<!-- Linea #2 | Inserimento indirizzo -->
						<div class="row">
							<!-- Indirizzo -->
							<div class="form-group col-sm-6">
								<label for="indirizzo">Via/Piazza/Altro</label>
								<input type="text" name="indirizzo" id="indirizzo" class="form-control">
							</div>
							<!-- Civico -->
							<div class="form-group col-sm-2">
								<label for="civico">Civico</label>
								<input type="text" name="civico" id="civico" class="form-control">
							</div>
						</div>
						<!-- Linea #3 | Inerimento dati catastali -->
						<div class="row">
							<!-- Foglio -->
							<div class="form-group col-sm-2">
								<label for="foglio">Foglio</label>
								<input type="text" name="foglio" id="foglio" class="form-control">
							</div>
							<!-- Mappale -->
							<div class="form-group col-sm-2">
								<label for="mappale">Mappale</label>
								<input type="text" name="mappale" id="mappale" class="form-control">
							</div>
							<!-- Subalterno -->
							<div class="form-group col-sm-2">
								<label for="subalterno">Subalterno</label>
								<input type="text" name="subalterno" id="subalterno" class="form-control">
							</div>
						</div>
						<!-- Linea #4| Inerimento dati anagrafici -->
						<div class="row">
							<!-- Nome -->
							<div class="form-group col-sm-4">
								<label for="nome">Nome</label>
								<input type="text" name="nome" id="nome" class="form-control">
							</div>
							<!-- Cognome -->
							<div class="form-group col-sm-4">
								<label for="cognome">Cognome</label>
								<input type="text" name="cognome" id="cognome" class="form-control">
							</div>
							<!-- Società -->
							<div class="form-group col-sm-4">
								<label for="societa">Societ&agrave;</label>
								<input type="text" name="societa" id="societa" class="form-control">
							</div>
						</div>
						<!-- Linea #5| Inerimento oggetto -->
						<div class="row">
							<div class="form-group col-sm-12">
								<label for="oggetto">Oggetto</label>
								<textarea name="oggetto" rows="1" cols="3" id="oggetto" class="form-control" style="padding: 6px 0px;"></textarea>
							</div>
						</div>
					</div>
				</div>
				<!-- Pannello ALLEGATI -->
				<div style="margin-top: 40px; background: #FFF;" class="dp2 panel panel-default">
					<!-- Intestazione Pannello -->
					<div class="panel-heading">
						<h3 class="panel-title">Allegati</h3>
					</div>
					<!-- Corpo del Pannello -->
					<div class="panel-body">
						<div class="row">
<?php				//Controlla lista allegati in config
						foreach($allegabili as $allegato => $desc){
							//Imposta il valore NULL come default 
							$$allegato = NULL;
							//Stampa la sezione di caricamento
							echo('							<!-- Sezione file '.$allegato.' -->
							<div class="col-sm-6">
								<span class="help-block">'.$desc.'</span>
								<div class="row input-group">
									<div class="col-sm-2" style="height:34px;">
										<label class="no-btn btn-primary">
											<i class="material-icons" style="margin: 5px 10px;">file_upload</i>
											<input type="file" name="'.$allegato.'_up" id="'.$allegato.'_up" onchange="sizeCheck(this.id);" style="display: none;">
										</label>
									</div>
									<div class="col-sm-8" style="padding: 0;">
										<input type="text" id="'.$allegato.'_label" class="form-control" disabled>
									</div>
									<div class="col-sm-2" style="height:34px;">
										<label id="'.$allegato.'_del" class="no-btn btn-danger" onclick="rimuoviFile(this.id)">
											<i class="material-icons" style="margin: 5px 10px;">delete_forever</i>
										</label>
									</div>
								</div>
							</div>
');
						}	?>
						</div>
					</div>
				</div>
				<!-- Pannello Pulsanti -->
				<div style="text-align: center; margin: 32px auto 96px auto;">
					<input class="btn btn-primary" type="submit" name="submit">
					<input class="btn btn-default" type="reset" name="reset">
				</div>
			</form>
			<!-- Calendario e selezione data --><script>
				var headerHtml = $("#material-header-holder .ui-datepicker-material-header");

				var changeMaterialHeader = function(header, date) {
					var year   = date.format('YYYY');
					var month  = date.format('MMM');
					var dayNum = date.format('D');
					var isoDay = date.isoWeekday();
					
					var weekday = new Array(7);
					weekday[1] = "Luned\xEC";
					weekday[2] = "Marted\xEC";
					weekday[3] = "Mercoled\xEC";
					weekday[4] = "Gioved\xEC";
					weekday[5] = "Venerd\xEC";
					weekday[6] = "Sabato";
					weekday[7]=  "Domenica";

					$('.ui-datepicker-material-day', header).text(weekday[isoDay]);
					$('.ui-datepicker-material-year', header).text(year);
					$('.ui-datepicker-material-month', header).text(month);
					$('.ui-datepicker-material-day-num', header).text(dayNum);
				};

				$.datepicker._selectDateOverload = $.datepicker._selectDate;
				$.datepicker._selectDate = function(id, dateStr) {
					var target = $(id);
					var inst = this._getInst(target[0]);
					inst.inline = true;
					$.datepicker._selectDateOverload(id, dateStr);
					inst.inline = false;
					this._updateDatepicker(inst);
					
					headerHtml.remove();
					$(".ui-datepicker").prepend(headerHtml);
				};

				$("#data_presentazione").datepicker({
					monthNamesShort: [ "Gen", "Feb", "Mar", "Apr", "Mag", "Giu", "Lug", "Ago", "Set", "Ott", "Nov", "Dic" ],
					monthNames: [ "Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre" ],
					dayNamesMin: [ "Do", "Lu", "Ma", "Me", "Gi", "Ve", "Sa" ],
					dayNamesShort: [ "Dom", "Lun", "Mar", "Mer", "Gio", "Ven", "Sab" ],
					dayNames: [ "Domenica", "Luned&igrave;", "Marted&igrave;", "Mercoled&igrave;", "Gioved&igrave;", "Venered&igrave;", "Sabato" ],
					changeMonth: true,
					changeYear: true,
					showButtonPanel: true,
					closeText: "Chiudi",
					currentText: "Oggi",
					dateFormat: "dd/mm/yy",
					yearRange: '<?php echo($anno0.":".date('Y')); ?>',
					firstDay: 1,
					beforeShow: function() {
						$('#page-mask').fadeIn()
					},
					onSelect: function(date, inst) {
						changeMaterialHeader(headerHtml, moment(date, 'DD/MM/YYYY'));
					},
					onClose: function() {
						$('#page-mask').fadeOut()
					},
				});
				
				$("#data_presentazione").on("focus", function() {
					var date;
					if (this.value == "") {
						date = moment();
					} else {
						date = moment(this.value, 'DD/MM/YYYY');
					}

					$(".ui-datepicker").prepend(headerHtml);
					changeMaterialHeader(headerHtml, moment(date, 'DD/MM/YYYY'));
				});
				
				$("#data_rilascio").datepicker({
					monthNamesShort: [ "Gen", "Feb", "Mar", "Apr", "Mag", "Giu", "Lug", "Ago", "Set", "Ott", "Nov", "Dic" ],
					monthNames: [ "Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre" ],
					dayNamesMin: [ "Do", "Lu", "Ma", "Me", "Gi", "Ve", "Sa" ],
					dayNamesShort: [ "Dom", "Lun", "Mar", "Mer", "Gio", "Ven", "Sab" ],
					dayNames: [ "Domenica", "Luned&igrave;", "Marted&igrave;", "Mercoled&igrave;", "Gioved&igrave;", "Venered&igrave;", "Sabato" ],
					changeMonth: true,
					changeYear: true,
					showButtonPanel: true,
					closeText: "Chiudi",
					currentText: "Oggi",
					dateFormat: "dd/mm/yy",
					yearRange: '<?php echo($anno0.":".date('Y')); ?>',
					firstDay: 1,
					beforeShow: function() {
						$('#page-mask').fadeIn()
					},
					onSelect: function(date, inst) {
						changeMaterialHeader(headerHtml, moment(date, 'DD/MM/YYYY'));
					},
					onClose: function() {
						$('#page-mask').fadeOut()
					},
				});
				
				$("#data_rilascio").on("focus", function() {
					var date;
					if (this.value == "") {
						date = moment();
					} else {
						date = moment(this.value, 'DD/MM/YYYY');
					}

					$(".ui-datepicker").prepend(headerHtml);
					changeMaterialHeader(headerHtml, moment(date, 'DD/MM/YYYY'));
				});
			</script>
			
			<!-- Ridimensionamento campo oggetto --><script>
				autosize(document.querySelectorAll('#oggetto'));
			</script>
		</div>
	<?php
	}
	//Include pié di pagina
	include $path.'/part/footer.php';
?>