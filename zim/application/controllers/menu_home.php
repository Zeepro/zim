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
		$sso_name = NULL;

		$this->load->library('parser');
		$this->lang->load('menu_home', $this->config->item('language'));
		$this->load->helper('zimapi');
		
		if (!ZimAPI_cameraOff()) {
			$this->load->helper('printerlog');
			PrinterLog_logError('can not turn off camera', __FILE__, __LINE__);
		}
		
		// parse the main body
		if (ZimAPI_getPrinterSSOName($value) != ERROR_OK);
			;
		if ($value == '')
			$activation_btn = $this->parser->parse('template/activation/activation_btn', array('activate_printer' => t('activate_printer')), true);
		else
			$activation_btn = NULL;
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
				'contents'		=> $body_page
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
		return;
	}
	
}

