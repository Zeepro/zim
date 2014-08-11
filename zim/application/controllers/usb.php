<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Usb extends MY_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->helper(array('url'));
	}
	
	public function index()
	{
		$this->load->library('parser');
		$this->lang->load('usb', $this->config->item('language'));
		
		$data = array(
			'hint'	=>	t('hint'),
		);
		
		$body_page = $this->parser->parse('template/usb', $data, TRUE);
		
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . t('usb_pagetitle') . '</title>',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
	}
}