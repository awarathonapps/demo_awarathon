<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$base_url = base_url();?>            
<div class="modal-header">
    <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Trainee Detail Report</h4>
</div>
<div class="modal-body">                           
    <div class="row">
        <div class="col-md-12">                                
            <div class="col-md-12">
                <div class='col-md-6' id='ChartDiv' style='border:1px solid #d4d4d4;padding: 5px;'>    
                    <div id="AppendChart"  >
                        <div id='container'>
                            <div id='chart' >

                            </div>
                        </div>
                    </div>                                    
                </div>
                <div class="col-md-6" id='tablecontainer'>
                    <?php echo $Table ?> 
                    <span  id='qatable'>

                    </span>
                </div>                                    
            </div>
            <div class="col-md-12" id="AppendChart2"> 

            </div>                                
        </div>                          
    </div>
</div>
<script>
    var Lable = <?php echo $label; ?>;
    $(document).ready(function () {
        var Dataset = <?php echo $dataset; ?>;
        Highcharts.chart('chart', {
            chart: {
                type: 'bar'
            },
            title: {
                text: 'Workshop :' + Lable,
                'style': {
                    'fontSize': '12px',
                    'fontFamily': 'Arial'
                }
            },
            subtitle: {
                text: 'Trainee :<?php echo $Trainee_name; ?>'
            },
            xAxis: {
                categories: Lable,
                title: {
                    text: 'Workshop'
                }
            },
            yAxis: {
                title: {
                    text: 'Post Compentency',
                    align: 'middle'
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
            credits: {
                enabled: false
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
                            color: 'black',
                            fontSize: '10px',
                        }
                    }
                }
            },
            series: [{
                    name: 'Post Compentency',
                    data: Dataset,
<?php echo (count((array)$label) > 10 ? '' : 'pointWidth: 28,') ?>
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
    function WorkshopWiseTopicSubtopicGraph(workshop_id, trainee_id) {
        $.ajax({
            type: "POST",
            url: "<?php echo $base_url; ?>trainer_trainee_workshop_reports/Detail_TopicSubtopicChart",
            data: {workshop_id: workshop_id, trainee_id: trainee_id},
            beforeSend: function () {
                customBlockUI();
            },
            success: function (Data) {
                if (Data != '') {
                    var Oresult = jQuery.parseJSON(Data);
                    var ChartMSt = Oresult['HTMLGraphData'];
                    var QATableMSt = Oresult['QATable'];
                    if (Oresult['Error'] != '') {
                        $('#errordiv').show();
                        $('#errorlog').html(Oresult['Error']);
                        App.scrollTo(form_error, -200);
                    } else {
                        $('#AppendChart2').empty();
                        $('#AppendChart2').append(ChartMSt);
                        $('#qatable').empty();
                        $('#qatable').append(QATableMSt);
                        $("#Mwrk" + trainee_id + " td").addClass("selectedBox");
                    }
                }
                customunBlockUI();
            }
        });
    }
</script>