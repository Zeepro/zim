<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Test_log extends CI_Controller {

	public function index() {
// 		$this->output->set_content_type('text/plain; charset=UTF-8');
		$this->output->set_content_type('txt_u');
		echo 'Log level: ' . $this->config->item('log_level') . "\n";
		if (file_exists($this->config->item('log_file'))) {
			$array_log = file($this->config->item('log_file'));
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
		if (file_exists($this->config->item('log_file'))) {
			unlink($this->config->item('log_file'));
// 			echo "clear log file\n";
		}
// 		$this->index();
		$this->output->set_header('Location: /test_log');
	}

}
