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
	define('USERAUTH_PRM_MESSAGE',		'message');
	
	define('USERAUTH_PRM_MODEL_ID',		'id');
	define('USERAUTH_PRM_MODEL_NAME',	'name');
	define('USERAUTH_PRM_PRINT_DATE',	'date');
	
	define('USERAUTH_TITLE_EMAIL',		'email');
	define('USERAUTH_TITLE_NAME',		'name');
	define('USERAUTH_TITLE_COUNTRY',	'country');
	define('USERAUTH_TITLE_CITY',		'city');
	define('USERAUTH_TITLE_BIRTHDAY',	'birth_date');
	define('USERAUTH_TITLE_WHY',		'why');
	define('USERAUTH_TITLE_WHAT',		'what');
	
	define('USERAUTH_TITLE_MODEL_ID',			'id');
	define('USERAUTH_TITLE_MODEL_NAME',			'name');
	define('USERAUTH_TITLE_MODEL_DESP',			'description');
	define('USERAUTH_TITLE_MODEL_IMAGE',		'img');
	define('USERAUTH_TITLE_MODEL_PART1',		'3dfile1');
	define('USERAUTH_TITLE_MODEL_PART2',		'3dfile2');
	define('USERAUTH_TITLE_MODEL_TAG_PART1',	'3dfile1etag');
	define('USERAUTH_TITLE_MODEL_TAG_PART2',	'3dfile2etag');
	define('USERAUTH_TITLE_MODEL_DESP_DATE',	'creation_date');
	define('USERAUTH_TITLE_MODEL_PRINT',		'print');
	define('USERAUTH_TITLE_PRINT_DATE',			'date');
	define('USERAUTH_TITLE_PRINT_DESP',			'description');
	define('USERAUTH_TITLE_PRINT_GCODE',		'gcode');
	define('USERAUTH_TITLE_PRINT_TAG_GCODE',	'gcodeetag');
	define('USERAUTH_TITLE_PRINT_VIDEO',		'video');
	define('USERAUTH_TITLE_PRINT_TAG_VIDEO',	'videoetag');
	define('USERAUTH_TITLE_PRINT_IMAGE',		'img');
	define('USERAUTH_TITLE_PRINT_DESP_NAME',	'name');
	define('USERAUTH_TITLE_PRINT_DESP_MAT1',	'm1');
	define('USERAUTH_TITLE_PRINT_DESP_MAT2',	'm2');
	define('USERAUTH_TITLE_PRINT_DESP_LENG1',	'l1');
	define('USERAUTH_TITLE_PRINT_DESP_LENG2',	'l2');
	define('USERAUTH_TITLE_PRINT_DESP_TEMP1',	't1');
	define('USERAUTH_TITLE_PRINT_DESP_TEMP2',	't2');
	define('USERAUTH_TITLE_PRINT_DESP_TEMPB',	'tb');
	define('USERAUTH_TITLE_PRINT_DESP_PRESET',	'preset');
	
	define('USERAUTH_TITLE_USERLIB_STATE',	'state');
	
// 	define('USERAUTH_TITLE_JSON_M_NAME',	'name');
	define('USERAUTH_TITLE_JSON_M_MULTI',	'multiple');
	define('USERAUTH_TITLE_JSON_M_DATE',	'creation_date');
	define('USERAUTH_TITLE_JSON_P_NAME',	'name');
	define('USERAUTH_TITLE_JSON_P_LENG1',	'l1');
	define('USERAUTH_TITLE_JSON_P_LENG2',	'l2');
	define('USERAUTH_TITLE_JSON_P_MAT1',	'm1');
	define('USERAUTH_TITLE_JSON_P_MAT2',	'm2');
	define('USERAUTH_TITLE_JSON_P_TIME',	'creation_time');
	define('USERAUTH_TITLE_JSON_P_PRESET',	'preset');
	define('USERAUTH_TITLE_JSON_P_TEMPER1',	't1');
	define('USERAUTH_TITLE_JSON_P_TEMPER2',	't2');
	define('USERAUTH_TITLE_JSON_P_TEMPERB',	'tb');
	
	define('USERAUTH_PRM_UTIL_UPLOAD_M',	' upload_stl ');
	define('USERAUTH_PRM_UTIL_UPLOAD_P',	' upload_print ');
	define('USERAUTH_PRM_UTIL_SYNC_ALL',	' sync_all_local_lib ');
	
	define('USERAUTH_VALUE_P_ON',	'yes');
	define('USERAUTH_VALUE_P_OFF',	'no');
	
	define('USERAUTH_VALUE_UL_UPLOAD',		'uploading');
	define('USERAUTH_VALUE_UL_M_NEW',		0);
	define('USERAUTH_VALUE_UL_M_UPLOAD',	1);
	define('USERAUTH_VALUE_UL_M_READY',		2);
	define('USERAUTH_VALUE_UL_P_UPLOAD',	3);
	define('USERAUTH_VALUE_UL_P_READY',		4);
	define('USERAUTH_VALUE_USERLIB_COLOR1',	'#FFFFFF');
	define('USERAUTH_VALUE_USERLIB_COLOR2',	'#FF0000');
	
// 	define('USERAUTH_URL_REDIRECTION',	'http://home.dev'); // dev
// 	define('USERAUTH_URL_REDIRECTION',	'https://zhomedev.azurewebsites.net'); // beta
	define('USERAUTH_URL_REDIRECTION',	'https://zeeproshare.com'); // prod
	define('USERAUTH_URL_SSO',			'https://sso.zeepro.com/');
	define('USERAUTH_URI_USERLEVEL',	'useraccess.ashx');
	define('USERAUTH_URI_GRANTUSER',	'grantuser.ashx');
	define('USERAUTH_URI_LISTUSER',		'listuser.ashx');
	define('USERAUTH_URI_REVOKEUSER',	'revokeuser.ashx');
	define('USERAUTH_URI_GET_USERINFO',	'getuserinfo.ashx');
	define('USERAUTH_URI_SET_USERINFO',	'setuserinfo.ashx');
	define('USERAUTH_URI_REMOTE_INDEX',	'/user/v2');
	
	define('USERAUTH_URI_USERLIB_LIST',		'userlib/list.ashx');
	define('USERAUTH_URI_USERLIB_DELETE_M',	'userlib/delete.ashx');
	define('USERAUTH_URI_USERLIB_DELETE_P',	'userlib/deleteprint.ashx');
	define('USERAUTH_URI_USERLIB_CREATE_M',	'userlib/create.ashx');
	
	define('USERAUTH_RESPONSE_OK',			200);
	define('USERAUTH_RESPONSE_MISS_PRM',	432);
	define('USERAUTH_RESPONSE_WRONG_PRM',	433);
	define('USERAUTH_RESPONSE_UF_PRINTER',	436);
	define('USERAUTH_RESPONSE_UF_USER',		442);
	define('USERAUTH_RESPONSE_TOOMANY_REQ',	435);
	define('USERAUTH_RESPONSE_UNKWN_MODEL',	443);
	define('USERAUTH_RESPONSE_MODEL_EXIST',	444);
	define('USERAUTH_RESPONSE_UNKWN_PRINT',	445);
	
	define('USERAUTH_VALUE_PREFIX_USERLIB',	$CI->config->item('temp') . 'userlib_');
	define('USERAUTH_VALUE_FOLDER_TMP_F',	'remote_lib');
	define('USERAUTH_VALUE_PREFIX_TMP_F',	'tUL');
	define('USERAUTH_VALUE_SUFFIX_TMP_F_M',	'Fm');
	define('USERAUTH_VALUE_SUFFIX_TMP_F_P',	'Fp');
	define('USERAUTH_VALUE_FILE_M_IMAGE',	'/image.png');
	define('USERAUTH_VALUE_FILE_M_INFO',	'/info.json');
	define('USERAUTH_VALUE_FILE_M_MODEL_P',	'/model');
	define('USERAUTH_VALUE_FILE_M_MODEL_S',	'.stl');
	define('USERAUTH_VALUE_FILE_P_IMAGE',	'/image.jpg');
	define('USERAUTH_VALUE_FILE_P_VIDEO',	'/timelapse.mp4');
	define('USERAUTH_VALUE_FILE_P_GCODE',	'/model.gcode');
	define('USERAUTH_VALUE_FILE_P_INFO',	'/info.json');
	define('USERAUTH_VALUE_USERLIB_LOGF',	'/var/log/userlib.log');
	define('USERAUTH_VALUE_FOLDER_CACHE',	$CI->config->item('base_library') . 'cache/');
	define('USERAUTH_VALUE_FILE_M_CACHE_S',	'.stl.zip');
	define('USERAUTH_VALUE_SUFFIX_CACHE_T',	'.tmp');
	define('USERAUTH_VALUE_FILE_P_CACHE_S',	'.gcode.zip');
	define('USERAUTH_VALUE_FILE_GCODE_EXT',	'model.gcode');
	define('USERAUTH_VALUE_FILE_FLAG_SYNC',	$CI->config->item('temp') . 'sync_userlib.tmp');
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
	$response = @file_get_contents(USERAUTH_URL_SSO . $uri, false, $context);
	
	if ($response === FALSE || is_null($http_response_header)) {
		return 404;
	}
	
	return UserAuth__getHTTPCode($http_response_header);
}

function UserAuth__checkAccess($flag_check) {
	if (isset($_SESSION[USERAUTH_TITLE_ACCESS])
			&& ($_SESSION[USERAUTH_TITLE_ACCESS] & $flag_check) == $flag_check) {
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

function UserAuth_grantUser($user_email, $user_name, $user_access, $user_message = NULL) {
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
	
	// user permission
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
	// user message
	if (strlen($user_message)) {
		$post_data[USERAUTH_PRM_MESSAGE] = $user_message;
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
	
// 	$CI->load->helper('zimapi');
	
	if (PHP_SESSION_NONE == session_status()) UserAuth_initialSession();
	// send request
	$ret_val = UserAuth__requestSSO(USERAUTH_URI_GET_USERINFO, array(
			USERAUTH_PRM_TOKEN	=> $_SESSION[USERAUTH_TITLE_TOKEN],
// 			USERAUTH_PRM_SERIAL	=> ZimAPI_getSerial(),
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
	
// 	$CI->load->helper('zimapi');
	
	if (PHP_SESSION_NONE == session_status()) UserAuth_initialSession();
	// send request
	$ret_val = UserAuth__requestSSO(USERAUTH_URI_SET_USERINFO, array(
			USERAUTH_PRM_TOKEN		=> $_SESSION[USERAUTH_TITLE_TOKEN],
// 			USERAUTH_PRM_SERIAL		=> ZimAPI_getSerial(),
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
	
	// remove userlib for session
	@unlink(USERAUTH_VALUE_PREFIX_USERLIB . session_id());
	
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
		// launch synchronization of model if necessary
		if (!file_exists(USERAUTH_VALUE_FILE_FLAG_SYNC)) {
			$output = array();
			$ret_val = 0;
			$CI = &get_instance();
			$command = $CI->config->item('siteutil') . USERAUTH_PRM_UTIL_SYNC_ALL
					. "'" . $_SESSION[USERAUTH_TITLE_TOKEN] . "'";
			
			// set flag file
			$fp = fopen(USERAUTH_VALUE_FILE_FLAG_SYNC, 'w');
			if ($fp) {
				fwrite($fp, 'sync ' . $_SESSION[USERAUTH_TITLE_TOKEN]);
				fclose($fp);
				chmod(USERAUTH_VALUE_FILE_FLAG_SYNC, 0777);
			}
			
			// run command
			exec($command, $output, $ret_val);
			$CI->load->helper('printerlog'); PrinterLog_logDebug('sync command: ' . $command); // test
			if ($ret_val != ERROR_NORMAL_RC_OK) {
				$CI->load->helper('printerlog');
				PrinterLog_logError('sync model+print command failed: ' . $command);
				// don't return FALSE if error
			}
		}
		
		return TRUE;
	}
	
	return FALSE;
}

function UserAuth__checkURLStateReady($file) {
	if ($file != USERAUTH_VALUE_UL_UPLOAD
	&& FALSE === !filter_var($file, FILTER_VALIDATE_URL)) {
		return TRUE;
	}

	return FALSE;
}

function UserAuth_getUserLib(&$userlib, $force_reload = FALSE) {
	$CI = &get_instance();
	$file_userlib = USERAUTH_VALUE_PREFIX_USERLIB . session_id();
	
	$userlib = array();
	
	// use cache if possible
	if ($force_reload == FALSE && file_exists($file_userlib)) {
		$tmp_json = NULL; //array()
		
		$CI->load->helper('json');
		
		$tmp_json = json_read($file_userlib, TRUE);
		if (!$tmp_json['error']) {
			$userlib =  $tmp_json['json'];
			return ERROR_OK;
		}
	}
	
	$response = NULL;
	$retry = FALSE;
	$ret_val = 0;
	$cr = 0;
	
	do { // retry once if 435, break out if not
		$ret_val = UserAuth__requestSSO(USERAUTH_URI_USERLIB_LIST,
				array( USERAUTH_PRM_TOKEN => $_SESSION[USERAUTH_TITLE_TOKEN] ), $response);
		if ($ret_val == USERAUTH_RESPONSE_TOOMANY_REQ && $retry == FALSE) {
			$retry = TRUE;
			sleep(3); // sleep 3 to pass the limit of 5 times in 5 seconds
			continue;
		}
		else {
			break;
		}
	} while (TRUE);
	
	switch ($ret_val) {
		case USERAUTH_RESPONSE_OK:
			$tmp_json = json_decode($response, TRUE);
			
			if (!is_array($tmp_json)) { // is_null($tmp_json)
				$cr = ERROR_INTERNAL;
				
				$CI->load->helper('printerlog');
				PrinterLog_logError('cannot decode userlib list from sso', __FILE__, __LINE__);
			}
			else {
				$cr = ERROR_OK;
				$userlib = $tmp_json;
				
				// write json into cache file
				$fp = fopen($file_userlib, 'w');
				if ($fp) {
					fwrite($fp, $response);
					fclose($fp);
				}
				else {
					$cr = ERROR_INTERNAL;
				}
			}
			break;
			
		case USERAUTH_RESPONSE_TOOMANY_REQ:
			$cr = ERROR_BUSY_PRINTER;
			break;
			
		case USERAUTH_RESPONSE_UF_USER:
			$cr = ERROR_AUTHOR_FAIL;
			break;
			
		case USERAUTH_RESPONSE_MISS_PRM:
			$cr = ERROR_MISS_PRM;
			break;
			
		default:
			$cr = ERROR_INTERNAL;
			
			$CI->load->helper('printerlog');
			PrinterLog_logError('unknown return from sso for userlib: ' . $ret_val);
			break;
	}
	
	return $cr;
}

function UserAuth_getUserModelList(&$list_model, $force_reload = FALSE) {
	$list_model = array();
	$userlib = array();
	$cr = UserAuth_getUserLib($userlib, $force_reload);
	
	if ($cr == ERROR_OK) {
		foreach ($userlib as $model_ele) {
			if (isset($model_ele[USERAUTH_TITLE_MODEL_ID])
					&& isset($model_ele[USERAUTH_TITLE_MODEL_NAME])) {
				$model_ready = FALSE;
				$ele_list = array(
						USERAUTH_TITLE_MODEL_ID		=> $model_ele[USERAUTH_TITLE_MODEL_ID],
						USERAUTH_TITLE_MODEL_NAME	=> $model_ele[USERAUTH_TITLE_MODEL_NAME],
				);
				
				if (isset($model_ele[USERAUTH_TITLE_MODEL_PART1])) {
// 						&& isset($model_ele[USERAUTH_TITLE_MODEL_DESP])) {
					if (UserAuth__checkURLStateReady($model_ele[USERAUTH_TITLE_MODEL_PART1])) {
						$model_ready = TRUE;
						
						if (isset($model_ele[USERAUTH_TITLE_MODEL_IMAGE])) {
							$ele_list[USERAUTH_TITLE_MODEL_IMAGE] = $model_ele[USERAUTH_TITLE_MODEL_IMAGE];
						}
					}
					$ele_list[USERAUTH_TITLE_USERLIB_STATE] = $model_ready ? USERAUTH_VALUE_UL_M_READY : USERAUTH_VALUE_UL_M_UPLOAD;
				}
				else if (isset($model_ele[USERAUTH_TITLE_MODEL_PRINT])) {
					foreach($model_ele[USERAUTH_TITLE_MODEL_PRINT] as $print_ele) {
						if (isset($print_ele[USERAUTH_TITLE_PRINT_GCODE])
								&& isset($print_ele[USERAUTH_TITLE_MODEL_DESP])
								&& UserAuth__checkURLStateReady($print_ele[USERAUTH_TITLE_PRINT_GCODE])) {
							$model_ready = TRUE;
							
							if (isset($print_ele[USERAUTH_TITLE_PRINT_IMAGE])) {
								$ele_list[USERAUTH_TITLE_MODEL_IMAGE] = $print_ele[USERAUTH_TITLE_PRINT_IMAGE];
							}
							break;
						}
					}
					$ele_list[USERAUTH_TITLE_USERLIB_STATE] = $model_ready ? USERAUTH_VALUE_UL_P_READY : USERAUTH_VALUE_UL_P_UPLOAD;
				}
				else {
					$ele_list[USERAUTH_TITLE_USERLIB_STATE] = USERAUTH_VALUE_UL_M_NEW;
				}
				
				$list_model[] = $ele_list;
			}
			else {
				continue; // ignore model without id and name (abnormal case)
			}
		}
	}
	
	return $cr;
}

function UserAuth__getPrintDetailFromData($print_ele, &$print_info) {
	$print_info = array();
	
	if (isset($print_ele[USERAUTH_TITLE_PRINT_DATE])) {
		$print_ready = FALSE;
		$print_info = array(
				USERAUTH_TITLE_PRINT_DATE	=> $print_ele[USERAUTH_TITLE_PRINT_DATE],
		);
		
		if (isset($print_ele[USERAUTH_TITLE_PRINT_GCODE])
				&& isset($print_ele[USERAUTH_TITLE_PRINT_DESP])
				&& UserAuth__checkURLStateReady($print_ele[USERAUTH_TITLE_PRINT_GCODE])) {
			$tmp_string = json_decode($print_ele[USERAUTH_TITLE_PRINT_DESP], TRUE);
			
			if (!is_array($tmp_string)) {
				$CI = &get_instance();
				$CI->load->helper('printerlog');
				PrinterLog_logError('cannot decode sso userlib print description', __FILE__, __LINE__);
				
				return FALSE; // ignore print if description cannot be decoded
			}
			
			$print_ready = TRUE;
			$print_info[USERAUTH_TITLE_PRINT_GCODE] = $print_ele[USERAUTH_TITLE_PRINT_GCODE];
			$print_info[USERAUTH_TITLE_PRINT_IMAGE] = $print_ele[USERAUTH_TITLE_PRINT_IMAGE];
			$print_info[USERAUTH_TITLE_PRINT_TAG_GCODE] = $print_ele[USERAUTH_TITLE_PRINT_TAG_GCODE];
			
			foreach (array(
					USERAUTH_TITLE_PRINT_DESP_LENG1		=> 'tmp_string',
					USERAUTH_TITLE_PRINT_DESP_LENG2		=> 'tmp_string',
					USERAUTH_TITLE_PRINT_DESP_MAT1		=> 'tmp_string',
					USERAUTH_TITLE_PRINT_DESP_MAT2		=> 'tmp_string',
					USERAUTH_TITLE_PRINT_DESP_PRESET	=> 'tmp_string',
					USERAUTH_TITLE_PRINT_DESP_NAME		=> 'tmp_string',
					USERAUTH_TITLE_PRINT_DESP_TEMP1		=> 'tmp_string',
					USERAUTH_TITLE_PRINT_DESP_TEMP2		=> 'tmp_string',
					USERAUTH_TITLE_PRINT_DESP_TEMPB		=> 'tmp_string',
					USERAUTH_TITLE_PRINT_VIDEO			=> 'print_ele',
					USERAUTH_TITLE_PRINT_TAG_VIDEO		=> 'print_ele',
			) as $key_check => $array_check) {
				$print_info[$key_check] = array_key_exists($key_check, $$array_check)
						? ${$array_check}[$key_check] : NULL;
			}
		}
		
		$print_info[USERAUTH_TITLE_USERLIB_STATE] = $print_ready ? USERAUTH_VALUE_UL_P_READY : USERAUTH_VALUE_UL_P_UPLOAD;
		
		return TRUE;
	}
	
	return FALSE; // ignore print without date (abnormal case)
}

function UserAuth_getUserModelDetail($id_model, $need_print, $need_model, &$list_print = array(), &$model_info = array(), $force_reload = FALSE) {
	$list_print = array();
	$model_info = array();
	$userlib = array();
	$cr = UserAuth_getUserLib($userlib, $force_reload);
	
	if ($cr == ERROR_OK) {
		foreach ($userlib as $model_ele) {
			if (isset($model_ele[USERAUTH_TITLE_MODEL_ID])
					&& $model_ele[USERAUTH_TITLE_MODEL_ID] == $id_model) {
				if ($need_model) {
					$model_ready = FALSE;
					$model_info = array(
							USERAUTH_TITLE_MODEL_ID		=> $model_ele[USERAUTH_TITLE_MODEL_ID],
							USERAUTH_TITLE_MODEL_NAME	=> $model_ele[USERAUTH_TITLE_MODEL_NAME], // we think if we have id, we have name (not always true if error)
					);
					
					if (isset($model_ele[USERAUTH_TITLE_MODEL_PART1])
							&& isset($model_ele[USERAUTH_TITLE_MODEL_DESP])
							&& UserAuth__checkURLStateReady($model_ele[USERAUTH_TITLE_MODEL_PART1])) {
						$model_ready = TRUE;
						$model_date = NULL;
						$tmp_string = json_decode($model_ele[USERAUTH_TITLE_MODEL_DESP], TRUE);
						
						if (isset($tmp_string[USERAUTH_TITLE_MODEL_DESP_DATE])) {
							$model_date = $tmp_string[USERAUTH_TITLE_MODEL_DESP_DATE];
						}
						
						$model_info[USERAUTH_TITLE_MODEL_DESP_DATE] = $model_date;
						
						foreach(array(
								USERAUTH_TITLE_MODEL_PART1, USERAUTH_TITLE_MODEL_PART2,
								USERAUTH_TITLE_MODEL_TAG_PART1, USERAUTH_TITLE_MODEL_TAG_PART2,
						) as $check_key) {
							if (isset($model_ele[$check_key])) $model_info[$check_key] = $model_ele[$check_key];
						}
					}
					
					$model_info[USERAUTH_TITLE_USERLIB_STATE] = $model_ready ? USERAUTH_VALUE_UL_M_READY : USERAUTH_VALUE_UL_M_UPLOAD;
				}
				
				if ($need_print && isset($model_ele[USERAUTH_TITLE_MODEL_PRINT])) {
					foreach ($model_ele[USERAUTH_TITLE_MODEL_PRINT] as $print_ele) {
						$print_info = array();
						
						if (UserAuth__getPrintDetailFromData($print_ele, $print_info)) {
							$list_print[] = $print_info;
						}
						else {
							continue; // ignore print if grabbing info failed
						}
					}
				}
			}
			else {
				continue;
			}
		}
	}
	
	return $cr;
}

function UserAuth_getUserPrint($id_model, $timestamp, &$print_info, $force_reload = FALSE) {
	$print_info = array();
	$userlib = array();
	$cr = UserAuth_getUserLib($userlib, $force_reload);
	
	if ($cr == ERROR_OK) {
		$flag_found = FALSE;
		$check_date = @date('Y-m-d\TH:i:s', (int) $timestamp);
		
		if ($check_date === FALSE) {
			return ERROR_WRONG_PRM;
		}
		
		foreach ($userlib as $model_ele) {
			if (isset($model_ele[USERAUTH_TITLE_MODEL_ID])
					&& $model_ele[USERAUTH_TITLE_MODEL_ID] == $id_model
					&& isset($model_ele[USERAUTH_TITLE_MODEL_PRINT])) {
				foreach ($model_ele[USERAUTH_TITLE_MODEL_PRINT] as $print_ele) {
					if (isset($print_ele[USERAUTH_TITLE_PRINT_TAG_GCODE])
							&& $print_ele[USERAUTH_TITLE_PRINT_DATE] == $check_date) {
						if (UserAuth__getPrintDetailFromData($print_ele, $print_info)) {
							$flag_found = TRUE;
							break;
						}
						else {
							$print_info = array();
						}
					}
				}
				break;
			}
		}
		
		if (!$flag_found) {
			$cr = ERROR_NO_SLICED;
		}
	}
	
	return $cr;
}

function UserAuth_deleteUserModel($id_model, $force_reload = TRUE) {
	$ret_val = $cr = 0;
	
	$ret_val = UserAuth__requestSSO(USERAUTH_URI_USERLIB_DELETE_M, array(
			USERAUTH_PRM_TOKEN		=> $_SESSION[USERAUTH_TITLE_TOKEN],
			USERAUTH_PRM_MODEL_ID	=> (int) $id_model,
	));
	
	switch ($ret_val) {
		case USERAUTH_RESPONSE_OK:
			$cr = ERROR_OK;
			
			if ($force_reload) {
				$userlib = array();
				
				UserAuth_getUserLib($userlib, $force_reload);
			}
			break;
			
		case USERAUTH_RESPONSE_MISS_PRM:
			$cr = ERROR_MISS_PRM;
			break;
			
		case USERAUTH_RESPONSE_WRONG_PRM:
			$cr = ERROR_WRONG_PRM;
			break;
			
		case USERAUTH_RESPONSE_UF_USER:
			$cr = ERROR_AUTHOR_FAIL;
			break;
			
		case USERAUTH_RESPONSE_UNKWN_MODEL:
			$cr = ERROR_UNKNOWN_MODEL;
			break;
			
		default:
			$cr = ERROR_INTERNAL;
			$CI = &get_instance();
			$CI->load->helper('printerlog');
			PrinterLog_logError('sso return unknown code for delete model: ' . $ret_val);
			break;
	}
	
	return $cr;
}

function UserAuth_deleteUserPrint($id_model, $timestamp, $force_reload = TRUE) {
	$ret_val = $cr = 0;
	$check_date = @date('Y-m-d\TH:i:s', (int) $timestamp);
	
	if ($check_date === FALSE) {
		return ERROR_WRONG_PRM;
	}
	
	$ret_val = UserAuth__requestSSO(USERAUTH_URI_USERLIB_DELETE_P, array(
			USERAUTH_PRM_TOKEN		=> $_SESSION[USERAUTH_TITLE_TOKEN],
			USERAUTH_PRM_MODEL_ID	=> (int) $id_model,
			USERAUTH_PRM_PRINT_DATE	=> $check_date,
	));
	
	switch ($ret_val) {
		case USERAUTH_RESPONSE_OK:
			$cr = ERROR_OK;
			
			if ($force_reload) {
				$userlib = array();
				
				UserAuth_getUserLib($userlib, $force_reload);
			}
			break;
			
		case USERAUTH_RESPONSE_MISS_PRM:
			$cr = ERROR_MISS_PRM;
			break;
			
		case USERAUTH_RESPONSE_WRONG_PRM:
			$cr = ERROR_WRONG_PRM;
			break;
			
		case USERAUTH_RESPONSE_UF_USER:
			$cr = ERROR_AUTHOR_FAIL;
			break;
			
		case USERAUTH_RESPONSE_UNKWN_MODEL:
		case USERAUTH_RESPONSE_UNKWN_PRINT: //TODO add or alter a new errorcode for this case if necessary
			$cr = ERROR_UNKNOWN_MODEL;
			break;
			
		default:
			$cr = ERROR_INTERNAL;
			$CI = &get_instance();
			$CI->load->helper('printerlog');
			PrinterLog_logError('sso return unknown code for delete model: ' . $ret_val);
			break;
	}
	
	return $cr;
}

function UserAuth_requestNewModelId($model_name, &$model_id) {
	$ret_val = $cr = $model_id = 0;
	
	if (strlen($model_name) == 0) {
		return ERROR_WRONG_PRM;
	}
	
	$ret_val = UserAuth__requestSSO(USERAUTH_URI_USERLIB_CREATE_M, array(
			USERAUTH_PRM_TOKEN		=> $_SESSION[USERAUTH_TITLE_TOKEN],
			USERAUTH_PRM_MODEL_NAME	=> $model_name,
	));
	
	switch ($ret_val) {
		case USERAUTH_RESPONSE_MODEL_EXIST:
			$cr = ERROR_FULL_PRTLST; //TODO add or alter a new errorcode for this case if necessary
			
		case USERAUTH_RESPONSE_OK:
			$userlib = array();
			$ret_lib = UserAuth_getUserLib($userlib, TRUE);
			
			if ($cr == 0) $cr = ERROR_OK;
			if ($ret_lib == ERROR_OK) {
				// find id
				foreach ($userlib as $ele_model) {
					if (isset($ele_model[USERAUTH_TITLE_MODEL_ID])
							&& isset($ele_model[USERAUTH_TITLE_MODEL_NAME])
							&& $ele_model[USERAUTH_TITLE_MODEL_NAME] == $model_name) {
						$model_id = $ele_model[USERAUTH_TITLE_MODEL_ID];
					}
				}
				
				if ($model_id == 0) {
					$CI->load->helper('printerlog');
					PrinterLog_logError('cannot find model with name: ' . $model_name);
					
					$cr = ERROR_INTERNAL;
				}
			}
			else {
				$cr = $ret_lib;
			}
			
			break;
			
		case USERAUTH_RESPONSE_MISS_PRM:
			$cr = ERROR_MISS_PRM;
			break;
			
		case USERAUTH_RESPONSE_WRONG_PRM:
			$cr = ERROR_WRONG_PRM;
			break;
			
		case USERAUTH_RESPONSE_UF_USER:
			$cr = ERROR_AUTHOR_FAIL;
			break;
			
		case USERAUTH_RESPONSE_TOOMANY_REQ:
			$cr = ERROR_BUSY_PRINTER;
			break;
			
		default:
			$cr = ERROR_INTERNAL;
			$CI = &get_instance();
			$CI->load->helper('printerlog');
			PrinterLog_logError('sso return unknown code for delete model: ' . $ret_val);
			break;
	}
	
	return $cr;
}

// internal function
function UserAuth__prespareUserLibTempFolder(&$temp_basefolder) {
	$CI = &get_instance();
	
	$temp_basefolder = $CI->config->item('temp') . USERAUTH_VALUE_FOLDER_TMP_F;
	if (!file_exists($temp_basefolder)) {
		if (!mkdir($temp_basefolder, 0777, TRUE)) {
			return FALSE;
		}
	}
	
	return TRUE;
}

function UserAuth_uploadUserModel($model_id, $from_print = TRUE, $array_model = array()) {
	$CI = &get_instance();
	$ret_val = 0;
	$path_image = NULL;
	$temp_folder = NULL;
	$temp_basefolder = NULL;
	$nb_model = 0;
	
	if ($model_id <= 0) { // id 0 is called by system itself with different structure
		return ERROR_WRONG_PRM;
	}
	else if (!is_array($array_model)) {
		return ERROR_INTERNAL;
	}
	
	$CI->load->helper(array('slicer', 'zimapi'));
	if ($from_print) {
		$ret_val = Slicer_getModelFile(0, $array_model, FALSE);
		if ($ret_val != ERROR_OK) {
			return $ret_val;
		}
	}
	else {
		// add model to slicer
		$ret_val = Slicer_addModel($array_model, FALSE);
		if ($ret_val != ERROR_OK) {
			return $ret_val;
		}
	}
	
	// check model number
	$nb_model = count($array_model);
	if ($nb_model <= 0) {
		return ERROR_MISS_PRM;
	}
	else if ($nb_model > 2) {
		return ERROR_WRONG_PRM;
	}
	
	// generate rendering
	$ret_val = Slicer_rendering(ZIMAPI_VALUE_DEFAULT_RHO,
			ZIMAPI_VALUE_DEFAULT_THETA, ZIMAPI_VALUE_DEFAULT_DELTA, $path_image,
			USERAUTH_VALUE_USERLIB_COLOR1, USERAUTH_VALUE_USERLIB_COLOR2);
	if ($ret_val != ERROR_OK) {
		return $ret_val;
	}
	
	// prepare folder
	if (!UserAuth__prespareUserLibTempFolder($temp_basefolder)) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('model base folder prepare failed');
		
		return ERROR_INTERNAL;
	}
	
	$temp_folder = tempnam($temp_basefolder, USERAUTH_VALUE_PREFIX_TMP_F);
	if ($temp_folder != FALSE && !file_exists($temp_folder . USERAUTH_VALUE_SUFFIX_TMP_F_M)
			&& mkdir($temp_folder . USERAUTH_VALUE_SUFFIX_TMP_F_M, 0777)) {
		unlink($temp_folder);
		$temp_folder = $temp_folder . USERAUTH_VALUE_SUFFIX_TMP_F_M;
		
		rename($path_image, $temp_folder . USERAUTH_VALUE_FILE_M_IMAGE);
		
		$file_id = 1;
		foreach($array_model as $tmp_filepath) {
			rename($tmp_filepath, $temp_folder . USERAUTH_VALUE_FILE_M_MODEL_P
					. $file_id . USERAUTH_VALUE_FILE_M_MODEL_S);
		}
		
		$fp = fopen($temp_folder . USERAUTH_VALUE_FILE_M_INFO, 'w');
		if ($fp) {
			// write info json
			fwrite($fp, json_encode(array(
// 					USERAUTH_TITLE_JSON_M_NAME	=> $model_name,
					USERAUTH_TITLE_JSON_M_MULTI	=> ($nb_model > 1),
					USERAUTH_TITLE_JSON_M_DATE	=> date('Y-m-d\TH:i:s'),
			)));
			fclose($fp);
			
			// launch update script
			$output = array();
			$command = $CI->config->item('siteutil') . USERAUTH_PRM_UTIL_UPLOAD_M
					. "\"$temp_folder\" '" . $_SESSION[USERAUTH_TITLE_TOKEN] . "' $model_id";
			
			exec($command, $output, $ret_val);
			$CI->load->helper('printerlog'); PrinterLog_logDebug('upload command: ' . $command); // test
			if ($ret_val != ERROR_NORMAL_RC_OK) {
				$CI->load->helper('printerlog');
				PrinterLog_logError('upload model command failed: ' . $command);
				
				return ERROR_INTERNAL;
			}
			
			return ERROR_OK;
		}
		else {
			$CI->load->helper('printerlog');
			PrinterLog_logError('model json prepare failed');
			
			return ERROR_INTERNAL;
		}
	}
	else {
		$CI->load->helper('printerlog');
		PrinterLog_logError('model temp folder failed');
		
		return ERROR_DISK_FULL;
	}
	
	return ERROR_INTERNAL;
}

function UserAuth_uploadUserPrint($model_id, $print_name) {
	$data_json = NULL;
	$array_length = array();
	$array_material = array();
	$array_temper = array();
	$array_status = array();
	$nb_models = 0;
	$preset_id = NULL;
	$temp_folder = NULL;
	$temp_basefolder = NULL;
	$CI = &get_instance();
	
	if ($model_id <= 0) { // id 0 is called by system itself with different structure
		return ERROR_WRONG_PRM;
	}
	
	// get length and material info
	$CI->load->helper(array('printerstate', 'zimapi', 'slicer'));
	if (ERROR_OK != PrinterState_getSlicedJson($data_json)) {
		return ERROR_INTERNAL;
	}
	
	foreach(array('r', 'l') as $abb_cartridge) {
		if (array_key_exists($abb_cartridge, $data_json)
		&& array_key_exists(PRINTERSTATE_TITLE_NEED_L, $data_json[$abb_cartridge])
		&& $data_json[$abb_cartridge][PRINTERSTATE_TITLE_NEED_L] > 0) {
			$array_length[$abb_cartridge] = $data_json[$abb_cartridge][PRINTERSTATE_TITLE_NEED_L];
			$array_material[$abb_cartridge] = $data_json[$abb_cartridge][PRINTERSTATE_TITLE_MATERIAL];
			++$nb_models;
		}
		else {
			$array_length[$abb_cartridge] = 0;
			$array_material[$abb_cartridge] = NULL;
		}
	}
	
	// get preset id
	if (!ZimAPI_getPreset($preset_id)) {
		$preset_id = NULL;
	}
	
	// prepare temperature info
	$CI->load->helper('corestatus');
	if (!CoreStatus_getStatusArray($array_status)) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('get work status failed');
		
		return ERROR_INTERNAL;
	} else {
		foreach (array(
				'r'	=> CORESTATUS_TITLE_P_TEMPER_R,
				'l'	=> CORESTATUS_TITLE_P_TEMPER_L,
				'b'	=> CORESTATUS_TITLE_P_TEMPER_B,
		) as $abb_cartridge => $tmp_key) {
			$array_temper[$abb_cartridge] = (isset($array_status[$tmp_key]) ? $array_status[$tmp_key] : NULL);
		}
	}
	
	// prepare folder
	if (!UserAuth__prespareUserLibTempFolder($temp_basefolder)) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('print base folder prepare failed');
		
		return ERROR_INTERNAL;
	}
	
	$temp_folder = tempnam($temp_basefolder, USERAUTH_VALUE_PREFIX_TMP_F);
	if ($temp_folder != FALSE && !file_exists($temp_folder . USERAUTH_VALUE_SUFFIX_TMP_F_P)
			&& mkdir($temp_folder . USERAUTH_VALUE_SUFFIX_TMP_F_P, 0777)) {
		unlink($temp_folder);
		$temp_folder = $temp_folder . USERAUTH_VALUE_SUFFIX_TMP_F_P;
		
		// copy image
		if (!file_exists(ZIMAPI_FILEPATH_CAPTURE)
				|| !copy(ZIMAPI_FILEPATH_CAPTURE, $temp_folder . USERAUTH_VALUE_FILE_P_IMAGE)) {
			$CI->load->helper('printerlog');
			PrinterLog_logError('prepare print image failed');
			
			return ERROR_IMG_NOTFOUND;
		}
		
		// copy gcode
		$path_temp = $CI->config->item('temp') . SLICER_FILE_MODEL;
		if (!file_exists($path_temp) || !copy($path_temp, $temp_folder . USERAUTH_VALUE_FILE_P_GCODE)) {
			$CI->load->helper('printerlog');
			PrinterLog_logError('prepare print gcode failed');
			
			return ERROR_GCODE_NOTFOUND;
		}
		
		// copy video
		if (!file_exists(ZIMAPI_FILEPATH_TIMELAPSE)
				|| !copy(ZIMAPI_FILEPATH_TIMELAPSE, $temp_folder . USERAUTH_VALUE_FILE_P_VIDEO)) {
			$CI->load->helper('printerlog');
			PrinterLog_logError('prepare print video failed');
			
			return ERROR_INTERNAL;
		}
		
		// prepare meta file
		$fp = fopen($temp_folder . USERAUTH_VALUE_FILE_P_INFO, 'w');
		if ($fp) {
			// write info json
			fwrite($fp, json_encode(array(
					USERAUTH_TITLE_JSON_P_NAME		=> $print_name,
					USERAUTH_TITLE_JSON_P_TIME		=> time(),
					USERAUTH_TITLE_JSON_P_LENG1		=> $array_length['r'],
					USERAUTH_TITLE_JSON_P_LENG2		=> $array_length['l'],
					USERAUTH_TITLE_JSON_P_MAT1		=> $array_material['r'],
					USERAUTH_TITLE_JSON_P_MAT2		=> $array_material['l'],
					USERAUTH_TITLE_JSON_P_PRESET	=> $preset_id,
					USERAUTH_TITLE_JSON_P_TEMPER1	=> $array_temper['r'],
					USERAUTH_TITLE_JSON_P_TEMPER2	=> $array_temper['l'],
					USERAUTH_TITLE_JSON_P_TEMPERB	=> $array_temper['b'],
			)));
			fclose($fp);
			
			// launch update script
			$output = array();
			$ret_val = 0;
			$command = $CI->config->item('siteutil') . USERAUTH_PRM_UTIL_UPLOAD_P
					. "\"$temp_folder\" '" . $_SESSION[USERAUTH_TITLE_TOKEN] . "' $model_id";
			
			exec($command, $output, $ret_val);
			$CI->load->helper('printerlog'); PrinterLog_logDebug('upload command: ' . $command); // test
			if ($ret_val != ERROR_NORMAL_RC_OK) {
				$CI->load->helper('printerlog');
				PrinterLog_logError('upload print command failed: ' . $command);
				
				return ERROR_INTERNAL;
			}
			
			return ERROR_OK;
		}
		else {
			$CI->load->helper('printerlog');
			PrinterLog_logError('model json prepare failed');
			
			return ERROR_INTERNAL;
		}
	}
	
	return ERROR_INTERNAL;
}

function UserAuth_prepareUserLibCacheFolder() {
	if (!file_exists(USERAUTH_VALUE_FOLDER_CACHE)){
		if (!mkdir(USERAUTH_VALUE_FOLDER_CACHE, 0777, TRUE)) {
			return FALSE;
		}
	}
	else if (!is_dir(USERAUTH_VALUE_FOLDER_CACHE)) {
		return FALSE;
	}
	
	return TRUE;
}

function UserAuth_getModelDownloadList($model_id, &$list_download) {
	$cr = 0;
	$CI = &get_instance();
	
	if ($model_id) {
		$model_prints = array();
		$model_info = array();
			
		$cr = UserAuth_getUserModelDetail($model_id, FALSE, TRUE, $model_prints, $model_info);
		if ($cr == ERROR_OK) {
			if ($model_info[USERAUTH_TITLE_USERLIB_STATE] != USERAUTH_VALUE_UL_M_READY) {
				$cr = ERROR_WRONG_PRM;
			}
			else {
				$flag_file = FALSE;
// 				$flag_download = FALSE;
				
				foreach (array(
						USERAUTH_TITLE_MODEL_TAG_PART1 => USERAUTH_TITLE_MODEL_PART1,
						USERAUTH_TITLE_MODEL_TAG_PART2 => USERAUTH_TITLE_MODEL_PART2,
				) as $check_key_tag => $check_key_url) {
					if (isset($model_info[$check_key_tag]) && isset($model_info[$check_key_url])) {
						$path_stl = USERAUTH_VALUE_FOLDER_CACHE . $model_info[$check_key_tag] . USERAUTH_VALUE_FILE_M_CACHE_S;
						
						$flag_file = TRUE;
						if (file_exists($path_stl)) {
							continue;
						}
// 						else if (file_exists($path_stl . USERAUTH_VALUE_SUFFIX_CACHE_T)) {
// 							$flag_download = TRUE; // but without file in list (download is launched generally)
// 						}
						else {
// 							$flag_download = TRUE;
// 							$list_download[$path_stl . USERAUTH_VALUE_SUFFIX_CACHE_T] = $model_info[$check_key_url];
							$list_download[$path_stl] = $model_info[$check_key_url];
						}
					}
				}
				
				if (!$flag_file) {
					$CI->load->helper('printerlog');
					PrinterLog_logError('no any file tag and url found for model download', __FILE__, __LINE__);
					
					$cr = ERROR_INTERNAL;
				}
// 				else if ($flag_download) {
// 					$cr = ERROR_TOOBIG_FILE;
// 				}
			}
		}
	}
	else {
		$cr = ERROR_MISS_PRM;
	}
	
	return $cr;
}

function UserAuth_importUserModel($model_id) {
	$cr = ERROR_MISS_PRM;
	$CI = &get_instance();
	
	if ($model_id) {
		$model_prints = array();
		$model_info = array();
			
		$cr = UserAuth_getUserModelDetail($model_id, FALSE, TRUE, $model_prints, $model_info);
		if ($cr == ERROR_OK) {
			if ($model_info[USERAUTH_TITLE_USERLIB_STATE] != USERAUTH_VALUE_UL_M_READY) {
				$cr = ERROR_WRONG_PRM;
			}
			else {
				$array_stl = array();
				foreach (array( USERAUTH_TITLE_MODEL_TAG_PART1, USERAUTH_TITLE_MODEL_TAG_PART2 )
						as $check_key_tag) {
					if (isset($model_info[$check_key_tag])) {
						$path_stl = USERAUTH_VALUE_FOLDER_CACHE
								. $model_info[$check_key_tag] . USERAUTH_VALUE_FILE_M_CACHE_S;
						
						if (!file_exists($path_stl)) {
							$cr = ERROR_EMPTY_PLATFORM;
							break;
						}
						else {
							$zip = new ZipArchive();
							
							if ($zip->open($path_stl) && ($zip->numFiles == 1)
									&& ($filename = $zip->getNameIndex(0))) {
								if ($zip->extractTo($CI->config->item('temp'), $filename)
										&& file_exists($CI->config->item('temp') . $filename)) {
									$array_stl[] = $CI->config->item('temp') . $filename;
									$zip->close();
								}
								else {
									$CI->load->helper('printerlog');
									PrinterLog_logError('extract model zip error', __FILE__, __LINE__);
									
									$cr = ERROR_INTERNAL;
									break;
								}
							}
							else {
								$cr = ERROR_UNKNOWN_MODEL;
								break;
							}
						}
					}
				}
				
				if ($cr == ERROR_OK) {
					if (count($array_stl) == 0) {
						$CI->load->helper('printerlog');
						PrinterLog_logError('no any file tag found for model import', __FILE__, __LINE__);
							
						$cr = ERROR_INTERNAL;
					}
					else {
						$CI->load->helper('slicer');
						
						$cr = Slicer_addModel($array_stl, FALSE, $model_id);
					}
				}
			}
		}
	}
	
	return $cr;
}

function UserAuth_getPrintDownloadList($model_id, $timestamp, &$list_download) {
	$cr = ERROR_MISS_PRM;
	$CI = &get_instance();
	
	if ($model_id && $timestamp) {
		$print_info = array();
			
		$cr = UserAuth_getUserPrint($model_id, $timestamp, $print_info);
		if ($cr == ERROR_OK) {
			if ($print_info[USERAUTH_TITLE_USERLIB_STATE] != USERAUTH_VALUE_UL_P_READY) {
				$cr = ERROR_WRONG_PRM;
			}
			else if (isset($print_info[USERAUTH_TITLE_PRINT_TAG_GCODE])
					&& isset($print_info[USERAUTH_TITLE_PRINT_GCODE])) {
				$path_print = USERAUTH_VALUE_FOLDER_CACHE . $print_info[USERAUTH_TITLE_PRINT_TAG_GCODE]
						. USERAUTH_VALUE_FILE_P_CACHE_S;
				
				if (!file_exists($path_print)) {
					$list_download[$path_print] = $print_info[USERAUTH_TITLE_PRINT_GCODE];
				}
			}
			else {
				$CI->load->helper('printerlog');
				PrinterLog_logError('no any file tag and url found for print download', __FILE__, __LINE__);
				
				$cr = ERROR_INTERNAL;
			}
		}
	}
	
	return $cr;
}
