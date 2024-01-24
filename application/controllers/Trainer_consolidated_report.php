<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Trainer_consolidated_report extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('trainer_consolidated_report');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('workshop_report_model');
        }

    public function index() {
        $data['module_id'] = '24.8';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        $RightsFlag = 1;
        $WRightsFlag = 1;
        if ($Company_id == "") {
            $data['CompanyData'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1', 'company_name');
        } else {
            $data['CompanyData'] = array();
            $trainer_id = $this->mw_session['user_id'];
            if (!$this->mw_session['superaccess']) {
            $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $trainer_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }

            if ($RightsFlag) {
                $data['TrainerData'] = $this->common_model->get_selected_values('company_users', 'userid,CONCAT(first_name, " " ,last_name) as fullname', 'status="1" AND login_type="1" AND company_id="' . $Company_id . '"', 'first_name');
            } else {
                $this->common_model->SyncTrainerRights($trainer_id);
                $data['TrainerData'] = $this->common_model->getUserRightsList($Company_id, $trainer_id);
            }
            if ($WRightsFlag) {
                $data['WTypeData'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status="1" AND company_id="' . $Company_id . '"', 'workshop_type');
                $data['WorkshopData'] = $this->common_model->get_selected_values('workshop', 'company_id,id,workshop_name', 'company_id=' . $Company_id, "id desc");
                $data['RegionData'] = $this->common_model->get_selected_values('region', 'id,region_name', 'company_id=' . $Company_id);
            } else {
                $this->common_model->SyncWorkshopRights($trainer_id, 0);
                $data['RegionData'] = $this->common_model->getUserRegionList($Company_id);
                $data['WTypeData'] = $this->common_model->getWTypeRightsList($Company_id);
                $data['WorkshopData'] = $this->common_model->getWkshopRightsList($Company_id);
            }
//                $data['TopicData']    = $this->common_model->get_selected_values('question_topic','id,description','company_id='.$Company_id);
        }
        $data['Company_id'] = $Company_id;
        $this->load->view('trainer_consolidated_report/index', $data);
    }

   public function ajax_topicwise_data(){
        $topic_id = $this->input->post('topic_id', TRUE);
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $lchtml='<option value="">Please Select</option>';
        if($topic_id !=''){
            $SubTopicData = $this->common_model->get_selected_values('question_subtopic', 'id,description', 'company_id=' . $company_id . ' and topic_id=' . $topic_id, 'description');
            if(count((array)$SubTopicData)>0){
                foreach ($SubTopicData as $value) {
                    $lchtml .='<option value="'.$value->id.'">'.$value->description.'</option>';
                }
            }
        }
        echo $lchtml;
    }
    public function DatatableRefresh() {
        $dtSearchColumns = array('r.region_name','wsr.description','wt.workshop_type','wst.description','w.workshop_name','CONCAT(cu.first_name," ",cu.last_name)', 'qt.description', 'qst.description');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $RightsFlag = 1;
        $WRightsFlag = 1;
        $dtHaving = '';
        $Login_id = $this->mw_session['user_id'];
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
        } else {
            $cmp_id =$this->mw_session['company_id'];
            if(!$this->mw_session['superaccess']){
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
        }else{
            $output = array(
                "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => array()
            );
            echo json_encode($output);
            exit;
        }
        $wrkshop_id = ($this->input->get('workshop_id') ? $this->input->get('workshop_id') : '');
        if ($wrkshop_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.workshop_id  = " . $wrkshop_id;
            } else {
                $dtWhere .= " WHERE ar.workshop_id = " . $wrkshop_id;
            }
        }
        $region_id = ($this->input->get('region_id') ? $this->input->get('region_id') : '0');
        if ($region_id != "0") {
            if ($region_id == 0) {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND w.region  = " . $region_id;
                } else {
                    $dtWhere .= " WHERE w.region  = " . $region_id;
                }
            } else {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND w.region  = " . $region_id;
                    ;
                } else {
                    $dtWhere .= " WHERE w.region  = " . $region_id;
                    ;
                }
            }
        }
        $wsubrgion_id = ($this->input->get('wsubregion_id') ? $this->input->get('wsubregion_id') : '');
        if ($wsubrgion_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshopsubregion_id  = " . $wsubrgion_id;
            } else {
                $dtWhere .= " WHERE w.workshopsubregion_id = " . $wsubrgion_id;
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

        $wtype_id = ($this->input->get('wtype_id') ? $this->input->get('wtype_id') : '0');
        if ($wtype_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND w.workshop_type  = " . $wtype_id;
            } else {
                $dtWhere .= " WHERE w.workshop_type = " . $wtype_id;
            }
        }
        $workshop_subtype = ($this->input->get('workshop_subtype') ? $this->input->get('workshop_subtype') : '');
        if ($workshop_subtype != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshopsubtype_id  = " . $workshop_subtype;
            } else {
                $dtWhere .= " WHERE w.workshopsubtype_id = " . $workshop_subtype;
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
		$session_id = ($this->input->get('session_id') ? $this->input->get('session_id') : '');
        if ($session_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.workshop_session  = '" . $session_id."'";
            } else {
                $dtWhere .= " WHERE ar.workshop_session = '" . $session_id."'";
            }
        }
//      $from_range= ($this->input->get('from_range') ? $this->input->get('from_range') :'');
//      $to_range= ($this->input->get('to_range') ? $this->input->get('to_range') :'');
        $range = ($this->input->get('result_range') ? $this->input->get('result_range') : '');
        if ($range != '') {
            $result_range = explode("-", $range);
            if (count((array)$result_range) > 0) {
                $from_range = $result_range[0];
                $to_range = $result_range[1];
                if ($from_range != "" && $to_range != '') {
                    $dtHaving .= " having result between " . $from_range . " AND " . $to_range;
                }
            }
        }
        $this->session->set_userdata(array('exportWhere' => $dtWhere, 'exportHaving' => $dtHaving, 'RightsFlag' => $RightsFlag, 'WRightsFlag' => $WRightsFlag, 'Company_id' => $cmp_id));

        $DTRenderArray = $this->workshop_report_model->TrainerConsolidatedLoadDataTable($dtWhere, $dtOrder, $dtLimit, $dtHaving, $RightsFlag, $WRightsFlag);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('region_name', 'workshop_subregion', 'workshop_type', 'workshop_subtype', 'workshop_name', 'trainername', 'topicname', 'subtopicname', 'total_question', 'total_trainee_played', 'total_question_played', 'total_correct_ans', 'result');
        $site_url = base_url();
        $acces_management = $this->acces_management;

        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == 'result') {
                    $row[] =$dtRow['result'].'%';
                }
                else if ($dtDisplayColumns[$i] != ' ') {
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
                ->setCellValue('A3', "Workshop Region")
                ->setCellValue('B3', "Workshop Sub-region")
                ->setCellValue('C3', "Workshop Type")
                ->setCellValue('D3', "Workshop Sub-type")
                ->setCellValue('E3', "Workshop Name")
                ->setCellValue('F3', "Trainer Name")
                ->setCellValue('G3', "TOPIC")
                ->setCellValue('H3', "SUB-TOPIC")
                ->setCellValue('I3', "NO. OF UNIQUE QUESTIONS")
                ->setCellValue('J3', "NO OF TRAINEE PLAYED")
                ->setCellValue('K3', "TOTAL QUESTIONS PLAYED")
                ->setCellValue('L3', "TOTAL CORRECT ANSWERS")
                ->setCellValue('M3', "RESULT");
        $styleArray = array(
            'font' => array(
//                'bold' => true
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

        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A3:M3')->applyFromArray($styleArray_header);


        $styleArray_body = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $exportWhere = $this->session->userdata('exportWhere');
        $exportHaving = $this->session->userdata('exportHaving');
        $RightsFlag = $this->session->userdata('RightsFlag');
        $WRightsFlag = $this->session->userdata('WRightsFlag');
        $i = 3;
        $j = 0;
        $Data_list = $this->workshop_report_model->TrainerConsolidatedExportToExcel($exportWhere, $exportHaving, $RightsFlag, $WRightsFlag);


        foreach ($Data_list as $value) {
            $i++;
            $j++;
            $objPHPExcel->getActiveSheet()
                    ->setCellValue("A$i", $value->region_name)
                    ->setCellValue("B$i", $value->workshop_subregion)
                    ->setCellValue("C$i", $value->workshop_type)
                    ->setCellValue("D$i", $value->workshop_subtype)
                    ->setCellValue("E$i", $value->workshop_name)
                    ->setCellValue("F$i", $value->trainername)
                    ->setCellValue("G$i", $value->topicname)
                    ->setCellValue("H$i", $value->subtopicname)
                    ->setCellValue("I$i", $value->total_question)
                    ->setCellValue("J$i", $value->total_trainee_played)
                    ->setCellValue("K$i", $value->total_question_played)
                    ->setCellValue("L$i", $value->total_correct_ans)
                    ->setCellValue("M$i", $value->result.'%');
            $objPHPExcel->getActiveSheet()->getStyle("A$i:M$i")->applyFromArray($styleArray_body);
        }


        //Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="TrainerConsolidated_Report.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        // ob_end_clean();
        $objWriter->save('php://output');
        // Sending headers to force the user to download the file
        }else{
            redirect('trainer_consolidated_report');
        }
    }

}
