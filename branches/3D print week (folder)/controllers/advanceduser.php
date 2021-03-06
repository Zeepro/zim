<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

if (!defined('PRONTERFACE_EMULATOR_LOG')) {
	define('PRONTERFACE_EMULATOR_LOG', '_emulator.log');
}

class Advanceduser extends MY_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->helper( array(
				'url',
				'errorcode'
		) );
	}
	
	public function index() {
		global $CFG;

		$this->load->library('parser');
		
		if (file_exists($CFG->config['conf'] . '/G-code.json')) {
			$this->load->helper(array('printerstate', 'zimapi'));
			
			$temp_info = PrinterState_getInfoAsArray();
			$template_data = array('serial' => $temp_info[PRINTERSTATE_TITLE_SERIAL], 'err_msg' => '');
			
			$this->_parseBaseTemplate('Advanced user', $this->parser->parse('advanceduser', $template_data, TRUE));
		} else {
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$this->load->helper(array('printerstate', 'zimapi'));
				
				$temp_info = PrinterState_getInfoAsArray();
				
				if (strtoupper($this->input->post('serial')) != strtoupper($temp_info[PRINTERSTATE_TITLE_SERIAL])) {
					$template_data = array('err_msg' => 'Incorrect serial number');
					
					$this->_parseBaseTemplate('Advanced user', $this->parser->parse('advanceduserregister', $template_data, TRUE));
				} else {

					$url = 'https://stat.service.zeepro.com/log.ashx';
					$data = array('printersn' => $temp_info[PRINTERSTATE_TITLE_SERIAL], 'version' => '1', 'category' => 'gcode', 'action' => 'register');
					$options = array('http' => array('header'  => "Content-type: application/x-www-form-urlencoded\r\n",
							'method'  => 'POST',
							'content' => http_build_query($data)));
					$context  = stream_context_create($options);
					@file_get_contents($url, false, $context);
					$result = substr($http_response_header[0], 9, 3);
					if ($result != 200) {
						$template_data = array('err_msg' => 'Can\'t connect to the Internet');
						
						$this->_parseBaseTemplate('Advanced user', $this->parser->parse('advanceduserregister', $template_data, TRUE));
					} else {

						$fp = fopen($CFG->config['conf'] . '/G-code.json', 'w');
						if ($fp) {
							fwrite($fp, json_encode(array('register' => date("c"))));
							fclose($fp);

							$template_data = array('err_msg' => '');

							$this->_parseBaseTemplate('Advanced user', $this->parser->parse('advanceduser', $template_data, TRUE));
						} else {
							$template_data = array('err_msg' => 'Internal error');
							
							$this->_parseBaseTemplate('Advanced user', $this->parser->parse('advanceduserregister', $template_data, TRUE));
						}
					}
				}
			} else {
				$template_data = array('err_msg' => '');

				$this->_parseBaseTemplate('Advanced user', $this->parser->parse('advanceduserregister', $template_data, TRUE));
			}
		}
		return;
	}
	
	public function stop() {
		$this->load->helper('printerstate');
		PrinterState_stopPrinting();
		$this->output->set_header('Location: /advanceduser');

		return;
	}
	
	public function move() {
		$axis = $this->input->get('axis');
		$value = $this->input->get('value');
		$speed = $this->input->get('speed');
		
		if ($axis === FALSE || $value === FALSE || $speed === FALSE
				|| ((float)$value == 0) || ((int)$speed == 0)) {
			$this->output->set_status_header(403);
			return;
		}
		else {
			$cr = 0;
			
			$this->load->helper(array('printerstate', 'errorcode'));
			
			$axis = strtoupper($axis);
			$cr = PrinterState_relativePositioning(TRUE);
			if ($cr == ERROR_OK) {
				$cr = PrinterState_move($axis, (float)$value, (int)$speed);
			}
			if ($cr == ERROR_OK) {
				$cr = PrinterState_relativePositioning(FALSE);
			}
			if ($cr == ERROR_OK) {
				$this->output->set_status_header(200);
				return;
			}
		}
		
		$this->output->set_status_header(403);
		return;
	}
	
	public function extrude($extruder = NULL, $value = NULL, $speed = NULL) {
		if (is_null($extruder) || is_null($value) || is_null($speed)
				|| ((int)$value == 0) || ((int)$speed == 0)) {
			$this->output->set_status_header(403);
			return;
		}
		else {
			$cr = 0;
			
			$this->load->helper(array('printerstate', 'errorcode'));
			
			$cr = PrinterState_setExtruder($extruder);
			if ($cr == ERROR_OK) {
				$cr = PrinterState_move('E', (int)$value, (int)$speed);
			}
			if ($cr == ERROR_OK) {
				$this->output->set_status_header(200);
				return;
			}
		}
		
		$this->output->set_status_header(403);
		return;
	}
	
	public function home($axis = 'ALL') {
		$cr = 0;
		
		$this->load->helper(array('printerstate', 'errorcode'));
		$axis = strtoupper($axis);
		$cr = PrinterState_homing($axis);
		if ($cr == ERROR_OK) {
			$this->output->set_status_header(200);
			return;
		}
		
		$this->output->set_status_header(403);
		return;
	}
	
	public function level($point = NULL) {
		$cr = 0;
		$array_cmd = array();
		
		if (is_null($point)) {
			$this->output->set_status_header(403);
			return;
		}
		
		$this->load->helper(array('printerstate', 'errorcode'));
		$cr = PrinterState_relativePositioning(FALSE);
		if ($cr != ERROR_OK) {
			$point = 'error';
		}
		switch ($point) {
			case 'center':
				$array_cmd = array(
						'X'	=> 75,
						'Y'	=> 75,
				);
				break;
				
			case 'xmin_ymin':
				$array_cmd = array(
						'X'	=> 0,
						'Y'	=> 0,
				);
				break;
				
			case 'xmin_ymax':
				$array_cmd = array(
						'X'	=> 0,
						'Y'	=> 150,
				);
				break;
				
			case 'xmax_ymax':
				$array_cmd = array(
						'X'	=> 150,
						'Y'	=> 150,
				);
				break;
				
			case 'xmax_ymin':
				$array_cmd = array(
						'X'	=> 150,
						'Y'	=> 0,
				);
				break;
				
			default:
				$this->output->set_status_header(403);
				return;
				break; // never reach here
		}
		
		foreach ($array_cmd as $axis => $value) {
			$cr = PrinterState_move($axis, $value, 2000);
			if ($cr != ERROR_OK) {
				$this->output->set_status_header(403);
				return;
			}
		}
		
		$this->output->set_status_header(200);
		return;
	}
	
	public function heat($extruder = NULL, $temper = NULL) {
		$cr = 0;
		
		if (is_null($extruder) || is_null($temper)) {
			$this->output->set_status_header(403);
			return;
		}
		
		$this->load->helper(array('printerstate', 'errorcode'));
		$cr = PrinterState_setExtruder($extruder);
		if ($cr == ERROR_OK) {
			$cr = PrinterState_setTemperature($temper);
		}
		if ($cr == ERROR_OK) {
			$this->output->set_status_header(200);
			return;
		}
		
		$this->output->set_status_header(403);
		return;
	}
	
	public function gcodefile() {
		$cr = 0;
		$gcode = NULL;
		$mode = '';
		$rewrite = TRUE;
		
		$this->load->library('parser');
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
					$this->_parseBaseTemplate('Advanced user', $this->parser->parse('advanceduserconfirm', array(), TRUE));
				}
				else {
					$template_data = array('err_msg' => 'Internal error');
					
					$this->_parseBaseTemplate('Advanced user', $this->parser->parse('advanceduser', $template_data, TRUE));
				}
			} else {
				$template_data = array('err_msg' => 'Missing file');
				
				$this->_parseBaseTemplate('Advanced user', $this->parser->parse('advanceduser', $template_data, TRUE));
			}
		}
		else {
			$this->output->set_header('Location: /');
		}
		
		return;
	}
	
	public function temper_ajax() {
		$array_temper = 0;
		
		$this->load->helper(array('printerstate', 'errorcode'));
		$array_temper = PrinterState_getExtruderTemperaturesAsArray();
		if (count($array_temper)) {
			if (isset($array_temper[PRINTERSTATE_LEFT_EXTRUD])) {
				$array_temper['left'] = $array_temper[PRINTERSTATE_LEFT_EXTRUD];
				unset($array_temper[PRINTERSTATE_LEFT_EXTRUD]);
			}
			if (isset($array_temper[PRINTERSTATE_RIGHT_EXTRUD])) {
				$array_temper['right'] = $array_temper[PRINTERSTATE_RIGHT_EXTRUD];
				unset($array_temper[PRINTERSTATE_RIGHT_EXTRUD]);
			}
			
			$this->output->set_status_header(200);
			print json_encode($array_temper);
			return;
		}
		
		$this->output->set_status_header(403);
		return;
	}
	
	public function rfid_ajax() {
		$array_rfid = array();
		$array_cmd = array();
		
		$this->load->helper(array('printerstate', 'errorcode'));
		if (PrinterState_getNbExtruder() >= 2) {
			$array_cmd['left'] = 'l';
		}
		$array_cmd['right'] = 'r';
		foreach($array_cmd as $key => $value) {
			$tmp_rfid = NULL;
			$cr = PrinterState_getCartridgeCode($tmp_rfid, $value);
			if ($cr != ERROR_OK) {
				$this->output->set_status_header(403);
				return;
			}
			else if (is_null($tmp_rfid)) {
				$tmp_rfid = 'EMPTY';
			}
			$array_rfid[$key] = $tmp_rfid;
		}
				
		$this->output->set_status_header(200);
		print json_encode($array_rfid);
		return;
	}
	
	// not in use
// 	public function emulator() {
// 		$cr = 0;
// 		$gcode = NULL;
// 		$command = '';
// 		$output = NULL;
// 		$ret_val = 0;
// // 		$path_file = '';
		
// 		$this->load->helper(array('detectos', 'printer'));
// // 		if (DectectOS_checkWindows()) {
// // 			$command = 'php ' . $this->config->item('bin') . 'GCEmulator.php ' . $this->config->item('temp') . ' ';
// // 		}
// // 		else {
// // 			$command = $this->config->item('bin') . 'GCEmulator.php p=' . $this->config->item('temp') . ' v=';
// // 		}
		
// 		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
// 			$upload_config = array (
// 					'upload_path'	=> $this->config->item('temp'),
// 					'allowed_types'	=> '*',
// // 					'allowed_types'	=> 'gcode',
// 					'overwrite'		=> TRUE,
// 					'remove_spaces'	=> TRUE,
// 					'encrypt_name'	=> TRUE,
// 			);
// 			$this->load->library('upload', $upload_config);
			
// 			if ($this->upload->do_upload('f')) {
// 				$gcode = $this->upload->data();
// // 				$path_file = $this->config->item('temp') . PRONTERFACE_EMULATOR_LOG;
				
// 				$context = stream_context_create(
// 						array('http' => array('ignore_errors' => TRUE))
// 				);
// 				$url = 'http://localhost:' . $_SERVER['SERVER_PORT'] . '/bin/GCEmulator.php?p='
// 						. $this->config->item('temp') . '&v=' . $gcode['full_path'];
				
// 				Printer_preparePrint();
// 				$response = @file_get_contents($url, FALSE, $context);
				
// 				if ($response === FALSE) {
// 					$cr = 403;
// 				}
// 				else {
// 					$cr = ERROR_OK;
// 					$this->output->set_content_type('txt_u');
// 					$this->load->library('parser');
// 					$this->parser->parse('plaintxt', array('display' => $response));
// 				}
				
// // 				$command .= $gcode['full_path'] . ' > ' . $path_file;
// // 				PrinterLog_logArduino($command);
// // 				system($command, $ret_val);
// // 				if ($ret_val != ERROR_NORMAL_RC_OK) {
// // 					$this->output->set_status_header(404);
// // 				}
// // 				else if (!file_exists($path_file)) {
// // 					$this->output->set_status_header(404);
// // 				} else {
// // 					$this->load->helper('file');
// // 					$this->output->set_content_type(get_mime_by_extension($path_file))->set_output(@file_get_contents($path_file));
// // 				}
// 			}
// 			else {
// 				$cr = 403;
// 			}
// 		}
// 		else {
// 			$cr = 403;
// 		}
		
// 		if ($cr != ERROR_OK) {
// 			$this->output->set_status_header($cr);
// 			$this->output->set_content_type('txt_u');
// 			$this->load->library('parser');
// 			$this->parser->parse('plaintxt', array('display' => $cr . MyERRMSG($cr)));
// 		}
		
// 		return;
// 	}
}
