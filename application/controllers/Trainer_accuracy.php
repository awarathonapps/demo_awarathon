<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Trainer_accuracy extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('trainer_accuracy');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('trainer_accuracy_model');
        }

    public function index($trainer_id = '') {
        $data['module_id'] = '25.3';
        $data['username'] = $this->mw_session['username'];
        $data['trainee_name'] = $this->mw_session['first_name'] . " " . $this->mw_session['last_name'];
        $data['company_id'] = $this->mw_session['company_id'];
        $data['user_id'] = $this->mw_session['user_id'];
        $data['acces_management'] = $this->acces_management;
        $RightsFlag=1;
        $WRightsFlag=1;
        $company_id="";
        if ($data['company_id'] == "") {
            $data['company_array'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1', 'company_name');
            if($trainer_id !=""){
                    $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type', 'userid=' . $trainer_id);
                $company_id =$Rowset->company_id;
                $data['Trainer_array'] = $this->common_model->get_selected_values('company_users', 'userid,CONCAT(first_name, " " ,last_name) as fullname', 'status="1" AND login_type="1" AND company_id="' . $company_id . '"');
                $data['wksh_type_array'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status="1" AND company_id="' . $company_id . '"', 'workshop_type');
            }
        } else {
            $data['company_array'] = array();
            $Login_id  =$this->mw_session['user_id'];
            if(!$this->mw_session['superaccess']){
            $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
                $WRightsFlag=($Rowset->workshoprights_type==1 ? 1:0);
            }
            $company_id = $data['company_id'];
            if ($RightsFlag) {
                $data['Trainer_array'] = $this->common_model->get_selected_values('company_users', 'userid,CONCAT(first_name, " " ,last_name) as fullname', 'status="1" AND login_type="1" AND company_id="' . $company_id . '"');
            } else {
                $this->common_model->SyncTrainerRights($Login_id);
                $data['Trainer_array'] = $this->common_model->getUserRightsList($company_id, $Login_id);
            }
            if ($WRightsFlag) {
                $data['wksh_type_array'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status="1" AND company_id="' . $company_id . '"', 'workshop_type');
                $data['workshop_array'] = $this->common_model->getTrainerWorkshop($company_id,1);
                $data['RegionData'] = $this->common_model->get_selected_values('region', 'id,region_name', 'company_id=' . $company_id);
            } else {
                $this->common_model->SyncWorkshopRights($Login_id,0);
                $data['wksh_type_array'] = $this->common_model->getWTypeRightsList($company_id);
                $data['workshop_array'] = $this->common_model->getTrainerWorkshop($company_id,0); 
                $data['RegionData'] = $this->common_model->getUserRegionList($company_id);
            }
            
            $data['TraineeRegionData'] = $this->trainer_accuracy_model->get_TraineeRegionData($company_id);
        }
        $data['trainer_id'] = $trainer_id;
        $data['Supcompany_id'] = $company_id;
        
        $data['wksh_top_five_array'] = [];
        $this->load->view('trainer_accuracy/index', $data);
    }

    public function ajax_company_trainer_type() {
        $RightsFlag=1;
        $WRightsFlag=1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
            $Trainer_id = '';
        } else {
            $company_id = $this->mw_session['company_id'];
             $Trainer_id = $this->mw_session['user_id'];
            if(!$this->mw_session['superaccess']){
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Trainer_id);
                $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
                $WRightsFlag=($Rowset->workshoprights_type==1 ? 1:0);
            }
        }
        $lcWhere = 'status=1 AND login_type=1 AND company_id=' . $company_id;
        if ($Trainer_id != "") {
            if ($RightsFlag) {
                $data['user_array'] = $this->common_model->get_selected_values('company_users', 'company_id,userid,CONCAT(first_name, " " ,last_name) as fullname', $lcWhere, "fullname");
            } else {
                $data['user_array'] = $this->common_model->getUserRightsList($company_id, $Trainer_id);
            }
            if ($WRightsFlag) {
                $data['wksh_type_array'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status="1" AND company_id="' . $company_id . '"', 'workshop_type');
                $data['workshop_array'] = $this->common_model->getTrainerWorkshop($company_id,1); 
            } else {
                $data['wksh_type_array'] = $this->common_model->getWTypeRightsList($company_id);
                $data['workshop_array'] = $this->common_model->getTrainerWorkshop($company_id,0);
            }
        } else {
            $data['user_array'] = $this->common_model->get_selected_values('company_users', 'company_id,userid,CONCAT(first_name, " " ,last_name) as fullname', $lcWhere, "fullname");
            $data['wksh_type_array'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status="1" AND company_id="' . $company_id . '"', 'workshop_type');
            $data['workshop_array'] = $this->common_model->getTrainerWorkshop($company_id,1);
        }
        echo json_encode($data);
    }
    public function ajax_fetch_workshop() {
        $WRightsFlag=1;
        $Login_id =$this->mw_session['user_id'];
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            if(!$this->mw_session['superaccess']){
                $Rowset = $this->common_model->get_value('company_users', 'company_id,workshoprights_type', 'userid=' . $Login_id);
                //$RightsFlag=($Rowset->userrights_type==1 ? 1:0);
                $WRightsFlag=($Rowset->workshoprights_type==1 ? 1:0);
            }
        }
        $trainer_id = $this->input->post('user_id', TRUE);
        $workshop_type_id = $this->input->post('workshop_type_id', TRUE);
        $data['workshop_array'] = $this->common_model->getTrainerWorkshop($company_id,$WRightsFlag, $trainer_id,'0', $workshop_type_id);
        echo json_encode($data);
    }
    public function ajax_fetch_trainee() {
        $workshop_id = $this->input->post('workshop_id', TRUE);
        $workshop_session = $this->input->post('workshop_session', TRUE);
        $trainee_array = $this->trainer_accuracy_model->getTrainee($workshop_id, $workshop_session);
         $lchtml='<option value="0">All Select</option>';
        if(count((array)$trainee_array)>0){
            foreach ($trainee_array as $value) {
                $lchtml .='<option value="'.$value->user_id.'">'.$value->username.'</option>';
    }
        }
        echo $lchtml;
    }
    public function load_report() {
        $RightsFlag=1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            if(!$this->mw_session['superaccess']){
            $Login_id  =$this->mw_session['user_id'];
            $Rowset = $this->common_model->get_value('company_users','company_id,userrights_type','userid='.$Login_id);
            $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
        }
        }
        $this->load->model('trainee_reports_model');
        $this->trainee_reports_model->SynchTraineeData($company_id);
        $trainer_id = ($this->input->post('user_id', TRUE) !='' ? $this->input->post('user_id', TRUE) : 0);
        $workshop_type_id = $this->input->post('workshop_type_id', TRUE);
        $workshop_id = $this->input->post('workshop_id', TRUE);
        $workshop_session = $this->input->post('workshop_session', TRUE);
        $trainee_id = ($this->input->post('trainee_id', TRUE)!='' ? $this->input->post('trainee_id', TRUE) : 0);
        $trainee_region_id = $this->input->post('trainee_region_id', TRUE);
        
        $isWorkshopLive =$this->trainer_accuracy_model->isWorkshopLive($workshop_id,$workshop_session);
        
        $TraineeData = $this->trainer_accuracy_model->get_traineeAccuracy($RightsFlag,$trainee_id, $trainer_id, $workshop_id, $workshop_session,$isWorkshopLive,$trainee_region_id);
        //TOP AND BOTTOM 5 TRAINEE
        $top_five_trainee = array();
        $topfivetrainee ='';
        
        $trainee_top_five_array = $this->trainer_accuracy_model->top_five_trainee($RightsFlag,$trainee_id, $trainer_id, $workshop_id,$workshop_session,$isWorkshopLive,$trainee_region_id);
        $trainee_top_five_html = '';
        if (count((array)$trainee_top_five_array) > 0) {
            foreach ($trainee_top_five_array as $trainee_top) {
                $top_five_trainee[] = $trainee_top->trainee_id;
                $trainee_top_five_html .= '<tr class="tr-background">
                                            <td class="wksh-td">' . $trainee_top->trainee_name . '</td>
                                            <td class="wksh-td">
                                                <span class="bold theme-font">' .($trainee_top->accuracy=="" ? "Not Played":$trainee_top->accuracy."%"). '</span>
                                            </td>
                                        </tr>';
            }
        } else {
            $trainee_top_five_html .= '<tr class="tr-background">
                                        <td colspan="2" class="wksh-td">No Records Found</td>
                                    </tr>';
        }
        if(count((array)$top_five_trainee) > 0){
            $topfivetrainee = implode(',', $top_five_trainee);
        }
        $trainee_bottom_five_array = $this->trainer_accuracy_model->bottom_five_trainee($RightsFlag,$trainee_id, $trainer_id, $workshop_id, $workshop_session, $topfivetrainee,$isWorkshopLive,$trainee_region_id);
        $trainee_bottom_five_html = '';
        if (count((array)$trainee_bottom_five_array) > 0) {
            foreach ($trainee_bottom_five_array as $trainee_bottom) {
                $trainee_bottom_five_html .= '<tr class="tr-background">
                                            <td class="wksh-td">' . $trainee_bottom->trainee_name . '</td>
                                            <td class="wksh-td">
                                                <span class="bold theme-font">' . ($trainee_bottom->accuracy=="" ? "Not Played":$trainee_bottom->accuracy."%") . '</span>
                                            </td>
                                        </tr>';
            }
        } else {
            $trainee_bottom_five_html .= '<tr class="tr-background">
                                        <td colspan="2" class="wksh-td">No Records Found</td>
                                    </tr>';
        }
        $topic_subtopic_array = $this->trainer_accuracy_model->get_PrepostAccuracy($workshop_id,$trainee_id,$workshop_session,$trainer_id,$RightsFlag,$isWorkshopLive,$trainee_region_id);
        if (count((array)$topic_subtopic_array) > 0) {
            foreach ($topic_subtopic_array as $tst) {
                $subtopic_name = $tst->subtopic;
                $label[] = $tst->topic  . ($subtopic_name=="No sub-Topic" ? '': '-'.$subtopic_name);
                $dataset[] = $tst->accuracy;
            }
        }else{
            $dataset = [];
            $label = [];
        }
        $trainerTopicSubtopicCEGraph = "<div id='container' style='max-height:600px; overflow-y:auto; '>
                                <div id='topic_subtopic' style='height:".(count((array)$label)>5 ? '600':'400')."px'></div>
                            </div>
                        <script>
                            $(document).ready(function () {
                                var chartData1 =" . json_encode($dataset, JSON_NUMERIC_CHECK) . "
                                Highcharts.chart('topic_subtopic', {
                                    chart: {
                                        type: 'bar'
                                    },
                                    title: {
                                        text: '',
                                        'style': {
                                            'fontSize': '12px',
                                            'fontFamily': 'Arial'
                                        }
                                    },
                                    subtitle: {
                                        text: ''
                                    },
                                    xAxis: {
                                        categories:" . json_encode($label) . ",
                                        title: {
                                            text: 'Topic + Sub Topic Wise'
                                        },
                                        scrollbar: {
                                            enabled: false
                                        },
                                    },
                                    yAxis: {
                                        title: {
                                            text: 'Overall Accuracy',
                                            align: 'middle',
                                        },
                                        labels: {
                                            formatter: function () {
                                                return this.value;
                                            },
                                            overflow: 'justify'

                                        },
                                    },
                                    tooltip: {
                                        valueSuffix: '%'
                                    },
                                    legend: {
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
                                            name: 'Overall Accuracy',
                                            data: chartData1,
                                            " . (count((array)$label) > 10 ? '' : 'pointWidth: 28,') . "
                                            stacking: 'normal',
                                            color:'#0070c0',
                                        }
                                    ]
                                });
                            });
                        </script>";
        $data['topic_subtopic_chart'] = $trainerTopicSubtopicCEGraph;
        
        
        
        $html='';
        if (count((array)$TraineeData) > 0) {
            foreach ($TraineeData as $wksh) {
                $html .= '<tr>
                            <td>' . $wksh->trainee_name . '</td>
                            <td>' . $wksh->played_questions . '</td>
                            <td>' . $wksh->correct . '</td>
                            <td>' . ($wksh->played_questions-$wksh->correct) . '</td>
                            <td>' . ($wksh->accuracy=="" ? "Not Played" : $wksh->accuracy."%") . '</td>
                            <td>' . $wksh->rank . '</td>
                            <td>' . ($wksh->played_questions >0 ? $wksh->status :'Not Attended') . '</td>
                        </tr>';
            }
        } else {
            $html .= '<tr class="tr-background">
                        <td colspan="7" class="wksh-td">No Records Found</td>
                    </tr>';
        }
        
        $data['trainee_top_five_table'] = $trainee_top_five_html;
        $data['trainee_bottom_five_table'] = $trainee_bottom_five_html;
        $data['wksh_list'] = $html;
        echo json_encode($data);
    }
    public function export_workshop(){
        $ExportRights = $this->acces_management;
        if(!$ExportRights->allow_export) {
             redirect('trainer_accuracy');
        }
        $RightsFlag=1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            if(!$this->mw_session['superaccess']){
                $Login_id  =$this->mw_session['user_id'];
                $Rowset = $this->common_model->get_value('company_users','company_id,userrights_type','userid='.$Login_id);
                $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
            }
        }
        $trainer_id = $this->input->post('user_id', TRUE);
        //$workshop_type_id = $this->input->post('workshop_type_id', TRUE);
        $workshop_id = $this->input->post('workshop_id', TRUE);
        $workshop_session = $this->input->post('workshop_session', TRUE);
        $trainee_id = $this->input->post('trainee_id', TRUE);
        $trainee_region_id = $this->input->post('trainee_region_id', TRUE);
        $isWorkshopLive =$this->trainer_accuracy_model->isWorkshopLive($workshop_id,$workshop_session);
        $TraineeData = $this->trainer_accuracy_model->get_traineeAccuracy($RightsFlag,$trainee_id, $trainer_id, $workshop_id, $workshop_session,$isWorkshopLive,$trainee_region_id);
        $tariner_name='All';
            if($trainer_id !="0"){
                $Trainer_rowset = $this->common_model->get_value('company_users', "CONCAT(first_name,' ',last_name) as name ", 'userid=' . $trainer_id);
                $tariner_name = $Trainer_rowset->name;
            }
            $Workshop_rowset = $this->common_model->get_value('workshop', "workshop_name ", 'id=' . $workshop_id);
            $workshop_name = $Workshop_rowset->workshop_name;
            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()
                    ->setCellValue('A1', "Workshop Name :".$workshop_name)
                    ->setCellValue('A2', "Workshop Session :".$workshop_session)
                    ->setCellValue('A3', "Trainer :".$tariner_name)
                    ->setCellValue('A4', "TRAINEE ID")
                    ->setCellValue('B4', "TRAINEE NAME")
                    ->setCellValue('C4', "TRAINEE REGION")
                    ->setCellValue('D4', "TOTAL PLAYED")
                    ->setCellValue('E4', "CORRECT")
                    ->setCellValue('F4', "WRONG")
                    ->setCellValue('G4', "RESULT")
                    ->setCellValue('H4', "RANK")
                    ->setCellValue('I4', "STATUS");
            $styleArray = array(
                'font' => array(
//                'color' => array('rgb' => '990000'),
            ));
            $styleArray_header = array(
                'font' => array(
                    'color' => array('rgb' => '990000'),
                    'border' => 1
            ));
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);

            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('A4:I4')->applyFromArray($styleArray_header);
            
            $styleArray_body = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
        $i = 4;
        if (count((array)$TraineeData) > 0) {
            foreach ($TraineeData as $wksh) {
                $i++;
                $objPHPExcel->getActiveSheet()
                        ->setCellValue("A$i", $wksh->trainee_id)
                        ->setCellValue("B$i", $wksh->trainee_name)
                        ->setCellValue("C$i", $wksh->trainee_region)
                        ->setCellValue("D$i", $wksh->played_questions)
                        ->setCellValue("E$i",$wksh->correct)
                        ->setCellValue("F$i",($wksh->played_questions-$wksh->correct))
                        ->setCellValue("G$i", ($wksh->accuracy=="" ? "Not Played" : $wksh->accuracy."%"))
                        ->setCellValue("H$i", $wksh->rank)
                        ->setCellValue("I$i", ($wksh->played_questions >0 ? $wksh->status :'Not Attended'));
                $objPHPExcel->getActiveSheet()->getStyle("A$i:I$i")->applyFromArray($styleArray_body);
            }
        }
        //Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Trainer Accuracy Reports.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            // ob_end_clean();
            $objWriter->save('php://output');
    }
}
