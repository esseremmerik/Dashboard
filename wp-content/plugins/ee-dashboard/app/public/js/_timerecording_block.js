//De hoofdfunctie die de stopwatch start en stopt
function startTimer(){
	var value = document.getElementById('startstop').value;
	if(value==1){
		document.getElementById('startstop').innerHTML="Stop";
		document.getElementById('startstop').value="2";
		document.getElementById('startstop').setAttribute("class", "btn btn-large btn-important");
		document.getElementById('startstop').setAttribute("href", "");
		clockStart();
	}
	if(value==2){
		document.getElementById('startstop').innerHTML="Start";
		document.getElementById('startstop').value="1";
		document.getElementById('startstop').setAttribute("class", "btn btn-large btn-success");

		clockStop();
	}
}