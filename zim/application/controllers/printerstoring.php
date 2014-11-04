<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class Printerstoring extends MY_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->helper( array(
				'printerstoring',
				'url',
				'json'
		) );
	}

	public function storestl() {
		$template_data = array();
		$body_page = NULL;
		$error = NULL;
		$response = 0;
		$button_goto_slice = NULL;
		$f1 = NULL;
		$f2 = NULL;
		$name = NULL;
		
		$this->load->library('parser');
		$this->lang->load('printerstoring/storestl', $this->config->item('language'));
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
			
			if ($this->upload->do_upload('file') && ($name = $this->input->post('name')))
			{
				$f1 = $this->upload->data();
			}
			else if ($this->upload->do_upload('file_c1') && ($name = $this->input->post('name'))) {
				$first_combine = TRUE;
				$f1 = $this->upload->data();
				
				foreach (array('file_c2') as $file_key) {
					if ($this->upload->do_upload($file_key)) {
						$first_combine = FALSE;
						$f2 = $this->upload->data();
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

			if ($f1 && ($name = $this->input->post('name'))) {
				$this->load->helper('printerstoring');
				$cr = PrinterStoring_storeStl($name, $f1, $f2);
				if ($cr === ERROR_OK) {
					header('Location: ' . '/printerstoring/liststl?uploaded=uploaded');
				}
				if ($cr === ERROR_DISK_FULL) {
					$error = t('fail_disk_full');
				}
				else {
					$error = t('fail_message');
				}
			}
		}
		// parse the main body
		$template_data = array(
				'back'			=> t('back'),
				'home'			=> t('home'),
				'select_hint'	=> t('select_hint'),
				'select_hint_multi'	=> t('select_hint_multi'),
				'header_single' => t('header_single'),
				'header_multi'	=> t('header_multi'),
				'upload_button'	=> t('upload_button'),
				'error'			=> $error,
				'name'		=> t('name')
		);
		$body_page = $this->parser->parse('template/printerstoring/storestl', $template_data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . t('printerstoring_storestl_pagetitle') . '</title>',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
		
		return;
	}

	public function libraries() {
		$template_data = array();

		$this->load->library('parser');

		$this->lang->load('printerstoring/libraries', $this->config->item('language'));

		$template_data = array(
				'back'			=> t('back'),
				'home'			=> t('home'),
				'libraries_info'		=> t('libraries_info'),
				'stl_models'		=> t('stl_models'),
				'gcode_models'		=> t('gcode_models'),
		);
		$body_page = $this->parser->parse('template/printerstoring/libraries', $template_data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . t('printerstoring_libraries_pagetitle') . '</title>',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
	}

	public function stllibrary() {
		$template_data = array();

		$this->load->library('parser');

		$this->lang->load('printerstoring/stllibrary', $this->config->item('language'));

		$template_data = array(
				'back'			=> t('back'),
				'home'			=> t('home'),
				'browse_models'		=> t('browse_models'),
				'add_model'		=> t('add_model')
		);
		$body_page = $this->parser->parse('template/printerstoring/stllibrary', $template_data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . t('printerstoring_stllibrary_pagetitle') . '</title>',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
	}

	private function array_sort($array, $on, $order=SORT_ASC)
	{
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}

	public function liststl() {
		$template_data = array();

		$this->load->library('parser');

		$this->lang->load('printerstoring/liststl', $this->config->item('language'));
		$this->load->helper('printerstoring');

		$uploaded = null;
		if ($_SERVER['REQUEST_METHOD'] == 'GET')
		{
			$uploaded = $this->input->get('uploaded');
		}

		$json_data = json_decode(PrinterStoring_listStl(), true);
		
		// prepare display data
		foreach ($json_data as $model_data) {
			
			$display_printlist[] = array(
					'name'	=> $model_data['name'],
					'id'	=> $model_data['id'],
//					'image'	=> $model_data['imglink'],
					'image'	=> "/printerstoring/getpicture?id=" . $model_data['id'] . "&type=stl",
					'creation_date' => $model_data['creation_date']
			);
		}
		if (isset($display_printlist)) {
			$display_printlist = $this->array_sort($display_printlist, 'name');
		}

		$template_data = array(
				'back'			=> t('back'),
				'home'			=> t('home'),
				'list_info'		=> t('list_info'),
				'print-model'		=> t('print-model'),
				'delete-model'		=> t('delete-model'),
				'list'		=> (isset($display_printlist) ? $display_printlist : array()),
				'uploaded'		=> ($uploaded ? t('uploaded') : NULL),
				'print_error'		=> t('print_error'),
				'delete_error'	=> t('delete_error')
		);
		$body_page = $this->parser->parse('template/printerstoring/liststl', $template_data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . t('printerstoring_liststl_pagetitle') . '</title>',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
	}

	public function getpicture() {
		$id = NULL;
		$type = NULL;
		//$url_pid = '';
		$cr = 0; //return code
		
		$this->load->helper(array('printlist', 'file'));
		$this->load->helper('printerstoring');
		$id = intval($this->input->get('id')); //return false if missing
		$type = $this->input->get('type'); //return false if missing
		
		if ($id && $type) {
			global $CFG;
//			$CI = &get_instance();
			if ($type === "stl") {
				$image_file = $CFG->config['stl_library'] . sprintf('%06d', $id) . '/' . PRINTERSTORING_FILE_IMG_PNG;				
			}
			else if ($type === "gcode") {
				$image_file = $CFG->config['gcode_library'] . sprintf('%06d', $id) . '/' . PRINTERSTORING_FILE_IMG_JPG;
			}
			else {
				$this->_return_cr(ERROR_WRONG_PRM);
				return;
			}
			// get img link from info file
			try {
				if (file_exists($image_file) === false) {
					$this->load->helper('printerlog');
					PrinterLog_logError('model id not found', __FILE__, __LINE__);
					$this->_return_cr(ERROR_UNKNOWN_MODEL);
					return ;
				}
				$this->output->set_content_type(get_mime_by_extension($image_file))->set_output(@file_get_contents($image_file));
				return ;
			}
			catch (Exception $e) {
				$CI->load->helper('printerlog');
				PrinterLog_logError('could not get the picture', __FILE__, __LINE__);
				$this->_return_cr(ERROR_UNKNOWN_MODEL);
				return ;
			}
		} else {
			$cr = ERROR_MISS_PRM;
		}
		
		$this->_return_cr($cr);
		
		return;
	}

	public function listgcode() {
		$template_data = array();

		$this->load->library('parser');

		$this->lang->load('printerstoring/listgcode', $this->config->item('language'));
		$this->load->helper('printerstoring');

		$json_data = json_decode(PrinterStoring_listGcode(), true);
		// prepare display data
		foreach ($json_data as $model_data) {
			
			$display_printlist[] = array(
					'modelname'	=> $model_data['name'],
					'mid'	=> $model_data['id'],
//					'image'	=> $model_data['imglink'],
					'image'	=> "/printerstoring/getpicture?id=" . $model_data['id'] . "&type=gcode",
					'creation_date' => strtotime($model_data['creation_date']), //date('d-M-Y', strtotime($model_data['creation_date']))
					'creation_datestr' => $model_data['creation_date'] //date('d-M-Y', strtotime($model_data['creation_date']))
			);
		}

		if (isset($display_printlist)) {
			$display_printlist = $this->array_sort($display_printlist, 'modelname');
		}

		$template_data = array(
				'back'			=> t('back'),
				'home'			=> t('home'),
				'list_info'		=> t('list_info'),
				'print-model'		=> t('print-model'),
				'delete-model'		=> t('delete-model'),
				//'list'		=> $display_printlist,
				'encoded_list'		=> json_encode((isset($display_printlist) ? $display_printlist : array())),
				'select_alphabetical'		=> t('select_alphabetical'),
				'select_mostrecent'		=> t('select_mostrecent'),
				'print_error'		=> t('print_error'),
				'delete_error'	=> t('delete_error')
		);
		$body_page = $this->parser->parse('template/printerstoring/listgcode', $template_data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . t('printerstoring_listgcode_pagetitle') . '</title>',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
	}

}