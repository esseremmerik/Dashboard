//De hoofdfunctie die de stopwatch start en stopt
function initTimer(){
	var value = document.getElementById('startstop').value;
	if(value==1){
		document.getElementById('startstop').innerHTML="Stop";
		document.getElementById('startstop').value="2";
		document.getElementById('startstop').setAttribute("class", "btn btn-large btn-important");
		document.getElementById('startstop').setAttribute("href", "");
		
		clockStart('time_recording');
		
		var url = 'saveTimerecording';
		var aTimeObj = new Array();
		
		aTimeObj['autocomplete'] 		= document.getElementById('timerecording_autocomplete').value;
		aTimeObj['solve360_taskid'] 	= document.getElementById('parentid').value; 
		aTimeObj['wp_taskid']			= document.getElementById('key').value;
		aTimeObj['tasklistid']			= document.getElementById('tasklistid').value;
		aTimeObj['details']				= document.getElementById('activiteit').value;
		aTimeObj['pricePerHour']		= document.getElementById('pricing').value;
		
		jQuery.post(url, { 
			autocomplete 	: aTimeObj['autocomplete'],
			solve360_taskid : aTimeObj['solve360_taskid'],
			wp_taskid 		: aTimeObj['wp_taskid'],
			tasklistid		: aTimeObj['tasklistid'],
			details			: aTimeObj['details'],
			pricePerHour    : aTimeObj['pricePerHour']
			}, function(data){})
			.success(function(data){})
			.error(function(){ alert('error jquery.post timeObject');})
			.complete(function(data){}
		);
	}
	if(value==2){
		document.getElementById('startstop').innerHTML="Start";
		document.getElementById('startstop').value="1";
		document.getElementById('startstop').setAttribute("class", "btn btn-large btn-success");

		jQuery.post('deleteTimeObject');
		
		clockStop('time_recording');
	}
}