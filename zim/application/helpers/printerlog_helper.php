<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

// call error list if we want
// $CI = &get_instance();
// $CI->load->helper(array (
// 		'errorcode',
// ));

// log for arduino part
function PrinterLog_logArduino($command, $output = '') {
	return PrinterLog__logToFile('log_arduino', $command, $output); 
}

// log for debug test
// debug 3 > message 2 > error 1 > none 0 (anything else)
function PrinterLog_logDebug($msg, $file = NULL, $line = NULL, $need_trim = TRUE) {
	global $CFG;
	if ($CFG->config['log_level'] >= 3) {
		$location = '';
		if (!is_null($file) && !is_null($line)) {
			$location = "\t(" . PrinterLog__filterAppPath($file) . ' ' . $line . ')';
		}
		
		return PrinterLog__logToDebugFile($CFG->config['log_file'], $msg, "DBG: ", $location, $need_trim);
	}
	else {
		return FALSE;
	}
}

function PrinterLog_logMessage($msg, $file = NULL, $line = NULL, $need_trim = TRUE) {
	global $CFG;
	if ($CFG->config['log_level'] >= 2) {
		$location = '';
		if (!is_null($file) && !is_null($line)) {
			$location = "\t(" . PrinterLog__filterAppPath($file) . ' ' . $line . ')';
		}
		
		return PrinterLog__logToDebugFile($CFG->config['log_file'], $msg, "MSG: ", $location, $need_trim);
	}
	else {
		return FALSE;
	}
}

function PrinterLog_logError($msg, $file = NULL, $line = NULL, $need_trim = TRUE) {
	global $CFG;
	if ($CFG->config['log_level'] >= 1) {
		$location = '';
		if (!is_null($file) && !is_null($line)) {
			$location = "\t(" . PrinterLog__filterAppPath($file) . ' ' . $line . ')';
		}
		
		return PrinterLog__logToDebugFile($CFG->config['log_file'], $msg, "ERR: ", $location, $need_trim);
	}
	else {
		return FALSE;
	}
}

function PrinterLog_logSSO($level, $code, $message) {
	$context = NULL;
	$data = array();
	$options = array();
	$CI = &get_instance();
	
	$CI->load->helper('zimapi');
	$data = array(
			'printersn'		=> ZimAPI_getSerial(),
			'printertime'	=> date("Y-m-d H:i:s\Z", time()),
			'level'			=> $level,
			'code'			=> $code,
			'message'		=> $message,
	);
	$options = array(
			'http' => array(
					'header'	=> "Content-type: application/x-www-form-urlencoded\r\n",
					'method'	=> 'POST',
					'content'	=> http_build_query($data),
			)
	);
	$context = stream_context_create($options);
	
	@file_get_contents('https://sso.zeepro.com/errorlog.ashx', false, $context);
	
	return;
}

function PrinterLog__logToFile($file_index, $command, $output = '') {
	global $CFG;
	if (is_array($output)) { // if several lines 
		$tmp_string = '';
		foreach ($output as $line) {
			$tmp_string .= $line . '; ';
		}
		$output = trim($tmp_string, " ;\t\n\r\0\x0B");
	}
	$msg = date("[Y-m-d\TH:i:s\Z]\t", time()) . $command . "\t[" . $output . "]\n";
	
	$fp = fopen($CFG->config[$file_index], 'a');
	if ($fp) {
		fwrite($fp, $msg);
		fclose($fp);
		return TRUE;
	}
	else {
		return FALSE;
	}
}

function PrinterLog__logToDebugFile($file, $msg, $prefix, $suffix, $need_trim) {
	if ($need_trim == TRUE) {
		$msg = trim($msg, " \t\n\r\0\x0B");
	}
// 	$msg = time() . $prefix . $msg . "\n";
	$msg = date("[Y-m-d\TH:i:s\Z]\t", time()) . $prefix . $msg . $suffix . "\n";
	$fp = fopen($file, 'a');
	if ($fp) {
		fwrite($fp, $msg);
		fclose($fp);
		return TRUE;
	}
	else {
		return FALSE;
	}
}

function PrinterLog__filterAppPath($filepath) {
	$return_path = str_replace(FCPATH, '', $filepath);
	return $return_path;
}
