<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Jqm extends MY_Controller {

    public function index() {
        global $CFG;

        $this->lang->load('master', $this->config->item('language'));
        $this->lang->load('connection', $this->config->item('language'));
        
        $this->load->view('jqm');
    }
}