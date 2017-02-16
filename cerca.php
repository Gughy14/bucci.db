<?php
	//Definisce la directory base del sito
	$path = $_SERVER['DOCUMENT_ROOT'];
	
	//Include Intestazione
	include $path.'/part/head.php';
?>
		
		<link rel="stylesheet" type="text/css" href="/css/dataTables.css">
		<script type="text/javascript" charset="utf-8" src="/js/jquery.dataTables.min.js"></script>
		
		<script>
		
			function sortNumbersIgnoreText(a, b, high) {
				var reg = /[+-]?((\d+(\.\d*)?)|\.\d+)([eE][+-]?[0-9]+)?/;    
				a = a.match(reg);
				a = a !== null ? parseFloat(a[0]) : high;
				b = b.match(reg);
				b = b !== null ? parseFloat(b[0]) : high;
				return ((a < b) ? -1 : ((a > b) ? 1 : 0));    
			}
		
		jQuery.extend( jQuery.fn.dataTableExt.oSort, {
			"sort-numbers-ignore-text-asc": function (a, b) {
        return sortNumbersIgnoreText(a, b, Number.POSITIVE_INFINITY);
			},
			"sort-numbers-ignore-text-desc": function (a, b) {
        return sortNumbersIgnoreText(a, b, Number.NEGATIVE_INFINITY) * -1;
			}
		});
		
			$(document).ready( function () {
				$('#risultati-tab').DataTable({
					"language": {
            "lengthMenu": "Mostra _MENU_ risultati per pagina",
            "info":	"Pagina _PAGE_ di _PAGES_",
            "infoEmpty": "Nessun risultato",
            "infoFiltered": "(_MAX_ risultati nascosti)",
						"search":	"Filtra:",
						"zeroRecords":	"Nessun risultato corrispondente ai parametri di ricerca",
						"paginate": {
							"first":		"Prima",
							"last":			"Ultima",
							"next": 	  "&#xE5CC;",
							"previous":	"&#xE5CB;"
						},
					},
					columnDefs: [
						{type: 'sort-numbers-ignore-text', targets : 2 },
					],
					"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tutti"]]
				});
			});
		</script>
		
		
		
		
		
<?php
	//Include barra di navigazione
	include $path.'/part/topbar.php';

	//Controlla livello di autorizzazione
	if(!isset($_SESSION['livello']) || ($_SESSION['livello'] > 2)){
		//Codice di accesso negato
	
	//Se Autorizzato
	}else{ ?>
		
		<!-- Intestazione Colore -->
		<div class="section material"></div>
		
		<!-- Sezione dati di ricerca -->
		<div class="section">
			<form action="<?php echo htmlentities($_SERVER['PHP_SELF']."?".$chiave); ?>" method="post" class="container">
				<div class="dp2 panel panel-default first-panel">
					<!-- Intestazione Pannello -->
					<div class="panel-heading">
						<p class="panel-title">Dati dell'atto</p>
					</div>
					<!-- Corpo del Pannello -->
					<div class="panel-body">
						<!--Linea #1 | Determinazione specifica/annuale -->
						<div class="row">
							<!-- Tipo di Atto -->
							<div class="form-group col-sm-6">
								<label for='atto'>Tipo di Atto</label>
								<select id='atto' name="atto" class="form-control">
									<option value='' <?php echo((isset($_POST['atto']) && $_POST['atto'] == '') ? 'selected' : NULL); ?>>&nbsp;</option>
<!--							<option value='PP' <?php echo((isset($_POST['atto']) && $_POST['atto'] == 'PE') ? 'selected' : NULL); ?>>"Piccole Pratiche"</option>  -->
									<option value='LE' <?php echo((isset($_POST['atto']) && $_POST['atto'] == 'LE') ? 'selected' : NULL); ?>>Licenza Edilizia</option>
									<option value='AE' <?php echo((isset($_POST['atto']) && $_POST['atto'] == 'AE') ? 'selected' : NULL); ?>>Autorizzazione Edilizia</option>
									<option value='NO' <?php echo((isset($_POST['atto']) && $_POST['atto'] == 'NO') ? 'selected' : NULL); ?>>Nulla osta alla costruzione</option>
									<option value='CE' <?php echo((isset($_POST['atto']) && $_POST['atto'] == 'CE') ? 'selected' : NULL); ?>>Concessione Edilizia</option>
									<option value='PDC' <?php echo((isset($_POST['atto']) && $_POST['atto'] == 'PDC') ? 'selected' : NULL); ?>>Permesso di Costruire (PDC)</option>
									<option value='DIA.PE' <?php echo((isset($_POST['atto']) && $_POST['atto'] == 'DIA.PE') ? 'selected' : NULL); ?>>Denuncia inizio attivit&agrave; (DIA o PE)</option>
									<option value='SCIA' <?php echo((isset($_POST['atto']) && $_POST['atto'] == 'SCIA') ? 'selected' : NULL); ?>>Segnalazione Certificata Inizio Attivit&agrave; (SCIA)</option>
									<option value='L7310' <?php echo((isset($_POST['atto']) && $_POST['atto'] == 'L7310') ? 'selected' : NULL); ?>>Comunicazione Inizio Lavori (L7310)</option>
									<option value='CIL' <?php echo((isset($_POST['atto']) && $_POST['atto'] == 'CIL') ? 'selected' : NULL); ?>>Comunicazione Inizio Lavori (CIL)</option>
									<option value='CILA' <?php echo((isset($_POST['atto']) && $_POST['atto'] == 'CILA') ? 'selected' : NULL); ?>>Comunicazione Inizio Lavori Asseverata (CILA)</option>
								</select>
							</div>
							<!-- Numero di Atto -->
							<div class="form-group col-sm-2">
								<label for='numero'>Atto n&deg;</label>
								<input type="text" class="form-control" id="numero" name="numero" size="8" <?php echo(!isset($_POST['numero']) ? NULL : 'value="'.$_POST['numero'].'"'); ?>>
							</div>
							<!-- Anno di Presentazione -->
							<div class="form-group col-sm-2">
								<label for='anno_presentazione'>Anno di pres.</label>
								<select id="anno_presentazione" name="anno_presentazione" class="form-control">
									<option value="">&nbsp;</option>
<?php							foreach(range(date('Y'), $anno0) as $anno){
										echo('									<option value="'.$anno.'" '.((isset($_POST['anno_presentazione']) && $_POST['anno_presentazione'] == $anno) ? 'selected' : NULL).'>'.$anno.'</option>
');								} ?>
								</select>
							</div>
							<!-- Anno di Rilascio -->
							<div class="form-group col-sm-2">
								<label for='anno_rilascio'>Anno di rilascio</label>
								<select id="anno_rilascio" name="anno_rilascio" class="form-control">
									<option value="">&nbsp;</option>
<?php							foreach(range(date('Y'), $anno0) as $anno){
										echo('									<option value="'.$anno.'" '.((isset($_POST['anno_rilascio']) && $_POST['anno_rilascio'] == $anno) ? 'selected' : NULL).'>'.$anno.'</option>
');								} ?>
								</select>
							</div>
						</div>
						<!--Linea #2 | Ricerca per indirizzo e Dati catastali -->
						<div class="row">
							<!-- Indirizzo -->
							<div class="form-group col-sm-6">
								<label for="indirizzo">Via/Piazza/Altro</label>
								<input type="text" id="indirizzo" name="indirizzo" class="form-control" <?php echo(!isset($_POST['indirizzo']) ? NULL : 'value="'.$_POST['indirizzo'].'"'); ?>>
							</div>
							<!-- Civico -->
							<div class="form-group col-sm-2">
								<label for="civico">Civico</label>
								<input type="text" id="civico" name="civico" class="form-control" <?php echo(!isset($_POST['civico']) ? NULL : 'value="'.$_POST['civico'].'"'); ?>>
							</div>
							<!-- Foglio -->
							<div class="form-group col-sm-2">
								<label for="foglio">Foglio</label>
								<input type="text" id="foglio" name="foglio" class="form-control" <?php echo(!isset($_POST['foglio']) ? NULL : 'value="'.$_POST['foglio'].'"'); ?>>
							</div>
							<!-- Mappale -->
							<div class="form-group col-sm-2">
								<label for="mappale">Mappale</label>
								<input type="text" id="mappale" name="mappale" class="form-control" <?php echo(!isset($_POST['mappale']) ? NULL : 'value="'.$_POST['mappale'].'"'); ?>>
							</div>
						</div>
						<!--Linea #4| Ricerca per dati anagrafici -->
						<div class="row">
							<!-- Nome -->
							<div class="form-group col-sm-4">
								<label for="nome">Nome</label>
								<input type="text" id="nome" name="nome" class="form-control" <?php echo(!isset($_POST['nome']) ? NULL : 'value="'.$_POST['nome'].'"'); ?>>
							</div>
							<!-- Cognome -->
							<div class="form-group col-sm-4">
								<label for="cognome">Cognome</label>
								<input type="text" id="cognome" name="cognome" class="form-control" <?php echo(!isset($_POST['cognome']) ? NULL : 'value="'.$_POST['cognome'].'"'); ?>>
							</div>
							<!-- Società -->
							<div class="form-group col-sm-4">
								<label for="societa">Societ&agrave;</label>
								<input type="text" id="societa" name="societa" class="form-control" <?php echo(!isset($_POST['societa']) ? NULL : 'value="'.$_POST['societa'].'"'); ?>>
							</div>
						</div>
					</div>
				</div>
				<!-- Pannello Pulsanti -->
				<div style="text-align: center;">
					<button type="submit" name="submit" class="btn btn-primary">CERCA</button>
					<button type="reset" name="reset" class="btn" onclick="window.location.href='<?php echo htmlentities($_SERVER['PHP_SELF']); ?>'">REIMPOSTA</button>
				</div>
			</form>
		</div>
<?php
		//Controllo inserimento tramite invio e chiave
		if(isset($_POST['submit']) && isset($_GET[$chiave])){
			
			//Legge ed elabora i valori del modulo di ricerca
			empty($_POST['atto']) ? $atto = "%" : $atto = $_POST['atto'];
			empty($_POST['numero']) ? $numero = "%" : $numero = "%".$_POST['numero']."%";
			empty($_POST['anno_presentazione']) ? $anno_presentazione = "%" : $anno_presentazione = "%".$_POST['anno_presentazione']."%";
			empty($_POST['anno_rilascio']) ? $anno_rilascio = "%" : $anno_rilascio = "%".$_POST['anno_rilascio']."%";
			empty($_POST['indirizzo']) ? $indirizzo = "%" : $indirizzo = "%".$_POST['indirizzo']."%";
			empty($_POST['civico']) ? $civico = "%" : $civico = "%".$_POST['civico']."%";
			empty($_POST['foglio']) ? $foglio = "%" : $foglio = $_POST['foglio'];
			empty($_POST['mappale']) ? $mappale = "%" : $mappale = $_POST['mappale'];
			empty($_POST['nome']) ? $nome = "%" : $nome = "%".$_POST['nome']."%";
			empty($_POST['cognome']) ? $cognome = "%" : $cognome = "%".$_POST['cognome']."%";
			empty($_POST['societa']) ? $societa = "%" : $societa = "%".$_POST['societa']."%";
			
			//Stabilisce la connessione al database
			$link = sqlsrv_connect($dbserver, $dbdata);
			if($link === false){
				
				//Codice in caso di connessione fallita
				
			}else{
				//Query di ricerca
				$search_query = "SELECT ID, atto, numero, data_presentazione, oggetto
												FROM pratica.dat
												JOIN pratica.loc
												ON locID = IDloc
												WHERE atto LIKE ?
												AND numero LIKE ?
												AND data_presentazione LIKE ?
												AND data_rilascio LIKE ?
												AND indirizzo LIKE ?
												AND civico LIKE ?
												AND foglio LIKE ?
												AND mappale LIKE ?
												AND nome LIKE ?
												AND cognome LIKE ?
												AND societa LIKE ?
												";
				//Parametri di ricerca
				$search_params = array(
																$atto,
																$numero,
																$anno_presentazione,
																$anno_rilascio,
																$indirizzo,
																$civico,
																$foglio,
																$mappale,
																$nome,
																$cognome,
																$societa
															);
				//Esecuzione ricerca
				$search_stmt = sqlsrv_query($link, $search_query, $search_params);
				if($search_stmt === false){
					//Codice in caso di errore di ricerca
					
				}else{
					//Stampa della sezione risultati
					echo("
		<!-- Sezione risultati della ricerca -->
		<div class='section' id='results_list'>
			<div class='container'>
				<div class='dp2 panel panel-default'>
					<!-- Intestazione Pannello -->
					<div class='panel-heading'>
						<h3 class='panel-title'>Risultato della ricerca</h3>
					</div>
					<!-- Corpo Pannello -->
					<div class='panel-body'>");
						
						//Elaborazione in base al numero di risultati
						if(sqlsrv_has_rows($search_stmt) === false){
							echo("Nessun risultato corrispondente");
						}else{
							//Stampa della tabella di contenimento
							echo("
						<table id='risultati-tab' class='table table-striped table-hover'>
							<!-- Intestazione Tabella --><thead>
								<tr>
									<th style='width: 32px;'>File</th>
									<th style='width: 32px;'>Atto</th>
									<th style='width: 80px;'>Num.</th>		
									<th>Oggetto</th>				
								</tr>
							</thead>
							<!-- Corpo Tabella --><tbody>");
							
							//Elaborazione ricorsiva dei risultati
							while($row = sqlsrv_fetch_array($search_stmt, SQLSRV_FETCH_ASSOC)){
								
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
								<!-- Pratica ID $ID--><tr>
									<td style='text-align: center;'>
										<a href='/atto.php?id=".$ID."'>
											<i class='material-icons'>attachment</i>
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
						</table>");
						}
						
						//Stampa elementi di chiusura
						echo("
					</div>
				</div>
			</div>
		</div>
");
				}
				//Chiude la connessione
				sqlsrv_close($link);
			}
		}
	}
	
	//Include pié di pagina
	include $path.'/part/footer.php';
 ?>