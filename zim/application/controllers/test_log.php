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
	
	public function file($type = 'debug') {
		$path_file = '';
		
		switch ($type) {
			case 'debug':
				$path_file = $this->config->item('log_file');
				break;
				
			case 'arduino':
				$path_file = $this->config->item('log_arduino');
				break;
				
			case 'printlog':
				$this->load->helper('printerstate');
				$path_file = PRINTERSTATE_FILE_PRINTLOG;
				break;
				
			default:
				break;
		}
		
		if (!$path_file) {
			$this->output->set_content_type('txt_u');
			echo 'error';
		}
		else if (!file_exists($path_file)) {
			$this->output->set_content_type('txt_u');
			echo 'no file';
		}
		else {
			$this->load->helper('file');
			$this->output->set_content_type(get_mime_by_extension($path_file))->set_output(file_get_contents($path_file));
		}
		
		return;
	}

}
