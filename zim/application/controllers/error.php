<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Error extends ZP_Controller {
	public function index() {
		$template_data = NULL; //array();
		
		$this->load->library('parser');
		$this->lang->load('error', $this->config->item('language'));
		
		$template_data = array(
			'title'			=>	t('title'),
			'error'			=>	t('error'),
			'button_home'	=>	t('button_home'),
		);
		
		$this->_parseBaseTemplate(t('pagetitle_error'),
				$this->parser->parse('error', $data, TRUE));
		$this->output->set_status_header(503, 'Error Zeepro');
		
		return;
	}

}