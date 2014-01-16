<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class Printerstate extends MY_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->helper( array(
				'printerstate',
				'url',
				'json'
		) );
	}
	
	function index() {
		$this->changecartridge();
		
		return;
	}
	
	function changecartridge() {
		global $CFG;
		$template_data = array();
		
		$abb_cartridge = $this->input->get('v');
		
		if (!$abb_cartridge && !in_array($abb_cartridge, array('l', 'r'))) {
			if (isset($_SERVER['HTTP_REFERER'])) {
				$this->output->set_header('Location: ' . $_SERVER['HTTP_REFERER']);
			}
			else {
				$this->output->set_header('Location: /');
			}
			return;
		}
		
		$this->load->library('parser');
		$this->lang->load('printerstate', $this->config->item('language'));
		
		// parse the main body
		$template_data = array(
				'title'			=> ($abb_cartridge == 'l') ? t('Left cartridge change') : t('Right cartridge change'),
				'step1_title'	=> t('Step one: Clear out the filament'),
				'step1_action'	=> t('Clear out'),
				'step1_message'	=> t('Pull out the cartridge'),
				'step2_title'	=> t('Step two: Pull on the cartridge'),
				'step2_action'	=> t('Pull on'),
				'step2_message'	=> t('Filament charging'),
				'step_process'	=> t('Running'),
				'back'			=> t('back'),
		);
		
		$body_page = $this->parser->parse('template/printerstate/changecartridge', $template_data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $CFG->config ['language_abbr'],
				'headers'		=> '<title>' . t('ZeePro Personal Printer 21 - Change cartridge') . '</title>',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
		
		return;
	}
	
}