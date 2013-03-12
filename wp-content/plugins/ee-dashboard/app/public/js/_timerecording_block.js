//De hoofdfunctie die de stopwatch start en stopt
function initTimer(){
	var value = document.getElementById('startstop').value;
	if(value==1){
		document.getElementById('startstop').innerHTML="Stop";
		document.getElementById('startstop').value="2";
		document.getElementById('startstop').setAttribute("class", "btn btn-large btn-important");
		document.getElementById('startstop').setAttribute("href", "");
		clockStart('time_recording');
	}
	if(value==2){
		document.getElementById('startstop').innerHTML="Start";
		document.getElementById('startstop').value="1";
		document.getElementById('startstop').setAttribute("class", "btn btn-large btn-success");

		clockStop('time_recording');
	}
}