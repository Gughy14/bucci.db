<?php include 'head.html';?>

<script>
//Funzioni animazione pressione tasti
function clickCerca() {
  var cerca = document.getElementById("cerca");
  cerca.style.position = "relative"; 
  cerca.style.boxShadow = "none";
  cerca.style.top = "5px";
  cerca.style.right = "5px";
}
function clickInserisci() {
  var inserisci = document.getElementById("inserisci");
  inserisci.style.position = "relative"; 
  inserisci.style.boxShadow = "none";
  inserisci.style.top = "5px";
  inserisci.style.right = "5px";
  }

//Funzione rilascio tasti
function mouseUp() {
  var cerca = document.getElementById("cerca");
  cerca.style.position = "relative"; 
  cerca.style.boxShadow = "-1px 1px 0px green, -2px 2px 0px green, -3px 3px 0px green, -4px 4px 0px green, -5px 5px 0px green";
  cerca.style.top = "0px";
  cerca.style.right = "0px";
  var inserisci = document.getElementById("inserisci");
  inserisci.style.position = "relative"; 
  inserisci.style.boxShadow = "-1px 1px 0px green, -2px 2px 0px green, -3px 3px 0px green, -4px 4px 0px green, -5px 5px 0px green";
  inserisci.style.top = "0px";
  inserisci.style.right = "0px";
}
</script>

<?php include 'topbar.html';?>

<!-- Cover -->
<div class="full-width" style="margin-top: 34px; background: #11283b; min-height: 190px;">
  <div class="container" style="text-align: center;">
	<br>
	<h1 style="font-size: 64px; color: #f0f0f0;">Database atti edilizi</h1>
	<h3 style="color: #f0f0f0;"></h3>
  </div>
</div>

<!-- Breaking Line -->
<div class="full-width" style="background: #38414A;">
  &nbsp;
</div>

<!--Selezione operazioni-->
<div class="full-width" onmouseup="mouseUp()" style="margin: auto; min-height: 100%">
  <div class="container" style="padding: 100px 0px;">
    <h3><a href="cerca.php" id="cerca" class="btngg" onmousedown="clickCerca()" style="margin: 5px 275px;">Esegui una ricerca</a></h3>
	<br><br><br>
	<h3><a href="inserisci.php" id="inserisci" class="btngg"  onmousedown="clickInserisci()" style="margin: 5px 235px;">Inserisci un nuovo atto</a></h3>
	</div>
</div>

<?php include 'footer.html';?>