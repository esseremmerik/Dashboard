//De hoofdfunctie die de stopwatch start en stopt
function initTimer(){
	var value = document.getElementById('startstop').value;
	if(value==1){
		document.getElementById('startstop').innerHTML="Stop";
		document.getElementById('startstop').value="2";
		document.getElementById('startstop').setAttribute("class", "btn btn-large btn-important");
		document.getElementById('startstop').setAttribute("href", "");
		
		var url = 'saveTimeObj';
		var aTimeObj = new Array();
		
		aTimeObj['autocomplete'] 		= document.getElementById('timerecording_autocomplete').value;
		aTimeObj['solve360_taskid'] 	= document.getElementById('parentid').value; 
		aTimeObj['wp_taskid']			= document.getElementById('key').value;
		aTimeObj['tasklistid']			= document.getElementById('tasklistid').value;
		aTimeObj['details']				= document.getElementById('activiteit').value;
		
		/*alert('autocomplete: ' + aTimeObj['autocomplete'] + '<br />' +
			  'solve360_taskid: ' + aTimeObj['solve360_taskid'] + '<br />' +
			  'wp_taskid: ' + aTimeObj['wp_taskid'] + '<br />' +
			  'tasklistid: ' + aTimeObj['tasklistid'] + '<br />' + 
			  'details: ' + aTimeObj['details']);
		*/
		jQuery.post(url, { 
			autocomplete 	: aTimeObj['autocomplete'],
			solve360_taskid : aTimeObj['solve360_taskid'],
			wp_taskid 		: aTimeObj['wp_taskid'],
			tasklistid		: aTimeObj['tasklistid'],
			details			: aTimeObj['details']
			}, function(data){})
			.success(function(data){})
			.error(function(){ alert('error jquery.post timeObject');})
			.complete(function(data){console.log(data.responseText);
			});
		
		clockStart('time_recording');
	}
	if(value==2){
		document.getElementById('startstop').innerHTML="Start";
		document.getElementById('startstop').value="1";
		document.getElementById('startstop').setAttribute("class", "btn btn-large btn-success");

		clockStop('time_recording');
	}
}