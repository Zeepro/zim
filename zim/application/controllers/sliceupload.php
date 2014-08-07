<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Sliceupload extends MY_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->helper( array(
				'errorcode',
		) );
	}

	public function index() {
		$this->output->set_header('Location: /sliceupload/upload');
		return;
	}
	
	public function upload() {
		$template_data = array();
		$body_page = NULL;
		$error = NULL;
		$response = 0;
		$button_goto_slice = NULL;
		
		$this->load->library('parser');
		$this->lang->load('sliceupload/upload', $this->config->item('language'));
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$array_model = array();
			$upload_config = array (
					'upload_path'	=> $this->config->item('temp'),
					'allowed_types'	=> '*',
					'overwrite'		=> FALSE,
					'remove_spaces'	=> TRUE,
					'encrypt_name'	=> TRUE,
			);
			$this->load->library('upload', $upload_config);
			
// 			if (!empty($_FILES['file']))
// 				$ret = $this->upload->do_upload('file');
// 			else
// 				$ret = $this->upload->do_upload('file_c1') && $this->upload->do_upload('file_c2');
			
			if ($this->upload->do_upload('file'))
			{
				$model = $this->upload->data();
				$array_model[] = $model['file_name'];
			}
			else if ($this->upload->do_upload('file_c1')) {
				$first_combine = TRUE;
				$model = $this->upload->data();
				$array_model[] = $model['file_name'];
				
				foreach (array('file_c2') as $file_key) {
					if ($this->upload->do_upload($file_key)) {
						$first_combine = FALSE;
						$model = $this->upload->data();
						$array_model[] = $model['file_name'];
					}
					else if ($first_combine == TRUE) {
						$error = t('upload_miss_fail');
						break;
					}
				}
			}
			else {
				// treat error - missing gcode file
				$error = t('upload_miss_fail');
			}
			
			if (is_null($error) && count($array_model)) {
				// load a wait page for adding model into slicer
				$template_data = array(
						'wait_message'	=> t('wait_message'),
						'return_button'	=> t('return_button'),
						'model_name'	=> json_encode($array_model),
						'fail_message'	=> t('fail_message'),
						'fin_message'	=> t('fin_message'),
				);
				$body_page = $this->parser->parse('template/sliceupload/upload_wait', $template_data, TRUE);
				
				$template_data = array(
						'lang'			=> $this->config->item('language_abbr'),
						'headers'		=> '<title>' . t('sliceupload_upload_pagetitle') . '</title>',
						'contents'		=> $body_page,
				);
				
				$this->parser->parse('template/basetemplate', $template_data);
				
				return;
			}
		}
		
		$this->load->helper('slicer');
		if (ERROR_OK == Slicer_listModel($response) && $response != "[]") {
			$template_data = array(
					'text'	=> t('button_goto_slice'),
					'link'	=> '/sliceupload/slice',
					'id'	=> 'goto_slice_button',
			);
			$button_goto_slice = $this->parser->parse('template/sliceupload/a_button', $template_data, TRUE);
		}
		
		// parse the main body
		$template_data = array(
				'back'			=> t('back'),
				'select_hint'	=> t('select_hint'),
				'select_hint_multi'	=> t('select_hint_multi'),
				'header_single' => t('header_single'),
				'header_multi'	=> t('header_multi'),
				'upload_button'	=> t('upload_button'),
				'goto_slice'	=> $button_goto_slice,
				'error'			=> $error,
		);
		$body_page = $this->parser->parse('template/sliceupload/upload', $template_data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . t('sliceupload_upload_pagetitle') . '</title>',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
		
		return;
	}
	
	function slice() {
		$template_data = array();
		$body_page = NULL;
		$list_preset = array();
		$list_display = array();
		$current_stage = 'wait_slice';
		
		// redirect the client when in slicing
		$this->load->helper('corestatus');
		$ret_val = CoreStatus_checkInIdle($status_current);
		// check status in slicing
		if ($ret_val == FALSE || $status_current == CORESTATUS_VALUE_SLICE) {
			$this->output->set_header('Location: /slicestatus/slicestatus');
			return;
		}
		
		$this->load->helper('zimapi');
		$list_preset = ZimAPI_getPresetListAsArray();
		
		if ($this->input->get('callback') !== FALSE) {
			$current_stage = 'wait_print';
		}
		
		foreach ($list_preset as $preset) {
			$list_display[] = array(
					'id'	=> $preset[ZIMAPI_TITLE_PRESET_ID],
					'name'	=> $preset[ZIMAPI_TITLE_PRESET_NAME],
			);
		}
		
		$this->load->library('parser');
		$this->lang->load('sliceupload/slice', $this->config->item('language'));
		
		// parse the main body
		$template_data = array(
 				'home'			=> t('home'),
				'select_hint'	=> t('select_hint'),
				'slice_button'	=> t('slice_button'),
				'goto_preset'	=> t('goto_preset'),
				'value_rho'		=> ZIMAPI_VALUE_DEFAULT_RHO,
				'value_delta'	=> ZIMAPI_VALUE_DEFAULT_DELTA,
				'value_theta'	=> ZIMAPI_VALUE_DEFAULT_THETA,
				'preset_list'	=> $list_display,
				'current_stage'	=> $current_stage,
				'goto_hint'		=> t('goto_hint'),
				'wait_preview'	=> t('wait_preview'),
				'wait_slice'	=> t('wait_slice'),
				'wait_in_slice'	=> t('wait_in_slice'),
				'near_button'	=> t('near_button'),
				'far_button'	=> t('far_button'),
				'small_button'	=> t('small_button'),
				'big_button'	=> t('big_button'),
		);
		$body_page = $this->parser->parse('template/sliceupload/slice', $template_data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . t('sliceupload_slice_pagetitle') . '</title>',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
		
		return;
	}
	
	function slicestatus() {
		$template_data = array();
		$body_page = NULL;
		$ret_val = 0;
		$status_current = NULL;
		
		$this->load->helper('corestatus');
		$ret_val = CoreStatus_checkInIdle($status_current);
		// check status in slicing
		if ($ret_val != FALSE || $status_current != CORESTATUS_VALUE_SLICE) {
			$this->output->set_header('Location: /slicestatus/slice');
			return;
		}
		
		$this->load->library('parser');
		$this->lang->load('sliceupload/slice', $this->config->item('language'));
		
		// parse the main body
		$template_data = array(
				'wait_in_slice'	=> t('wait_in_slice'),
		);
		$body_page = $this->parser->parse('template/sliceupload/slicestatus', $template_data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . t('sliceupload_slice_pagetitle') . '</title>',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
		
		return;
	}
	
	function add_model_ajax() {
		$cr = 0;
		$display = NULL;
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$filename = $this->input->post('file');
			if ($filename) {
				$array_model = json_decode($filename, TRUE);
				$number_model = count($array_model);
				if ($number_model) {
					$tmp_i = 0;
					$cr = ERROR_OK;
					for ($tmp_i = 0; $tmp_i < $number_model; $tmp_i++) {
						$array_model[$tmp_i] = $this->config->item('temp') . $array_model[$tmp_i];
						if (!file_exists($array_model[$tmp_i])) {
							$cr = ERROR_WRONG_PRM;
							break;
						}
					}
					
					if ($cr == ERROR_OK) {
						$this->load->helper('slicer');
						$cr = Slicer_addModel($array_model);
					}
				}
			}
			else {
				$cr = ERROR_MISS_PRM;
			}
		}
		else {
			$cr = ERROR_WRONG_PRM;
		}
		
		$display = $cr . " " . t(MyERRMSG($cr));
		$this->output->set_status_header($cr, $display);
		$this->output->set_content_type('txt_u');
		$this->load->library('parser');
		$this->parser->parse('template/plaintxt', array('display' => $display)); //optional
		
		if ($cr != ERROR_OK) {
			$this->load->helper('printerlog');
			PrinterLog_logError('add model in slicer error, ' . $cr, __FILE__, __LINE__);
		}
// 		else {
// 			unlink($this->config->item('temp') . SLICER_FILE_TEMP_DATA);
// 		}
		
		return;
	}
	
	function slice_model_ajax() {
		$cr = 0;
		$array_cartridge = array();
		$display = NULL;
		$id_preset = $this->input->get('id');
		
		$this->load->helper('slicer');
		
		if ($id_preset) {
			$cr = ZimAPI_setPreset($id_preset);
		}
		else {
			$cr = ERROR_MISS_PRM;
		}
		
		if ($cr == ERROR_OK) {
			$cr = Slicer_reloadPreset();
		}
		
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
		
		$display = $cr . " " . t(MyERRMSG($cr));
		$this->output->set_status_header($cr, $display);
		$this->output->set_content_type('txt_u');
		$this->load->library('parser');
		$this->parser->parse('template/plaintxt', array('display' => $display)); //optional
		
		return;
	}
	
	function slice_status_ajax() {
		$template_data = array();
		$ret_val = 0;
		$array_data = array();
		$status_current = NULL;
		$cr = 0;
		$need_filament_l = 0;
		$need_filament_r = 0;
		$state_f_l = NULL;
		$state_f_r = NULL;
		$change_left = '';
		$change_right = '';
		$file_temp_data = NULL;
// 		$array_key_real_temper = 'real_temperature'; //TODO think about if we need to declare this key name in helper or not
		$error = NULL;
		
		$callback_return = $this->input->get('callback');
		$this->load->helper(array('printerstate', 'slicer'));
		$file_temp_data = $this->config->item('temp') . SLICER_FILE_TEMP_DATA;
		$this->load->library('parser');
		$this->lang->load('sliceupload/slice_status_ajax', $this->config->item('language'));
		
		if ($callback_return) {
			if (!file_exists($file_temp_data)) {
				$this->load->helper('printerlog');
				PrinterLog_logError('callback return to slice page without temp data file', __FILE__, __LINE__);
				$cr = ERROR_INTERNAL;
			}
			else {
				$data_json = array();
				$temp_json = array();
				
				$this->load->helper('json');
				$temp_json = json_read($file_temp_data, TRUE);
				if (isset($temp_json['error'])) {
					$this->load->helper('printerlog');
					PrinterLog_logError('read temp data file error', __FILE__, __LINE__);
					$cr = ERROR_INTERNAL;
				}
				else {
					$material = NULL;
					
					$data_json = $temp_json['json'];
					$cr = ERROR_OK;
					foreach ($data_json as $abb_filament => $array_temp) {
						$data_cartridge = array();
						$tmp_ret = 0;
						$volume_need = $array_temp[PRINTERSTATE_TITLE_NEED_L];
						
						$tmp_ret = PrinterState_checkFilament($abb_filament, $volume_need, $data_cartridge);
						$array_data[$abb_filament] = array(
								PRINTERSTATE_TITLE_COLOR		=> $data_cartridge[PRINTERSTATE_TITLE_COLOR],
								PRINTERSTATE_TITLE_EXT_TEMPER	=> $data_cartridge[PRINTERSTATE_TITLE_EXT_TEMPER],
								PRINTERSTATE_TITLE_EXT_TEMP_1	=> $data_cartridge[PRINTERSTATE_TITLE_EXT_TEMP_1],
								PRINTERSTATE_TITLE_NEED_L		=> $volume_need,
						);
						// only assign return code when success to make a tour of used cartridges
						if ($cr == ERROR_OK) {
							$cr = $tmp_ret;
						}
						// check material difference for all used cartridges
						if ($material == NULL) {
							$material = $data_cartridge[PRINTERSTATE_TITLE_MATERIAL];
						}
						else if ($material != $data_cartridge[PRINTERSTATE_TITLE_MATERIAL]) {
							$error .= t('cartridge_material_diff_msg') . '<br>';
						}
						if ($array_temp[PRINTERSTATE_TITLE_EXT_TEMPER] != $data_cartridge[PRINTERSTATE_TITLE_EXT_TEMPER]) {
							$error .= t('temper_diff_msg',
									array(
											$array_temp[PRINTERSTATE_TITLE_EXT_TEMPER],
											$data_cartridge[PRINTERSTATE_TITLE_EXT_TEMPER],
									)
							) . '<br>';
						}
						if ($array_temp[PRINTERSTATE_TITLE_EXT_TEMP_1] != $data_cartridge[PRINTERSTATE_TITLE_EXT_TEMP_1]) {
							$error .= t('first_temper_diff_msg',
									array(
											$array_temp[PRINTERSTATE_TITLE_EXT_TEMP_1],
											$data_cartridge[PRINTERSTATE_TITLE_EXT_TEMP_1],
									)
							) . '<br>';
						}
					}
					if (!is_null($error)) {
						$error .= t('suggest_reslice');
					}
				}
			}
		}
		else {
			CoreStatus_checkInIdle($status_current);
			$ret_val = PrinterState_checkBusyStatus($status_current, $array_data, FALSE);
			if ($ret_val == TRUE && $status_current == CORESTATUS_VALUE_IDLE) {
				if (isset($array_data[PRINTERSTATE_TITLE_LASTERROR])) {
					$cr = $array_data[PRINTERSTATE_TITLE_LASTERROR];
				}
				else {
					$cr = ERROR_OK;
				}
			}
			else if ($ret_val == FALSE && $status_current == CORESTATUS_VALUE_SLICE) {
				if (!isset($array_data[PRINTERSTATE_TITLE_PERCENT])) {
					$this->load->helper('printerlog');
					PrinterLog_logError('can not find percentage in slicing', __FILE__, __LINE__);
					$cr = ERROR_INTERNAL;
				}
				else {
					$cr = ERROR_OK;
					$this->output->set_status_header($cr);
					$this->output->set_content_type('txt_u');
					$this->parser->parse('template/plaintxt', array('display' => $array_data[PRINTERSTATE_TITLE_PERCENT]));
					
					return;
				}
			}
			else {
				$this->load->helper('printerlog');
				PrinterLog_logError('unknown status in slicing', __FILE__, __LINE__);
				$cr = ERROR_INTERNAL;
			}
			
// 			// save data info into temp json file
// 			if ($cr != ERROR_INTERNAL) {
// 				try {
// 					$fp = fopen($file_temp_data, 'w');
// 					if ($fp) {
// 						fwrite($fp, json_encode($array_data));
// 						fclose($fp);
// 					}
// 					else {
// 						throw new Exception('can not open file');
// 					}
// 				} catch (Exception $e) {
// 					$this->load->helper('printerlog');
// 					PrinterLog_logError('can not save temp json file', __FILE__, __LINE__);
// 					$cr = ERROR_INTERNAL;
// 				}
// 			}
			
// 			// assign the same value for real temperature
// 			foreach(array_keys($array_data) as $abb_filament) {
// 				$array_data[$abb_filament][$array_key_real_temper] = $array_data[$abb_filament][PRINTERSTATE_TITLE_EXT_TEMPER];
// 			}
		}
		
		
		//TODO treat mono color and mono extruder case
		
		$state_f_l = $state_f_r = t('filament_ok');
		if (!isset($array_data['l'])) {
			$state_f_l = t('filament_not_need');
		}
		$change_left = $change_right = t('change_filament');
		switch ($cr) {
			case ERROR_OK:
				// do nothing if no error
				break;
			
			case ERROR_INTERNAL:
				$display = $cr . " " . t(MyERRMSG($cr));
				$this->output->set_status_header($cr);
				$this->output->set_content_type('txt_u');
				$this->parser->parse('template/plaintxt', array('display' => $display)); //optional
				return;
				break; // never reach here
				
			case ERROR_LOW_RIGT_FILA:
				$state_f_r = t('filament_not_enough');
				break;
				
			case ERROR_MISS_RIGT_FILA:
				$state_f_r = t('filament_unloaded');
				$change_right = t('load_filament');
				break;
				
			case ERROR_MISS_RIGT_CART:
				$state_f_r = t('filament_empty');
				$change_right = t('load_filament');
				break;
				
			case ERROR_LOW_LEFT_FILA:
				$state_f_l = t('filament_not_enough');
				break;
				
			case ERROR_MISS_LEFT_FILA:
				$state_f_l = t('filament_unloaded');
				$change_left = t('load_filament');
				break;
				
			case ERROR_MISS_LEFT_CART:
				$state_f_l = t('filament_empty');
				$change_left = t('load_filament');
				break;
				
			default:
				$this->load->helper('printerlog');
				PrinterLog_logError('unknown return after slicing: ' . $cr, __FILE__, __LINE__);
				break;
		}
		
		
		$template_data = array(
				'cartridge_c_l'		=> isset($array_data['l']) ? $array_data['l'][PRINTERSTATE_TITLE_COLOR] : PRINTERSTATE_VALUE_DEFAULT_COLOR,
				'cartridge_c_r'		=> isset($array_data['r']) ? $array_data['r'][PRINTERSTATE_TITLE_COLOR] : PRINTERSTATE_VALUE_DEFAULT_COLOR,
				'state_f_l'			=> $state_f_l,
				'state_f_r'			=> $state_f_r,
				'need_filament_l'	=> isset($array_data['l']) ? $array_data['l'][PRINTERSTATE_TITLE_NEED_L] : 0,
				'need_filament_r'	=> isset($array_data['r']) ? $array_data['r'][PRINTERSTATE_TITLE_NEED_L] : 0,
				'temper_l'			=> isset($array_data['l']) ? $array_data['l'][PRINTERSTATE_TITLE_EXT_TEMPER] : '---',
				'temper_r'			=> isset($array_data['r']) ? $array_data['r'][PRINTERSTATE_TITLE_EXT_TEMPER] : '---',
// 				'real_temper_l'		=> isset($array_data['l']) ? $array_data['l'][$array_key_real_temper] : 200,
// 				'real_temper_r'		=> isset($array_data['r']) ? $array_data['r'][$array_key_real_temper] : 200,
				'print_button'		=> t('print_button'),
				'change_left'		=> $change_left,
				'change_right'		=> $change_right,
				'enable_print'		=> ($cr == ERROR_OK) ? 'true' : 'false',
				'error_msg'			=> $error,
				'enable_reslice'	=> $error ? 'true' : 'false',
				'reslice_button'	=> t('reslice_button'),
		);
		
		$this->output->set_status_header(202);
		$this->parser->parse('template/sliceupload/slice_result_ajax', $template_data); //optional
		
		CoreStatus_setInIdle();
		return;
	}
	
	function preview_ajax() {
		$cr = 0;
		$path_image = NULL;
		$display = NULL;
		
// 		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
// 			$rho = $this->input->post('rho');
// 			$theta = $this->input->post('theta');
// 			$delta = $this->input->post('delta');
			$rho = $this->input->get('rho');
			$theta = $this->input->get('theta');
			$delta = $this->input->get('delta');
			
			if ($rho === FALSE || $theta === FALSE || $delta === FALSE) {
				$cr = ERROR_MISS_PRM;
			}
			else if ((int)$rho < 0) {
				$cr = ERROR_WRONG_PRM;
			}
			else {
				$file_info = array();
				$file_cartridge = NULL;
				$color1 = NULL;
				$color2 = NULL;
				
				$cr = ERROR_OK;
				$this->load->helper('slicer');
				$file_cartridge = $this->config->item('temp') . SLICER_FILE_TEMP_DATA;
				if (file_exists($file_cartridge)) {
					$this->load->helper(array('json', 'printerstate'));
					$temp_json = json_read($file_cartridge, TRUE);
					
					if (isset($temp_json['error'])) {
						$this->load->helper('printerlog');
						PrinterLog_logError('read temp data file error', __FILE__, __LINE__);
						$cr = ERROR_INTERNAL;
					}
					else {
// 						unset($temp_json['json']['e']); //FIXME try to find a better way to remove error code
						
						foreach ($temp_json['json'] as $abb_cartridge => $data_cartridge) {
							switch ($abb_cartridge) {
								case 'r':
									$color1 = $data_cartridge[PRINTERSTATE_TITLE_COLOR];
									break;
									
								case 'l':
									$color2 = $data_cartridge[PRINTERSTATE_TITLE_COLOR];
									break;
									
								default:
									$this->load->helper('printerlog');
									PrinterLog_logError('unknown cartridge abb: ' . $abb_cartridge, __FILE__, __LINE__);
									$cr = ERROR_INTERNAL;
									break;
							}
							if ($cr != ERROR_OK) {
								break;
							}
						}
					}
				}
				if ($cr == ERROR_OK) {
					$cr = Slicer_rendering((int)$rho, (int)$theta, (int)$delta, $path_image, $color1, $color2);
				}
				if ($cr == ERROR_OK) {
					//TODO add the possibility of making path everywhere, but not only in /var/www/tmp/
					$this->load->helper('file');
					$file_info = get_file_info(realpath($path_image), array('name'));
					$display = '/tmp/' . $file_info['name'] . '?' . time();
				}
			}
// 		}
// 		else {
// 			$cr = ERROR_WRONG_PRM;
// 		}
		
		if ($cr != ERROR_OK) {
			$display = $cr . " " . t(MyERRMSG($cr));
		}
		$this->output->set_status_header($cr, ($cr != ERROR_OK) ? $display : 'ok');
		$this->output->set_content_type('txt_u');
		$this->load->library('parser');
		$this->parser->parse('template/plaintxt', array('display' => $display));
		
		return;
	}
	
	function preview_change_ajax() {
		$cr = 0;
		$display = NULL;
		
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
		
		$cr = Slicer_setModel($array_data);
		
		$display = $cr . " " . t(MyERRMSG($cr));
		$this->output->set_status_header($cr, $display);
		// 		http_response_code($cr);
		$this->output->set_content_type('txt_u');
		$this->load->library('parser');
		$this->parser->parse('template/plaintxt', array('display' => $display)); //optional
		
		return;
	}
}
