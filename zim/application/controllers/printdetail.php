<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class Printdetail extends MY_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->helper( array(
				'printerstate',
				'url',
				'json'
		) );
	}
	
	public function index() {
		return;
	}
	
	public function printmodel() {
		$mid = NULL;
		$cr = 0;
		
		// check model id, and then send it to print command
		$this->load->helper('printer');
		$mid = $this->input->get('id');
		$callback = $this->input->get('cb');
		
		if ($mid) {
			$cr = Printer_printFromModel($mid);
// 			$cr = Printer_startPrintingStatusFromModel($mid);
			if ($cr != ERROR_OK) {
				$this->output->set_header('Location: /printmodel/listmodel');
				return;
			}
		} else {
			$this->output->set_header('Location: /printmodel/listmodel');
			return;
		}
		
		if ($callback) {
			$this->output->set_header('Location: /printdetail/status?id=' . $mid . '&cb=' . $callback);
		}
		else {
			$this->output->set_header('Location: /printdetail/status');
		}
		
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
		
		$callback = $this->input->get('cb');
		$mid = $this->input->get('id');
		
		// parse the main body
		$template_data = array(
				'title'			=> t('Control your printing'),
				'print_detail'	=> t('Printing details'),
				'print_stop'	=> t('Cancel'),
 				'wait_info'		=> t('Waiting for starting...'),
				'finish_info'	=> t('Congratulation, your printing is complete!'),
				'return_button'	=> t('Home'),
				'return_url'	=> '/',
				'restart_url'	=> '/printdetail/printmodel?id=' . $mid . '&cb=' . $callback,
				'var_prime'		=> 'false',
				'prime_button'	=> t('Yes'),
		);
		
		if ($callback && $mid) {
			$template_data['finish_info']	= t('Restart?');
			$template_data['return_url']	= '/printmodel/detail?id=' . $callback;
			$template_data['return_button']	= t('No');
			$template_data['var_prime']		= 'true';
		}
		
		$body_page = $this->parser->parse('template/printdetail/status', $template_data, TRUE);
		
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
		$printing_status = '';
		$ret_val = 0;
		$data_status = array();
		$time_remain = 0;
		
		$this->load->helper(array('printer', 'timedisplay'));
		$this->load->library('parser');
		$this->lang->load('printdetail', $this->config->item('language'));
		$this->lang->load('timedisplay', $this->config->item('language'));
		
		$ret_val = Printer_checkPrint($data_status);
		if ($ret_val == FALSE) {
			$this->load->helper('corestatus');
			$ret_val = CoreStatus_setInIdle();
			if ($ret_val == FALSE) {
				// log internal error
				$this->load->helper('printerlog');
				PrinterLog_logError('can not set idle after printing');
			}
			
			//FIXME just set temperature for simulation
			$this->load->helper('printerstate');
			PrinterState_setExtruder('r');
			PrinterState_setTemperature(20);
			PrinterState_setExtruder('l');
			PrinterState_setTemperature(20);
			PrinterState_setExtruder('r');
			
			$this->output->set_status_header(202);
			return;
		}
		
		// treat time remaining for display
		if (isset($data_status['print_remain'])) {
			$time_remain = TimeDisplay__convertsecond(
					$data_status['print_remain'], t('Time remaining: '), t('under calculating'));
		}
		else {
			$time_remain = t('Time remaining: ') . t('unknown');
		}
		
		// parse the ajax part
		$template_data = array(
				'print_percent'	=> t('Percentage: %d%%', array($data_status['print_percent'])),
				'print_remain'	=> $time_remain,
				'print_temperL'	=> t('Temperature of the left extruder: %d °C', array($data_status['print_temperL'])),
				'print_temperR'	=> t('Temperature of the right extruder: %d °C', array($data_status['print_temperR'])),
				'value_temperL'	=> $data_status['print_temperL'],
				'value_temperR'	=> $data_status['print_temperR'],
		);
		$this->parser->parse('template/printdetail/status_ajax', $template_data);
		
		$this->output->set_content_type('text/plain; charset=UTF-8');
		
		return;
	}
}
