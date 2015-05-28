<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class User extends MY_Controller {
	public function __construct() {
		parent::__construct();
		
		$this->load->helper(array('userauth', 'errorcode', 'corestatus'));
		
		// check local first
		if (UserAuth_checkView()) {
			// get access from sso, and check it again
			if (UserAuth_getUserAccess($_SESSION[USERAUTH_TITLE_TOKEN]) && UserAuth_checkView()
					&& (!CoreStatus_checkCallUserManagement() || UserAuth_checkAccount())) {
				return;
			}
		}
		
		$this->_exit_redirectHome();
	}
	
	private function _user_usortCompare($a, $b) {
		return strcasecmp($a['user_name'], $b['user_name']);
	}
	
	private function _exit_redirectHome() {
		// remove session data, and redirect user to login page
		//TODO think if it's better to logout directly to force login again to get a new token
		UserAuth_removeSessionData();
		header('Location: ' . USERAUTH_URL_REDIRECTION);
		exit;
	}
	
	public function index() {
		$template_data = NULL; //array()
		
		$this->load->library('parser');
		$this->lang->load('user/index', $this->config->item('language'));
		
		$template_data = array(
				'button_user_info'		=> t('button_user_info'),
				'button_newsletter'		=> t('button_newsletter'),
				'button_edit_password'	=> t('button_edit_password'),
				'button_delete_user'	=> t('button_delete_user'),
		);
		
		$this->_parseBaseTemplate(t('pagetitle_user_index'),
				$this->parser->parse('user/index', $template_data, TRUE));
		
		return;
	}
	
	public function manage() {
		$this->userlist();
		
		return;
// 		$template_data = NULL; //array()
		
// 		$this->load->library('parser');
// 		$this->lang->load('user/manage', $this->config->item('language'));
		
// 		$template_data = array(
// 				'button_add_user'	=> t('button_add_user'),
// 				'button_list_user'	=> t('button_list_user'),
// 				'msg_add_ok'		=> t('msg_add_ok'),
// 				'display_add_ok'	=> ($this->input->get('add_success') !== FALSE) ? 'true' : 'false',
// 		);
		
// 		$this->_parseBaseTemplate(t('pagetitle_user_manage'),
// 				$this->parser->parse('user/manage', $template_data, TRUE));
		
// 		return;
	}
	
	public function add() {
		$template_data = NULL; //array()
		$userlist_api = array();
		$error = NULL;
		
		$this->lang->load('user/add', $this->config->item('language'));
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('user_name', 'lang:title_name', 'required|alpha_dash');
			$this->form_validation->set_rules('user_email', 'lang:title_email', 'required|valid_email');
// 			$this->form_validation->set_rules('access_view', 'lang:title_p_view', 'required|is_natural|less_than[2]');
// 			$this->form_validation->set_rules('access_manage', 'lang:title_p_manage', 'required|is_natural|less_than[2]');
// 			$this->form_validation->set_rules('access_account', 'lang:title_p_account', 'required|is_natural|less_than[2]');
			$this->form_validation->set_rules('user_access', 'lang:title_access', 'required|is_natural_no_zero|less_than[4]');
			$this->form_validation->set_rules('user_message', 'lang:title_message', 'max_length[2048]');
			
			if ($this->form_validation->run() == FALSE) {
				$error = validation_errors();
			}
			else if (ERROR_OK != UserAuth_getUserListArray($userlist_api)) {
// 				$error = t('error_get_list');
				$this->_exit_redirectHome();
				
				return; // never reach here
			}
			else {
				$user_access = (int) $this->input->post('user_access');
				$user_email = $this->input->post('user_email');
				$user_name = $this->input->post('user_name');
				$user_message = $this->input->post('user_message');
				$user_edit = (FALSE !== $this->input->post('edit_user'));
				$user_exist = FALSE;
				
				if ($user_edit) {
					$user_oldname = $this->input->post('user_oldname');
					
					if ($user_name != $user_oldname) {
						foreach ($userlist_api as $user_element) {
							if ($user_name == $user_element[USERAUTH_TITLE_NAME]) {
// 								$user_exist = TRUE;
								$this->output->set_header('Location: /user/userlist?error_modify_exist');
								
								return;
							}
						}
					}
				}
				else {
					foreach ($userlist_api as $user_element) {
						if ($user_name == $user_element[USERAUTH_TITLE_NAME]
								|| $user_email == $user_element[USERAUTH_TITLE_EMAIL]) {
							$user_exist = TRUE;
							break;
						}
					}
				}
				
				if ($user_exist) {
					$error = t('error_exist');
				}
				else {
					$ret_val = UserAuth_grantUser($user_email, $user_name,
							array(
									USERAUTH_PRM_P_VIEW		=> ($user_access > 0)	? TRUE : FALSE,
									USERAUTH_PRM_P_MANAGE	=> ($user_access > 1)	? TRUE : FALSE,
									USERAUTH_PRM_P_ACCOUNT	=> ($user_access > 2)	? TRUE : FALSE,
							), $user_message);
					
					switch ($ret_val) {
						case ERROR_OK:
// 							$url_redirect = '/user/manage?add_success';
							
// 							if ($user_edit) {
// 								$url_redirect = '/user/userlist';
// 							}
// 							$this->output->set_header('Location: ' . $url_redirect);
							$this->output->set_header('Location: /user/userlist');
							
							return;
							break; // never reach here
							
						case ERROR_MISS_PRM:
						case ERROR_WRONG_PRM:
							$error = t('error_parameter');
							break;
							
						case ERROR_AUTHOR_FAIL:
// 							$error = t('error_authorize');
							$this->_exit_redirectHome();
							
							return;
							break; // never reach here
							
						default:
							$error = t('error_unknown');
							break;
					}
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
				'title_message'		=> t('title_message'),
				'hint_message'		=> t('hint_message'),
				'button_confirm'	=> t('button_confirm'),
				'hint_access'		=> t('hint_access'),
				'popup_what'		=> t('popup_what'),
				'msg_grant_manage'	=> t('msg_grant_manage'),
				'msg_add_exist'		=> t('msg_add_exist'),
				'button_ok'			=> t('button_ok'),
// 				'function_on'		=> t('function_on'),
// 				'function_off'		=> t('function_off'),
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
		$error = NULL;
		$radio_checked = 'checked';
		
		$this->lang->load('user/manage', $this->config->item('language'));
		$this->lang->load('user/add', $this->config->item('language'));
		$this->lang->load('user/list', $this->config->item('language'));
		
		if ($ret_val != ERROR_OK) {
			$this->_exit_redirectHome();
			
			return;
		}
		else if (FALSE !== $this->input->get('error_modify_exist')) {
			$error = t('msg_error_modify_exist');
		}
		
		foreach($userlist_api as $user_element) {
			$user_access = 1; // $user_element[USERAUTH_PRM_P_VIEW] as default
			
			if ($user_element[USERAUTH_PRM_P_ACCOUNT]) {
				$user_access = 3;
			}
			else if ($user_element[USERAUTH_PRM_P_MANAGE]) {
				$user_access = 2;
			}
			
			$userlist_display[] = array(
					'user_name'			=> $user_element[USERAUTH_TITLE_NAME],
					'user_email'		=> $user_element[USERAUTH_TITLE_EMAIL],
					'user_p_view'		=> ($user_access == 1) ? $radio_checked : NULL,
					'user_p_manage'		=> ($user_access == 2) ? $radio_checked : NULL,
					'user_p_account'	=> ($user_access == 3) ? $radio_checked : NULL,
					'random_id'			=> rand(),
			);
		}
		// sort list by name of user, by order of SSO if not do so
		usort($userlist_display, 'User::_user_usortCompare');
		
		$this->load->library('parser');
		
		$template_data = array(
				'button_add_user'	=> t('button_add_user'),
				'title_name'		=> t('title_name'),
				'title_email'		=> t('title_email'),
				'title_access'		=> t('title_access'),
				'title_p_view'		=> t('title_p_view'),
				'title_p_manage'	=> t('title_p_manage'),
				'title_p_account'	=> t('title_p_account'),
				'title_message'		=> t('title_message'),
				'hint_message'		=> t('hint_message'),
				'button_confirm'	=> t('button_confirm'),
				'hint_access'		=> t('hint_access'),
				'popup_what'		=> t('popup_what'),
// 				'function_on'		=> t('function_on'),
// 				'function_off'		=> t('function_off'),
				'button_delete'		=> t('button_delete'),
				'message_delete'	=> t('message_delete'),
				'button_delete_ok'	=> t('button_delete_ok'),
				'button_delete_no'	=> t('button_delete_no'),
				'msg_delete_error'	=> t('msg_delete_error'),
				'msg_grant_manage'	=> t('msg_grant_manage'),
				'msg_add_exist'		=> t('msg_add_exist'),
				'button_ok'			=> t('button_ok'),
				'error_get_list'	=> $error, //($ret_val == ERROR_OK) ? NULL : t('error_get_list'),
				'userlist'			=> $userlist_display,
		);
		
		$this->_parseBaseTemplate(t('pagetitle_user_list'),
				$this->parser->parse('user/list', $template_data, TRUE));
		
		return;
	}
	
	public function info() {
		$template_data = NULL; //array()
		$user_info = array();
		$assign_func = NULL; //function()
		$user_birth = NULL;
		$error = NULL;
		$ret_val = 0;
		
		$this->load->helper('userauth');
		$this->lang->load('user/info', $this->config->item('language'));
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$this->load->library('form_validation');
			
// 			$this->form_validation->set_rules('user_country', 'lang:title_country', 'required');
// 			$this->form_validation->set_rules('user_city', 'lang:title_city', 'required');
// 			$this->form_validation->set_rules('user_birth', 'lang:title_birth', 'required');
			$this->form_validation->set_rules('user_why', 'lang:title_why', 'max_length[200]');
			$this->form_validation->set_rules('user_what', 'lang:title_what', 'max_length[200]');
			
			if ($this->form_validation->run() == FALSE) {
				$error = validation_errors();
			}
			else {
				$array_info = array();
				
				foreach (array(
						USERAUTH_TITLE_COUNTRY	=> 'user_country',
// 						USERAUTH_TITLE_CITY		=> 'user_city',
						USERAUTH_TITLE_BIRTHDAY	=> 'user_birth',
						USERAUTH_TITLE_WHY		=> 'user_why',
						USERAUTH_TITLE_WHAT		=> 'user_what',
				) as $key => $value) {
					$array_info[$key] = ($this->input->post($value) !== FALSE) ? $this->input->post($value) : "";
				}
				$array_info[USERAUTH_TITLE_CITY] = "";
				foreach (array('user_city_input', 'user_city') as $value) {
					if ($this->input->post($value) !== FALSE && strlen($this->input->post($value)) > 0) {
						$array_info[USERAUTH_TITLE_CITY] = $this->input->post($value);
						break;
					}
				}
				
				$ret_val = UserAuth_setUserInfo($array_info);
				
				switch ($ret_val) {
					case ERROR_OK:
						$this->output->set_header('Location: /user');
						
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
		
		$ret_val = UserAuth_getUserInfo($user_info);
		if ($ret_val == ERROR_AUTHOR_FAIL) {
			$error = t('error_authorize');
		}
		
		$assign_func = function ($key_array) use (&$user_info) {
			return (isset($user_info[$key_array]) ? htmlspecialchars($user_info[$key_array]) : NULL);
		};
		$user_birth = $assign_func(USERAUTH_TITLE_BIRTHDAY);
		if ($user_birth) {
			$obj_date = DateTime::createFromFormat('n/j/Y', $user_birth);
			$user_birth = $obj_date->format('Y-m-d');
		}
		
		$this->load->library('parser');
		
		$template_data = array(
				'title_location'	=> t('title_location'),
				'title_birth'		=> t('title_birth'),
				'label_why'			=> t('label_why'),
				'label_what'		=> t('label_what'),
				'msg_head_hint'		=> t('msg_head_hint'),
				'button_confirm'	=> t('button_confirm'),
				'hint_country'		=> t('hint_country'),
				'hint_city'			=> t('hint_city'),
				'hint_not_found'	=> t('hint_not_found'),
				'hint_why'			=> t('hint_why'),
				'hint_what'			=> t('hint_what'),
				'msg_error'			=> $error,
				'value_country'		=> $assign_func(USERAUTH_TITLE_COUNTRY),
				'value_city'		=> $assign_func(USERAUTH_TITLE_CITY),
				'value_birth'		=> $user_birth,
				'value_why'			=> $assign_func(USERAUTH_TITLE_WHY),
				'value_what'		=> $assign_func(USERAUTH_TITLE_WHAT),
		);
		
		$this->_parseBaseTemplate(t('pagetitle_user_info'),
				$this->parser->parse('user/info', $template_data, TRUE));
		
		return;
	}
	
	public function delete_ajax() {
		$ret_val = 0;
		$user_email = $this->input->post('user_email');
		
		if ($user_email) {
			$ret_val = UserAuth_revokeUser($user_email);
			if ($ret_val != ERROR_OK) {
				$this->load->helper('printerlog');
				PrinterLog_logDebug('revoke user return code: ' . $ret_val, __FILE__, __LINE__);
			}
		}
		else {
			$ret_val = ERROR_MISS_PRM;
		}
		$this->output->set_status_header($ret_val, MyERRMSG($ret_val));
		
		return;
	}
	
	public function check_exist_ajax() {
		$ret_val = 0;
		$userlist_api = array();
		$user_action = $this->input->post('action');
		$user_email = $this->input->post('email');
		$user_name = $this->input->post('name');
		
		if ($user_action && $user_email && $user_name) {
			$ret_val = UserAuth_getUserListArray($userlist_api);
			if ($ret_val == ERROR_OK) {
				switch ($user_action) {
					case 'add':
						$ret_val = ERROR_OK;
						foreach ($userlist_api as $user_element) {
							if ($user_name == $user_element[USERAUTH_TITLE_NAME]
							|| $user_email == $user_element[USERAUTH_TITLE_EMAIL]) {
								$ret_val = 202;
								break;
							}
						}
						break;
						
					case 'edit':
						$user_oldname = $this->input->post('oldname');
						
						$ret_val = ERROR_OK;
						if ($user_oldname && $user_name != $user_oldname) {
							foreach ($userlist_api as $user_element) {
								if ($user_name == $user_element[USERAUTH_TITLE_NAME]) {
									$ret_val = 202;
									break;
								}
							}
						}
						break;
						
					default:
						$ret_val = ERROR_WRONG_PRM;
						break;
				}
			}
		}
		else {
			$ret_val = ERROR_MISS_PRM;
		}
		
		$this->output->set_status_header($ret_val, MyERRMSG($ret_val));
		
		return;
	}
}