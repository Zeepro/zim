<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

if (!defined('RETURN_CONTENT_TYPE')) {
	define('RETURN_CONTENT_TYPE', 'text/plain; charset=UTF-8');
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
		if (CoreStatus_checkInInitialization() || CoreStatus_checkInConnection()) {
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
			
			switch ($status_current) {
				case CORESTATUS_VALUE_PRINT:
				case CORESTATUS_VALUE_CANCEL: // we treat canceling as printing
					//TODO test here for canceling
					$this->load->helper('printerstate');
					$ret_val = PrinterState_checkInPrint();
					if ($ret_val == FALSE) {
						$ret_val = CoreStatus_setInIdle();
						if ($ret_val == TRUE) {
							$this->load->helper('printerlog');
							PrinterLog_logDebug('set idle when call print / cancel');
							return; // continue to generate if we are now in idle
						}
						$this->load->helper('printerlog');
						PrinterLog_logError('can not set status in idle');
					}
					break;
					
				case CORESTATUS_VALUE_LOAD_FILA_L:
				case CORESTATUS_VALUE_LOAD_FILA_R:
				case CORESTATUS_VALUE_UNLOAD_FILA_L:
				case CORESTATUS_VALUE_UNLOAD_FILA_R:
					// generate parameters by different status
					$abb_filament =
							(($status_current == CORESTATUS_VALUE_LOAD_FILA_L)
									|| ($status_current == CORESTATUS_VALUE_UNLOAD_FILA_L))
							? 'l' : 'r';
					$status_fin_filament =
							($status_current == CORESTATUS_VALUE_LOAD_FILA_L || $status_current == CORESTATUS_VALUE_LOAD_FILA_R)
							? TRUE : FALSE;
					
					$this->load->helper('printerstate');
					$ret_val = PrinterState_getFilamentStatus($abb_filament);
					if ($ret_val == $status_fin_filament) {
						$ret_val = CoreStatus_setInIdle();
						if ($ret_val == TRUE) {
							return; // continue to generate if we are now in idle
						}
						$this->load->helper('printerlog');
						PrinterLog_logError('can not set status into idle');
					}
					break;
					
				default:
					// log internal API error
					$this->load->helper('printerlog');
					PrinterLog_logError('unknown status in work json');
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
		echo $display; //optional
		
		return;
	}
	
	//==========================================================
	//index for status
	//==========================================================
	public function index()
	{
		$this->status();
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
			PrinterLog_logError('reset network error by REST');
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
			$this->output->set_content_type(RETURN_CONTENT_TYPE);
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
			$this->output->set_content_type(RETURN_CONTENT_TYPE);
			echo $json_data;
		}
		
		return;
	}
	
	public function listssid() {
		$display = '';
		
		$this->load->helper('zimapi');
		$display = ZimAPI_listSSID();
		
		$this->output->set_content_type(RETURN_CONTENT_TYPE);
		echo $display;
		
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
		global $CFG;
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
		$this->output->set_content_type(RETURN_CONTENT_TYPE);
// 		header('Content-type: text/plain; charset=UTF-8');
		echo $display;
		
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
				$this->output->set_content_type(get_mime_by_extension($path_pid))->set_output(file_get_contents($path_pid));
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
		$this->output->set_content_type(RETURN_CONTENT_TYPE);
		echo $display;
		
		return;
	}
	
	public function cancel() {
		$ret_val = 0;
		
		$this->load->helper('printer');
		
		$ret_val = Printer_stopPrint();
		if ($ret_val == TRUE) {
			$this->_return_cr(ERROR_OK);
		}
		else {
			$this->load->helper('printerlog');
			PrinterLog_logError('can not stop printing by REST');
			$this->_return_cr(ERROR_NO_PRINT);
		}
		
		return;
	}
	
	public function get() {
		$parameter = NULL;
		$cr = 0;
		$display = NULL;
		$api_prm = NULL;
		
		$this->load->helper('printerstate');
		
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
							$tmp_array = PrinterState_getExtruderTemperaturesAsArray();
							$cr = ERROR_OK;
							$display = ($has_v == 'l')
									? $tmp_array[PRINTERSTATE_LEFT_EXTRUD]
									: $tmp_array[PRINTERSTATE_RIGHT_EXTRUD];
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
 		echo $display;
		
		return;
	}
	
	public function set() {
		$parameter = NULL;
		$cr = 0;
		$api_prm = NULL;
		
		$this->load->helper('printerstate');
		
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
	
	//==========================================================
	//another part (end of print list)
	//==========================================================
	
	public function gcode() {
		$cr = 0;
		$gcodes = '';
		
		$this->load->helper('printerstate');
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$gcodes = $this->input->post('v');
			
			$cr = PrinterState_runGcode($gcodes);
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
			
			if (PrinterState_runGcode($array_gcode, TRUE, $return_data)) {
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
		
		$this->load->helper('printerstate');
		
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
				
				$cr = PrinterState_runGcodeFile($gcode['full_path']);
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
