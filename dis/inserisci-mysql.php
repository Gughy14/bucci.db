<?php include 'head.html';?>

	<!-- Ritorno nome file caricato -->
	<script>
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

	<!-- Modal Success -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

	<!-- Messaggi di errore -->
<?php
	$null_act = "Il numero di atto non può essere omesso";
	$mkdir_failed = "Si è verificato un errore durante la creazione della cartella in archivio";
	$arch_err = "Si è verificato un errore durante l'archiviazione del file";
	$up_err = "Si è verificato un errore durante il caricamento del file";
	$size_err = "Impossibile caricare il file poiché di dimensioni superiori a 10MB: ";
	$index_err = "Si è verificato un errore durante la creazione della copertina della pratica";
?>

<?php include 'topbar.html';?>

<!-- Cover -->
<div class="full-width" style="margin-top: 34px; background: #11283b; min-height: 125px;">
  <div class="container" style="text-align: center;">
	<h2 style="color: #f0f0f0;">Inserisci un nuovo atto</h2>
  </div>
</div>

<!-- Breaking Line -->
<div class="full-width" style="background: #38414A;">
  &nbsp;
</div>

<!-- Banner -->
<div class="full-width" style="background: #f0f0f0;">
  &nbsp;
</div>
<div class="full-width" style="background: #0971aa;">
  <div class="container">
    <a href="placeholder" style="text-decoration: none;">
      <center><h4 style="color: #f0f0f0;">Aggiungi un nuovo atto al database, compilando i campi sottostanti</h4></center>
    </a>
  </div>
</div>
<div class="full-width" style="background: #f0f0f0;">
  &nbsp;
</div>

<!-- Sezione del form -->
<section style="background: #f0f0f0;" class="full-width form">
  <form action="inserisci.php?vai" method="post" enctype="multipart/form-data" class="container">
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
        <!--Linea #2 | Inserimento indirizzo -->
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
        <!--Linea #3 | Inerimento dati catastali -->
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
        <!--Linea #4| Inerimento dati anagrafici -->
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
        <br>
		<!--Linea #5 | Inserimento oggetto -->
		<div class="form-group">
          <label for="oggetto">Oggetto</label>
          <textarea id="oggetto" name="oggetto" rows="4" cols="75" class="form-control"></textarea>
        </div>
      </div>
    </div>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Allegati</h3>
      </div>
	  <div class="panel-body">
        <div class="col-lg-6 col-sm-5 col-10">
          <span class="help-block">Modulo di presentazione</span>
          <div class="input-group">
            <label class="input-group-btn">
              <span class="btn btn-primary">
                Carica&hellip; <input type="file" name="presentazione" id="presentazione" style="display: none;">
              </span>
            </label>
            <input type="text" class="form-control" readonly>
          </div>
        </div>
        <div class="col-lg-6 col-sm-5 col-10">
          <span class="help-block">Comunicazione Inizio lavori</span>
          <div class="input-group">
            <label class="input-group-btn">
              <span class="btn btn-primary">
                Carica&hellip; <input type="file" name="inizio_lavori" id="inizio_lavori" style="display: none;">
              </span>
            </label>
            <input type="text" class="form-control" readonly>
          </div>
        </div>
        <div class="col-lg-6 col-sm-5 col-10">
          <span class="help-block">Relazione Tecnica</span>
          <div class="input-group">
            <label class="input-group-btn">
              <span class="btn btn-primary">
                Carica&hellip; <input type="file" name="relazione_tec" id="relazione_tec" style="display: none;">
              </span>
            </label>
            <input type="text" class="form-control" readonly>
          </div>
        </div>
        <div class="col-lg-6 col-sm-5 col-10">
          <span class="help-block">Documento di Rilascio</span>
          <div class="input-group">
            <label class="input-group-btn">
              <span class="btn btn-primary">
                Carica&hellip; <input type="file" name="rilascio" id="rilascio" style="display: none;">
              </span>
            </label>
            <input type="text" class="form-control" readonly>
          </div>
        </div>
	  </div>
    </div>
	<div style="text-align: center; margin: 50px auto;">
      <input class="btn btn-primary" type="submit" name="submit">
      <input class="btn btn-default" type="reset" name="reset">
    </div>
  </form>
</section>
<?php
if(isset($_POST['submit'])){
	//Controllo del valore ?vai
	if(isset($_GET['vai'])){
		
		//Controllo ed inserimento variabili direttamente in query
		if(empty($_POST['atto'])){
			$qatto = "'NonSpec'";
		}else{
			$atto = $_POST['atto'];
			$qatto = "'".$atto."'";
		}
		if(empty($_POST['atto_num'])){
			$qatto_num = "NULL";
			die('<script>alert("'.$null_act.'")</script>');
		}else{
			$qatto_num = "'".$_POST['atto_num']."'";
			$atto_num = $_POST['atto_num'];
			$num_noslash = str_replace('/','.',$atto_num);
		}
		
		if(empty($_POST['anno_pres'])){
			$qanno_pres = "'0000'";
		}else{
			$qanno_pres = "'".$_POST['anno_pres']."'";
			$anno_pres = $_POST['anno_pres'];
		}
		if(empty($_POST['anno_ril'])){
			$qanno_ril = "0000";
		}else{
			$qanno_ril = "'".$_POST['anno_ril']."'";
		}
		
		if(empty($_POST['via'])){
			$qvia = "'Non Specificato'";
		}else{
			$qvia = "'".$_POST['via']."'";
			$via = $_POST['via'];
		}
		if(empty($_POST['civico'])){
			$qcivico = "'ND'";
		}else{
			$qcivico = "'".$_POST['civico']."'";
			$civico = $_POST['civico'];
		}
		
		if(empty($_POST['foglio'])){
			$qfoglio = "'0'";
		}else{
			$qfoglio = "'".$_POST['foglio']."'";
			$foglio = $_POST['foglio'];
		}
		if(empty($_POST['map'])){
			$qmap = "'0'";
		}else{
			$qmap = "'".$_POST['map']."'";
			$map = $_POST['map'];
		}
		if(empty($_POST['sub'])){
			$qsub = "'0'";
		}else{
			$qsub = "'".$_POST['sub']."'";
			$sub = $_POST['sub'];
		}
		
		if(empty($_POST['nome'])){
			$qnome = "'X'";
		}else{
			$qnome = "'".$_POST['nome']."'";
			$nome = $_POST['nome'];}
		if(empty($_POST['cognome'])){
			$qcognome = "'X'";
		}else{
			$qcognome = "'".$_POST['cognome']."'";
			$cognome = $_POST['cognome'];
		}
		if(empty($_POST['societa'])){
			$qsocieta = "'X'";
		}else{
			$qsocieta = "'".$_POST['societa']."'";
			$societa = $_POST['societa'];
		}
		
		if(empty($_POST['oggetto'])){
			$qoggetto = "'Nessun Oggetto'";
		}else{	
			$oggetto = addslashes($_POST['oggetto']);
			$qoggetto = "'".$oggetto."'";
		}
		
		//Include dati e credenziali di connessione al db
		include 'D:/web/edit_link.php';
		
		//Creazione della cartella in archivio
		$percorso = "../atti/".$anno_pres."/".$atto."_".$num_noslash;
		
		if(!file_exists($percorso)){
			if(!mkdir($percorso, 0777, true)){
				die('<script>alert("'.$mkdir_failed.'")</script>');
			}
		}
		
		/*======CARICAMENTO ALLEGATI======*/
		
		//Percorso archiviazione files
		$percorso_file = "../atti/".$anno_pres."/".$atto."_".$num_noslash."/";
		
		//Assegnazione variabile per file
		$file_presentazione = $percorso_file.basename($_FILES['presentazione']['name']);
		$file_inizio_lavori = $percorso_file.basename($_FILES['inizio_lavori']['name']);
		$file_relazione_tec = $percorso_file.basename($_FILES['relazione_tec']['name']);
		$file_rilascio = $percorso_file.basename($_FILES['rilascio']['name']);
		
		//Controllo dimensione presentazione
		if($_FILES['presentazione']['size'] < 10000000){
			//Trasferimento documento da temp ad archivio atti
			if(move_uploaded_file($_FILES['presentazione']['tmp_name'], $file_presentazione)){
				//Ottenimento estensione originaria del file
				$info_file_pres = pathinfo($file_presentazione);
				//Archivio definitivo del documento con nome ed estensione corretta
				if(rename($file_presentazione, $percorso_file."presentazione.".$info_file_pres['extension'])){
					$qa_pres = "'1'";
					$qext_pres = "'".$info_file_pres['extension']."'";
				}else{
					die('<script>alert("'.$arch_err.': presentazione.'.$info_file_pres['extension'].'")</script>');
				}
			}elseif(!isset($_FILES['presentazione']) || !is_uploaded_file($_FILES['presentazione']['tmp_name'])){
				$qa_pres = "'0'";
				$qext_pres = "NULL";
			}else{
				die('<script>alert("'.$up_err.': presentazione.'.$info_file_pres['extension'].'")</script>');
			}
		}else{
			die('<script>alert("'.$size_err.$_FILES['presentazione']['name'].'")</script>');
		}
		
		//Controllo dimensione inizio lavori
		if($_FILES['inizio_lavori']['size'] < 10000000){
			//Trasferimento documento da temp ad archivio atti
			if(move_uploaded_file($_FILES['inizio_lavori']['tmp_name'], $file_inizio_lavori)){
				//Ottenimento estensione originaria del file
				$info_file_il = pathinfo($file_inizio_lavori);
				//Archivio definitivo del documento con nome ed estensione corretta
				if(rename($file_inizio_lavori, $percorso_file."inizio_lavori.".$info_file_il['extension'])){
					$qa_il = "'1'";
					$qext_il = "'".$info_file_il['extension']."'";
				}else{
					die('<script>alert("'.$arch_err.': inizio_lavori.'.$info_file_il['extension'].'")</script>');
				}
			}elseif(!isset($_FILES['inizio_lavori']) || !is_uploaded_file($_FILES['inizio_lavori']['tmp_name'])){
				$qa_il = "'0'";
				$qext_il = "NULL";
			}else{
				die('<script>alert("'.$up_err.': inizio_lavori.'.$info_file_il['extension'].'")</script>');
			}
		}else{
			die('<script>alert("'.$size_err.$_FILES['inizio_lavori']['name'].'")</script>');
		}
		
		//Controllo autorizzazione e caricamento relazione tecnica
		if($_FILES['relazione_tec']['size'] < 10000000){
			//Trasferimento documento da temp ad archivio atti
			if(move_uploaded_file($_FILES['relazione_tec']['tmp_name'], $file_relazione_tec)){
				//Ottenimento estensione originaria del file
				$info_file_rt = pathinfo($file_relazione_tec);
				//Archivio definitivo del documento con nome ed estensione corretta
				if(rename($file_relazione_tec, $percorso_file."relazione_tecnica.".$info_file_rt['extension'])){
					$qa_rt = "'1'";
					$qext_rt = "'".$info_file_rt['extension']."'";
				}else{
					die('<script>alert("'.$arch_err.': relazione_tecnica.'.$info_file_rt['extension'].'")</script>');
				}
			}elseif(!isset($_FILES['relazione_tec']) || !is_uploaded_file($_FILES['relazione_tec']['tmp_name'])){
				$qa_rt = "'0'";
				$qext_rt = "NULL";
			}else{
				die('<script>alert("'.$up_err.': relazione_tecnica.'.$info_file_rt['extension'].'")</script>');
			}
		}else{
			die('<script>alert("'.$size_err.$_FILES['relazione_tec']['name'].'")</script>');
		}
		
		//Controllo autorizzazione e caricamento rilascio
		if($_FILES['rilascio']['size'] < 10000000){
			//Trasferimento documento da temp ad archivio atti
			if(move_uploaded_file($_FILES['rilascio']['tmp_name'], $file_rilascio)){
				//Ottenimento estensione originaria del file
				$info_file_ril = pathinfo($file_rilascio);
				//Archivio definitivo del documento con nome ed estensione corretta
				if(rename($file_rilascio, $percorso_file."rilascio.".$info_file_ril['extension'])){
					$qa_ril = "'1'";
					$qext_ril = "'".$info_file_ril['extension']."'";
				}else{
					die('<script>alert("'.$arch_err.': rilascio.'.$info_file_ril['extension'].'")</script>');
				}
			}elseif(!isset($_FILES['rilascio']) || !is_uploaded_file($_FILES['rilascio']['tmp_name'])){
				$qa_ril = "'0'";
				$qext_ril = "NULL";
			}else{
				die('<script>alert("'.$up_err.': rilascio.'.$info_file_ril['extension'].'")</script>');
			}
		}else{
			die('<script>alert("'.$size_err.$_FILES['rilascio']['name'].'")</script>');
		}
		//------------------------------
		
		//Stringa da inserire in database
		$query = "INSERT INTO ".$tabella." (`atto`, `atto_num`, `anno_pres`, `anno_ril`, `via`, `civico`, `foglio`, `map`, `sub`, `cognome`, `nome`, `societa`, `oggetto`, `a_pres`, `a_il`, `a_rt`, `a_ril`, `ext_pres`, `ext_il`, `ext_rt`, `ext_ril`) VALUES (".$qatto.", ".$qatto_num.", ".$qanno_pres.", ".$qanno_ril.", ".$qvia.", ".$qcivico.", ".$qfoglio.", ".$qmap.", ".$qsub.", ".$qcognome.", ".$qnome.", ".$qsocieta.", ".$qoggetto.", ".$qa_pres.", ".$qa_il.", ".$qa_rt.", ".$qa_ril.", ".$qext_pres.", ".$qext_il.", ".$qext_rt.", ".$qext_rt.")";

		//Esecuzione dell'inserimento in database
		$trova = mysql_query($query);
		if(!$trova){
			die('<script>alert("Errore durante l\'inserimento in database: '.mysql_error().'")</script>');
		}else{
			$ID = mysql_insert_id();
			echo "
	<!-- Modal -->
    <div class=\"modal fade\" id=\"modalSuccess\" role=\"dialog\">
      <div class=\"modal-dialog\" style=\"margin-top: 20%;\">
        <div class=\"modal-content\">
          <div class=\"modal-header\" style=\"background: #2E353C; padding:15px 15px;\">
            <button type=\"button\" class=\"close\" style=\"color: #f0f0f0\" data-dismiss=\"modal\">×</button>
            <h3 style=\"color: #f0f0f0;\"><center><span class=\"fa fa-check\"></span> Pratica inserita con successo</center></h3>
            <a  href=\"../atti/".$anno_pres."/".$atto."_".$num_noslash."/atto.php\">Visualizza</a></li>\";
          </div>
        </div>
      </div>
    </div>
			";
			echo '<script>$("#modalSuccess").modal();</script>';
		}
		
		//Creazione pagina di copertina
		$cover_atto = fopen($percorso_file."atto.php", "w") or die('<script>alert("'.$index_err.'")</script>');
		$cover_content = "<?php	\$ID = ".$ID.";	include 'D:/web/atto.php';?>";
		
		fwrite($cover_atto, $cover_content);
		fclose($cover_atto);
		
	}else{
		//Codice per errore GET
		echo "Errore durante la ricezione della pratica";
		echo '<script> popupErrore(); </script>';
	}
}else{
	print "Chiave di invio non corretta";
}
?>
<?php include 'footer.html';?>