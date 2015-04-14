<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

$CI = &get_instance();
$CI->load->helper('errorcode');

if (!defined('USERAUTH_VALUE_TIMEOUT')) {
	define('USERAUTH_VALUE_TIMEOUT', 5);
	
	define('USERAUTH_FLAG_CHECK_ACCESS',	0);		// 00000
	define('USERAUTH_FLAG_VIEW',			1);		// 00001
// 	define('USERAUTH_FLAG_VIEW_ALL',		2);		// 00010
// 	define('USERAUTH_FLAG_MANAGE',			4);		// 00100
// 	define('USERAUTH_FLAG_MANAGE_ALL',		8);		// 01000
// 	define('USERAUTH_FLAG_ACCOUNT',			16);	// 10000
	define('USERAUTH_FLAG_VIEW_ALL',		3);		// 00011
	define('USERAUTH_FLAG_MANAGE',			5);		// 00101
	define('USERAUTH_FLAG_MANAGE_ALL',		15);	// 01111
	define('USERAUTH_FLAG_ACCOUNT',			31);	// 11111
	define('USERAUTH_FLAG_ALL_ACCESS',		31);	// 11111
	
	define('USERAUTH_TITLE_TOKEN',	'user_token');
	define('USERAUTH_TITLE_ACCESS',	'user_access');
	define('USERAUTH_TITLE_SESSID',	'user_sessid');
	
	define('USERAUTH_PRM_TOKEN',		'token');
	define('USERAUTH_PRM_SERIAL',		'printersn');
	// user
	define('USERAUTH_PRM_EMAIL',		'user_email');
	define('USERAUTH_PRM_NAME',			'user_name');
	// user info
	define('USERAUTH_PRM_I_COUNTRY',	'country');
	// etc..
	// permission
	define('USERAUTH_PRM_P_ACCOUNT',	'account');
	define('USERAUTH_PRM_P_MANAGE',		'manage');
	define('USERAUTH_PRM_P_VIEW',		'view');
	
	define('USERAUTH_TITLE_EMAIL',		'email');
	define('USERAUTH_TITLE_NAME',		'name');
	define('USERAUTH_TITLE_COUNTRY',	'country');
	define('USERAUTH_TITLE_CITY',		'city');
	define('USERAUTH_TITLE_BIRTHDAY',	'birth_date');
	define('USERAUTH_TITLE_WHY',		'why');
	define('USERAUTH_TITLE_WHAT',		'what');
	
	define('USERAUTH_VALUE_P_ON',	'yes');
	define('USERAUTH_VALUE_P_OFF',	'no');
	
// 	define('USERAUTH_URL_REDIRECTION',	'https://zeeproshare.com');
	define('USERAUTH_URL_REDIRECTION',	'http://home.dev');
	define('USERAUTH_URI_USERLEVEL',	'useraccess.ashx');
	define('USERAUTH_URI_GRANTUSER',	'grantuser.ashx');
	define('USERAUTH_URI_LISTUSER',		'listuser.ashx');
	define('USERAUTH_URI_REVOKEUSER',	'revokeuser.ashx');
	define('USERAUTH_URI_GET_USERINFO',	'getuserinfo.ashx');
	define('USERAUTH_URI_SET_USERINFO',	'setuserinfo.ashx');
	
	define('USERAUTH_RESPONSE_OK',			200);
	define('USERAUTH_RESPONSE_MISS_PRM',	432);
	define('USERAUTH_RESPONSE_WRONG_PRM',	433);
	define('USERAUTH_RESPONSE_UF_PRINTER',	436);
	define('USERAUTH_RESPONSE_UF_USER',		442);
}

function UserAuth__getHTTPCode($http_response_header) {
	$matches = array();
	preg_match('#HTTP/\d+\.\d+ (\d+)#', $http_response_header[0], $matches);
	return (int)$matches[1];
}

function UserAuth__requestSSO($uri, $post_data, &$response = NULL) {
	$options = array(
			'http' => array(
					'header'		=> "Content-type: application/x-www-form-urlencoded\r\n",
					'method'		=> 'POST',
					'content'		=> http_build_query($post_data),
					'ignore_errors'	=> TRUE,
					'timeout'		=> USERAUTH_VALUE_TIMEOUT,
			)
	);
	$context = stream_context_create($options);
	$response = @file_get_contents('https://sso.zeepro.com/' . $uri, false, $context);
	
	if ($response === FALSE || is_null($http_response_header)) {
		return 404;
	}
	
	return UserAuth__getHTTPCode($http_response_header);
}

function UserAuth__checkAccess($flag_check) {
	if (isset($_SESSION[USERAUTH_TITLE_ACCESS])
			&& $_SESSION[USERAUTH_TITLE_ACCESS] & $flag_check == $flag_check) {
		return TRUE;
	}
	
	return FALSE;
}

function UserAuth_initialSession() {
	$CI = &get_instance();
	$session_path = $CI->config->item('temp') . 'php_session';
	
	try {
		// change session folder
		if (!is_dir($session_path)) {
			if (file_exists($session_path)) {
				unlink($session_path);
			}
			mkdir($session_path);
			chmod($session_path, 0777);
		}
		session_save_path($session_path);
		ini_set('session.gc_probability', 1); // for debian
		
		// check codeigniter and php session
		session_start();
		$CI->load->library('session');
		// user data in php session found
		if (isset($_SESSION[USERAUTH_TITLE_TOKEN]) && isset($_SESSION[USERAUTH_TITLE_ACCESS])) {
			$sessid_ci = $CI->session->userdata(USERAUTH_TITLE_SESSID);
			
			if (!$sessid_ci || $sessid_ci != session_id()) {
				$CI->session->set_userdata(USERAUTH_TITLE_SESSID, session_id());
			}
		}
		// user data in codeigniter session (just php session id) found
		else if ($CI->session->userdata(USERAUTH_TITLE_SESSID) != FALSE
				&& $CI->session->userdata(USERAUTH_TITLE_SESSID) != session_id()) {
			// change session id to stored one
			session_destroy();
			session_unset();
			session_id($CI->session->userdata(USERAUTH_TITLE_SESSID));
			session_start();
		}
	}
	catch (Exception $e) {
		return FALSE;
	}
	
	return TRUE;
}

function UserAuth_getUserAccess($user_token) {
	$CI = &get_instance();
	$CI->load->helper('zimapi');
	
	if ($user_token) {
		$response = NULL;
		$ret_val = UserAuth__requestSSO(USERAUTH_URI_USERLEVEL,
				array(
						USERAUTH_PRM_TOKEN	=> $user_token,
						USERAUTH_PRM_SERIAL	=> ZimAPI_getSerial(),
		), $response);
		
		if ($ret_val == USERAUTH_RESPONSE_OK) {
			$flag_assign = 0;
			$return_array = json_decode($response, TRUE);
			
			if (is_array($return_array)) {
				foreach(array(
								USERAUTH_PRM_P_VIEW		=> USERAUTH_FLAG_VIEW | USERAUTH_FLAG_VIEW_ALL,
								USERAUTH_PRM_P_MANAGE	=> USERAUTH_FLAG_MANAGE | USERAUTH_FLAG_MANAGE_ALL,
								USERAUTH_PRM_P_ACCOUNT	=> USERAUTH_FLAG_ACCOUNT,
						) as $key_check => $user_flag) {
					if (isset($return_array[$key_check]) && $return_array[$key_check] == USERAUTH_VALUE_P_ON) {
						$flag_assign |= $user_flag;
					}
				}
			}
			
// 			// codeigniter session case
// 			$CI->load->library('session');
// 			$CI->session->set_userdata(array(
// 					USERAUTH_TITLE_TOKEN	=> $user_token,
// 					USERAUTH_TITLE_ACCESS	=> $flag_assign,
// 			));
			
			// set session
			if (PHP_SESSION_NONE == session_status()) UserAuth_initialSession();
			$_SESSION[USERAUTH_TITLE_TOKEN] = $user_token;
			$_SESSION[USERAUTH_TITLE_ACCESS] = $flag_assign;
			
			return TRUE;
		}
	}
	
	return FALSE;
}

function UserAuth_getUserListArray(&$user_list) {
	$CI = &get_instance();
	$ret_val = 0;
	$cr = 0;
	$response = NULL;
	
	$CI->load->helper('zimapi');
	
	if (PHP_SESSION_NONE == session_status()) UserAuth_initialSession();
	$ret_val = UserAuth__requestSSO(USERAUTH_URI_LISTUSER, array(
			USERAUTH_PRM_TOKEN	=> $_SESSION[USERAUTH_TITLE_TOKEN],
			USERAUTH_PRM_SERIAL	=> ZimAPI_getSerial(),
	), $response);
	
	$user_list = array();
	switch ($ret_val) {
		case USERAUTH_RESPONSE_OK:
			$array_return = NULL; //array()
			
			$cr = ERROR_OK;
			$array_return = json_decode($response, TRUE);
			if (is_array($array_return)) {
				foreach ($array_return as $user_element) {
					$user_data = array();
					$data_ok = TRUE;
					
					foreach (array(
							USERAUTH_TITLE_EMAIL, USERAUTH_TITLE_NAME,
							USERAUTH_PRM_P_VIEW, USERAUTH_PRM_P_MANAGE, USERAUTH_PRM_P_ACCOUNT,
					) as $key_check) {
						if (isset($user_element[$key_check])) {
							if (in_array($key_check, array( USERAUTH_TITLE_EMAIL, USERAUTH_TITLE_NAME ))) {
								$user_data[$key_check] = $user_element[$key_check];
							}
							else if ($user_element[$key_check] == USERAUTH_VALUE_P_ON) {
								$user_data[$key_check] = TRUE;
							}
							else { // $user_element[$key_check] == USERAUTH_VALUE_P_OFF
								$user_data[$key_check] = FALSE;
							}
						}
						else {
							$data_ok = FALSE;
							break;
						}
					}
					if ($data_ok) {
						// treat empty name as email address
						if (0 == strlen($user_data[USERAUTH_TITLE_NAME])) {
							$user_data[USERAUTH_TITLE_NAME] = $user_data[USERAUTH_TITLE_EMAIL];
						}
						
						$user_list[] = $user_data;
					}
					else {
						continue; // ignore break data
					}
				}
			}
			
			break;
		
		case USERAUTH_RESPONSE_MISS_PRM:
			$cr = ERROR_MISS_PRM;
			break;
			
		case USERAUTH_RESPONSE_UF_PRINTER:
		case USERAUTH_RESPONSE_UF_USER:
			$cr = ERROR_AUTHOR_FAIL;
			
		default:
			if ($cr == 0) $cr = ERROR_INTERNAL;
			$CI->load->helper('printerlog');
			PrinterLog_logError('sso listuser return failed: ' . $ret_val, __FILE__, __LINE__);
			break;
	}
	
	return $cr;
}

function UserAuth_grantUser($user_email, $user_name, $user_access) {
	$CI = &get_instance();
	$ret_val = 0;
	$cr = 0;
	$post_data = NULL; //array()
	
	$CI->load->helper('zimapi');
	
	if (PHP_SESSION_NONE == session_status()) UserAuth_initialSession();
	$post_data = array(
			USERAUTH_PRM_TOKEN	=> $_SESSION[USERAUTH_TITLE_TOKEN],
			USERAUTH_PRM_SERIAL	=> ZimAPI_getSerial(),
			USERAUTH_PRM_EMAIL	=> $user_email,
			USERAUTH_PRM_NAME	=> $user_name,
	);
	
	foreach(array(
			USERAUTH_PRM_P_VIEW,
			USERAUTH_PRM_P_MANAGE,
			USERAUTH_PRM_P_ACCOUNT
	) as $key_check) {
		if (isset($user_access[$key_check]) && $user_access[$key_check] == TRUE) {
			$post_data[$key_check] = USERAUTH_VALUE_P_ON;
		}
		else {
			$post_data[$key_check] = USERAUTH_VALUE_P_OFF;
		}
	}
	
	// send request
	$ret_val = UserAuth__requestSSO(USERAUTH_URI_GRANTUSER, $post_data);
	
	switch ($ret_val) {
		case USERAUTH_RESPONSE_OK:
			$cr = ERROR_OK;
			break;
		
		case USERAUTH_RESPONSE_MISS_PRM:
			$cr = ERROR_MISS_PRM;
			break;
			
		case USERAUTH_RESPONSE_WRONG_PRM:
			$cr = ERROR_WRONG_PRM;
			break;
			
		case USERAUTH_RESPONSE_UF_PRINTER:
		case USERAUTH_RESPONSE_UF_USER:
			$cr = ERROR_AUTHOR_FAIL;
			
		default:
			if ($cr == 0) $cr = ERROR_INTERNAL;
			$CI->load->helper('printerlog');
			PrinterLog_logError('sso grantuser return failed: ' . $ret_val, __FILE__, __LINE__);
			break;
	}
	
	return $cr;
}

function UserAuth_revokeUser($user_email) {
	$CI = &get_instance();
	$ret_val = 0;
	$cr = 0;
	
	$CI->load->helper('zimapi');
	
	if (PHP_SESSION_NONE == session_status()) UserAuth_initialSession();
	
	// send request
	$ret_val = UserAuth__requestSSO(USERAUTH_URI_REVOKEUSER, array(
			USERAUTH_PRM_TOKEN	=> $_SESSION[USERAUTH_TITLE_TOKEN],
			USERAUTH_PRM_SERIAL	=> ZimAPI_getSerial(),
			USERAUTH_PRM_EMAIL	=> $user_email,
	));
	
	switch ($ret_val) {
		case USERAUTH_RESPONSE_OK:
			$cr = ERROR_OK;
			break;
		
		case USERAUTH_RESPONSE_MISS_PRM:
			$cr = ERROR_MISS_PRM;
			break;
			
		case USERAUTH_RESPONSE_WRONG_PRM:
			$cr = ERROR_WRONG_PRM;
			break;
			
		case USERAUTH_RESPONSE_UF_PRINTER:
		case USERAUTH_RESPONSE_UF_USER:
			$cr = ERROR_AUTHOR_FAIL;
			
		default:
			if ($cr == 0) $cr = ERROR_INTERNAL;
			$CI->load->helper('printerlog');
			PrinterLog_logError('sso revokeuser return failed: ' . $ret_val, __FILE__, __LINE__);
			break;
	}
	
	return $cr;
}

function UserAuth_getUserInfo(&$user_info) {
	$CI = &get_instance();
	$ret_val = 0;
	$cr = 0;
	$response = NULL;
	
	$CI->load->helper('zimapi');
	
	if (PHP_SESSION_NONE == session_status()) UserAuth_initialSession();
	// send request
	$ret_val = UserAuth__requestSSO(USERAUTH_URI_GET_USERINFO, array(
			USERAUTH_PRM_TOKEN	=> $_SESSION[USERAUTH_TITLE_TOKEN],
			USERAUTH_PRM_SERIAL	=> ZimAPI_getSerial(),
	), $response);
	
	$user_info = array();
	switch ($ret_val) {
		case USERAUTH_RESPONSE_OK:
			$array_return = NULL; //array()
			
			$cr = ERROR_OK;
			$array_return = json_decode($response, TRUE);
			if (is_array($array_return)) {
				foreach (array(
						USERAUTH_TITLE_COUNTRY, USERAUTH_TITLE_CITY, USERAUTH_TITLE_BIRTHDAY,
						USERAUTH_TITLE_WHY, USERAUTH_TITLE_WHAT
				) as $info_key) {
					$user_info[$info_key] = isset($array_return[$info_key]) ? $array_return[$info_key] : NULL;
				}
			}
			break;
			
		case USERAUTH_RESPONSE_MISS_PRM:
			$cr = ERROR_MISS_PRM;
			break;
			
		case USERAUTH_RESPONSE_UF_USER:
			$cr = ERROR_AUTHOR_FAIL;
			
		default:
			if ($cr == 0) $cr = ERROR_INTERNAL;
			$CI->load->helper('printerlog');
			PrinterLog_logError('sso getuserinfo return failed: ' . $ret_val, __FILE__, __LINE__);
			break;
	}
	
	return $cr;
}

function UserAuth_setUserInfo($array_info) {
	$CI = &get_instance();
	$ret_val = 0;
	$cr = 0;
	
	$CI->load->helper('zimapi');
	
	if (PHP_SESSION_NONE == session_status()) UserAuth_initialSession();
	// send request
	$ret_val = UserAuth__requestSSO(USERAUTH_URI_SET_USERINFO, array(
			USERAUTH_PRM_TOKEN		=> $_SESSION[USERAUTH_TITLE_TOKEN],
			USERAUTH_PRM_SERIAL		=> ZimAPI_getSerial(),
			USERAUTH_TITLE_COUNTRY	=> $array_info[USERAUTH_TITLE_COUNTRY],
			USERAUTH_TITLE_CITY		=> $array_info[USERAUTH_TITLE_CITY],
			USERAUTH_TITLE_BIRTHDAY	=> $array_info[USERAUTH_TITLE_BIRTHDAY],
			USERAUTH_TITLE_WHY		=> $array_info[USERAUTH_TITLE_WHY],
			USERAUTH_TITLE_WHAT		=> $array_info[USERAUTH_TITLE_WHAT],
	));
	
	switch ($ret_val) {
		case USERAUTH_RESPONSE_OK:
			$cr = ERROR_OK;
			break;
		
		case USERAUTH_RESPONSE_MISS_PRM:
			$cr = ERROR_MISS_PRM;
			break;
			
		case USERAUTH_RESPONSE_WRONG_PRM:
			$cr = ERROR_WRONG_PRM;
			break;
			
		case USERAUTH_RESPONSE_UF_USER:
			$cr = ERROR_AUTHOR_FAIL;
			
		default:
			if ($cr == 0) $cr = ERROR_INTERNAL;
			$CI->load->helper('printerlog');
			PrinterLog_logError('sso setuserinfo return failed: ' . $ret_val, __FILE__, __LINE__);
			break;
	}
	
	return $cr;
}

function UserAuth_checkView() {
	return UserAuth__checkAccess(USERAUTH_FLAG_VIEW);
}

function UserAuth_checkViewAll() {
	return UserAuth__checkAccess(USERAUTH_FLAG_VIEW_ALL);
}

function UserAuth_checkManage() {
	return UserAuth__checkAccess(USERAUTH_FLAG_MANAGE);
}

function UserAuth_checkManageAll() {
	return UserAuth__checkAccess(USERAUTH_FLAG_MANAGE_ALL);
}

function UserAuth_checkAccount() {
	return UserAuth__checkAccess(USERAUTH_FLAG_ACCOUNT);
}

function UserAuth_removeSessionData() {
	if (PHP_SESSION_NONE == session_status()) UserAuth_initialSession();

// 	unset($_SESSION[USERAUTH_TITLE_TOKEN]);
// 	unset($_SESSION[USERAUTH_TITLE_ACCESS]);
	session_destroy();
	session_unset();
	// attention: we have no session after this function
}

function UserAuth_checkSessionExist() {
// 	// codeigniter session case
// 	$CI = &get_instance();
// 	$CI->load->library('session');
	
// 	if ($CI->session->userdata(USERAUTH_TITLE_TOKEN) && $CI->session->userdata(USERAUTH_TITLE_ACCESS)
// 			&& ($CI->session->userdata(USERAUTH_TITLE_ACCESS) | USERAUTH_FLAG_CHECK_ACCESS)) {
// 		return TRUE;
// 	}
	
	if (PHP_SESSION_NONE == session_status()) UserAuth_initialSession();
	if (isset($_SESSION[USERAUTH_TITLE_TOKEN]) && isset($_SESSION[USERAUTH_TITLE_ACCESS])
			&& ($_SESSION[USERAUTH_TITLE_ACCESS] | USERAUTH_FLAG_CHECK_ACCESS)) {
		return TRUE;
	}
	
	return FALSE;
}
