<?php
use Vimeo\Vimeo;
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Test_python extends MY_Controller {

    public function __construct() {
       
    }

    public function index() {
        try {
            $output = shell_exec(sprintf("python3.7 /var/www/html/awarathon.com/ai/python/app.py  2>&1"));
            print_r($output, true);
            $_output = print_r($output, true);
            $encode_output = '{"success": "true", "message": '.json_encode($_output).'}';
            // echo $encode_output;
            echo "<pre>";
            print_r(json_decode($encode_output));

        }catch(Exception $e) {
            $output = json_decode('{"success": "false", "message": "Script failed"}');
            echo json_encode($output);
        }
    }


    public function call_pythen_in() {
        try {
            $output = shell_exec(sprintf("python3.7 /var/www/html/awarathon.com/ai/python/Data_Filtering.py  2>&1"));
            print_r($output, true);
            $_output = print_r($output, true);
            $encode_output = '{"success": "true", "message": '.json_encode($_output).'}';
            echo $encode_output;
        }catch(Exception $e) {
            $output = json_decode('{"success": "false", "message": "Script failed"}');
            echo json_encode($output);
        }
    }

}
