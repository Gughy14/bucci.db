<div class="footer">
  <div class="container">
    <div class="row update">
	  <?php
			//Controllo se presente timestamp (è una pratica?)
			if(isset($timestamp)){
				//Converte il timestamp in formato data
				$insert_date = $timestamp->format('d.m.Y H:i:s');
				
				//Stampa il valore di footer
				print('<p>Inserimento pratica: '.$insert_date.'</p>');
			}else{
				//Dichiara i file contenenti valori provvisori (da rendere JSON!)
				$time = 'conf/time.txt';
				$update = 'conf/update.txt';
				
				//Ottieni timestamp ultima esecuzione
				$lastexec = file_get_contents($time);
				
				//Controlla se è passato il tempo richiesto
				if(time() >= $lastexec + (150)){
					
					//Collega alla repository su github tramite API
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
					curl_setopt($ch, CURLOPT_URL, 'https://api.github.com/repos/gughy14/bucci.db/git/refs/heads/master');
					$result = curl_exec($ch);
					curl_close($ch);
					
					//Decodifica il JSON
					$obj = json_decode($result);
					
					//Ottiene l'URL dell'ultimo commit
					$commit = $obj->object->url;
					
					//Collega al commit ottenuto in precedenza sempre tramite API
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
					curl_setopt($ch, CURLOPT_URL, $commit);
					$result = curl_exec($ch);
					curl_close($ch);
					
					//Decodifica il JSON
					$obj = json_decode($result);
					
					//Ottiene la data dell'ultimo commit
					$commitdate = $obj->committer->date;
					//Converte la data in formato datetime
					$commitdatetime = new DateTime($commitdate);
					//Codifica la data in gg.mm.yy hh:mm:ss UTC
					$last_commit = $commitdatetime->format('d.m.Y H:i:s').' UTC';
					//Carica la data nel file
					file_put_contents($update, $last_commit);
					
					//Ottieni il timestamp ultima esecuzione
					$lastexec = time();
					
					//Aggiorna il timestamp nel file
					file_put_contents($time, $lastexec);
				}else{
					//Ottiene l'ultimo commit tramite valore in file
					$last_commit = file_get_contents($update);
				}
				//Stampa in entrambi i casi il footer
				echo('<p>Ultimo Aggiornamento: '.$last_commit.'</p>');
			}
	  ?>
    </div>
  </div>
</div>

</body>



</html>