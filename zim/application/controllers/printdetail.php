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
		
		// parse the main body
		$template_data = array(
				'title'			=> t('Control your printing'),
				'print_detail'	=> t('Printing details'),
				'print_stop'	=> t('Cancel'),
 				'wait_info'		=> t('Waiting for starting...'),
				'finish_info'	=> t('Congratulation, your printing is complete!'),
				'return_button'	=> t('Home'),
		);
		
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
		
		$this->load->helper(array('printer', 'timedisplay'));
		$this->load->library('parser');
		$this->lang->load('printdetail', $this->config->item('language'));
		$this->lang->load('timedisplay', $this->config->item('language'));
		
// 		// get phase of printing (heat / print)
// 		$ret_val = Printer_getStatus($printing_status);
// 		if ($ret_val == FALSE) { // we are not in printing
// 			$this->output->set_status_header(403);
// 			return;
// 		}
		
// 		switch ($printing_status) {
// 			case PRINTER_VALUE_STATUS_HEAT:
// 				$ret_val = Printer_checkStartTemperature($data_status);
// 				if ($ret_val == TRUE) {
// 					echo 'reach temperature';
// 					//TODO delete the checktemperature status
// 				}
// 				else {
// 					echo var_dump($data_status);
// 				}
// 				break;
				
// 			case PRINTER_VALUE_STATUS_PRINT:
				$time_remain = 0;
				
				$ret_val = Printer_checkPrint($data_status);
				if ($ret_val == FALSE) {
					$this->load->helper('corestatus');
					$ret_val = CoreStatus_setInIdle();
					if ($ret_val == FALSE) {
						//TODO treat internal error here
					}
					
					//FIXME just set temperature for simulation
					$this->load->helper('printerstate');
					PrinterState_setExtruder('r');
					PrinterState_setTemperature(20);
					PrinterState_setExtruder('l');
					PrinterState_setTemperature(20);
					PrinterState_setExtruder('r');
					
					$this->output->set_status_header(204);
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
				$this->parser->parse('template/printdetail/status_ajax_print', $template_data);
// 				break;
				
// 			default:
// 				$this->output->set_status_header(403);
// 				return;
// 				break; // never reach here
// 		}
		
		$this->output->set_content_type('text/plain; charset=UTF-8');
		
		return;
	}
	
// 	public function status_ajax() {
// 		$template_data = array();
// 		$data_status = array();
// 		$temper_status = array();
		
// 		$this->load->helper('printerstate');
// 		$this->load->library('parser');
// 		$this->lang->load('printdetail', $this->config->item('language'));
// 		$this->lang->load('timedisplay', $this->config->item('language'));
		
// 		// check status, if we are not in printing, send users to menu home
// 		$data_status = PrinterState__checkStatusAsArray();
// 		if ($data_status[PRINTERSTATE_TITLE_STATUS] != PRINTERSTATE_VALUE_IN_PRINT) {
// 			$this->output->set_status_header(404);
// 			return;
// 		}
		
// 		// get temperatures of extruders
// 		$temper_status = PrinterState_getExtruderTemperaturesAsArray();
// 		if (!is_array($temper_status)) {
// 			//TODO treat the internal error when getting temperatures of extruders
// 		}
		
// 		// get time remaining
// 		if (isset($data_status[PRINTERSTATE_TITLE_DURATION])) {
// 			$time_remain = TimeDisplay__convertsecond(
// 					$data_status[PRINTERSTATE_TITLE_DURATION], t('Time remaining: '), t('Under calculating'));
// 		}
// 		else {
// 			$time_remain = t('Time remaining: ') . t('Unknown');
// 		}
		
// 		// parse the ajax part
// 		$template_data = array(
// 				'print_percent'	=> t('Percentage: %d%%', array($data_status[PRINTERSTATE_TITLE_PERCENT])),
// 				'print_remain'	=> $time_remain,
// 				'print_temperL'	=> t('Temperature of left extruder: %d °C', array($temper_status[PRINTERSTATE_LEFT_EXTRUD])),
// 				'print_temperR'	=> t('Temperature of right extruder: %d °C', array($temper_status[PRINTERSTATE_RIGHT_EXTRUD])),
// 		);
		
// 		$this->parser->parse('template/printdetail/status_ajax_print', $template_data);
// 		$this->output->set_content_type('text/plain; charset=UTF-8');
// 	}
}
