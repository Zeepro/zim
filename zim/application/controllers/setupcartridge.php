<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

if (!defined('SETUPCARTRIDGE_JSON_FILENAME')) {
	define('SETUPCARTRIDGE_JSON_FILENAME', 'Cartridgetype.json');
}

class Setupcartridge extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->helper( array(
				'printerstate',
				'json'
		) );
	}
	
	private function _get_final_code(&$code, $year, $month, $day, $prefix) {
		$time_code = 0;
		$time_offset = 0;
		$time_rfid = 0;
		$string_tmp = NULL;
		$hex_cal = 0;
		$hex_tmp = 0;
		
		if (strlen($prefix) != 26
				|| $year * $month * $day == 0) {
			$this->load->helper('printerlog');
			PrinterLog_logError('user input error', __FILE__, __LINE__);
			return FALSE;
		}
		$code = $prefix;
		
		// package date
		$time_code = gmmktime(0, 0, 0, $month, $day, $year);
		$time_offset = gmmktime(0, 0, 0, 1, 1, PRINTERSTATE_OFFSET_YEAR_SETUP_DATE);
		$time_rfid = ($time_code - $time_offset) / 60 / 60 / 24;
		$string_tmp = strtoupper(dechex($time_rfid));
		while (strlen($string_tmp) < 4) {
			$string_tmp = '0' . $string_tmp;
		}
		$code .= $string_tmp;
		
		// Checksum
		for($i=0; $i<=14; $i++) {
			$string_tmp = substr($code, $i*2, 2);
			$hex_tmp = hexdec($string_tmp);
			$hex_cal = $hex_cal ^ $hex_tmp;
		}
		$hex_cal = strtoupper(dechex($hex_cal));
		if (strlen($hex_cal) == 1) {
			$hex_cal = '0' . $hex_cal;
		}
		$code .= $hex_cal;
		
		return TRUE;
	}
	
	private function _get_type_array(&$array_type) {
		$cr = FALSE;
		$array_tmp = array();
		$array_data = array();
		$filename = $this->config->item('conf') . SETUPCARTRIDGE_JSON_FILENAME;

		$array_type = array();
		if (file_exists($filename)) {
			$array_tmp = json_read($filename, TRUE);
			if (isset($array_tmp['error'])) {
				$this->load->helper('printerlog');
				PrinterLog_logError('read json error', __FILE__, __LINE__);
			}
			else {
				$array_data = $array_tmp['json'];
				foreach ($array_data as $ele_type) {
					if (is_array($ele_type)
							&& array_key_exists('name', $ele_type)
							&& array_key_exists('code', $ele_type)) {
						$array_type[] = $ele_type;
					}
					else {
						$this->load->helper('printerlog');
						PrinterLog_logError('json structure error', __FILE__, __LINE__);
						
						return $cr;
					}
				}
				if (count($array_type) == 0) {
					$this->load->helper('printerlog');
					PrinterLog_logMessage('json file is empty', __FILE__, __LINE__);
				}
				else {
					$cr = TRUE;
				}
			}
		}
		else {
			$this->load->helper('printerlog');
			PrinterLog_logMessage('json file not found', __FILE__, __LINE__);
		}
		
		return $cr;
	}
	
	public function index() {
// 		$code = NULL;
		
// 		if ($this->_get_final_code($code, 2014, 2, 1, '5C1200FFFFFF1FBD000011A064')) {
// 			print $code;
// 		}
		$this->output->set_header('Location: /setupcartridge/input');
		
		return;
	}
	
	public function right() {
		$this->output->set_header('Location: /setupcartridge/input?v=r');
	}
	
	public function left() {
		$this->output->set_header('Location: /setupcartridge/input?v=l');
	}
	
	public function write() {
		$cr = 0;
		$code_write = NULL;
		$code_read = NULL;
		$template_data = array();
		$body_page = NULL;
		$button_html = '<input type="submit" value="ok 确认">';
		$type = $this->input->post('type');
		$year = (int)($this->input->post('year'));
		$month = (int)($this->input->post('month'));
		$day = (int)($this->input->post('day'));
		$times = (int)($this->input->post('times'));
		$side = $this->input->post('side');
		
		if ($side != 'l') {
			$side = 'r';
		}
		
		if ($times > 0
				&& $this->_get_final_code($code_write, $year, $month, $day, $type)) {
			$cr = PrinterState_setCartridgeCode($code_write, $side, FALSE);
			if ($cr == ERROR_OK) {
				$power_off = FALSE;
				if ($times == 1) {
					$power_off = TRUE;
				}
				$cr = PrinterState_getCartridgeCode($code_read, $side, $power_off);
				if ($cr == ERROR_OK) {
					if ($code_write != $code_read) {
						$cr = ERROR_WRONG_FORMAT;
						$this->load->helper('printerlog');
						PrinterLog_logError('write tag not correspond', __FILE__, __LINE__);
					}
					else {
						--$times;
					}
				}
			}
		}
		else {
			$cr = ERROR_INTERNAL;
			$this->load->helper('printerlog');
			PrinterLog_logError('user input error', __FILE__, __LINE__);
		}
		
		if (in_array($cr, array(ERROR_OK, ERROR_WRONG_FORMAT))) {
			$this->load->library('parser');
			$template_data = array(
					'type'	=> $type,
					'year'	=> $year,
					'month'	=> $month,
					'day'	=> $day,
					'times'	=> $times,
					'side'	=> $side,
					'ok'	=> NULL,
					'ko'	=> NULL,
			);
			$template_data['image'] = '/images/' . (($cr == ERROR_OK) ? 'right.png' : 'wrong.png');
			if ($cr == ERROR_OK) {
				$template_data['hint'] = ($times == 0)
						? 'All tags written 全部标签已写完' : 'Tag written successfully 标签已成功写入';
				$template_data['ok'] = $button_html;
			}
			else {
				$template_data['hint'] = 'Error 标签写入错误';
				$template_data['ko'] = $button_html;
			}
			
			$body_page = $this->parser->parse('template/setupcartridge/write', $template_data, TRUE);
			
			// parse all page
			$template_data = array(
					'lang'			=> 'en',
					'headers'		=> '<title>SetupCartridge 设置标签</title>',
					'contents'		=> $body_page,
			);
			$this->parser->parse('template/basetemplate', $template_data);
		}
		else {
			$this->output->set_header('Location: /setupcartridge/input?v=' . $side);
		}
		
		return;
	}
	
	public function wait() {
		$name = NULL;
		$array_type = array();
		$type = $this->input->post('type');
		$year = (int)($this->input->post('year'));
		$month = (int)($this->input->post('month'));
		$day = (int)($this->input->post('day'));
		$times = (int)($this->input->post('times'));
		$side = $this->input->post('side');
		$hint_left = 'Attention, you are trying to write on left side 注意，您正在尝试写入左侧墨盒';
		
		if ($side != 'l') {
			$side = 'r';
		}
		
		if ($this->_get_type_array($array_type)) {
			$cr = FALSE;
			$template_data = array();
			$body_page = NULL;
			
			foreach($array_type as $ele_type) {
				if ($ele_type['code'] == $type) {
					$cr = TRUE;
					$name = $ele_type['name'];
					break;
				}
			}
			
			if ($cr) {
				if ($times == 0) {
					$this->output->set_header('Location: /setupcartridge/input?v=' . $side);
					
					return;
				}
				else if ($year * $month * $day == 0) {
					$this->load->helper('printerlog');
					PrinterLog_logError('user input error', __FILE__, __LINE__);
					$this->output->set_header('Location: /setupcartridge/input?v=' . $side);
					
					return;
				}
				
				// all right to wait for writing
				$this->load->library('parser');
				$template_data = array(
						'type'	=> $type,
						'year'	=> $year,
						'month'	=> $month,
						'day'	=> $day,
						'times'	=> $times,
						'name'	=> $name,
						'side'	=> $side,
						'hint'	=> ($side == 'l') ? $hint_left : NULL,
				);
				
				$body_page = $this->parser->parse('template/setupcartridge/wait', $template_data, TRUE);
				
				// parse all page
				$template_data = array(
						'lang'			=> 'en',
						'headers'		=> '<title>SetupCartridge 设置标签</title>',
						'contents'		=> $body_page,
				);
				$this->parser->parse('template/basetemplate', $template_data);
			}
			else {
				$this->load->helper('printerlog');
				PrinterLog_logError('unknown filament type', __FILE__, __LINE__);
				$this->output->set_header('Location: /setupcartridge/input?v=' . $side);
			}
		}
		else {
			$this->output->set_header('Location: /setupcartridge/input?v=' . $side);
		}
		
		return;
	}
	
	public function input() {
		$cr = 0;
		$array_type = array();
		$template_data = array();
		$body_page = NULL;
		$side = $this->input->get('v');
		
		if ($side != 'l') {
			$side = 'r';
		}
		
		$this->load->library('parser');
		if ($this->_get_type_array($array_type)) {
			$template_data = array(
					'types'	=> $array_type,
					'side'	=> $side,
			);
			
			$body_page = $this->parser->parse('template/setupcartridge/input', $template_data, TRUE);
			
			// parse all page
			$template_data = array(
					'lang'			=> 'en',
					'headers'		=> '<title>SetupCartridge 设置标签</title>',
					'contents'		=> $body_page,
			);
			$this->parser->parse('template/basetemplate', $template_data);
		}
		else {
			$this->parser->parse('template/plaintxt', array(
					'display' => 'Please contact your administrator 请联系您的管理员',
			));
		}
		
		return;
	}
	
	public function readnwrite($side = 'r') {
		$cr = 0;
		$cartridge_data = array();
		$body_page = NULL;
		$length_value = 0;
		$temper_value = 0;
		$temper_first = 0;
		$material_value = 0;
		$color_value = '#ffffff';
		$option_selected = 'selected="selected"';
		
		// right as default
		if (in_array($side, array('l', 'left'))) {
			$side = 'l';
		}
		else {
			$side = 'r';
		}
		
		$this->load->library('parser');
		$cr = PrinterState_getCartridgeAsArray($cartridge_data, $side);
		if ($cr != ERROR_OK) {
			$length_value = 10;
			$temper_value = 200;
			$temper_first = 210;
		}
		else {
			$length_value = floor(($cartridge_data[PRINTERSTATE_TITLE_INITIAL] - $cartridge_data[PRINTERSTATE_TITLE_USED]) / 1000);
// 			if ($length_value < 10) {
// 				$length_value = 10;
// 			}
			$temper_value = $cartridge_data[PRINTERSTATE_TITLE_EXT_TEMPER];
			$temper_first = $cartridge_data[PRINTERSTATE_TITLE_EXT_TEMP_1];
			$color_value = $cartridge_data[PRINTERSTATE_TITLE_COLOR];
// 			if ($temper_value < 160) {
// 				$temper_value = 160;
// 			}
		}
		
		$template_data = array (
				'material_array'	=> array(
						array('name' => 'PLA', 'value' => PRINTERSTATE_VALUE_MATERIAL_PLA, 'on' => NULL),
						array('name' => 'ABS', 'value' => PRINTERSTATE_VALUE_MATERIAL_ABS, 'on' => NULL),
						array('name' => 'PVA', 'value' => PRINTERSTATE_VALUE_MATERIAL_PVA, 'on' => NULL),
				),
				'length_value'		=> $length_value,
				'temper_value'		=> $temper_value,
				'temper_f_value'	=> $temper_first,
				'rfid_color'		=> $color_value,
				'abb_cartridge'		=> $side,
				'side_cartridge'	=> ($side == 'l') ? 'Left' : 'Right',
				'error'				=> ($cr != ERROR_OK) ? 'No cartridge / cartridge error' : NULL,
		);
		
		switch ($cartridge_data[PRINTERSTATE_TITLE_MATERIAL]) {
			case PRINTERSTATE_DESP_MATERIAL_PLA:
				$material_value = PRINTERSTATE_VALUE_MATERIAL_PLA;
				break;
		
			case PRINTERSTATE_DESP_MATERIAL_ABS:
				$material_value = PRINTERSTATE_VALUE_MATERIAL_ABS;
				break;
		
			case PRINTERSTATE_DESP_MATERIAL_PVA:
				$material_value = PRINTERSTATE_VALUE_MATERIAL_PVA;
				break;
		
			default:
				$material_value = PRINTERSTATE_VALUE_MATERIAL_PLA;
				break;
		}
		$template_data['material_array'][$material_value]['on'] = $option_selected;
		
		$body_page = $this->parser->parse('template/setupcartridge/readnwrite', $template_data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> 'en',
				'headers'		=> '<title>Read \'n\' Write</title>',
				'contents'		=> $body_page,
		);
		$this->parser->parse('template/basetemplate', $template_data);
		
		return;
	}
	
	public function readnwrite_ajax() {
		$ret_val = 0;
		$array_data = array();
		$array_old = array();
		$color = $this->input->get('c');
		$temper = (int) $this->input->get('t');
		$temper_f = (int) $this->input->get('tf');
		$material = (int) $this->input->get('m');
		$length = (int) $this->input->get('l') * 1000;
		$abb_cartridge = $this->input->get('v');
		
		// get cartridge type from old RFID
		$ret_val = PrinterState_getCartridgeAsArray($array_old, $abb_cartridge, FALSE);
		if ($ret_val != ERROR_OK) {
			$this->output->set_status_header(403);
				
			$this->load->helper('printerlog');
			PrinterLog_logMessage('read rfid error: ' . $ret_val, __FILE__, __LINE__);
			break;
		}
		// change color from name to hex code
		$this->load->helper('printlist');
		$ret_val = ModelList__changeColorName($color);
		if ($ret_val == ERROR_WRONG_PRM) {
			$this->output->set_status_header(404);
				
			$this->load->helper('printerlog');
			PrinterLog_logMessage('unknown color name: ' . $color, __FILE__, __LINE__);
			break;
		}
		$color = str_replace('#', '', $color);
		// write RFID card
		$array_data = array(
				PRINTERSTATE_TITLE_COLOR		=> $color,
				PRINTERSTATE_TITLE_EXT_TEMPER	=> $temper,
				PRINTERSTATE_TITLE_INITIAL		=> $length,
				PRINTERSTATE_TITLE_MATERIAL		=> $material,
				PRINTERSTATE_TITLE_CARTRIDGE	=> $array_old[PRINTERSTATE_TITLE_CARTRIDGE],
				PRINTERSTATE_TITLE_EXT_TEMPER	=> $temper_f,
		);
		$ret_val = PrinterState_setCartridgeAsArray($abb_cartridge, $array_data);
		if ($ret_val != ERROR_OK) {
			$this->output->set_status_header(403);
				
			$this->load->helper('printerlog');
			PrinterLog_logMessage('write rfid error: ' . $ret_val, __FILE__, __LINE__);
			break;
		}
		
		return;
	}
}
