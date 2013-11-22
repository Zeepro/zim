<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public function __construct() {
        global $CFG;

        parent::__construct();
        $this->load->helper('json');

        // Workflow management

        if ($this->router->class != 'initialization') {
            if (file_exists($CFG->config['conf'] . 'Boot.json')) {
            	if ($this->router->class == 'rest') {
                // Upgrade in progress error code
            		http_response_code(450);
            		exit;
				} else {
                // Initialization page redirect
					header('location:/initialization');
            	}
            } else {
                if ($this->router->class != 'connection') {
                    if (!file_exists($CFG->config['conf'] . 'Connection.json')) {
                        // Connection page redirect
                        header('location:/connection');
                    }
                    $arr = json_read($CFG->config['conf'] . 'Work.json');
                    if (!$arr["error"] and
                            $arr["json"]["State"] == "Working" and
                            $arr["json"]["CallBackURL"] != $this->router->uri->uri_string) {
                        // Work in progress -> redirect to the waiting page
                        header("Location:" . $arr["json"]["CallBackURL"]);
                    }
                } else {
                    if (file_exists($CFG->config['conf'] . 'Connection.json')) {
                        // The connexion page shouldn't be accessed otherwise
                        header('location:/');
                    }
                }
            }
        } else {
            if (!file_exists($CFG->config['conf'] . 'Boot.json')) {
                // The initialisation page shouldn't be accessed otherwise
                header('location:/');
            }
        }
    }

}