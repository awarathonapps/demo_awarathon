<div class='col-lg-6' id='ChartDiv_<?php echo $TotalChart; ?>' style='border:1px solid #d4d4d4;padding: 5px;'>
    <button id='button-filter' style='text-align: center !important;float: right;height:25px;width:25px;margin:0px;padding:0px;' class='btn btn-sm btn-small btn-danger' type='button' onclick='RemoveChart(<?php echo $TotalChart; ?>);'>X</button>
    <div id='container'>
        <div id='chart_<?php echo $TotalChart; ?>' style='min-width: 310px; height: 350px; margin: 0 auto'></div>
    </div>
</div>
<script>
    var TotalChart = '<?php echo $TotalChart; ?>';
    var Label = <?php echo $label; ?>;
    $(document).ready(function () {
    var Dataset = <?php echo $dataset; ?>;
    Highcharts.chart('chart_' + TotalChart, {
            chart: {
                    type: 'column'
                    },
            title: {
                    text: ' Workshop : Finance Demo Pre (Topic + Subtopic wise)',
                    'style': {
                    'fontSize': '12px',
                    'fontFamily': 'Arial'
                    }
            },
            subtitle: {
                    text: 'Trainer :'+<?php echo $user ?>
                    },
            xAxis: {
                    categories:Label,
                    title: {
                    text: 'Topic+Subtopic wise'
                    }
            },
            yAxis: {
                    title: {
                    text: 'Overall Accuracy',
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
                    column: {
                            dataLabels: {
                            enabled: true,
                            format: '{point.y:.2f}%'
                            }
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
                    }}
            }]
        });
    });
</script>                              
