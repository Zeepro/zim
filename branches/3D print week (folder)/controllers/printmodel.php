<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class Printmodel extends MY_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->helper( array(
				'url',
				'json' 
		) );
	}
	
	private function _model_usortCompare($a, $b) {
		return strcasecmp($a['name'], $b['name']);
	}
	
	public function index() {
// 		$this->listmodel();
		$this->output->set_header('Location: /printmodel/listmodel');
		return;
	}
	
	public function listmodel() {
// 		$json_text = $this->curl->simple_get(base_url('rest/listmodel'));
// 		curl_init('http://example.com');
		$display_printlist = array();
		$template_data = array();
		
		$this->load->helper('printlist');
		$this->load->library('parser');
		$this->lang->load('printlist', $this->config->item('language'));
		
		$json_data = ModelList__listAsArray(TRUE);
		
		// prepare display data
		foreach ($json_data[PRINTLIST_TITLE_MODELS] as $model_data) {
			$nb_image = count($model_data[PRINTLIST_TITLE_PIC]);
			
			$display_printlist[] = array(
					'name'	=> $model_data[PRINTLIST_TITLE_NAME],
					'id'	=> $model_data[PRINTLIST_TITLE_ID],
					'image'	=> $model_data[PRINTLIST_TITLE_PIC][0],
			);
		}
		// sort list by name of translation, by name of folder if not do so
		usort($display_printlist, 'Printmodel::_model_usortCompare');
		
		// parse the main body
		$template_data = array(
// 				'title'				=> t('Print'),
				'search_hint'		=> t('Select a model'),
				'baseurl_detail'	=> '/printmodel/detail',
				'model_lists'		=> $display_printlist,
				'back'				=> t('back'),
		);
		
		// parse all page
		$this->_parseBaseTemplate(t('ZeePro Personal Printer 21 - Quick printing list'),
				$this->parser->parse('printlist/listmodel', $template_data, TRUE));
		
		return;
	}
	
	public function detail() {
		$model_data = array();
		$cartridge_data = array();
		$template_data = array();
		$cr = 0;
		$check_left_filament = '';
		$check_right_filament = '';
		$color_left_filament = '';
		$color_right_filament = '';
		$change_left_filament = '';
		$change_right_filament = '';
		$temper_left_filament = 0;
		$temper_right_filament = 0;
		$time_estimation = '';
		$body_page = NULL;
		$mono_color = FALSE;
		$nb_extruder = 0;
		$enable_print = 'true';
		$select_disable = 'disabled="disabled"';
		
		$this->load->helper(array('printlist', 'printerstate', 'timedisplay'));
		$this->load->library('parser');
		$this->lang->load('printlist', $this->config->item('language'));
		$this->lang->load('timedisplay', $this->config->item('language'));
		
		$mid = $this->input->get('id');
		
		// check model id, resend user to if not valid
		if ($mid) {
			if ($mid == 'calibration') {
				$mid = ModelList_codeModelHash(PRINTLIST_MODEL_CALIBRATION);
			}
			$cr = ModelList__getDetailAsArray($mid, $model_data, TRUE);
			if (($cr != ERROR_OK) || is_null($model_data)) {
				$this->output->set_header('Location: /printmodel/listmodel');
				return;
			}
		}
		else {
			$this->output->set_header('Location: /printmodel/listmodel');
			return;
		}
		
		// check the model is mono-color or 2 colors
		if (isset($model_data[PRINTLIST_TITLE_LENG_F2]) && $model_data[PRINTLIST_TITLE_LENG_F2] != 0) {
			$mono_color = FALSE;
		}
		else {
			$mono_color = TRUE;
		}
		
		// get number of extruder
		$nb_extruder = PrinterState_getNbExtruder();
		
		// check quantity of filament and get cartridge information (color)
		// color1 => right, color2 => left
		$cr = PrinterState_checkFilament('r', $model_data[PRINTLIST_TITLE_LENG_F1], $cartridge_data);
		$check_right_filament = t('ok');
		if ($model_data[PRINTLIST_TITLE_LENG_F1] == 0) {
			$check_right_filament = t('filament_not_need');
		}
		$change_right_filament = t('Change');
		switch ($cr) {
			case ERROR_OK:
				break; // break directly if no error
				
			case ERROR_LOW_RIGT_FILA:
				$check_right_filament = t('not enough');
				break;
				
			case ERROR_MISS_RIGT_CART:
				$check_right_filament = t('empty');
				$change_right_filament = t('Load');
				break;
				
			case ERROR_MISS_RIGT_FILA:
				$check_right_filament = t('unloaded');
				$change_right_filament = t('Load');
				break;
				
			default:
				// treat error here, usually happened when checksum failed
				$this->load->helper('printerlog');
				PrinterLog_logError('not previewed return code for checking right filament', __FILE__, __LINE__);
				$check_right_filament = t('error');
				$change_right_filament = t('Load');
				break;
		}
		if (($cr != ERROR_MISS_RIGT_CART) && ($cr != ERROR_INTERNAL)) {
			$color_right_filament = $cartridge_data[PRINTERSTATE_TITLE_COLOR];
			$temper_right_filament = $cartridge_data[PRINTERSTATE_TITLE_EXT_TEMPER];
		}
		else {
			$color_right_filament = PRINTERSTATE_VALUE_DEFAULT_COLOR;
		}
		if ($cr != ERROR_OK && $model_data[PRINTLIST_TITLE_LENG_F1] > 0) {
			$enable_print = 'false';
		}
		
		if ($nb_extruder >= 2) {
// 			if ($mono_color == FALSE) {
				$cr = PrinterState_checkFilament('l', $model_data[PRINTLIST_TITLE_LENG_F2], $cartridge_data);
// 			}
// 			else {
// 				$cr = PrinterState_getCartridgeAsArray($cartridge_data, 'l');
// 			}
			$check_left_filament = t('ok');
			if ($model_data[PRINTLIST_TITLE_LENG_F2] == 0) {
				$check_left_filament = t('filament_not_need');
			}
			$change_left_filament = t('Change');
			switch ($cr) {
				case ERROR_OK:
					break; // break directly if no error
					
				case ERROR_LOW_LEFT_FILA:
					$check_left_filament = t('not enough');
					break;
					
				case ERROR_MISS_LEFT_CART:
					$check_left_filament = t('empty');
					$change_left_filament = t('Load');
					break;
					
				case ERROR_MISS_LEFT_FILA:
					$check_left_filament = t('unloaded');
					$change_left_filament = t('Load');
					break;
					
				default:
					// treat error here, usually happened when checksum failed
					$this->load->helper('printerlog');
					PrinterLog_logError('not previewed return code for checking left filament', __FILE__, __LINE__);
					$check_left_filament = t('error');
					$change_left_filament = t('Load');
					break;
			}
			if (($cr != ERROR_MISS_LEFT_CART) && ($cr != ERROR_INTERNAL)) {
				$color_left_filament = $cartridge_data[PRINTERSTATE_TITLE_COLOR];
				$temper_left_filament = $cartridge_data[PRINTERSTATE_TITLE_EXT_TEMPER];
			}
			else {
				$color_left_filament = PRINTERSTATE_VALUE_DEFAULT_COLOR;
			}
			if ($cr != ERROR_OK && $model_data[PRINTLIST_TITLE_LENG_F2] > 0) {
				$enable_print = 'false';
			}
		}
		
		// get a more legible time of estimation
		$time_estimation = TimeDisplay__convertsecond(
				$model_data[PRINTLIST_TITLE_TIME], t('Time estimation: '), t('unknown'));
		
		// show detail page if valid, parse the body of page
		$template_data = array(
				'home'				=> t('Home'),
				'title'				=> $model_data[PRINTLIST_TITLE_NAME],
				'image'				=> $model_data[PRINTLIST_TITLE_PIC][0],
				'model_c_r'			=> $model_data[PRINTLIST_TITLE_COLOR_F1],
// 				'model_c_l'			=> $model_data[PRINTLIST_TITLE_COLOR_F2],
				'time'				=> $time_estimation,
				'desp'				=> $model_data[PRINTLIST_TITLE_DESP],
// 				'state_c_l'			=> $color_left_filament,
				'state_c_r'			=> $color_right_filament,
// 				'state_f_l'			=> $check_left_filament,
				'state_f_r'			=> $check_right_filament,
				'model_id'			=> $mid,
				'title_current' 	=> t('Filament'),
// 				'change_filament_l'	=> $change_left_filament,
				'change_filament_r'	=> $change_right_filament,
// 				'need_filament_l'	=> $model_data[PRINTLIST_TITLE_LENG_F2],
// 				'need_filament_l'	=> 0,
				'need_filament_r'	=> $model_data[PRINTLIST_TITLE_LENG_F1],
// 				'temper_filament_l'	=> $temper_left_filament,
				'temper_filament_r'	=> $temper_right_filament,
				'print_model'		=> t('Print'),
				'back'				=> t('back'),
				'preview_title'		=> t('Preview'),
				'desp_title'		=> t('Description'),
				'color_suggestion'	=> t('color_suggestion'),
				'temp_adjustments_l'=> t('temp_adjustments_l'),
				'temp_adjustments_r'=> t('temp_adjustments_r'),
				'error'				=> t('error'),
				'enable_exchange'	=> $select_disable,
				'enable_print'		=> $enable_print,
				'filament_not_need'	=> t('filament_not_need'),
				'filament_ok'		=> t('ok'),
				'temper_max'		=> PRINTERSTATE_TEMPER_CHANGE_MAX,
				'temper_min'		=> PRINTERSTATE_TEMPER_CHANGE_MIN,
				'temper_delta'		=> PRINTERSTATE_TEMPER_CHANGE_VAL,
		);
		if ($nb_extruder >= 2) {
			$template_data['state_c_l'] = $color_left_filament;
			$template_data['state_f_l'] = $check_left_filament;
			$template_data['change_filament_l'] = $change_left_filament;
			$template_data['temper_filament_l'] = $temper_left_filament;
			$template_data['exchange_extruder'] = t('exchange_extruder');
			
			// check if we can inverse filament / exchange extruder or not
			$cr = PrinterState_checkFilaments(array(
					'l'	=> $model_data[PRINTLIST_TITLE_LENG_F1],
					'r'	=> $model_data[PRINTLIST_TITLE_LENG_F2],
			));
			if ($cr == ERROR_OK) {
				$template_data['enable_exchange'] = NULL;
			}
// 			$cr = PrinterState_checkFilament('l', $model_data[PRINTLIST_TITLE_LENG_F1]);
// 			if ($cr == ERROR_OK) {
// 				$cr = PrinterState_checkFilament('r', $model_data[PRINTLIST_TITLE_LENG_F2]);
// 				if ($cr == ERROR_OK) {
// 					$template_data['enable_exchange'] = NULL;
// 				}
// 			}
			
			if ($mono_color == FALSE) {
				$template_data['model_c_l'] = $model_data[PRINTLIST_TITLE_COLOR_F2];
				$template_data['need_filament_l'] = $model_data[PRINTLIST_TITLE_LENG_F2];
				$template_data['exchange_off'] = t('exchange_straight');
				$template_data['exchange_on'] = t('exchange_crossover');
				$body_page = $this->parser->parse('printlist/detail_2extrud_2color', $template_data, TRUE);
			}
			else {
				$template_data['need_filament_l'] = 0;
				$template_data['exchange_off'] = t('exchange_right');
				$template_data['exchange_on'] = t('exchange_left');
				$body_page = $this->parser->parse('printlist/detail_2extrud_1color', $template_data, TRUE);
			}
		}
		else if ($nb_extruder == 1) {
			if ($mono_color == FALSE) {
				$template_data['model_c_l'] = $model_data[PRINTLIST_TITLE_COLOR_F2];
				$template_data['need_filament_l'] = $model_data[PRINTLIST_TITLE_LENG_F2];
				$body_page = $this->parser->parse('printlist/detail_1extrud_2color', $template_data, TRUE);
			}
			else {
				$body_page = $this->parser->parse('printlist/detail_1extrud_1color', $template_data, TRUE);
			}
		}
		
		// parse all page
		$this->_parseBaseTemplate(t('ZeePro Personal Printer 21 - Printing details'), $body_page);
		
		return;
	}
}
