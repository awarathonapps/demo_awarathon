<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');  
 
require_once('vimeo-api/src/Vimeo/Vimeo.php');
class Vimeo_api extends Vimeo\Vimeo {
    public function __construct($params) {
        parent::__construct($params['client_id'],$params['client_secret'],$params['access_token']);
    }
}