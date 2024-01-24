<?php
use Vimeo\Vimeo;
defined('BASEPATH') OR exit('No direct script access allowed');
class Vimeo_url_fetch extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('common_model');
        $this->load->model('ai_cronjob_model');
    }
    public function index(){
        echo "Welcome to the Awarathon";
        exit;
    }

    public function fix_vimeo_url(){
        $vimeo_client_id     = '6e257ab857cdfdbf0078f9314e7a7f3391df1110';
        $vimeo_client_secret = 'IyfnxxSFhzLoY/UZuzNOiFglDkr32w7cg5w7a36MX1yHv+ovdXY9I88kNc0eJrk7X2qddumo4nLiyKFoS0+gA5WATnF1BZXSFbIAObs5e9sifAEzzfMySTUG+gAd0zST';
        $vimeo_access_token  = 'd4ebc2f67ad07a412c2302238ea7fe4e';
        $lib = new Vimeo($vimeo_client_id, $vimeo_client_secret,$vimeo_access_token);

        $task_result = $this->common_model->get_selected_values('assessment_results', 'id,assessment_id,user_id,video_url', 'ftp_status=0');

        foreach($task_result as $vimeo_result){
            $video_url = $vimeo_result->video_url;

            $request_url = "/me/videos/".$video_url;
            $vimeo_vidstat_response = $lib->request($request_url);
            
            if(isset($vimeo_vidstat_response['body']['embed']['html'])){
                $tdata = $vimeo_vidstat_response['body']['embed']['html'];
                $htmldata1 = explode('/',$tdata);
                $htmldata2 = (isset($htmldata1[4]) ? explode('&',$htmldata1[4]) : '');
                if(isset($htmldata2[0])){
                    $vimeo_uri= $htmldata2[0];
                    $update_data = [
                        'vimeo_uri' => $vimeo_uri,
                        'ftp_status' => 1
                    ];
                    $this->common_model->update('assessment_results', 'id', $vimeo_result->id, $update_data);
                }

                $attempt_result = $this->common_model->get_value('assessment_attempts', 'id', 'assessment_id="'.$vimeo_result->assessment_id.'" AND user_id="'.$vimeo_result->user_id.'" AND is_completed=1');
                if(!empty($attempt_result)){
                    $data = array(
                        'ftpto_vimeo_uploaded' => 1,
                        'ftpto_vimeo_dttm'     => date('Y-m-d H:i:s')
                    );
                    $cmpltid = $this->common_model->update('assessment_attempts', 'id',$attempt_result->id,$data);
                }
                
            }
        }

    }
}