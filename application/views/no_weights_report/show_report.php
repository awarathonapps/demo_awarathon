<div class='col-sm-6' id='ChartDiv_<?php echo $TotalChart; ?>'  style='border:1px solid #d4d4d4;padding: 5px;'>    
    <div id='container'>
        <div id='chart_<?php echo $TotalChart; ?>' style='min-width: 310px; height: 350px; margin: 0 auto'></div>
<!--        <div>Total User : < ?php echo array_sum($TotalUsers) ?></div>-->
    </div>
</div>
<script>    
    $(document).ready(function () {      
    var maindata = <?php echo $ar_ofuser; ?>    
    Highcharts.chart('chart_<?php echo $TotalChart; ?>', {
            chart: {                                       
                    type: "<?php echo($graphtype_id == 1 ?'pie':'bar'); ?>"
                },
            title: {
                text:   '<b>Question : </b>'+<?php echo $Question_title ?> , 
                style: {
                    font: 'normal 13px Verdana, sans-serif'
                }
            },
            <?php if($graphtype_id == 2 ) { ?>
            xAxis: {
                categories:<?php echo  $label; ?>,                 
                title: {
                    text: ''
                }        
            },            
            yAxis: {                
                title: {
                    text : 'Average <br><br>No. of responses : <?php echo array_sum($TotalUsers) ?>',
                    align: 'middle',
                },                
                labels: {
                    formatter: function () {
                        return this.value + '%';
                    },
                    overflow: 'justify'
                }
            },<?php } ?>
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.2f}%</b>'
            },            
            <?php if($graphtype_id == 2 ) { ?>           
            plotOptions: {
                series: {
                    colorByPoint: true,
                    enableMouseTracking: false
                },
                bar: {                       
                    dataLabels: {                        
                        enabled: true,
                        style: {
                            textOutline: 0
                        },
                        formatter: function () {
                            return Highcharts.numberFormat(this.y,2)+ '% ('+ maindata[this.point.index]+')';
                        }                        
                    },
                    showInLegend: false   
                }                                
            },           
            <?php }else{ ?> 
             plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',                    
                    dataLabels: {
                        enabled: false                        
                    },                    
                    showInLegend: true                    
                }                
            },                  
            <?php } ?> 
            credits: {
                enabled: false
            },                                                                        
            series: [{
                name: 'Selected Option',                
                data: [{
                    name: <?php echo $option_a_title ?>+' ('+<?php echo $selected_user_a ?>+')',
                    y: <?php echo $option_a ?> ,
                    color:'#06f'                                        
                }, {
                    name: <?php echo $option_b_title ?>+' ('+<?php echo $selected_user_b ?>+')',
                    y: <?php echo $option_b ?>,
                    color:'#99ccff'
                },
                <?php if($option_c_title !='""') {?>{
                    name: <?php echo $option_c_title ?>+' ('+<?php echo $selected_user_c ?>+')',
                    y: <?php echo $option_c ?>,
                    color:'#e5b8b7'
                },
                <?php } if($option_d_title !='""') {?>{
                    name: <?php echo $option_d_title ?>+' ('+<?php echo $selected_user_d ?>+')',
                    y: <?php echo $option_d ?>,
                    color:'#953734'
                }, 
                <?php } if($option_e_title !='""') {?>
                    {
                    name: <?php echo $option_e_title ?>+' ('+<?php echo $selected_user_e ?>+')',
                    y: <?php echo $option_e ?>,
                    color:'#d6e3bc'
                    },
                <?php } ?>
                <?php if($option_f_title !='""') {?>
                    {
                    name: <?php echo $option_f_title ?>+' ('+<?php echo $selected_user_f ?>+')',
                    y: <?php echo $option_f ?>,
                    color:'#76923c'
                    }
                <?php } ?>
                ]                
            }]
        });
    });    
</script>                              
