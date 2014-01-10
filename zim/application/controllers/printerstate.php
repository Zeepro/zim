<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class Printerstate extends CI_Controller {
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
		
		// parse the main body
		$template_data = array(
				'title'			=> 'Cartridge change',
				'step1_title'	=> 'Step one: Clear out the filament',
				'step1_action'	=> 'Clear out',
				'step1_message'	=> 'Pull out the cartridge',
				'step2_title'	=> 'Step two: pull on the cartridge',
				'step2_action'	=> 'Pull on',
				'step2_message'	=> 'Filament charging',
				'step_process'	=> 'Running',
		);
		
		$body_page = $this->parser->parse('template/printerstate/changecartridge', $template_data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $CFG->config ['language_abbr'],
				'headers'		=> '<title>ZeePro Personal Printer 21 - Change cartridge</title>',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
		
		return;
	}
	
}