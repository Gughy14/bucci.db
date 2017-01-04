
</head>

<body>

	<!-- Barra superiore -->
	<script>
	jQuery(document).ready(function(){
		
		$("#menu-icon").click(function(){
			$(".etichetta").toggleClass("enlarged");
		});
		
	});
	</script>
	
	<header class="dp4">
		<button id="menu-icon" class="icona left">
			<i style="color: #fff; width: 24px; vertical-align: middle;" class="material-icons" title="Mostra il menu">menu</i>
		</button>
		<div style="transition: padding-left .2s cubic-bezier(.4,0,.2,1); padding-left: 0; height: 64px; left: 72px; right: 72px; top: 0; z-index: 2; position: fixed;">
			<div style="white-space: nowrap; overflow: hidden; color: #fff; font-size: 18px; line-height: 64px; width: 100%;">
				<?php echo $pagine[basename($_SERVER['PHP_SELF'])];?>
			</div>
		</div>
			
<?php if(isset($_SESSION['livello'])){ ?>

		<div onclick="window.location.href='/part/esci.php'" class="etichetta right">
			<div style="margin-top: 16px;">ESCI</div>
		</div>
		<button onclick="window.location.href='/index.php?logout'" class="icona right">
			<i style="width: 24px; vertical-align: middle;" class="material-icons" title="Esci dall'applicazione">exit_to_app</i>
		</button>

				
				
<?php		if($_SESSION['livello'] <= 2){ ?>

		<div onclick="window.location.href='/cerca.php'" class="etichetta right">
			<div style="margin-top: 16px;">RICERCA</div>
		</div>
		<button onclick="window.location.href='/cerca.php'" class="icona right">
			<i style="width: 24px; vertical-align: middle;" class="material-icons" title="Ricerca un atto">search</i>
		</button>

<?php		}
				if($_SESSION['livello'] <= 1){ ?>
				
		<div onclick="window.location.href='/inserisci.php'" class="etichetta right">
			<div style="margin-top: 16px;">INSERISCI</div>
		</div>	
		<button onclick="window.location.href='/inserisci.php'" class="icona right">
			<i style="width: 24px; vertical-align: middle;" class="material-icons" title="Aggiungi un nuovo atto">note_add</i>
		</button>
					
<?php 	}
			} ?>
		<!-- Pulsante per un'homepage che ora come ora non esiste
		<div onclick="window.location.href='/'" class="etichetta right">
			<div style="margin-top: 16px;">HOMEPAGE</div>
		</div>
		<button onclick="window.location.href='/'" class="icona right">
			<i style="width: 24px; vertical-align: middle;" class="material-icons" title="Mostra il menu">home</i>
		</button>-->
	</header>
