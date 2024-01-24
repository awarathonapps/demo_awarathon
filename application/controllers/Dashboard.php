<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Dashboard extends MY_Controller {
    function __construct() {
        parent::__construct();
    }
    public function index() {
        $data['module_id'] = '1.0';
        // $this->load->view('blank',$data);
        redirect('home');
    }

}
