var time = null;
var timer_is_on=0;

var min = 0;
var sec = 0;
var hour = 0;

var current_hour=0;
var current_min=0;

var timerElementId = '';

//Om de stopwatch te starten. Roept timer() elke 1000 microseconde (1 sec) aan.
function clockStart(timerElementId){
    if(!timer_is_on){
    		document.getElementById(timerElementId).disabled="disabled";
            timer_is_on=1;
            time=setInterval(function(){timer(timerElementId)}, 1000);
    }
}

//Om de stopwatch te stoppen
function clockStop(timerElementId) {
	  document.getElementById(timerElementId).disabled="";
	  document.getElementById("eindtijd").value=currentTime();
	  showPopup(); //popup_timerecord_confirm.js
	  clearInterval(time);
	  timer_is_on= 0;
	  document.getElementById(timerElementId).value="00:00:00";
}

//Een teller die bij elke aanroep 1 omhoog gaat.
function timer(timerElementId){
	var element = document.getElementById(timerElementId).value;
	var strtime = element.split(":");
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
    document.getElementById(timerElementId).value=hour+':'+min+":"+ sec;
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