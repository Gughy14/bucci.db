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
		<div class="section" style="height: 256px; background: #2196F3"></div>
		
		<!-- Riquadro di autenticazione -->
		<div style="position: absolute; top: 144px; width: 404px; height: 303px; margin-left: auto; margin-right: auto; left: 0; right: 0; background: #FFF; padding: 44px 65px; " class="dp2">
			<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">
				<h3 style="color: #222; margin: 0 0 10px; font-size: 18px; font-weight: 700;">
					Accedi
				</h3>
				<p style="color: red; margin: 10px 0 30px; font-size: 13px; line-height: 160%;">
					<?php echo (isset($login_err) ? $login_err : 'Inserisci le credenziali di accesso');?>

				</p>
				<div class="form-group">
					<label>Nome Utente</label>
					<input class="form-control" name="username" type="text" placeholder="Test"/>
				</div>
				<div class="form-group">
					<label >Chiave di accesso</label>
					<input class="form-control" name="password" id="pwd" type="password" placeholder="123"/>
				</div>
				<button type="submit" name="submit" style="top: 8px; left: 245px; position: relative; display:block; cursor: pointer; height: 56px; width: 56px; background-color: #FF6D00;border-radius: 50%; border: none; box-shadow: 0 6px 10px 0 rgba(0,0,0,0.3);">
					<i class="material-icons" style="margin:8px; color: #f0f0f0;">lock_open</i>
				</button>
			</form>
		</div>
<?php
	}
	
	//Include pié di pagina
	include $path.'/part/footer.php';
?>