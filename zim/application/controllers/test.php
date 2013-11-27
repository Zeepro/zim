<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Test extends MY_Controller {

    public function index() {
        global $CFG;

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $gcode = $_POST['gcode'];
                if ($gcode) {
//                 	exec ('Arcontrol_cli ' . $gcode , &$response);
					$response = 'Arcontrol_cli response...';
                } else {
                	$response = '';
                }
                break;
            default: // GET
            	$response = '';
            	break;
        }

        $this->lang->load('master', $this->config->item('language'));

        $this->template->load('master', 'test', array("response" => $response));
    }

}