<div class='col-lg-12' id='ChartDiv' style='border:1px solid #d4d4d4;padding: 5px;'>    
    <div id='container'>
        <div id='histogram' style='min-width: 310px; height: 370px; margin: 0 auto'></div>
    </div>
</div>
<script>
    var graph_type = '<?php echo ($graphtype_id == '' ? 1 : $graphtype_id );?>' 
    var histogramData = <?php echo $dataset; ?>;
    var Lable = <?php echo $label; ?>;
    $(document).ready(function () {    
    Highcharts.chart('histogram', {
            chart: {
                type: (graph_type == 1 ? 'spline' : graph_type == 2 ? 'column':'')
            },
            title: {
                text: '<?php echo $report_title; ?>',
                    'style': {
                        'fontSize'  : '12px',
                        'fontFamily': 'Arial'
                    }
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories:Lable,
                title: {
                    text: 'Avg Score Range'
                }
            },
            yAxis: {
                allowDecimals: false,
                title: {
                        text : 'Workshop Frequency',
                        align: 'middle',
                    },
            labels: {
                formatter: function () {
                    return this.value ;
                    },
                    overflow: 'justify'
                }
            },
//            tooltip: {
//                valueSuffix: '%'
//            },
//            legend: {
//                enabled: true
//            },
            plotOptions: {
                column: {
                    dataLabels: {
                       // enabled: true
                    }
                }
            },
            credits: {
                enabled: false
            },
            series: [
                    <?php if($graphtype_id==3){ ?>
                    {
                    type: (graph_type == 3  ? 'spline' :''),                     
                    name: 'Workshop Frequency',
                    data: (graph_type != 2  ? histogramData :''),                    
                    <?php echo (count((array)$label) > 10 ? '':'pointWidth: 28,') ?>                   
                    color: '#2E64FE',
                    dataLabels: {
                        style: {
                        fontWeight: 'normal',
                        textOutline: '0',
                        color: 'black',
                        'fontSize': '12px',
                        }
                    }
                    }, <?php } ?>
                    {                    
                    type: (graph_type == 1 ? 'spline' :'column'),  
                    name: 'Workshop Frequency',
                    data: histogramData,
                    color: '#2E64FE',
                    <?php echo (count((array)$label) > 10 ? '':'pointWidth: 28,') ?>                    
                    
                }]
        });
    });
</script>                              
