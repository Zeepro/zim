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
	
	private function _clean_upload_folder() {
		$upload_dir = array();
		$current_time = time();
		$upload_path = $this->config->item('temp');
		
		$this->load->helper('file');
		
		$upload_dir = @get_dir_file_info($upload_path);
		
		foreach($upload_dir as $file) {
			$fileinfo = pathinfo($file['server_path']);
			if (is_array($fileinfo) && array_key_exists('extension', $fileinfo)
					&& in_array(strtolower($fileinfo['extension']), array('stl', 'amf', 'obj'))) {
				$alive_time = $file['date'] + 172800; // 48 hours alive duration (3600*48)
				
				if ($alive_time <= $current_time) {
					@unlink($file['server_path']);
					
					$this->load->helper('printerlog');
					PrinterLog_logDebug('deleted old model file: ' . $file['server_path']);
				}
			}
		}
		
		return;
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
// 					'encrypt_name'	=> TRUE,
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
						$error = t('fail_message');
						break;
					}
				}
			}
			else {
				// treat error - missing gcode file
				$error = t('fail_message');
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
		if (0 == strlen(@file_get_contents($this->config->item('temp') . SLICER_FILE_HTTP_PORT))
				&& FALSE == $this->config->item('simulator')) {
			$this->output->set_header('Location: /sliceupload/restart?inboot=1');
			
			return;
		}
		else if (!Slicer_checkOnline(FALSE)) {
			$this->output->set_header('Location: /sliceupload/restart');
			
			return;
		}
		
		// cleanup old upload temporary files
		$this->_clean_upload_folder();
		
		if (ERROR_OK == Slicer_listModel($response) && $response != "[]"
				&& ERROR_OK == Slicer_checkPlatformModel()) {
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
		$ret_val = 0;
		$status_current = NULL;
		$body_page = NULL;
		$list_display = array();
		$template_data = array();
		$current_stage = 'wait_slice';
		$current_scale = 100;
		$current_xrot = 0;
		$current_yrot = 0;
		$current_zrot = 0;
		
		// redirect the client when in slicing
		$this->load->helper('corestatus');
		$ret_val = CoreStatus_checkInIdle($status_current);
		// check status in slicing
		if ($ret_val == FALSE || $status_current == CORESTATUS_VALUE_SLICE) {
			$this->output->set_header('Location: /sliceupload/slicestatus');
			return;
		}
		
		$this->load->helper(array('zimapi', 'printerstate'));
		
		if ($this->input->get('callback') !== FALSE) {
			$current_stage = 'wait_print';
		}
		else { // need preset list only in wait slice mode
			$list_preset = ZimAPI_getPresetListAsArray();
			
			foreach ($list_preset as $preset) {
				$list_display[] = array(
						'name'		=> $preset[ZIMAPI_TITLE_PRESET_NAME],
						'id'		=> $preset[ZIMAPI_TITLE_PRESET_ID],
						'infill'	=> $preset[ZIMAPI_TITLE_PRESET_INFILL],
						'skirt'		=> $preset[ZIMAPI_TITLE_PRESET_SKIRT],
						'raft'		=> $preset[ZIMAPI_TITLE_PRESET_RAFT],
						'support'	=> $preset[ZIMAPI_TITLE_PRESET_SUPPORT],
				);
			}
			sort($list_display);
			
			try {
				$tmp_string = NULL;
				$tmp_array = NULL;
				
				$this->load->helper('slicer');
				$ret_val = Slicer_listModel($tmp_string);
				$tmp_array = json_decode($tmp_string, TRUE);
				
				if ($ret_val == ERROR_OK && count($tmp_array)) {
					$current_scale = $tmp_array[0][SLICER_PRM_SCALE];
					$current_xrot = $tmp_array[0][SLICER_PRM_XROT];
					$current_yrot = $tmp_array[0][SLICER_PRM_YROT];
					$current_zrot = $tmp_array[0][SLICER_PRM_ZROT];
				}
			} catch (Exeception $e) {
				$this->load->helper('printerlog');
				PrinterLog_logError('synchronize slicer model info error', __FILE__, __LINE__);
			}
		}
		
		$this->load->library('parser');
		$this->lang->load('sliceupload/slice', $this->config->item('language'));
		
		// parse the main body
		$template_data = array(
 				'home'			=> t('home'),
				'back'			=> t('back'),
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
				'color_default'	=> PRINTERSTATE_VALUE_DEFAULT_COLOR,
				'model_scale'	=> $current_scale,
				'model_xrot'	=> $current_xrot,
				'model_yrot'	=> $current_yrot,
				'model_zrot'	=> $current_zrot,
				'preview_fail'	=> t('preview_fail'),
				'setmodel_fail'	=> t('setmodel_fail'),
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
			$this->output->set_header('Location: /sliceupload/slice');
			return;
		}
		
		$this->load->library('parser');
		$this->lang->load('sliceupload/slice', $this->config->item('language'));
		
		// parse the main body
		$template_data = array(
				'wait_in_slice'	=> t('wait_in_slice'),
				'slice_suffix'	=> t('slice_suffix'),
				'cancel_button'	=> t('cancel_button'),
				'wait_cancel'	=> t('wait_cancel'),
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
	
	function restart() {
		$template_data = array();
		$body_page = NULL;
		$in_boot = $this->input->get('inboot');
		
		$this->load->library('parser');
		$this->lang->load('sliceupload/upload', $this->config->item('language'));
		
		// parse the main body
		$template_data = array(
				'home'			=> t('home'),
				'check_in_boot'	=> $in_boot ? 'true' : 'false',
				'wait_msg'		=> $in_boot ? t('wait_in_boot') : t('wait_in_restart'),
		);
		$body_page = $this->parser->parse('template/sliceupload/restart', $template_data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . t('sliceupload_slice_pagetitle') . '</title>',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
		
		return;
	}
	
	function reducesize() {
		$template_data = array();
		$body_page = NULL;
		$id = 0;
		$xsize = 0;
		$ysize = 0;
		$zsize = 0;
		$scalemax = 0;
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$scale_set = (int) $this->input->post('sizepercentage');
			
			$id = (int) $this->input->post('id');
			$xsize = (float) $this->input->post('x');
			$ysize = (float) $this->input->post('y');
			$zsize = (float) $this->input->post('z');
			$scalemax = (float) $this->input->post('ms');
			
			
		}
		else {
			$id = (int) $this->input->get('id');
			$xsize = (float) $this->input->get('x');
			$ysize = (float) $this->input->get('y');
			$zsize = (float) $this->input->get('z');
			$scalemax = (float) $this->input->get('ms');
		}

		$scalemax = floor($scalemax);
		// simple check of passing value
		if ($xsize * $ysize * $zsize * $scalemax == 0) {
			$this->output->set_header('Location: /sliceupload/upload');
			return;
		}
		
		
		$this->load->library('parser');
		$this->lang->load('sliceupload/upload', $this->config->item('language'));
		
		// parse the main body
		$template_data = array(
				'home'				=> t('home'),
				'back'				=> t('back'),
				'cancel_button'		=> t('cancel_button'),
				'max_percent'		=> $scalemax,
				'xsize'				=> $xsize,
				'ysize'				=> $ysize,
				'zsize'				=> $zsize,
				'id'				=> $id,
				'reducesize_title'	=> t('reducesize_title'),
				'reducesize_text'	=> t('reducesize_text'),
				'reducesize_scale'	=> t('reducesize_scale'),
				'reduced_size'		=> t('reduced_size'),
				'resize_button'		=> t('resize_button'),
		);
		$body_page = $this->parser->parse('template/sliceupload/reducesize', $template_data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . t('sliceupload_slice_pagetitle') . '</title>' . "\n"
						. '<link rel="stylesheet" href="/assets/jquery-mobile-fluid960.min.css">',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
		
		return;
	}
	
	function gcode($mode = "display")
	{
		$this->load->library('parser');
		$view_data = array(
				'home'		=> t('back')
		);
		$body_page = $this->parser->parse('/template/sliceupload/gcode.php', $view_data, TRUE);
		$template_data = array(
				'headers'		=> '<title>Zim - G-code</title>',
				'contents'		=> $body_page
		);
		$this->parser->parse('template/basetemplate', $template_data);
		return;
	}
	
	function add_model_ajax() {
		$cr = ERROR_OK;
		$display = NULL;
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$filename = $this->input->post('file');
			if ($filename) {
				$array_model = json_decode($filename, TRUE);
				$number_model = count($array_model);
				if ($number_model) {
					$tmp_i = 0;
					
					for ($tmp_i = 0; $tmp_i < $number_model; $tmp_i++) {
						$array_model[$tmp_i] = $this->config->item('temp') . $array_model[$tmp_i];
						if (!file_exists($array_model[$tmp_i])) {
							$cr = ERROR_WRONG_PRM;
							break;
						}
					}
					
					if ($cr == ERROR_OK) {
						$array_return = array();
						
						$this->load->helper('slicer');
// 						$cr = Slicer_addModel($array_model);
						$cr = Slicer_addModel($array_model, FALSE, $array_return);
						if ($cr == ERROR_OK) {
							try {
								if ($array_return[SLICER_TITLE_MAXSCALE] < 100) {
									$display = json_encode($array_return);
									$this->output->set_status_header(202);
									$this->output->set_content_type('txt_u');
									$this->load->library('parser');
									$this->parser->parse('template/plaintxt', array('display' => $display)); //optional
									
									return;
								}
							} catch (Exception $e) {
								$cr = ERROR_INTERNAL;
							}
						}
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
		
		return;
	}
	
	function slice_model_ajax() {
		$cr = 0;
		$array_cartridge = array();
		$display = NULL;
		$id_preset = $this->input->get('id');
		$density = $this->input->get('density');
		$skirt = $this->input->get('skirt');
		$raft = $this->input->get('raft');
		$support = $this->input->get('support');
		$array_setting = array();
		
		$this->load->helper('slicer');
		
		// set and load preset into slicer
		if ($id_preset) {
			if ($id_preset == 'previous') {
				$cr = ZimAPI_getPreset($id_preset);
			}
			else {
				$cr = ZimAPI_setPreset($id_preset);
			}
		}
		else {
			$cr = ERROR_MISS_PRM;
		}
		
		if ($cr == ERROR_OK) {
			$cr = Slicer_reloadPreset();
		}
		
		// load 4 extra parameters
		//TODO finish me (syntax in comment need be changed to function)
// 		if ($density !== FALSE) {
// 			if (FALSE == strpos('%', $density)) {
// 				$density = (float)$density;
// 				if ($density <= 0 || $density >= 1) {
// 					$cr = ERROR_MISS_PRM;
// 					break;
// 				}
// 			}
// 			$array_setting['fill_density'] = $density;
// 		}
// 		if ($skirt !== FALSE) {
// 			$array_setting['skirts'] = ((int)$skirt == 1) ? 1 : 0;
// 		}
// 		if ($raft !== FALSE) {
// 			$array_setting['raft_layers'] = ((int)$raft == 1) ? 1 : 0;
// 		}
// 		if ($support !== FALSE) {
// 			$array_setting['support_material'] = ((int)$support == 1) ? 1 : 0;
// 		}
// 		if (count($array_setting) == 0) {
// 			$cr = ERROR_MISS_PRM;
// 		}
// 		else if ($cr == ERROR_OK) {
// 			$cr = Slicer_changeParameter($array_setting);
// 		}
		
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
		$ret_val = 0;
		$cr = 0;
		$array_data = array();
		$status_current = NULL;
		$display = NULL;
		
		$this->load->helper(array('printerstate', 'slicer'));
		$this->load->library('parser');
		
		$ret_val = CoreStatus_checkInIdle($status_current);
		if ($ret_val == TRUE) {
			$cr = 403;
			$this->output->set_status_header($cr);
			
			return;
		}
		$ret_val = PrinterState_checkBusyStatus($status_current, $array_data);
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
			CoreStatus_setInIdle();
		}
		
		if (!in_array($cr, array(
				ERROR_OK, ERROR_INTERNAL,
				ERROR_LOW_RIGT_FILA, ERROR_LOW_LEFT_FILA,
				ERROR_MISS_RIGT_FILA, ERROR_MISS_LEFT_FILA,
				ERROR_MISS_RIGT_CART, ERROR_MISS_LEFT_CART,
		))) {
			$this->load->helper('printerlog');
			PrinterLog_logError('unknown return after slicing: ' . $cr, __FILE__, __LINE__);
			$cr = ERROR_INTERNAL;
		}
		
		if ($cr == ERROR_INTERNAL) {
			$this->output->set_status_header($cr);
		}
		else {
			$this->output->set_status_header(202);
		}
		$display = $cr . " " . t(MyERRMSG($cr));
		$this->output->set_content_type('txt_u');
		$this->parser->parse('template/plaintxt', array('display' => $display)); //optional
		
		return;
	}
	
	function slice_result_ajax() {
		//FIXME need to rewrite this function to read always two cartridges and treat with temporary sliced model information
		$template_data = array();
		$ret_val = 0;
		$array_data = array();
		$cr = ERROR_OK;
		$state_f_l = NULL;
		$state_f_r = NULL;
		$change_left = '';
		$change_right = '';
		$data_json = array();
// 		$array_key_real_temper = 'real_temperature'; //TODO think about if we need to declare this key name in helper or not
		$error = NULL;
		$option_selected = 'selected="selected"';
		$select_disable = 'disabled="disabled"';
		$array_need = array('r' => 'false', 'l' => 'false');
		
		$this->load->helper(array('printerstate', 'slicer'));
		$this->load->library('parser');
		$this->lang->load('sliceupload/slice_status_ajax', $this->config->item('language'));
		
		$cr = PrinterState_getSlicedJson($data_json);
		
		if ($cr != ERROR_OK) {
			$display = $cr . " " . t(MyERRMSG($cr));
			$this->output->set_status_header($cr);
			$this->output->set_content_type('txt_u');
			$this->parser->parse('template/plaintxt', array('display' => $display)); //optional
			
			return;
		}
		else {
			$material = NULL;
			
			//TODO treat mono color and mono extruder case
			$state_f_l = $state_f_r = t('filament_ok');
			$change_left = $change_right = t('change_filament');
			
			foreach (array('r', 'l') as $abb_filament) {
				$data_cartridge = array();
				$data_slice = array();
				$tmp_ret = 0;
				$volume_need = 0;
				
				if (isset($data_json[$abb_filament])) {
					$data_slice = $data_json[$abb_filament];
					if (isset($data_slice[PRINTERSTATE_TITLE_NEED_L])) {
						$volume_need = $data_slice[PRINTERSTATE_TITLE_NEED_L];
						if ($volume_need > 0) {
							$array_need[$abb_filament] = 'true';
						}
					}
				}
				else if ($abb_filament == 'l') {
					$state_f_l = t('filament_not_need');
				}
				else { // $abb_filament == 'r'
					$state_f_r = t('filament_not_need');
				}
				
				$tmp_ret = PrinterState_checkFilament($abb_filament, $volume_need, $data_cartridge);
				if (in_array($tmp_ret, array(
						ERROR_OK, ERROR_MISS_LEFT_FILA, ERROR_MISS_RIGT_FILA,
						ERROR_LOW_LEFT_FILA, ERROR_LOW_RIGT_FILA,
				))) {
					$array_data[$abb_filament] = array(
							PRINTERSTATE_TITLE_COLOR		=> $data_cartridge[PRINTERSTATE_TITLE_COLOR],
							PRINTERSTATE_TITLE_EXT_TEMPER	=> $data_cartridge[PRINTERSTATE_TITLE_EXT_TEMPER],
							PRINTERSTATE_TITLE_EXT_TEMP_1	=> $data_cartridge[PRINTERSTATE_TITLE_EXT_TEMP_1],
							PRINTERSTATE_TITLE_NEED_L		=> $volume_need,
					);
				}
				else {
					$array_data[$abb_filament] = array(
							PRINTERSTATE_TITLE_COLOR		=> PRINTERSTATE_VALUE_DEFAULT_COLOR,
							PRINTERSTATE_TITLE_EXT_TEMPER	=> SLICER_VALUE_DEFAULT_TEMPER,
							PRINTERSTATE_TITLE_EXT_TEMP_1	=> SLICER_VALUE_DEFAULT_FIRST_TEMPER,
							PRINTERSTATE_TITLE_NEED_L		=> $volume_need,
					);
				}
				
				// treat error
				switch ($tmp_ret) {
					case ERROR_OK:
						// do nothing if no error
						break;
						
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
						PrinterLog_logError('unexpected return when generating slicing result: ' . $cr, __FILE__, __LINE__);
						
						// assign error message if necessary
						if ($abb_filament == 'l') {
							$state_f_l = t('filament_error');
						}
						else { // $abb_filament == 'r'
							$state_f_r = t('filament_error');
						}
						break;
				}
				// assign $cr only when status is ok (acts like a flag of error)
				if ($cr == ERROR_OK && $volume_need > 0) {
					$cr = $tmp_ret;
				}
				
				// check material difference for all used cartridges
				if (!in_array($tmp_ret, array(
						ERROR_INTERNAL, ERROR_MISS_LEFT_CART, ERROR_MISS_RIGT_CART,
				)) && $volume_need > 0) {
					if ($material == NULL) {
						$material = $data_cartridge[PRINTERSTATE_TITLE_MATERIAL];
					}
					else if ($material != $data_cartridge[PRINTERSTATE_TITLE_MATERIAL]) {
						$error .= t('cartridge_material_diff_msg') . '<br>';
					}
					
// 					if ($volume_need > 0) { // act as count($data_slice), but with more verification
// 						if ($data_slice[PRINTERSTATE_TITLE_EXT_TEMPER] != $data_cartridge[PRINTERSTATE_TITLE_EXT_TEMPER]) {
// 							$error .= t('temper_diff_msg',
// 									array(
// 											$data_slice[PRINTERSTATE_TITLE_EXT_TEMPER],
// 											$data_cartridge[PRINTERSTATE_TITLE_EXT_TEMPER],
// 									)
// 							) . '<br>';
// 						}
// 						if ($data_slice[PRINTERSTATE_TITLE_EXT_TEMP_1] != $data_cartridge[PRINTERSTATE_TITLE_EXT_TEMP_1]) {
// 							$error .= t('first_temper_diff_msg',
// 									array(
// 											$data_slice[PRINTERSTATE_TITLE_EXT_TEMP_1],
// 											$data_cartridge[PRINTERSTATE_TITLE_EXT_TEMP_1],
// 									)
// 							) . '<br>';
// 						}
// 					}
				}
			}
			if (!is_null($error)) {
				$error .= t('suggest_reslice');
			}
		}
		
		$template_data = array(
				'cartridge_c_l'		=> $array_data['l'][PRINTERSTATE_TITLE_COLOR],
				'cartridge_c_r'		=> $array_data['r'][PRINTERSTATE_TITLE_COLOR],
				'state_f_l'			=> $state_f_l,
				'state_f_r'			=> $state_f_r,
				'need_filament_l'	=> $array_data['l'][PRINTERSTATE_TITLE_NEED_L],
				'need_filament_r'	=> $array_data['r'][PRINTERSTATE_TITLE_NEED_L],
				'temper_l'			=> $array_data['l'][PRINTERSTATE_TITLE_EXT_TEMPER],
				'temper_r'			=> $array_data['r'][PRINTERSTATE_TITLE_EXT_TEMPER],
// 				'real_temper_l'		=> isset($array_data['l']) ? $array_data['l'][$array_key_real_temper] : 200,
// 				'real_temper_r'		=> isset($array_data['r']) ? $array_data['r'][$array_key_real_temper] : 200,
				'print_button'		=> t('print_button'),
				'left_temperature'	=> t('left_temperature'),
				'right_temperature'	=> t('right_temperature'),
				'change_left'		=> $change_left,
				'change_right'		=> $change_right,
				'enable_print'		=> ($cr == ERROR_OK) ? 'true' : 'false',
				'error_msg'			=> $error,
				'enable_reslice'	=> $error ? 'true' : 'false',
				'reslice_button'	=> t('reslice_button'),
				'exchange_extruder'	=> t('exchange_extruder'),
				'exchange_o1_val'	=> 0,
				'exchange_o2_val'	=> 1,
				'exchange_o2_sel'	=> NULL,
				'exchange_o1'		=> t('exchange_straight'),
				'exchange_o2'		=> t('exchange_crossover'),
				'bicolor_model'		=> 'false',
				'needprint_right'	=> $array_need['r'],
				'needprint_left'	=> $array_need['l'],
				'enable_exchange'	=> $select_disable,
				'filament_not_need'	=> t('filament_not_need'),
				'filament_ok'		=> t('filament_ok'),
				'temper_max'		=> PRINTERSTATE_TEMPER_CHANGE_MAX,
				'temper_min'		=> PRINTERSTATE_TEMPER_CHANGE_MIN,
				'temper_delta'		=> PRINTERSTATE_TEMPER_CHANGE_VAL,
		);
		
		if (ERROR_OK == PrinterState_checkFilaments(array(
				'l'	=> $array_data['r'][PRINTERSTATE_TITLE_NEED_L],
				'r'	=> $array_data['l'][PRINTERSTATE_TITLE_NEED_L],
		))) {
			$template_data['enable_exchange'] = NULL; // enable exchange if verification is passed
		}
// 		if (ERROR_OK == PrinterState_checkFilament('l', $array_data['r'][PRINTERSTATE_TITLE_NEED_L])
// 		&& ERROR_OK == PrinterState_checkFilament('r', $array_data['l'][PRINTERSTATE_TITLE_NEED_L])) {
// 			$template_data['enable_exchange'] = NULL; // enable exchange if verification is passed
// 		}
		
		if ($array_need['r'] == 'true' && $array_need['l'] == 'true') {
			$template_data['bicolor_model'] = 'true';
			$this->parser->parse('template/sliceupload/slice_result_ajax_2color', $template_data);
		}
		else {
			$template_data['exchange_o1']		= t('exchange_left');
			$template_data['exchange_o2']		= t('exchange_right');
			
			if ($array_need['r'] == 'true') {
				$template_data['exchange_o1_val']	= 1;
				$template_data['exchange_o2_val']	= 0;
				$template_data['exchange_o2_sel']	= $option_selected;
			}
			$this->parser->parse('template/sliceupload/slice_result_ajax_1color', $template_data);
		}
		
		$this->output->set_status_header(202);
		
		return;
	}
	
	function preview_ajax() {
		$cr = 0;
		$path_image = NULL;
		$display = NULL;
		$rho = $this->input->get('rho');
		$theta = $this->input->get('theta');
		$delta = $this->input->get('delta');
		$color1 = $this->input->get('color_right');
		$color2 = $this->input->get('color_left');
		
		// check color input
		if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color1)) {
			$color1 = NULL;
		}
		if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color2)) {
			$color2 = NULL;
		}
		
		if ($rho === FALSE || $theta === FALSE || $delta === FALSE) {
			$cr = ERROR_MISS_PRM;
		}
		else if ((int)$rho < 0) {
			$cr = ERROR_WRONG_PRM;
		}
		else {
			$file_info = array();
			$file_cartridge = NULL;
			
			$this->load->helper('slicer');
			$cr = Slicer_rendering((int)$rho, (int)$theta, (int)$delta, $path_image, $color1, $color2);
			
			if ($cr == ERROR_OK) {
				//TODO add the possibility of making path everywhere, but not only in /var/www/tmp/
				$this->load->helper('file');
				$file_info = get_file_info(realpath($path_image), array('name'));
				$display = '/tmp/' . $file_info['name'] . '?' . time();
			}
		}
		
		if ($cr != ERROR_OK) {
			$display = $cr . " " . t(MyERRMSG($cr));
		}
		else if (!file_exists(realpath($path_image))) {
			// in the case: $cr == ERROR_OK 
			$cr = 202;
			$display = 'preview image unavailable';
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
	
	function restart_ajax() {
		$ret_val = 0;
		$display = NULL;
		$action = $this->input->get('action');
		
		$this->load->helper('slicer');
		if (Slicer_checkOnline(FALSE)) {
			$this->output->set_status_header(202, 'Opened');
			
			return;
		}
		else if ($action) {
			$this->load->helper('printerlog');
			PrinterLog_logDebug('restarting slicer', __FILE__, __LINE__);
			
			Slicer_restart();
		}
		
		$display = 200 . " " . t(MyERRMSG(200));
		$this->output->set_status_header(200, $display);
		
		return;
	}
	
	function preset_prm_ajax() {
		$ret_val = 0;
		$display = NULL;
		$array_setting = array();
		$id = $this->input->get('id');
		
		$this->load->helper('zimapi');
		$this->load->library('parser');
		$ret_val = ZimAPI_getPresetSettingAsArray($id, $array_setting);
		
		if ($ret_val == ERROR_OK) {
			if (array_key_exists(ZIMAPI_TITLE_PRESET_INFILL, $array_setting)
					&& array_key_exists(ZIMAPI_TITLE_PRESET_SKIRT, $array_setting)
					&& array_key_exists(ZIMAPI_TITLE_PRESET_RAFT, $array_setting)
					&& array_key_exists(ZIMAPI_TITLE_PRESET_SUPPORT, $array_setting)) {
				$array_display = array();
				
				$array_display[ZIMAPI_TITLE_PRESET_INFILL]	= $array_setting[ZIMAPI_TITLE_PRESET_INFILL];
				$array_display[ZIMAPI_TITLE_PRESET_SKIRT]	= $array_setting[ZIMAPI_TITLE_PRESET_SKIRT];
				$array_display[ZIMAPI_TITLE_PRESET_RAFT]	= $array_setting[ZIMAPI_TITLE_PRESET_RAFT];
				$array_display[ZIMAPI_TITLE_PRESET_SUPPORT]	= $array_setting[ZIMAPI_TITLE_PRESET_SUPPORT];
				$display = json_encode($array_display);
				
				$this->output->set_status_header($ret_val, $display);
				$this->output->set_content_type('application/json; charset=UTF-8');
				$this->parser->parse('template/plaintxt', array('display' => $display));
				
				return;
			}
			else {
				$ret_val = ERROR_INTERNAL;
			}
		}
		
		$display = $ret_val . " " . t(MyERRMSG($ret_val));
		$this->output->set_status_header($ret_val, $display);
		$this->output->set_content_type('text/plain; charset=UTF-8');
		$this->parser->parse('template/plaintxt', array('display' => $display));
		
		return;
	}
}
