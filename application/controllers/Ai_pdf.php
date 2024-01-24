<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Ai_pdf extends CI_Controller {
    function __construct() {
		parent::__construct();
        $this->load->model('common_model');
        $this->load->model('ai_process_model');
        $this->load->model('ai_reports_model');
    }
    public function index() {
        echo "Welcome";
        exit;
    }
    public function ai($_company_id,$_assessment_id,$enc_user_id){
        //KRISHNA -- TRINITY assessment AI report level change
		$_user_id = base64_decode($enc_user_id, true);
        if ($_company_id=="" OR $_assessment_id=="" OR $_user_id==""){
            echo "Invalid parameter passed";
        }else{
            //GET COMPANY DETAILS
            $company_name = '';
			$company_logo = 'assets/images/Awarathon-Logo.png';
            $company_result = $this->common_model->get_value('company', 'company_name, company_logo', 'id="'.$_company_id.'"');
            if (isset($company_result) AND count((array)$company_result)>0){
                $company_name = $company_result->company_name;
                // $company_logo = !empty($company_result->company_logo) ? '/assets/uploads/company/'.$company_result->company_logo : '';
            }
            $data['company_name'] = $company_name;
			$data['company_logo'] = $company_logo;

            //spotlight change -----
            $assessment_type = '';
            $assessment_result = $this->common_model->get_value('assessment_mst', 'assessment_type', 'id="' . $_assessment_id . '"');
            if (isset($assessment_result) and count((array)$assessment_result) > 0) {
                $assessment_type = $assessment_result->assessment_type;
            }
            $data['assessment_type'] = $assessment_type;
            //spotlight change -----

            //GET PARTICIPANT DETAILS
            $participant_name = '';
            $participant_result = $this->common_model->get_value('device_users', '*', 'user_id="'.$_user_id.'"');
            if (isset($participant_result) AND count((array)$participant_result)>0){
                $participant_name = $participant_result->firstname." ".$participant_result->lastname." - ".$_user_id;
            }
            $data['participant_name'] = $participant_name;
            $data['attempt'] ='';
            $attempt_data = $this->ai_reports_model->assessment_attempts_data($_assessment_id,$_user_id);
            if(count((array)$attempt_data) > 0){
                $data['attempt'] = $attempt_data->attempts.'/'.$attempt_data->total_attempts;
            }
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
            
            // Industry thresholds - 04-04-2023
            $this->db->select('company_id,range_from,range_to,title,rating');
            $this->db->from('industry_threshold_range');
            $this->db->order_by('rating', 'asc');
            $data['color_range'] = $this->db->get()->result();
            // end 04-04-2023
            $rating = '';
            // if ((float)$overall_score >= 69.9){
            //     $rating = 'A';
            // }else if ((float)$overall_score < 69.9 AND (float)$overall_score >= 63.23){
            //     $rating = 'B';
            // }else if ((float)$overall_score < 63.23 AND (float)$overall_score >= 54.9){
            //     $rating = 'C';
            // }else if ((float)$overall_score < 54.9){
            //     $rating = 'D';
            // }
            if ((float)$overall_score < $data['color_range'][0]->range_to . '.99' and (float)$overall_score >= $data['color_range'][0]->range_from) {
                $rating = $data['color_range'][0]->rating;
            } else if ((float)$overall_score < $data['color_range'][1]->range_to . '.99' and (float)$overall_score >= $data['color_range'][1]->range_from) {
                $rating = $data['color_range'][1]->rating;
            } else if ((float)$overall_score < $data['color_range'][2]->range_to . '.99' and (float)$overall_score >= $data['color_range'][2]->range_from) {
                $rating = $data['color_range'][2]->rating;
            } else if ((float)$overall_score < $data['color_range'][3]->range_to . '.99' and (float)$overall_score >= $data['color_range'][3]->range_from) {
                $rating = $data['color_range'][3]->rating;
            } else if ((float)$overall_score < $data['color_range'][4]->range_to . '.99' and (float)$overall_score >= $data['color_range'][4]->range_from) {
                $rating = $data['color_range'][4]->rating;
            } else if ((float)$overall_score < $data['color_range'][5]->range_to . '.99' and (float)$overall_score >= $data['color_range'][5]->range_from) {
                $rating = $data['color_range'][5]->rating;
            } else {
                $rating = '-';
            }
            $data['rating'] = $rating;


            //QUESTIONS LIST
            $best_video_list = [];
            $questions_list  = [];
            $partd_list      = [];
            $i = 0;
            $question_result = $this->ai_process_model->get_questions($_company_id,$_assessment_id,$_user_id); //Spotlight assessment
            // $question_result = $this->ai_reports_model->get_questions($_company_id,$_assessment_id);
            $question_minmax_score_result = $this->ai_process_model->get_question_minmax_score($_company_id,$_assessment_id);
            $question_minmax_score_result_temp = [];
            if(!empty($question_minmax_score_result)){
                foreach($question_minmax_score_result as $que){
                    $question_minmax_score_result_temp[$que->question_id] = [
                        'max_score' => $que->max_score, 
                        'min_score' => $que->min_score
                    ];
                }
            }
            foreach ($question_result as $qr){
                $question_id     = $qr->question_id;
                $question        = $qr->question;
                $question_series = $qr->question_series;
                $_trans_id       = $qr->trans_id;
                $question_your_score_result   = $this->ai_process_model->get_question_your_score($_company_id,$_assessment_id,$_user_id,$question_id);
                // $question_minmax_score_result = $this->ai_reports_model->get_question_minmax_score($_company_id,$_assessment_id,$question_id);
                
                // $question_your_video_result   = $this->ai_process_model->get_your_video($_company_id,$_assessment_id,$_user_id,$_trans_id,$question_id);
                $this->db->select(" CONCAT('https://player.vimeo.com/video/',vimeo_uri) as vimeo_url ");
                if($assessment_type == 3){
                    //Trinity assessment video
                    $this->db->from('trinity_results');
                    $where = ' company_id ="' . $_company_id . '" AND assessment_id ="' . $_assessment_id . '" AND user_id ="' . $_user_id . '"';
                }else{
                    $this->db->from('assessment_results');
                    $where = ' company_id ="' . $_company_id . '" AND assessment_id ="' . $_assessment_id . '" AND user_id ="' . $_user_id . '" AND trans_id ="' . $_trans_id . '" AND question_id ="' . $question_id . '" ';
                }
                $this->db->where($where);
                $question_your_video_result = $this->db->get()->row();
                $question_best_video_result   = $this->ai_process_model->get_best_video($_company_id,$_assessment_id,$question_id);

                // $ai_sentkey_score_result      = $this->common_model->get_selected_values('ai_sentkey_score', '*', 'company_id="'.$_company_id.'" AND assessment_id="'.$_assessment_id.'" AND user_id="'.$_user_id.'" AND trans_id="'.$_trans_id.'" AND question_id="'.$question_id.'"');
                $this->db->select('*');
                $this->db->from('ai_sentkey_score');
                $where = 'company_id="' . $_company_id . '" AND assessment_id="' . $_assessment_id . '" AND user_id="' . $_user_id . '" AND question_id="' . $question_id . '" ';
                if($assessment_type !== 3){
                    $where .= ' AND trans_id="' . $_trans_id . '"';
                }
                $this->db->where($where);
                $ai_sentkey_score_result =  $this->db->get()->result();

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
                $highest_score       = 0;
                $lowest_score        = 0;
                $failed_counter_your = 0;
                // $failed_counter_max  = 0;
                // $failed_counter_min  = 0;
                if (isset($question_minmax_score_result_temp) AND count((array)$question_minmax_score_result_temp)>0){
                    $highest_score = $question_minmax_score_result_temp[$question_id]['max_score'];
                    $lowest_score  = $question_minmax_score_result_temp[$question_id]['min_score'];
                    // $highest_score = $question_minmax_score_result->max_score;
                    // $lowest_score  = $question_minmax_score_result->min_score;

                    // $user_max_score_result        = $this->ai_reports_model->get_user_from_minmax_score($_company_id,$_assessment_id,$question_id,$highest_score);
                    // if (isset($user_max_score_result) AND count((array)$user_max_score_result)>0){
                    //     $failed_counter_max = $user_max_score_result->failed_counter;
                    // }
                    // $user_min_score_result        = $this->ai_reports_model->get_user_from_minmax_score($_company_id,$_assessment_id,$question_id,$lowest_score);
                    // if (isset($user_max_score_result) AND count((array)$user_max_score_result)>0){
                    //     $failed_counter_min = $user_max_score_result->failed_counter;
                    // }
                }
                $ai_failed_result = $this->common_model->get_value('ai_schedule', '*', 'assessment_id="'.$_assessment_id.'" AND user_id="'.$_user_id.'" AND question_id="'.$question_id.'"');
                if (isset($ai_failed_result) AND count((array)$ai_failed_result)>0){
                    $failed_counter_your = $ai_failed_result->failed_counter;
                }
                $question_reference_video_result = $this->common_model->get_value('assessment_ref_videos', '*', 'assessment_id="' . $_assessment_id . '" AND question_id = "' . $question_id . '"');
                $refe_video_url = "";
                if (isset($question_reference_video_result) and count((array) $question_reference_video_result) > 0) {
                    $refe_video_url = $question_reference_video_result->video_url;
                }
                //KRISHNA -- TRINITY -- SHOW SINGLE VIDEO in PART A
                if($assessment_type == 3){
                    $best_video_list[0] = array(
                        "question_series" => 'Q',
                        "your_vimeo_url" => $your_vimeo_url,
                        "best_vimeo_url" => $best_vimeo_url,
                        "refe_video_url" => $refe_video_url,
                    );
                }else{
                    array_push($best_video_list,array(
                        "question_series" => $question_series,
                        "your_vimeo_url"  => $your_vimeo_url,
                        "best_vimeo_url"  => $best_vimeo_url,
                        "refe_video_url"  => $refe_video_url,
                    ));
                }
                array_push($questions_list,array(
                    "question_id"         => $question_id,
                    "question"            => $question,
                    "question_series"     => $question_series,
                    "your_score"          => $your_score,
                    "highest_score"       => $highest_score,
                    "lowest_score"        => $lowest_score,
                    "failed_counter_your" => $failed_counter_your,
                    // "failed_counter_max"  => $failed_counter_max,
                    // "failed_counter_min"  => $failed_counter_min
                ));
                
                $temp_partd_list = [];
                $partd_list[$i]['question_series'] = $question_series;
                $partd_list[$i]['question']        = $question;
                
                if (isset($ai_sentkey_score_result) AND count($ai_sentkey_score_result)>0){
                    foreach($ai_sentkey_score_result as $sksr){
                        
                        // $sentkey_type_result = $this->common_model->get_value('assessment_trans_sparam', '*', 'type_id!=0 AND assessment_id="'.$_assessment_id.'" AND question_id="'.$question_id.'"');
                        $this->db->select('*');
                        if($assessment_type == 3){
                            $this->db->from('trinity_trans_sparam');
                        }else{
                            $this->db->from('assessment_trans_sparam');
                        }
                        $where = 'type_id!=0 AND assessment_id="' . $_assessment_id . '" AND question_id="' . $question_id . '" ';
                        $this->db->where($where);
                        $sentkey_type_result =  $this->db->get()->row();
                        $tick_icons = '';
                        if (isset($sentkey_type_result) AND count((array)$sentkey_type_result)>0){
                            $que_lang = $sentkey_type_result->language_id;
                            // Set different range for English and other languages sentence/keyword
                            $gcolor_score = ($que_lang == 1) ? 60 : 50;
                            $ycolor_high_score = ($que_lang == 1) ? 60 : 50;
                            $ycolor_low_score = ($que_lang == 1) ? 50 : 40;
                            $rcolor_score = ($que_lang == 1) ? 50 : 40;
                            if ($sentkey_type_result->type_id==1){ //Sentance 
                                if ($sksr->score >= $gcolor_score){
                                    $tick_icons = 'green';
                                }
                                if ($sksr->score <= $rcolor_score){
                                    $tick_icons = 'red';
                                }
                                if ($sksr->score > $ycolor_low_score AND $sksr->score < $ycolor_high_score){
                                    $tick_icons = 'yellow';
                                }
                            }
                            if ($sentkey_type_result->type_id==2){ //Keyword
                                if ($sksr->score >= $gcolor_score){
                                    $tick_icons = 'green';
                                }
                                if ($sksr->score < $gcolor_score){
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
            // $parameter_score_result = $this->ai_process_model->get_parameters($_company_id,$_assessment_id);
            $parameter_lebel_table = 'parameter_label_mst';
            if($assessment_type == 3){
                $parameter_lebel_table = 'goal_mst';
            }
            $this->db->distinct('ps.parameter_id');
            $this->db->select('ps.parameter_id,ps.parameter_label_id,p.description as parameter_name,pl.description as parameter_label_name');
            $this->db->from('ai_subparameter_score as ps');
            $this->db->join('parameter_mst as p', 'ps.parameter_id = p.id', 'left');
            $this->db->join(''.$parameter_lebel_table.' as pl', 'ps.parameter_label_id = pl.id AND ps.parameter_id = pl.parameter_id', 'left');
            $where = 'ps.parameter_type ="parameter" AND ps.company_id ="' . $_company_id . '" AND ps.assessment_id ="' . $_assessment_id . '" AND ps.parameter_label_id != 0';
            $this->db->where($where);
            $this->db->order_by('ps.parameter_id,ps.parameter_label_id');
            $parameter_score_result = $this->db->get()->result();

            $assessment_trans_table = 'assessment_trans_sparam';
            if($assessment_type == 3){
                $assessment_trans_table = 'trinity_trans_sparam';
            }
            foreach ($parameter_score_result as $psr){
                $parameter_id                  = $psr->parameter_id;
                $parameter_label_id            = $psr->parameter_label_id;
                $parameter_your_score_result   = $this->ai_reports_model->get_parameters_your_score($_company_id,$_assessment_id,$_user_id,$parameter_id,$parameter_label_id);
                $parameter_minmax_score_result = $this->ai_process_model->get_parameter_minmax_score($_company_id,$_assessment_id,$parameter_id,$parameter_label_id);
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
            $data['reference_video_right'] = 0;
            $data['best_video_right'] = 0;
            $rights_rrports_video = $this->common_model->get_value('assessment_mst', 'show_reports,show_best_video', 'id="' . $_assessment_id . '"');
            if (isset($rights_rrports_video) and count((array) $rights_rrports_video) > 0) {
                $data['reference_video_right'] = ($rights_rrports_video->show_reports) ? $rights_rrports_video->show_reports : 0;
                $data['best_video_right'] = ($rights_rrports_video->show_best_video) ? $rights_rrports_video->show_best_video : 0;
            }

            $htmlContent = $this->load->view('ai_process/ai_pdf',$data,true);
            
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
    public function manual($_company_id,$_assessment_id,$enc_user_id){
		$_user_id = base64_decode($enc_user_id, true);
        if ($_company_id=="" OR $_assessment_id=="" OR $_user_id==""){
            echo "Invalid parameter passed";
        }else{
            //GET COMPANY DETAILS
            $company_name = '';
			$company_logo = 'assets/images/Awarathon-Logo.png';
            $company_result = $this->common_model->get_value('company', 'company_name, company_logo', 'id="'.$_company_id.'"');
            if (isset($company_result) AND count((array)$company_result)>0){
                $company_name = $company_result->company_name;
                // $company_logo = !empty($company_result->company_logo) ? '/assets/uploads/company/'.$company_result->company_logo : '';
            }
            $data['company_name'] = $company_name;
			$data['company_logo'] = $company_logo;

            //spotlight change -----
            $assessment_type = '';
            $assessment_result = $this->common_model->get_value('assessment_mst', 'assessment_type', 'id="' . $_assessment_id . '"');
            if (isset($assessment_result) and count((array)$assessment_result) > 0) {
                $assessment_type = $assessment_result->assessment_type;
            }
            $data['assessment_type'] = $assessment_type;
            //spotlight change -----
            
            //GET PARTICIPANT DETAILS
            $participant_name = '';
            $participant_result = $this->common_model->get_value('device_users', '*', 'user_id="'.$_user_id.'"');
            if (isset($participant_result) AND count((array)$participant_result)>0){
                $participant_name = $participant_result->firstname." ".$participant_result->lastname." - ".$_user_id;
            }
            $data['participant_name'] = $participant_name;
            $data['attempt'] ='';
            $attempt_data = $this->ai_reports_model->assessment_attempts_data($_assessment_id,$_user_id);
            if(count((array)$attempt_data) > 0){
                $data['attempt'] = $attempt_data->attempts.'/'.$attempt_data->total_attempts;
            }
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
			
            // Industry thresholds - 04-04-2023
            $this->db->select('company_id,range_from,range_to,title,rating');
            $this->db->from('industry_threshold_range');
            $this->db->order_by('rating', 'asc');
            $data['color_range'] = $this->db->get()->result();
            // end 04-04-2023 

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
                // if ((float)$overall_score >= 69.9){
                //     $rating = 'A';
                // }else if ((float)$overall_score < 69.9 AND (float)$overall_score >= 63.23){
                //     $rating = 'B';
                // }else if ((float)$overall_score < 63.23 AND (float)$overall_score >= 54.9){
                //     $rating = 'C';
                // }else if ((float)$overall_score < 54.9){
                //     $rating = 'D';
                // }
                if ((float)$overall_score < $data['color_range'][0]->range_to and (float)$overall_score >= $data['color_range'][0]->range_from) {
                    $rating = $data['color_range'][0]->rating;
                } else if ((float)$overall_score < $data['color_range'][1]->range_to . '.99' and (float)$overall_score >= $data['color_range'][1]->range_from) {
                    $rating = $data['color_range'][1]->rating;
                } else if ((float)$overall_score < $data['color_range'][2]->range_to . '.99' and (float)$overall_score >= $data['color_range'][2]->range_from) {
                    $rating = $data['color_range'][2]->rating;
                } else if ((float)$overall_score < $data['color_range'][3]->range_to . '.99' and (float)$overall_score >= $data['color_range'][3]->range_from) {
                    $rating = $data['color_range'][3]->rating;
                } else if ((float)$overall_score < $data['color_range'][4]->range_to . '.99' and (float)$overall_score >= $data['color_range'][4]->range_from) {
                    $rating = $data['color_range'][4]->rating;
                } else if ((float)$overall_score < $data['color_range'][5]->range_to . '.99' and (float)$overall_score >= $data['color_range'][5]->range_from) {
                    $rating = $data['color_range'][5]->rating;
                } else {
                    $rating = '-';
                }
                $data['rating'] = $rating;
            }

            //QUESTIONS LIST
            $best_video_list = [];
            $questions_list  = [];
            $partd_list      = [];
            $manager_comments_list = [];
            $i = 0;
            $question_result = $this->ai_reports_model->get_questions($_company_id,$_assessment_id,$_user_id); //Spotlight assessment
            // $question_result = $this->ai_reports_model->get_questions($_company_id,$_assessment_id);
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
                $question_reference_video_result = $this->common_model->get_value('assessment_ref_videos', '*', 'assessment_id="' . $_assessment_id . '" AND question_id = "' . $question_id . '"');

                $your_vimeo_url  = "";
                if (isset($question_your_video_result) AND count((array)$question_your_video_result)>0){
                    $your_vimeo_url = $question_your_video_result->vimeo_url;
                }
                $refe_video_url = "";
                if (isset($question_reference_video_result) and count((array) $question_reference_video_result) > 0) {
                    $refe_video_url = $question_reference_video_result->video_url;
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
                    "refe_video_url" => $refe_video_url,
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
            $parameter_manual_score_result = $this->ai_reports_model->get_manual_parameters_score($_company_id,$_assessment_id,$_user_id);
            $parameter_manual_score_result = json_decode(json_encode($parameter_manual_score_result), true);
            $parameter_score = [];
            if(!empty($parameter_manual_score_result)){
                foreach($parameter_manual_score_result as $p_result){
                    $your_score  = 0;
                    if (isset($p_result['percentage'])){
                        $your_score = number_format($p_result['percentage'],2,'.','').'%';
                    }else{
                        $your_score = 'Not assessed';
                    }
                    $parameter_score[] = [
                        'parameter_id' => $p_result['parameter_id'],
                        'parameter_label_id' => $p_result['parameter_label_id'],
                        'parameter_name' => $p_result['parameter_name'],
                        'parameter_label_name' => $p_result['parameter_label_name'],
                        'your_score' => $your_score,
                    ];
                }
            }
            // $parameter_score_result = $this->ai_reports_model->get_parameters($_company_id,$_assessment_id);
            // foreach ($parameter_score_result as $psr){
            //     $parameter_id                  = $psr->parameter_id;
            //     $parameter_label_id            = $psr->parameter_label_id;
            //     $parameter_your_score_result   = $this->ai_reports_model->get_manual_parameters_your_score($_company_id,$_assessment_id,$_user_id,$parameter_id,$parameter_label_id);
            //     $parameter_minmax_score_result = $this->ai_reports_model->get_manual_parameter_minmax_score($_user_id,$_assessment_id,$parameter_id,$parameter_label_id);

            //     $your_score  = 0;
            //     if (isset($parameter_your_score_result) AND count((array)$parameter_your_score_result)>0 AND !empty($parameter_your_score_result->percentage)){
            //         $your_score = number_format($parameter_your_score_result->percentage,2,'.','').'%';
            //     }else{
			// 		$your_score = 'Not assessed';
			// 	}
            //     $highest_score = 0;
            //     $lowest_score  = 0;
            //     if (isset($parameter_minmax_score_result) AND count((array)$parameter_minmax_score_result)>0){
            //         $highest_score = $parameter_minmax_score_result->max_score;
            //         $lowest_score  = $parameter_minmax_score_result->min_score;
            //     }

            //     array_push($parameter_score,array(
            //         "parameter_id"         => $psr->parameter_id,
            //         "parameter_label_id"   => $psr->parameter_label_id,
            //         "parameter_name"       => $psr->parameter_name,
            //         "parameter_label_name" => $psr->parameter_label_name,
            //         "your_score"           => $your_score,
            //         "highest_score"        => $highest_score,
            //         "lowest_score"         => $lowest_score,
            //     ));
            // } 
            $data['parameter_score'] = $parameter_score;

            $data['reference_video_right'] = 0;
            $data['best_video_right'] = 0;
            $rights_rrports_video = $this->common_model->get_value('assessment_mst', 'show_reports,show_best_video', 'id="' . $_assessment_id . '"');
            if (isset($rights_rrports_video) and count((array) $rights_rrports_video) > 0) {
                $data['reference_video_right'] = ($rights_rrports_video->show_reports) ? $rights_rrports_video->show_reports : 0;
                $data['best_video_right'] = ($rights_rrports_video->show_best_video) ? $rights_rrports_video->show_best_video : 0;
            }
            // $this->load->library('Pdf_Library');
            $htmlContent = $this->load->view('ai_process/manual_pdf',$data,true);

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
    public function combine($_company_id,$_assessment_id,$enc_user_id){
		$_user_id = base64_decode($enc_user_id, true);
        if ($_company_id=="" OR $_assessment_id=="" OR $_user_id==""){
            echo "Invalid parameter passed";
        }else{
            //GET COMPANY DETAILS
            $company_name = '';
			$company_logo = 'assets/images/Awarathon-Logo.png';
            $company_result = $this->common_model->get_value('company', 'company_name, company_logo', 'id="'.$_company_id.'"');
            if (isset($company_result) AND count((array)$company_result)>0){
                $company_name = $company_result->company_name;
                // $company_logo = !empty($company_result->company_logo) ? '/assets/uploads/company/'.$company_result->company_logo : '';
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
            $data['attempt'] ='';
            $attempt_data = $this->ai_reports_model->assessment_attempts_data($_assessment_id,$_user_id);
            if(count((array)$attempt_data) > 0){
                $data['attempt'] = $attempt_data->attempts.'/'.$attempt_data->total_attempts;
            }
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
            $question_result = $this->ai_reports_model->get_questions($_company_id,$_assessment_id,$_user_id); //Spotlight assessment
            // $question_result = $this->ai_reports_model->get_questions($_company_id,$_assessment_id);
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
            $parameter_combined_score = $this->ai_reports_model->get_combined_parameters_your_score($_company_id,$_assessment_id,$_user_id);
            if(!empty($parameter_combined_score)){
                foreach($parameter_combined_score as $pcs){
                    $ai_score  = 0;
                    if (isset($pcs->score)){
                        $ai_score = $pcs->score;
                    }
                    $manual_score  = 0;
                    if (isset($pcs->percentage)){
                        $manual_score = $pcs->percentage;
                    }
                    if($manual_score==0 || $ai_score==0){
                        $combined_score = number_format((($ai_score + $manual_score)),2);    
                    }else{
                        $combined_score = number_format((($ai_score + $manual_score)/2),2);
                    }

                    array_push($parameter_score,array(
                        "parameter_id"         => $pcs->parameter_id,
                        "parameter_label_id"   => $pcs->parameter_label_id,
                        "parameter_name"       => $pcs->parameter_name,
                        "parameter_label_name" => $pcs->parameter_label_name,
                        "your_score"           => $ai_score,
                        "manual_score"        => empty($manual_score) ? 'Not assessed' : number_format($manual_score,2,'.','').'%',
                        "combined_score"        => $combined_score,
                    ));
                }
            }
            // $parameter_score_result = $this->ai_reports_model->get_parameters($_company_id,$_assessment_id);
            // foreach ($parameter_score_result as $psr){
            //     $parameter_id                  = $psr->parameter_id;
            //     $parameter_label_id            = $psr->parameter_label_id;
            //     $parameter_your_score_result   = $this->ai_reports_model->get_parameters_your_score($_company_id,$_assessment_id,$_user_id,$parameter_id,$parameter_label_id);
            //     $parameter_manual_score_result = $this->ai_reports_model->get_parameter_manual_score($_assessment_id,$_user_id,$parameter_id,$parameter_label_id);
                
            //     $your_score  = 0;
            //     if (isset($parameter_your_score_result) AND count((array)$parameter_your_score_result)>0){
            //         $your_score = $parameter_your_score_result->score;
            //     }
            //     $manual_score  = 0;
            //     if (isset($parameter_manual_score_result) AND count((array)$parameter_manual_score_result)>0){
            //         $manual_score = $parameter_manual_score_result->percentage;
            //     }
			// 	if($manual_score==0 || $your_score==0)
            //     {
            //         $combined_score = number_format((($your_score + $manual_score)),2);    
            //     }
            //     else
            //     {
            //         $combined_score = number_format((($your_score + $manual_score)/2),2);
            //     }

            //     array_push($parameter_score,array(
            //         "parameter_id"         => $psr->parameter_id,
            //         "parameter_label_id"   => $psr->parameter_label_id,
            //         "parameter_name"       => $psr->parameter_name,
            //         "parameter_label_name" => $psr->parameter_label_name,
            //         "your_score"           => $your_score,
            //         "manual_score"        => empty($manual_score) ? 'Not assessed' : number_format($manual_score,2,'.','').'%',
            //         "combined_score"        => $combined_score,
            //     ));
            // } 
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
}