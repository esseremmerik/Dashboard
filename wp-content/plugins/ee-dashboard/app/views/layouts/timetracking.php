<?php get_header(); 

define('BASE_PATH', dirname(dirname(dirname(__FILE__))) . "/");
include BASE_PATH . 'update_wp_database.php';

wp_enqueue_style('general', '/wp-content/plugins/ee-dashboard/app/public/css/general.css'); 
//wp_enqueue_style('autocomplete', '/wp-content/plugins/ee-dashboard/app/public/css/jquery-ui.css');
wp_enqueue_style('datepicker', '/wp-content/plugins/ee-dashboard/app/public/css/datepicker.css'); 
wp_enqueue_style('modal', '/wp-content/plugins/ee-dashboard/app/public/css/modal.css'); 

//wp_enqueue_script('jquery_ui', mvc_js_url('ee-dashboard', 'jquery-ui.js'));

wp_enqueue_script('timerec', mvc_js_url('ee-dashboard', 'timetracking.js')); 
wp_enqueue_script('jquery.autocomplete', mvc_js_url('ee-dashboard', 'jquery.autocomplete.js'));
?>

<?php //test //update_timerecords();?>

<div><?php $this->render_view('layouts/_timerecording_block'); ?></div>

<button class='btn btn-large btn-success' onclick="showTop5();">Top500</button></a>
<button class='btn btn-large btn-success' onclick="showAllTasks();">Alle open taken</button></a>
<button class='btn btn-large btn-success' onclick="showAllTimerecords();">Alle tijdregistraties</button></a>
<button class='btn btn-large btn-success' onclick="showGhostTimerecords();">Niet gekoppelde tijdregistraties</button></a>
<button class='btn btn-large btn-success' style='margin-left:50px;' onclick="update_tasktasklist();">Update taak/taaklijst</button></a>
	
<div id='wrapper'>

<!-- ========= Tabel met de 5 snelst eindigende deadlines ================= -->
<div><?php $this->render_view('layouts/_tasktable_block'); ?></div>



<!-- ========= Tabel met de 5 meest recente tijdregistraties ============== -->
<div><?php $this->render_view('layouts/_timetable_block'); ?></div>


<!--  ======== Popup ========= -->
<div><?php $this->render_view('layouts/_popup_block'); ?></div>


</div> <!--einde wrapper-->



<?php get_footer(); ?>
<?php echo $this->generate_lookup_js(''); ?>