<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class Printdetail extends MY_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->helper( array(
// 				'printerstate',
				'url',
				'json',
				'corestatus',
		) );
	}
	
	private function set_led() {
		$ret_val = 0;
		$status_strip = 0;
		$status_head = 0;
		
		$this->load->library('session');
		$this->load->helper('printerstate');

		$ret_val = PrinterState_getStripLedStatus($status_strip);
		if ($ret_val != ERROR_OK || $status_strip == FALSE) {
			$status_strip = 0;
		}
		else {
			$status_strip = 1;
		}
		$ret_val = PrinterState_getTopLedStatus($status_head);
		if ($ret_val != ERROR_OK || $status_head == FALSE) {
			$status_head = 0;
		}
		else {
			$status_head = 1;
		}
		
		$this->session->set_flashdata('led_strip', $status_strip);
		$this->session->set_flashdata('led_head', $status_head);
		
		return;
	}
	
	private function get_led(&$status_strip, &$status_head) {
		$this->load->library(array('parser', 'session'));
		
		$status_strip = $this->session->flashdata('led_strip');
		if ($status_strip === FALSE) {
			$ret_val = PrinterState_getStripLedStatus($status_strip);
			if ($ret_val != ERROR_OK) {
				$status_strip = FALSE;
			}
		}
		$status_head = $this->session->flashdata('led_head');
		if ($status_head === FALSE) {
			$ret_val = PrinterState_getTopLedStatus($status_head);
			if ($ret_val != ERROR_OK) {
				$status_head = FALSE;
			}
		}
		
		return;
	}
	
	public function index() {
		$this->output->set_header('Location: /');
		return;
	}
	
// 	public function printcalibration() {
// 		$mid = NULL;
// 		$cr = 0;
		
// 		// check model id, and then send it to print command
// 		$this->load->helper('printer');
		
// 		$this->set_led();
// 		$cr = Printer_printFromCalibration();
// 		if ($cr != ERROR_OK) {
// 			$this->output->set_header('Location: /printmodel/listmodel');
// 			return;
// 		}
		
// 		$this->output->set_header('Location: /printdetail/status?id=calibration');
		
// 		return;
// 	}
	
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
			$this->set_led();
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
		$cr = 0;
		$model_calibration = FALSE;
		$mid = $this->input->get('id');
		$exchange_extruder = (int) $this->input->post('exchange');
		
		// check model id, and then send it to print command
		$this->load->helper(array('printer', 'printlist'));
// 		$callback = $this->input->get('cb');
		
		$exchange_extruder = ($exchange_extruder != 0) ? TRUE : FALSE;
		
		if ($mid) {
			if ($mid == ModelList_codeModelHash(PRINTLIST_MODEL_CALIBRATION)) {
// 				$this->output->set_header('Location: /printdetail/printcalibration');
// 				return;
				$model_calibration = TRUE;
			}
			$this->set_led();
			$cr = Printer_printFromModel($mid, $model_calibration, $exchange_extruder);
// 			$cr = Printer_startPrintingStatusFromModel($mid);
			if ($cr != ERROR_OK) {
				if ($model_calibration) {
					$this->output->set_header('Location: /printmodel/detail?id=calibration');
				}
				else {
					$this->output->set_header('Location: /printmodel/listmodel');
				}
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
		if ($model_calibration) {
// 			$this->output->set_header('Location: /printdetail/status?id=calibration');
			$this->output->set_header('Location: /printdetail/status?id=' . CORESTATUS_VALUE_MID_CALIBRATION);
		}
		else {
			$this->output->set_header('Location: /printdetail/status?id=' . $mid);
		}
		
		return;
	}
	
	public function printmodel_temp() {
		$cr = 0;
		$model_calibration = FALSE;
		$mid = $this->input->get('id');
		$temperature_r = (int) $this->input->post('r');
		$temperature_l = (int) $this->input->post('l');
		$exchange_extruder = (int) $this->input->post('exchange');
		$array_temper = array();
		
		// check model id, and then send it to print command
		$this->load->helper(array('printer', 'printlist'));
		
		if ($temperature_r > 0) $array_temper['r'] = $temperature_r;
		if ($temperature_l > 0) $array_temper['l'] = $temperature_l;
		$exchange_extruder = ($exchange_extruder != 0) ? TRUE : FALSE;
		
		if ($mid) {
			if ($mid == ModelList_codeModelHash(PRINTLIST_MODEL_CALIBRATION)) {
// 				$this->output->set_header('Location: /printdetail/printcalibration');
// 				return;
				$model_calibration = TRUE;
			}
			$this->set_led();
			$cr = Printer_printFromModel($mid, $model_calibration, $exchange_extruder, $array_temper);
			if ($cr != ERROR_OK) {
				if ($model_calibration) {
					$this->output->set_header('Location: /printmodel/detail?id=calibration');
				}
				else {
					$this->output->set_header('Location: /printmodel/listmodel');
				}
				return;
			}
		}
		else {
			$this->output->set_header('Location: /printmodel/listmodel');
			return;
		}
		
		if ($model_calibration) {
// 			$this->output->set_header('Location: /printdetail/status?id=calibration');
			$this->output->set_header('Location: /printdetail/status?id=' . CORESTATUS_VALUE_MID_CALIBRATION);
		}
		else {
			$this->output->set_header('Location: /printdetail/status?id=' . $mid);
		}
		
		return;
	}
	
	public function printslice() {
		$cr = 0;
		$exchange_extruder = (int) $this->input->post('exchange');
		
		$this->load->helper('printer');
		$exchange_extruder = ($exchange_extruder != 0) ? TRUE : FALSE;
		
		$this->set_led();
		$cr = Printer_printFromSlice($exchange_extruder);
		if ($cr != ERROR_OK) {
			$this->output->set_header('Location: /sliceupload/slice?callback');
			return;
		}
		else {
// 			$this->output->set_header('Location: /printdetail/status?id=slice');
			$this->output->set_header('Location: /printdetail/status?id=' . CORESTATUS_VALUE_MID_SLICE);
		}
		
		return;
	}
	
	public function printslice_temp() {
		$cr = 0;
		$temperature_r = (int) $this->input->post('r');
		$temperature_l = (int) $this->input->post('l');
		$exchange_extruder = (int) $this->input->post('exchange');
		$array_temper = array();
		
		$this->load->helper('printer');
		
		if ($temperature_r > 0) $array_temper['r'] = $temperature_r;
		if ($temperature_l > 0) $array_temper['l'] = $temperature_l;
		$exchange_extruder = ($exchange_extruder != 0) ? TRUE : FALSE;
		
		$this->set_led();
		$cr = Printer_printFromSlice($exchange_extruder, $array_temper);
		if ($cr != ERROR_OK) {
			$this->output->set_header('Location: /sliceupload/slice?callback');
			return;
		}
		else {
// 			$this->output->set_header('Location: /printdetail/status?id=slice');
			$this->output->set_header('Location: /printdetail/status?id=' . CORESTATUS_VALUE_MID_SLICE);
		}
		
		return;
	}
	
	public function end_print() {
		//TODO need option for changing return page
		//TODO finish me
// 		$this->load->helper('printerstate');
		
// 		foreach(array('l', 'r') as $abb_filament) {
// 			PrinterState_setTemperature($array_cartridge[PRINTERSTATE_TITLE_EXT_TEMP_1], 'e', $abb_filament);
// 		}
		
		$this->output->set_header('Location: /');
		
		return;
	}
	
	public function status() {
		$time_remain = NULL;
		$body_page = NULL;
		$pagetitle = NULL;
		$template_data = array();
		$data_status = array();
		$temper_status = array();
		$print_slice = FALSE;
		$print_calibration = FALSE;
		$status_strip = FALSE;
		$status_head = FALSE;
		$ret_val = 0;
		$option_selected = 'selected="selected"';
		$camera_prm = NULL;
		
		$this->load->library('parser');
		$this->lang->load('printdetail', $this->config->item('language'));
		$this->lang->load('printerstate/index', $this->config->item('language'));
		
		$this->load->helper(array('zimapi', 'printerstate'));

		$id = $this->input->get('id');

		// get print length
		$length = 0;
		// if just sliced model, get value from temporary json file
		if ($id == CORESTATUS_VALUE_MID_SLICE) {
			$temp_json = array();
			
			$this->load->helper(array('printerstate', 'slicer'));
			$temp_json = json_read($this->config->item('temp') . SLICER_FILE_TEMP_DATA, TRUE);
			if (!isset($temp_json['error'])) {
				foreach($temp_json['json'] as $temp_filament) {
					if (array_key_exists(PRINTERSTATE_TITLE_NEED_L, $temp_filament)) {
						$length += $temp_filament[PRINTERSTATE_TITLE_NEED_L];
					}
				}
			}
		}
		else if ($id == CORESTATUS_VALUE_MID_CALIBRATION) {
			$this->load->helper('printlist');
			ModelList_codeModelHash(PRINTLIST_MODEL_CALIBRATION);
		}
		// if presliced model get from helper
		else if ($id == CORESTATUS_VALUE_MID_CALIBRATION || strlen($id) == 32) {
			$model_info = array();
			$mid = NULL;
			
			if ($id == CORESTATUS_VALUE_MID_CALIBRATION) {
				$mid = ModelList_codeModelHash(PRINTLIST_MODEL_CALIBRATION);
			}
			else {
				$mid = $id;
			}
			
			$this->load->helper('printlist');
			if (ERROR_OK == ModelList__getDetailAsArray($mid, $model_info) && !is_null($model_info)) {
				foreach (array(PRINTLIST_TITLE_LENG_F1, PRINTLIST_TITLE_LENG_F2) as $key_length) {
					if (array_key_exists($key_length, $model_info)) {
						$length += $model_info[$key_length];
					}
				}
			}
		}
		// if from gcode library
		else {
			$this->load->helper('printerstoring');
			$info = PrinterStoring_getInfo("gcode", $id);

			//$length += (array_key_exists('length', $info) ? $info['length'] : 0);
		}
		if ($length == 0) {
			$this->load->helper('printerlog');
			PrinterLog_logMessage('detected zero filament length, use default length instead', __FILE__, __LINE__);
			$length = ZIMAPI_VALUE_DEFAULT_LENGTH;
		}

		// 30s timelapse at 10 fps, so 300 / print time with 0.5mm/s average speed
		$camera_prm = str_replace('{fps}',
				(ZIMAPI_VALUE_DEFAULT_TL_LENGTH * 10 / ($length / ZIMAPI_VALUE_DEFAULT_SPEED + ZIMAPI_VALUE_DEFAULT_TL_OFFSET)),
				ZIMAPI_PRM_CAMERA_PRINTSTART_TIMELAPSE);
		if (!ZimAPI_cameraOn($camera_prm)) {
			$this->load->helper('printerlog');
			PrinterLog_logError('can not set camera', __FILE__, __LINE__);
		}
		// test for debug
		else {
			$this->load->helper('printerlog');
			PrinterLog_logDebug('camera parameter: ' . $camera_prm, __FILE__, __LINE__);
		}
		
		//TODO improve passing the real value of LED later
		$this->get_led($status_strip, $status_head);
		
		$callback = $this->input->get('cb');
		$abb_cartridge = $this->input->get('v');
		
		if ($id == CORESTATUS_VALUE_MID_SLICE) {
			$print_slice = TRUE;
// 			$callback = CORESTATUS_VALUE_MID_SLICE;
		}
		else if ($id == CORESTATUS_VALUE_MID_CALIBRATION) {
			$print_calibration = TRUE;
// 			$callback = CORESTATUS_VALUE_MID_CALIBRATION;
		}
		
		// parse the main body
		$template_data = array(
				'title'				=> t('Control your printing'),
				'print_detail'		=> t('Printing details'),
				'print_stop'		=> t('Cancel'),
				'cancel_confirm'	=> t('cancel_confirm'),
 				'wait_info'			=> t('Waiting for starting...'),
				'finish_info'		=> t('Congratulation, your printing is complete!'),
				'return_button'		=> t('Home'),
				'return_url'		=> '/',
// 				'restart_url'		=> '/printdetail/printprime?r&v=' . $abb_cartridge . '&cb=' . $callback,
				'restart_url'		=> '/printdetail/printmodel?id=' . $id,
				'var_prime'			=> 'false',
				'var_slice'			=> 'false',
				'again_button'		=> t('Print again'),
				'video_url'			=> $this->config->item('video_url'),
				'strip_led'			=> t('strip_led'),
				'head_led'			=> t('head_led'),
				'led_on'			=> t('led_on'),
				'led_off'			=> t('led_off'),
				'lighting'			=> t('lighting'),
				'initial_strip'		=> ($status_strip == TRUE) ? $option_selected : NULL,
				'initial_head'		=> ($status_head == TRUE) ? $option_selected : NULL,
				'video_error'		=> t('video_error'),
				'loading_player'	=> t('loading_player'),
				// storegcode + timelapse
				'storegcode_info'	=> t('storegcode_info'),
				'storegcode_name'	=> t('storegcode_name'),
				'timelapse_error'	=> t('timelapse_error'),
				'timelapse_ok'		=> t('timelapse_ok'),
				'timelapse_info'	=> t('timelapse_info'),
				'timelapse_button'	=> t('timelapse_button')
		);
		
		if ($print_slice == TRUE) {
			$template_data['restart_url']	= '/printdetail/printslice';
			$template_data['var_slice']		= 'true';
// 			$template_data['return_url']	= '/sliceupload/slice?callback';
		}
		else if ($print_calibration == TRUE) {
// 			$template_data['restart_url'] = '/printdetail/printcalibration';
			$template_data['restart_url'] = '/printmodel/detail?id=calibration';
			$template_data['return_url']	= '/printerstate/offset_setting';
			$template_data['return_button']	= t('button_set_offset');
		} else if ($abb_cartridge) {
			$template_data['finish_info']	= t('Restart?');
// 			$template_data['return_url']	= '/printmodel/detail?id=' . $callback;
			$template_data['restart_url']	= '/printdetail/printprime?r&v=' . $abb_cartridge . '&cb=' . $callback;
			$template_data['return_button']	= t('No');
			$template_data['var_prime']		= 'true';
			$template_data['again_button']	= t('Yes');
			
			// change wording
			$template_data['title'] = t('title_prime');
			$template_data['print_detail'] = t('print_detail_prime');
			$template_data['cancel_confirm'] = t('cancel_confirm_prime');
// 			$template_data['finish_info'] = t('finish_info_prime');
			$template_data['wait_info'] = t('wait_info_prime');
		}
		
// 		if ($callback)
		if ($callback && !in_array($callback, array(CORESTATUS_VALUE_MID_CALIBRATION, CORESTATUS_VALUE_MID_SLICE))) {
// 			if ($callback == 'slice') {
// 				$template_data['return_url']	= '/sliceupload/slice?callback';
// 			}
// 			else if ($callback == 'calibration') {
// 				$template_data['return_url']	= '/printerstate/offset_setting';
// 				$template_data['return_button']	= t('button_set_offset');
// 			}
// 			else {
				$template_data['return_url']	= '/printmodel/detail?id=' . $callback;
// 			}
		}
		
		$body_page = $this->parser->parse('template/printdetail/status', $template_data, TRUE);
		
		// parse all page
		$pagetitle = ($abb_cartridge) ? t('pagetitle_prime') : t('ZeePro Personal Printer 21 - Printing details');
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . $pagetitle . '</title>',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
		
		return;
	}
	
// 	public function slice() {
// 		$this->load->library('parser');
// 		$this->parser->parse('template/plaintxt', array('display' => 'IN CONSTRUCTION, goto /rest/status or any rest service'));
// 	}
	
	public function cancel() {
		$ret_val = NULL;
		$this->load->helper('printer');
		
		$ret_val = Printer_stopPrint();
		if ($ret_val == TRUE) {
			$template_data = array();
			$body_page = '';
			$status_current = NULL;
			$array_status = array();
			
			$this->load->library('parser');
			$this->lang->load('printdetail', $this->config->item('language'));
			
			$this->load->helper('zimapi');
			if (!ZimAPI_cameraOn(ZIMAPI_PRM_CAMERA_PRINTSTART)) {
				$this->load->helper('printerlog');
				PrinterLog_logError('can not set camera', __FILE__, __LINE__);
			}
			
			// parse the main body
			$template_data = array(
					'title'			=> t('Control your printing'),
					'wait_info'		=> t('wait_hint_cancel'),
					'finish_info'	=> t('finish_hint_cancel'),
					'return_button'	=> t('Home'),
					'return_url'	=> '/',
					'video_url'		=> $this->config->item('video_url'),
			);
			
			CoreStatus_checkInIdle($status_current, $array_status);
			if (is_array($array_status) && array_key_exists(CORESTATUS_TITLE_PRINTMODEL, $array_status)
					&& in_array($array_status[CORESTATUS_TITLE_PRINTMODEL],
							array(CORESTATUS_VALUE_MID_PRIME_L, CORESTATUS_VALUE_MID_PRIME_R)
					)) {
				$template_data['title'] = t('title_prime');
			}
			
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
		$body_page = NULL;
		$template_data = array();
		
		//TODO finish me for recovery
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
	
	public function timelapse() {
		$body_page = NULL;
		$template_data = array();
		$array_info = array();
		$status_current = NULL;
		$array_status = array();
		$restart_url = NULL;
		
		$this->load->library('parser');
		$this->load->helper('zimapi');
		$this->lang->load('printdetail', $this->config->item('language'));
		
		if (CoreStatus_checkInIdle($status_current, $array_status) && array_key_exists(CORESTATUS_TITLE_PRINTMODEL, $array_status)) {
			$model_id = NULL;
			$abb_cartridge = NULL;
			
			switch ($array_status[CORESTATUS_TITLE_PRINTMODEL]) {
				case CORESTATUS_VALUE_MID_SLICE:
					$preset_id = NULL;
					$preset_name = t('timelapse_info_presetname_unknown');
					
					$array_info[] = array(
							'title'	=> t('timelapse_info_modelname_title'),
							'value'	=> t('timelapse_info_modelname_slice'),
					);
					
					$this->load->helper('zimapi');
					if (ZimAPI_getPreset($preset_id)) {
						$array_json = array();
						
						if (ERROR_OK == ZimAPI_getPresetInfoAsArray($preset_id, $array_json)) {
							$preset_name = $array_json[ZIMAPI_TITLE_PRESET_NAME];
						}
					}
					$array_info[] = array(
							'title'	=> t('timelapse_info_presetname_title'),
							'value'	=> $preset_name,
					);
					
					$restart_url = '/printdetail/printslice';
					break;
					
				case CORESTATUS_VALUE_MID_PRIME_R:
					$abb_cartridge = 'r';
					// treat priming in the same way
					
				case CORESTATUS_VALUE_MID_PRIME_L:
					// never reach here normally (no timelapse for priming in principe, just for safety)
					$array_info[] = array(
							'title'	=> t('timelapse_info_modelname_title'),
							'value'	=> t('timelapse_info_modelname_prime'),
					);
					
					if (is_null($abb_cartridge)) {
						$abb_cartridge = 'l';
					}
					$restart_url = '/printdetail/printprime?r&v=' . $abb_cartridge;
					//TODO we lose callback info here
					break;
					
				case CORESTATUS_VALUE_MID_CALIBRATION:
					$this->load->helper('printlist');
					$model_id = ModelList_codeModelHash(PRINTLIST_MODEL_CALIBRATION);
					$restart_url = '/printmodel/detail?id=calibration';
					// treat as a normal pre-sliced model
					
				default:
					// treat as pre-sliced model
					$model_data = array();
					$model_name = t('timelapse_info_modelname_unknown');
					
					if (is_null($model_id)) {
						$this->load->helper('printlist');
						$model_id = $array_status[CORESTATUS_TITLE_PRINTMODEL];
					}
					
					if (ERROR_OK == ModelList__getDetailAsArray($model_id, $model_data, TRUE)) {
						$model_name = $model_data[PRINTLIST_TITLE_NAME];
					}
					$array_info[] = array(
							'title'	=> t('timelapse_info_modelname_title'),
							'value'	=> $model_name,
					);
					
					if (is_null($restart_url)) {
						$restart_url = '/printdetail/printmodel?id=' . $model_id;
					}
					break;
			}
			
			if (array_key_exists(CORESTATUS_TITLE_ELAPSED_TIME, $array_status)) {
				$display_time = NULL;
				
				$this->load->helper('timedisplay');
				$display_time = TimeDisplay__convertsecond($array_status[CORESTATUS_TITLE_ELAPSED_TIME], '');
				
				$array_info[] = array(
						'title'	=> t('timelapse_info_elapsedtime_title'),
						'value'	=> $display_time,
				);
			}
		}
		else {
			$this->load->helper('printerlog');
			PrinterLog_logError('unintended status detected in timelapse page: ' . $status_current, __FILE__, __LINE__);
			$this->output->set_header('Location: /');
			
			return;
		}
		
		// parse the main body
		$template_data = array(
				'loading_player'		=> t('loading_player'),
				'finish_info'			=> t('Congratulation, your printing is complete!'),
				'home_button'			=> t('Home'),
				'video_error'			=> t('video_error'),
				'timelapse_title'		=> t('timelapse_title'),
// 				'timelapse_button'		=> t('timelapse_button'),
				'send_email_button'		=> t('send_email_button'),
				'send_email_hint'		=> t('send_email_hint'),
				'send_email_action'		=> t('send_email_action'),
				'send_email_error'		=> t('send_email_error'),
				'send_email_wrong'		=> t('send_email_wrong'),
				'video_url'				=> '/tmp/' . ZIMAPI_FILENAME_TIMELAPSE,
				'timelapse_info_title'	=> t('timelapse_info_title'),
				'timelapse_info'		=> $array_info,
				'again_button'			=> t('Print again'),
		);
		
		$body_page = $this->parser->parse('template/printdetail/timelapse', $template_data, TRUE);
		
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
		$temper_l = 0;
		$temper_r = 0;
		$finish_hint = NULL;
		$hold_temper = NULL;
		
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
		
		if ($data_status['print_percent'] == 100) {
			$current_status = NULL;
			$array_status = array();
			
			CoreStatus_checkInIdle($status_current, $array_status);
			if (is_array($array_status) && array_key_exists(CORESTATUS_TITLE_PRINTMODEL, $array_status)
					&& in_array($array_status[CORESTATUS_TITLE_PRINTMODEL],
							array(CORESTATUS_VALUE_MID_PRIME_L, CORESTATUS_VALUE_MID_PRIME_R)
					)) {
				$finish_hint = t('in_finish_prime');
			}
			else {
				$finish_hint = t('in_finish');
			}
			
			$hold_temper = 'true';
		}
		else {
			$hold_temper = 'false';
			$temper_l = $data_status['print_temperL'];
			$temper_r = $data_status['print_temperR'];
		}
		
		// parse the ajax part
		$template_data = array(
				'print_percent'	=> t('Percentage: %d%%', array($data_status['print_percent'])),
				'print_remain'	=> $time_remain,
				'hold_temper'	=> $hold_temper,
				'print_temperL'	=> t('Temperature of the left extruder: %d °C', array($temper_l)),
				'print_temperR'	=> t('Temperature of the right extruder: %d °C', array($temper_r)),
				'value_temperL'	=> $temper_l,
				'value_temperR'	=> $temper_r,
				'in_finish'		=> $finish_hint,
		);
		$this->parser->parse('template/printdetail/status_ajax', $template_data);
		
		$this->output->set_content_type('text/plain; charset=UTF-8');
		
		return;
	}
	
	public function cancel_ajax() {
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
				PrinterLog_logError('can not set idle after canceling', __FILE__, __LINE__);
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

	public function camera_stop_ajax() {
		$timelapse_path = '';
		$capture = (int) $this->input->post('capture');
		
		$this->load->helper('zimapi');
		if (!ZimAPI_cameraOff()) {
			$this->load->helper('printerlog');
			PrinterLog_logError('can not turn off camera', __FILE__, __LINE__);
			return null;
		}
		
		// we need capture image only in slice model case
		if ($capture == 1) {
			$capture_path = '';
			
			sleep(3); // wait for release of camera
			if (!ZimAPI_cameraCapture($capture_path)) {
				$this->load->helper('printerlog');
				PrinterLog_logError('can not take capture camera', __FILE__, __LINE__);
				return null;
			}
		}
		
		if (!ZimAPI_encodeTimelapse($timelapse_path)) {
			$this->load->helper('printerlog');
			PrinterLog_logError('can not take encode timelapse', __FILE__, __LINE__);
			return null;
		}
		
		echo '/tmp/' . ZIMAPI_FILENAME_TIMELAPSE;
		
		return;
//		return $timelapse_path;
	}
	
	public function timelapse_ready_ajax() {
		$is_ready = FALSE;
		$status_code = 200;
		
		if (CoreStatus_checkInPrinted($is_ready)) {
			if ($is_ready == TRUE) {
				$status_code = 202;
			}
			else {
				$status_code = 200;
			}
		}
		else {
			$status_code = 403;
		}
		
		$this->output->set_status_header($status_code);
		
		return;
	}
	
	public function timelapse_end_ajax() {
		$status_code = 200;
		
		if (!CoreStatus_checkInPrinted()) {
			$status_code = 403;
		}
		else if (!ZimAPI_removeTimelapse()) {
			$status_code = 500;
		}
		
		$this->output->set_status_header($status_code);
		
		return;
	}
	
	public function sendemail_ajax() {
		$cr = 0;
		$display = NULL;
		
		$this->load->helper('zimapi');
		
		$email = $this->input->post('email');
		
		if ($email) {
			$cr = ZimAPI_sendTimelapse($email);
		}
		else {
			$cr = ERROR_MISS_PRM;
		}
		
		$display = $cr . " " . t(MyERRMSG($cr));
		$this->output->set_status_header($cr, $display);
		
		return;
	}
}
