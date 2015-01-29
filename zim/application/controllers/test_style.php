<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');


class Test_style extends CI_Controller {
	
	public function index() {
		$template_data = array();
		$body_page = NULL;
		
		$this->load->library('parser');
		$body_page = $this->parser->parse('test_style', array(), TRUE);
		
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>Test style</title>'. "\n"
						. '<link rel="stylesheet" href="/assets/jquery-mobile-fluid960.min.css">',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('basetemplate', $template_data);
		
		return;
	}

}
