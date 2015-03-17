<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class Test_version extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->helper( array(
				'printerstate',
				'url',
				'json'
		) );
	}
	
	public function ssh() {
		$output = array();
		$ret_val = 0;
		if (!file_exists('/tmp/remoteSSH')) {
			exec('/etc/init.d/remote_ssh start');
		}
		exec('/etc/init.d/remote_ssh status', $output, $ret_val);
		
		var_dump(array(
				'ret_code'	=> $ret_val,
				'output'	=> $output,
		));
		
		return;
	}
	
	public function test_port() {
		$cr = 500;
		$port = (int) $this->input->get('v');
		
		if ($port <= 0) {
			$cr = 403;
		}
		else if (FALSE === @file_get_contents("http://portquiz.net:" . $port)) {
			$cr = 404;
		}
		else {
			$cr = 200;
		}
		
		$this->load->library('parser');
		$this->output->set_status_header($cr);
		$this->parser->parse('plaintxt', array('display' => 'test'));
		
		return;
	}
	
	public function index() {
		$template_data = array();
		$body_page = NULL;
		$temp_info = array();
		$array_info = array();
		$sso_name = NULL;
		$upgrade_mode = NULL;
		$profile_link = NULL;
		
		$this->load->helper(array('printerstate', 'zimapi'));
		$this->load->library('parser');
		$this->lang->load('printerstate/printerinfo', $this->config->item('language'));
		$this->lang->load('test_version', $this->config->item('language'));
		ZimAPI_getPrinterSSOName($sso_name);
		ZimAPI_getUpgradeMode($upgrade_mode, $profile_link);
		
		$temp_info = PrinterState_getInfoAsArray();
		// config variable is set in MY_controller, so we need to correct number of extruder by ourselves
		$temp_info[PRINTERSTATE_TITLE_NB_EXTRUD] = PrinterState_getNbExtruder();
		$array_info = array(
				array(
						'title'	=> t('profile_title'),
						'value'	=> $upgrade_mode . ' [ ' . $profile_link . ' ]',
				),
				array(
						'title'	=> t('version_title'),
						'value'	=> $temp_info[PRINTERSTATE_TITLE_VERSION],
				),
				array(
						'title'	=> t('next_version_title'),
						'value'	=> $temp_info[PRINTERSTATE_TITLE_VERSION_N],
				),
				array(
						'title'	=> t('type_title'),
						'value'	=> $temp_info[PRINTERSTATE_TITLE_TYPE],
				),
				array(
						'title'	=> t('serial_title'),
						'value'	=> $temp_info[PRINTERSTATE_TITLE_SERIAL],
				),
				array(
						'title'	=> t('extruder_title'),
						'value'	=> $temp_info[PRINTERSTATE_TITLE_NB_EXTRUD],
				),
				array(
						'title'	=> t('marlin_title'),
						'value'	=> $temp_info[PRINTERSTATE_TITLE_VER_MARLIN],
				),
				array(
						'title' => t('ip_address'),
						'value'	=> $temp_info[ZIMAPI_TITLE_IP],
				)
		);
		
		// parse the main body
		$template_data = array(
				'array_info'		=> $array_info,
				'port_test_title'	=> t('port_test_title'),
				'port_test_ok'		=> t('port_test_ok'),
				'port_test_ko'		=> t('port_test_ko'),
				'port_test_r80'		=> t('port_test_printer', array(80)),
				'port_test_r443'	=> t('port_test_printer', array(443)),
				'port_test_r4443'	=> t('port_test_printer', array(4443)),
				'port_test_l80'		=> t('port_test_client', array(80)),
				'port_test_l443'	=> t('port_test_client', array(443)),
		);
		
		$body_page = $this->parser->parse('test_version', $template_data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . t('printerstate_printerinfo_pagetitle') . '</title>',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('basetemplate', $template_data);
		
		return;
	}
}