<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Ai_reports extends MY_Controller {
    function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('ai_reports');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('ai_reports_model');
    }
    public function index() {
        $data['module_id'] = '14.03';
        $data['acces_management'] = $this->acces_management;
        // $_assessment_result = $this->common_model->get_selected_values('assessment_mst', 'id,assessment', 'status=1','assessment');
        $data['assessment_result'] = $this->ai_reports_model->get_assessments();
        $data['company_id'] = $this->mw_session['company_id'];
        $this->load->view('ai_reports/index',$data);
    }
    function fetch_participants(){
        $html               = '';
        $company_id         = $this->input->post('company_id', true);
        $asssessment_id     = $this->input->post('assessment_id', true);
        $report_type_result = $this->common_model->get_value('assessment_mst', 'report_type', 'company_id="'.$company_id.'" AND id="'.$asssessment_id.'"');
        $report_type        = 0;
        if (isset($report_type_result) AND count((array)$report_type_result)>0){
            $report_type = (int)$report_type_result->report_type;
        }
        $_participants_result         = $this->ai_reports_model->get_distinct_participants($company_id,$asssessment_id);
        $data['report_type']          = $report_type;
        $data['_participants_result'] = $_participants_result;

        $total_questions_played        = 0;
        $total_task_completed          = 0;
        $total_manual_rating_completed = 0;
        $show_ai_pdf                   = false;
        $show_manual_pdf               = false;
        $is_schdule_running            = false;
        $show_reports_flag             = false;
        $_total_played_result     = $this->common_model->get_value('assessment_results', 'count(*) as total', "company_id = '".$company_id."' AND assessment_id = '".$asssessment_id."' AND trans_id > 0 AND question_id > 0 AND vimeo_uri!='' AND ftp_status=1");
        if (isset($_total_played_result) AND count((array)$_total_played_result)>0){
            $total_questions_played = $_total_played_result->total;
        }
        $_tasksc_results     = $this->common_model->get_value('ai_schedule', 'count(*) as total', 'task_status="1" AND xls_generated="1" AND xls_filename!="" AND xls_imported="1" AND company_id="'.$company_id.'" AND assessment_id="'.$asssessment_id.'"');
        if (isset($_tasksc_results) AND count((array)$_tasksc_results)>0){
            $total_task_completed = $_tasksc_results->total;
        }
        $_manualrate_results     = $this->common_model->get_value('assessment_results_trans', 'count(DISTINCT user_id,question_id) as total', 'assessment_id="'.$asssessment_id.'"');
        if (isset($_manualrate_results) AND count((array)$_manualrate_results)>0){
            $total_manual_rating_completed = $_manualrate_results->total;
        }
        $_schdule_running_result     = $this->common_model->get_value('ai_cronjob', '*', 'assessment_id="'.$asssessment_id.'"');
        if (isset($_schdule_running_result) AND count((array)$_schdule_running_result)>0){
            $is_schdule_running = true;
        }
        $show_report_result = $this->common_model->get_value('ai_cronreports', 'id', 'company_id="'.$company_id.'" AND assessment_id="'.$asssessment_id.'" AND show_reports="1"');
        if (isset($show_report_result) AND count((array)$show_report_result)>0){
            $show_reports_flag = true;
        }
        if (((int)$total_questions_played >= (int)$total_task_completed) AND ((int)$total_task_completed>0)) {
            $show_ai_pdf = true;
        }
        if (((int)$total_task_completed >= (int)$total_questions_played) AND ((int)$total_questions_played>0)) {
            $show_ai_pdf = true;
        }
        if ((int)$total_questions_played >= (int)$total_manual_rating_completed){
            $show_manual_pdf = true;
        }
		$_user_rating_given        = $this->common_model->get_selected_values('assessment_results_trans', 'DISTINCT user_id,question_id', 'assessment_id="'.$asssessment_id.'"');																																									  
        $data['show_reports_flag'] = $show_reports_flag;
        $data['show_ai_pdf']       = $show_ai_pdf;
        $data['show_manual_pdf']   = $show_manual_pdf;
		$data['user_rating']       = $_user_rating_given;
        $html                      = $this->load->view('ai_reports/load_participants',$data,true);
        $output['html']            = $html;
        $output['success']         = "true";
        $output['message']         = "";
        echo json_encode($output);
    }
    public function fetch_questions(){
        $company_id    = $this->input->post('company_id', true);
        $assessment_id = $this->input->post('assessment_id', true);
        $user_id       = $this->input->post('user_id', true);

        $_participants_result         = $this->ai_reports_model->get_questions($company_id,$assessment_id,$user_id);
        $data['_participants_result'] = $_participants_result;
        $html                         = $this->load->view('ai_reports/load_questions',$data,true);
        $data['html']                 = $html;
        $data['success']              = "true";
        $data['message']              = "";
        echo json_encode($data);
    }
    public function view_ai_reports($_company_id,$_assessment_id,$_user_id){
        if ($_company_id=="" OR $_assessment_id=="" OR $_user_id==""){
            echo "Invalid parameter passed";
        }else{
            //GET COMPANY DETAILS
            $company_name = '';
			$company_logo = 'assets/images/Awarathon-Logo.png';
            $company_result = $this->common_model->get_value('company', 'company_name, company_logo', 'id="'.$_company_id.'"');
            if (isset($company_result) AND count((array)$company_result)>0){
                $company_name = $company_result->company_name;
                $company_logo = !empty($company_result->company_logo) ? '/assets/uploads/company/'.$company_result->company_logo : '';
            }
            $data['company_name'] = $company_name;
			$data['company_logo'] = $company_logo;
			
            //GET PARTICIPANT DETAILS
            $participant_name = '';
            $participant_result = $this->common_model->get_value('device_users', '*', 'user_id="'.$_user_id.'"');
            if (isset($participant_result) AND count((array)$participant_result)>0){
                $participant_name = $participant_result->firstname." ".$participant_result->lastname." - ".$_user_id;
            }
            $data['participant_name'] = $participant_name;
            
            //OVERALL SCORE
            $overall_score = 0;
            $your_rank = 0;
            $overall_score_result = $this->ai_reports_model->get_overall_score_rank($_company_id,$_assessment_id,$_user_id);
            if (isset($overall_score_result) AND count((array)$overall_score_result)>0){
                $overall_score = $overall_score_result->overall_score;
                $your_rank = $overall_score_result->final_rank;
            }
            $data['overall_score'] = $overall_score;
            $data['your_rank'] = $your_rank;
            
            $rating = '';
            if ((float)$overall_score >= 69.9){
                $rating = 'A';
            }else if ((float)$overall_score < 69.9 AND (float)$overall_score >= 63.23){
                $rating = 'B';
            }else if ((float)$overall_score < 63.23 AND (float)$overall_score >= 54.9){
                $rating = 'C';
            }else if ((float)$overall_score < 54.9){
                $rating = 'D';
            }
            $data['rating'] = $rating;


            //QUESTIONS LIST
            $best_video_list = [];
            $questions_list  = [];
            $partd_list      = [];
            $i = 0;
            $question_result = $this->ai_reports_model->get_questions($_company_id,$_assessment_id);
            foreach ($question_result as $qr){
                $question_id     = $qr->question_id;
                $question        = $qr->question;
                $question_series = $qr->question_series;
                $_trans_id       = $qr->trans_id;

                $question_your_score_result   = $this->ai_reports_model->get_question_your_score($_company_id,$_assessment_id,$_user_id,$question_id);
                $question_minmax_score_result = $this->ai_reports_model->get_question_minmax_score($_company_id,$_assessment_id,$question_id);
                $question_your_video_result   = $this->ai_reports_model->get_your_video($_company_id,$_assessment_id,$_user_id,$_trans_id,$question_id);
                $question_best_video_result   = $this->ai_reports_model->get_best_video($_company_id,$_assessment_id,$question_id);
                $ai_sentkey_score_result      = $this->common_model->get_selected_values('ai_sentkey_score', '*', 'company_id="'.$_company_id.'" AND assessment_id="'.$_assessment_id.'" AND user_id="'.$_user_id.'" AND trans_id="'.$_trans_id.'" AND question_id="'.$question_id.'"');

                $your_vimeo_url  = "";
                if (isset($question_your_video_result) AND count((array)$question_your_video_result)>0){
                    $your_vimeo_url = $question_your_video_result->vimeo_url;
                }

                $best_vimeo_url  = "";
                if (isset($question_best_video_result) AND count((array)$question_best_video_result)>0){
                    $best_vimeo_url = $question_best_video_result->vimeo_url;
                    $ai_best_ideal_video_result = $this->common_model->get_value('ai_best_ideal_video', '*', 'assessment_id="'.$_assessment_id.'" AND question_id="'.$question_id.'"');
                    if (isset($ai_best_ideal_video_result) AND count((array)$ai_best_ideal_video_result)>0){
                        $best_vimeo_url = $ai_best_ideal_video_result->best_video_link;
                    }
                }else{
                    $ai_best_ideal_video_result = $this->common_model->get_value('ai_best_ideal_video', '*', 'assessment_id="'.$_assessment_id.'" AND question_id="'.$question_id.'"');
                    if (isset($ai_best_ideal_video_result) AND count((array)$ai_best_ideal_video_result)>0){
                        $best_vimeo_url = $ai_best_ideal_video_result->best_video_link;
                    }
                }

                $your_score  = 0;
                if (isset($question_your_score_result) AND count((array)$question_your_score_result)>0){
                    $your_score = $question_your_score_result->score;
                }
                $highest_score  = 0;
                $lowest_score  = 0;
                if (isset($question_minmax_score_result) AND count((array)$question_minmax_score_result)>0){
                    $highest_score = $question_minmax_score_result->max_score;
                    $lowest_score  = $question_minmax_score_result->min_score;
                }

                array_push($best_video_list,array(
                    "question_series" => $question_series,
                    "your_vimeo_url"  => $your_vimeo_url,
                    "best_vimeo_url"  => $best_vimeo_url,
                ));
                array_push($questions_list,array(
                    "question_id"     => $question_id,
                    "question"        => $question,
                    "question_series" => $question_series,
                    "your_score"      => $your_score,
                    "highest_score"   => $highest_score,
                    "lowest_score"    => $lowest_score,
                ));
                
                $temp_partd_list = [];
                $partd_list[$i]['question_series'] = $question_series;
                $partd_list[$i]['question']        = $question;
                
                if (isset($ai_sentkey_score_result) AND count($ai_sentkey_score_result)>0){
                    foreach($ai_sentkey_score_result as $sksr){
                        // $sentkey_type_result = $this->common_model->get_value('assessment_trans_sparam', '*', 'type_id!=0 AND assessment_id="'.$_assessment_id.'" AND question_id="'.$question_id.'" AND sentence_keyword LIKE "%'.$sksr->sentance_keyword.'%" ');
                        $sentkey_type_result = $this->common_model->get_value('assessment_trans_sparam', '*', 'type_id!=0 AND assessment_id="'.$_assessment_id.'" AND question_id="'.$question_id.'"');
                        $tick_icons = '';
                        if (isset($sentkey_type_result) AND count((array)$sentkey_type_result)>0){
                            if ($sentkey_type_result->type_id==1){ //Sentance 
                                if ($sksr->score >= 60){
                                    $tick_icons = 'green';
                                }
                                if ($sksr->score <= 50){
                                    $tick_icons = 'red';
                                }
                                if ($sksr->score > 50 AND $sksr->score < 60){
                                    $tick_icons = 'yellow';
                                }
                            }
                            if ($sentkey_type_result->type_id==2){ //Keyword
                                if ($sksr->score >= 60){
                                    $tick_icons = 'green';
                                }
                                if ($sksr->score < 60){
                                    $tick_icons = 'red';
                                }
                            }
                        }
                        array_push($temp_partd_list,array(
                            "sentance_keyword" => $sksr->sentance_keyword,
                            "score"            => $sksr->score,
                            "tick_icons"       => $tick_icons,
                        ));
                    }
                    $partd_list[$i]['list']        = $temp_partd_list;
                }
                $i++;
            }
            $data['best_video_list'] = $best_video_list;    
            $data['questions_list']  = $questions_list;    
            $data['partd_list']      = $partd_list;    
            
            //PARAMETER LIST
            $parameter_score = [];
            $parameter_score_result = $this->ai_reports_model->get_parameters($_company_id,$_assessment_id);
            foreach ($parameter_score_result as $psr){
                $parameter_id                  = $psr->parameter_id;
                $parameter_label_id            = $psr->parameter_label_id;
                $parameter_your_score_result   = $this->ai_reports_model->get_parameters_your_score($_company_id,$_assessment_id,$_user_id,$parameter_id,$parameter_label_id);
                $parameter_minmax_score_result = $this->ai_reports_model->get_parameter_minmax_score($_company_id,$_assessment_id,$parameter_id,$parameter_label_id);
                
                $your_score  = 0;
                if (isset($parameter_your_score_result) AND count((array)$parameter_your_score_result)>0){
                    $your_score = $parameter_your_score_result->score;
                }
                $highest_score = 0;
                $lowest_score  = 0;
                if (isset($parameter_minmax_score_result) AND count((array)$parameter_minmax_score_result)>0){
                    $highest_score = $parameter_minmax_score_result->max_score;
                    $lowest_score  = $parameter_minmax_score_result->min_score;
                }

                array_push($parameter_score,array(
                    "parameter_id"         => $psr->parameter_id,
                    "parameter_label_id"   => $psr->parameter_label_id,
                    "parameter_name"       => $psr->parameter_name,
                    "parameter_label_name" => $psr->parameter_label_name,
                    "your_score"           => $your_score,
                    "highest_score"        => $highest_score,
                    "lowest_score"         => $lowest_score,
                ));
            } 
            $data['parameter_score'] = $parameter_score;

            // $this->load->library('Pdf_Library');
            $data['show_ranking'] = 0;
            $show_ranking_result = $this->common_model->get_value('ai_cronreports', 'show_ranking', 'assessment_id="'.$_assessment_id.'"');
            if (isset($show_ranking_result) AND count((array)$show_ranking_result)>0){
                $data['show_ranking'] = $show_ranking_result->show_ranking;
            }
            $htmlContent = $this->load->view('ai_reports/ai_pdf',$data,true);
            
            // //DIVEYSH PANCHAL
            ob_start();
            define('K_TCPDF_EXTERNAL_CONFIG', true);
            $this->load->library('Pdf');
            // $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $data['pdf'] = $pdf;
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Awarathon');
            $pdf->SetTitle("Awarathon's Sales Readiness Reports");
            $pdf->SetSubject("Awarathon's Sales Readiness Reports");
            $pdf->SetKeywords('Awarathon');
            $pdf->SetHeaderData('',0, '', '',array(255,255,255),array(255,255,255));
			$pdf->setHtmlHeader('<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #000000;">
                <tr>
                    <td style="height:10px;width:60%">
                        <div class="page-title">Sales Readiness Reports</div>
                    </td>
                    <td style="height:10px;width:40%;text-align:right;">
                        <img style="text-align: top;width:90px;height:auto;margin:0px auto;" src="'.$data['company_logo'].'"/>
                    </td>
                </tr>
            </table>');
            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
            $pdf->SetMargins(PDF_MARGIN_LEFT, 5, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            $pdf->SetAutoPageBreak(TRUE, 20);
            //$pdf->SetAutoPageBreak(TRUE, 0);
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            $pdf->PrintCoverPageFooter = True;

            $pdf->AddPage();
            $pdf->setJPEGQuality(100);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->writeHTML($htmlContent, true, false, true, false, '');
            $pdf->lastPage();       
            ob_end_clean();
            
            $now       = date('YmdHis');
            $file_name = 'C'.$_company_id.'A'.$_assessment_id.'U'.$_user_id.'DTTM'.$now.'.pdf';
            $pdf->Output($file_name, 'I'); 
        }
    }
    public function view_manual_reports($_company_id,$_assessment_id,$_user_id){
        if ($_company_id=="" OR $_assessment_id=="" OR $_user_id==""){
            echo "Invalid parameter passed";
        }else{
            //GET COMPANY DETAILS
            $company_name = '';
            $company_logo = 'assets/images/Awarathon-Logo.png';
            $company_result = $this->common_model->get_value('company', 'company_name, company_logo', 'id="'.$_company_id.'"');
            if (isset($company_result) AND count((array)$company_result)>0){
                $company_name = $company_result->company_name;
                $company_logo = !empty($company_result->company_logo) ? '/assets/uploads/company/'.$company_result->company_logo : '';
            }
            $data['company_name'] = $company_name;
			$data['company_logo'] = $company_logo;			
            
            //GET PARTICIPANT DETAILS
            $participant_name = '';
            $participant_result = $this->common_model->get_value('device_users', '*', 'user_id="'.$_user_id.'"');
            if (isset($participant_result) AND count((array)$participant_result)>0){
                $participant_name = $participant_result->firstname." ".$participant_result->lastname." - ".$_user_id;
            }
            $data['participant_name'] = $participant_name;

            //GET MANAGER NAME
            $manager_id = '';
            $manager_name = '';
            $manager_result = $this->ai_reports_model->get_manager_name($_assessment_id,$_user_id);
            if (isset($manager_result) AND count((array)$manager_result)>0){
                $manager_id = $manager_result->manager_id;
                $manager_name = $manager_result->manager_name;
            }
            $data['manager_name'] = $manager_name;

            
            //OVERALL SCORE
            $overall_score = 0;
            $your_rank = 0;
			$user_rating = $this->common_model->get_selected_values('assessment_results_trans', 'DISTINCT user_id,question_id', 'assessment_id="'.$_assessment_id.'" AND user_id="'.$_user_id.'"');
			if(empty($user_rating)){
				$data['overall_score'] = 'Not assessed';
				$data['your_rank'] = 'Pending';
				$data['rating'] = 'Pending';
			}else{
				$overall_score_result = $this->ai_reports_model->get_manual_overall_score_rank($_company_id,$_assessment_id,$_user_id);
				if (isset($overall_score_result) AND count((array)$overall_score_result)>0){
					$overall_score = $overall_score_result->overall_score;
					$your_rank = $overall_score_result->final_rank;
				}
				$data['overall_score'] = number_format($overall_score,2,'.','').'%';
				$data['your_rank'] = $your_rank;
				$rating = '';
				if ((float)$overall_score >= 69.9){
					$rating = 'A';
				}else if ((float)$overall_score < 69.9 AND (float)$overall_score >= 63.23){
					$rating = 'B';
				}else if ((float)$overall_score < 63.23 AND (float)$overall_score >= 54.9){
					$rating = 'C';
				}else if ((float)$overall_score < 54.9){
					$rating = 'D';
				}
				$data['rating'] = $rating;
			}

            //QUESTIONS LIST
            $best_video_list = [];
            $questions_list  = [];
            $partd_list      = [];
            $manager_comments_list = [];
            $i = 0;
            $question_result = $this->ai_reports_model->get_questions($_company_id,$_assessment_id);
            foreach ($question_result as $qr){
                $question_id     = $qr->question_id;
                $question        = $qr->question;
                $question_series = $qr->question_series;
                $_trans_id       = $qr->trans_id;

                $question_your_score_result      = $this->ai_reports_model->get_manual_question_your_score($_company_id,$_assessment_id,$_user_id,$question_id);
                $question_minmax_score_result    = $this->ai_reports_model->get_manual_question_minmax_score($_company_id,$_assessment_id,$question_id);
                $question_your_video_result      = $this->ai_reports_model->get_your_video($_company_id,$_assessment_id,$_user_id,$_trans_id,$question_id);
                $question_best_video_result      = $this->ai_reports_model->get_manual_best_video($_company_id,$_assessment_id,$question_id);
                $question_manager_comment_result = $this->ai_reports_model->get_manager_comments($_assessment_id,$_user_id,$question_id,$manager_id);

                $your_vimeo_url  = "";
                if (isset($question_your_video_result) AND count((array)$question_your_video_result)>0){
                    $your_vimeo_url = $question_your_video_result->vimeo_url;
                }

                $best_vimeo_url  = "";
                if (isset($question_best_video_result) AND count((array)$question_best_video_result)>0){
                    $best_vimeo_url = $question_best_video_result->vimeo_url;
                }

                $your_score  = 0;
                if (isset($question_your_score_result) AND count((array)$question_your_score_result)>0){
                    $your_score = number_format($question_your_score_result->score,2,'.','').'%';
                }else{
					$your_score = 'Not assessed';
				}
                $highest_score  = 0;
                $lowest_score  = 0;
                if (isset($question_minmax_score_result) AND count((array)$question_minmax_score_result)>0){
                    $highest_score = $question_minmax_score_result->max_score;
                    $lowest_score  = $question_minmax_score_result->min_score;
                }
                $comments  = '';
                if (isset($question_manager_comment_result) AND count((array)$question_manager_comment_result)>0){
                    $comments  = $question_manager_comment_result->remarks;
                }

                array_push($best_video_list,array(
                    "question_series" => $question_series,
                    "your_vimeo_url"  => $your_vimeo_url,
                    "best_vimeo_url"  => $best_vimeo_url,
                ));
                array_push($questions_list,array(
                    "question_id"     => $question_id,
                    "question"        => $question,
                    "question_series" => $question_series,
                    "your_score"      => $your_score,
                    "highest_score"   => $highest_score,
                    "lowest_score"    => $lowest_score,
                ));
                array_push($manager_comments_list,array(
                    "question_id"     => $question_id,
                    "question"        => $question,
                    "question_series" => $question_series,
                    "comments"        => $comments,
                ));

                $temp_partd_list = [];
                $partd_list[$i]['question_series'] = $question_series;
                $partd_list[$i]['question']        = $question;
                $i++;
            }
            $data['best_video_list']       = $best_video_list;    
            $data['questions_list']        = $questions_list;    
            $data['manager_comments_list'] = $manager_comments_list;    
            
            //GET OVERALL COMMENTS
            $overall_comments = '';
            $overall_comments_result = $this->common_model->get_value('assessment_trainer_result', 'remarks', 'assessment_id="'.$_assessment_id.'" and user_id="'.$_user_id.'" and trainer_id="'.$manager_id.'"');
            if (isset($overall_comments_result) AND count((array)$overall_comments_result)>0){
                $overall_comments = $overall_comments_result->remarks;
            }
            $data['overall_comments'] = $overall_comments;

            //PARAMETER LIST
            $parameter_score = [];
            $parameter_score_result = $this->ai_reports_model->get_parameters($_company_id,$_assessment_id);
            foreach ($parameter_score_result as $psr){
                $parameter_id                  = $psr->parameter_id;
                $parameter_label_id            = $psr->parameter_label_id;
                $parameter_your_score_result   = $this->ai_reports_model->get_manual_parameters_your_score($_company_id,$_assessment_id,$_user_id,$parameter_id,$parameter_label_id);
                $parameter_minmax_score_result = $this->ai_reports_model->get_manual_parameter_minmax_score($_user_id,$_assessment_id,$parameter_id,$parameter_label_id);
                
                $your_score  = 0;
                if (isset($parameter_your_score_result) AND count((array)$parameter_your_score_result)>0 AND !empty($parameter_your_score_result->percentage)){
                    $your_score = number_format($parameter_your_score_result->percentage,2,'.','').'%';
                }else{
					$your_score = 'Not assessed';
				}
                $highest_score = 0;
                $lowest_score  = 0;
                if (isset($parameter_minmax_score_result) AND count((array)$parameter_minmax_score_result)>0){
                    $highest_score = $parameter_minmax_score_result->max_score;
                    $lowest_score  = $parameter_minmax_score_result->min_score;
                }

                array_push($parameter_score,array(
                    "parameter_id"         => $psr->parameter_id,
                    "parameter_label_id"   => $psr->parameter_label_id,
                    "parameter_name"       => $psr->parameter_name,
                    "parameter_label_name" => $psr->parameter_label_name,
                    "your_score"           => $your_score,
                    "highest_score"        => $highest_score,
                    "lowest_score"         => $lowest_score,
                ));
            } 
            $data['parameter_score'] = $parameter_score;


            // $this->load->library('Pdf_Library');
            $htmlContent = $this->load->view('ai_reports/manual_pdf',$data,true);

            // //DIVEYSH PANCHAL
            ob_start();
            define('K_TCPDF_EXTERNAL_CONFIG', true);
            $this->load->library('Pdf');
            //$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            //Below line is added
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $data['pdf'] = $pdf;
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Awarathon');
            $pdf->SetTitle("Awarathon's Sales Readiness Reports");
            $pdf->SetSubject("Awarathon's Sales Readiness Reports");
            $pdf->SetKeywords('Awarathon');
            $pdf->SetHeaderData('',0, '', '',array(255,255,255),array(255,255,255));
			$pdf->setHtmlHeader('<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #000000;">
                <tr>
                    <td style="height:10px;width:60%">
                        <div class="page-title">Sales Readiness Reports</div>
                    </td>
                    <td style="height:10px;width:40%;text-align:right;">
                        <img style="text-align: top;width:90px;height:auto;margin:0px auto;" src="'.$data['company_logo'].'"/>
                    </td>
                </tr>
            </table>');
            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
            $pdf->SetMargins(PDF_MARGIN_LEFT, 5, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            //$pdf->SetAutoPageBreak(TRUE, 0);
            $pdf->SetAutoPageBreak(TRUE, 20);
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            //Added below line: As we don't want footer on front page
            $pdf->PrintCoverPageFooter = True;

            $pdf->AddPage();
            $pdf->setJPEGQuality(100);
            $pdf->SetFont('helvetica', '', 10);
            
            $pdf->writeHTML($htmlContent, true, false, true, false, '');
            $pdf->lastPage();       
            ob_end_clean();
            
            $now       = date('YmdHis');
            $file_name = 'MANU-C'.$_company_id.'A'.$_assessment_id.'U'.$_user_id.'DTTM'.$now.'.pdf';
            $pdf->Output($file_name, 'I'); 
        }
    }
    public function view_combine_reports($_company_id,$_assessment_id,$_user_id){
        if ($_company_id=="" OR $_assessment_id=="" OR $_user_id==""){
            echo "Invalid parameter passed";
        }else{
            //GET COMPANY DETAILS
            $company_name = '';
            $company_logo = 'assets/images/Awarathon-Logo.png';
            $company_result = $this->common_model->get_value('company', 'company_name, company_logo', 'id="'.$_company_id.'"');
            if (isset($company_result) AND count((array)$company_result)>0){
                $company_name = $company_result->company_name;
                $company_logo = !empty($company_result->company_logo) ? '/assets/uploads/company/'.$company_result->company_logo : '';
            }
            $data['company_name'] = $company_name;
			$data['company_logo'] = $company_logo;			
            
            //GET PARTICIPANT DETAILS
            $participant_name = '';
            $participant_result = $this->common_model->get_value('device_users', '*', 'user_id="'.$_user_id.'"');
            if (isset($participant_result) AND count((array)$participant_result)>0){
                $participant_name = $participant_result->firstname." ".$participant_result->lastname." - ".$_user_id;
            }
            $data['participant_name'] = $participant_name;
            
            //GET MANAGER NAME
            $manager_id = '';
            $manager_name = '';
            $manager_result = $this->ai_reports_model->get_manager_name($_assessment_id,$_user_id);
            if (isset($manager_result) AND count((array)$manager_result)>0){
                $manager_id = $manager_result->manager_id;
                $manager_name = $manager_result->manager_name;
            }
            $data['manager_name'] = $manager_name;
            
            //OVERALL SCORE
            $overall_score = 0;
            $overall_score_result = $this->ai_reports_model->get_user_overall_score_combined($_company_id,$_assessment_id,$_user_id);
            if (isset($overall_score_result) AND count((array)$overall_score_result)>0){
                $overall_score = $overall_score_result->overall_score;
            }
            $data['overall_score'] = $overall_score;
            

            //QUESTIONS LIST
            $questions_list  = [];
            $manager_comments_list = [];
            $question_result = $this->ai_reports_model->get_questions($_company_id,$_assessment_id);
            foreach ($question_result as $qr){
                $question_id     = $qr->question_id;
                $question        = $qr->question;
                $question_series = $qr->question_series;

                $question_ai_score_result   = $this->ai_reports_model->get_question_your_score($_company_id,$_assessment_id,$_user_id,$question_id);
                $question_manual_score_result = $this->ai_reports_model->get_question_manual_score($_assessment_id,$_user_id,$question_id);
                $question_manager_comment_result = $this->ai_reports_model->get_manager_comments($_assessment_id,$_user_id,$question_id,$manager_id);
                
                $ai_score  = 0;
                if (isset($question_ai_score_result) AND count((array)$question_ai_score_result)>0){
                    $ai_score = $question_ai_score_result->score;
                }
                $manual_score  = 0;
                if (isset($question_manual_score_result) AND count((array)$question_manual_score_result)>0){
                    $manual_score  = $question_manual_score_result->score;
                }
                $comments  = '';
                if (isset($question_manager_comment_result) AND count((array)$question_manager_comment_result)>0){
                    $comments  = $question_manager_comment_result->remarks;
                }
                if($manual_score==0 || $ai_score==0)
                {
                    $combined_score = number_format((($ai_score + $manual_score)),2);    
                }
                else
                {
                    $combined_score = number_format((($ai_score + $manual_score)/2),2);
                }

                array_push($questions_list,array(
                    "question_id"     => $question_id,
                    "question"        => $question,
                    "question_series" => $question_series,
                    "ai_score"        => $ai_score,
                    "manual_score"    => empty($manual_score) ? 'Not assessed' : number_format($manual_score,2,'.','').'%',
                    "combined_score"  => $combined_score,
                ));

                array_push($manager_comments_list,array(
                    "question_id"     => $question_id,
                    "question"        => $question,
                    "question_series" => $question_series,
                    "comments"        => $comments,
                ));

            }
            $data['questions_list']  = $questions_list;    
            $data['manager_comments_list']  = $manager_comments_list;    
        

            //GET OVERALL COMMENTS
            $overall_comments = '';
            $overall_comments_result = $this->common_model->get_value('assessment_trainer_result', 'remarks', 'assessment_id="'.$_assessment_id.'" and user_id="'.$_user_id.'" and trainer_id="'.$manager_id.'"');
            if (isset($overall_comments_result) AND count((array)$overall_comments_result)>0){
                $overall_comments = $overall_comments_result->remarks;
            }
            $data['overall_comments'] = $overall_comments;

            //PARAMETER LIST
            $parameter_score = [];
            $parameter_score_result = $this->ai_reports_model->get_parameters($_company_id,$_assessment_id);
            foreach ($parameter_score_result as $psr){
                $parameter_id                  = $psr->parameter_id;
                $parameter_label_id            = $psr->parameter_label_id;
                $parameter_your_score_result   = $this->ai_reports_model->get_parameters_your_score($_company_id,$_assessment_id,$_user_id,$parameter_id,$parameter_label_id);
                $parameter_manual_score_result = $this->ai_reports_model->get_parameter_manual_score($_assessment_id,$_user_id,$parameter_id,$parameter_label_id);
                
                $your_score  = 0;
                if (isset($parameter_your_score_result) AND count((array)$parameter_your_score_result)>0){
                    $your_score = $parameter_your_score_result->score;
                }
                $manual_score  = 0;
                if (isset($parameter_manual_score_result) AND count((array)$parameter_manual_score_result)>0){
                    $manual_score = $parameter_manual_score_result->percentage;
                }
                if($manual_score==0 || $your_score==0)
                {
                    $combined_score = number_format((($your_score + $manual_score)),2);    
                }
                else
                {
                    $combined_score = number_format((($your_score + $manual_score)/2),2);
                }

                array_push($parameter_score,array(
                    "parameter_id"         => $psr->parameter_id,
                    "parameter_label_id"   => $psr->parameter_label_id,
                    "parameter_name"       => $psr->parameter_name,
                    "parameter_label_name" => $psr->parameter_label_name,
                    "your_score"           => $your_score,
                    "manual_score"        => empty($manual_score) ? 'Not assessed' : number_format($manual_score,2,'.','').'%',
                    "combined_score"        => $combined_score,
                ));
            } 
            $data['parameter_score'] = $parameter_score;

            // $this->load->library('Pdf_Library');
            $htmlContent = $this->load->view('ai_reports/combined_pdf',$data,true);

            // //DIVEYSH PANCHAL
            ob_start();
            define('K_TCPDF_EXTERNAL_CONFIG', true);
            $this->load->library('Pdf');
			//  $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $data['pdf'] = $pdf;
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Awarathon');
            $pdf->SetTitle("Awarathon's Sales Readiness Reports");
            $pdf->SetSubject("Awarathon's Sales Readiness Reports");
            $pdf->SetKeywords('Awarathon');
            $pdf->SetHeaderData('',0, '', '',array(255,255,255),array(255,255,255));
			$pdf->setHtmlHeader('<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #000000;">
                <tr>
                    <td style="height:10px;width:60%">
                        <div class="page-title">Sales Readiness Reports</div>
                    </td>
                    <td style="height:10px;width:40%;text-align:right;">
                        <img style="text-align: top;width:90px;height:auto;margin:0px auto;" src="'.$data['company_logo'].'"/>
                    </td>
                </tr>
            </table>');
            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
            $pdf->SetMargins(PDF_MARGIN_LEFT, 5, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            //$pdf->SetAutoPageBreak(TRUE, 0);
            $pdf->SetAutoPageBreak(TRUE, 20);
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            $pdf->PrintCoverPageFooter = True;

            $pdf->AddPage();
            $pdf->setJPEGQuality(100);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->writeHTML($htmlContent, true, false, true, false, '');
            $pdf->lastPage();       
            ob_end_clean();
            
            $now       = date('YmdHis');
            $file_name = 'COMB-C'.$_company_id.'A'.$_assessment_id.'U'.$_user_id.'DTTM'.$now.'.pdf';
            $pdf->Output($file_name, 'I'); 
        }
    }
    public function regenerate_pdf(){
        $_company_id     = 59; //$this->input->post('company_id', true);
        $_assessment_id  = 94; //$this->input->post('assessment_id', true);
        $_report_type    = 1;  //$this->input->post('report_type', true);
        $site_url_result = $this->common_model->get_value('company', 'domin_url', 'id="'.$_company_id.'"'); 
        $site_url        = 'https://ai.awarathon.com';
        if (isset($site_url_result) AND count((array)$site_url_result)>0){
            $site_url = $site_url_result->domin_url;
        }
        $task_result = $this->ai_reports_model->get_unique_candidates($_company_id,$_assessment_id);
        if (isset($task_result) AND count((array)$task_result)>0){
            foreach($task_result as $tdata){
                $company_id      = $tdata->company_id;
                $assessment_id   = $tdata->assessment_id;
                $user_id         = $tdata->user_id;

                if ($_report_type==1){ //AI PDF START
                    if ($company_id!="" AND $assessment_id!="" AND $user_id!=""){

                        //CHECK ALL EXCEL IMPORTED ?
                        $total_video        = 0;
                        $total_xls_imported = 0;
                        $total_video_result = $this->common_model->get_value('ai_schedule', 'count(*) as total_video', 'company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'"');
                        if (isset($total_video_result) AND count((array)$total_video_result)>0){
                            $total_video = (float)$total_video_result->total_video;
                        }
                        $total_xls_imported_result = $this->common_model->get_value('ai_schedule', 'count(*) as total_xls', 'company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'" AND xls_imported=1');
                        if (isset($total_xls_imported_result) AND count((array)$total_xls_imported_result)>0){
                            $total_xls_imported = (float)$total_xls_imported_result->total_xls;
                        }
                        if ($total_video == $total_xls_imported){
                            //GET COMPANY DETAILS
                            $company_name = '';
							$company_logo = 'assets/images/Awarathon-Logo.png';
							$company_result = $this->common_model->get_value('company', 'company_name, company_logo', 'id="'.$company_id.'"');
							if (isset($company_result) AND count((array)$company_result)>0){
								$company_name = $company_result->company_name;
								$company_logo = !empty($company_result->company_logo) ? '/assets/uploads/company/'.$company_result->company_logo : '';
							}
							$data['company_name'] = $company_name;
							$data['company_logo'] = $company_logo;
                            
                            //GET PARTICIPANT DETAILS
                            $participant_name = '';
                            $participant_result = $this->common_model->get_value('device_users', '*', 'user_id="'.$user_id.'"');
                            if (isset($participant_result) AND count((array)$participant_result)>0){
                                $participant_name = $participant_result->firstname." ".$participant_result->lastname." - ".$user_id;
                            }
                            $data['participant_name'] = $participant_name;
                            
                            //OVERALL SCORE
                            $overall_score = 0;
                            $your_rank = 0;
                            $overall_score_result = $this->ai_reports_model->get_overall_score_rank($company_id,$assessment_id,$user_id);
                            if (isset($overall_score_result) AND count((array)$overall_score_result)>0){
                                $overall_score = $overall_score_result->overall_score;
                                $your_rank = $overall_score_result->final_rank;
                            }
                            $data['overall_score'] = $overall_score;
                            $data['your_rank'] = $your_rank;
                            
                            $rating = '';
                            if ((float)$overall_score >= 69.9){
                                $rating = 'A';
                            }else if ((float)$overall_score < 69.9 AND (float)$overall_score >= 63.23){
                                $rating = 'B';
                            }else if ((float)$overall_score < 63.23 AND (float)$overall_score >= 54.9){
                                $rating = 'C';
                            }else if ((float)$overall_score < 54.9){
                                $rating = 'D';
                            }
                            $data['rating'] = $rating;


                            //QUESTIONS LIST
                            $best_video_list = [];
                            $questions_list  = [];
                            $partd_list      = [];
                            $i = 0;
                            $question_result = $this->ai_reports_model->get_questions($company_id,$assessment_id);
                            foreach ($question_result as $qr){
                                $question_id     = $qr->question_id;
                                $question        = $qr->question;
                                $question_series = $qr->question_series;
                                $_trans_id       = $qr->trans_id;

                                $question_your_score_result   = $this->ai_reports_model->get_question_your_score($company_id,$assessment_id,$user_id,$question_id);
                                $question_minmax_score_result = $this->ai_reports_model->get_question_minmax_score($company_id,$assessment_id,$question_id);
                                $question_your_video_result   = $this->ai_reports_model->get_your_video($company_id,$assessment_id,$user_id,$_trans_id,$question_id);
                                $question_best_video_result   = $this->ai_reports_model->get_best_video($company_id,$assessment_id,$question_id);
                                $ai_sentkey_score_result      = $this->common_model->get_selected_values('ai_sentkey_score', '*', 'company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'" AND user_id="'.$user_id.'" AND trans_id="'.$_trans_id.'" AND question_id="'.$question_id.'"');
                    
                                $your_vimeo_url  = "";
                                if (isset($question_your_video_result) AND count((array)$question_your_video_result)>0){
                                    $your_vimeo_url = $question_your_video_result->vimeo_url;
                                }

                                $best_vimeo_url  = "";
                                if (isset($question_best_video_result) AND count((array)$question_best_video_result)>0){
                                    $best_vimeo_url = $question_best_video_result->vimeo_url;
                                }

                                $your_score  = 0;
                                if (isset($question_your_score_result) AND count((array)$question_your_score_result)>0){
                                    $your_score = $question_your_score_result->score;
                                }
                                $highest_score  = 0;
                                $lowest_score  = 0;
                                if (isset($question_minmax_score_result) AND count((array)$question_minmax_score_result)>0){
                                    $highest_score = $question_minmax_score_result->max_score;
                                    $lowest_score  = $question_minmax_score_result->min_score;
                                }

                                array_push($best_video_list,array(
                                    "question_series" => $question_series,
                                    "your_vimeo_url"  => $your_vimeo_url,
                                    "best_vimeo_url"  => $best_vimeo_url,
                                ));
                                array_push($questions_list,array(
                                    "question_id"     => $question_id,
                                    "question"        => $question,
                                    "question_series" => $question_series,
                                    "your_score"      => $your_score,
                                    "highest_score"   => $highest_score,
                                    "lowest_score"    => $lowest_score,
                                ));
                                
                                $temp_partd_list = [];
                                $partd_list[$i]['question_series'] = $question_series;
                                $partd_list[$i]['question']        = $question;
                                
                                if (isset($ai_sentkey_score_result) AND count($ai_sentkey_score_result)>0){
                                    foreach($ai_sentkey_score_result as $sksr){
                                        
                                        // $sentkey_type_result = $this->common_model->get_value('assessment_trans_sparam', '*', 'type_id!=0 AND assessment_id="'.$assessment_id.'" AND question_id="'.$question_id.'" AND sentence_keyword LIKE "%'.$sksr->sentance_keyword.'%" ');
                                        $sentkey_type_result = $this->common_model->get_value('assessment_trans_sparam', '*', 'type_id!=0 AND assessment_id="'.$_assessment_id.'" AND question_id="'.$question_id.'"');
                                        $tick_icons = '';
                                        if (isset($sentkey_type_result) AND count((array)$sentkey_type_result)>0){
                                            if ($sentkey_type_result->type_id==1){ //Sentance 
                                                if ($sksr->score >= 60){
                                                    $tick_icons = 'green';
                                                }
                                                if ($sksr->score <= 50){
                                                    $tick_icons = 'red';
                                                }
                                                if ($sksr->score > 50 AND $sksr->score < 60){
                                                    $tick_icons = 'yellow';
                                                }
                                            }
                                            if ($sentkey_type_result->type_id==2){ //Keyword
                                                if ($sksr->score >= 60){
                                                    $tick_icons = 'green';
                                                }
                                                if ($sksr->score < 60){
                                                    $tick_icons = 'red';
                                                }
                                            }
                                        }
                                        array_push($temp_partd_list,array(
                                            "sentance_keyword" => $sksr->sentance_keyword,
                                            "score"            => $sksr->score,
                                            "tick_icons"       => $tick_icons,
                                        ));
                                    }
                                    $partd_list[$i]['list']        = $temp_partd_list;
                                }
                                $i++;
                            }
                            $data['best_video_list'] = $best_video_list;    
                            $data['questions_list']  = $questions_list;    
                            $data['partd_list']      = $partd_list;    
                            
                            //PARAMETER LIST
                            $parameter_score = [];
                            $parameter_score_result = $this->ai_reports_model->get_parameters($company_id,$assessment_id);
                            foreach ($parameter_score_result as $psr){
                                $parameter_id                  = $psr->parameter_id;
                                $parameter_label_id            = $psr->parameter_label_id;
                                $parameter_your_score_result   = $this->ai_reports_model->get_parameters_your_score($company_id,$assessment_id,$user_id,$parameter_id,$parameter_label_id);
                                $parameter_minmax_score_result = $this->ai_reports_model->get_parameter_minmax_score($company_id,$assessment_id,$parameter_id,$parameter_label_id);
                                
                                $your_score  = 0;
                                if (isset($parameter_your_score_result) AND count((array)$parameter_your_score_result)>0){
                                    $your_score = $parameter_your_score_result->score;
                                }
                                $highest_score = 0;
                                $lowest_score  = 0;
                                if (isset($parameter_minmax_score_result) AND count((array)$parameter_minmax_score_result)>0){
                                    $highest_score = $parameter_minmax_score_result->max_score;
                                    $lowest_score  = $parameter_minmax_score_result->min_score;
                                }

                                array_push($parameter_score,array(
                                    "parameter_id"         => $psr->parameter_id,
                                    "parameter_label_id"   => $psr->parameter_label_id,
                                    "parameter_name"       => $psr->parameter_name,
                                    "parameter_label_name" => $psr->parameter_label_name,
                                    "your_score"           => $your_score,
                                    "highest_score"        => $highest_score,
                                    "lowest_score"         => $lowest_score,
                                ));
                            } 
                            $data['parameter_score'] = $parameter_score;

                            // $this->load->library('Pdf_Library');
                            $htmlContent = $this->load->view('ai_reports/ai_pdf',$data,true);
							
                            // //DIVEYSH PANCHAL
                            ob_start();
                            define('K_TCPDF_EXTERNAL_CONFIG', true);
                            $this->load->library('Pdf');
                            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                            $data['pdf'] = $pdf;
                            $pdf->SetCreator(PDF_CREATOR);
                            $pdf->SetAuthor('Awarathon');
                            $pdf->SetTitle("Awarathon's Sales Readiness Reports");
                            $pdf->SetSubject("Awarathon's Sales Readiness Reports");
                            $pdf->SetKeywords('Awarathon');
                            $pdf->SetHeaderData('',0, '', '',array(255,255,255),array(255,255,255));
							$pdf->setHtmlHeader('<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #000000;">
									<tr>
										<td style="height:10px;width:60%">
											<div class="page-title">Sales Readiness Reports</div>
										</td>
										<td style="height:10px;width:40%;text-align:right;">
											<img style="text-align: top;width:90px;height:auto;margin:0px auto;" src="'.$data['company_logo'].'"/>
										</td>
									</tr>
								</table>');
                            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
                            $pdf->SetMargins(PDF_MARGIN_LEFT, 5, PDF_MARGIN_RIGHT);
                            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
							$pdf->SetAutoPageBreak(TRUE, 20);
                            //$pdf->SetAutoPageBreak(TRUE, 0);
                            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
                            $pdf->PrintCoverPageFooter = True;

                            $pdf->AddPage();
                            $pdf->setJPEGQuality(100);
                            $pdf->SetFont('helvetica', '', 10);
                            $pdf->writeHTML($htmlContent, true, false, true, false, '');
                            $pdf->lastPage();       
                            ob_end_clean();
                            
                            $now       = date('YmdHis');
							$file_name = 'C'.$company_id.'A'.$assessment_id.'U'.$user_id.'DTTM'.$now.'.pdf';
							$file_path = "/var/www/html/awarathon.com/ai/pdf_reports/".$file_name; 
							if($tdata->pdf_filename !=''){
								$old_file_name= '/var/www/html/awarathon.com/ai/pdf_reports/'.$tdata->pdf_filename;
								unlink($old_file_name);
							}
							//UPDATE PDF STATUS
							$pdf_updtstatus_result = $this->ai_reports_model->update_pdf_status($company_id,$assessment_id,$user_id,$file_name);
                            $pdf->Output($file_path, 'F'); 
                            $temp_file_path = $site_url.'/pdf_reports/'.$file_name;

                            
                        }
                    }
                }// AI PDF END

                if ($_report_type==2){ //MANUAL PDF START
                    
                    //CHECK ALL USERS HAS BEEN RATED FROM THE MANAGER
                    $aim_count_result = $this->ai_reports_model->get_user_rated_by_manager($assessment_id);
                    if (isset($aim_count_result) AND count((array)$aim_count_result)>0){
                        $ai_count     = $aim_count_result->ai_count;
                        $manual_count = $aim_count_result->manual_count;
                        if ((float)$ai_count == (float)$manual_count){

                            //GET COMPANY DETAILS
                            $company_name = '';
                            $company_logo = 'assets/images/Awarathon-Logo.png';
							$company_result = $this->common_model->get_value('company', 'company_name, company_logo', 'id="'.$company_id.'"');
							if (isset($company_result) AND count((array)$company_result)>0){
								$company_name = $company_result->company_name;
								$company_logo = !empty($company_result->company_logo) ? '/assets/uploads/company/'.$company_result->company_logo : '';
							}
							$data['company_name'] = $company_name;
							$data['company_logo'] = $company_logo;
                            
                            //GET PARTICIPANT DETAILS
                            $participant_name = '';
                            $participant_result = $this->common_model->get_value('device_users', '*', 'user_id="'.$user_id.'"');
                            if (isset($participant_result) AND count((array)$participant_result)>0){
                                $participant_name = $participant_result->firstname." ".$participant_result->lastname." - ".$user_id;
                            }
                            $data['participant_name'] = $participant_name;

                            //GET MANAGER NAME
                            $manager_id = '';
                            $manager_name = '';
                            $manager_result = $this->ai_reports_model->get_manager_name($assessment_id,$user_id);
                            if (isset($manager_result) AND count((array)$manager_result)>0){
                                $manager_id = $manager_result->manager_id;
                                $manager_name = $manager_result->manager_name;
                            }
                            $data['manager_name'] = $manager_name;

                            
                            //OVERALL SCORE
                            $overall_score = 0;
                            $your_rank = 0;
                            $overall_score_result = $this->ai_reports_model->get_manual_overall_score_rank($company_id,$assessment_id,$user_id);
                            if (isset($overall_score_result) AND count((array)$overall_score_result)>0){
                                $overall_score = $overall_score_result->overall_score;
                                $your_rank = $overall_score_result->final_rank;
                            }
                            $data['overall_score'] = $overall_score;
                            $data['your_rank'] = $your_rank;
                            
                            $rating = '';
                            if ((float)$overall_score >= 69.9){
                                $rating = 'A';
                            }else if ((float)$overall_score < 69.9 AND (float)$overall_score >= 63.23){
                                $rating = 'B';
                            }else if ((float)$overall_score < 63.23 AND (float)$overall_score >= 54.9){
                                $rating = 'C';
                            }else if ((float)$overall_score < 54.9){
                                $rating = 'D';
                            }
                            $data['rating'] = $rating;


                            //QUESTIONS LIST
                            $best_video_list = [];
                            $questions_list  = [];
                            $partd_list      = [];
                            $manager_comments_list = [];
                            $i = 0;
                            $question_result = $this->ai_reports_model->get_questions($company_id,$assessment_id);
                            foreach ($question_result as $qr){
                                $question_id     = $qr->question_id;
                                $question        = $qr->question;
                                $question_series = $qr->question_series;
                                $_trans_id       = $qr->trans_id;

                                $question_your_score_result      = $this->ai_reports_model->get_manual_question_your_score($company_id,$assessment_id,$user_id,$question_id);
                                $question_minmax_score_result    = $this->ai_reports_model->get_manual_question_minmax_score($company_id,$assessment_id,$question_id);
                                $question_your_video_result      = $this->ai_reports_model->get_your_video($company_id,$assessment_id,$user_id,$_trans_id,$question_id);
                                $question_best_video_result      = $this->ai_reports_model->get_manual_best_video($company_id,$assessment_id,$question_id);
                                $question_manager_comment_result = $this->ai_reports_model->get_manager_comments($assessment_id,$user_id,$question_id,$manager_id);

                                $your_vimeo_url  = "";
                                if (isset($question_your_video_result) AND count((array)$question_your_video_result)>0){
                                    $your_vimeo_url = $question_your_video_result->vimeo_url;
                                }

                                $best_vimeo_url  = "";
                                if (isset($question_best_video_result) AND count((array)$question_best_video_result)>0){
                                    $best_vimeo_url = $question_best_video_result->vimeo_url;
                                }

                                $your_score  = 0;
                                if (isset($question_your_score_result) AND count((array)$question_your_score_result)>0){
                                    $your_score = $question_your_score_result->score;
                                }
                                $highest_score  = 0;
                                $lowest_score  = 0;
                                if (isset($question_minmax_score_result) AND count((array)$question_minmax_score_result)>0){
                                    $highest_score = $question_minmax_score_result->max_score;
                                    $lowest_score  = $question_minmax_score_result->min_score;
                                }
                                $comments  = '';
                                if (isset($question_manager_comment_result) AND count((array)$question_manager_comment_result)>0){
                                    $comments  = $question_manager_comment_result->remarks;
                                }

                                array_push($best_video_list,array(
                                    "question_series" => $question_series,
                                    "your_vimeo_url"  => $your_vimeo_url,
                                    "best_vimeo_url"  => $best_vimeo_url,
                                ));
                                array_push($questions_list,array(
                                    "question_id"     => $question_id,
                                    "question"        => $question,
                                    "question_series" => $question_series,
                                    "your_score"      => $your_score,
                                    "highest_score"   => $highest_score,
                                    "lowest_score"    => $lowest_score,
                                ));
                                array_push($manager_comments_list,array(
                                    "question_id"     => $question_id,
                                    "question"        => $question,
                                    "question_series" => $question_series,
                                    "comments"        => $comments,
                                ));

                                $temp_partd_list = [];
                                $partd_list[$i]['question_series'] = $question_series;
                                $partd_list[$i]['question']        = $question;
                                $i++;
                            }
                            $data['best_video_list']       = $best_video_list;    
                            $data['questions_list']        = $questions_list;    
                            $data['manager_comments_list'] = $manager_comments_list;    
                            
                            //GET OVERALL COMMENTS
                            $overall_comments = '';
                            $overall_comments_result = $this->common_model->get_value('assessment_trainer_result', 'remarks', 'assessment_id="'.$assessment_id.'" and user_id="'.$user_id.'" and trainer_id="'.$manager_id.'"');
                            if (isset($overall_comments_result) AND count((array)$overall_comments_result)>0){
                                $overall_comments = $overall_comments_result->company_name;
                            }
                            $data['overall_comments'] = $overall_comments;

                            //PARAMETER LIST
                            $parameter_score = [];
                            $parameter_score_result = $this->ai_reports_model->get_parameters($company_id,$assessment_id);
                            foreach ($parameter_score_result as $psr){
                                $parameter_id                  = $psr->parameter_id;
                                $parameter_label_id            = $psr->parameter_label_id;
                                $parameter_your_score_result   = $this->ai_reports_model->get_manual_parameters_your_score($company_id,$assessment_id,$user_id,$parameter_id,$parameter_label_id);
                                $parameter_minmax_score_result = $this->ai_reports_model->get_manual_parameter_minmax_score($user_id,$assessment_id,$parameter_id,$parameter_label_id);
                                
                                $your_score  = 0;
                                if (isset($parameter_your_score_result) AND count((array)$parameter_your_score_result)>0){
                                    $your_score = $parameter_your_score_result->percentage;
                                }
                                $highest_score = 0;
                                $lowest_score  = 0;
                                if (isset($parameter_minmax_score_result) AND count((array)$parameter_minmax_score_result)>0){
                                    $highest_score = $parameter_minmax_score_result->max_score;
                                    $lowest_score  = $parameter_minmax_score_result->min_score;
                                }

                                array_push($parameter_score,array(
                                    "parameter_id"         => $psr->parameter_id,
                                    "parameter_label_id"   => $psr->parameter_label_id,
                                    "parameter_name"       => $psr->parameter_name,
                                    "parameter_label_name" => $psr->parameter_label_name,
                                    "your_score"           => $your_score,
                                    "highest_score"        => $highest_score,
                                    "lowest_score"         => $lowest_score,
                                ));
                            } 
                            $data['parameter_score'] = $parameter_score;


                            // $this->load->library('Pdf_Library');
                            $htmlContent = $this->load->view('ai_reports/manual_pdf',$data,true);
        
                            // //DIVEYSH PANCHAL
                            ob_start();
                            define('K_TCPDF_EXTERNAL_CONFIG', true);
                            $this->load->library('Pdf');
                            //$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                            //Below line is added
                            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                            $data['pdf'] = $pdf;
                            $pdf->SetCreator(PDF_CREATOR);
                            $pdf->SetAuthor('Awarathon');
                            $pdf->SetTitle("Awarathon's Sales Readiness Reports");
                            $pdf->SetSubject("Awarathon's Sales Readiness Reports");
                            $pdf->SetKeywords('Awarathon');
                            $pdf->SetHeaderData('',0, '', '',array(255,255,255),array(255,255,255));
							$pdf->setHtmlHeader('<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #000000;">
									<tr>
										<td style="height:10px;width:60%">
											<div class="page-title">Sales Readiness Reports</div>
										</td>
										<td style="height:10px;width:40%;text-align:right;">
											<img style="text-align: top;width:90px;height:auto;margin:0px auto;" src="'.$data['company_logo'].'"/>
										</td>
									</tr>
								</table>');
                            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
                            $pdf->SetMargins(PDF_MARGIN_LEFT, 5, PDF_MARGIN_RIGHT);
                            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
                            //$pdf->SetAutoPageBreak(TRUE, 0);
							$pdf->SetAutoPageBreak(TRUE, 20);
                            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
                            //Added below line: As we don't want footer on front page
                            $pdf->PrintCoverPageFooter = True;

                            $pdf->AddPage();
                            $pdf->setJPEGQuality(100);
                            $pdf->SetFont('helvetica', '', 10);
                            
                            $pdf->writeHTML($htmlContent, true, false, true, false, '');
                            $pdf->lastPage();       
                            ob_end_clean();
                            
                            $now       = date('YmdHis');
                            $file_name = 'MANU-C'.$company_id.'A'.$assessment_id.'U'.$user_id.'DTTM'.$now.'.pdf';
                            $file_path = "/var/www/html/awarathon.com/ai/pdf_reports/".$file_name; 
                        
                            $pdf->Output($file_path, 'F'); 
                            $temp_file_path = $site_url.'/pdf_reports/'.$file_name;

                            //UPDATE PDF STATUS
                            $pdf_updtstatus_result = $this->ai_reports_model->update_manual_pdf_status($company_id,$assessment_id,$user_id,$file_name);
                        }
                    }                        
                }// MANUAL PDF ENDS

                if ($_report_type==3){ //COMBINE (AI + MANUAL) PDF START
                    //CHECK ALL USERS HAS BEEN RATED FROM THE MANAGER
                    $aim_count_result = $this->ai_reports_model->get_user_rated_by_manager($assessment_id);
                    if (isset($aim_count_result) AND count((array)$aim_count_result)>0){
                        $ai_count     = $aim_count_result->ai_count;
                        $manual_count = $aim_count_result->manual_count;
                        if ((float)$ai_count == (float)$manual_count){

                            //GET COMPANY DETAILS
                            $company_name = '';
                            $company_logo = 'assets/images/Awarathon-Logo.png';
							$company_result = $this->common_model->get_value('company', 'company_name, company_logo', 'id="'.$company_id.'"');
							if (isset($company_result) AND count((array)$company_result)>0){
								$company_name = $company_result->company_name;
								$company_logo = !empty($company_result->company_logo) ? '/assets/uploads/company/'.$company_result->company_logo : '';
							}
							$data['company_name'] = $company_name;
							$data['company_logo'] = $company_logo;
                            
                            //GET PARTICIPANT DETAILS
                            $participant_name = '';
                            $participant_result = $this->common_model->get_value('device_users', '*', 'user_id="'.$user_id.'"');
                            if (isset($participant_result) AND count((array)$participant_result)>0){
                                $participant_name = $participant_result->firstname." ".$participant_result->lastname." - ".$user_id;
                            }
                            $data['participant_name'] = $participant_name;
                            
                            //GET MANAGER NAME
                            $manager_id = '';
                            $manager_name = '';
                            $manager_result = $this->ai_reports_model->get_manager_name($assessment_id,$user_id);
                            if (isset($manager_result) AND count((array)$manager_result)>0){
                                $manager_id = $manager_result->manager_id;
                                $manager_name = $manager_result->manager_name;
                            }
                            $data['manager_name'] = $manager_name;
                            
                            //OVERALL SCORE
                            $overall_score = 0;
                            $overall_score_result = $this->ai_reports_model->get_user_overall_score_combined($company_id,$assessment_id,$user_id);
                            if (isset($overall_score_result) AND count((array)$overall_score_result)>0){
                                $overall_score = $overall_score_result->overall_score;
                            }
                            $data['overall_score'] = $overall_score;
                            

                            //QUESTIONS LIST
                            $questions_list  = [];
                            $manager_comments_list = [];
                            $question_result = $this->ai_reports_model->get_questions($company_id,$assessment_id);
                            foreach ($question_result as $qr){
                                $question_id     = $qr->question_id;
                                $question        = $qr->question;
                                $question_series = $qr->question_series;

                                $question_ai_score_result   = $this->ai_reports_model->get_question_your_score($company_id,$assessment_id,$user_id,$question_id);
                                $question_manual_score_result = $this->ai_reports_model->get_question_manual_score($assessment_id,$user_id,$question_id);
                                $question_manager_comment_result = $this->ai_reports_model->get_manager_comments($assessment_id,$user_id,$question_id,$manager_id);
                                
                                $ai_score  = 0;
                                if (isset($question_ai_score_result) AND count((array)$question_ai_score_result)>0){
                                    $ai_score = $question_ai_score_result->score;
                                }
                                $manual_score  = 0;
                                if (isset($question_manual_score_result) AND count((array)$question_manual_score_result)>0){
                                    $manual_score  = $question_manual_score_result->score;
                                }
                                $comments  = '';
                                if (isset($question_manager_comment_result) AND count((array)$question_manager_comment_result)>0){
                                    $comments  = $question_manager_comment_result->remarks;
                                }
                                if($manual_score==0 || $ai_score==0)
                                {
                                    $combined_score = number_format((($ai_score + $manual_score)),2);
                                }
                                else
                                {
                                    $combined_score = number_format((($ai_score + $manual_score)/2),2);
                                }
                                array_push($questions_list,array(
                                    "question_id"     => $question_id,
                                    "question"        => $question,
                                    "question_series" => $question_series,
                                    "ai_score"        => $ai_score,
                                    "manual_score"    => $manual_score,
                                    "combined_score"  => $combined_score,
                                ));

                                array_push($manager_comments_list,array(
                                    "question_id"     => $question_id,
                                    "question"        => $question,
                                    "question_series" => $question_series,
                                    "comments"        => $comments,
                                ));

                            }
                            $data['questions_list']  = $questions_list;    
                            $data['manager_comments_list']  = $manager_comments_list;    
                        

                            //GET OVERALL COMMENTS
                            $overall_comments = '';
                            $overall_comments_result = $this->common_model->get_value('assessment_trainer_result', 'remarks', 'assessment_id="'.$assessment_id.'" and user_id="'.$user_id.'" and trainer_id="'.$manager_id.'"');
                            if (isset($overall_comments_result) AND count((array)$overall_comments_result)>0){
                                $overall_comments = $overall_comments_result->company_name;
                            }
                            $data['overall_comments'] = $overall_comments;

                            //PARAMETER LIST
                            $parameter_score = [];
                            $parameter_score_result = $this->ai_reports_model->get_parameters($company_id,$assessment_id);
                            foreach ($parameter_score_result as $psr){
                                $parameter_id                  = $psr->parameter_id;
                                $parameter_label_id            = $psr->parameter_label_id;
                                $parameter_your_score_result   = $this->ai_reports_model->get_parameters_your_score($company_id,$assessment_id,$user_id,$parameter_id,$parameter_label_id);
                                $parameter_manual_score_result = $this->ai_reports_model->get_parameter_manual_score($assessment_id,$user_id,$parameter_id,$parameter_label_id);
                                
                                $your_score  = 0;
                                if (isset($parameter_your_score_result) AND count((array)$parameter_your_score_result)>0){
                                    $your_score = $parameter_your_score_result->score;
                                }
                                $manual_score  = 0;
                                if (isset($parameter_manual_score_result) AND count((array)$parameter_manual_score_result)>0){
                                    $manual_score = $parameter_manual_score_result->percentage;
                                }
                                if($manual_score == 0 ||$your_score==0)
                                {
                                    $combined_score = number_format((($your_score + $manual_score)),2);
                                }
                                else
                                {
                                    $combined_score = number_format((($your_score + $manual_score)/2),2);
                                }
                                array_push($parameter_score,array(
                                    "parameter_id"         => $psr->parameter_id,
                                    "parameter_label_id"   => $psr->parameter_label_id,
                                    "parameter_name"       => $psr->parameter_name,
                                    "parameter_label_name" => $psr->parameter_label_name,
                                    "your_score"           => $your_score,
                                    "manual_score"        => $manual_score,
                                    "combined_score"        => $combined_score,
                                ));
                            } 
                            $data['parameter_score'] = $parameter_score;

                            // $this->load->library('Pdf_Library');
                            $htmlContent = $this->load->view('ai_reports/combined_pdf',$data,true);

                            // //DIVEYSH PANCHAL
                            ob_start();
                            define('K_TCPDF_EXTERNAL_CONFIG', true);
                            $this->load->library('Pdf');
                            //  $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
							$pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                            $data['pdf'] = $pdf;
                            $pdf->SetCreator(PDF_CREATOR);
                            $pdf->SetAuthor('Awarathon');
                            $pdf->SetTitle("Awarathon's Sales Readiness Reports");
                            $pdf->SetSubject("Awarathon's Sales Readiness Reports");
                            $pdf->SetKeywords('Awarathon');
                            $pdf->SetHeaderData('',0, '', '',array(255,255,255),array(255,255,255));
							$pdf->setHtmlHeader('<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #000000;">
										<tr>
											<td style="height:10px;width:60%">
												<div class="page-title">Sales Readiness Reports</div>
											</td>
											<td style="height:10px;width:40%;text-align:right;">
												<img style="text-align: top;width:90px;height:auto;margin:0px auto;" src="'.$data['company_logo'].'"/>
											</td>
										</tr>
									</table>');
                            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
                            $pdf->SetMargins(PDF_MARGIN_LEFT, 5, PDF_MARGIN_RIGHT);
                            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
                            //$pdf->SetAutoPageBreak(TRUE, 0);
							$pdf->SetAutoPageBreak(TRUE, 20);
                            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
                            $pdf->PrintCoverPageFooter = True;

                            $pdf->AddPage();
                            $pdf->setJPEGQuality(100);
                            $pdf->SetFont('helvetica', '', 10);
                            $pdf->writeHTML($htmlContent, true, false, true, false, '');
                            $pdf->lastPage();       
                            ob_end_clean();
                            
                            $now       = date('YmdHis');
                            $file_name = 'COMB-C'.$company_id.'A'.$assessment_id.'U'.$user_id.'DTTM'.$now.'.pdf';
                            $file_path = "/var/www/html/awarathon.com/ai/pdf_reports/".$file_name; 
                        
                            $pdf->Output($file_path, 'F'); 
                            $temp_file_path = $site_url.'/pdf_reports/'.$file_name;

                            //UPDATE PDF STATUS
                            $pdf_updtstatus_result = $this->ai_reports_model->update_combined_pdf_status($company_id,$assessment_id,$user_id,$file_name);
                                
                            
                        }
                    }
                }//COMBINE (AI + MANUAL) PDF ENDS

                
           }
        }
    }
}