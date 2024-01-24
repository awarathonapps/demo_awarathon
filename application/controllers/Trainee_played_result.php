<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Trainee_played_result extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('trainee_played_result');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('trainee_played_result_model');
        }

    public function index() {
        $data['module_id'] = '24.1';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        $RightsFlag = 1;
        $WRightsFlag = 1;
        if ($Company_id == "") {
            $data['CompanyData'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1', 'company_name');
        } else {
            $data['CompanyData'] = array();
            if (!$this->mw_session['superaccess']) {
                $trainer_id = $this->mw_session['user_id'];
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $trainer_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
            if ($RightsFlag) {
                $data['TrainerData'] = $this->common_model->get_selected_values('company_users', 'userid,CONCAT(first_name, " " ,last_name) as fullname', 'status="1" AND login_type="1" AND company_id="' . $Company_id . '"');
            } else {
                $data['TrainerData'] = $this->common_model->getUserRightsList($Company_id, $RightsFlag);
            }
            if ($WRightsFlag) {
                $data['WorkshopData'] = $this->common_model->get_selected_values('workshop', 'company_id,id,workshop_name', 'company_id=' . $Company_id);
                $data['WorkshopTypeData'] = $this->common_model->get_selected_values('workshoptype_mst', 'company_id,id,workshop_type', 'company_id=' . $Company_id);
                $data['RegionData'] = $this->common_model->get_selected_values('region', 'id,region_name', 'company_id=' . $Company_id);
                $data['TraineeData'] = $this->common_model->get_selected_values('device_users', 'user_id,concat(firstname," ",lastname," (",email," )") '
                        . 'as traineename', 'status=1  AND company_id=' . $Company_id, 'firstname');
            } else {
                $this->common_model->SyncWorkshopRights($trainer_id, 0);
                $data['RegionData'] = $this->common_model->getUserRegionList($Company_id);
                $data['WorkshopData'] = $this->common_model->getWkshopRightsList($Company_id);
                $data['WorkshopTypeData'] = $this->common_model->getWTypeRightsList($Company_id);
                $data['TraineeData'] = $this->common_model->getUserTraineeList($Company_id);
            }
            $data['DesignationData'] = $this->common_model->get_selected_values('designation_trainee', 'id,description', 'company_id=' . $Company_id);
        }
        $data['Company_id'] = $Company_id;
        $this->load->view('trainee_played_result/index', $data);
    }
    public function DatatableRefresh() {
        $dtSearchColumns = array('du.user_id', 'concat(du.firstname," ",du.lastname)', 'w.workshop_name','srg.description','wst.description','wt.workshop_type','rg.region_name','ar.workshop_session', 'qt.description', 'cu.last_name', 'cu.first_name','dt.description');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $RightsFlag = 1;
        $WRightsFlag = 1;
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
        } else {
            $cmp_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Login_id = $this->mw_session['user_id'];
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        if ($cmp_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.company_id  = " . $cmp_id;
            } else {
                $dtWhere .= " WHERE ar.company_id  = " . $cmp_id;
            }
        } else {
            $output = array(
                "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => array()
            );
            echo json_encode($output);
            exit;
        }
        $wrkshoptype_id = ($this->input->get('workshoptype_id') ? $this->input->get('workshoptype_id') : '0');
        if ($wrkshoptype_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND w.workshop_type  = " . $wrkshoptype_id;
            } else {
                $dtWhere .= " WHERE w.workshop_type = " . $wrkshoptype_id;
            }
        }
        $wrkshop_id = ($this->input->get('workshop_id') ? $this->input->get('workshop_id') : '');
        if ($wrkshop_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.workshop_id  = " . $wrkshop_id;
            } else {
                $dtWhere .= " WHERE ar.workshop_id = " . $wrkshop_id;
            }
        }
        $session_id = ($this->input->get('sessions') ? $this->input->get('sessions') : '');
        if ($session_id != "") {
            if ($session_id == 0) {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND ar.workshop_session  = 'PRE' ";
                } else {
                    $dtWhere .= " WHERE ar.workshop_session = 'PRE' ";
                }
            } else {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND ar.workshop_session  = 'POST' ";
                } else {
                    $dtWhere .= " WHERE ar.workshop_session = 'POST' ";
                }
            }
        }
        $topic_id = ($this->input->get('topic_id') ? $this->input->get('topic_id') : '');
        if ($topic_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.topic_id  = " . $topic_id;
            } else {
                $dtWhere .= " WHERE ar.topic_id = " . $topic_id;
            }
        }
        $subtopic_id = ($this->input->get('subtopic_id') ? $this->input->get('subtopic_id') : '');
        if ($subtopic_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.subtopic_id  = " . $subtopic_id;
            } else {
                $dtWhere .= " WHERE ar.subtopic_id = " . $subtopic_id;
            }
        }

        $user_id = ($this->input->get('user_id') ? $this->input->get('user_id') : '');
        if ($user_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.user_id  = " . $user_id;
            } else {
                $dtWhere .= " WHERE ar.user_id = " . $user_id;
            }
        }
        $trainer_id = ($this->input->get('trainer_id') ? $this->input->get('trainer_id') : '0');
        if ($trainer_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.trainer_id  = " . $trainer_id;
            } else {
                $dtWhere .= " WHERE ar.trainer_id = " . $trainer_id;
            }
        }

        $result_search = ($this->input->get('result_search') ? $this->input->get('result_search') : '');
        if ($result_search != "") {
            if ($result_search == 1) {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND ar.is_correct  = 1";
                } else {
                    $dtWhere .= " WHERE ar.is_correct = 1";
                }
            }
            if ($result_search == 2) {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND ar.is_wrong  = 1";
                } else {
                    $dtWhere .= " WHERE ar.is_wrong = 1";
                }
            }
            if ($result_search == 3) {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND ar.is_timeout  = 1";
                } else {
                    $dtWhere .= " WHERE ar.is_timeout = 1";
                }
            }
        }
        $rgion_id = ($this->input->get('region_id') ? $this->input->get('region_id') : '0');
        if ($rgion_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND w.region  = " . $rgion_id;
            } else {
                $dtWhere .= " WHERE w.region = " . $rgion_id;
            }
        }
        $workshopsubtype_id = ($this->input->get('workshopsubtype_id') ? $this->input->get('workshopsubtype_id') : '');
        if ($workshopsubtype_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND w.workshopsubtype_id  = " . $workshopsubtype_id;
            } else {
                $dtWhere .= " WHERE w.workshopsubtype_id = " . $workshopsubtype_id;
            }
        }
        $subregion_id = ($this->input->get('subregion_id') ? $this->input->get('subregion_id') : '');
        if ($subregion_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND w.workshopsubregion_id  = " . $subregion_id;
            } else {
                $dtWhere .= " WHERE w.workshopsubregion_id = " . $subregion_id;
            }
        }
        $trgion_id= ($this->input->get('tregion_id') ? $this->input->get('tregion_id') :'0');
        if($trgion_id !="0")
        {
        if($dtWhere<>''){
                $dtWhere .= " AND du.region_id  = ".$trgion_id; 
           }else{
               $dtWhere .= " WHERE du.region_id = ".$trgion_id; 
           } 
        }
        $designation_id= ($this->input->get('designation_id') ? $this->input->get('designation_id') :'0');
        //echo($designation_id);
        if($designation_id !="0")
        {
        if($dtWhere<>''){
                $dtWhere .= " AND du.designation_id  = ".$designation_id; 
           }else{
                $dtWhere .= " WHERE du.designation_id = ".$designation_id; 
           } 
        }
        $this->session->set_userdata(array('exportWhere' => $dtWhere, 'RightsFlag' => $RightsFlag, 'WRightsFlag' => $WRightsFlag, 'Company_id' => $cmp_id));

        $DTRenderArray = $this->trainee_played_result_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag);


        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('user_id', 'traineename','designation', 'workshop_name','workshop_type','workshop_subtype','region_name','sub_region','workshop_session', 'questionset', 'trainername', 'tregion_name', 'topicname', 'subtopicname', 'question_title', 'correct_answer', 'user_answer', 'start_dttm', 'end_dttm', 'seconds', 'timer', 'question_result');
        $site_url = base_url();
        $acces_management = $this->acces_management;

        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function exportReport() {//In use for Export
        $Company_name = "";
        $Company_id = $this->session->userdata('Company_id');
        if ($Company_id != "") {
            if ($this->mw_session['company_id'] == "") {
                $Rowset = $this->common_model->get_value('company', 'company_name', 'id=' . $Company_id);
                $Company_name = 'Company Name : ' . $Rowset->company_name;
            }
            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();

            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->setCellValue('A1', "$Company_name")
                    ->setCellValue('A3', "Trainee ID")
                    ->setCellValue('B3', "Trainee Name")
                    ->setCellValue('C3', "Designation")
                    ->setCellValue('D3', "Workshop Name")
                    ->setCellValue('E3', "Workshop Type")
                    ->setCellValue('F3', "Workshop Sub-Type")
                    ->setCellValue('G3', "Workshop Sub-Region")
                    ->setCellValue('H3', "Workshop Region")
                    ->setCellValue('I3', "Session")
                    ->setCellValue('J3', "Question Set")
                    ->setCellValue('K3', "Trainer Name")
                    ->setCellValue('L3', "Trainee Region")
                    ->setCellValue('M3', "TOPIC NAME")
                    ->setCellValue('N3', "SUB TOPIC NAME")
                    ->setCellValue('O3', "QUESTION ID")
                    ->setCellValue('P3', "QUESTION TITLE")
                    ->setCellValue('Q3', "CORRECT ANSWER")
                    ->setCellValue('R3', "USER ANSWERER")
                    ->setCellValue('S3', "START DATE / TIME	")
                    ->setCellValue('T3', "END DATE / TIME")
                    ->setCellValue('U3', "SECONDS")
                    ->setCellValue('V3', "CORRECT/WRONG/TIME OUT");
            $styleArray = array(
                'font' => array(
//                'color' => array('rgb' => '990000'),
            ));

            $styleArray_header = array(
                'font' => array(
                    'color' => array('rgb' => '990000'),
                    'border' => 1
            ));
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(14);

            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('A3:V3')->applyFromArray($styleArray_header);


            $styleArray_body = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            $exportWhere = $this->session->userdata('exportWhere');
            $RightsFlag = $this->session->userdata('RightsFlag');
            $WRightsFlag = $this->session->userdata('WRightsFlag');
            $i = 3;
            $j = 0;
            $Data_list = $this->trainee_played_result_model->exportToExcel($exportWhere, $RightsFlag, $WRightsFlag);

            foreach ($Data_list as $value) {
                $i++;
                $j++;
                $objPHPExcel->getActiveSheet()
                        ->setCellValue("A$i", $value->user_id)
                        ->setCellValue("B$i", $value->traineename)
                        ->setCellValue("C$i", $value->designation)
                        ->setCellValue("D$i", $value->workshop_name)
                        ->setCellValue("E$i", $value->workshop_type)
                        ->setCellValue("F$i", $value->workshop_subtype)
                        ->setCellValue("G$i", $value->sub_region)
                        ->setCellValue("H$i", $value->region_name)
                        ->setCellValue("I$i", $value->workshop_session)
                        ->setCellValue("J$i", $value->questionset)
                        ->setCellValue("K$i", $value->trainername)
                        ->setCellValue("L$i", $value->tregion_name)
                        ->setCellValue("M$i", $value->topicname)
                        ->setCellValue("N$i", $value->subtopicname)
                        ->setCellValue("O$i", $value->question_id)
                        ->setCellValue("P$i", $value->question_title)
                        ->setCellValue("Q$i", $value->correct_answer)
                        ->setCellValue("R$i", $value->user_answer)
                        ->setCellValue("S$i", $value->start_dttm)
                        ->setCellValue("T$i", $value->end_dttm)
                        ->setCellValue("U$i", $value->seconds)
                        ->setCellValue("V$i", $value->question_result);
                $objPHPExcel->getActiveSheet()->getStyle("A$i:V$i")->applyFromArray($styleArray_body);
            }
            //Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Trainee Played Results Report.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            // ob_end_clean();
            $objWriter->save('php://output');
            // Sending headers to force the user to download the file
        }else{
            redirect('trainee_played_result');
        }
    }

}
