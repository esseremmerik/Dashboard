var number = 0;

var time = null;
var timer_is_on=0;

var min = 0;
var sec = 0;
var hour = 0;

var current_hour=0;
var current_min=0;

//De hoofdfunctie die de stopwatch start en stopt
function initStopwatch(){
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

//Om de stopwatch te starten. Roept timer() elke 1000 microseconde (1 sec) aan.
function clockStart(){
    if(!timer_is_on){
            timer_is_on=1;
            time=setInterval(timer, 1000);
            timer();
    }
}

//Een teller die bij elke aanroep 1 omhoog gaat.
function timer(){
	document.getElementById("time_recording").disabled="disabled";
	var lol = document.getElementById("time_recording").value;
	var strtime = lol.split(":");
	hour = strtime[0];
	min = strtime[1];
	sec = strtime[2];
    sec++;  
             
    if(sec>59){
    	min++;
    	if(min<10){ min='0'+min;}
    	sec=0;
    }
    if(min>59){
	    hour++;
	    if(hour<10){hour='0'+hour;}
	    min='00';
    }  
    if(sec<10){
    	sec = '0' + sec; 
    }
    if(document.getElementById("starttijd").value==''){document.getElementById("starttijd").value=currentTime();}
    document.getElementById("time_recording").value=hour+':'+min+":"+ sec;
}

//Om de stopwatch te stoppen
function clockStop() {
	  document.getElementById("time_recording").disabled="";
	  document.getElementById("eindtijd").value=currentTime();
	  showPopup(); //popup_timerecord_confirm.js
	  clearInterval(time);
	  timer_is_on= 0;
	  document.getElementById("time_recording").value="00:00:00";
}


//geeft de huidige tijd in hh:mm
function currentTime(){
	var current_time = new Date();
	current_hour = current_time.getHours();
	current_min = current_time.getMinutes();
	
	if(current_hour<10){current_hour='0'+current_time.getHours();}
    if(current_min<10){current_min='0'+current_time.getMinutes();}
    
    return current_hour+":"+current_min;
}