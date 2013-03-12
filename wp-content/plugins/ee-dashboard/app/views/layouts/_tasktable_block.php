<!-- ========= Tabel met de 5 snelst eindigende deadlines ================= -->
<div id="task_table_block">
		<h3>Jouw 5 dichtbijzijnste deadlines</h3>
		<span class="dropdown" style="margin:0px 0px 0px 50px;">
			<button class="btn btn-mini btn-primary dropdown-toggle" data-toggle="dropdown" href="#" style="width:90px;margin:5px 0px 5px 0px;">Legenda<span class="caret" style="margin-left:10px;"></span></button>
			<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
				<li><a tabindex="-1" href="#"><?php echo $this->getIcon('taak') . ' : Taak';?></a></li>
				<li><a tabindex="-1" href="#"><?php echo $this->getIcon('tijd_wit') . ' :  Jouw geschreven uren';?></a></li>
				<li><a tabindex="-1" href="#">Something else here</a></li>
				<li><a tabindex="-1" href="#"><?php echo $this->getIcon('deadline_wit') . ' : Deadline van de taak';?></a></li>
				<li class="divider"></li>
				<li><a tabindex="-1" href="#">Separated link</a></li>
			</ul>
		</span> 
		<span id='task_table'><?php echo $this->showTaskTable(); ?></span>
</div>
<!-- ====================================================================== -->