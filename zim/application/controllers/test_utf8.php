<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Test_utf8 extends CI_Controller {

	public function index() {
		$display = '';
		$display_f = '';
		$display_id = '';
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			global $CFG;
			$chars = $this->input->post('chars');
			$this->load->helper('json');
			
			$display .= "raw: $chars\n";
			$temp_id = md5($chars);
			$display_id .= "raw: $temp_id\n";
			$temp = utf8_decode($chars);
			$temp_id = md5($temp);
			$display .= "utf8_decode: $temp\n";
			$display_id .= "utf8_decode: $temp_id\n";
			
			$fp = fopen($CFG->config['temp'] . 'chars.tmp', 'w');
			fwrite($fp, json_encode_unicode(array($display)));
			fclose($fp);
			if (file_exists($CFG->config['temp'] . 'chars.tmp')) {
				$array_log = file($CFG->config['temp'] . 'chars.tmp');
				foreach ($array_log as $line) {
					$display_f .= $line;
				}
			}
			
			$this->load->helper('json');
			$temp_array = json_read($CFG->config['temp'] . 'chars.tmp', TRUE);
			$display_id = var_dump($temp_array);
		}
		
		$this->load->helper('form');
		
		$this->load->library('parser');
		$this->parser->parse('template/test_utf8', array(
				'display'=>$display,
				'display_f'=>$display_f,
				'display_id'=>$display_id,
		));
		
		return;
	}

}
