<table class="table  table-bordered table-hover table-checkable order-column" id="index_table">
	<thead>
		<tr>
			<th>E code</th>
			<th>Employee Name</th>
			<!-- <th>DoJ</th> -->
			<th>Division</th>
			<!-- <th>PC HQ</th> -->
			<!-- <th>State</th> -->
			<th>Zone</th>
			<th>Region</th>
			<th>L+1 EC</th>
			<th>L+1 Name</th>
			<th>Email ID</th>
			<th>Designation</th>
			<th>Assessment Name</th>
			<th>Employee Status</th>
			<th>AI Score %</th>		  
			<?php 
			if (!empty($parameter_score_result)) {
				foreach($parameter_score_result as $param){ ?>
					   <th ><?= $param. ' %'  ?></th>
			<?php }
			} ?>
			<?php
			if (!empty($parameter_score_manaul_result)) {
				foreach ($parameter_score_manaul_result as $params_manaul) { ?>
					<th><?= $params_manaul . ' Manual %'  ?></th>
			<?php }
			} ?>
			<th>Assessor Rating %</th>
			<th>Overall Avg (AI and Assessor) %</th>
			<!-- <th>Diff (AI -Assesor)</th> -->
			<th>Number of Attempts</th>
			<th>Range AI</th>
			<th>Range Asssesor</th>
			<!-- <th>Joining Range</th>  -->
	  </tr>
	</thead>
	<tbody></tbody>
</table>