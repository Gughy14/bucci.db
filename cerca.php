<?php /* CONFIGURAZIONI, LOCALIZZAZIONI E COSTANTI*/

	//CONFIGURAZIONI
	$dbserver = "CORE-CJ84\sqlexpress";
	$dbdata = array( "Database"=>"edilizia", "UID"=>"dbmaster", "PWD"=>"6X!PdYncts#n%-jP2PxR4wBN" );
	$chiave = "chiave";
	$anno0 = 1946; /*Anno minimo di selezione*/
	
	//MESSAGGI DI ERRORE
	$link_err = "Errore durante la connessione al database: controllare i parametri!";
	$search_err = "Errore durante la ricerca dei dati.";
?>

<?php /* INTESTAZIONE HTML */
	include 'head.html';
?>
	<script><!-- SortRows -->
		function sortTable(table, col, reverse) {
			var tb = table.tBodies[0], // use `<tbody>` to ignore `<thead>` and `<tfoot>` rows
					tr = Array.prototype.slice.call(tb.rows, 0), // put rows into array
					i;
			reverse = -((+reverse) || -1);
			tr = tr.sort(function (a, b) { // sort rows
					return reverse // `-1 *` if want opposite order
							* (a.cells[col].textContent.trim() // using `.textContent.trim()` for test
									.localeCompare(b.cells[col].textContent.trim())
								);
			});
			for(i = 0; i < tr.length; ++i) tb.appendChild(tr[i]);	// append each row in order
		}
		
		function makeSortable(table) {
			var th = table.tHead, i;
			th && (th = th.rows[0]) && (th = th.cells);
			if (th) i = th.length;
			else return; // if no `<thead>` then do nothing
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
			if(document.getElementById("ogg").className.match(/(?:^|\s)fa-sort-alpha-desc(?!\S)/)){
				document.getElementById("ogg").className = "fa fa-sort-alpha-asc";	
			}else{
				document.getElementById("ogg").className = "fa fa-sort-alpha-desc";	
			}
		}
	</script>
	
	<?php /* CHIUSURA INTESTAZIONE & TOPBAR */
	include 'topbar.html';
?>

<div class="full-width" style="margin-top: 34px; background: #11283b; min-height: 125px;"><!-- Cover -->
	<div class="container" style="text-align: center;">
	<h2 style="color: #f0f0f0;">Ricerca atti edilizi</h2>
	</div>
</div>
<div class="full-width" style="background: #38414A;"><!-- Breaking Line -->
	&nbsp;
</div>
<div class="full-width" style="background: #f0f0f0;"><!-- Fake BG -->
	&nbsp;
</div>
<div class="full-width" style="background: #0971aa;"><!-- Banner -->
	<div class="container">
		<a href="placeholder" style="text-decoration: none;">
			<center><h4 style="color: #f0f0f0;">Consulta il database avviando una ricerca specifica, compilando i parametri desiderati</h4></center>
		</a>
	</div>
</div>

<section style="background: #f0f0f0;" class="full-width form"><!-- Form ricerca dati-->
	<form action="cerca.php?<?php print($chiave); ?>" method="post" class="container">
		<div class="panel panel-default"><!-- Pannello Dati -->
			<div class="panel-heading"><!-- Intestazione Pannello -->
				<h3 class="panel-title">Dati dell'atto</h3>
			</div>
			<div class="panel-body"><!-- Corpo del Pannello -->
				<div class="row"><!--Linea #1 | Determinazione specifica/annuale -->
					<div class="form-group col-sm-6"><!-- Tipo di Atto -->
						<label for='atto'>Tipo di Atto</label>
						<select id='atto' name="atto" class="form-control">
							<option value=""></option>
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
						<input type="text" class="form-control" name="numero" size="8">
					</div>
					<div class="form-group col-sm-2"><!-- Anno di Presentazione -->
						<label for='anno_presentazione'>Anno di pres.</label>
						<select id="anno_presentazione" name="anno_presentazione" class="form-control">
							<option value=""></option>
							<?php
								foreach(range(date('Y'), $anno0) as $anno){
									echo('<option value="' . $anno . '">' . $anno . '</option>');
								}
							?>
						</select>
					</div>
					<div class="form-group col-sm-2"><!-- Anno di Rilascio -->
						<label for='anno_rilascio'>Anno di rilascio</label>
						<select id="anno_rilascio" name="anno_rilascio" class="form-control">
							<option value=""></option>
							<?php
								foreach(range(date('Y'), $anno0) as $anno){
									echo('<option value="' . $anno . '">' . $anno . '</option>');
								}
							?>
						</select>
					</div>
				</div>
				<br>
				<div class="row"><!--Linea #2 | Ricerca per indirizzo e Dati catastali -->
					<div class="form-group col-sm-6"><!-- Indirizzo -->
						<label for="indirizzo">Via/Piazza/Altro</label>
						<input type="text" id="indirizzo" name="indirizzo" class="form-control">
					</div>
					<div class="form-group col-sm-2">
						<label for="civico">Civico</label><!-- Civico -->
						<input type="text" id="civico" name="civico" class="form-control">
					</div>
					<div class="form-group col-sm-2"><!-- Foglio -->
						<label for="foglio">Foglio</label>
						<input type="text" id="foglio" name="foglio" class="form-control">
					</div>
					<div class="form-group col-sm-2"><!-- Mappale -->
						<label for="mappale">Mappale</label>
						<input type="text" id="mappale" name="mappale" class="form-control">
					</div>
				</div>
				<br>
				<div class="row"><!--Linea #4| Ricerca per dati anagrafici -->
					<div class="form-group col-sm-4"><!-- Nome -->
						<label for="nome">Nome</label>
						<input type="text" id="nome" name="nome" class="form-control">
					</div>
					<div class="form-group col-sm-4"><!-- Cognome -->
						<label for="cognome">Cognome</label>
						<input type="text" id="cognome" name="cognome" class="form-control">
					</div>
					<div class="form-group col-sm-4"><!-- societa -->
						<label for="societa">Societ&agrave;</label>
						<input type="text" id="societa" name="societa" class="form-control">
					</div>
				</div>
			</div>
		</div>
		<div style="text-align: center; margin: 50px auto;"><!-- Pannello Pulsanti -->
			<input class="btn btn-primary" type="submit" name="submit">
			<a href="http://129.1.0.92/cerca.php" class="btn btn-default">Reimposta</a>
		</div>
	</form>
</section>
	
<?php
	//Controllo inserimento tramite invio e chiave
	if(isset($_POST['submit']) && isset($_GET[$chiave])){
		
		//================================//
		//===   LETTURA VALORI FORM    ===//
		//================================//
		if(empty($_POST['atto'])){
			$atto = "%";
		}else{
			$atto = $_POST['atto'];
		}	
		if(empty($_POST['numero'])){
			$numero = "%";
		}else{
			$numero = "%".$_POST['numero']."%";
		}
		if(empty($_POST['anno_presentazione'])){
			$anno_presentazione = "%";
		}else{
			$anno_presentazione = "%".$_POST['anno_presentazione']."%";
		}
		if(empty($_POST['anno_rilascio'])){
			$anno_rilascio = "%";
		}else{
			$anno_rilascio = "%".$_POST['anno_rilascio']."%";
		}
		if(empty($_POST['indirizzo'])){
			$indirizzo = "%";
		}else{
			$indirizzo = "%".$_POST['indirizzo']."%";
		}
		if(empty($_POST['civico'])){
			$civico = "%";
		}else{
			$civico = "%".$_POST['civico']."%";
		}
		if(empty($_POST['foglio'])){
			$foglio = "%";
		}else{
			$foglio = "%".$_POST['foglio']."%";
		}
		if(empty($_POST['mappale'])){
			$mappale = "%";
		}else{
			$mappale = "%".$_POST['mappale']."%";
		}
		if(empty($_POST['nome'])){
			$nome = "%";
		}else{
			$nome = "%".$_POST['nome']."%";
		}
		if(empty($_POST['cognome'])){
			$cognome = "%";
		}else{
			$cognome = "%".$_POST['cognome']."%";
		}
		if(empty($_POST['societa'])){
			$societa = "%";
		}else{
			$societa = "%".$_POST['societa']."%";
		}
		
		//STABILIMENTO CONNESSIONE AL DATABASE
		$link = sqlsrv_connect($dbserver, $dbdata);
		if($link === false){
			die('<script>alert("'.$link_err.'")</script>');
		}
		//------------------------------
		
		$search_query = "SELECT atto, numero, data_presentazione, oggetto
										FROM pratica.dat
										JOIN pratica.loc
										ON loc = locID
										WHERE atto LIKE ?
										AND numero LIKE ?
										AND data_presentazione LIKE ?
										AND data_rilascio LIKE ?
										AND indirizzo LIKE ?
										AND civico LIKE ?
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
												$nome,
												$cognome,
												$societa
												);
		//Esecuzione inserimento loc
		$search_stmt = sqlsrv_query($link, $search_query, $search_params);
		
		if($search_stmt === false){
			die( print_r( sqlsrv_errors(), true));
			die('<script>alert("'.$search_err.'")</script>');
		}
		
		//Stampa della sezione risultati
		echo("
<section class='full-width' style='background: #f0f0f0; margin-bottom: 42px;' id='results_list'>
	<div class='container'>
		<div class='panel panel-default'>
			<div class='panel-heading'>
				<h3 class='panel-title'>Risultato della ricerca</h3>
			</div>
			<div class='panel-body'>
			");
			
			//Elaborazione in base al numero di risultati
			if(sqlsrv_has_rows($search_stmt) === false){
				echo("Nessun risultato corrispondente");
			}else{
				//Stampa della tabella di contenimento
				echo("
				<table class='table table-responsive table-hover table-striped'>
					<thead>
						<tr>
							<th>File</th>
							<th>Atto</th>
							<th>Numero</th>
							<th onclick='sort_ogg()'>
								<i id='ogg' class='fa fa-sort-alpha-desc' aria-hidden='true'></i>
								&nbsp; Oggetto
							</th>				
						</tr>
					</thead>
          <tbody>
				");
				
				//Elaborazione ricorsiva dei risultati
				while($row = sqlsrv_fetch_array($search_stmt, SQLSRV_FETCH_ASSOC)){
					
					//Assegnazione ed elaborazione variabili
					$atto = $row['atto'];
					$numero = $row['numero'];
					$num_noslash = str_replace('/','.',$numero);
					$data_presentazione = $row['data_presentazione'];
					$anno_presentazione = $data_presentazione->format("Y");
					$oggetto = $row['oggetto'];
					
					//Stampa delle variabili in tabella
					echo("
            <tr>
              <td>
	            <a href='../atti/".$anno_presentazione."/".$atto."_".$num_noslash."/atto.php'>
	              <i class='fa fa-folder-open-o' aria-hidden='true'></i>
	            </a>
	          </td>
              <td>".$atto."</td>
              <td>".$numero."</td>
              <td>".$oggetto."</td>
            </tr>
					");
				}
				
				//Stampa chiusura tabella
				echo("
          </tbody>
        </table>
				");
			}
			
			//Stampa elementi di chiusura
			echo("
      </div>
    </div>
  </div>
</section>
			");
	}
?>
<?php include 'footer.html';?>