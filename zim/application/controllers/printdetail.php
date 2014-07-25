<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class Printdetail extends MY_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->helper( array(
// 				'printerstate',
				'url',
				'json'
		) );
	}
	
	public function index() {
		return;
	}
	
	public function printprime() {
		$abb_cartridge = NULL;
		$first_run = FALSE;
		$cr = 0;
		
		// check model id, and then send it to print command
		$this->load->helper('printer');
		$abb_cartridge = $this->input->get('v');
		$first_run = $this->input->get('r');
		$callback = $this->input->get('cb');
		
		if ($abb_cartridge) {
			$first_run = ($first_run === FALSE) ? TRUE : FALSE;
			$cr = Printer_printFromPrime($abb_cartridge, $first_run);
// 			$cr = Printer_startPrintingStatusFromModel($mid);
			if ($cr != ERROR_OK) {
				$this->output->set_header('Location: /printmodel/listmodel');
				return;
			}
		}
		else {
			$this->output->set_header('Location: /printmodel/listmodel');
			return;
		}
		
		if ($callback) {
			$this->output->set_header('Location: /printdetail/status?v=' . $abb_cartridge . '&cb=' . $callback);
		}
		else {
			$this->output->set_header('Location: /printdetail/status?v=' . $abb_cartridge);
		}
		
		return;
	}
	
	public function printmodel() {
		$mid = NULL;
		$cr = 0;
		
		// check model id, and then send it to print command
		$this->load->helper('printer');
		$mid = $this->input->get('id');
// 		$callback = $this->input->get('cb');
		
		if ($mid) {
			$cr = Printer_printFromModel($mid);
// 			$cr = Printer_startPrintingStatusFromModel($mid);
			if ($cr != ERROR_OK) {
				$this->output->set_header('Location: /printmodel/listmodel');
				return;
			}
		}
		else {
			$this->output->set_header('Location: /printmodel/listmodel');
			return;
		}
		
// 		if ($callback) {
// 			$this->output->set_header('Location: /printdetail/status?id=' . $mid . '&cb=' . $callback);
// 		}
// 		else {
//  			$this->output->set_header('Location: /printdetail/status');
// 		}
		$this->output->set_header('Location: /printdetail/status?id=' . $mid);
		
		return;
	}
	
	public function printmodel_temp() {
		$mid = NULL;
		$cr = 0;
		$temperature_r = (int) $this->input->post('r');
		$temperature_l = (int) $this->input->post('l');
		$array_temper = array();
		if ($temperature_r > 0) $array_temper['r'] = $temperature_r;
		if ($temperature_l > 0) $array_temper['l'] = $temperature_l;
		
		// check model id, and then send it to print command
		$this->load->helper('printer');
		$mid = $this->input->get('id');
		
		if ($mid) {
			$cr = Printer_printFromModel($mid, $array_temper);
			if ($cr != ERROR_OK) {
				$this->output->set_header('Location: /printmodel/listmodel');
				return;
			}
		}
		else {
			$this->output->set_header('Location: /printmodel/listmodel');
			return;
		}
		
		$this->output->set_header('Location: /printdetail/status?id=' . $mid);
		
		return;
	}
	
	public function printslice() {
		$cr = 0;
		
		$this->load->helper('printer');
		
		$cr = Printer_printFromSlice();
		if ($cr != ERROR_OK) {
			$this->output->set_header('Location: /sliceupload/slice?callback');
			return;
		}
		else {
			$this->output->set_header('Location: /printdetail/status?id=slice');
		}
		
		return;
	}
	
	public function printslice_temp() {
		$cr = 0;
		$temperature_r = (int) $this->input->post('r');
		$temperature_l = (int) $this->input->post('l');
		$array_temper = array();
		if ($temperature_r > 0) $array_temper['r'] = $temperature_r;
		if ($temperature_l > 0) $array_temper['l'] = $temperature_l;
		
		$this->load->helper('printer');
		
		$cr = Printer_printFromSlice($array_temper);
		if ($cr != ERROR_OK) {
			$this->output->set_header('Location: /sliceupload/slice?callback');
			return;
		}
		else {
			$this->output->set_header('Location: /printdetail/status?id=slice');
		}
		
		return;
	}
	
	public function status() {
		$time_remain = NULL;
		$body_page = NULL;
		$template_data = array();
		$data_status = array();
		$temper_status = array();
		$print_slice = FALSE;
		
		$this->load->library('parser');
		$this->lang->load('printdetail', $this->config->item('language'));
		
		$callback = $this->input->get('cb');
		$abb_cartridge = $this->input->get('v');
		$id = $this->input->get('id');
		
		if ($id == 'slice') {
			$print_slice = TRUE;
		}
		
		// parse the main body
		$template_data = array(
				'title'			=> t('Control your printing'),
				'print_detail'	=> t('Printing details'),
				'print_stop'	=> t('Cancel'),
 				'wait_info'		=> t('Waiting for starting...'),
				'finish_info'	=> t('Congratulation, your printing is complete!'),
				'return_button'	=> t('Home'),
				'return_url'	=> '/',
// 				'restart_url'	=> '/printdetail/printprime?r&v=' . $abb_cartridge . '&cb=' . $callback,
				'restart_url'	=> '/printdetail/printmodel?id=' . $id,
				'var_prime'		=> 'false',
				'again_button'	=> t('Print again'),
				'video_url'		=> $this->config->item('video_url'),
		);
		
		if ($print_slice == TRUE) {
			$template_data['restart_url'] = '/printdetail/printslice';
		} else if ($abb_cartridge) {
			$template_data['finish_info']	= t('Restart?');
// 			$template_data['return_url']	= '/printmodel/detail?id=' . $callback;
			$template_data['restart_url']	= '/printdetail/printprime?r&v=' . $abb_cartridge . '&cb=' . $callback;
			$template_data['return_button']	= t('No');
			$template_data['var_prime']		= 'true';
			$template_data['again_button']	= t('Yes');
		}
		if ($callback) {
			if ($callback == 'slice') {
				$template_data['return_url']	= '/sliceupload/slice?callback';
			}
			else {
				$template_data['return_url']	= '/printmodel/detail?id=' . $callback;
			}
		}
		
		$body_page = $this->parser->parse('template/printdetail/status', $template_data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . t('ZeePro Personal Printer 21 - Printing details') . '</title>',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
		
		return;
	}
	
	public function status_ajax() {
		$template_data = array();
// 		$printing_status = '';
		$ret_val = 0;
		$data_status = array();
		$time_remain = 0;
		
		$this->load->helper(array('printer', 'timedisplay'));
		$this->load->library('parser');
		$this->lang->load('printdetail', $this->config->item('language'));
		$this->lang->load('timedisplay', $this->config->item('language'));
		
		$ret_val = Printer_checkPrintStatus($data_status);
		if ($ret_val == FALSE) {
			$this->load->helper('corestatus');
			$ret_val = CoreStatus_setInIdle();
			if ($ret_val == FALSE) {
				// log internal error
				$this->load->helper('printerlog');
				PrinterLog_logError('can not set idle after printing', __FILE__, __LINE__);
			}

			if ($this->config->item('simulator')) {
				// just set temperature for simulation
				$this->load->helper('printerstate');
				PrinterState_setExtruder('r');
				PrinterState_setTemperature(20);
				PrinterState_setExtruder('l');
				PrinterState_setTemperature(20);
				PrinterState_setExtruder('r');
			}
			
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
	
	public function slice() {
		$this->load->library('parser');
		$this->parser->parse('template/plaintxt', array('display' => 'IN CONSTRUCTION, goto /rest/status or any rest service'));
	}
	
	public function cancel() {
		$ret_val = NULL;
		//TODO finish me for canceling
		$this->load->helper('printer');
		
		$ret_val = Printer_stopPrint();
		if ($ret_val == TRUE) {
			$template_data = array();
			$body_page = '';
			
			$this->load->library('parser');
			$this->lang->load('printdetail', $this->config->item('language'));
			
			// parse the main body
			$template_data = array(
					'title'			=> t('Control your printing'),
					'wait_info'		=> t('wait_hint_cancel'),
					'finish_info'	=> t('finish_hint_cancel'),
					'return_button'	=> t('Home'),
					'return_url'	=> '/',
					'video_url'		=> $this->config->item('video_url'),
			);
			
			$body_page = $this->parser->parse('template/printdetail/cancel', $template_data, TRUE);
			
			// parse all page
			$template_data = array(
					'lang'			=> $this->config->item('language_abbr'),
					'headers'		=> '<title>' . t('printdetail_cancel_pagetitle') . '</title>',
					'contents'		=> $body_page,
			);
			
			$this->parser->parse('template/basetemplate', $template_data);
			
			return;
		}
		else {
			$this->load->helper('printerlog');
			PrinterLog_logError('can not stop printing', __FILE__, __LINE__);
			$this->output->set_status_header(403);
			return;
		}
		
		return;
	}
	
	public function recovery() {
		$ret_val = NULL;
		//TODO finish me for canceling
		$this->load->helper('printer');
		$this->load->library('parser');
		$this->lang->load('printdetail', $this->config->item('language'));
		
		// parse the main body
		$template_data = array(
				'title'			=> t('Control your printing'),
				'wait_info'		=> t('wait_hint_recovery'),
				'finish_info'	=> t('finish_hint_recovery'),
				'return_button'	=> t('Home'),
				'return_url'	=> '/',
		);
		
		$body_page = $this->parser->parse('template/printdetail/recovery', $template_data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . t('printdetail_recovery_pagetitle') . '</title>',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
		
		return;
	}
	
	public function cancel_ajax() {
		//TODO finish me for canceling
		$template_data = array();
		$ret_val = 0;
// 		$data_status = array();
		
// 		$this->load->helper(array('printer', 'timedisplay'));
		$this->load->helper('printer');
		$this->load->library('parser');
		$this->lang->load('printdetail', $this->config->item('language'));
// 		$this->lang->load('timedisplay', $this->config->item('language'));
		
// 		$ret_val = Printer_checkCancelStatus($data_status);
		$ret_val = Printer_checkCancelStatus();
		if ($ret_val == FALSE) {
			$this->load->helper('corestatus');
			$ret_val = CoreStatus_setInIdle();
			if ($ret_val == FALSE) {
				// log internal error
				$this->load->helper('printerlog');
				PrinterLog_logError('can not set idle after calceling', __FILE__, __LINE__);
			}
			
			if ($this->config->item('simulator')) {
				// just set temperature for simulation
				$this->load->helper('printerstate');
				PrinterState_setExtruder('r');
				PrinterState_setTemperature(20);
				PrinterState_setExtruder('l');
				PrinterState_setTemperature(20);
				PrinterState_setExtruder('r');
			}
			
			$this->output->set_status_header(202);
			return;
		}
		
// 		// treat time remaining for display
// 		if (isset($data_status['print_remain'])) {
// 			$time_remain = TimeDisplay__convertsecond(
// 					$data_status['print_remain'], t('Time remaining: '), t('under calculating'));
// 		}
// 		else {
// 			$time_remain = t('Time remaining: ') . t('unknown');
// 		}
		
		// parse the ajax part
		$template_data = array(
				'wait_info'	=> t('wait_hint_cancel'),
		);
		$this->parser->parse('template/printdetail/cancel_ajax', $template_data);
		
		$this->output->set_content_type('text/plain; charset=UTF-8');
		
		return;
	}
	
	public function recovery_ajax() {
		//TODO finish me for recovery
		$template_data = array();
		$ret_val = 0;
		$status_current = NULL;
		$data_status = array();
		
		$this->load->helper('corestatus');
		$this->load->library('parser');
		$this->lang->load('printdetail', $this->config->item('language'));
		
		$template_data = array(
				'wait_info'	=> t('wait_hint_recovery'),
		);
		
		$ret_val = CoreStatus_checkInIdle($status_current, $data_status);
		if ($ret_val == TRUE) {
			// log recovery finish
			$this->load->helper('printerlog');
			PrinterLog_logMessage('recovery status finish', __FILE__, __LINE__);
			
			$this->output->set_status_header(202);
			return;
		}
		else if ($status_current == CORESTATUS_VALUE_RECOVERY) {
			if ($data_status[CORESTATUS_TITLE_SUBSTATUS] == CORESTATUS_VALUE_PRINT) {
				$template_data['wait_info'] = t('wait_hint_recovery_printing');
			}
			else {
				$template_data['wait_info'] = t('wait_hint_recovery_unknown');
				$this->load->helper('printerlog');
				PrinterLog_logError('unknown substatus value in recovery ' . $data_status[CORESTATUS_TITLE_SUBSTATUS], __FILE__, __LINE__);
			}
		}
		else {
			$this->output->set_status_header(403);
			
			$this->load->helper('printerlog');
			PrinterLog_logError('call recovery status check when not in recovery', __FILE__, __LINE__);
			return;
		}
		
		// parse the ajax part
		$this->parser->parse('template/printdetail/cancel_ajax', $template_data); // we can use the same view for recovery
		
		$this->output->set_content_type('text/plain; charset=UTF-8');
		
		return;
	}
}
