<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Test_video extends CI_Controller {

	public function index() {
		$this->load->library('parser');
		$this->parser->parse('template/test_video', array('video_url' => $this->config->item('video_url')));
		
		return;
	}

}
