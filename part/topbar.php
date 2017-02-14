<?php
	if(isset($_GET['esci'])){
		//Distrugge la sessione
		session_destroy();
		//Ricarica la pagina dopo 3 secondi
		header('Location: /');
	}
?>

		<!-- Etichette icone topbar -->
		<script type="application/javascript">
			jQuery(document).ready(function(){
				$("#menu-icon").click(function(){
					$(".etichetta").toggleClass("enlarged");
				});
			});
		</script>
	</head>
	<body>
		<!-- Barra superiore -->
		<header class="dp4">
			<!-- Pulsante Menu -->
			<button id="menu-icon" class="icona left">
				<i style="color: #fff; width: 24px; vertical-align: middle;" class="material-icons" title="Mostra il menu">menu</i>
			</button>
			<div style="transition: padding-left .2s cubic-bezier(.4,0,.2,1); padding-left: 0; height: 64px; left: 72px; right: 72px; top: 0; z-index: 2; position: fixed;">
				<div style="white-space: nowrap; overflow: hidden; color: #fff; font-size: 18px; line-height: 64px; width: 100%;">
					<?php echo $pagine[basename($_SERVER['PHP_SELF'])];?>
					
				</div>
			</div>
<?php if(isset($_SESSION['livello'])){ ?>
			<!-- Pulsante Uscita -->
			<div onclick="window.location.href='?esci'" class="etichetta right">
				<div style="margin-top: 16px;">ESCI</div>
			</div>
			<button onclick="window.location.href='/index.php?logout'" class="icona right">
				<i style="width: 24px; vertical-align: middle;" class="material-icons" title="Esci dall'applicazione">exit_to_app</i>
			</button>
<?php		if($_SESSION['livello'] <= 2){ ?>
			<!-- Pulsante Ricerca -->
			<div onclick="window.location.href='/cerca.php'" class="etichetta right">
				<div style="margin-top: 16px;">RICERCA</div>
			</div>
			<button onclick="window.location.href='/cerca.php'" class="icona right">
				<i style="width: 24px; vertical-align: middle;" class="material-icons" title="Ricerca un atto">search</i>
			</button>
<?php		}
				if($_SESSION['livello'] <= 1){ ?>
			<!-- Pulsante Modifica -->
			<div onclick="window.location.href='/inserisci.php'" class="etichetta right">
				<div style="margin-top: 16px;">INSERISCI</div>
			</div>	
			<button onclick="window.location.href='/inserisci.php'" class="icona right">
				<i style="width: 24px; vertical-align: middle;" class="material-icons" title="Aggiungi un nuovo atto">note_add</i>
			</button>
<?php 	}
			}else{ ?>
			<!-- Pulsante Login -->
			<div onclick="window.location.href='/'" class="etichetta right">
				<div style="margin-top: 16px;">ACCEDI</div>
			</div>
			<button onclick="window.location.href='/'" class="icona right">
				<i style="width: 24px; vertical-align: middle;" class="material-icons" title="Esci dall'applicazione">lock_outline</i>
			</button>	
<?php	}


			?>
			<!-- Pulsante per un'homepage che ora come ora non esiste
			<div onclick="window.location.href='/'" class="etichetta right">
				<div style="margin-top: 16px;">HOMEPAGE</div>
			</div>
			<button onclick="window.location.href='/'" class="icona right">
				<i style="width: 24px; vertical-align: middle;" class="material-icons" title="Mostra il menu">home</i>
			</button>-->
		</header>
		