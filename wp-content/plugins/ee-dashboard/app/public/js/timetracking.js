function change(action)
{
//	1 = focus op invoerveld 'activiteit'
//  2 = onblur op invoerveld 'activiteit'
//  3 = onblur blauwe div 'timerecording_block'
	
	if(action == 1){
		document.getElementById("timerecording_block").style.height=165+'px';
		if(document.getElementById('labelwarning').innerHTML!=''){document.getElementById("labelwarning").style.display='block';}
		if(document.getElementById("activiteit").placeholder == 'Benoem je activiteit'){
			document.getElementById("timerecording_autocomplete").type='text';
			if(document.getElementById('startstop').getAttribute("class")!='btn btn-large btn-success'){
			document.getElementById('timerecording_autocomplete').style.borderColor='#D6A000';
			document.getElementById('timerecording_autocomplete').style.backgroundColor='#FFFDD3';
			}
		}	
	}
	if(action == 2){
		if(document.getElementById("activiteit").value == ''){
		}
	}
}
function hideTrackBlock(){
	document.getElementById("timerecording_block").style.height=115+"px";
}
jQuery(document).ready(function() {
	jQuery.post('getTimerecording', {},function(data){})
	.complete(function(data){
		if(data.responseText){
			console.log(data.responseText);
			var timerecordingObject = data.responseText;
			var splitTimerecordingObject = timerecordingObject.split(';;;');
			
			var autocompleteText = splitTimerecordingObject[0];
			var solve360TaskId   = splitTimerecordingObject[1];
			var wpTaskId		 = splitTimerecordingObject[2];
			var tasklistId		 = splitTimerecordingObject[3];
			var details			 = splitTimerecordingObject[4];
			var durationInHourMinutesSeconds= splitTimerecordingObject[5];
			var startTime		 = splitTimerecordingObject[6];
			var profitPerHour    = splitTimerecordingObject[7];
			
			document.getElementById('activiteit').focus();
			document.getElementById('activiteit').value = details;
			document.getElementById('timerecording_autocomplete').value = autocompleteText;
			document.getElementById('parentid').value = solve360TaskId;
			document.getElementById('key').value = wpTaskId;
			document.getElementById('tasklistid').value = tasklistId;
			document.getElementById('time_recording').value = durationInHourMinutesSeconds;
			document.getElementById('starttijd').value = startTime;
			document.getElementById('pricing').value = profitPerHour;
			
			document.getElementById('startstop').click();
		}
	});
	//wanneer een taak is geselecteerd moet start button groen worden
	jQuery("#content").mouseover(function() {
		if(document.getElementById('parentid').value!=""){
			taskValidation(1);
			if(document.getElementById('startstop').getAttribute("class")=='btn btn-large btn-warning'){
			document.getElementById('startstop').setAttribute("class", "btn btn-large btn-success");
			}
		}
	});
	jQuery('.autocomplete-ajax').change(function(){
		if(document.getElementById("timerecording_autocomplete").placeholder == 'Benoem taak of project'){
			taskValidation(2);
		}
	});
});
function taskValidation($number){
	// 1=ingevuld en goed
	// 2=leeg
	
	if($number==1){
		if(document.getElementById('timericon')){document.getElementById('timericon').setAttribute("class", "icon");}
		document.getElementById('timerecording_autocomplete').style.backgroundColor='#ECFFE9'; //groen
		document.getElementById('timerecording_autocomplete').style.borderColor='#64B059'; //groen
		jQuery('#labelwarning').empty();
		document.getElementById('labelwarning').style.display='none';
		document.getElementById('timerecording_block').style.height='130px';
		return;
	}
	if($number==2){
		document.getElementById('parentid').value="";
		document.getElementById('itemid').value="";
		document.getElementById('pricing').value="";
		document.getElementById('tasklistid').value="";
		document.getElementById('starttijd').value="";
		document.getElementById('eindtijd').value="";
		document.getElementById('timerecording_autocomplete').style.borderColor='#D6A000'; //geel
		document.getElementById('timerecording_autocomplete').style.backgroundColor='#FFFDD3'; //geel
		document.getElementById('labelwarning').innerHTML="<span id='warning_header'>Warning: <span style='font-size:12px;margin-left:5px;'>geen taak geselecteerd</span></span>";
		document.getElementById('startstop').setAttribute("class",'btn btn-large btn-warning');
		document.getElementById('startstop').innerHTML="<i id='timericon' class='icon-warning-sign'></i> Start";
		return;
	}
}
function loadTask(solve_id,pricing,tasklistid,projectid,taskname,projectname, spent_time, estimated_time,element_id,id) {
	jQuery(document).ready(function() {
		var play_button = element_id;
		
		change(1);
		taskValidation(1);
		
		//wanneer je al op/over je begroting zit
		if(parseFloat(estimated_time)<parseFloat(spent_time) && estimated_time!='-'){
			
			//body van popup legen
			jQuery('modal_body').empty();
		
			document.getElementById(play_button).setAttribute("href", "#popup");
			document.getElementById(play_button).setAttribute("data-toggle", "modal");
			
			//Gegevens die nodig zijn voor ajax call
			var url = jQuery('form').attr('action')+'timetrackings/getModalContent';
			var popup_type = 'time_above_estimated';
			
			document.getElementById('form_action').name='update_task'; //wordt gebruikt voor de afhandeling tijdens submit in 'modal.js'

			jQuery.post(url, { type:popup_type}, function(data){})
			.success(function(data){document.getElementById('modal_body').innerHTML=data;})
			.error(function(){})
			.complete(function(){ 
				document.getElementById('modal_title').innerHTML		= 'Je zit over je begroting';
				document.getElementById('modal_geschreven').value		= spent_time + ' uur';
				document.getElementById('modal_begroot').value			= estimated_time + ' uur';
				document.getElementById('modal_projectname').innerHTML	= projectname;
				document.getElementById('modal_taskname').innerHTML 	= taskname;
				document.getElementById('modal_pricing').value			= pricing;
				document.getElementById('submit_form').value			= 'Start';
				document.getElementById('key').value					= id;
			});
			
		}
		else{
			document.getElementById('startstop').click();
		}
		
		document.getElementById('timerecording_autocomplete').value	= taskname+' '+'['+projectname+']';
		document.getElementById('itemid').value				= projectid;
		document.getElementById('parentid').value			= solve_id;
		document.getElementById('pricing').value			= pricing;
		document.getElementById('tasklistid').value			= tasklistid;
	});
}
function editTimerecord(id,element_id,details,solve_id, itemid, parentparentid, parentid, parentcn, date, billable, hours, start, eind, itemname)
{
	var edit_button = element_id;

	//body van popup legen
	jQuery('modal_body').empty();

	document.getElementById(edit_button).setAttribute("href", "#popup");
	document.getElementById(edit_button).setAttribute("data-toggle", "modal");
	
	//Gegevens die nodig zijn voor ajax call
	var url = jQuery('form').attr('action')+'timetrackings/getModalContent';
	var popup_type = 'edit_timerecord';
	
	document.getElementById('form_action').name='edit_timerecord'; //wordt gebruikt voor de afhandeling tijdens submit in 'modal.js'

	jQuery.post(url, { type:popup_type}, function(data){})
	.success(function(data){document.getElementById('modal_body').innerHTML=data;})
	.error(function(){})
	.complete(function(){ 
		document.getElementById('modal_title').innerHTML		= 'Tijdregistratie wijzigen';
		document.getElementById('modal_beschrijving').value		= details;
		document.getElementById('modal_itemid').value			= itemid;
		document.getElementById('modal_parentparentid').value	= parentparentid;
		document.getElementById('modal_parentid').value			= parentid;
		document.getElementById('modal_parentcn').value			= parentcn;
		document.getElementById('modal_date').value				= date;
		document.getElementById('modal_billable').value			= billable;
		document.getElementById('modal_hours').value			= hours;
		document.getElementById('modal_start').value			= start;
		document.getElementById('modal_eind').value				= eind;
		document.getElementById('modal_itemname').innerHTML		= itemname;
		document.getElementById('modal_key').value				= id;
		document.getElementById('key').value					= id;
		document.getElementById('modal_solveid').value			= solve_id;
		document.getElementById('submit_form').value			='Updaten';
	});
}






function deleteTimerecord(id, element_id){ //opgeroepen wanneer  een delete button van tijdregistratie geselecteerd
	jQuery(document).ready(function() {
		var timerecord_id = id;
		var delete_button = element_id;
		
		//body van popup legen
		jQuery('modal_body').empty();
		
		document.getElementById(delete_button).setAttribute("href", "#popup");
		document.getElementById(delete_button).setAttribute("data-toggle", "modal");
			
		//Gegevens die nodig zijn voor ajax call
		var url = jQuery('form').attr('action')+'timetrackings/getModalContent';
		var popup_type = 'delete_timerecord';
		
		//jQuery('image').show()
		jQuery.post(url, { type:popup_type}, function(data){})
		.success(function(data){
			document.getElementById('form_action').action='/timetrackings/delete_timerecord';
  			document.getElementById('form_action').name='delete_timerecord'; //wordt gebruikt voor de afhandeling tijdens submit in 'modal.js'
			document.getElementById('modal_body').innerHTML=data;})
		.error(function(){})
		.complete(function(data){ 
			document.getElementById('modal_title').innerHTML='Verwijderen';
			document.getElementById('modal_timerecordId').value=timerecord_id;
			document.getElementById('submit_form').value='Verwijderen';
		});
	});
	
}



/* attach a submit handler to the form when you want to save a timerecord*/ 
/*
 * showPopup() wordt vanuit stopwatch.js aangeroepen in de stopclock functie
 */
function showPopup(){
	var start= document.getElementById('starttijd').value;
	var eind = document.getElementById('eindtijd').value;
	var duur = document.getElementById('time_recording').value;
	var declarabel = document.getElementById('pricing').value;
	var beschrijving = document.getElementById('activiteit').value;
	
	
	var taakproject = document.getElementById('timerecording_autocomplete').value;
	taakproject = taakproject.split('['); //gedeelte van het project verwijderen uit string
	var taak = taakproject[0]; //alleen linkerdeel van de gesplitste string gebruiken.
	var project = taakproject[1]; //rechterdeel van de gesplitste string.
	if(project){project = project.replace("]","");	}	
	if(declarabel <= 0){ declarabel="Onbetaald";}
	
	taak = taak.split('['); //gedeelte van het project verwijderen uit string
	taak = taak[0]; //alleen linkerdeel van de gesplitste string gebruiken.
		
	document.getElementById('startstop').setAttribute("href", "#popup");
	document.getElementById('startstop').setAttribute("data-toggle", "modal");
	
	//body van popup legen
	jQuery('modal_body').empty();
		
	//Gegevens die nodig zijn voor ajax call
	var url = jQuery('form').attr('action')+'timetrackings/getModalContent';
	var popup_type='save_timerecord';
	
	//ajax call naar controller. 
	jQuery.post( url, { type:popup_type},
  		function( data ) {})
  		.success(function(data){
  			document.getElementById('form_action').action='/timetrackings/save_timerecord';
  			document.getElementById('form_action').name='save_timerecord'; //wordt gebruikt voor de afhandeling tijdens submit in 'modal.js'
  			document.getElementById('modal_body').innerHTML=data;})//zet de opmaak van de content in de popup (labels,invoervelden etc.)
  		.error(function(){})
  		.complete(function(){
  		
  			//Alle velden vullen met informatie
  			document.getElementById('modal_title').innerHTML		='Bevestiging';
  			document.getElementById('modal_duur').value 			= duur;
			document.getElementById('modal_starttijd').value		= start;
			document.getElementById('modal_eindtijd').value 		= eind;
			document.getElementById('modal_declarabel').innerHTML 	= declarabel;
			document.getElementById('modal_beschrijving').value		= beschrijving;
			document.getElementById('modal_parentcn').value			= taak;
			document.getElementById('modal_itemname').innerHTML		= project;
			document.getElementById('submit_form').value			='Opslaan';
			
			//======Wanneer je over je begroting zit en de extra tijd niet declarabel is============//
			if(document.getElementById('extratime').value==0 && document.getElementById('extratime').value!=''){
				document.getElementById('modal_declarabel').innerHTML = 'Onbetaald ;-(';
			}
			//=====================================//
		}
	);
	return;
}





function refresh_taskTable(){
	jQuery(document).ready(function() {
		jQuery.post('/timetrackings/initialiseTaskTable', function(data) {
		})
		.success(function(){/*alert('Tabel is vernieuwd');*/})
		.error(function(){/*alert('Tabel kan door onbekende reden niet vernieuwd worden');*/})
		.complete(function(data){
			jQuery(document).ready(function() {
				jQuery('#task_table').empty().append(data.responseText);
			});
		});
	});
}
function refresh_timerecordTable(){
	jQuery(document).ready(function() {
		jQuery.post('/timetrackings/initialiseTimerecordTable', function(data) {
				
		})
		.success(function(){/*alert('timerecord Tabel is vernieuwd');*/})
		.error(function(){/*alert('Tabel kan door onbekende reden niet vernieuwd worden');*/})
		.complete(function(data){/*alert('refresh timerecordtable complete');*/jQuery('#timerecord_table').empty().append(data.responseText);refresh_taskTable();});
	});
}
function showAllTasks(){
	jQuery(document).ready(function() {
		jQuery.post('/timetrackings/initialiseAllTasksTable', function(data){
	
		})
		.complete(function(data){
			jQuery('#timerecord_table').empty();
			jQuery('#task_table_block h3').text('Alle openstaande taken');
			jQuery('#task_table_block h3').show();
			jQuery('#task_table').empty().append(data.responseText);
			jQuery('#timerecord_table_block h3').hide();
		});
	});
}
function showAllTimerecords(){
	jQuery(document).ready(function() {
		jQuery.post('/timetrackings/initialiseAllTimerecordsTable', function(data){
	
		})
		.complete(function(data){
			jQuery('#task_table').empty();
			jQuery('#timerecord_table_block h3').show();
			jQuery('#timerecord_table_block h3').text('Alle tijdregistraties');
			jQuery('#timerecord_table').empty().append(data.responseText);
			jQuery('#task_table_block h3').hide();
			jQuery('.dropdown').hide();
		});
	});
}
function showGhostTimerecords(){
	jQuery(document).ready(function() {
		jQuery.post('/timetrackings/initialiseGhostTimerecordsTable', function(data){})
		.complete(function(data){
			jQuery('#task_table').empty();
			jQuery('#timerecord_table_block h3').text('Niet gekoppelde tijdregistraties');
			jQuery('#timerecord_table_block h3').show();
			jQuery('#timerecord_table').empty().append(data.responseText);
			jQuery('#task_table_block h3').hide();
		});
	});
}
function showTop5(){
	refresh_timerecordTable();
	refresh_taskTable();
	jQuery('#task_table_block h3').text('Top 5 taken');
	jQuery('#timerecord_table_block h3').text('Top 5 tijdregistraties');
	jQuery('#timerecord_table_block h3').show();
	jQuery('#task_table_block h3').show();
	
}
function update_tasktasklist(){
	jQuery(document).ready(function() {
		jQuery.post('/timetrackings/updateTaskTaskList', function(data) {
		})
		.success(function(){/*alert('Tabel is vernieuwd');*/})
		.error(function(){/*alert('Tabel kan door onbekende reden niet vernieuwd worden')*/;})
		.complete(function(data){
			alert('Succesvol bijgewerkt');
			refresh_taskTable();
			refresh_timerecordTable();
		});
	});
}
