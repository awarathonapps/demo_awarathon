<?php $base_url = base_url();    
?>
<div class="modal-header no-padding">
    <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Region Level Report</h4>
</div>
<div class="row">
<div class="col-md-12">
	<div id="container" style="max-height:370px; overflow-y:auto; ">
		<div id='region_level_graph' style=' height: 450px;'></div>
	</div>
 </div>
</div>
<script>           
    $(document).ready(function () {    
        Highcharts.chart('region_level_graph', {
            chart: {
                type: 'bar'
            },
            title: {
                text: ''
            },
            xAxis: {
                categories: <?php echo $assessment ;?>
            },
            yAxis: {
                min: 0,
                max:100,
                title: {
                    text: 'Percentage'
                }
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.2f}%</b>'            
            },
            legend: {
                reversed: true
            },     
			exporting: { 
				enabled: true
			},	
			credits: {
				enabled: false
			},
            plotOptions: {
                series: {
                    stacking: 'normal'
                },
                bar: {
                    dataLabels: {
                        enabled: true,
                        format: '{point.y:.2f}%',
                        allowOverlap: true,
                        crop: false,
                        //style: {
                          //  fontWeight: 'normal',
                            //textOutline: '0',
                            //color:'white',
                            //fontSize: '10px',
                        //}
                    }
                }
            },
            series: [ {
                name: 'Pending',
                color:<?php echo $pending_color_code ?>,
                data: <?php echo $pending; ?>
            }, {
                name: 'Fail',
                color:<?php echo $fail_color_code ?>,
                data: <?php echo $fail; ?>
            },{
                name: 'Pass',
                color:<?php echo $pass_color_code ?>,
                data: <?php echo $pass;  ?>
            }]
        });
    });
</script>                              
