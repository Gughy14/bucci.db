	
	<div class="footer">
		<div class="container">
			<div class="row update">
			<?php
				//Controllo se presente timestamp (è una pratica?)
				if(isset($insertstamp)){
					//Converte il timestamp in formato data
					$insert_date = $insertstamp->format('d.m.Y H:i:s');
					
					//Stampa il valore di footer
					print('<p>Inserimento pratica: '.$insert_date.'</p>');
				}else{
					//Apre e decodifica il file contenente i valori temporali
					$time_json = file_get_contents('conf/time.json');
					$time_data = json_decode($time_json, true);
					
					//Ottieni timestamp ultima esecuzione
					$lastexec = $time_data['last_exec'];
					
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
						$lastcommit = $commitdatetime->format('d.m.Y H:i:s').' UTC';
						//Aggiorna il valore dell'ultimo commit
						$time_data['last_commit'] = $lastcommit;
						
						//Ottieni il timestamp ultima esecuzione
						$lastexec = time();
						//Aggiorna il timestamp ultima esecuzione
						$time_data['last_exec'] = $lastexec;
						//Ricodifica l'array in JSON
						$time_json = json_encode($time_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
						
						//Carica i dati nel file
						$fp = fopen('conf/time.json', 'w');
						fwrite($fp, $time_json);
						fclose($fp);
					}else{
						//Ottieni l'ultimo commit tramite JSON
						$lastcommit = $time_data['last_commit'];
					}
					//Stampa in entrambi i casi il footer
					echo('<p>Ultimo Aggiornamento: '.$lastcommit.'</p>');
				}
			?>
			
			</div>
		</div>
	</div>
</body>



</html>