<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Rest extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('form', 'url', 'json'));
    }

    public function resetnetwork() {
        global $CFG;
        
        echo 'Yo!';
    }

}