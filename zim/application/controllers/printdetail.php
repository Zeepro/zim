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
	
	private function get_extra_info(&$array_temper, &$exchange_extruder) {
		$temperature_r = 0;
		$temperature_l = 0;
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$temperature_r = (int) $this->input->post('r');
			$temperature_l = (int) $this->input->post('l');
			$exchange_extruder = (int) $this->input->post('exchange');
		}
		else {
			$status_current = NULL;
			$array_status = array();
			
			$this->load->helper('corestatus');
			if (CoreStatus_checkInIdle($status_current, $array_status)) {
				$temperature_r = $array_status[CORESTATUS_TITLE_P_TEMPER_R];
				$temperature_l = $array_status[CORESTATUS_TITLE_P_TEMPER_L];
				$exchange_extruder = $array_status[CORESTATUS_TITLE_P_EXCH_BUS];
			}
		}
		
		if ($temperature_r > 0) $array_temper['r'] = $temperature_r;
		if ($temperature_l > 0) $array_temper['l'] = $temperature_l;
		$exchange_extruder = ($exchange_extruder != 0) ? TRUE : FALSE;
		
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
		//TODO it's better to stock callback model in json file
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
	
	public function printgcode($gid = NULL) {
		$this->printgcode_temp($gid);
		
		return;
	}
	
	public function printgcode_temp($gid = NULL) {
		$exchange_extruder = 0;
		$array_temper = array();
		
		$this->get_extra_info($array_temper, $exchange_extruder);
		
		if (is_null($gid)) {
			$gid = (int) $this->input->get('id');
		}
		
		if ($gid) {
			$gcode_info = array();
			
			$this->load->helper(array('printerstoring', 'corestatus'));
			
			$gcode_info = PrinterStoring_getInfo("gcode", $gid);
			if (!is_null($gcode_info)) {
				$cr = PrinterStoring_printGcode($gid);
				
				if ($cr == ERROR_OK) {
					$this->output->set_header('Location: /printdetail/status?id=' . CORESTATUS_VALUE_MID_PREFIXGCODE . $gid);
					
					return;
				}
			}
		}
		
		$this->output->set_header('Location: /printerstoring/listgcode');
		
		return;
	}
	
	public function printmodel() {
		$this->printmodel_temp();
		
		return;
	}
	
	public function printmodel_temp() {
		$cr = 0;
		$model_calibration = FALSE;
		$mid = $this->input->get('id');
		$exchange_extruder = 0;
		$array_temper = array();
		
		// check model id, and then send it to print command
		$this->load->helper(array('printer', 'printlist', 'corestatus'));
		
		$this->get_extra_info($array_temper, $exchange_extruder);
		
		if ($mid) {
			if (strpos($mid, CORESTATUS_VALUE_MID_PREFIXGCODE) === 0) {
				$gid = (int) substr($mid, strlen(CORESTATUS_VALUE_MID_PREFIXGCODE) - 1);
				
				$this->printgcode_temp($gid);
				
				return;
			}
			
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
		$this->printslice_temp();
		
		return;
	}
	
	public function printslice_temp() {
		$cr = 0;
		$exchange_extruder = 0;
		$array_temper = array();
		
		$this->load->helper('printer');
		
		$this->get_extra_info($array_temper, $exchange_extruder);
		
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
		
		$this->load->library('parser');
		$this->lang->load('printdetail', $this->config->item('language'));
		$this->lang->load('printerstate/index', $this->config->item('language'));
		
		$this->load->helper(array('zimapi', 'printerstate'));
		
		$id = $this->input->get('id');
		$callback = $this->input->get('cb');
		$abb_cartridge = $this->input->get('v');
		
		if ($abb_cartridge || $id == CORESTATUS_VALUE_MID_CALIBRATION) {
			// do not launch timelapse image generation for priming and calibration model
			if (!ZimAPI_cameraOn(ZIMAPI_PRM_CAMERA_PRINTSTART)) {
				$this->load->helper('printerlog');
				PrinterLog_logError('can not set camera', __FILE__, __LINE__);
			}
		}
		else {
			// get print length for timelapse
			$length = 0;
			
			// if just sliced model, get value from temporary json file
			if ($id == CORESTATUS_VALUE_MID_SLICE) {
				$temp_json = array();
				
				$this->load->helper('printerstate');
				
				if (ERROR_OK == PrinterState_getSlicedJson($temp_json)) {
					foreach($temp_json as $temp_filament) {
						if (array_key_exists(PRINTERSTATE_TITLE_NEED_L, $temp_filament)) {
							$length += $temp_filament[PRINTERSTATE_TITLE_NEED_L];
						}
					}
				}
			}
			// if gcode file from user library
			else if (strpos($id, CORESTATUS_VALUE_MID_PREFIXGCODE) === 0) {
				$gcode_info = array();
				
				$this->load->helper('printerstoring');
				$id = (int) substr($id, strlen(CORESTATUS_VALUE_MID_PREFIXGCODE) - 1);
				
				$gcode_info = PrinterStoring_getInfo("gcode", $id);
				if (!is_null($gcode_info) && array_key_exists(PRINTERSTORING_TITLE_LENG_R, $gcode_info)
						&& array_key_exists(PRINTERSTORING_TITLE_LENG_L, $gcode_info)) {
					$length = $gcode_info[PRINTERSTORING_TITLE_LENG_R] + $gcode_info[PRINTERSTORING_TITLE_LENG_L];
				}
			}
			// if presliced model get from helper
			else if (strlen($id) == 32) {
				$model_info = array();
				
				$this->load->helper('printlist');
				if (ERROR_OK == ModelList__getDetailAsArray($id, $model_info) && !is_null($model_info)) {
					foreach (array(PRINTLIST_TITLE_LENG_F1, PRINTLIST_TITLE_LENG_F2) as $key_length) {
						if (array_key_exists($key_length, $model_info)) {
							$length += $model_info[$key_length];
						}
					}
				}
			}
			
			if (!ZimAPI_cameraOn(ZIMAPI_PRM_CAMERA_PRINTSTART_TIMELAPSE, $length)) {
				$this->load->helper('printerlog');
				PrinterLog_logError('can not set camera with timelapse, length: ' . $length, __FILE__, __LINE__);
			}
		}
		
		//TODO improve passing the real value of LED later
		$this->get_led($status_strip, $status_head);
		
		if ($id == CORESTATUS_VALUE_MID_SLICE) {
			$print_slice = TRUE;
		}
		else if ($id == CORESTATUS_VALUE_MID_CALIBRATION) {
			$print_calibration = TRUE;
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
				'var_calibration'	=> $print_calibration ? 'true' : 'false',
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
				'timelapse_button'	=> t('timelapse_button'),
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
			
			if ($callback) {
				if ($callback == 'slice') {
					$template_data['return_url']	= '/sliceupload/slice?callback';
				}
				else {
					$template_data['return_url']	= '/printmodel/detail?id=' . $callback;
				}
			}
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
		$model_displayname = NULL;
		
		$this->load->library('parser');
		$this->load->helper('zimapi');
		$this->lang->load('printdetail', $this->config->item('language'));
		
		if (CoreStatus_checkInIdle($status_current, $array_status) && array_key_exists(CORESTATUS_TITLE_PRINTMODEL, $array_status)) {
			$model_id = NULL;
			$abb_cartridge = NULL;
			
			switch ($array_status[CORESTATUS_TITLE_PRINTMODEL]) {
				case CORESTATUS_VALUE_MID_SLICE:
					$preset_id = NULL;
					$model_filename = array();
					$preset_name = t('timelapse_info_presetname_unknown');
					
					$this->load->helper('slicer');
					if (ERROR_OK == Slicer_getModelFile(0, $model_filename, TRUE)) {
						foreach($model_filename as $model_basename) {
							if (strlen($model_displayname)) {
								$model_displayname .= ' + ' . $model_basename;
							}
							else {
								$model_displayname = $model_basename;
							}
						}
					}
					else {
						$model_displayname = t('timelapse_info_modelname_slice');
					}
					$array_info[] = array(
							'title'	=> t('timelapse_info_modelname_title'),
							'value'	=> $model_displayname,
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
					// never reach here normally (no timelapse for calibration model, just for safety)
					$this->load->helper('printlist');
					$model_id = ModelList_codeModelHash(PRINTLIST_MODEL_CALIBRATION);
					$restart_url = '/printmodel/detail?id=calibration';
					// treat as a normal pre-sliced model
					
				default:
					// treat as pre-sliced model
					$model_data = array();
					$model_displayname = t('timelapse_info_modelname_unknown');
					
					if (is_null($model_id)) {
						$this->load->helper('printlist');
						$model_id = $array_status[CORESTATUS_TITLE_PRINTMODEL];
					}
					
					if (ERROR_OK == ModelList__getDetailAsArray($model_id, $model_data, TRUE)) {
						$model_displayname = $model_data[PRINTLIST_TITLE_NAME];
					}
					$array_info[] = array(
							'title'	=> t('timelapse_info_modelname_title'),
							'value'	=> $model_displayname,
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
			
			if (array_key_exists(CORESTATUS_TITLE_P_TEMPER_L, $array_status)
					&& $array_status[CORESTATUS_TITLE_P_TEMPER_L] > 0
					&& array_key_exists(CORESTATUS_TITLE_P_TEMPER_R, $array_status)
					&& $array_status[CORESTATUS_TITLE_P_TEMPER_R] > 0) {
				$array_info[] = array(
						'title'	=> t('timelapse_info_temperature_title'),
						'value'	=> t('timelapse_info_temperature_values', array(
								$array_status[CORESTATUS_TITLE_P_TEMPER_L],
								$array_status[CORESTATUS_TITLE_P_TEMPER_R],
						)),
				);
			}
			else if (array_key_exists(CORESTATUS_TITLE_P_TEMPER_R, $array_status)
					&& $array_status[CORESTATUS_TITLE_P_TEMPER_R] > 0) {
				$array_info[] = array(
						'title'	=> t('timelapse_info_temperature_title'),
						'value'	=> t('timelapse_info_temperature_value_r', array(
								$array_status[CORESTATUS_TITLE_P_TEMPER_R],
						)),
				);
			}
			else if (array_key_exists(CORESTATUS_TITLE_P_TEMPER_L, $array_status)
					&& $array_status[CORESTATUS_TITLE_P_TEMPER_L] > 0) {
				$array_info[] = array(
						'title'	=> t('timelapse_info_temperature_title'),
						'value'	=> t('timelapse_info_temperature_value_l', array(
								$array_status[CORESTATUS_TITLE_P_TEMPER_L],
						)),
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
				'internet_ok'			=> (@file_get_contents("https://sso.zeepro.com/login.ashx") === FALSE) ? 'false' : 'true',
				'loading_player'		=> t('timelapse_info'),
				'finish_info'			=> t('Congratulation, your printing is complete!'),
				'home_button'			=> t('Home'),
				'home_popup_text'		=> t('home_popup_text'),
				'yes'					=> t('Yes'),
				'no'					=> t('No'),
				'video_error'			=> t('video_error'),
				'timelapse_title'		=> t('timelapse_title'),
// 				'timelapse_button'		=> t('timelapse_button'),
				'send_email_button'		=> t('send_email_button'),
				'send_yt_button'		=> t('send_yt_button'),
				'send_email_hint'		=> t('send_email_hint'),
				'send_email_action'		=> t('send_email_action'),
				'send_email_error'		=> t('send_email_error'),
				'send_email_wrong'		=> t('send_email_wrong'),
				'send_email_multi'		=> t('send_email_multi'),
				'video_url'				=> '/tmp/' . ZIMAPI_FILENAME_TIMELAPSE . '?_=' . time(),
				'timelapse_info_title'	=> t('timelapse_info_title'),
				'timelapse_info'		=> $array_info,
				'again_button'			=> t('Print again'),
				'restart_url'			=> $restart_url ? $restart_url : '/',
				'send_email_modelname'	=> $model_displayname,
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
		$time_remain = NULL;
		$time_passed = NULL;
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
			$time_remain = t('Time remaining: ') . t('in_progress');
		}
		$time_passed = TimeDisplay__convertsecond($data_status['print_tpassed'], t('time_elapsed'));
		
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
// 				'print_percent'	=> t('Percentage: %d%%', array($data_status['print_percent'])),
				'percent_title'	=> t('percent_title'),
				'value_percent'	=> $data_status['print_percent'],
				'print_remain'	=> $time_remain,
				'print_passed'	=> $time_passed,
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
		$model = $this->input->post('model');
		
		if ($email && $model) {
			$emails = explode(',', $email);
			// check parenthesis surround, add them if not exist
			if (strlen($model) && ($model[0] != '(' || $model[strlen($model) - 1] != ')')) {
				$model = '(' . $model . ')';
			}
			
			$cr = ZimAPI_sendTimelapse($emails, $model);
		}
		else {
			$cr = ERROR_MISS_PRM;
		}
		
		$display = $cr . " " . t(MyERRMSG($cr));
		$this->output->set_status_header($cr, $display);
		
		return;
	}

	public function youtube_form()
	{
		$this->load->library('parser');
		$this->lang->load('printdetail', $this->config->item('language'));
		
		if ($this->input->server('REQUEST_METHOD') == 'POST')
		{
			$this->load->library("session");
			
			$title = isset($_POST['yt_title']) ? $_POST['yt_title'] : '3D printing by zim';
			$description = isset($_POST['yt_description']) ? $_POST['yt_description'] : 'Time-lapse video powered by zim 3D printer, the reference in personal 3D printing. Visit zeepro.com to join the zim experience !';
			
			$tags = explode(',', $_POST['yt_tags'] ? $_POST['yt_tags'] : 'zim, zeepro');
			$tags = array_map('trim', $tags);
			$video_infos = array(
				'yt_title'		=> $title,
				'yt_tags'		=> $tags,
				'yt_desc'		=> $description,
				'yt_privacy'	=> $_POST["yt_privacy"]
			);
			$this->session->set_userdata($video_infos);
			var_dump($this->session->all_userdata());
			$this->output->set_header("Location: /printdetail/connect_google");
		}
		
		$data = array();
		$body_page = $this->parser->parse('template/printdetail/youtube_form', $data, TRUE);
		
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . 'Zim - Zim-motion' . '</title>',
				'contents'		=> $body_page
		);
		$this->parser->parse('template/basetemplate', $template_data);
		return;
	}

	public function video_upload()
	{
		$state = $_GET['state'];
		$code = $_GET['code'];
		$this->load->library('parser');
		$this->lang->load('printdetail', $this->config->item('language'));
		$data = array(
				'state'		=> $state,
				'code'		=> $code,
				'uploading'	=> t('uploading')
		);
		$body_page = $this->parser->parse('template/printdetail/video_upload', $data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . 'Zim - Zim-motion' . '</title>',
				'contents'		=> $body_page
		);
		$this->parser->parse('template/basetemplate', $template_data);
		return;
	}

	public function connect_google($in_upload_state = "")
	{
		set_include_path(get_include_path() . PATH_SEPARATOR . BASEPATH . '../assets/google_api/src');
		require_once 'Google/Client.php';
		require_once 'Google/Service/YouTube.php';
		$this->load->library('session');
		// 		$this->session->sess_destroy();
		//session_start();
	
	
	
		$client = new Google_Client();
		$client->setApplicationName("Test youtube upload");
		$client->setClientId("607574717756-dki0mh9seat7g8rsj34rtd79aeh47oo3.apps.googleusercontent.com");
		$client->setClientSecret("yX_irSTlQVjGM5miuHlhaDHg");
		$client->setScopes('https://www.googleapis.com/auth/youtube');
		//		$client->setDeveloperKey("AIzaSyCoUgRm1SYZ9Tk8TMOhy7tmlGDbNgKigfw");
		$redirect = filter_var('https://sso.zeepro.com/redirect.ashx', FILTER_SANITIZE_URL);
		$client->setRedirectUri($redirect);
		$client->setAccessType('offline');
	
		$youtube = new Google_Service_YouTube($client);
	
		if (isset($_GET['code']))
		{
			if (strval($this->session->userdata('state')) !== strval($_GET['state']))
			{
				var_dump($this->session->all_userdata());
				die('The session state did not match.');
			}
			$client->authenticate($_GET['code']);
			$this->session->set_userdata('token', $client->getAccessToken());
			$this->session->set_userdata('code', $_GET['code']);
			//header('Location: ' . $redirect);
		}
	
		if ($this->session->userdata('token') !== FALSE)
		{
			$client->setAccessToken($this->session->userdata('token'));
			if ($client->isAccessTokenExpired())
			{
				$currentTokenData = json_decode($this->session->userdata('token'));
				if (isset($currentTokenData->refresh_token))
				{
					$client->refreshToken($tokenData->refresh_token);
				}
			}
		}
		if ($client->getAccessToken() && $in_upload_state != "")
		{
			$this->load->helper('zimapi');	
			try
			{
				//REPLACE this value with the path to the file you are uploading.
				$videoPath = ZIMAPI_FILEPATH_TIMELAPSE;
	
				//Create a snippet with title, description, tags and category ID
				// Create an asset resource and set its snippet metadata and type.
				// This example sets the video's title, description, keyword tags, and
				// video category.
				$snippet = new Google_Service_YouTube_VideoSnippet();
				$snippet->setTitle($this->session->userdata('yt_title'));
				$snippet->setDescription($this->session->userdata("yt_desc"));
				$snippet->setTags($this->sesssion->userdata("yt_tags"));
					
				// Numeric video category. See https://developers.google.com/youtube/v3/docs/videoCategories/list
				$snippet->setCategoryId("22");
					
				// Set the video's status to "public". Valid statuses are "public", "private" and "unlisted".
				$status = new Google_Service_YouTube_VideoStatus();
				$status->privacyStatus = $this->session->userdata('yt_privacy');
					
				// Associate the snippet and status objects with a new video resource.
				$video = new Google_Service_YouTube_Video();
				$video->setSnippet($snippet);
				$video->setStatus($status);
					
				// Specify the size of each chunk of data, in bytes. Set a higher value for
				// reliable connection as fewer chunks lead to faster uploads. Set a lower
				// value for better recovery on less reliable connections.
				$chunkSizeBytes = 1 * 1024 * 1024;
					
				// Setting the defer flag to true tells the client to return a request which can be called
				// with ->execute(); instead of making the API call immediately.
				$client->setDefer(true);
					
				// Create a request for the API's videos.insert method to create and upload the video.
				$insertRequest = $youtube->videos->insert("status,snippet", $video);
					
				// Create a MediaFileUpload object for resumable uploads.
				$media = new Google_Http_MediaFileUpload($client, $insertRequest, 'video/mp4', null, true, $chunkSizeBytes);
				$media->setFileSize(filesize($videoPath));
	
				// Read the media file and upload it chunk by chunk.
				$status = false;
				$handle = fopen($videoPath, "rb");
				while (!$status && !feof($handle))
				{
					$chunk = fread($handle, $chunkSizeBytes);
					$status = $media->nextChunk($chunk);
				}
				fclose($handle);
	
				// If you want to make other calls after the file upload, set setDefer back to false
				$client->setDefer(false);
				//$client->revokeToken($this->session->userdata('token'));
				//$this->session->unset_userdata('token');
				$this->session->unset_userdata(array('yt_title', 'yt_desc', 'yt_tags', 'yt_privacy'));
				echo "<h3>Video Uploaded</h3><ul>";
				echo sprintf('<li>%s (%s)</li>', $status['snippet']['title'], $status['id']);
				echo '</ul>';
			}
			catch (Google_ServiceException $e)
			{
				die();
				echo sprintf('<p>A service error occurred: <code>%s</code></p>',
						htmlspecialchars($e->getMessage()));
			}
			catch (Google_Exception $e)
			{
				die();
				echo sprintf('<p>An client error occurred: <code>%s</code></p>',
						htmlspecialchars($e->getMessage()));
			}
			$this->session->set_userdata('token', $client->getAccessToken());
			// 			}
			// 			else
				// 				$this->output->set_header("Location: /printdetail/video_upload?state=" . $this->session->userdata('state') . '&code=' . $this->session->userdata('code'));
		}
		else
		{
			$this->load->helper(array('zimapi', 'corestatus'));
			$prefix = CoreStatus_checkTromboning() ? 'https://' : 'http://';
			$data = array('printersn' => ZimAPI_getSerial(), 'URL' => $prefix . $_SERVER['HTTP_HOST'] . '/printdetail/video_upload');
				
			$options = array('http' => array('header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					'method'  => 'POST',
					'content' => http_build_query($data)));
			$context = stream_context_create($options);
			@file_get_contents('https://sso.zeepro.com/url.ashx', false, $context);
			$result = substr($http_response_header[0], 9, 3);
			if ($result == 200)
			{
				//echo 'ca marche';
			}
			$state = ZimAPI_getSerial();
			$client->setState($state);
			$this->session->set_userdata('state', $state);
			$authUrl = $client->createAuthUrl();
			$this->output->set_header("Location: " . $authUrl);
			//	echo "<br /><a href='$authUrl'>click</a>";
		}
		return;
	}
}
