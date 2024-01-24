<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$base_url = base_url();
?>
<div class="modal-header">
    <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Trainee Summary Report</h4>
</div>
<div class="modal-body">

    <div class="row">
        <div class="col-md-12">                                
            <div class="col-md-12">
                <div class='col-md-6' id='ChartDiv' style='border:1px solid #d4d4d4;padding: 5px;'>    
                    <div id="AppendChart">

                        <div id='container'>
                            <div id='chart' style='min-width: 310px; height: 400px; margin: 0 auto'>

                            </div>
                        </div>
                    </div>                                    
                </div>
                <div class="col-md-6" id='tablecontainer'>
                    <div class="col-6 table-scrollable" id="maintablecontainer"> 
                    <?php echo $Table ?>
                    </div>
                    <div class="col-6 table-scrollable" id="maintablecontainer">                                    
                        <?php echo $MainTable ?>                                    
                    </div>
                </div>                                    
            </div>                                                        
            <div class="col-md-12 margin-top-20">

            </div>
        </div>    
    </div>
</div>
<script>
    var Lable = <?php echo $label; ?>;
    $(document).ready(function () {
    var Dataset = <?php echo $dataset; ?>;
    var Dataset2 = <?php echo $dataset1; ?>;
    Highcharts.chart('chart', {
    chart:{
    type: 'bar'
    },
            title: {
            text: ' Workshop:<?php echo $WorkshopName; ?>',
                    'style': {
                    'fontSize': '12px',
                            'fontFamily': 'Arial'
                    }
            },
            subtitle: {
            text: 'Trainee :' + Lable
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
            enabled: true
            },
            plotOptions: {
            bar: {
            dataLabels: {
            enabled: true,
                    format: '{point.y:.2f}%',
                    allowOverlap: true,
                    crop: false,
                    style: {
                    fontWeight: 'normal',
                            textOutline: '0',
                            color:'black',
                            fontSize: '10px',
                    }
            }
            }
            },
            credits: {
            enabled: false
            },
            series: [{
            name: 'Positive C.E',
                    data:  Dataset,
<?php echo (count($label) > 10 ? '' : 'pointWidth: 28,') ?>
            stacking: 'normal',
                    color:'#ffc000',
            },
            {
            name: 'Negative C.E',
                    data: Dataset2,
<?php echo (count($label) > 10 ? '' : 'pointWidth: 28,') ?>
            stacking: 'normal',
                    color:'#FF0000',
                    dataLabels: {
                    style: {
                    fontWeight: 'normal',
                            textOutline: '0',
                            color: 'black',
                            'fontSize': '12px',
                    },
                            formatter: function () {
                            if (this.y < 0) {
                            return this.y;
                            }
                            },
                            enabled: true,
                            overflow: 'none'
                    }
            } ]
    });
    });
</script>