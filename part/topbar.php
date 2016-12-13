
</head>

<body>

	<!-- Barra superiore -->
    <div class="full-width topbar">
      <div class="container">
        <div class="row">
          <div class="col-md-12 tb-right">
            <div id="top-nav">
				<?php if(!isset($_SESSION['livello'])){ ?>
								<div class="pull-right nav">
									<a href="" class="quick-nav">
										<i class="fa"></i>
										<span class="hidden-xs"></span>
									</a>
								</div>
				<?php }else{
								if($_SESSION['livello'] == 3){ ?>
									<div class="pull-left nav">
										<a href="/cerca.php" class="quick-nav">
											<i class="fa fa-search"></i>
											<span class="hidden-xs">Esegui una nuova ricerca</span>
										</a>
									</div>
					<?php	}elseif($_SESSION['livello'] == 2){ ?>
									<div class="pull-left nav">
										<a href="/inserisci.php" class="quick-nav">
											<i class="fa fa-archive"></i>
											<span class="hidden-xs">Inserisci un nuovo atto</span>
										</a>
									</div>
					<?php	}elseif($_SESSION['livello'] <= 1){ ?>
									<div class="pull-left nav">
										<a href="/inserisci.php" class="quick-nav">
											<i class="fa fa-archive"></i>
											<span class="hidden-xs">Inserisci un nuovo atto</span>
										</a>
									</div>
									<div class="pull-left nav">
										<a href="/cerca.php" class="quick-nav">
											<i class="fa fa-search"></i>
											<span class="hidden-xs">Esegui una nuova ricerca</span>
										</a>
									</div>
					<?php } ?>
								<div class="pull-right nav">
									<a href="/part/esci.php" class="quick-nav">
										<i class="fa fa-sign-out"></i>
										<span class="hidden-xs">Esci</span>
									</a>
								</div>
				<?php	} ?>
            </div>
          </div>
        </div>
      </div>
    </div>
