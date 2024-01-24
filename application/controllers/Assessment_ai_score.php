<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Assessment_ai_score extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('assessment_ai_score');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->common_db = $this->common_model->connect_db2();
        $this->acces_management = $acces_management;
        $this->load->model('assessment_model');
    }
    public function index() {
        $data['module_id'] = '13.05';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_edit) {
            redirect('assessment');
            return;
        }
        $data['Assessment_list'] = $this->common_model->get_selected_values('assessment_mst', 'id,assessment', 'status=1','assessment');

        $this->load->view('assessment/import_ai_score', $data);
    }

    public function get_assessor_list() {
        $assessment_id = $this->input->post('assessment_id', true);
        $lcoption = '<option value="">Please Select</option>';
        if ($assessment_id != '') {
            $trainer_list = $this->assessment_model->get_trainerdata($assessment_id);
            if (count((array)$trainer_list) > 0) {
                foreach ($trainer_list as $value) {
                    $lcoption .='<option value="' . $value->trainer_id . '">' . $value->name . '</option>';
                }
            }
        }
        echo $lcoption;
    }

    public function samplexls_ai_score() {
        $this->load->library('PHPExcel_CI');
        $Excel = new PHPExcel_CI;
        $Excel->setActiveSheetIndex(0);
        $Excel->getActiveSheet()->setTitle('User_List');
        $Excel->createSheet();
        $Excel->getActiveSheet()
                ->setCellValue('A1', 'Do not modify or delete the Columns.');
        $Excel->getActiveSheet()->getStyle('A1:D1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'FF0000'
            )
        ));
        //merge cell A1 until D1
        $Excel->getActiveSheet()->mergeCells('A1:D1');
        $styleArray = array(
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => 'ffffff'),
                'size' => 11,
                'name' => 'Calibri'
        ));
        $Excel->getActiveSheet()
                ->setCellValue('A2', 'Name*')
                ->setCellValue('B2', 'Question no*')
                ->setCellValue('C2', 'Parameters*')
                ->setCellValue('D2', 'Score in %*');

        $Excel->getActiveSheet()->getStyle('A1:C1')->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getStyle("A2:D2")->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getColumnDimension('A')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('B')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('C')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('D')->setWidth("30");

        $Excel->getActiveSheet()->getStyle('A2:D2')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'eb3a12'
            )
        ));
        //set aligment to center for that merged cell (A1 to D1)
        $filename = "AI_Score_Import.xls";
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $objWriter = PHPExcel_IOFactory::createWriter($Excel, 'Excel5');
        //force user to download the Excel file without writing it to server's HD
        ob_end_clean();
        $objWriter->save('php://output');
    }

    public function uploadXls_ai_score() {
        $Message = '';
        $SuccessFlag = 1;
        $acces_management = $this->acces_management;
        $user_list = array();
        if (!$acces_management->allow_edit) {
            $Message = "You have no rights to Edit,Contact Administrator for rights.";
        } else {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('assessment_id', 'Assessment name', 'required');
            $this->form_validation->set_rules('assessor_id', 'Assessor name', 'required');
            $assessment_id = $this->input->post('assessment_id');
            $assessor_id = $this->input->post('assessor_id');
            $this->form_validation->set_rules('filename', '', 'callback_file_check');
        }
        if ($this->form_validation->run() == FALSE) {
            $Message = validation_errors();
            $SuccessFlag = 0;
        } else {
            $FileData = $_FILES['filename'];
            $this->load->library('PHPExcel_CI');
            $objPHPExcel = PHPExcel_IOFactory::load($FileData['tmp_name']);
            $objPHPExcel->setActiveSheetIndex(0);
            $worksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $highestColumm = $worksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumm);
            if ($highestRow <= 2) {
                $Message .= "Excel row/column mismatch,Please download sample file.";
                $SuccessFlag = 0;
            }
            if ($highestColumnIndex < 4) {
                $Message .= "Excel column mismatch,Please download sample file.";
                $SuccessFlag = 0;
            }
            if ($SuccessFlag) {
                $assessment_set = $this->assessment_model->get_assessment_parameter($assessment_id);
                //echo "<pre>";
                //print_r($assessment_set);
                //exit;
                for ($row = 3; $row <= $highestRow; $row++) {
                    $name = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                    if ($name == '') {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row,Name is Empty. </br> ";
                        continue;
                    } else {
                        $Name_array = explode("-", $name);
                        if (count((array)$Name_array) > 1) {
                            $device_set = $this->common_model->get_value('device_users', 'user_id', 'user_id=' . $Name_array[1]);
                            if (count((array)$device_set) == 0) {
                                $SuccessFlag = 0;
                                $Message .= "Row No. $row,Invalid User Details. </br> ";
                                continue;
                            }
                        } else {
                            $SuccessFlag = 0;
                            $Message .= "Row No. $row,Invalid User Details. </br> ";
                            continue;
                        }
                    }
                    $Question_no = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    if ($Question_no == '') {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, Question no is Empty. </br> ";
                        continue;
                    } elseif (!isset($assessment_set[$Question_no])) {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, Invalid Question no. </br> ";
                        continue;
                    }
                    $Parameters = strtolower($worksheet->getCellByColumnAndRow(2, $row)->getValue());
                    if ($Parameters == '') {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, Parameters is Empty. </br> ";
                        continue;
                    } elseif (!isset($assessment_set[$Question_no]['parameterset'][$Parameters])) {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, Invalid Question Parameters. </br> ";
                        continue;
                    }
                    $Score = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                    if ($Score == '') {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, Score is Empty. </br> ";
                        continue;
                    }
                    /*$Rating = number_format($worksheet->getCellByColumnAndRow(4, $row)->getCalculatedValue(), 0);
                    if ($Rating == '') {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, Rating is Empty. </br> ";
                        continue;
                    }*/
                }
            }
        }
        if ($SuccessFlag) {
            $now = date('Y-m-d H:i:s');
            $Counter = 0;
            for ($row = 3; $row <= $highestRow; $row++) {
                $Counter++;
                $name = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                $Name_array = explode("-", $name);
                $user_id = $Name_array[1];
                $Question_no = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                $question_id = $assessment_set[$Question_no]['question_id'];
                $Parameters = strtolower($worksheet->getCellByColumnAndRow(2, $row)->getValue());
                $Score = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
				$Rating = number_format(($Score*5)/100,2);
                //$Rating = number_format($worksheet->getCellByColumnAndRow(4, $row)->getCalculatedValue(), 0);
                $lchwhere = "assessment_id=" . $assessment_id . " AND user_id=" . $user_id;
                $lchwhere .= " AND question_id=" . $question_id;
                $resultset = $this->common_model->get_value('assessment_results', 'id', $lchwhere);

                if (count((array)$resultset) > 0) {
                    $parameter_id = $assessment_set[$Question_no]['parameterset'][$Parameters]->id;
                    $parameter_label_id = $assessment_set[$Question_no]['parameterset'][$Parameters]->parameter_label_id;
                    $lchwhere .= " AND parameter_id=" . $parameter_id;
                    $lchwhere .= " AND parameter_label_id=" . $parameter_label_id;
                    $lchwhere .= " AND trainer_id=" . $assessor_id;
                    $resultset2 = $this->common_model->get_value('assessment_results_trans', 'id', $lchwhere);
                    $user_list[$user_id][] = array('question_id' => $question_id, 'result_id' => $resultset->id);
                    if (count((array)$resultset2) == 0) {
                        $data = array(
                            'assessment_id' => $assessment_id,
                            'user_id' => $user_id,
                            'trainer_id' => $assessor_id,
                            'result_id' => $resultset->id,
                            'question_no' => $Question_no,
                            'question_id' => $question_id,
                            'parameter_id' => $parameter_id,
                            'parameter_label_id' => $parameter_label_id,
                            'score' => $Rating,
                            'is_aiscore' => 1,
                            'percentage' => $Score,
                            'addeddate' => $now,
                            'addedby' => $this->mw_session['user_id'],
                        );
                        $this->common_model->insert('assessment_results_trans', $data);
                    } else {
                        $data = array(
                            'assessment_id' => $assessment_id,
                            'user_id' => $user_id,
                            'result_id' => $resultset->id,
                            'question_no' => $Question_no,
                            'trainer_id' => $assessor_id,
                            'question_id' => $question_id,
                            'parameter_id' => $parameter_id,
                            'parameter_label_id' => $parameter_label_id,
                            'score' => $Rating,
                            'percentage' => $Score,
                            'modifieddate' => $now,
                            'modifiedby' => $this->mw_session['user_id'],
                        );
                        $this->common_model->update('assessment_results_trans', 'id', $resultset2->id, $data);
                    }
                    //echo $this->db->last_query();
                    //exit;
                    $Message = $Counter . " AI Score Imported successfully.";
                } else {
                    $Message = " Invalid Users assessment data.";
                }
            }

            if (count((array)$user_list) > 0) {
				$AssessmentData = $this->common_model->get_value('assessment_mst', 'assessment_type,ratingstyle', 'id=' . $assessment_id);
                foreach ($user_list as $user_id => $tdata) {
                    $UserScoreData = $this->assessment_model->get_your_rating($assessment_id, $user_id, $assessor_id,0);
                    if (count((array)$UserScoreData) > 0 && $UserScoreData->total_rating != 0) {
						/*if($AssessmentData->ratingstyle==2){
							$user_rating = round($UserScoreData->avg_percentage / ($UserScoreData->total_param), 2);
						}else{
							$user_rating = round($UserScoreData->total_score / ($UserScoreData->total_rating) * 100, 2);
						}*/
						$user_rating =$UserScoreData->total_rating;
                        $trainer_data = array('assessment_id' => $assessment_id,
                            'user_id'     => $user_id,
                            'trainer_id'  => $assessor_id,
                            'user_rating' => $user_rating,
                        );
                        $trainer_rate = $this->common_model->get_value('assessment_trainer_result', 'id', 'assessment_id=' . $assessment_id . ' AND user_id=' . $user_id . ' AND trainer_id=' . $assessor_id);
                        if (count((array)$trainer_rate) > 0) {
                            $this->common_model->update('assessment_trainer_result', 'id', $trainer_rate->id, $trainer_data);
                        } else {
                            $this->common_model->insert('assessment_trainer_result', $trainer_data);
                        }
                        $cnt_rate = $this->common_model->get_value('assessment_complete_rating', 'id', 'assessment_id=' . $assessment_id . ' AND user_id=' . $user_id . ' AND trainer_id=' . $assessor_id);
                        if (count((array)$cnt_rate) == 0) {
                            $prameterData = $this->assessment_model->LoadTotalParameter($assessment_id, $user_id, $assessor_id);
                            $total_para = count((array)explode(',', $prameterData->para_list));
                            if ($total_para == $prameterData->tot_para) {
                                $qrate_data = array('assessment_id' => $assessment_id,
                                    'user_id' => $user_id,
                                    'trainer_id' => $assessor_id
                                );
                                $this->common_model->insert('assessment_complete_rating', $qrate_data);
								$this->stored_parameterwise_data($assessment_id,$user_id);
                            }
                        }
                    }
                }
				
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }
	public function stored_parameterwise_data($assessment_id='',$user_id=''){
		$lcwhere ='status=1';
		if($assessment_id !=''){
				$lcwhere .=' AND id ='.$assessment_id;
		}
		$assessment_set = $this->common_model->get_selected_values('assessment_mst', 'id,ratingstyle,is_weights',$lcwhere);
		if(count((array)$assessment_set)>0){
			foreach($assessment_set as $value){
				$this->assessment_model->insert_parameterwise_data($value->id,$value->ratingstyle,$value->is_weights,$user_id);
				//$this->assessment_model->insert_assessmentwise_data($value->id,$value->ratingstyle,$value->is_weights);
			}
		}
	}
    public function file_check($str) {
        $allowed_mime_type_arr = array('application/excel', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/octet-stream');
        $mime = $_FILES['filename']['type'];
        if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != "") {
            if (in_array($mime, $allowed_mime_type_arr)) {
                return true;
            } else {
                $this->form_validation->set_message('file_check', 'Please select only .xlsx or.xls file.');
                return false;
            }
        } else {
            $this->form_validation->set_message('file_check', 'Please select xls to import.');
            return false;
        }
    }

}
