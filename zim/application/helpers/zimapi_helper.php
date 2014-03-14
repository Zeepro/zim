<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

$CI = &get_instance();
$CI->load->helper(array('detectos', 'errorcode'));

if (!defined('ZIMAPI_CMD_LIST_SSID')) {
	define('ZIMAPI_CMD_LIST_SSID',		'sudo /usr/sbin/zeepro-list-ssid');
	define('ZIMAPI_CMD_CONFIG_NET',		'sudo /usr/sbin/zeepro-netconf ');
	define('ZIMAPI_CMD_SWIFI',			ZIMAPI_CMD_CONFIG_NET . 'sWifi');
	define('ZIMAPI_CMD_CWIFI',			ZIMAPI_CMD_CONFIG_NET . 'cWifi');
	define('ZIMAPI_CMD_PETH',			ZIMAPI_CMD_CONFIG_NET . 'pEth');
	define('ZIMAPI_CMD_CETH',			ZIMAPI_CMD_CONFIG_NET . 'cEth ');
	define('ZIMAPI_CMD_RESET_NETWORK',	ZIMAPI_CMD_CONFIG_NET . 'default');
	define('ZIMAPI_CMD_RESTART_WEB',	'sudo /etc/init.d/zeepro-network delayed-restart >> /var/log/network.log 2>&1 &');
	define('ZIMAPI_CMD_GATEWAY',		'route -n | awk \'$2 != "0.0.0.0" { print $2 }\' | sed -n 3p');
	define('ZIMAPI_CMD_DNS',			'grep nameserver /etc/resolv.conf | awk \'{print $2}\'');
	
	define('ZIMAPI_TITLE_TOPOLOGY',	'topology');
	define('ZIMAPI_TITLE_MEDIUM',	'medium');
	define('ZIMAPI_TITLE_SSID',		'ssid');
	define('ZIMAPI_TITLE_IP',		'ip');
	define('ZIMAPI_TITLE_GATEWAY',	'gateway');
	define('ZIMAPI_TITLE_DNS',		'dns');
	define('ZIMAPI_TITLE_MAC',		'mac');
	define('ZIMAPI_TITLE_MASK',		'mask');
	define('ZIMAPI_TITLE_MODE',		'mode');
	define('ZIMAPI_TITLE_CUSTOM',	'ipv4');
	define('ZIMAPI_TITLE_CUS_IP',	'user_assigned_address');
	define('ZIMAPI_TITLE_CUS_GW',	'user_assigned_gateway');
	define('ZIMAPI_TITLE_CUS_MK',	'user_assigned_mask');
	define('ZIMAPI_TITLE_PASSWD',	'password');
	
	define('ZIMAPI_VALUE_ETH',		'eth');
	define('ZIMAPI_VALUE_WIFI',		'wifi');
	define('ZIMAPI_VALUE_NETWORK',	'network');
	define('ZIMAPI_VALUE_P2P',		'p2p');
	define('ZIMAPI_MODE_CETH',		'cEth');
}

function ZimAPI_getNetworkInfoAsArray(&$array_data) {
	try {
		$command = '';
		$output = NULL;
		$ret_val = 0;
		$line_output_ip = 0;
		$array_temp = NULL;
		
		// detect OS type, if windows, just do simulation
		if (DectectOS_checkWindows()) {
			$ret_val = ERROR_NORMAL_RC_OK;
// 			$output = array(
// 					'MODE: pEth',
// 					'IP Config:',
// 					'addr:192.168.1.99  Bcast:192.168.1.255  Mask:255.255.255.0',
// 					'MAC: 0c:82:68:21:69:57',
// 			);
// 			$output = array(
// 					'MODE: cEth',
// 					'IP Config:',
// 					'addr:192.168.1.99  Bcast:192.168.1.255  Mask:255.255.255.0',
// 					'MAC: 0c:82:68:21:69:57',
// 			);
// 			$output = array(
// 					'MODE: sWifi',
// 					'SSID: zim_peng',
// 					'PASSWORD:',
// 					'IP Config:',
// 					'addr:10.0.0.1  Bcast:10.255.255.255  Mask:255.0.0.0',
// 					'MAC: 0c:82:68:21:69:57',
// 			);
			$output = array(
					'MODE: cWifi',
					'ACCESS POINT: ssid="freebox_zeepro"',
					'IP Config:',
					'addr:192.168.1.41  Bcast:192.168.1.255  Mask:255.255.255.0',
					'MAC: 0c:82:68:21:69:57',
			);
		}
		else {
			exec(ZIMAPI_CMD_CONFIG_NET, $output, $ret_val);
		}
		if ($ret_val != ERROR_NORMAL_RC_OK) {
			return ERROR_INTERNAL;
		}
		
		// get medium and potology
		if (strpos($output[0], 'MODE') === 0) {
			$array_data[ZIMAPI_TITLE_MODE] = substr($output[0], 6);
			if (strpos($output[0], 'Eth') !== FALSE) {
				$array_data[ZIMAPI_TITLE_MEDIUM] = ZIMAPI_VALUE_ETH;
				$array_data[ZIMAPI_TITLE_TOPOLOGY] = ZIMAPI_VALUE_NETWORK;
			}
			else if (strpos($output[0], 'Wifi') !== FALSE) {
				$array_data[ZIMAPI_TITLE_MEDIUM] = ZIMAPI_VALUE_WIFI;
				if (strpos($output[0], 'cWifi') !== FALSE) {
					$array_data[ZIMAPI_TITLE_TOPOLOGY] = ZIMAPI_VALUE_NETWORK;
				}
				else {
					$array_data[ZIMAPI_TITLE_TOPOLOGY] = ZIMAPI_VALUE_P2P;
				}
			}
			else {
				return ERROR_INTERNAL;
			}
		}
		
		// get ip, mask and mac
		if ($array_data[ZIMAPI_TITLE_MEDIUM] == ZIMAPI_VALUE_ETH) {
			$line_output_ip = 2;
		}
		else if ($array_data[ZIMAPI_TITLE_TOPOLOGY] == ZIMAPI_VALUE_NETWORK) {
			$line_output_ip = 3;
			$array_data[ZIMAPI_TITLE_SSID] = substr($output[1], strpos($output[1], 'ssid=') + 6, -1);
		}
		else { // mode sWifi
			$line_output_ip = 4;
			$array_data[ZIMAPI_TITLE_SSID] = substr($output[1], strpos($output[1], 'SSID:') + 6);
		}
		$array_temp = explode(' ', str_replace('  ', ' ', $output[$line_output_ip]));
		$array_data[ZIMAPI_TITLE_IP] = substr($array_temp[0], 5);
		$array_data[ZIMAPI_TITLE_MASK] = substr($array_temp[2], 5);
		$array_data[ZIMAPI_TITLE_MAC] = substr($output[$line_output_ip + 1], 5);
		
		// get gateway if not P2P
		if ($array_data[ZIMAPI_TITLE_TOPOLOGY] == ZIMAPI_VALUE_NETWORK) {
			$output = array();
			if (DectectOS_checkWindows()) {
				$ret_val = ERROR_NORMAL_RC_OK;
				$output = array('192.168.1.254');
			}
			else {
				exec(ZIMAPI_CMD_GATEWAY, $output, $ret_val);
			}
			if ($ret_val != ERROR_NORMAL_RC_OK) {
				return ERROR_INTERNAL;
			}
			$array_data[ZIMAPI_TITLE_GATEWAY] = $output[0];
		}
		
		// get DNS
		$output = array();
		if (DectectOS_checkWindows()) {
			$ret_val = ERROR_NORMAL_RC_OK;
			$output = array('8.8.8.8');
		}
		else {
			exec(ZIMAPI_CMD_DNS, $output, $ret_val);
		}
		if ($ret_val != ERROR_NORMAL_RC_OK) {
			return ERROR_INTERNAL;
		}
		$array_data[ZIMAPI_TITLE_DNS] = $output[0];
		
	} catch (Exception $e) {
		return ERROR_INTERNAL;
	}
	
	return ERROR_OK;
}

function ZimAPI_getNetworkIP(&$json_data) {
	$return_array = array();
	$array_data = array();
	$ret_val = ZimAPI_getNetworkInfoAsArray($array_data);
	
	if ($ret_val == ERROR_OK) {
		$return_array = array(
				ZIMAPI_TITLE_IP			=> $array_data[ZIMAPI_TITLE_IP],
				ZIMAPI_TITLE_GATEWAY	=> $array_data[ZIMAPI_TITLE_GATEWAY],
				ZIMAPI_TITLE_DNS		=> $array_data[ZIMAPI_TITLE_DNS],
				ZIMAPI_TITLE_MAC		=> $array_data[ZIMAPI_TITLE_MAC],
		);
		$json_data = json_encode($return_array);
	}
	
	return $ret_val;
}

function ZimAPI_getNetwork(&$json_data) {
	$return_array = array();
	$array_data = array();
	$ret_val = ZimAPI_getNetworkInfoAsArray($array_data);
	
	if ($ret_val == ERROR_OK) {
		$return_array = array(
					ZIMAPI_TITLE_TOPOLOGY	=> $array_data[ZIMAPI_TITLE_TOPOLOGY],
					ZIMAPI_TITLE_MEDIUM		=> $array_data[ZIMAPI_TITLE_MEDIUM],
		);
		
		if ($array_data[ZIMAPI_TITLE_MEDIUM] == ZIMAPI_VALUE_WIFI) {
			$return_array[ZIMAPI_TITLE_SSID] = $array_data[ZIMAPI_TITLE_SSID];
		}
		else if ($array_data[ZIMAPI_TITLE_MODE] == ZIMAPI_MODE_CETH) {
			$return_array[ZIMAPI_TITLE_CUSTOM] = array(
					ZIMAPI_TITLE_CUS_IP	=> $array_data[ZIMAPI_TITLE_IP],
					ZIMAPI_TITLE_CUS_GW	=> $array_data[ZIMAPI_TITLE_GATEWAY],
					ZIMAPI_TITLE_CUS_MK	=> $array_data[ZIMAPI_TITLE_MASK],
			);
		}
		
		$json_data = json_encode($return_array);
	}
	
	return $ret_val;
}

function ZimAPI_listSSID() {
	return json_encode(ZimAPI_listSSIDAsArray());
}

function ZimAPI_listSSIDAsArray() {
	try {
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
	$CI->load->helper(array('corestatus'));
	
	if (!DectectOS_checkWindows()) {
		try {
			$command = '';
			$output = NULL;
			$ret_val = 0;
			
			//treat some special characters
			if (!ctype_print($nameWifi) || ($passWifi && !ctype_print($passWifi))) {
				return ERROR_WRONG_PRM;
			}
			$nameWifi = str_replace('"', '\"', $nameWifi);
			$passWifi = str_replace('"', '\"', $passWifi);
			
			if (strlen($passWifi) == 0) {
				$command = ZIMAPI_CMD_SWIFI . ' "' . $nameWifi . '"';
			}
			else {
				// check password length
				if (strlen($passWifi) < 8 || strlen($passWifi) > 64) {
					return ERROR_WRONG_PRM;
				}
				
				// use WPA crypt as default
				$command = ZIMAPI_CMD_SWIFI . ' "' . $nameWifi . '" wpa "' . $passWifi . '"';
			}
			exec($command, $output, $ret_val);
			if ($ret_val != ERROR_NORMAL_RC_OK) {
				return ERROR_INTERNAL;
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
			return ERROR_INTERNAL;
		}
	}
	
	$ret_val = CoreStatus_finishConnection(array('type'=>'sWifi', "name"=>$nameWifi, "passwd"=>$passWifi));
	if ($ret_val == FALSE) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('finish connection in sWifi error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	
	return ERROR_OK;
}

function ZimAPI_setcWifi($nameWifi, $passWifi = '') {
	$CI = &get_instance();
	$CI->load->helper(array('corestatus', 'printerlog'));
	
	if (!DectectOS_checkWindows()) {
		try {
			$command = '';
			$output = NULL;
			$ret_val = 0;
			
			//treat some special characters
			if (!ctype_print($nameWifi) || ($passWifi && !ctype_print($passWifi))) {
				return ERROR_WRONG_PRM;
			}
			$nameWifi = str_replace('"', '\"', $nameWifi);
			$passWifi = str_replace('"', '\"', $passWifi);
			
			if (strlen($passWifi) == 0) {
				$command = ZIMAPI_CMD_CWIFI . ' "' . $nameWifi . '"';
			}
			else {
				// check password length
				if (strlen($passWifi) < 8 || strlen($passWifi) > 64) {
					return ERROR_WRONG_PRM;
				}
				
				// use WPA crypt as default
				$command = ZIMAPI_CMD_CWIFI . ' "' . $nameWifi . '" "' . $passWifi . '"';
			}
			exec($command, $output, $ret_val);
			if ($ret_val != ERROR_NORMAL_RC_OK) {
				return ERROR_INTERNAL;
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
			return ERROR_INTERNAL;
		}
	}
	
	$ret_val = CoreStatus_finishConnection(array('type'=>'cWifi', "name"=>$nameWifi, "passwd"=>$passWifi));
	if ($ret_val == FALSE) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('finish connection in sWifi error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	
	return ERROR_OK;
}

function ZimAPI_setcEth($ip = '', $mask = '', $gateWay = '') {
	$CI = &get_instance();
	$CI->load->helper(array('corestatus', 'printerlog'));
	
	if (!DectectOS_checkWindows()) {
		$command = '';
		$output = NULL;
		$ret_val = 0;
		
		if (is_null($ip . $mask . $gateWay)) {
			$command = ZIMAPI_CMD_PETH;
		}
		else if (filter_var($ip, FILTER_VALIDATE_IP)
				&& filter_var($mask, FILTER_VALIDATE_IP)
				&& filter_var($gateWay, FILTER_VALIDATE_IP)) {
			//TODO check mask work with gateway
			$command = ZIMAPI_CMD_CETH . $ip . ' ' . $mask . ' ' . $gateWay;
		}
		else {
			return ERROR_WRONG_PRM;
		}
		
		try {
			exec($command, $output, $ret_val);
			
			if ($ret_val != ERROR_NORMAL_RC_OK) {
				return ERROR_INTERNAL;
			}
			else {
				exec(ZIMAPI_CMD_RESTART_WEB);
			}
		} catch (Exception $e) {
			return ERROR_INTERNAL;
		}
	}
	
	$ret_val = CoreStatus_finishConnection(array('type'=>'Eth'));
	if ($ret_val == FALSE) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('finish connection in Eth error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	
	return ERROR_OK;
}

function ZimAPI_setpEth() {	
	return ZimAPI_setcEth();
}

function ZimAPI_setNetwork($string_json) {
	$array_config = json_decode($string_json);
	
	if ($array_config) {
		if (isset($array_config[ZIMAPI_TITLE_TOPOLOGY]) && isset($array_config[ZIMAPI_TITLE_MEDIUM])) {
			if ($array_config[ZIMAPI_TITLE_MEDIUM] == ZIMAPI_VALUE_WIFI) {
				if (!isset($array_config[ZIMAPI_TITLE_SSID]) || !isset($array_config[ZIMAPI_TITLE_PASSWD])) {
					return ERROR_MISS_PRM;
				}
				
				$ssid = $array_config[ZIMAPI_TITLE_SSID];
				$pwd = $array_config[ZIMAPI_TITLE_PASSWD];
				
				if ($array_config[ZIMAPI_TITLE_TOPOLOGY] == ZIMAPI_VALUE_NETWORK) {
					return ZimAPI_setcWifi($ssid, $pwd);
				}
				else if ($array_config[ZIMAPI_TITLE_TOPOLOGY] == ZIMAPI_VALUE_P2P) {
					return ZimAPI_setsWifi($ssid, $pwd);
				}
				else {
					return ERROR_WRONG_PRM;
				}
			}
			else if ($array_config[ZIMAPI_TITLE_MEDIUM] == ZIMAPI_VALUE_ETH
					&& $array_config[ZIMAPI_TITLE_TOPOLOGY] == ZIMAPI_VALUE_NETWORK) {
				if (isset($array_config[ZIMAPI_TITLE_CUS_IP])
						|| isset($array_config[ZIMAPI_TITLE_CUS_GW])
						|| isset($array_config[ZIMAPI_TITLE_CUS_MK])) {
					if (!isset($array_config[ZIMAPI_TITLE_CUS_IP])
							|| !isset($array_config[ZIMAPI_TITLE_CUS_GW])
							|| !isset($array_config[ZIMAPI_TITLE_CUS_MK])) {
						return ERROR_MISS_PRM;
					}
					$ip = $array_config[ZIMAPI_TITLE_CUS_IP];
					$gateway = $array_config[ZIMAPI_TITLE_CUS_GW];
					$mask = $array_config = $array_config[ZIMAPI_TITLE_CUS_MK];
					
					return ZimAPI_setcEth($ip, $mask, $gateway);
				}
				else {
					return ZimAPI_setpEth();
				}
			}
			else {
				return ERROR_WRONG_PRM;
			}
		}
		else {
			return ERROR_MISS_PRM;
		}
	}
	else {
		return ERROR_WRONG_PRM;
	}
}

function ZimAPI_resetNetwork() {
	$CI = &get_instance();
	$CI->load->helper(array('corestatus'));
	
	if (!DectectOS_checkWindows()) {
		try {
			$command = '';
			$output = NULL;
			$ret_val = 0;
			
			exec(ZIMAPI_CMD_RESET_NETWORK, $output, $ret_val);
			if ($ret_val != ERROR_NORMAL_RC_OK) {
				return ERROR_INTERNAL;
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
			return ERROR_INTERNAL;
		}
	}
	
	$ret_val = CoreStatus_wantConnection();
	if ($ret_val == FALSE) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('want connection in reset error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	
	return ERROR_OK;
}


/* End of file Someclass.php */