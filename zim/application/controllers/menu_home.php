<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class Menu_home extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->helper( array(
				'url',
				'json' 
		) );
	}
	
	public function index() {
		global $CFG;
		$template_data = array();
		$body_page = NULL;
		
		$this->load->library('parser');
		
		// parse the main body
		$template_data = array(
				'title'				=> 'Home',
				'menu_printlist'	=> 'Quick print',
				'menu_printerstate'		=> 'Printer details',
		);
		
		$body_page = $this->parser->parse('template/menu_home', $template_data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $CFG->config ['language_abbr'],
				'headers'		=> '<title>ZeePro Personal Printer 21 - Menu Home</title>',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
		
		return;
	}
	
}

