<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Userlib extends MY_Controller {
	public function __construct() {
		parent::__construct();
		
		$this->load->helper(array('userauth', 'errorcode'));
		
		// check local first
		if (UserAuth_checkView()) {
			// get access from sso, and check it again
			if (UserAuth_getUserAccess($_SESSION[USERAUTH_TITLE_TOKEN]) && UserAuth_checkView()) {
				return;
			}
		}
		
		// remove session data, and redirect user to login page
		//TODO think if it's better to logout directly to force login again to get a new token
		UserAuth_removeSessionData();
		header('Location: ' . USERAUTH_URL_REDIRECTION);
		exit;
	}
	
	private function _list_usortCompareName($a, $b) {
		return strcasecmp($a['name'], $b['name']);
	}
	
	private function _list_usortCompareDateDesc($a, $b) {
		return -strcasecmp($a['date'], $b['date']);
	}
	
	public function index() {
		$cr = 0;
		$error = NULL;
		$list_model = array();
		$list_display = array();
		
		$this->lang->load('userlib/modellist', $this->config->item('language'));
		$cr = UserAuth_getUserModelList($list_model, TRUE);
		if ($cr == ERROR_OK) {
			foreach ($list_model as $ele_model) {
				$model_display = array(
						'id'	=> $ele_model[USERAUTH_TITLE_MODEL_ID],
						'name'	=> $ele_model[USERAUTH_TITLE_MODEL_NAME],
						'state'	=> $ele_model[USERAUTH_TITLE_USERLIB_STATE],
						'popup'	=> 'true',
				);
				
				switch ($ele_model[USERAUTH_TITLE_USERLIB_STATE]) {
					case USERAUTH_VALUE_UL_M_READY:
					case USERAUTH_VALUE_UL_P_READY:
						$model_display['link'] = '/userlib/modeldetail?id=' . $ele_model[USERAUTH_TITLE_MODEL_ID];
						$model_display['image'] = isset($ele_model[USERAUTH_TITLE_MODEL_IMAGE])
								? $ele_model[USERAUTH_TITLE_MODEL_IMAGE] : '/images/error.png';
						$model_display['popup'] = 'false';
						break;
						
					case USERAUTH_VALUE_UL_M_UPLOAD:
					case USERAUTH_VALUE_UL_P_UPLOAD:
						$model_display['link'] = '#upload_model_popup';
						$model_display['image'] = '/images/modelImageUploading.png';
						break;
						
					case USERAUTH_VALUE_UL_M_NEW:
						$model_display['link'] = '#prepare_model_popup';
						$model_display['image'] = '/images/modelImagePreparing.png';
						break;
						
					default:
						$model_display['link'] = '#error_model_popup';
						$model_display['image'] = '/images/error.png';
						break;
				}
				
				$list_display[] = $model_display;
			}
		}
		else if ($cr == ERROR_AUTHOR_FAIL) {
			$error = t('get_userlib_author_fail');
		}
		else if ($cr == ERROR_BUSY_PRINTER) {
			$error = t('get_userlib_request_busy');
		}
		else {
			$error = t('get_userlib_errorcode' . $cr);
		}
		
		$this->load->library('parser');
		
		$template_data = array(
				'model_list'		=> $list_display,
				'error_get_list'	=> $error,
				'random_nb'			=> rand(),
				'button_add_model'	=> t('button_add_model'),
				'search_hint'		=> t('search_hint'),
				'message_delete'	=> t('message_delete'),
				'button_delete_ok'	=> t('button_delete_ok'),
				'button_delete_no'	=> t('button_delete_no'),
				'button_ok'			=> t('button_ok'),
				'msg_upload_model'	=> t('msg_upload_model'),
				'msg_prepare_model'	=> t('msg_prepare_model'),
				'msg_error_model'	=> t('msg_error_model'),
				'msg_delete_error'	=> t('msg_delete_error'),
		);
		
		$this->_parseBaseTemplate(t('pagetitle_userlib_modellist'),
				$this->parser->parse('userlib/modellist', $template_data, TRUE));
		
		return;
	}
	
	public function modeldetail() {
		$id_model = (int) $this->input->get('id');
		
		if ($id_model > 0) {
			$cr = 0;
			$list_print = array();
			$model_info = array();
			
			$cr = UserAuth_getUserModelDetail($id_model, TRUE, TRUE, $list_print, $model_info);
			
			if ($cr == ERROR_OK) {
				$show_3dfile = FALSE;
				$show_prints = FALSE;
				$template_data = NULL; //array()
				
				if ($model_info[USERAUTH_TITLE_USERLIB_STATE] == USERAUTH_VALUE_UL_M_READY) {
					$show_3dfile = TRUE;
				}
				foreach ($list_print as $print_info) {
					if ($print_info[USERAUTH_TITLE_USERLIB_STATE] == USERAUTH_VALUE_UL_P_READY) {
						$show_prints = TRUE;
						break;
					}
				}
				
				$this->load->library('parser');
				$this->lang->load('userlib/modeldetail', $this->config->item('language'));
				
				$template_data = array(
						'button_3dfile'			=> t('button_3dfile'),
						'button_prints'			=> t('button_prints'),
						'msg_wait_prepare'		=> t('msg_wait_prepare'), // before js treating page
						'msg_3dfile_n_rdy'		=> t('msg_3dfile_n_rdy'),
						'msg_import_fail'		=> t('msg_import_fail'),
						'msg_download_fail'		=> t('msg_download_fail'),
						'msg_3dfile_import'		=> t('msg_3dfile_import'),
						'msg_3dfile_download'	=> t('msg_3dfile_download'),
						'show_3dfile'			=> $show_3dfile ? 'true' : 'false',
						'show_prints'			=> $show_prints ? 'true' : 'false',
						'model_id'				=> $model_info[USERAUTH_TITLE_MODEL_ID],
						'model_name'			=> $model_info[USERAUTH_TITLE_MODEL_NAME],
				);
				
				$this->_parseBaseTemplate(t('pagetitle_userlib_modeldetail'),
						$this->parser->parse('userlib/modeldetail', $template_data, TRUE));
				
				return;
			}
		}
		
		$this->load->helper('url');
		redirect('/userlib');
		
		return;
	}
	
	public function modelgcodes() {
		$id_model = (int) $this->input->get('id');
		
		if ($id_model > 0) {
			$cr = 0;
			$list_print = array();
			
			$this->lang->load('userlib/modelgcodes', $this->config->item('language'));
			$cr = UserAuth_getUserModelDetail($id_model, TRUE, FALSE, $list_print);
			
			if ($cr == ERROR_OK) {
				$prints_display = array();
				
				foreach ($list_print as $ele_print) {
					$obj_date = date_create($ele_print[USERAUTH_TITLE_PRINT_DATE]);
					if ($obj_date == FALSE) $obj_date = new DateTime('now'); // use now for rollback
					$print_timestamp = $obj_date->getTimestamp();
					$print_display = array(
							'model_id'	=> $id_model,
							'date'		=> $obj_date->format('Y-m-d H:i:s'),
							'preset'	=> t('preset_unknown'),
							'timestamp'	=> $print_timestamp,
// 							'popup'			=> 'true',
					);
					
					switch ($ele_print[USERAUTH_TITLE_USERLIB_STATE]) {
						case USERAUTH_VALUE_UL_P_READY:
							$print_display['link'] = '/userlib/gcodedetail?id=' . $id_model
									. '&t=' . $print_timestamp;
// 									. '&v=' . $ele_print[USERAUTH_TITLE_PRINT_TAG_VIDEO];
							$print_display['image'] = $ele_print[USERAUTH_TITLE_PRINT_IMAGE];
// 							$print_display['popup'] = 'false';
							if (isset($ele_print[USERAUTH_TITLE_PRINT_DESP_PRESET])) {
								$print_display['preset'] = $ele_print[USERAUTH_TITLE_PRINT_DESP_PRESET];
							}
							break;
							
						case USERAUTH_VALUE_UL_P_UPLOAD:
							$print_display['link'] = '#upload_print_popup';
							$print_display['image'] = '/images/modelImageUploading.png';
							break;
							
						default:
							$model_display['link'] = '#error_model_popup';
							$model_display['image'] = '/images/error.png';
							break;
					}
					
					$prints_display[] = $print_display;
				}
				
				// sort list
				usort($prints_display, 'Userlib::_list_usortCompareDateDesc');
				
				$this->load->library('parser');
				
				$template_data = array(
						'print_list'		=> $prints_display,
						'preset_name_title'	=> t('preset_name_title'),
						'search_hint'		=> t('search_hint'),
						'message_delete'	=> t('message_delete'),
						'button_delete_ok'	=> t('button_delete_ok'),
						'button_delete_no'	=> t('button_delete_no'),
						'msg_delete_error'	=> t('msg_delete_error'),
						'button_ok'			=> t('button_ok'),
						'msg_upload_print'	=> t('msg_upload_print'),
						'msg_error_print'	=> t('msg_error_print'),
				);
				
				$this->_parseBaseTemplate(t('pagetitle_userlib_modelgcodes'),
						$this->parser->parse('userlib/modelgcodes', $template_data, TRUE));
				
				return;
			}
		}
		
		$this->load->helper('url');
		redirect('/userlib');
		
		return;
	}
	
	public function gcodedetail() {
		$cr = 0;
		$error = NULL;
		$model_id = (int) $this->input->get('id');
		$print_date = (int) $this->input->get('t');
// 		$data_json = array();
		$array_data = array();
		$print_info = array();
		$check_left = NULL;
		$check_right = NULL;
		$change_left = NULL;
		$change_right = NULL;
		$enable_print = TRUE;
		$key_suggest_temper = 'suggest_temperature';
		$bicolor = ($this->config->item('nb_extruder') >= 2);
		$heat_bed = $this->config->item('heat_bed');
		$contain_heat_bed = FALSE;
		
		if ($model_id && $print_date) {
			$cr = UserAuth_getUserPrint($model_id, $print_date, $print_info);
		}
		else {
			$cr = ERROR_MISS_PRM;
		}
		
		if ($cr != ERROR_OK) {
			$this->load->helper('url');
			redirect('/userlib/modelgcodes?id=' . $model_id);
			
			return;
		}
		
		$this->load->library('parser');
		$this->load->helper('printerstate');
		$this->lang->load('printerstoring/gcodedetail', $this->config->item('language'));
		$this->lang->load('userlib/gcodedetail', $this->config->item('language'));
		
		$check_left = $check_right = t('filament_ok');
		$change_left = $change_right = t('change_filament');
		
		foreach (array('r', 'l') as $abb_filament) {
			$data_cartridge = array();
			$tmp_ret = 0;
			$volume_need = 0;
			$key_length = NULL;
			$key_material = NULL;
				
			if ($abb_filament == 'l') {
				$key_length = USERAUTH_TITLE_PRINT_DESP_LENG2;
				$key_material = USERAUTH_TITLE_PRINT_DESP_MAT2;
			}
			else { // $abb_filament == 'r'
				$key_length = USERAUTH_TITLE_PRINT_DESP_LENG1;
				$key_material = USERAUTH_TITLE_PRINT_DESP_MAT1;
			}
				
			if ($print_info[$key_length] > 0) {
				$volume_need = $print_info[$key_length];
			}
			else if ($abb_filament == 'l') {
				$check_left = t('filament_not_need');
			}
			else { // $abb_filament == 'r'
				$check_right = t('filament_not_need');
			}
				
			// check mono extruder case (normally, it's not necessary)
			if ($bicolor == FALSE && $abb_filament == 'l') {
				$tmp_ret = ERROR_MISS_LEFT_CART;
			}
			else {
				$tmp_ret = PrinterState_checkFilament($abb_filament, $volume_need, $data_cartridge);
			}
				
			if (in_array($tmp_ret, array(
					ERROR_OK, ERROR_MISS_LEFT_FILA, ERROR_MISS_RIGT_FILA,
					ERROR_LOW_LEFT_FILA, ERROR_LOW_RIGT_FILA,
			))) {
				$array_data[$abb_filament] = array(
						PRINTERSTATE_TITLE_COLOR		=> $data_cartridge[PRINTERSTATE_TITLE_COLOR],
						PRINTERSTATE_TITLE_EXT_TEMPER	=> $data_cartridge[PRINTERSTATE_TITLE_EXT_TEMPER],
						PRINTERSTATE_TITLE_MATERIAL		=> $data_cartridge[PRINTERSTATE_TITLE_MATERIAL],
				);
					
				// set default temperature if pla
				if ($data_cartridge[PRINTERSTATE_TITLE_MATERIAL] == PRINTERSTATE_DESP_MATERIAL_PLA) {
					$array_data[$abb_filament][PRINTERSTATE_TITLE_EXT_TEMPER] = PRINTERSTATE_VALUE_FILAMENT_PLA_PRINT_TEMPER;
				}
			}
			else {
				$array_data[$abb_filament] = array(
						PRINTERSTATE_TITLE_COLOR		=> PRINTERSTATE_VALUE_DEFAULT_COLOR,
						PRINTERSTATE_TITLE_EXT_TEMPER	=> 0,
						PRINTERSTATE_TITLE_MATERIAL		=> NULL,
				);
			}
			$array_data[$abb_filament][PRINTERSTATE_TITLE_NEED_L]	= $volume_need;
			$array_data[$abb_filament][$key_suggest_temper]			= 0;
				
			if ($volume_need > 0 && $print_info[$key_material] != $array_data[$abb_filament][PRINTERSTATE_TITLE_MATERIAL]) {
				$required_material = t('require_' . $print_info[$key_material]);
				
				if ($abb_filament == 'l') {
					$check_left = $required_material;
				}
				else { // $abb_filament == 'r'
					$check_right = $required_material;
				}
				
				$enable_print = FALSE; // disable print when material is different
			}
			else {
				// treat error
				switch ($tmp_ret) {
					case ERROR_OK:
						// do nothing if no error
						break;
						
					case ERROR_LOW_RIGT_FILA:
						$check_right = t('filament_not_enough');
						break;
						
					case ERROR_MISS_RIGT_FILA:
						$check_right = t('filament_unloaded');
						$change_right = t('load_filament');
						break;
						
					case ERROR_MISS_RIGT_CART:
						$check_right = t('filament_empty');
						$change_right = t('load_filament');
						break;
						
					case ERROR_LOW_LEFT_FILA:
						$check_left = t('filament_not_enough');
						break;
						
					case ERROR_MISS_LEFT_FILA:
						$check_left = t('filament_unloaded');
						$change_left = t('load_filament');
						break;
						
					case ERROR_MISS_LEFT_CART:
						$check_left = t('filament_empty');
						$change_left = t('load_filament');
						break;
						
					default:
						$this->load->helper('printerlog');
						PrinterLog_logError('unexpected return when getting detail of gcode library model: ' . $cr, __FILE__, __LINE__);
						
						// assign error message if necessary
						if ($abb_filament == 'l') {
							$check_left = t('filament_error');
						}
						else { // $abb_filament == 'r'
							$check_right = t('filament_error');
						}
						break;
				}
			}
				
			// block print
			if ($enable_print == TRUE && $tmp_ret != ERROR_OK && $volume_need > 0) {
				$enable_print = FALSE;
			}
		}
		
// 		$print_info[USERAUTH_TITLE_PRINT_DESP_TEMPB] = 60;
// 		// heat bed prepare (only display when we have heat bed and also need heat bed)
		// heat bed prepare (display adjust slider when we have heat bed and also need heat bed, select slider when we have it only)
		if ($print_info[USERAUTH_TITLE_PRINT_DESP_TEMPB] > 0) {
			$contain_heat_bed = TRUE;
			if ($heat_bed == FALSE) {
				$enable_print = FALSE;
				$error = t('msg_need_heatbed');
			}
		}
// 		else {
// 			$heat_bed = FALSE;
// 		}
		
		$template_data = array(
				'id'					=> $model_id . '|' . $print_date,
				'title'					=> $print_info[USERAUTH_TITLE_PRINT_DESP_NAME],
				'photo_title'			=> t('photo_title'),
				'photo_link'			=> $print_info[USERAUTH_TITLE_PRINT_IMAGE],
				'title_current'			=> t('filament_title'),
				'show_video'			=> $print_info[USERAUTH_TITLE_PRINT_VIDEO] ? 'true' : 'false',
				'video_title'			=> t('video_title'),
				'video_url'				=> $print_info[USERAUTH_TITLE_PRINT_VIDEO],
				'msg_ok'				=> t('filament_ok'),
				'state_c_l'				=> $array_data['l'][PRINTERSTATE_TITLE_COLOR],
				'state_c_r'				=> $array_data['r'][PRINTERSTATE_TITLE_COLOR],
				'state_f_l'				=> $check_left,
				'state_f_r'				=> $check_right,
				'need_filament_l'		=> $array_data['l'][PRINTERSTATE_TITLE_NEED_L],
				'need_filament_r'		=> $array_data['r'][PRINTERSTATE_TITLE_NEED_L],
				'temper_filament_l'		=> $array_data['l'][PRINTERSTATE_TITLE_EXT_TEMPER],
				'temper_filament_r'		=> $array_data['r'][PRINTERSTATE_TITLE_EXT_TEMPER],
				'temper_suggest_l'		=> $array_data['l'][$key_suggest_temper],
				'temper_suggest_r'		=> $array_data['r'][$key_suggest_temper],
				'print_button'			=> t('print_button'),
				'change_filament_l'		=> $change_left,
				'change_filament_r'		=> $change_right,
				'enable_print'			=> $enable_print ? 'true' : 'false',
				'temp_adjustments_l'	=> t('temp_adjustments_l'),
				'temp_adjustments_r'	=> t('temp_adjustments_r'),
				'temp_adjustments'		=> t('temp_adjustments'),
				'advanced'				=> t('advanced'),
				'gcode_link'			=> t('gcode_link'),
				'2drender_link'			=> t('2drender_link'),
				'temper_max'			=> PRINTERSTATE_TEMPER_CHANGE_MAX,
				'temper_min'			=> PRINTERSTATE_TEMPER_CHANGE_MIN,
				'temper_delta'			=> PRINTERSTATE_TEMPER_CHANGE_VAL,
				'bicolor'				=> $bicolor ? 'true' : 'false',
				'extrud_multiply'		=> t('extrud_multiply'),
				'left_extrud_mult'		=> t('left_extrud_mult'),
				'right_extrud_mult'		=> t('right_extrud_mult'),
				'extrud_r'				=> PRINTERSTATE_EXT_MULTIPLY_DEFAULT,
				'extrud_l'				=> PRINTERSTATE_EXT_MULTIPLY_DEFAULT,
				'extrud_min'			=> PRINTERSTATE_EXT_MULTIPLY_MIN,
				'extrud_max'			=> PRINTERSTATE_EXT_MULTIPLY_MAX,
				'msg_gcode_download'	=> t('msg_gcode_download'),
				'msg_download_fail'		=> t('msg_download_fail'),
				'error'					=> $error,
				'title_heatbed'			=> t('title_heatbed'),
				'enable_heatbed'		=> t('enable_heatbed'),
				'button_bed_off'		=> t('button_bed_off'),
				'heat_bed'				=> $heat_bed ? 'true' : 'false',
				'contain_heat_bed'		=> $contain_heat_bed ? 'true' : 'false',
				'checked_heatbed'		=> $heat_bed ? 'checked="checked"' : NULL,
				'value_heatbed'			=> (int) $print_info[USERAUTH_TITLE_PRINT_DESP_TEMPB],
				'bed_temper_max'		=> PRINTERSTATE_TEMPER_MAX_H,
				'bed_temper_pla'		=> PRINTERSTATE_TEMPER_BED_PLA,
				'bed_temper_abs'		=> PRINTERSTATE_TEMPER_BED_ABS,
		);
		
		// parse all page
		$this->_parseBaseTemplate(t('userlib_gcodedetail_pagetitle'),
				$this->parser->parse('userlib/gcodedetail', $template_data, TRUE));
		
		return;
	}
	
	public function storemodel() {
		$template_data = NULL; //array()
		$error = NULL;
		$model_id = 0;
		
		$this->load->library('parser');
		$this->load->helper(array('slicer', 'userauth'));
		//TODO need generate own language file
		$this->lang->load('sliceupload/upload', $this->config->item('language'));
		$this->lang->load('printerstoring/storestl', $this->config->item('language'));
		$this->lang->load('userlib/storemodel', $this->config->item('language'));
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$array_model = array();
			$upload_config = array (
					'upload_path'	=> $this->config->item('temp'),
					'allowed_types'	=> '*',
					'overwrite'		=> FALSE,
					'remove_spaces'	=> TRUE,
					'encrypt_name'	=> TRUE,
			);
// 			$model_name = $this->input->post('name');
			$model_id = (int) $this->input->post('id');
			
			$this->load->library('upload', $upload_config);
			
			if ($this->upload->do_upload('file')) {
				$model = $this->upload->data();
				$model_ext = strtolower($model['file_ext']);
				
				if (!is_null($model_ext) && $model_ext != '.stl') {
					// we can treat extension error differently
					$error = t('fail_message_ext');
				}
				else {
					$array_model[] = $model['full_path'];
				}
			}
			else if ($this->upload->do_upload('file_c1')) {
				$first_combine = TRUE;
				$model = $this->upload->data();
				$model_ext = strtolower($model['file_ext']);
				
				if (!is_null($model_ext) && $model_ext != '.stl') {
					// we can treat extension error differently
					$error = t('fail_message_ext');
				}
				else {
					$array_model[] = $model['full_path'];
					
					foreach (array('file_c2') as $file_key) {
						if ($this->upload->do_upload($file_key)) {
							$first_combine = FALSE;
							$model = $this->upload->data();
							$model_ext = strtolower($model['file_ext']);
								
							if (!is_null($model_ext) && $model_ext != '.stl') {
								// we can treat extension error differently
								$error = t('fail_message_ext');
							}
							else {
								$array_model[] = $model['full_path'];
							}
						}
						else if ($first_combine == TRUE) {
							$error = t('fail_message');
							break;
						}
					}
				}
			}
			else {
				// treat error - missing gcode file
				$error = t('fail_message');
			}
			
			if ($model_id <= 0) {
				$error = t('fail_message');
			}
			else if (is_null($error) && count($array_model)) {
				$ret_val = UserAuth_uploadUserModel($model_id, FALSE, $array_model);
				
				if ($ret_val == ERROR_OK) {
					$this->load->helper('url');
					redirect('/userlib');
					
					return;
				}
				else {
					$error = t('msg_fail_prepare_upload');
				}
			}
		}
		
		//TODO reunion this redundancy code part with sliceupload controller
		if (0 == strlen(@file_get_contents($this->config->item('temp') . SLICER_FILE_HTTP_PORT))
		&& FALSE == $this->config->item('simulator')) {
			$this->output->set_header('Location: /sliceupload/restart?inboot=1&userlib=1');
			
			return;
		}
		else if (!Slicer_checkOnline(FALSE)) {
			$this->output->set_header('Location: /sliceupload/restart?userlib=1');
			
			return;
		}
		
		$model_id = $this->input->get('id');
		if ($model_id === FALSE) $model_id = 0;
		
		// parse the main body
		$template_data = array(
				'select_hint'		=> t('select_hint'),
				'select_hint_multi'	=> t('select_hint_multi'),
				'header_single' 	=> t('header_single'),
				'header_multi'		=> t('header_multi'),
				'upload_button'		=> t('upload_button'),
				'save_overwrite'	=> t('save_overwrite'),
				'button_save_ok'	=> t('button_save_ok'),
				'button_save_no'	=> t('button_save_no'),
				'error'				=> $error,
				'model_id'			=> $model_id,
				'title_name'		=> t('name'),
				'bicolor'			=> ($this->config->item('nb_extruder') >= 2) ? 'true' : 'false',
		);
		
		$this->_parseBaseTemplate(t('userlib_storemodel_pagetitle'),
				$this->parser->parse('userlib/storemodel', $template_data, TRUE));
		
		return;
	}
	
	public function deletemodel_ajax() {
		$ret_val = ERROR_MISS_PRM;
		$model_id = (int) $this->input->post('id');
		
		if ($model_id) {
			$ret_val = UserAuth_deleteUserModel($model_id);
			if ($ret_val != ERROR_OK) {
				$this->load->helper('printerlog');
				PrinterLog_logDebug('delete user model return code: ' . $ret_val, __FILE__, __LINE__);
			}
		}
		$this->output->set_status_header($ret_val, MyERRMSG($ret_val));
		
		return;
	}
	
	public function deleteprint_ajax() {
		$ret_val = 0;
		$model_id = (int) $this->input->post('id');
		$print_timestamp = (int) $this->input->post('time');
		
		if ($model_id && $print_timestamp) {
			$ret_val = UserAuth_deleteUserPrint($model_id, $print_timestamp);
			if ($ret_val != ERROR_OK) {
				$this->load->helper('printerlog');
				PrinterLog_logDebug('delete user print return code: ' . $ret_val, __FILE__, __LINE__);
			}
		}
		else {
			$ret_val = ERROR_MISS_PRM;
		}
		$this->output->set_status_header($ret_val, MyERRMSG($ret_val));
		
		return;
	}
	
	public function addmodel_ajax() {
		$model_id = 0;
		$ret_val = ERROR_MISS_PRM;
		$model_name = $this->input->post('name');
		
		if ($model_name) {
			$ret_val = UserAuth_requestNewModelId($model_name, $model_id);
			
			if (in_array($ret_val, array(ERROR_OK, ERROR_FULL_PRTLST)) && $model_id > 0) {
				$display = json_encode(array(
						'id'	=> $model_id,
						'exist'	=> ($ret_val == ERROR_FULL_PRTLST),
				));
				
				$this->load->library('parser');
				$this->parser->parse('plaintxt', array('display' => $display));
				$this->output->set_content_type('json');
				if ($ret_val == ERROR_FULL_PRTLST) {
					$this->output->set_status_header(202);
				}
				
				return;
			}
		}
		$this->output->set_status_header($ret_val, MyERRMSG($ret_val));
		
		return;
	}
	
	public function preparemodel_ajax() {
		$cr = ERROR_MISS_PRM;
		$model_id = (int) $this->input->post('id');
		
		if ($model_id) {
			if (!UserAuth_prepareUserLibCacheFolder()) {
				$cr = ERROR_INTERNAL;
			}
			else {
				$list_download = array();
				
				$ret_val = UserAuth_getModelDownloadList($model_id, $list_download);
				if ($ret_val == ERROR_OK) {
					$cr = ERROR_OK;
					foreach ($list_download as $stl_path => $stl_url) {
						if (file_exists($stl_path . USERAUTH_VALUE_SUFFIX_CACHE_T)) {
							$cr = 403;
							break;
						}
						else {
							$fp = @fopen($stl_url, 'r');
							if (!$fp) {
								$this->_exitWithError500('sso remote file url failed');
							}
// 							else if (flock($fp, LOCK_EX|LOCK_NB)) {
// 								$cr = 403;
// 								break;
// 							}
							else {
								file_put_contents($stl_path . USERAUTH_VALUE_SUFFIX_CACHE_T, $fp, LOCK_EX);
								fclose($fp);
// 								flock($fp, LOCK_UN);
								
								rename($stl_path . USERAUTH_VALUE_SUFFIX_CACHE_T, $stl_path); // rename
							}
						}
					}
				}
			}
		}
		$this->output->set_status_header($cr, MyERRMSG($cr));
		
		return;
	}
	
	public function prepareprint_ajax() {
		$cr = ERROR_MISS_PRM;
		$userlib_id =  $this->input->post('id');
		
		if ($userlib_id) {
			if (!UserAuth_prepareUserLibCacheFolder()) {
				$cr = ERROR_INTERNAL;
			}
			
			if (FALSE === strpos($userlib_id, '|')) {
				$cr = ERROR_WRONG_PRM;
			}
			else {
				$list_download = array();
				$tmp_array = explode('|', $userlib_id);
				$model_id = (int) $tmp_array[0];
				$timestamp = (int) $tmp_array[1];
				
				$ret_val = UserAuth_getPrintDownloadList($model_id, $timestamp, $list_download);
				if ($ret_val == ERROR_OK) {
					$cr = ERROR_OK;
					foreach ($list_download as $gcode_path => $gcode_url) {
						if (file_exists($gcode_path . USERAUTH_VALUE_SUFFIX_CACHE_T)) {
							$cr = 403;
							break;
						}
						else {
							$fp = @fopen($gcode_url, 'r');
							if (!$fp) {
								$this->_exitWithError500('sso remote file url failed');
							}
							else {
								file_put_contents($gcode_path . USERAUTH_VALUE_SUFFIX_CACHE_T, $fp, LOCK_EX);
								fclose($fp);
								
								rename($gcode_path . USERAUTH_VALUE_SUFFIX_CACHE_T, $gcode_path); // rename
							}
						}
					}
				}
			}
		}
		$this->output->set_status_header($cr, MyERRMSG($cr));
		
		return;
	}
	
	public function gcode_ajax() {
		$cr = ERROR_MISS_PRM;
		$userlib_id = $this->input->get('id');
		
		if ($userlib_id) {
			if (FALSE === strpos($userlib_id, '|')) {
				$cr = ERROR_WRONG_PRM;
			}
			else {
				$tmp_array = explode('|', $userlib_id);
				$model_id = (int) $tmp_array[0];
				$timestamp = (int) $tmp_array[1];
				
				$this->load->helper('printer');
				
				$cr = Printer_getFileFromUserLib($model_id, $timestamp, $gcode_path);
				if ($cr == ERROR_OK) {
					if (file_exists($gcode_path)) {
						$this->_sendFileContent($gcode_path, 'library.gcode');
						
						return;
					}
					else {
						$cr = ERROR_INTERNAL;
					}
				}
			}
		}
		$this->output->set_status_header($cr, MyERRMSG($cr));
		
		return;
	}
	
	public function importmodel_ajax() {
		$cr = ERROR_MISS_PRM;
		$model_id = (int) $this->input->post('id');
		
		if ($model_id) {
			$cr = UserAuth_importUserModel($model_id);
		}
		$this->output->set_status_header($cr, MyERRMSG($cr));
		
		return;
	}
}
