<?php include 'head.html';?>

<script>
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

<?php include 'topbar.html';?>

<!-- Cover -->
<div class="full-width" style="margin-top: 34px; background: #11283b; min-height: 125px;">
  <div class="container" style="text-align: center;">
	<h2 style="color: #f0f0f0;">Ricerca atti edilizi</h2>
  </div>
</div>

<!-- Breaking Line -->
<div class="full-width" style="background: #38414A;">
  &nbsp;
</div>

<div class="full-width" style="background: #f0f0f0;">
  &nbsp;
</div>

<!-- Banner -->
<div class="full-width" style="background: #0971aa;">
  <div class="container">
    <a href="placeholder" style="text-decoration: none;">
      <center><h4 style="color: #f0f0f0;">Consulta il database avviando una ricerca specifica, compilando i parametri desiderati</h4></center>
    </a>
  </div>
</div>
<div class="full-width" style="background: #f0f0f0;">
  &nbsp;
</div>

<!-- Sezione del form -->
<section style="background: #f0f0f0;" class="full-width form">
  <form action="cerca.php?vai" method="post" class="container">
    <div class="panel panel-default">
	  <div class="panel-heading">
	    <h3 class="panel-title">Dati dell'atto</h3>
	  </div>
      <div class="panel-body">
        <!--Linea #1 | Determinazione specifica/annuale -->
        <?php $anno0 = 1946; /*Anno minimo di selezione*/ ?>
        <div class="row">
          <div class="form-group col-sm-6">
            <label for='atto'>Tipo di Atto</label>
			<select id='atto' name="atto" class="form-control">
              <option value=''></option>
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
          <div class="form-group col-sm-2">
            <label for='atto_num'>Atto n&deg;</label>
            <input type="text" class="form-control" name="atto_num" size="8">
          </div>
		  <div class="form-group col-sm-2">
            <label for='anno_pres'>Anno di pres.</label>
            <select id="anno_pres" name="anno_pres" class="form-control">
              <option value=""></option>
              <?php
                foreach(range(date('Y'), $anno0) as $anno){
                  echo('<option value="' . $anno . '">' . $anno . '</option>');
                }
              ?>
            </select>
          </div>
          <div class="form-group col-sm-2">
            <label for='anno_ril'>Anno di rilascio</label>
            <select id="anno_ril" name="anno_ril" class="form-control">
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
        <!--Linea #2 | Ricerca per indirizzo -->
        <div class="row">
          <div class="form-group col-sm-6">
            <label for="via">Via/Piazza/Altro</label>
            <input type="text" id="via" name="via" class="form-control">
          </div>
		  <div class="form-group col-sm-2">
            <label for="civico">Civico</label>
            <input type="text" id="civico" name="civico" class="form-control">
          </div>
        </div>
        <br>
        <!--Linea #3 | Ricerca per dati catastali -->
        <div class="row">
          <div class="form-group col-sm-2">
            <label for="foglio">Foglio</label>
            <input type="text" id="foglio" name="foglio" class="form-control">
          </div>
          <div class="form-group col-sm-2">
            <label for="map">Mappale</label>
            <input type="text" id="map" name="map" class="form-control">
          </div>
          <div class="form-group col-sm-2">
            <label for="sub">Subalterno</label>
            <input type="text" id="sub" name="sub" class="form-control">
          </div>
        </div>
        <br>
        <!--Linea #4| Ricerca per dati anagrafici -->
        <div class="row">
          <div class="form-group col-sm-4">
            <label for="nome">Nome</label>
            <input type="text" id="nome" name="nome" class="form-control">
          </div>
          <div class="form-group col-sm-4">
            <label for="cognome">Cognome</label>
            <input type="text" id="cognome" name="cognome" class="form-control">
          </div>
          <div class="form-group col-sm-4">
            <label for="societa">Societ&agrave;</label>
            <input type="text" id="societa" name="societa" class="form-control">
          </div>
        </div>
      </div>
    </div>
	<div style="text-align: center; margin: 50px auto;">
      <input class="btn btn-primary" type="submit" name="submit">
      <a href="http://129.1.0.92/cerca.php" class="btn btn-default">Reimposta</a>
	</div>
  </form>
</section>
	
<?php
	//Controllo l'invio del modulo
	if(isset($_POST['submit'])){
		
		//Controllo del valore ?vai
		if(isset($_GET['vai'])){
			
			//Controllo ed inserimento variabili direttamente in query
			if(empty($_POST['atto'])){$qatto = "atto IS NOT NULL";}else{$atto = $_POST['atto'];$qatto = "atto = '".$atto."'";}
			if(empty($_POST['atto_num'])){$qatto_num = "atto_num IS NOT NULL";}else{$atto_num = $_POST['atto_num'];$qatto_num = "atto_num LIKE '%".$atto_num."%'";}
			
			if(empty($_POST['anno_pres'])){$qanno_pres = "anno_pres IS NOT NULL";}else{$anno_pres = $_POST['anno_pres'];$qanno_pres = "anno_pres = '".$anno_pres."'";}
			if(empty($_POST['anno_ril'])){$qanno_ril = "anno_ril IS NOT NULL";}else{$anno_ril = $_POST['anno_ril'];$qanno_ril = "anno_ril = '".$anno_ril."'";}
			
			if(empty($_POST['via'])){$qvia = "via IS NOT NULL";}else{$via = $_POST['via'];$qvia = "via LIKE '%".$via."%'";}
			if(empty($_POST['civico'])){$qcivico = "civico IS NOT NULL";}else{$civico = $_POST['civico'];$qcivico = "civico LIKE '%".$civico."%'";}
			
			if(empty($_POST['foglio'])){$qfoglio = "foglio IS NOT NULL";}else{$foglio = $_POST['foglio'];$qfoglio = "foglio = '".$foglio."'";}
			if(empty($_POST['map'])){$qmap = "map IS NOT NULL";}else{$map = $_POST['map'];$qmap = "map = '".$map."'";}
			if(empty($_POST['sub'])){$qsub = "sub IS NOT NULL";}else{$sub = $_POST['sub'];$qsub = "sub = '".$sub."'";}
			
			if(empty($_POST['nome'])){$qnome = "nome IS NOT NULL";}else{$nome = $_POST['nome'];$qnome = "nome LIKE '%".$nome."%'";}
			if(empty($_POST['cognome'])){$qcognome = "cognome IS NOT NULL";}else{$cognome = $_POST['cognome'];$qcognome = "cognome LIKE '%".$cognome."%'";}
			if(empty($_POST['societa'])){$qsocieta = "societa IS NOT NULL";}else{$societa = $_POST['societa'];$qsocieta = "societa LIKE '%".$societa."%'";}

			//Include dati e credenziali di connessione al db
			include 'D:/web/query_link.php';
				
			//Selezione del risultato
			$query = "SELECT * FROM ".$tabella." WHERE ".$qatto." AND ".$qatto_num." AND ".$qanno_pres." AND ".$qanno_ril." AND ".$qvia." AND ".$qcivico." AND ".$qfoglio." AND ".$qmap." AND ".$qsub." AND ".$qnome." AND ".$qcognome." AND ".$qsocieta;
			
			//Esecuzione della ricerca
			$trova = mysql_query($query);
			if(!$trova){die ("Errore durante la ricerca : " . mysql_error());}
			
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
			
			//Elaborazione in caso di risultati corrispondenti
			if(mysql_num_rows($trova)>0){
				
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
				while($row = mysql_fetch_array($trova)){
					
					//Assegnazione nuove variabili
					$ID = $row['ID'];
					$atto = $row['atto'];
					$atto_num = $row['atto_num'];
					$num_noslash = $result = str_replace('/','.',$atto_num);
					$anno_pres = $row['anno_pres'];
					$anno_ril = $row['anno_ril'];
					$via = $row['via'];
					$civico = $row['civico'];
					$foglio = $row['foglio'];
					$map = $row['map'];
					$sub = $row['sub'];
					$nome = $row['nome'];
					$cognome = $row['cognome'];
					$societa = $row['societa'];
					$oggetto = $row['oggetto'];
					/* Altri valori eventuali*/
				
					//Stampa delle variabili in tabella
					echo("
            <tr>
              <td>
	            <a href='../atti/".$anno_pres."/".$atto."_".$num_noslash."/atto.php'>
	              <i class='fa fa-folder-open-o' aria-hidden='true'></i>
	            </a>
	          </td>
              <td>".$atto."</td>
              <td>".$atto_num."</td>
              <td>".$oggetto."</td>
            </tr>
					");
				}
				//Stampa chiusura tabella
				echo("
          </tbody>
        </table>
				");
				
			//Elaborazione in caso di nessun risultato
			}else{
				echo("Nessun risultato corrispondente");
			}
			
			//Stampa elementi di chiusura
			echo("
      </div>
    </div>
  </div>
</section>
			");
		}
	}
?>
<?php include 'footer.html';?>