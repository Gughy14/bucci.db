<?php
	//Definisce la directory base del sito
	$path = $_SERVER['DOCUMENT_ROOT'];
	
	//Include Intestazione
	include $path.'/part/head.php';
	//Include barra di navigazione
	include $path.'/part/topbar.php';
	
	//Controllo l'invio del modulo di login
	if(isset($_POST['submit'])){
		//Controllo la compilazione dei campi
		if(empty($_POST['username']) || empty($_POST['password'])){
			$login_err = "Inserire le credenziali prima di procedere all'accesso";
		}else{
			//Elaborazione variabili
			$username = $_POST['username'];
			$rawpassword = $_POST['password'];
			
			//Ottenimento credenziali da JSON
			$dbdata = $pass_data['dbmaster'];
			
			//STABILIMENTO CONNESSIONE AL DATABASE
			$link = sqlsrv_connect($dbserver, $dbdata);
			if($link === false){
				$login_err = "Errore durante la connessione al database";
				die();
			}
			
			//Query di login
			$login_query = "SELECT nome, password, livello
											FROM utenti.dat
											WHERE nome = ?
										 ";
										
			//Esecuzione ricerca login
			$login_stmt = sqlsrv_query($link, $login_query, array($username));
			if($login_stmt === false){
				$login_err = "Errore durante la verifica delle credenziali";
				die();
			}
			
			//Controllo presenza dell'utente in database
			if(sqlsrv_has_rows($login_stmt) === true){
				//Elaborazione del risultato della query
				while($row = sqlsrv_fetch_array($login_stmt, SQLSRV_FETCH_ASSOC)){
					//Salt.Password
					$NaClpassword = $row['password'];
					//Verifica la password immessa con quella archiviata
					if(password_verify($rawpassword, $NaClpassword) === true){
						//Inizializza la sessione
						$_SESSION['login_user'] = $row['nome'];
						$_SESSION['livello'] = $row['livello'];
						//Ricarica la pagina
						header("Refresh:0");
					}else{
						//Errore in caso di password errata
						$login_err = "Password errata";
					}
				}
			}else{
				//Errore in caso di utente errato
				$login_err = "Utente non registrato";
			}
			//Chiude la connessione
			sqlsrv_close($link);
		}
	}
?>

<?php
	//Controlla se l'utente è loggato tramite il livello
  if(isset($_SESSION['livello'])){ 
		//Codice per homepage se loggato 
		
		
		
		
		?>
	
	
<?php
	}else{
		//Codice di login ?>
		<script type="text/javascript">
			document.body.style.background = "rgba(0,0,0,0.5)";
		</script>
		
		<section class="full-width">
			<div class="container">
				<div class="modal-content center" style="max-width: 640px; margin-top: 10%;">
					<div class="modal-header" style="background: #2E353C; padding:15px 15px;">
						<h1 style="color: #f0f0f0;"><center><i class="fa fa-lock" aria-hidden="true"></i> Accedi</center></h1>
					</div>
					<div class="modal-body" style="padding:35px 40px;">
						<form action="" method="post">
							<div style="text-align: center;">
<?php						//Controlla se ci sono errori di login
								if(isset($login_err)){
									//Stampa eventuali errori
									echo('<p style="color: red; font-size: 15pt; font-weight: 700;" for="error">'.$login_err.'</p>');
								} ?>
							</div>
							<div class="form-group">
								<label for="username"><i class="fa fa-user" aria-hidden="true"></i> Nome utente</label>
								<input class="form-control" id="username" name="username" placeholder="Utente" type="text">
							</div>
							<div class="form-group">
								<label for="password"><i class="fa fa-eye" aria-hidden="true"></i> Chiave di accesso</label>
								<input class="form-control" id="password" name="password" placeholder="Password" type="password">
							</div>
							<button type="submit" name="submit" class="btn btn-success btn-block"><i class="fa fa-power-off" aria-hidden="true"></i> Accedi</button>
						</form>
					</div>
					<div class="modal-footer" style="background: #2E353C;"></div>
				</div>
			</div>
		</section>
	
<?php 
	}
	
	//Include pié di pagina
	include $path.'/part/footer.php';
?>