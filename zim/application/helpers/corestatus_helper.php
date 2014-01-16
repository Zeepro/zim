<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

if (!defined('CORESTATUS_FILENAME_WORK')) {
	define('CORESTATUS_FILENAME_WORK',		'Work.json');
	define('CORESTATUS_FILENAME_INIT',		'Boot.json');
	define('CORESTATUS_FILENAME_CONNECT',	'Connection.json');
	
	define('CORESTATUS_TITLE_VERSION',		'Version');
// 	define('CORESTATUS_TITLE_CMD',			'CommandLine');
	define('CORESTATUS_TITLE_STATUS',		'State');
// 	define('CORESTATUS_TITLE_CMD_CANCEL',	'Cancel');
// 	define('CORESTATUS_TITLE_CMD_PAUSE',	'PauseOrResume');
// 	define('CORESTATUS_TITLE_URL_REDIRECT',	'CallBackURL');
	define('CORESTATUS_TITLE_URL_REDIRECT',	'RedirectURL');
	define('CORESTATUS_TITLE_MESSAGE',		'Message');
	
	define('CORESTATUS_VALUE_IDLE',		'idle');
// 	define('CORESTATUS_VALUE_UPGRADE',	'upgrading');
}

function CoreStatus_checkInIdle($RedirectURL = '') {
	global $CFG;
	$state_file = $CFG->config['conf'] . CORESTATUS_FILENAME_WORK;
	$tmp_array = array();
	
	$CI = &get_instance();
	$CI->load->helper('json');
	
	// read json file
	try {
		$tmp_array = json_read($state_file);
		if ($tmp_array['error']) {
			throw new Exception('read json error');
		}
	} catch (Exception $e) {
		return FALSE; //TODO generate a way to return internal error
	}
	
	// check status
	if ($tmp_array['json'][CORESTATUS_TITLE_STATUS] == CORESTATUS_VALUE_IDLE) {
		$RedirectURL = $tmp_array['json'][CORESTATUS_TITLE_URL_REDIRECT];
		return TRUE;
	}
	
	return FALSE;
}

function CoreStatus_checkInInitialization() {
	global $CFG;
	$state_file = $CFG->config['conf'] . CORESTATUS_FILENAME_INIT;
	
	// we have the json file when in init
	if (file_exists($state_file)) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

function CoreStatus_checkInConnection() {
	global $CFG;
	$state_file = $CFG->config['conf'] . CORESTATUS_FILENAME_CONNECT;
	
	// we have the json file when having finished connection config
	if (file_exists($state_file)) {
		return FALSE;
	}
	else {
		return TRUE;
	}
}

function CoreStatus_checkCallREST() {
	$CI = &get_instance();
	if ($CI->router->class == 'rest') {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

function CoreStatus_checkCallInitialization() {
	$CI = &get_instance();
	if ($CI->router->class == 'initialization') {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

function CoreStatus_checkCallConnection() {
	$CI = &get_instance();
	if ($CI->router->class == 'connection') {
		return TRUE;
	}
	else {
		return FALSE;
	}
}
