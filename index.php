<?php
	//Definisce la directory base del sito
	$path = $_SERVER['DOCUMENT_ROOT'];
	
	//Include Intestazione
	include $path.'/part/head.php';
	
	//Include barra di navigazione
	include $path.'/part/topbar.php';
	
	//Controlla se l'utente è autenticato (mediante livello)
  if(isset($_SESSION['livello'])){
		
		//Controlla se è un'uscita
		if(isset($_GET['logout'])){
			//Distrugge la sessione
			session_destroy();
			//Ricarica la pagina
			header("location: /");
		
		//Se non è un'uscita
		}else{
			//Reindirizza alla funzione di ricerca
			header("location: /cerca.php");
		}
	//Se l'utente non è autenticato
	}else{
		
		//Controlla l'invio del modulo di autenticazione
		if(isset($_POST['submit'])){
			//Controlla la compilazione dei campi
			if(empty($_POST['username']) || empty($_POST['password'])){
				//Errore in caso di campi non compilati
				$login_err = "Inserisci le credenziali di accesso!";
			}else{
				//Elabora le variabili
				$username = $_POST['username'];
				$rawpassword = $_POST['password'];
				
				//Ottiene credenziali dal JSON
				$dbdata = $pass_data['dbmaster'];
				
				//Stabilisce la connessione al database
				$link = sqlsrv_connect($dbserver, $dbdata);
				if($link === false){
					//Errore in caso di connessione fallita
					$login_err = "Errore durante la connessione al database";
				}else{
					//Query di autenticazione
					$login_query = "SELECT nome, password, livello
													FROM utenti.dat
													WHERE nome = ?
												 ";
					//Esegue la verifica delle credenziali
					$login_stmt = sqlsrv_query($link, $login_query, array($username));
					if($login_stmt === false){
						//Errore in caso di problemi in fase di verifica
						$login_err = "Errore durante la verifica delle credenziali";
					}else{
						//Controlla la presenza dell'utente in database
						if(sqlsrv_has_rows($login_stmt) === false){
							//Errore in caso di utente errato
							$login_err = "Utente non registrato in database";
						}else{
							//Elabora il risultato della query
							while($row = sqlsrv_fetch_array($login_stmt, SQLSRV_FETCH_ASSOC)){
								//Salt.Password
								$NaClpassword = $row['password'];
								//Verifica la password immessa con quella archiviata
								if(password_verify($rawpassword, $NaClpassword) === false){
									//Errore in caso di password errata
									$login_err = "Password errata!";
								}else{
									//Inizializza la sessione
									$_SESSION['login_user'] = $row['nome'];
									$_SESSION['livello'] = $row['livello'];
									//Ricarica la pagina
									header("Refresh:0");
								}
							}
						}
					}
					//Chiude la connessione
					sqlsrv_close($link);
				}
			}
		} ?>
		
		<!-- Intestazione Colore -->
		<div class="section material" style="height: 256px;"></div>
		
		<!-- Riquadro di autenticazione -->
		<div id="login-popup" class="dp2">
			<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">
				<h3>Accedi</h3>
				<p><?php echo (isset($login_err) ? $login_err : '&nbsp;');?></p>
				<div class="form-group">
					<label>Nome Utente</label>
					<input class="form-control" name="username" type="text" placeholder="Test"/>
				</div>
				<div class="form-group">
					<label>Chiave di accesso</label>
					<input class="form-control" name="password" id="pwd" type="password" placeholder="123"/>
				</div>
				<button id="login" type="submit" name="submit">
					<i class="material-icons">lock_open</i>
				</button>
			</form>
		</div>
<?php
	}
	
	//Include pié di pagina
	include $path.'/part/footer.php';
?>