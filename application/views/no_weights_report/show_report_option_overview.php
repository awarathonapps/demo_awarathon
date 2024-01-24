<div class='col-sm-6' id='ChartDiv'  style='border:1px solid #d4d4d4;padding: 2px;'>    
    <div id='container'>
        <div id='chart_opt_overview' style='min-width: 310px; height: 250px; margin: 0 auto'></div>
    </div>
</div>
<script>        
    $(document).ready(function () {
    Highcharts.chart('chart_opt_overview', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: '<b> Overview </b>' ,                    
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.2f}%</b>'
            },
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
            credits: {
                enabled: false
            },
            series: [{
                name: 'Selected User',
                colorByPoint: true,
                data: [{
                    name: 'Option A',
                    y: <?php echo $option_a ?>,
                    color:' #06f',
                    sliced: true,
                    selected: true
                }, {
                    name: 'Option B',
                    y: <?php echo $option_b ?>,
                    color:'#99ccff'
                }, {
                    name: 'Option C',
                    y: <?php echo $option_c ?>,
                    color:'#e5b8b7'
                }, {
                    name: 'Option D',
                    y: <?php echo $option_d ?>,
                    color:'#953734'
                }, {
                    name: 'Option E',
                    y: <?php echo $option_e ?>,
                    color:'#d6e3bc'
                }, {
                    name: 'Option F',
                    y: <?php echo $option_f ?>,
                    color:'#76923c'
                }]
            }]
        });
    });
</script>                              
