<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

if (!defined('RETURN_CONTENT_TYPE')) {
	define('RETURN_CONTENT_TYPE',		'text/plain; charset=UTF-8');
	define('RETURN_CONTENT_TYPE_JSON',	'application/json; charset=UTF-8');
}

class Rest extends MY_Controller {
// 	private $finish_config = FALSE;
	
	function __construct() {
		parent::__construct ();
		$this->load->helper( array(
				'form',
				'url',
				'json',
				'errorcode',
				'corestatus',
		) );
		
		$status_current = '';
		if (CoreStatus_checkInInitialization() || CoreStatus_checkInConnection()
				|| CoreStatus_checkInUSB()) {
			// let no block REST web service go for setting network
			if (CoreStatus_checkCallNoBlockRESTInConnection()) {
				return;
			}
			
			// we haven't finished initialization or connection yet
			$cr = ERROR_BUSY_PRINTER;
			$display = $cr . " " . t(MyERRMSG($cr));
			$this->output->set_status_header($cr, $display);
// 			$this->output->set_content_type(RETURN_CONTENT_TYPE);
			header('Content-type: ' . RETURN_CONTENT_TYPE);
			echo $display; //optional
			exit;
		}
		else if (!CoreStatus_checkInIdle($status_current)) {
			// check if the status is changed
			$ret_val = 0;
			
			$this->load->helper('printerstate');
			
			switch ($status_current) {
				case CORESTATUS_VALUE_SLICE:
					if (CoreStatus_checkCallNoBlockRESTInSlice()) {
						// do not block some special REST for action in slicing
						return;
					}
					
					$ret_val = PrinterState_checkBusyStatus($status_current);
					if ($ret_val == FALSE) {
						// still in slicing
						break;
					}
					else if ($status_current == CORESTATUS_VALUE_IDLE) {
						// encounted some errors
						break;
					}
					else { // CORESTATUS_VALUE_PRINT
						$this->load->helper('printerlog');
						PrinterLog_logMessage('call rest when we are in slicing, but finished really', __FILE__, __LINE__);
					}
				
				// we treat canceling as printing
				case CORESTATUS_VALUE_PRINT:
					// do not block some special REST for action in printing
					if (CoreStatus_checkCallNoBlockRESTInPrint()) {
						return;
					}
					
				case CORESTATUS_VALUE_CANCEL:
					//TODO test here for canceling
					$ret_val = PrinterState_checkInPrint();
					if ($ret_val == FALSE) {
						$ret_val = CoreStatus_setInIdle();
						if ($ret_val == TRUE) {
							$this->load->helper('printerlog');
							PrinterLog_logDebug('set idle when calling REST', __FILE__, __LINE__);
							return; // continue to generate if we are now in idle
						}
						$this->load->helper('printerlog');
						PrinterLog_logError('can not set status in idle', __FILE__, __LINE__);
					}
					break;
					
				default:
					$ret_val = PrinterState_checkBusyStatus($status_current);
					if ($ret_val == TRUE) {
						$this->load->helper('printerlog');
						PrinterLog_logDebug('set idle when calling REST', __FILE__, __LINE__);
						return; // status has changed to idle
					}
					break;
			}
			
			// do not block some special REST
			if (CoreStatus_checkCallNoBlockREST()) {
				return;
			}
			
			// return that printer is busy
			$cr = ERROR_BUSY_PRINTER;
			$display = $cr . " " . t(MyERRMSG($cr));
			$this->output->set_status_header($cr, $display);
// 			$this->output->set_content_type(RETURN_CONTENT_TYPE);
			header('Content-type: ' . RETURN_CONTENT_TYPE);
			echo $display; //optional
			exit;
		}
// 		else {
// 			$this->finish_config = TRUE;
// 		}
	}
	
	private function _return_cr($cr) {
		$display = $cr . " " . t(MyERRMSG($cr));
		$this->output->set_status_header($cr, $display);
// 		http_response_code($cr);
		$this->output->set_content_type(RETURN_CONTENT_TYPE);
		$this->load->library('parser');
		$this->parser->parse('template/plaintxt', array('display' => $display)); //optional
		
		return;
	}
	
	private function _return_under_construction() {
		$display = 499 . " UNDER CONSTRUCTION";
		$this->output->set_status_header(499, $display);
// 		http_response_code($cr);
		$this->output->set_content_type(RETURN_CONTENT_TYPE);
		$this->load->library('parser');
		$this->parser->parse('template/plaintxt', array('display' => $display)); //optional
		
		return;
	}
	
	//==========================================================
	//index for status
	//==========================================================
	public function index()
	{
// 		$this->status();
		$this->output->set_header('Location: /rest/status');
		return;
	}
	
	public function test_dump() { //FIXME remove this function after test
		header('Content-type: ' . RETURN_CONTENT_TYPE);
		print_r(getallheaders());
		print_r($_SERVER);
		return;
	}
	
	
	//==========================================================
	//network web service
	//==========================================================
	public function resetnetwork() {
		$cr = ERROR_OK;
		
		$this->load->helper('zimapi');
		$cr = ZimAPI_resetNetwork();
		if ($cr != ERROR_OK) {
			$this->load->helper('printerlog');
			PrinterLog_logError('reset network error by REST', __FILE__, __LINE__);
		}
		
		$this->_return_cr($cr);
		
		return;
	}
	
	public function getnetwork() {
		$cr = ERROR_OK;
		$json_data = '';
		
		$this->load->helper('zimapi');
		$cr = ZimAPI_getNetwork($json_data);
		
		if ($cr != ERROR_OK) {
			$this->_return_cr($cr);
		}
		else {
			$this->output->set_status_header($cr, $json_data);
			// 		http_response_code($cr);
			$this->output->set_content_type(RETURN_CONTENT_TYPE_JSON);
			echo $json_data;
		}
		
		return;
	}
	
	public function getnetworkp2p() {
		$cr = ERROR_OK;
		$json_data = '';
		$array_json = array();
		
		$this->load->helper('zimapi');
		$cr = ERROR_OK;

		$array_json = array(
				ZIMAPI_TITLE_TOPOLOGY	=> ZIMAPI_VALUE_P2P,
				ZIMAPI_TITLE_MEDIUM		=> ZIMAPI_VALUE_WIFI,
				ZIMAPI_TITLE_SSID		=> 'zim_test_dev',
		);
		$json_data = json_encode($array_json);
		
		if ($cr != ERROR_OK) {
			$this->_return_cr($cr);
		}
		else {
			$this->output->set_status_header($cr, $json_data);
			// 		http_response_code($cr);
			$this->output->set_content_type(RETURN_CONTENT_TYPE_JSON);
			echo $json_data;
		}
		
		return;
	}
	
	public function getnetworkip() {
		$cr = ERROR_OK;
		$json_data = '';
		
		$this->load->helper('zimapi');
		$cr = ZimAPI_getNetworkIP($json_data);
		
		if ($cr != ERROR_OK) {
			$this->_return_cr($cr);
		}
		else {
			$this->output->set_status_header($cr, $json_data);
			// 		http_response_code($cr);
			$this->output->set_content_type(RETURN_CONTENT_TYPE_JSON);
			echo $json_data;
		}
		
		return;
	}
	
	public function listssid() {
		$display = '';
		
		$this->load->helper('zimapi');
		$display = ZimAPI_listSSID();
		
		$this->output->set_content_type(RETURN_CONTENT_TYPE_JSON);
// 		echo $display;
		$this->load->library('parser');
		$this->parser->parse('template/plaintxt', array('display' => $display));
		
		return;
	}
	
	public function setnetwork() {
		$cr = ERROR_OK;
		
		$string_json = $this->input->get('v');
		if ($string_json) {
			$cr = ZimAPI_setNetwork($string_json);
			$this->_return_cr($cr);
		}
		else {
			$this->_return_cr(ERROR_MISS_PRM);
		}
		
		return;
	}
	
	
	//==========================================================
	//print list web service
	//==========================================================
	public function storemodel() {
		$data = array('error'=> '');
		$upload_config = NULL;
		$api_data = array();
		$cr = 0; //return code
	
		$this->load->helper('printlist');
	
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			//validation (not file check included)
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('n', 'Modelname', 'required');
			
			if ($this->form_validation->run() == FALSE) {
				// Here is where you do stuff when the submitted form is invalid.
				$cr = ERROR_MISS_PRM;
			} else {
				$api_data['n'] = $this->input->post('n');
				if ($this->input->post('d')) {
					$api_data['d'] = $this->input->post('d');
				}
				else {
					$api_data['d'] = '{}'; // add default value
				}
				if ($this->input->post('t')) {
					$api_data['t'] = (int)$this->input->post('t');
				}
				if ($this->input->post('l1')) {
					$api_data['l1'] = (int)$this->input->post('l1');
				}
				if ($this->input->post('l2')) {
					$api_data['l2'] = (int)$this->input->post('l2');
					if ($api_data['l2'] == 0) {
						unset($api_data['l2']);
					}
				}
				if ($this->input->post('c1')) {
					$api_data['c1'] = $this->input->post('c1');
				}
				if ($this->input->post('c2')) {
					$api_data['c2'] = $this->input->post('c2');
					if ($api_data['c2'] == NULL) {
						unset($api_data['c2']);
					}
				}
				
				$upload_config = array (
						'upload_path'	=> $this->config->item('temp'),
// 						'allowed_types'	=> 'jpg|png|gcode',
	 					'allowed_types'	=> '*',
						'overwrite'		=> TRUE,
						'remove_spaces'	=> TRUE,
						'encrypt_name'	=> TRUE,
				);
				$this->load->library('upload', $upload_config);
//	 			$this->upload->initialize();
				
				//check gcode file required
				if ($this->upload->do_upload('f')) {
					//gcode file
					$api_data['f'] = $this->upload->data();
					
					//picture
					for($i=1; $i <= PRINTLIST_MAX_FILE_PIC; $i++) {
						if ($this->upload->do_upload("p$i")) {
							$api_data["p$i"] = $this->upload->data();
//						} else {
//							//treat error - optional
						}
					}
					
					//call function
					$cr = ModelList_add($api_data);
				} else {
					//treat error - missing gcode file
					$cr = ERROR_MISS_PRM;
				}
			}
		} else {
			$this->load->view('template/rest/printlist_form');
			return;
		}
		
		$this->_return_cr($cr);
		
		return;
	}
	
	public function deletemodel() {
		$mid = '';
		$cr = 0; //return code
		
		$this->load->helper('printlist');
		
		$mid = $this->input->get('id'); //return false if missing
		
		//check mid
		if ($mid) {
			//call function
			$cr = ModelList_delete($mid);
		} else {
			$cr = ERROR_MISS_PRM;
		}
		
		$this->_return_cr($cr);
		
		return;
	}
	
	public function listmodel() {
		$display = '';
		
		$this->load->helper('printlist');
		
		$display = ModelList_list();
		$this->output->set_content_type(RETURN_CONTENT_TYPE_JSON);
// 		header('Content-type: text/plain; charset=UTF-8');
// 		echo $display;
		$this->load->library('parser');
		$this->parser->parse('template/plaintxt', array('display' => $display));
		
		return;
	}
	
	public function getpicture() {
		$mid = '';
		$pid = 0;
		$url_pid = '';
		$cr = 0; //return code
		
		$this->load->helper(array('printlist', 'file'));
		
		$mid = $this->input->get('id'); //return false if missing
		$pid = (int)$this->input->get('p'); //return false if missing
		
		//check mid
		if ($mid && $pid) {
			//call function
			$cr = ModelList_getPic($mid, $pid, $path_pid);
			if ($cr == ERROR_OK) {
// 				header('Content-Length: ' . filesize($path_pid));
// 				header('Content-Type: ' . get_mime_by_extension($path_pid));
// 				header('Content-Disposition: inline; filename="img' . $pid . '";'); //filename header
// 				exit(file_get_contents($path_pid));
				$this->output->set_content_type(get_mime_by_extension($path_pid))->set_output(@file_get_contents($path_pid));
				return;
			}
		} else {
			$cr = ERROR_MISS_PRM;
		}
		
		$this->_return_cr($cr);
		
		return;
	}
	
	public function preslicedprint() {
		$mid = '';
		$cr = 0; //return code
		
		$this->load->helper('printlist');
		
		$mid = $this->input->get('id'); //return false if missing
		
		if ($mid) {
			$cr = ModelList_print($mid);
		}
		else {
			$cr = ERROR_MISS_PRM;
		}
		
		if ($cr == ERROR_OK) {
			// ALREADY FIXED BY CORESTATUS HELPER
			// change status file to indicate we are in printing now,
			// but think another condition:
			// when we have finished printing, how can we know that?
			// arcontrol client return directly, and will not infect file system with json file.
			// perhaps we have to lance a thread of PHP to check print status by arcontrol
			// time by time util the printing is finished?
			// if not, we will rely on the client to check status,
			// that means we force the client only accessing check print status page when we are in printing.
			// in that way, we can know when the printing is finished, and then change the status in json file
		}
		
		$this->_return_cr($cr);
		
		return;
	}
	
	
	//==========================================================
	//printer state web service
	//==========================================================
	public function status() {
		$display = NULL;
		
		$this->load->helper('printerstate');
		
		$display = PrinterState_checkStatus();
		$this->output->set_content_type(RETURN_CONTENT_TYPE_JSON);
// 		echo $display;
		$this->load->library('parser');
		$this->parser->parse('template/plaintxt', array('display' => $display));
		
		return;
	}
	
	public function cancel() {
		$ret_val = 0;
		$status_current = '';
		
		if (CoreStatus_checkInIdle($status_current)) {
			$this->_return_cr(ERROR_NO_PRINT);
			return;
		}
		
		if ($status_current == CORESTATUS_VALUE_SLICE) {
			$this->load->helper('slicer');
			
			$ret_val = Slicer_sliceHalt();
			if ($ret_val == ERROR_OK) {
				$this->_return_cr(ERROR_OK);
			}
			else {
				$this->load->helper('printerlog');
				PrinterLog_logError('can not stop slicing by REST', __FILE__, __LINE__);
				$this->_return_cr($ret_val);
			}
		}
		else if ($status_current == CORESTATUS_VALUE_PRINT) {
			$this->load->helper('printer');
			
			$ret_val = Printer_stopPrint();
			if ($ret_val == TRUE) {
				$this->_return_cr(ERROR_OK);
			}
			else {
				$this->load->helper('printerlog');
				PrinterLog_logError('can not stop printing by REST', __FILE__, __LINE__);
				$this->_return_cr(ERROR_NO_PRINT);
			}
		}
		else {
			$this->_return_cr(ERROR_NO_PRINT);
		}
		
		return;
	}
	
	public function suspend() {
		$ret_val = 0;
		$status_current = '';
		
		if (!CoreStatus_checkInIdle($status_current) && $status_current == CORESTATUS_VALUE_PRINT) {
			$this->load->helper('printer');

			$ret_val = Printer_pausePrint();
			if ($ret_val == TRUE) {
				$this->_return_cr(ERROR_OK);
			}
		}
		else {
			$this->_return_cr(ERROR_NO_PRINT);
		}
		
		return;
	}
	
	public function resume() {
		$ret_val = 0;
		$status_current = '';
		
		if (!CoreStatus_checkInIdle($status_current) && $status_current == CORESTATUS_VALUE_PRINT) {
			$this->load->helper('printer');

			$ret_val = Printer_resumePrint();
			if ($ret_val == TRUE) {
				$this->_return_cr(ERROR_OK);
			}
		}
		else {
			$this->_return_cr(ERROR_NO_PRINT);
		}
		
		return;
	}
	
	public function get() {
		$parameter = NULL;
		$cr = 0;
		$display = NULL;
		$api_prm = NULL;
		
		$this->load->helper(array('printerstate', 'zimapi'));
		
		$parameter = $this->input->get('p'); //return false if missing
		
		if ($parameter) {
			switch($parameter) {
				case PRINTERSTATE_PRM_EXTRUDER:
					$cr = PrinterState_getExtruder($display); //$abb_extruder
					break;
					
				case PRINTERSTATE_PRM_TEMPER:
					// check which temperature we want
					$has_e = $this->input->get('e');
					$has_h = $this->input->get('h');
					$has_v = $this->input->get('v');
					if (($has_e === FALSE) && ($has_h === FALSE)) {
						$cr = ERROR_MISS_PRM;
					}
					else if (!($has_e === FALSE) && !($has_h === FALSE)) {
						$cr = ERROR_WRONG_PRM;
					}
					else if (!($has_e === FALSE) && !($has_v === FALSE)) {
						// refuse getting data not existed for mono extruder
						if ($has_v == 'l' && PrinterState_getNbExtruder() == 1) {
							$cr = ERROR_WRONG_PRM;
						}
						else if (in_array($has_v, array('l', 'r'))) {
// 							$tmp_array = PrinterState_getExtruderTemperaturesAsArray();
// 							$cr = ERROR_OK;
// 							$display = ($has_v == 'l')
// 									? $tmp_array[PRINTERSTATE_LEFT_EXTRUD]
// 									: $tmp_array[PRINTERSTATE_RIGHT_EXTRUD];
							$cr = PrinterState_getTemperature($display, 'e', $has_v);
						}
						else {
							$cr = ERROR_WRONG_PRM;
						}
					}
					else {
						$api_prm = ($has_e === FALSE) ? 'h' : 'e';
						$cr = PrinterState_getTemperature($display, $api_prm);
					}
					break;
					
				case PRINTERSTATE_PRM_CARTRIDGE:
					$api_prm = $this->input->get('v');
					// refuse getting data not existed for mono extruder
					if ($api_prm == 'l' && PrinterState_getNbExtruder() == 1) {
						$cr = ERROR_WRONG_PRM;
					}
					else {
						$cr = PrinterState_getCartridge($display, $api_prm);
					}
					break;
					
				case PRINTERSTATE_PRM_INFO:
					$cr = ERROR_OK;
					$display = PrinterState_getInfo();
					break;
					
				case 'render':
					$this->_return_under_construction();
					return;
					break;
				case PRINTERSTATE_PRM_ACCELERATION:
					$cr = PrinterState_getAcceleration($display);
					break;
					
				case PRINTERSTATE_PRM_SPEED_MOVE:
				case PRINTERSTATE_PRM_SPEED_EXTRUDE:
					$cr = PrinterState_getSpeed($display);
					break;
					
				case PRINTERSTATE_PRM_COLDEXTRUSION:
					$value = NULL;
					$cr = PrinterState_getColdExtrusion($value);
					if ($cr == ERROR_OK) {
						if ($value == TRUE) {
							$display = 'on';
						}
						else {
							$display = 'off';
						}
					}
					break;
					
				case PRINTERSTATE_PRM_FILAMENT:
					$value = NULL;
					$api_prm = $this->input->get('v');
					// refuse getting data not existed for mono extruder
					if ($api_prm == 'l' && PrinterState_getNbExtruder() == 1) {
						$cr = ERROR_WRONG_PRM;
					}
					else if ($api_prm) {
						$cr = ERROR_OK;
						$value = PrinterState_getFilamentStatus($api_prm);
						if ($value == TRUE) {
							$display = 'true';
						}
						else {
							$display = 'false';
						}
					}
					else {
						$cr = ERROR_MISS_PRM;
					}
					break;
					
				case PRINTERSTATE_PRM_ENDSTOP:
					$status = NULL;
					$abb_endstop = $this->input->get('axis');
					
					$cr = PrinterState_getEndstop($abb_endstop, $status);
					if ($cr == ERROR_OK) {
						$display = $status ? 'on' : 'off';
					}
					break;
					
				case PRINTERSTATE_PRM_STRIPLED:
					$value = NULL;
					$cr = PrinterState_getStripLedStatus($value);
					if ($cr == ERROR_OK) {
						if ($value == TRUE) {
							$display = 'on';
						}
						else {
							$display = 'off';
						}
					}
					break;
					
				case PRINTERSTATE_PRM_HEADLED:
					$value = NULL;
					$cr = PrinterState_getTopLedStatus($value);
					
					if ($cr == ERROR_OK) {
						if ($value == TRUE) {
							$display = 'on';
						}
						else {
							$display = 'off';
						}
					}
					break;
					
				case PRINTERSTATE_PRM_OFFSET:
					$value = NULL;
					$axis = $this->input->get('axis');
					
					if ($axis) {
						$cr = PrinterState_getOffset($axis, $value);
						if ($cr == ERROR_OK) {
							$display = $value;
						}
					}
					else {
						$cr = ERROR_MISS_PRM;
					}
					break;
					
				case ZIMAPI_PRM_CAPTURE:
					$path_capture = '';
					$password = $this->input->get('password');
					
					if (!ZimAPI_checkCameraPassword($password)) {
						$cr = ERROR_WRONG_PWD;
						break;
					}
					
					$this->load->helper('file');
					if (ZimAPI_cameraCapture($path_capture)) {
						$this->output->set_content_type(get_mime_by_extension($path_capture))->set_output(@file_get_contents($path_capture));
						return;
					}
					else {
						$cr = ERROR_INTERNAL;
					}
					break;
					
				case ZIMAPI_PRM_VIDEO_MODE:
					if (ZimAPI_checkCamera($display)) {
						$cr = ERROR_OK;
					}
					else {
						$cr = ERROR_INTERNAL;
					}
					break;
					
				case ZIMAPI_PRM_PRESET:
					if (ZimAPI_getPreset($display)) {
						$cr = ERROR_OK;
					}
					break;
					
				default:
					$cr = ERROR_WRONG_PRM;
					break;
			}
		} else {
			$cr = ERROR_MISS_PRM;
		}
		
		if ($cr != ERROR_OK) {
			$display = $cr . " " . t(MyERRMSG($cr));
		}
		$this->output->set_status_header($cr, $display);
// 		http_response_code($cr);
		$this->output->set_content_type(RETURN_CONTENT_TYPE);
// 		echo $display;
		$this->load->library('parser');
		$this->parser->parse('template/plaintxt', array('display' => $display));
		
		return;
	}
	
	public function set() {
		$parameter = NULL;
		$cr = 0;
		
		$this->load->helper(array('printerstate', 'zimapi', 'slicer'));
		
		$parameter = $this->input->get('p'); //return false if missing
		
		if ($parameter) {
			switch($parameter) {
				case PRINTERSTATE_PRM_EXTRUDER:
					$api_prm = $this->input->get('v');
					
					if ($api_prm) {
						// refuse getting data not existed for mono extruder
						if ($api_prm == 'l' && PrinterState_getNbExtruder() == 1) {
							$cr = ERROR_WRONG_PRM;
						}
						else {
							$cr = PrinterState_setExtruder($api_prm);
						}
					} else {
						$cr = ERROR_MISS_PRM;
					}
					break;
					
				case PRINTERSTATE_PRM_TEMPER:
					// check which temperature we want
					$val_temper = 0;
					$api_prm = NULL;
					
					$val_temper = $this->input->get('v');
					$has_e = $this->input->get('e');
					$has_h = $this->input->get('h');
					if (($has_e === FALSE) && ($has_h === FALSE)) {
						$cr = ERROR_MISS_PRM;
					}
					else if (!($has_e === FALSE) && !($has_h === FALSE)) {
						$cr = ERROR_WRONG_PRM;
					}
					else {
						$api_prm = ($has_e === FALSE) ? 'h' : 'e';
						$cr = PrinterState_setTemperature($val_temper, $api_prm);
					}
					break;
					
				case PRINTERSTATE_PRM_ACCELERATION:
					$val_acceleration = (int)$this->input->get('v');
					if ($val_acceleration) {
						$cr = PrinterState_setAcceleration($val_acceleration);
					}
					else {
						$cr = ERROR_MISS_PRM;
					}
					break;
					
				case PRINTERSTATE_PRM_SPEED_MOVE:
				case PRINTERSTATE_PRM_SPEED_EXTRUDE:
					$val_speed = (int)$this->input->get('v');
					if ($val_speed) {
						$cr = PrinterState_setSpeed($val_speed);
					}
					else {
						$cr = ERROR_MISS_PRM;
					}
					break;
					
				case PRINTERSTATE_PRM_COLDEXTRUSION:
					$this->_return_under_construction();
					return;
					break;
					
				case PRINTERSTATE_PRM_STRIPLED:
					$status_set = $this->input->get('v');
					if ($status_set) {
						$cr = PrinterState_setStripLed($status_set);
					}
					else {
						$cr = ERROR_MISS_PRM;
					}
					break;
					
				case PRINTERSTATE_PRM_MOTOR_OFF:
					$status_set = $this->input->get('v');
					if ($status_set == 'off') {
						$cr = PrinterState_disableSteppers();
					}
					else if ($status_set) {
						$cr = ERROR_WRONG_PRM;
					}
					else {
						$cr = ERROR_MISS_PRM;
					}
					break;
					
				case PRINTERSTATE_PRM_HEADLED:
					$status_set = $this->input->get('v');
					if ($status_set) {
						$cr = PrinterState_setHeadLed($status_set);
					}
					else {
						$cr = ERROR_MISS_PRM;
					}
					break;
					
				case PRINTERSTATE_PRM_OFFSET:
					$axis = $this->input->get('axis');
					$val_offset = $this->input->get('adjustment');
					if ($axis && $val_offset !== FALSE) {
						$cr = PrinterState_setOffset(array($axis => $val_offset));
					}
					else {
						$cr = ERROR_MISS_PRM;
					}
					break;
					
				case ZIMAPI_PRM_PASSWD:
					$old_password = $this->input->get('o');
					$password = $this->input->get('v');
					
					if (ZimAPI_checkCameraPassword($old_password)) {
						if (ZimAPI_setCameraPassword($password)) {
							$cr = ERROR_OK;
						}
						else {
							$cr = ERROR_INTERNAL;
						}
					}
					else {
						$cr = ERROR_WRONG_PWD;
					}
					break;
					
				case ZIMAPI_PRM_VIDEO_MODE:
					$set_status = $this->input->get('v');
					$password = $this->input->get('s');
					$parameter = $this->input->get('m');
					
					if (!ZimAPI_checkCameraPassword($password)) {
						$cr = ERROR_WRONG_PWD;
						break;
					}
					if ($set_status && $set_status == 'off') {
						if (ZimAPI_cameraOff()) {
							$cr = ERROR_OK;
						}
						else {
							$cr = ERROR_INTERNAL;
						}
					}
					else if ($set_status && $set_status == 'on') {
						// temporary change - jump out of verification if we have no parameter 20140811
						if (!$parameter) {
							$parameter = ZIMAPI_PRM_CAMERA_PRINTSTART;
						}
							if (ZimAPI_cameraOn($parameter)) {
								$cr = ERROR_OK;
							}
							else {
								$cr = ERROR_INTERNAL;
							}
// 						}
// 						else {
// 							$cr = ERROR_MISS_PRM;
// 						}
					}
					else if ($set_status) {
						$cr = ERROR_WRONG_PRM;
					}
					else {
						$cr = ERROR_MISS_PRM;
					}
					break;
					
				case ZIMAPI_PRM_PRESET:
					$id_preset = $this->input->get('id');
					
					if ($id_preset) {
						$cr = ZimAPI_setPreset($id_preset);
					}
					else {
						$cr = ERROR_MISS_PRM;
					}
					break;
					
				case SLICER_PRM_PRM:
					$density = $this->input->get('density');
					$skirt = $this->input->get('skirt');
					$raft = $this->input->get('raft');
					$support = $this->input->get('support');
					
					$array_setting = array();
					if ($density !== FALSE) {
						$density = (int)$density / 100;
						if ($density <= 0 || $density >= 1) {
							$cr = ERROR_MISS_PRM;
							break;
						}
						$array_setting['fill_density'] = $density;
					}
					if ($skirt !== FALSE) {
						$array_setting['skirts'] = ((int)$skirt == 1) ? 1 : 0;
					}
					if ($raft !== FALSE) {
						$array_setting['raft_layers'] = ((int)$raft == 1) ? 1 : 0;
					}
					if ($support !== FALSE) {
						$array_setting['support_material'] = ((int)$support == 1) ? 1 : 0;
					}
					if (count($array_setting) == 0) {
						$cr = ERROR_MISS_PRM;
					}
					else {
						$cr = Slicer_changeParameter($array_setting);
					}
					break;
					
				case PRINTERSTATE_PRM_SSO_NAME:
					$name = $this->input->get('name');
					
					if ($name) {
						$cr = ZimAPI_setPrinterSSOName($name);
					}
					else {
						$cr = ERROR_MISS_PRM;
					}
					break;
					
				default:
					$cr = ERROR_WRONG_PRM;
					break;
			}
		} else {
			$cr = ERROR_MISS_PRM;
		}
		
		$this->_return_cr($cr);
		
		return;
	}
	
	public function home() {
		$cr = 0;
		$abb_axis = $this->input->get('a');
		
		$this->load->helper('printerstate');
		if ($abb_axis) {
			$cr = PrinterState_homing($abb_axis);
		}
		else {
			$cr = PrinterState_homing('all');
		}
		
		$this->_return_cr($cr);
		
		return;
	}
	
	public function move() {
		$cr = 0;
		$abb_axis = $this->input->get('a');
		$distance_axis = $this->input->get('v');
		
		$this->load->helper('printerstate');
		if ($abb_axis) {
			if ($distance_axis === FALSE) {
				$cr = ERROR_MISS_PRM;
			}
			else {
				$cr = PrinterState_move($abb_axis, (float)$distance_axis);
			}
		}
		else {
			$cr = ERROR_MISS_PRM;
		}
		
		$this->_return_cr($cr);
		
		return;
	}
	
	public function extrude() {
		$cr = 0;
		$distance = $this->input->get('v');
		
		$this->load->helper('printerstate');
		if ($distance) {
			$cr = PrinterState_extrude((float)$distance);
		}
		else {
			$cr = PrinterState_extrude();
		}
		
		$this->_return_cr($cr);
		
		return;
	}
	
	public function prime() {
		$cr = 0;
		$abb_cartridge = $this->input->get('v');
		$type = $this->input->get('t');
		
		$this->load->helper('printer');
		
		if ($abb_cartridge && $type) {
			$first_run = TRUE;
			
			switch ($abb_cartridge) {
				case 'l':
				case 'r':
					switch ($type) {
						case 's':
							$first_run = FALSE;
						case 'f':
							$cr = ERROR_OK;
							break;
							
						default:
							$cr = ERROR_WRONG_PRM;
							break;
					}
					break;
					
				default:
					$cr = ERROR_WRONG_PRM;
					break;
			}
			if ($cr == ERROR_OK) {
				$cr = Printer_printFromPrime($abb_cartridge, $first_run);
			}
		}
		
		$this->_return_cr($cr);
		
		return;
	}
	
	public function load() {
		$cr = 0;
		$array_cartridge = array();
		$abb_cartridge = $this->input->get('v');
		
		$this->load->helper('printerstate');
		
		if ($abb_cartridge) {
			switch ($abb_cartridge) {
				case 'l':
				case 'r':
					$cr = PrinterState_getCartridgeAsArray($array_cartridge, $abb_cartridge);
					if ($cr != ERROR_OK) {
						break;
					} 
					$cr = PrinterState_loadFilament($abb_cartridge);
					break;
					
				default:
					$cr = ERROR_WRONG_PRM;
					break;
			}
		}
		else {
			$cr = ERROR_MISS_PRM;
		}
		
		$this->_return_cr($cr);
		
		return;
	}
	
	public function unload() {
		$cr = 0;
		$abb_cartridge = $this->input->get('v');
		
		$this->load->helper('printerstate');
		
		if ($abb_cartridge) {
			switch ($abb_cartridge) {
				case 'l':
				case 'r':
// 					$temper = 0;
// 					$cr = PrinterState_getTemperature($temper, 'e', $abb_cartridge);
// 					if ($cr == ERROR_OK && $temper > PRINTERSTATE_VALUE_MAXTEMPER_BEFORE_UNLOAD) {
						$cr = PrinterState_unloadFilament($abb_cartridge);
// 					}
// 					else if ($cr == ERROR_OK) {
// 						$cr = ERROR_BUSY_PRINTER;
// 					}
					break;
					
				default:
					$cr = ERROR_WRONG_PRM;
					break;
			}
		}
		else {
			$cr = ERROR_MISS_PRM;
		}
		
		$this->_return_cr($cr);
		
		return;
	}
	
	public function raiseplatform() {
		$cr = PrinterState_raisePlatform();
		
		$this->_return_cr($cr);
		
		return;
	}
	
	public function slicerlistpreset() {
		$display = '';
		
		$this->load->helper('zimapi');
		
		$display = ZimAPI_getPresetList();
		$this->output->set_content_type(RETURN_CONTENT_TYPE_JSON);
// 		header('Content-type: text/plain; charset=UTF-8');
// 		echo $display;
		$this->load->library('parser');
		$this->parser->parse('template/plaintxt', array('display' => $display));
		
		return;
	}
	
	//==========================================================
	//platform web service
	//==========================================================
	public function getmodel() {
		$cr = 0;
		$id_model = $this->input->get('id');
		
		if ($id_model !== FALSE) {
			$path_model = '';
			
			$this->load->helper('slicer');
			$cr = Slicer_getModelFile($id_model, $path_model);
			
			if ($cr != ERROR_OK) {
				$this->_return_cr($cr);
			}
			else {
				$this->load->helper('file');
				$this->output->set_content_type(get_mime_by_extension($path_model))->set_output(@file_get_contents($path_model));
			}
		}
		else {
			$this->_return_cr(ERROR_MISS_PRM);
		}
		
		return;
	}
	
	public function level() {
		$this->_return_under_construction();
		return;
	}
	
	public function platformmodel() {
		$cr = 0;
		$display = '';
		
		$this->load->helper('slicer');
		$cr = Slicer_listModel($display);
		
		if ($cr != ERROR_OK) {
			$this->_return_cr($cr);
		}
		else {
			$this->load->library('parser');
			$this->output->set_status_header($cr, $display);
			$this->output->set_content_type(RETURN_CONTENT_TYPE);
			$this->parser->parse('template/plaintxt', array('display' => $display));
		}
		
		return;
	}
	
	public function setmodel() {
		$cr = 0;
		$array_data = NULL;
		
		$this->load->helper('slicer');
		$array_data = array(
				SLICER_PRM_ID		=> $this->input->get('id'),
				SLICER_PRM_XPOS		=> $this->input->get('xpos'),
				SLICER_PRM_YPOS		=> $this->input->get('ypos'),
				SLICER_PRM_ZPOS		=> $this->input->get('zpos'),
				SLICER_PRM_XROT		=> $this->input->get('xrot'),
				SLICER_PRM_YROT		=> $this->input->get('yrot'),
				SLICER_PRM_ZROT		=> $this->input->get('zrot'),
				SLICER_PRM_SCALE	=> $this->input->get('s'),
				SLICER_PRM_COLOR	=> $this->input->get('c'),
		);
		
		// check missing parameter
		$cr = ERROR_OK;
		foreach ($array_data as $value) {
			if ($value === FALSE) {
				$cr = ERROR_MISS_PRM;
				break;
			}
		}
		// if we pass to allow part of parameter
// 		$cr = ERROR_MISS_PRM;
// 		foreach ($array_data as $key => $value) {
// 			if ($value === FALSE) {
// 				if ($key == SLICER_PRM_ID) {
// 					break;
// 				}
// 			}
// 			else if ($key != SLICER_PRM_ID) {
// 				$cr = ERROR_OK;
// 				break;
// 			}
// 		}
		
		if ($cr != ERROR_MISS_PRM) {
			$cr = Slicer_setModel($array_data);
		}
		
		$this->_return_cr($cr);
// 		$this->_return_under_construction();
		
		return;
	}
	
	public function upload() {
		$cr = 0;
		$model = NULL;
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$upload_config = array (
					'upload_path'	=> $this->config->item('temp'),
					'allowed_types'	=> '*',
// 					'allowed_types'	=> 'gcode',
					'overwrite'		=> TRUE,
					'remove_spaces'	=> TRUE,
					'encrypt_name'	=> TRUE,
			);
			$this->load->library('upload', $upload_config);
			
			if ($this->upload->do_upload('f')) {
				$model = $this->upload->data();
				$model_path = $model['full_path'];
				
				$this->load->helper('slicer');
				$cr = Slicer_addModel(array($model_path));
			}
			else if ($this->upload->do_upload('s1')) {
				$array_model = array();
				$first_combine = TRUE;
				$model = $this->upload->data();
				$array_model[] = $model['full_path'];
				
				$cr = ERROR_OK;
				foreach (array('s2') as $file_key) {
					if ($this->upload->do_upload($file_key)) {
						$first_combine = FALSE;
						$model = $this->upload->data();
						$array_model[] = $model['full_path'];
					}
					else if ($first_combine == TRUE) {
						$cr = ERROR_MISS_PRM;
						break;
					}
				}
				if ($cr == ERROR_OK) {
					$this->load->helper('slicer');
					$cr = Slicer_addModel($array_model);
				}
			}
			else {
				// treat error - missing gcode file
				$cr = ERROR_MISS_PRM;
			}
			
			$this->_return_cr($cr);
		}
		else {
			$this->load->view('template/rest/model_form');
		}
		
		return;
	}
	
	public function removemodel() {
		$cr = 0;
		$id_model = $this->input->get('id');
		
		$this->load->helper('slicer');
		if ($id_model !== FALSE) {
			$cr = Slicer_removeModel($id_model);
		}
		else {
			$cr = ERROR_MISS_PRM;
		}
		
		$this->_return_cr($cr);
		
		return;
	}
	
	public function slice() {
		$cr = 0;
		$array_cartridge = array();
		$this->load->helper('slicer');
		
		$cr = Slicer_reloadPreset();
		
		// check platform and filament present (do not check filament quantity)
		if ($cr == ERROR_OK) {
			$cr = Slicer_checkPlatformColor($array_cartridge);
		}
		
		if ($cr == ERROR_OK) {
			$cr = Slicer_changeTemperByCartridge($array_cartridge);
		}
		
		// start slice command after checking filament
		if ($cr == ERROR_OK) {
			$cr = Slicer_slice();
		}
		
		$this->_return_cr($cr);
		
		return;
	}
	
	public function platformprint() {
		$cr = 0;
		
		$this->load->helper('printer');
		$cr = Printer_printFromSlice();
		
		$this->_return_cr($cr);
		
		return;
	}
	
	//==========================================================
	//debug part
	//==========================================================
	public function gcode() {
		$cr = 0;
		$gcodes = '';
		
		$this->load->helper('printerstate');
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$gcodes = $this->input->post('v');
			$mode = $this->input->post('mode');
			
			if ($mode == 'verbatim') {
				$cr = PrinterState_runGcode($gcodes, FALSE);
			}
			else {
				$cr = PrinterState_runGcode($gcodes);
			}
			if ($cr == TRUE) {
				$cr = ERROR_OK;
			}
			else {
				$cr = ERROR_INTERNAL;
			}
			
			$this->_return_cr($cr);
		}
		else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			$array_gcode = array();
			$gcodes = $this->input->get('v');
			if ($gcodes) {
				$array_gcode = explode("\t", $gcodes);
			}
			else {
				$array_gcode = array();
			}
			$return_data = '';
			
			if (PrinterState_runGcode($array_gcode, TRUE, TRUE, $return_data)) {
				print $return_data;
				$this->output->set_content_type(RETURN_CONTENT_TYPE);
			}
			else {
				$this->_return_cr(ERROR_INTERNAL);
			}
		}
		
		return;
	}
	
	public function gcodefile() {
		$cr = 0;
		$gcode = NULL;
		$mode = '';
		$rewrite = TRUE;
		
		$this->load->helper('printerstate');
		
		$mode = $this->input->post('mode');
		if ($mode == 'verbatim') {
			$rewrite = FALSE;
		}
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$upload_config = array (
					'upload_path'	=> $this->config->item('temp'),
					'allowed_types'	=> '*',
// 					'allowed_types'	=> 'gcode',
					'overwrite'		=> TRUE,
					'remove_spaces'	=> TRUE,
					'encrypt_name'	=> TRUE,
			);
			$this->load->library('upload', $upload_config);
			
			if ($this->upload->do_upload('f')) {
				$gcode = $this->upload->data();
				
				$cr = PrinterState_runGcodeFile($gcode['full_path'], $rewrite);
				if ($cr == TRUE) {
					$cr = ERROR_OK;
				}
				else {
					$cr = ERROR_INTERNAL;
				}
			} else {
				// treat error - missing gcode file
				$cr = ERROR_MISS_PRM;
			}
			
			$this->_return_cr($cr);
		}
		else {
			$this->load->view('template/rest/gcodefile_form');
		}
		
		return;
	}
}
