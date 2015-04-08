<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class User extends MY_Controller {
	public function __construct() {
		parent::__construct();
		
		$this->load->helper(array('userauth', 'errorcode'));
		
		// check local first
		if (UserAuth_checkAccount()) {
			// get access from sso, and check it again
			if (UserAuth_getUserAccess($_SESSION[USERAUTH_TITLE_TOKEN])
					&& UserAuth_checkAccount()) {
				return;
			}
		}
		
		// remove session data, and redirect user to login page
		//TODO think if it's better to logout directly to force login again to get a new token
		UserAuth_removeSessionData();
		header('Location: ' . USERAUTH_URL_REDIRECTION);
		exit;
	}
	
	private function _user_usortCompare($a, $b) {
		return strcasecmp($a['user_name'], $b['user_name']);
	}
	
	public function index() {
		$template_data = NULL; //array()
		
		$this->load->library('parser');
		$this->lang->load('user/index', $this->config->item('language'));
		
		$template_data = array(
				'button_add_user'	=> t('button_add_user'),
				'button_list_user'	=> t('button_list_user'),
		);
		
		$this->_parseBaseTemplate(t('pagetitle_user_index'),
				$this->parser->parse('user/index', $template_data, TRUE));
		
		return;
	}
	
	public function add() {
		$template_data = NULL; //array()
		$error = NULL;
		
		$this->lang->load('user/add', $this->config->item('language'));
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('user_name', 'lang:title_name', 'required|alpha_dash');
			$this->form_validation->set_rules('user_email', 'lang:title_email', 'required|valid_email');
			$this->form_validation->set_rules('access_view', 'lang:title_p_view', 'required|is_natural|less_than[2]');
			$this->form_validation->set_rules('access_manage', 'lang:title_p_manage', 'required|is_natural|less_than[2]');
			$this->form_validation->set_rules('access_account', 'lang:title_p_account', 'required|is_natural|less_than[2]');
			
			if ($this->form_validation->run() == FALSE) {
				$error = validation_errors();
			}
			else {
				$ret_val = UserAuth_grantUser(
						$this->input->post('user_email'),
						$this->input->post('user_name'),
						array(
								USERAUTH_PRM_P_VIEW		=> $this->input->post('access_view')	? TRUE : FALSE,
								USERAUTH_PRM_P_MANAGE	=> $this->input->post('access_manage')	? TRUE : FALSE,
								USERAUTH_PRM_P_ACCOUNT	=> $this->input->post('access_account')	? TRUE : FALSE,
				));
				
				switch ($ret_val) {
					case ERROR_OK:
						$url_redirect = '/user';
						
						if (FALSE !== $this->input->post('edit_user')) {
							$url_redirect .= '/userlist';
						}
						$this->output->set_header('Location: ' . $url_redirect);
						
						return;
						break; // never reach here
						
					case ERROR_MISS_PRM:
					case ERROR_WRONG_PRM:
						$error = t('error_parameter');
						break;
						
					case ERROR_AUTHOR_FAIL:
						$error = t('error_authorize');
						break;
						
					default:
						$error = t('error_unknown');
						break;
				}
			}
		}
		
		$this->load->library('parser');
		
		$template_data = array(
				'title_add_form'	=> t('title_add_form'),
				'title_name'		=> t('title_name'),
				'title_email'		=> t('title_email'),
				'title_access'		=> t('title_access'),
				'title_p_view'		=> t('title_p_view'),
				'title_p_manage'	=> t('title_p_manage'),
				'title_p_account'	=> t('title_p_account'),
				'button_confirm'	=> t('button_confirm'),
				'function_on'		=> t('function_on'),
				'function_off'		=> t('function_off'),
				'error'				=> $error,
		);
		
		$this->_parseBaseTemplate(t('pagetitle_user_add'),
				$this->parser->parse('user/add', $template_data, TRUE));
		
		return;
	}
	
	public function userlist() {
		$template_data = NULL; //array()
		$userlist_display = array();
		$userlist_api = array();
		$ret_val = UserAuth_getUserListArray($userlist_api);
		$option_selected = 'selected';
		
		foreach($userlist_api as $user_element) {
			$userlist_display[] = array(
					'user_name'			=> $user_element[USERAUTH_TITLE_NAME],
					'user_email'		=> $user_element[USERAUTH_TITLE_EMAIL],
					'user_p_view'		=> $user_element[USERAUTH_PRM_P_VIEW] ? $option_selected : NULL,
					'user_p_manage'		=> $user_element[USERAUTH_PRM_P_MANAGE] ? $option_selected : NULL,
					'user_p_account'	=> $user_element[USERAUTH_PRM_P_ACCOUNT] ? $option_selected : NULL,
					'random_id'			=> rand(),
			);
		}
		// sort list by name of user, by order of SSO if not do so
		usort($userlist_display, 'User::_user_usortCompare');
		
		$this->load->library('parser');
		$this->lang->load('user/add', $this->config->item('language'));
		$this->lang->load('user/list', $this->config->item('language'));
		
		$template_data = array(
				'title_name'		=> t('title_name'),
				'title_email'		=> t('title_email'),
				'title_access'		=> t('title_access'),
				'title_p_view'		=> t('title_p_view'),
				'title_p_manage'	=> t('title_p_manage'),
				'title_p_account'	=> t('title_p_account'),
				'button_confirm'	=> t('button_confirm'),
				'function_on'		=> t('function_on'),
				'function_off'		=> t('function_off'),
				'button_delete'		=> t('button_delete'),
				'message_delete'	=> t('message_delete'),
				'button_delete_ok'	=> t('button_delete_ok'),
				'button_delete_no'	=> t('button_delete_no'),
				'error_get_list'	=> ($ret_val == ERROR_OK) ? NULL : t('error_get_list'),
				'userlist'			=> $userlist_display,
		);
		
		$this->_parseBaseTemplate(t('pagetitle_user_list'),
				$this->parser->parse('user/list', $template_data, TRUE));
		
		return;
	}
	
	public function delete_ajax() {
		$user_email = NULL;
		$ret_val = 0;
		
		$user_email = $this->input->post('user_email');
		$ret_val = UserAuth_revokeUser($user_email);
		if ($ret_val != ERROR_OK) {
			$this->load->helper('printerlog');
			PrinterLog_logDebug('revoke user return code: ' . $ret_val, __FILE__, __LINE__);
		}
		$this->output->set_status_header($ret_val, MyERRMSG($ret_val));
		
		return;
	}
}