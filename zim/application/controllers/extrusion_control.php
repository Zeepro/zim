<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

if (!defined('PRONTERFACE_EMULATOR_LOG')) {
	define('PRONTERFACE_EMULATOR_LOG', '_emulator.log');
}

class Extrusion_control extends MY_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->helper( array(
				'url',
				'errorcode'
		) );
	}
	
	public function index() {
		global $CFG;
		$template_data = array();
		$body_page = NULL;

		$this->load->library('parser');

		// parse the main body
		$body_page = $this->parser->parse('template/extrusion_control', array(), TRUE);

		// parse all page
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>Zeepronterface</title>',
				'contents'		=> $body_page,
		);

		$this->parser->parse('template/basetemplate', $template_data);

		return;
	}
	
	public function stop() {
		$this->load->helper('printerstate');
		PrinterState_stopPrinting();
		$this->output->set_header('Location: /extrusion_control');

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
	
	public function emulator() {
		$cr = 0;
		$gcode = NULL;
		$command = '';
		$output = NULL;
		$ret_val = 0;
// 		$path_file = '';
		
		$this->load->helper(array('detectos', 'printer'));
// 		if (DectectOS_checkWindows()) {
// 			$command = 'php ' . $this->config->item('bin') . 'GCEmulator.php ' . $this->config->item('temp') . ' ';
// 		}
// 		else {
// 			$command = $this->config->item('bin') . 'GCEmulator.php p=' . $this->config->item('temp') . ' v=';
// 		}
		
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
// 				$path_file = $this->config->item('temp') . PRONTERFACE_EMULATOR_LOG;
				
				$context = stream_context_create(
						array('http' => array('ignore_errors' => TRUE))
				);
				$url = 'http://localhost:' . $_SERVER['SERVER_PORT'] . '/bin/GCEmulator.php?p='
						. $this->config->item('temp') . '&v=' . $gcode['full_path'];
				
				Printer_preparePrint();
				$response = @file_get_contents($url, FALSE, $context);
				
				if ($response === FALSE) {
					$cr = 403;
				}
				else {
					$cr = ERROR_OK;
					$this->output->set_content_type('txt_u');
					$this->load->library('parser');
					$this->parser->parse('template/plaintxt', array('display' => $response));
				}
				
// 				$command .= $gcode['full_path'] . ' > ' . $path_file;
// 				PrinterLog_logArduino($command);
// 				system($command, $ret_val);
// 				if ($ret_val != ERROR_NORMAL_RC_OK) {
// 					$this->output->set_status_header(404);
// 				}
// 				else if (!file_exists($path_file)) {
// 					$this->output->set_status_header(404);
// 				} else {
// 					$this->load->helper('file');
// 					$this->output->set_content_type(get_mime_by_extension($path_file))->set_output(@file_get_contents($path_file));
// 				}
			}
			else {
				$cr = 403;
			}
		}
		else {
			$cr = 403;
		}
		
		if ($cr != ERROR_OK) {
			$this->output->set_status_header($cr);
			$this->output->set_content_type('txt_u');
			$this->load->library('parser');
			$this->parser->parse('template/plaintxt', array('display' => $cr . MyERRMSG($cr)));
		}
		
		return;
	}
}
