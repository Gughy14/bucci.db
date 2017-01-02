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
			$login_err = "Inserisci le credenziali di accesso!";
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
						$login_err = "Password errata!";
					}
				}
			}else{
				//Errore in caso di utente errato
				$login_err = "Utente non registrato in database";
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
		<section style="height: 256px; background: #2196F3"></section>		
		<div style="position: absolute; top: 144px; width: 404px; height: 303px; margin-left: auto; margin-right: auto; left: 0; right: 0; background: #FFF; padding: 44px 65px; " class="dp2">
			<form action="" method="post">
				<h3 style="color: #222; margin: 0 0 10px; font-size: 18px; font-weight: 700;">
					Accedi
				</h3>
<?php			//Controlla se ci sono errori di login
					if(isset($login_err)){
						//Stampa eventuali errori
						echo('
									<p style="color: red; margin: 10px 0 30px; font-size: 13px; line-height: 160%;">
										'.$login_err.'
									</p>
								');
					}else{
						echo('
									<p style="color: #666; margin: 10px 0 30px; font-size: 13px; line-height: 160%;">
										Inserisci le credenziali di accesso
									</p>
								');
					}?>
				</p>
				<div id="user" class="input-container">
          <label>Nome Utente</label>
          <input style="color: #000; background: #fff; display: inline-block; border: 0; outline: 0; font-size: 13px; padding: 3px 0; margin: 3px 0 0; width: 100%; resize: none;" name="username" id="user" type="text" placeholder="Utente"/>
        </div>
				<div class="input-container" id="pwd">
          <label >Chiave di accesso</label>
          <input style="color: #000; background: #fff; display: inline-block; border: 0; outline: 0; font-size: 13px; padding: 3px 0; margin: 3px 0 0; width: 100%; resize: none;" name="password" id="pwd" type="password" placeholder="Password"/>
        </div>
				<button type="submit" name="submit" style="left: 245px; position: relative; display:block; cursor: pointer; height: 56px; width: 56px; background-color: #FF6D00;border-radius: 50%; border: none; box-shadow: 0 6px 10px 0 rgba(0,0,0,0.3);">
					<i class="material-icons" style="margin:8px; color: #f0f0f0;">lock_open</i>
				</button>
			</form>
		</div>
	
<?php 
	}
	
	//Include pié di pagina
	include $path.'/part/footer.php';
?>