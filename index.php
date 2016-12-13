<?php
	
	include 'part/head.php';
	include 'part/topbar.php';
	
	//Controllo l'invio del modulo di login
	if(isset($_POST['submit'])){
		//Controllo la compilazione dei campi
		if(empty($_POST['username']) || empty($_POST['password'])){
			$login_err = "Inserire le credenziali prima di procedere all'invio";
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
				$login_err = "Errore durante la ricerca delle credenziali in database";
				die();
			}

			if(sqlsrv_has_rows($login_stmt) === true){
				while($row = sqlsrv_fetch_array($login_stmt, SQLSRV_FETCH_ASSOC)){
					$NaClpassword = $row['password'];
					if(password_verify($rawpassword, $NaClpassword) === true){
						//Inizializza la sessione
						$_SESSION['login_user'] = $row['nome'];
						$_SESSION['livello'] = $row['livello'];
						header("Refresh:0");
					}else{
						$login_err = "Credenziali non valide";
					}
				}
			}else{
				$login_err = "Utente non registrato in database";
			}
			//Chiude la connessione
			sqlsrv_close($link);
		}
	}
?>

<?php
  if(isset($_SESSION['livello'])){
	
	}else{ ?>
	<section class="full-width">
		<div class="container">
			<div class="modal-content center" style="max-width: 640px; margin-top: 10%;">
				<div class="modal-header" style="background: #2E353C; padding:15px 15px;">
					<h1 style="color: #f0f0f0;"><center><i class="fa fa-lock" aria-hidden="true"></i> Accedi</center></h1>
				</div>
				<div class="modal-body" style="padding:35px 40px;">
					<form action="" method="post">
						<div style="text-align: center;">
							<p style="color: red; font-size: 15pt;" for="error"></p> <br>
						</div>
						<div class="form-group">
							<label for="username"><i class="fa fa-user" aria-hidden="true"></i> Nome utente</label>
							<input class="form-control" id="username" name="username" placeholder="TestUser" type="text">
						</div>
						<div class="form-group">
							<label for="password"><i class="fa fa-eye" aria-hidden="true"></i> Chiave di accesso</label>
							<input class="form-control" id="password" name="password" placeholder="********" type="password">
						</div>
						<div class="checkbox">
							<label><input value="ricorda" name="ricordami" checked="" type="checkbox">Ricordami (solo per dispositivi sicuri)</label>
						</div>
						<button type="submit" name="submit" class="btn btn-success btn-block"><i class="fa fa-power-off" aria-hidden="true"></i> Accedi</button>
						<a href="page/login.php?type=passwordrecover" type="submit" class="btn btn-info btn-block"><span class="fa fa-key"></span> Ripristina la chiave di accesso</a>
					</form>
				</div>
				<div class="modal-footer" style="background: #2E353C;">
					<?php 
						if(isset($login_err)){
							echo('<p style="color: #f0f0f0;">'.$login_err.'</p>');
						}
					?>
				</div>
			</div>
		</div>
	</section>
	
<?php 
	} 
?>

<?php
	//Include pié di pagina
	include 'part/footer.php';
?>