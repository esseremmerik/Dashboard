<script type="text/javascript">
/*jslint  browser: true, white: true, plusplus: true */
/*global $: true */
jQuery(document).ready(function() {
	//var a = jQuery('.autocomplete-ajax').autocomplete({
	var a = jQuery(document).on("keydown.autocomplete", ".autocomplete-ajax", function(e) {
		jQuery(this).autocomplete({
			serviceUrl:'<? echo mvc_plugin_app_url(MVCMODULENAME).  'views/layouts/lookup.php'; ?>',
			minChars:1,
			delimiter: /(,|;)\s*/, // regex or character
			maxHeight:400,
			width:750,
			zIndex: 9999,
			deferRequestBy: 0, //miliseconds
			params: { userid:'<?php echo $this->current_solve_user()->id; ?>', action:'<?php echo $lookup_action; ?>' }, //aditional parameters
			noCache: false, //default is false, set to true to disable caching
			// callback function:
			onSelect: function(suggestion){  
				var data = suggestion.data.split(';;;');
				var id = data[0];
				var completed = data[1];
				var deadline = data[2];
				var itemid = data[3];
				var pricing = data[4];
				var tasklistid = data[5];
				var tasklistname= data[6];
				var solve_id = data[7];
					
				document.getElementById('parentid').value = solve_id; //hetzelfde als 'parentid' van timerecord
				document.getElementById('itemid').value = itemid; //is hetzelde als itemid van timerecord
				document.getElementById('pricing').value = pricing;
				document.getElementById('tasklistid').value = tasklistid;
				document.getElementById('key').value = id;
				
				if(document.getElementById('modal_parentcn')){
					document.getElementById('modal_parentcn').focus();
				}
			}
		});
	});
});
</script>