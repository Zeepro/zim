<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Test_log extends CI_Controller {

	public function index() {
		global $CFG;

// 		$this->output->set_content_type('text/plain; charset=UTF-8');
		$this->output->set_content_type('txt_u');
		echo 'Log level: ' . $CFG->config['log_level'] . "\n";
		if (file_exists($CFG->config['log_file'])) {
			$array_log = file($CFG->config['log_file']);
			foreach ($array_log as $line) {
				echo $line;
			}
		}
		else {
			echo "no log file\n";
		}
		
		return;
	}
	
	public function clear() {
		global $CFG;
		if (file_exists($CFG->config['log_file'])) {
			unlink($CFG->config['log_file']);
// 			echo "clear log file\n";
		}
// 		$this->index();
		$this->output->set_header('Location: /test_log');
	}

}
