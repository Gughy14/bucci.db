$(function(){
	
	if(sessionStorage.getItem('theme') == null || undefined){
		var theme = '/css/light.css';
	}else{
		var theme = sessionStorage.getItem('theme');
	}
	
	document.getElementById('theme').setAttribute('href', theme);
});

function lightSwitch(){
	
	var theme = sessionStorage.getItem('theme');
	
	if(theme === '/css/light.css'){
		document.getElementById('theme').setAttribute('href', '/css/dark.css');
		sessionStorage.setItem('theme', '/css/dark.css');
	}else if(theme === '/css/dark.css'){
		document.getElementById('theme').setAttribute('href', '/css/light.css');
		sessionStorage.setItem('theme', '/css/light.css');
	}else if (sessionStorage.getItem('theme') == null || undefined){
		document.getElementById('theme').setAttribute('href', '/css/dark.css');
		sessionStorage.setItem('theme', '/css/dark.css');
	}else{
		document.getElementById('theme').setAttribute('href', '/css/light.css');
		sessionStorage.setItem('theme', '/css/light.css');
	}
}