<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

// call error list if we want
// $CI = &get_instance();
// $CI->load->helper(array (
// 		'errorcode',
// ));

// debug 3 > message 2 > error 1 > none 0 (anything else)

function PrinterLog_logDebug($msg, $need_trim = TRUE) {
	global $CFG;
	if ($CFG->config['log_level'] >= 3) {
		return PrinterLog__logToFile($CFG->config['log_file'], $msg, "\tDBG: ", $need_trim);
	}
	else {
		return FALSE;
	}
}

function PrinterLog_logMessage($msg, $need_trim = TRUE) {
	global $CFG;
	if ($CFG->config['log_level'] >= 2) {
		return PrinterLog__logToFile($CFG->config['log_file'], $msg, "\tMSG: ", $need_trim);
	}
	else {
		return FALSE;
	}
}

function PrinterLog_logError($msg, $need_trim = TRUE) {
	global $CFG;
	if ($CFG->config['log_level'] >= 1) {
		return PrinterLog__logToFile($CFG->config['log_file'], $msg, "\tERR: ", $need_trim);
	}
	else {
		return FALSE;
	}
}

function PrinterLog__logToFile($file, $msg, $prefix, $need_trim) {
	if ($need_trim == TRUE) {
		$msg = trim($msg, " \t\n\r\0\x0B");
	}
	$msg = time() . $prefix . $msg . "\n";
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
