<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Language extends MY_Controller {

    public function index() {
        global $CFG;

        $this->lang->load('master', $this->config->item('language'));
        $this->lang->load('language', $this->config->item('language'));
        
        $this->load->view('language', array("lang" => $CFG->config['language_abbr']));
    }
}