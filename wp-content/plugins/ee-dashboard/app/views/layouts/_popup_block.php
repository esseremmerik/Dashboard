<?php 
wp_enqueue_script('modal', mvc_js_url('ee-dashboard', 'modal.js'));
wp_enqueue_script('datepicker', mvc_js_url('ee-dashboard', 'bootstrap-datepicker.js'));
?>

<div class='modal fade' id='popup'>
	<form action='' id='form_action'>
    	 <div class='modal-header'><a class='close' data-dismiss='modal'>&times;</a><h3 id='modal_title'>Popup</h3></div>
       	 <div class='modal-body' id='modal_body'>
    	 </div>
         <div class='modal-footer'><a id='close' href='#' class='btn' data-dismiss='modal'>Sluiten</a><input type='submit' id='submit_form' class='btn btn-primary' value='Submit'/></div>
    </form>
</div>