<?php

class Cartridge extends MY_Controller
{
	function __construct()
	{
		parent::__construct ();
		$this->load->helper( array(
				'printerstate',
				'json'
		) );
	}

	public function readnwrite($side = 'r')
	{
		$cr = 0;
		$cartridge_code = 'Cartridge code';
		$err_code = NULL;
		$cartridge_data = array();
		$body_page = NULL;
		$initial_length_value = 0;
		$used_length_value = 0;
		$temper_value = 0;
		$temper_first = 0;
		$material_value = 0;
		$color_value = '#ffffff';
		$option_selected = 'selected="selected"';
	
		// right as default
		if (in_array($side, array('l', 'left')))
			$side = 'l';
		else
			$side = 'r';
	
		$this->load->library('parser');
		$this->lang->load('cartridge', $this->config->item('language'));

		if (PrinterState_getCartridgeCode($cartridge_code, $side) != ERROR_OK)
			$cartridge_code = "error retrieving cartridge code";
		
		$cr = PrinterState_getCartridgeAsArray($cartridge_data, $side, true, $err_code);
		if ($cr != ERROR_OK)
		{
			$initial_length_value = 20;
			$used_length_value = 10;
			$temper_value = 200;
			$temper_first = 210;
		}
		else
		{
			$initial_length_value = $cartridge_data[PRINTERSTATE_TITLE_INITIAL] / 1000;
			$used_length_value = $cartridge_data[PRINTERSTATE_TITLE_USED] / 1000;
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
				'cartridge_code'	=> $cartridge_code,
				'initial_length_value'	=> $initial_length_value,
				'used_length_value'	=> $used_length_value,
				'temper_value'		=> $temper_value,
				'temper_f_value'	=> $temper_first,
				'rfid_color'		=> $color_value,
				'abb_cartridge'		=> $side,
				'side_cartridge'	=> ($side == 'l') ? t('Left') : t('Right'),
				'error'				=> ($cr != ERROR_OK) ? 'No cartridge / cartridge error' : NULL,
		);
	
		if ($cr == ERROR_OK)
		{
			switch ($cartridge_data[PRINTERSTATE_TITLE_MATERIAL])
			{
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
		}
	
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
	
	public function readnwrite_ajax()
	{
		$ret_val = 0;
		$array_data = array();
		$array_old = array();
		$color = $this->input->get('c');
		$temper = (int) $this->input->get('t');
		$temper_f = (int) $this->input->get('tf');
		$material = (int) $this->input->get('m');
		$length = (int) $this->input->get('l') * 1000;
		$abb_cartridge = $this->input->get('v');
		$type = $this->input->get('ct');

		// get cartridge type from old RFID
		$ret_val = PrinterState_getCartridgeAsArray($array_old, $abb_cartridge, FALSE);
		if ($ret_val != ERROR_OK)
		{
			$this->output->set_status_header(403);
	
			$this->load->helper('printerlog');
			PrinterLog_logMessage('read rfid error: ' . $ret_val, __FILE__, __LINE__);
			break;
		}
		// change color from name to hex code
		$this->load->helper('printlist');
		$ret_val = ModelList__changeColorName($color);
		if ($ret_val == ERROR_WRONG_PRM)
		{
			$this->output->set_status_header(404);
	
			$this->load->helper('printerlog');
			PrinterLog_logMessage('unknown color name: ' . $color, __FILE__, __LINE__);
			return;
		}
		$color = str_replace('#', '', $color);
		// write RFID card
		$array_data = array(
				PRINTERSTATE_TITLE_COLOR		=> $color,
				PRINTERSTATE_TITLE_EXT_TEMPER	=> $temper,
				PRINTERSTATE_TITLE_INITIAL		=> $length,
				PRINTERSTATE_TITLE_MATERIAL		=> $material,
				PRINTERSTATE_TITLE_CARTRIDGE	=> $type,
				PRINTERSTATE_TITLE_EXT_TEMPER	=> $temper_f,
		);
		$ret_val = PrinterState_setCartridgeAsArray($abb_cartridge, $array_data);
		if ($ret_val != ERROR_OK)
		{
			$this->output->set_status_header(403);
	
			$this->load->helper('printerlog');
			PrinterLog_logMessage('write rfid error: ' . $ret_val, __FILE__, __LINE__);
		}
		return;
	}
}