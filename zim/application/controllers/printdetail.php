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
		$temper_status = array();
		
		$this->load->helper('printerstate');
		$this->load->library('parser');
		$this->lang->load('printdetail', $this->config->item('language'));
		$this->lang->load('timedisplay', $this->config->item('language'));
		
		//wait a little for the arcontrol emulator starting printing
		usleep(500000);
		
		// check status, if we are not in printing, send users to menu home
		$data_status = PrinterState__checkStatusAsArray();
		if ($data_status[PRINTERSTATE_TITLE_STATUS] != PRINTERSTATE_VALUE_IN_PRINT) {
			$this->output->set_header('Location: /menu_home');
			return;
		}
		
		// get temperatures of extruders
		$temper_status = PrinterState__getTemperaturesAsArray();
		if (!is_array($temper_status)) {
			//TODO treat the internal error when getting temperatures of extruders
		}
		
		// get time remaining
		if (isset($data_status[PRINTERSTATE_TITLE_DURATION])) {
			$time_remain = TimeDisplay__convertsecond(
					$data_status[PRINTERSTATE_TITLE_DURATION], t('Time remaining: '), t('Under calculating'));
		}
		else {
			$time_remain = t('Time remaining: ') . t('Unknown');
		}
		
		// parse the main body
		$template_data = array(
				'title'			=> t('Control your creation'),
				'print_detail'	=> 'Printing details',
 				'print_percent'	=> t('Percentage: %d%%', array($data_status[PRINTERSTATE_TITLE_PERCENT])),
				'print_remain'	=> $time_remain,
				'print_stop'	=> t('Stop'),
				'print_temperL'	=> t('Temperature of left extruder: %d °C', array($temper_status[PRINTERSTATE_LEFT_EXTRUD])),
				'print_temperR'	=> t('Temperature of right extruder: %d °C', array($temper_status[PRINTERSTATE_RIGHT_EXTRUD])),
		);
		
		$body_page = $this->parser->parse('template/printdetail', $template_data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $CFG->config ['language_abbr'],
				'headers'		=> '<title>' . t('ZeePro Personal Printer 21 - Printing details') . '</title>',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
		
		return;
	}
	
	public function status_ajax() {
		$template_data = array();
		$data_status = array();
		$temper_status = array();
		
		$this->load->helper('printerstate');
		$this->load->library('parser');
		$this->lang->load('printdetail', $this->config->item('language'));
		$this->lang->load('timedisplay', $this->config->item('language'));
		
		// check status, if we are not in printing, send users to menu home
		$data_status = PrinterState__checkStatusAsArray();
		if ($data_status[PRINTERSTATE_TITLE_STATUS] != PRINTERSTATE_VALUE_IN_PRINT) {
			$this->output->set_status_header(404);
			return;
		}
		
		// get temperatures of extruders
		$temper_status = PrinterState__getTemperaturesAsArray();
		if (!is_array($temper_status)) {
			//TODO treat the internal error when getting temperatures of extruders
		}
		
		// get time remaining
		if (isset($data_status[PRINTERSTATE_TITLE_DURATION])) {
			$time_remain = TimeDisplay__convertsecond(
					$data_status[PRINTERSTATE_TITLE_DURATION], t('Time remaining: '), t('Under calculating'));
		}
		else {
			$time_remain = t('Time remaining: ') . t('Unknown');
		}
		
		// parse the ajax part
		$template_data = array(
				'print_percent'	=> t('Percentage: %d%%', array($data_status[PRINTERSTATE_TITLE_PERCENT])),
				'print_remain'	=> $time_remain,
				'print_temperL'	=> t('Temperature of left extruder: %d °C', array($temper_status[PRINTERSTATE_LEFT_EXTRUD])),
				'print_temperR'	=> t('Temperature of right extruder: %d °C', array($temper_status[PRINTERSTATE_RIGHT_EXTRUD])),
		);
		
		$this->parser->parse('template/printdetail_ajax', $template_data);
		$this->output->set_content_type('text/plain; charset=UTF-8');
	}
}
