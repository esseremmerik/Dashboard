jQuery(document).ready(function() {
	jQuery('#close').click(function(){
		document.getElementById('extratime').value ='';
	});
	jQuery(document).on("change focus input", "#modal_starttijd", function(e) {
		var pattern = new RegExp("[0-9]{2}:[0-9]{2}");
		if(pattern.test(document.getElementById('modal_starttijd').value)){
			changeDuur();
			e.preventDefault();
		}
	});
	jQuery(document).on("change focus input", "#modal_eindtijd", function(e) {
		var pattern = new RegExp("[0-9]{2}:[0-9]{2}");
		if(pattern.test(document.getElementById('modal_eindtijd').value)){
			changeDuur();
			e.preventDefault();
		}
	});
	jQuery(document).on('keydown click focus', '#datepicker', function(e){
		jQuery(this).datepicker();  
	});   			
	jQuery(document).on("change input paste keyup focus ", "#modal_parentcn", function(e) {
		var patt1=new RegExp("\\[");
		if(patt1.test(document.getElementById('modal_parentcn').value)){
			var aCategorie = ((document.getElementById('modal_parentcn').value).replace(']', '')).split("[");
			//var tasklistname = aCategorie[2];  //parentparentcn
			document.getElementById('modal_parentcn').value = aCategorie[0];
			document.getElementById('modal_itemname').innerHTML = aCategorie[1]; 									//projectname
			document.getElementById('modal_parentid').value = document.getElementById('parentid').value;		//task ID
			document.getElementById('modal_parentparentid').value = document.getElementById('tasklistid').value;//tasklist ID
			
		}
	});
	jQuery('#form_action').submit(function(event) {
		/**
		* All names:
		* save_timerecord
		* delete_timerecord
		*/
		
		/* stop form from submitting normally */
		event.preventDefault();
		
		var name = document.getElementById('form_action').name;	//soort popup functie
		var task_id= '';

		//============Save timerecord =============//
		if(name=='save_timerecord'){
			
			var aTimerecord = new Array();
			var form = document.getElementById('form_action');
				
			aTimerecord['parentid'] 		= document.getElementById('parentid').value; 				//task id of solve360
			aTimerecord['parentcn'] 		= form.elements['modal_parentcn'].value; 					//task name
			aTimerecord['itemid']			= document.getElementById('itemid').value; 					//timerecord itemid
			aTimerecord['details']			= form.elements['modal_beschrijving'].value; 				//timerecord title
			aTimerecord['hours']			= form.elements['modal_duur'].value; 						//timerecord time
			aTimerecord['start']			= form.elements['modal_starttijd'].value;
			aTimerecord['eind']				= form.elements['modal_eindtijd'].value;
			aTimerecord['itemname']			= document.getElementById('modal_itemname').innerHTML; 		//project name
			aTimerecord['date']				= form.elements['modal_datum'].value; 						// date to write timerecord to
			aTimerecord['billable']			= document.getElementById('modal_declarabel').innerHTML;
			aTimerecord['parentparentid']	= document.getElementById('tasklistid').value; 				//tasklist id
			

			//======= wanneer de tijd die je over je begroting zit declarabel is=======================//
			if(document.getElementById('extratime').value==1){ //Tijd is boven begroting, maar is declarabel				
		  		task_id = document.getElementById('key').value;
				estimated = document.getElementById('modal_duur').value;
				url = jQuery('form').attr('action')+'timetrackings/update_task';
				jQuery('#popup').modal('hide');
				
				jQuery.post( url, { 
	  				taskid:task_id,
	  				estimated_time:estimated
	  				},
	  				function( data ) {})
	  				.success(function(){})
		  			.error(function(){})
		  			.complete(function(){}
		  		);
			}
			
			//=======wanneer de tijd die je over je begroting zit niet declarabel is==========================//
			if(document.getElementById('extratime').value==0 && document.getElementById('extratime').value!=''){
				aTimerecord['billable'] = 0;
			}
			
			//=====Normal save=============////=================================================//
			document.getElementById('extratime').value='';
			
			jQuery('#popup').modal('hide');
			save_timerecord(aTimerecord);
		}//einde if 'save_timerecord'
		
		//======== Edit timerecord ===========//
		if(name=='edit_timerecord'){
			var aTimerecord = new Array();
			var form = document.getElementById('form_action');
			
			//====save new timerecord====
			aTimerecord['solve_id']			= form.elements['modal_solveid'].value;						//solve id
			aTimerecord['details']			= form.elements['modal_beschrijving'].value; 				//timerecord title
			aTimerecord['itemid']			= form.elements['modal_itemid'].value; 
			aTimerecord['parentparentid']	= form.elements['modal_parentparentid'].value; 
			aTimerecord['parentid']			= form.elements['modal_parentid'].value; 
			aTimerecord['parentcn']			= form.elements['modal_parentcn'].value; 
			aTimerecord['date']				= form.elements['modal_date'].value; 
			aTimerecord['billable']			= form.elements['modal_billable'].value; 
			aTimerecord['hours']			= form.elements['modal_hours'].value; 
			aTimerecord['start']			= form.elements['modal_start'].value; 
			aTimerecord['eind']				= form.elements['modal_eind'].value; 
			aTimerecord['itemname']			= document.getElementById('modal_itemname').innerHTML;
			aTimerecord['gewijzigd']		= 1;
			
			document.getElementById('extratime').value='';
			
			jQuery('#popup').modal('hide');
			
			save_timerecord(aTimerecord);
			
			aTimerecord['id'] = document.getElementById('modal_key').value; //Id van record om te verwijderen
			delete_timerecord(aTimerecord);
		}
		
		//========Delete timerecord ===========//
		if(name=='delete_timerecord'){
			var aTimerecord = new Array();
			
			aTimerecord['id'] = document.getElementById('modal_timerecordId').value;
			
			jQuery('#popup').modal('hide');
			
			delete_timerecord(aTimerecord);
		}
		
		//=======Update Task =============//
		if(name=='update_task'){
			var task_id = '';
			var estimated='';
			
			document.getElementById('autocomplete-ajax').value = document.getElementById('modal_taskname').innerHTML+' ['+document.getElementById('modal_projectname').innerHTML+']';
			document.getElementById('startstop').click();
			
			jQuery('#popup').modal('hide');
		}			
	});
});
	
	
function rDeclarabel(){
	document.getElementById('extratime').value 	= 1;
	document.getElementById('pricing').value	= document.getElementById('modal_pricing').value;
}
function rFree(){
	document.getElementById('extratime').value 	= 0;
	document.getElementById('pricing').value	= 0;
}
function changeDuur(){
	jQuery(document).ready(function() {
		var start= document.getElementById('modal_starttijd').value.split(':');
		var eind = document.getElementById('modal_eindtijd').value.split(':');
		var duur = new Array();
		duur[0] = Math.max(start[0],eind[0])-Math.min(start[0], eind[0]);
		duur[1] = Math.max(start[1],eind[1])-Math.min(start[1], eind[1]);
		if(start[0]>=18 && eind[0]<6){duur[0] =((24-start[0])+eind[0]);}
		if(eind[1]<parseInt(start[1])){ 
			duur[1] = parseInt((60-start[1]))+parseInt(eind[1]);
			duur[0] = duur[0]-1;
		}
		if(duur[0]<10){duur[0] = '0'+duur[0];}
		if(duur[0]<1 && eind[1]<start[1]){duur[0] = '00';}
		if(duur[1]<10){duur[1] = '0'+duur[1];}
		oude_duur = document.getElementById('modal_duur').value.split(':'); 
		oude_duur_seconden = oude_duur[2];
		document.getElementById('modal_duur').value = duur[0] + ':' + duur[1] + ':' + oude_duur_seconden;
	});
}
function delete_timerecord(aTimerecord){
	var url = jQuery('form').attr('action')+'timetrackings/delete_timerecord';
	
	jQuery.post(url, {timerecordid:aTimerecord['id']},
	function(data) {})
				.success(function(){
					refresh_taskTable();
					refresh_timerecordTable();
			});
	return;
}
function save_timerecord(aTimerecord){
	var url = jQuery('form').attr('action')+'timetrackings/save_timerecord';
	
	jQuery.post(url, {
			solve_id		: aTimerecord['solve_id'],
			parentid		: aTimerecord['parentid'],
			parentcn		: aTimerecord['parentcn'],
			itemid			: aTimerecord['itemid'],				
			details			: aTimerecord['details'],			
			hours			: aTimerecord['hours'],		
			start			: aTimerecord['start'],			
			eind			: aTimerecord['eind'],				
			itemname		: aTimerecord['itemname'],			
			date			: aTimerecord['date'],				
			billable		: aTimerecord['billable'],			
			parentparentid	: aTimerecord['parentparentid'],
			gewijzigd		: aTimerecord['gewijzigd']
		},
		function(data){})
		.success(function(){
			document.getElementById('starttijd').value = '';
			document.getElementById('timerecording_autocomplete').value = '';
			document.getElementById('activiteit').value = '';
			document.getElementById('eindtijd').value = '';
			document.getElementById('parentid').value = '';
			document.getElementById('itemid').value = '';
			document.getElementById('pricing').value = '';
			document.getElementById('tasklistid').value = '';
			document.getElementById('extratime').value = '';
			document.getElementById('key').value = '';
			
			refresh_taskTable();
			refresh_timerecordTable();
	});
	return;
}