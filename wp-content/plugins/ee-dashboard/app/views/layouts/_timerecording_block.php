<?php wp_enqueue_script('stopwatch', mvc_js_url('ee-dashboard', 'stopwatch.js')); ?>
<?php wp_enqueue_script('timerecording_block_js', mvc_js_url('ee-dashboard', '_timerecording_block.js')); ?>
<?php wp_enqueue_style('timerecordingblock', '/wp-content/plugins/ee-dashboard/app/public/css/_timerecording_block.css'); ?>

<!-- ======= Blok waar je een nieuwe tijdregistratie kan starten ======= -->	
<div id="timerecording_block">
	<h3>Wat ga je doen?</h3>
	
	<input id='activiteit' type='text' placeholder='Benoem je activiteit' onfocus="change(1)">
	<input type="text" name="fname" id="time_recording" value="00:00:00"/>
	<button id="startstop" class="btn btn-large btn-warning" onclick="initStopwatch()" value="1" href=""><i id='timericon' class="icon-warning-sign"></i> Start</button>
	<div id='labelwarning'>
		<span id='warning_header'>Warning: <span style='font-size:12px;margin-left:5px;'>geen taak geselecteerd</span></span>
	</div>
	<input type="hidden" id="timerecording_autocomplete" class="autocomplete-ajax" placeholder="Benoem taak of project">
	<div id="selection-ajax"></div>
	<input id="starttijd" style="width:75px;" type="hidden" value="" />
	<input id="eindtijd" style="width:75px;" type="hidden" value="" />
	<input id="parentid" style="width:75px;" type="hidden" value="" />
	<input id="itemid" style="width:75px;" type="hidden" value="" />
	<input id="pricing" style="width:75px;" type="hidden" value="" />
	<input id="tasklistid" style="width:75px;" type="hidden" value="" />
	<input id="extratime" style="width:75px;" type="hidden" value="" />
	<input id="key" style="width:75px;" type="hidden" value="" />

	
</div>

<!-- ========= Einde tijdregistratie maken ===============================-->