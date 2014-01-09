<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class Printdetail extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->helper( array(
				'printerstate',
				'url',
				'json'
		) );
	}
	
	public function index() {
		$mid = NULL;
		$cr = 0;
		
		// check model id, and then send it to print command
		$this->load->helper('printlist');
		$mid = $this->input->get('id');
		
		if ($mid) {
			$cr = ModelList_print($mid);
			if ($cr != ERROR_OK) {
				$this->output->set_header('Location: /printmodel/listmodel');
				return;
			}
		} else {
			$this->output->set_header('Location: /printmodel/listmodel');
			return;
		}
		
		$this->output->set_header('Location: /printdetail/status');
		
		return;
	}
	
	public function status() {
		global $CFG;
		$time_remain = NULL;
		$body_page = NULL;
		$template_data = array();
		$data_status = array();
		
		$this->load->helper('printerstate');
		$this->load->library('parser');
		
		//wait a little for the arcontrol emulator starting printing
		usleep(500000);
		
		// check status, if we are not in printing, send users to menu home
		$data_status = PrinterState__checkStatusAsArray();
		if ($data_status[PRINTERSTATE_TITLE_STATUS] != PRINTERSTATE_VALUE_IN_PRINT) {
			$this->output->set_header('Location: /menu_home');
			return;
		}
		
		$time_remain = isset($data_status[PRINTERSTATE_TITLE_DURATION]) ? $data_status[PRINTERSTATE_TITLE_DURATION] : 'Unknown';
		
		// parse the main body
		$template_data = array(
				'title'			=> 'Control your creation',
				'print_detail'	=> 'Printing details',
				'print_percent'	=> 'Percentage: ' . $data_status[PRINTERSTATE_TITLE_PERCENT] . '%',
				'print_remain'	=> 'Time remaining: ' . $time_remain,
				'print_stop'	=> 'Stop',
		);
		
		$body_page = $this->parser->parse('template/printdetail', $template_data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $CFG->config ['language_abbr'],
				'headers'		=> '<title>ZeePro Personal Printer 21 - Printing details</title>',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
		
		return;
	}

}
