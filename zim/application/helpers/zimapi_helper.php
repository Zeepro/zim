<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

if (!defined('ZIMAPI_CMD_LIST_SSID')) {
	define('ZIMAPI_CMD_LIST_SSID',		'sudo /usr/sbin/zeepro-list-ssid');
	define('ZIMAPI_CMD_CONFIG_NET',		'sudo /usr/sbin/zeepro-netconf ');
	define('ZIMAPI_CMD_SWIFI',			ZIMAPI_CMD_CONFIG_NET . 'sWifi');
	define('ZIMAPI_CMD_RESET_NETWORK',	ZIMAPI_CMD_CONFIG_NET . 'default');
	define('ZIMAPI_CMD_RESTART_WEB',	'sudo /etc/init.d/zeepro-network delayed-restart >>/var/log/network.log 2>&1 &');
}

function Getnetwork() {
	/* zeepro-netconf */
}

function Getnetworkip() {
}

function ZimAPI_listSSID() {
	try {
		$CI = &get_instance();
		$CI->load->helper(array('detectos'));
		
		// detect OS type, if windows, just do simulation
		if (DectectOS_checkWindows()) {
			$list_ssid = "\"freebox_zeepro\"\n\"livebox_zeepro\"\n\"bbox_zeepro\"\n\"freebox_zeepro\"\n\"\"\n";
		}
		else {
			$list_ssid = shell_exec(ZIMAPI_CMD_LIST_SSID);
		}
		$ssid = explode("\n", str_replace('"', '', $list_ssid));
		$ssid = array_unique(array_filter($ssid)); // remove empty and duplicate name
	} catch (Exception $e) {
		$ssid = array();
	}
	return $ssid;
}

function ZimAPI_setsWifi($nameWifi, $passWifi = '') {
	$CI = &get_instance();
	$CI->load->helper(array('detectos', 'errorcode', 'corestatus', 'printerlog'));
	
	if (!DectectOS_checkWindows()) {
		try {
			$command = '';
			$output = NULL;
			$ret_val = 0;
			
			//treat some special characters
			if (!ctype_print($nameWifi) || ($passWifi && !ctype_print($passWifi))) {
				return FALSE;
			}
			$nameWifi = str_replace('"', '\"', $nameWifi);
			$passWifi = str_replace('"', '\"', $passWifi);
			
			if (strlen($passWifi) == 0) {
				$command = ZIMAPI_CMD_SWIFI . ' "' . $nameWifi . '"';
			}
			else {
				// check password length
				if (strlen($passWifi) < 8 || strlen($passWifi) > 64) {
					return FALSE;
				}
				
				// use WPA crypt as default
				$command = ZIMAPI_CMD_SWIFI . ' "' . $nameWifi . '" wpa "' . $passWifi . '"';
			}
			exec($command, $output, $ret_val);
			if ($ret_val != ERROR_NORMAL_RC_OK) {
				return FALSE;
			}
			else {
// 				$retry_once = TRUE;
				
// 				do {
// 					exec(ZIMAPI_CMD_RESTART_WEB, $output, $ret_val);
// 				} while (($ret_val != ERROR_NORMAL_RC_OK)
// 						&& ($retry_once == TRUE) && ($retry_once = FALSE));
				// we can not get return value because of '&'
				exec(ZIMAPI_CMD_RESTART_WEB);
			}
		} catch (Exception $e) {
			return FALSE;
		}
	}
	
	$ret_val = CoreStatus_finishConnection(array('type'=>'sWifi', "name"=>$nameWifi, "passwd"=>$passWifi));
	if ($ret_val == FALSE) {
		PrinterLog_logError('finish connection in sWifi error');
		return FALSE;
	}
	
	return TRUE;
}

function Setnetwork() {
}

function ZimAPI_resetNetwork() {
	$CI = &get_instance();
	$CI->load->helper(array('detectos', 'errorcode', 'corestatus', 'printerlog'));
	
	if (!DectectOS_checkWindows()) {
		try {
			$command = '';
			$output = NULL;
			$ret_val = 0;
			
			exec(ZIMAPI_CMD_RESET_NETWORK, $output, $ret_val);
			if ($ret_val != ERROR_NORMAL_RC_OK) {
				return FALSE;
			}
			else {
// 				$retry_once = TRUE;
				
// 				do {
// 					exec(ZIMAPI_CMD_RESTART_WEB, $output, $ret_val);
// 				} while (($ret_val != ERROR_NORMAL_RC_OK)
// 						&& ($retry_once == TRUE) && ($retry_once = FALSE));
				// we can not get return value because of '&'
				exec(ZIMAPI_CMD_RESTART_WEB);
			}
		} catch (Exception $e) {
			return FALSE;
		}
	}
	
	$ret_val = CoreStatus_wantConnection();
	if ($ret_val == FALSE) {
		PrinterLog_logError('want connection in reset error');
		return FALSE;
	}
	
	return TRUE;
}


/* End of file Someclass.php */