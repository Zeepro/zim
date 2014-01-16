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
	
	public function index() {
// 		$this->listmodel();
		$this->output->set_header('Location: /printmodel/listmodel');
		return;
	}
	
	public function listmodel() {
// 		$json_text = $this->curl->simple_get(base_url('rest/listmodel'));
// 		curl_init('http://example.com');
		global $CFG;
		$display_printlist = array();
		$template_data = array();
		$body_page = NULL;
		
		$this->load->helper('printlist');
		$this->load->library('parser');
		$this->lang->load('printlist', $this->config->item('language'));
		
		$json_data = ModelList__listAsArray();
		
		// prepare display data
		foreach ($json_data as $model_data) {
			$nb_image = count($model_data[PRINTLIST_TITLE_PIC]);
			
			$display_printlist[] = array(
					'id'	=> $model_data[PRINTLIST_TITLE_ID],
					'name'	=> $model_data[PRINTLIST_TITLE_NAME],
					'image'	=> $model_data[PRINTLIST_TITLE_PIC][0],
			);
		}
		
		// parse the main body
		$template_data = array(
				'title'				=> t('Quick Print'),
				'baseurl_detail'	=> '/printmodel/detail',
				'model_lists'		=> $display_printlist,
				'back'				=> t('back'),
		);
		
		$body_page = $this->parser->parse('template/printlist/listmodel', $template_data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $CFG->config ['language_abbr'],
				'headers'		=> '<title>' . t('ZeePro Personal Printer 21 - Quick print list') . '</title>',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
		
		return;
	}
	
	public function detail() {
		global $CFG;
		$model_data = array();
		$cartridge_data = array();
		$template_data = array();
		$cr = 0;
		$check_left_filament = '';
		$check_right_filament = '';
		$color_left_filament = '';
		$color_right_filament = '';
		$time_estimation = '';
		$body_page = NULL;
		
		$this->load->helper(array('printlist', 'printerstate', 'timedisplay'));
		$this->load->library('parser');
		$this->lang->load('printlist', $this->config->item('language'));
		$this->lang->load('timedisplay', $this->config->item('language'));
		
		$mid = $this->input->get('id');
		
		// check model id, resend user to if not valid
		if ($mid) {
			$cr = ModelList__getDetailAsArray($mid, $model_data);
			//TODO fix dé folder
			if (($cr != ERROR_OK) || is_null($model_data)) {
				$this->output->set_header('Location: /printmodel/listmodel');
				return;
			}
		}
		else {
			$this->output->set_header('Location: /printmodel/listmodel');
			return;
		}
		
		// check quantity of filament
		$cr = PrinterState__checkFilament($model_data[PRINTLIST_TITLE_LENG_F1], $model_data[PRINTLIST_TITLE_LENG_F2]);
		$check_left_filament = t('ok');
		$check_right_filament = t('ok');
		switch ($cr) {
			case ERROR_LOW_RIGT_FILA:
				$check_left_filament = t('not enough');
				break;
				
			case ERROR_LOW_RIGT_FILA:
				$check_right_filament = t('not enough');
				break;
				
			default:
				//TODO treat error here
				break;
		}
		
		// get color of cartridge
		foreach(array('l', 'r') as $abb_cartridge) {
			$ret_val = PrinterState__getCartridgeAsArray($cartridge_data, $abb_cartridge);
			if ($ret_val == ERROR_OK) {
				if($abb_cartridge == 'r') {
					$color_left_filament = $cartridge_data[PRINTERSTATE_TITLE_COLOR];
				}
				else {
					$color_right_filament = $cartridge_data[PRINTERSTATE_TITLE_COLOR];
				}
			}
			else {
				return; //TODO treat error here
			}
		}
		
		// get a more legible time of estimation
		$time_estimation = TimeDisplay__convertsecond(
				$model_data[PRINTLIST_TITLE_TIME], t('Time estimation: '), t('Unknown'));
		
		// show detail page if valid, parse the body of page
		$template_data = array(
				'title'				=> $model_data[PRINTLIST_TITLE_NAME],
				'image'				=> $model_data[PRINTLIST_TITLE_PIC][0],
				'model_c1'			=> $model_data[PRINTLIST_TITLE_COLOR_F1],
				'model_c2'			=> $model_data[PRINTLIST_TITLE_COLOR_F2],
// 				'time'				=> 'Time estimation: ' . $model_data[PRINTLIST_TITLE_TIME],
				'time'				=> $time_estimation,
				'state_c1'			=> $color_left_filament,
				'state_c2'			=> $color_right_filament,
				'state_f1'			=> $check_left_filament,
				'state_f2'			=> $check_right_filament,
				'model_id'			=> $mid,
				'title_current' 	=> t('Current material'),
				'change_filament'	=> t('Change'),
				'print_model'		=> t('Print'),
				'back'				=> t('back'),
				'preview_title'		=> t('Preview'),
		);
		
		$body_page = $this->parser->parse('template/printlist/detail', $template_data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $CFG->config ['language_abbr'],
				'headers'		=> '<title>' . t('ZeePro Personal Printer 21 - Quick print detail') . '</title>',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
		
		return;
	}
}