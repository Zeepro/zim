<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class Menu_home extends MY_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->helper( array(
				'url',
				'json' 
		) );
	}
	
	public function index() {
		$template_data = array();
		$body_page = NULL;
		
		$this->load->library('parser');
		$this->lang->load('menu_home', $this->config->item('language'));
		
		// parse the main body
		//TODO WHEN HELPER, CHECK PRINTER NAME
		$activation_btn = (false ?
							$this->load->view('template/activation/activation_btn', null, true) : null);
		$template_data = array(
// 				'title'				=> t('Home'),
				'menu_printlist'	=> t('Quick print'),
				'menu_printerstate'	=> t('Configuration'),
				'manage'			=> t('manage'),
				'upload'			=> t('upload'),
				'activation_btn'	=> $activation_btn
		);
		
		
		$body_page = $this->parser->parse('template/menu_home', $template_data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . t('ZeePro Personal Printer 21 - Home') . '</title>',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
		
		return;
	}
	
}

