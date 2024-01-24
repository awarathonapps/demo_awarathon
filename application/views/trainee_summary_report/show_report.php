<div class='col-lg-6' id='ChartDiv' style='border:1px solid #d4d4d4;padding: 5px;'>    
    <div id='container'>
        <div id='chart' style='min-width: 310px; height: 400px; margin: 0 auto'></div>
    </div>
</div>
<script>    
    var Lable = <?php echo $label; ?>;    
    $(document).ready(function () {   
    var Dataset = <?php echo $dataset; ?>;
    Highcharts.chart('chart', {
            chart:{
                type: 'bar'
            },
            title: {
            text: 'Trainee Workshop Tab: Summary Report',
                    'style': {
                    'fontSize': '12px',
                    'fontFamily': 'Arial'
                    }
            },
            subtitle: {
                text: 'Trainee :'+Lable
            },
            xAxis: {
                categories:Lable,
                title: {
                text: 'User wise'
                }
            },
            yAxis: {
                title: {
                text: 'Overall C.E.',
                align: 'middle',
            },
            labels: {
                formatter: function () {
                return this.value + '%';
                },
                overflow: 'justify'
            }
            },
            tooltip: {
            valueSuffix: '%'
            },
            legend: {
            enabled: false
            },
            plotOptions: {
                bar: {
                    zones: [{
                        value: 0, 
                        color: 'red'
                    },{
                        color: '#0070c0' 
                    }]
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                    name: 'Accuracy',
                    data: Dataset,
                    <?php echo (count($label) > 10 ? '':'pointWidth: 28,') ?>                   
                    color: '#0070c0',
                    dataLabels: {
                        style: {
                            fontWeight: 'normal',
                            textOutline: '0',
                            color: 'black',
                            'fontSize': '12px',
                        }
                    }
            }]
        });
    });
</script>                              
