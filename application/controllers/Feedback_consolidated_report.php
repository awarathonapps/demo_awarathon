<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Feedback_consolidated_report extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('feedback_consolidated');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('workshop_report_model');
        }

    public function index() {
        $data['module_id'] = '24.9';
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
            if ($WRightsFlag) {
                $data['RegionData'] = $this->common_model->get_selected_values('region', 'id,region_name', 'company_id=' . $Company_id, 'region_name');
                $data['WorkshopData'] = $this->common_model->get_selected_values('workshop', 'id as workshop_id,workshop_name', 'status=1 and company_id=' . $Company_id, 'workshop_name');
                $data['TraineeData'] = $this->common_model->get_selected_values('device_users', 'user_id,concat(firstname," ",lastname," (",email,")") as traineename', 'company_id=' . $Company_id);
                $data['WTypeData'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status=1 and company_id=' . $Company_id);
            } else {
                $this->common_model->SyncWorkshopRights($trainer_id, 0);
                $data['RegionData'] = $this->common_model->getUserRegionList($Company_id);
                $data['WorkshopData'] = $this->common_model->getWkshopFeedRightsList($Company_id);
                $data['TraineeData'] = $this->common_model->getUserTraineeList($Company_id);
                $data['WTypeData'] = $this->common_model->getWTypeFeedRightsList($Company_id);
            }
        }
        $data['Company_id'] = $Company_id;
        $this->load->view('feedback_consolidated_report/index', $data);
    }
    public function ajax_tregionwise_data(){        
        $tregion_id = $this->input->post('tregion_id', TRUE);
        $RightFlag = 1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $login_id = $this->mw_session['user_id'];
                $Rowset = $this->common_model->get_value('company_users', 'company_id,workshoprights_type', 'userid=' . $login_id);
                $RightFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        if ($tregion_id != "") {
            $data['TraineeData'] = $this->workshop_report_model->get_traineeData($company_id,$RightFlag='',$tregion_id='');
        } else {
            $data['TraineeData'] = array();
        }        
        echo json_encode($data);
    }
    public function DatatableRefresh() {
        $dtSearchColumns = array('af.user_id', 'concat(du.firstname," ",du.lastname)', 'du.email', 'du.mobile', 'w.workshop_name', 'ft.description', 'fst.description','','','','f.title','','','af.question_type','fq.question_title');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];

        $WRightsFlag = 1;
        $Login_id = $this->mw_session['user_id'];
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
        } else {
            $cmp_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        if ($cmp_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND af.company_id  = " . $cmp_id;
            } else {
                $dtWhere .= " WHERE af.company_id  = " . $cmp_id;
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
        $wrkshop_id = ($this->input->get('workshop_id') ? $this->input->get('workshop_id') : '');
        if ($wrkshop_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND af.workshop_id  = " . $wrkshop_id;
            } else {
                $dtWhere .= " WHERE af.workshop_id = " . $wrkshop_id;
            }
        }
        $user_id = ($this->input->get('user_id') ? $this->input->get('user_id') : '');
        if ($user_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND af.user_id  = " . $user_id;
            } else {
                $dtWhere .= " WHERE af.user_id = " . $user_id;
            }
        }
        $tregion_id= ($this->input->get('tregion_id') ? $this->input->get('tregion_id') :'0');
        if($tregion_id !="0")
            {
            if($dtWhere<>''){
                    $dtWhere .= " AND du.region_id  = ".$tregion_id; 
               }else{
                   $dtWhere .= " WHERE du.region_id = ".$tregion_id; 
               } 
            } 
        $wrgion_id = ($this->input->get('wregion_id') ? $this->input->get('wregion_id') : '0');
        if ($wrgion_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.region  = " . $wrgion_id;
            } else {
                $dtWhere .= " WHERE w.region = " . $wrgion_id;
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
        $workshop_type = ($this->input->get('workshop_type') ? $this->input->get('workshop_type') : '0');
        if ($workshop_type != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshop_type  = " . $workshop_type;
            } else {
                $dtWhere .= " WHERE w.workshop_type = " . $workshop_type;
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
        $result_search = ($this->input->get('result_search') ? $this->input->get('result_search') : '');
        if ($result_search != "") {
            if ($result_search == 1) {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND af.is_timeout  != 1";
                } else {
                    $dtWhere .= " WHERE ar.is_timeout != 1";
                }
            }
            if ($result_search == 2) {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND af.is_timeout  = 1";
                } else {
                    $dtWhere .= " WHERE af.is_timeout = 1";
                }
            }
        }

        $this->session->set_userdata(array('exportWhere' => $dtWhere, 'WRightsFlag' => $WRightsFlag, 'Company_id' => $cmp_id));

        $DTRenderArray = $this->workshop_report_model->FeedbackConsolidatedLoadDataTable($dtWhere, $dtOrder, $dtLimit, $WRightsFlag);


        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('user_id', 'trainee_name', 'email', 'mobile', 'workshop_name','workshop_type','workshop_subtype','workshop_region','workshop_subregion', 'tregion_name', 'feedback_set', 'feedbacktype', 'feedbacksubtype','question_type', 'question_title', 'feedback_option', 'feedback_weight', 'max_weightage', 'start_dttm', 'end_dttm', 'seconds', 'feedback_status');
        $site_url = base_url();
        $acces_management = $this->acces_management;

        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                $ans = '';
                if ($dtDisplayColumns[$i] == 'feedback_option') {
					if($dtRow['question_type']==0){
						for ($j = 'a'; $j <= 'f'; $j++) {
							if ($dtRow['option_' . $j] == 1) {
								$ans .= "Option " . strtoupper($j) . " - " . $dtRow['doption_' . $j] . ", ";
							}
						}
						$ans = rtrim($ans, ", ");
					}else{
						$ans=$dtRow['feedback_answer'];
					}
                    $row[] = $ans;
                }else if ($dtDisplayColumns[$i] == 'question_type') {
					$row[] = ($dtRow['question_type']==1 ? 'Text' :'Optional');
				}
				else if ($dtDisplayColumns[$i] == 'feedback_weight') {
                    $sum = 0;
					if($dtRow['question_type']==0){
						for ($j = 'a'; $j <= 'f'; $j++) {
							if ($dtRow['weight_' . $j] != 0) {
								$sum = $sum + $dtRow['weight_' . $j];
							}
						}
					}else{
						$sum=$dtRow['text_weightage'];
					}
                    $row[] = $sum;
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function exportReport() {
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
                    ->setCellValue('A3', "Trainee Id")
                    ->setCellValue('B3', "Trainee Name")
                    ->setCellValue('C3', "Email")
                    ->setCellValue('D3', "Mobile")
                    ->setCellValue('E3', "Workshop Name")
                    ->setCellValue('F3', "Workshop Type")
                    ->setCellValue('G3', "Workshop Sub-type")
                    ->setCellValue('H3', "Workshop Region")
                    ->setCellValue('I3', "Workshop Sub-region")
                    ->setCellValue('J3', "Trainee Region")
                    ->setCellValue('K3', "Feedback Set")
                    ->setCellValue('L3', "Type")
                    ->setCellValue('M3', "SubType")
					->setCellValue('N3', "Question Type")
                    ->setCellValue('O3', "Feedback Question")
                    ->setCellValue('P3', "Feedback Option")
                    ->setCellValue('Q3', "Feedback Weight")
                    ->setCellValue('R3', "Max Weightage")
                    ->setCellValue('S3', "Play Start")
                    ->setCellValue('T3', "Play End")
                    ->setCellValue('U3', "Play Time")
                    ->setCellValue('V3', "Status");
            $styleArray = array(
                'font' => array(
//                    'bold' => true
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
            $WRightsFlag = $this->session->userdata('WRightsFlag');
            $i = 3;
            $j = 0;
            $Data_list = $this->workshop_report_model->FeedbackConsolidatedExportToExcel($exportWhere, $WRightsFlag);


            foreach ($Data_list as $value) {
                $ans = '';
                $str = '';
                $sum = 0;
				if($value->question_type==0){
					for ($j = 'a'; $j <= 'f'; $j++) {
						$str = 'option_' . $j;
						if ($value->$str == 1) {
							$Option_str = 'doption_' . $j;
							$ans .= "Option " . strtoupper($j) . " - " . $value->$Option_str . " / ";
						}
						$str1 = 'weight_' . $j;
						if ($value->$str1 != 0) {
							$sum = $sum + $value->$str1;
						}
					}
					$ans = rtrim($ans, "/ ");
					$total_weight = $sum;
					$j++;
				}else{
					$ans = $value->feedback_answer;
					$total_weight = $value->text_weightage;
				}
				$i++;
                $objPHPExcel->getActiveSheet()
                        ->setCellValue("A$i", $value->user_id)
                        ->setCellValue("B$i", $value->trainee_name)
                        ->setCellValue("C$i", $value->email)
                        ->setCellValue("D$i", $value->mobile)
                        ->setCellValue("E$i", $value->workshop_name)
                        ->setCellValue("F$i", $value->workshop_type)
                        ->setCellValue("G$i", $value->workshop_subtype)
                        ->setCellValue("H$i", $value->workshop_region)
                        ->setCellValue("I$i", $value->workshop_subregion)
                        ->setCellValue("J$i", $value->tregion_name)
                        ->setCellValue("K$i", $value->feedback_set)
                        ->setCellValue("L$i", $value->feedbacktype)
                        ->setCellValue("M$i", $value->feedbacksubtype)
						->setCellValue("N$i", ($value->question_type==1 ? 'Text' :'Optional'))
                        ->setCellValue("O$i", $value->question_title)
                        ->setCellValue("P$i", $ans)
                        ->setCellValue("Q$i", $total_weight)
                        ->setCellValue("R$i", $value->max_weightage)
                        ->setCellValue("S$i", $value->start_dttm)
                        ->setCellValue("T$i", $value->end_dttm)
                        ->setCellValue("U$i", $value->seconds)
                        ->setCellValue("V$i", $value->feedback_status);

                $objPHPExcel->getActiveSheet()->getStyle("A$i:V$i")->applyFromArray($styleArray_body);
            }
            //Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Feedback Consolidated Report.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            // ob_end_clean();
            $objWriter->save('php://output');
            // Sending headers to force the user to download the file
        } else {
            redirect('feedback_consolidated_report');
        }
    }

}
