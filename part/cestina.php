<?php
	//Definisce i dati di connessione al database
	$dbserver = "CORE-CJ84\sqlexpress";
	$pass_data = json_decode(file_get_contents('../conf/pass.json'), true);
	$dbdata = $pass_data['dbmaster'];
	
	//STABILIMENTO CONNESSIONE AL DATABASE
	$link = sqlsrv_connect($dbserver, $dbdata);
	if($link === false){
		die("Errore connessione");
	}

	//Controlla l'invio della richiesta AJAX
	if(isset($_POST['attID']) && isset($_POST['percorso']) && isset($_POST['att'])){
		
		//Assegna le variabili
		$attID = $_POST['attID'];
		$percorso = $_POST['percorso'];
		$allegato = $_POST['att'];
		$estensione = NULL;
		
		$file_path = $percorso.$allegato.'*';
				
		//Legge il file con ogni estensione
		$files = glob(str_replace('..','D:',$file_path));
	
		//Per ogni estensione che esiste
		foreach ($files as $file){
			//Elimina il file
			unlink($file);
		}
		
		//Query di aggiornamento database
		$att_query = "UPDATE pratica.att
									SET $allegato=(?)
									WHERE IDatt=$attID";

		//Parametri di inserimento allegati
		$att_params = 
		//Statement di inserimento allegati
		$att_stmt = sqlsrv_query($link, $att_query, array($estensione));
		//Esecuzione inserimento allegati
		if($att_stmt === false){
			die("Errore aggiornamento");
		}else{
			echo("Aggiornamento eseguito");
		}		
	}

?>