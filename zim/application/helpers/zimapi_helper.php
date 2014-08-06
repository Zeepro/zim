<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

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
	define('ZIMAPI_CMD_SERIAL',			'ifconfig -a | grep eth0 | awk \'{print $5}\'');
	define('ZIMAPI_CMD_VERSION',		'zfw_printenv version`zfw_printenv last_good`');
	define('ZIMAPI_CMD_VERSION_REBOOT',	'zfw_printenv version`zfw_printenv update` || zfw_printenv version`zfw_printenv last_good`');
	define('ZIMAPI_CMD_SETHOSTNAME',	ZIMAPI_CMD_CONFIG_NET . '-n ');
	define('ZIMAPI_CMD_GETHOSTNAME',	'cat /etc/hostname');
	
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
	define('ZIMAPI_TITLE_VERSION',	'Version');
	
	define('ZIMAPI_VALUE_ETH',		'eth');
	define('ZIMAPI_VALUE_WIFI',		'wifi');
	define('ZIMAPI_VALUE_NETWORK',	'network');
	define('ZIMAPI_VALUE_P2P',		'p2p');
	define('ZIMAPI_MODE_CETH',		'cEth');
	
	define('ZIMAPI_FILENAME_CAMERA',	'Camera.json');
	define('ZIMAPI_FILENAME_SOFTWARE',	'Software.json');
	define('ZIMAPI_FILEPATH_CAPTURE',	'/var/www/tmp/capture.jpg');
	define('ZIMAPI_PRM_CAMERA_PRINTSTART',
			' -v quiet -r 25 -s 320x240 -f video4linux2 -i /dev/video0 -minrate 256k -maxrate 256k -bufsize 256k -map 0 -force_key_frames "expr:gte(t,n_forced*2)" -c:v libx264 -crf 35 -profile:v baseline -b:v 256k -pix_fmt yuv420p -flags -global_header -f segment -segment_list /var/www/tmp/zim.m3u8 -segment_time 1 -segment_format mpeg_ts -segment_list_type m3u8 -segment_list_flags live -segment_list_size 5 -segment_wrap 5 /var/www/tmp/zim%d.ts');
	define('ZIMAPI_PRM_CAMERA_STOP',	' stop ');
	define('ZIMAPI_PRM_CAMERA_CAPTURE',
			' -v quiet -f video4linux2 -i /dev/video0 -y -vframes 1 /var/www/tmp/capture.jpg');
// 	define('ZIMAPI_TITLE_MODE',			'mode');
	define('ZIMAPI_TITLE_COMMAND',		'command');
	define('ZIMAPI_VALUE_MODE_OFF',		'off');
	define('ZIMAPI_VALUE_MODE_HLS',		'hls');
	define('ZIMAPI_TITLE_PRESET',		'preset');
	define('ZIMAPI_TITLE_PRESET_ID',	'id');
	define('ZIMAPI_TITLE_PRESET_NAME',	'name');
	
	define('ZIMAPI_VALUE_DEFAULT_RHO',		600);
	define('ZIMAPI_VALUE_DEFAULT_DELTA',	45);
	define('ZIMAPI_VALUE_DEFAULT_THETA',	30);
	
	define('ZIMAPI_PRM_CAPTURE',	'picture');
	define('ZIMAPI_PRM_VIDEO_MODE',	'video');
	define('ZIMAPI_PRM_PRESET',		'slicerpreset');
	define('ZIMAPI_PRM_PASSWD',		'password');
	
	define('ZIMAPI_FILE_PRESET_JSON',	'preset.json');
	define('ZIMAPI_FILE_PRESET_INI',	'config.ini');
	define('ZIMAPI_FILE_SSO_NAME',		'SSOActivation.txt');
}

function ZimAPI_initialFile() {
	global $CFG;
	$setting_fullpath = $CFG->config['conf'] . ZIMAPI_FILENAME_SOFTWARE;
	
	if (file_exists($setting_fullpath)) {
		return TRUE;
	}
	else {
		// prepare data array
		$cr = 0;
		$array_preset = ZimAPI_getPresetListAsArray();
		
		$data_json = array(
				ZIMAPI_TITLE_VERSION	=> '1.0',
				ZIMAPI_TITLE_PASSWD		=> md5(''),
				ZIMAPI_TITLE_PRESET		=> NULL,
		);
		
		// write json file
		$fp = fopen($setting_fullpath, 'w');
		if ($fp) {
			fwrite($fp, json_encode($data_json));
			fclose($fp);
			chmod($fp, '0777');
		}
		else {
			return FALSE;
		}
		
		$cr = ZimAPI_setPreset($array_preset[0][ZIMAPI_TITLE_PRESET_ID]);
		if ($cr != ERROR_OK) {
			unlink($setting_fullpath);
			return FALSE;
		}
	}
	
	return TRUE;
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
			$nameWifi = ZimAPI__filterCharacter($nameWifi); //str_replace('"', '\"', $nameWifi);
			
			if (strlen($passWifi == 0)) {
				$command = ZIMAPI_CMD_SWIFI . ' ' . $nameWifi;
			}
			else {
				$passWifi = ZimAPI__filterCharacter($passWifi); //str_replace('"', '\"', $passWifi);
				
				// check password length
				if (strlen($passWifi) < 8 || strlen($passWifi) > 64) {
					return ERROR_WRONG_PRM;
				}
				
				// use WPA crypt as default
				$command = ZIMAPI_CMD_SWIFI . ' ' . $nameWifi . ' wpa ' . $passWifi;
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
			$nameWifi = ZimAPI__filterCharacter($nameWifi); //str_replace('"', '\"', $nameWifi);
			
			if (strlen($passWifi) == 0) {
				$command = ZIMAPI_CMD_CWIFI . ' ' . $nameWifi;
			}
			else {
				$passWifi = ZimAPI__filterCharacter($passWifi); //str_replace('"', '\"', $passWifi);
				
				// check password length
				if (strlen($passWifi) < 8 || strlen($passWifi) > 64) {
					return ERROR_WRONG_PRM;
				}
				
				// use WPA crypt as default
				$command = ZIMAPI_CMD_CWIFI . ' ' . $nameWifi . ' ' . $passWifi;
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
		
		if (strlen($ip . $mask . $gateWay) == 0) {
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
		} catch (Exception $e) {
			return ERROR_INTERNAL;
		}
			
		if ($ret_val != ERROR_NORMAL_RC_OK) {
			return ERROR_INTERNAL;
		}
		else {
			exec(ZIMAPI_CMD_RESTART_WEB);
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

function ZimAPI_setHostname($hostname) {
	// check characters
	if (preg_match('/^[A-Za-z0-9]+$/', $hostname)) {
		$ret_val = 0;
		$output = array();
		$command = ZIMAPI_CMD_SETHOSTNAME . $hostname;
		
		// do nothing for windows
		if (DectectOS_checkWindows()) {
			return ERROR_OK;
		}
		
		try {
			exec($command, $output, $ret_val);
			
			if ($ret_val != ERROR_NORMAL_RC_OK) {
				return ERROR_INTERNAL;
			}
			else {
				return ERROR_OK;
			}
		} catch (Exception $e) {
			return ERROR_INTERNAL;
		}
	}
	else {
		return ERROR_WRONG_PRM;
	}
	
	return ERROR_INTERNAL; // never reach here
}

function ZimAPI_getHostname(&$hostname) {
	$output = array();
	$ret_val = 0;
	$hostname = "zim";
	
	// do nothing for windows
	if (DectectOS_checkWindows()) {
		return ERROR_OK;
	}
	
	try {
		exec(ZIMAPI_CMD_GETHOSTNAME, $output, $ret_val);
			
		if ($ret_val != ERROR_NORMAL_RC_OK) {
			return ERROR_INTERNAL;
		}
		else {
			if (count($output) <= 0) {
				return ERROR_INTERNAL;
			}
			$hostname = $output[0];
			
			return ERROR_OK;
		}
	} catch (Exception $e) {
		return ERROR_INTERNAL;
	}
	
	return ERROR_INTERNAL; // never reach here
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

function ZimAPI_checkCamera(&$info_camera = '') {
	global $CFG;
	$camera_file = $CFG->config['temp'] . ZIMAPI_FILENAME_CAMERA;
	$tmp_array = array();
	
	$CI = &get_instance();
	$CI->load->helper('json');
	
	if (!file_exists($camera_file)) {
		$info_camera = ZIMAPI_VALUE_MODE_OFF;
		return TRUE;
	}
	
	// read json file
	try {
		$tmp_array = json_read($camera_file, TRUE);
		if ($tmp_array['error']) {
			throw new Exception('read json error');
		}
	} catch (Exception $e) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('read camera status file error', __FILE__, __LINE__);
		return FALSE;
	}
	$info_camera = $tmp_array['json'][ZIMAPI_TITLE_MODE];
	
	return TRUE;
}

function ZimAPI_checkCameraPassword($password) {
	$tmp_array = array();
	$CI = &get_instance();
	$json_fullpath = $CI->config->item('conf') . ZIMAPI_FILENAME_SOFTWARE;
	
	$CI->load->helper('json');
	try {
		$tmp_array = json_read($json_fullpath);
		if ($tmp_array['error']) {
			throw new Exception('read json error');
		}
	} catch (Exception $e) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('read software json error', __FILE__, __LINE__);
		return FALSE;
	}
	
	$md5_input = md5($password);
	$md5_system = $tmp_array['json'][ZIMAPI_TITLE_PASSWD];
	if ($md5_input != $md5_system) {
		$CI->load->helper('printerlog');
		PrinterLog_logMessage('input password is wrong', __FILE__, __LINE__);
		return FALSE;
	}
	
	return TRUE;
}

function ZimAPI_setCameraPassword($password = '') {
	$tmp_array = array();
	$data_json = array();
	$CI = &get_instance();
	$json_fullpath = $CI->config->item('conf') . ZIMAPI_FILENAME_SOFTWARE;
	
	$CI->load->helper('json');
	try {
		$tmp_array = json_read($json_fullpath);
		if ($tmp_array['error']) {
			throw new Exception('read json error');
		}
	} catch (Exception $e) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('read software json error', __FILE__, __LINE__);
		return FALSE;
	}
	
	$data_json = $tmp_array['json'];
	$data_json[ZIMAPI_TITLE_PASSWD] = md5($password);
	
	// write json file
	$fp = fopen($json_fullpath, 'w');
	if ($fp) {
		fwrite($fp, json_encode($data_json));
		fclose($fp);
	}
	else {
		$CI->load->helper('printerlog');
		PrinterLog_logError('write camera password error', __FILE__, __LINE__);
		return FALSE;
	}
	
	return TRUE;
}

function ZimAPI_cameraCapture(&$path_capture) {
	global $CFG;
	$output = NULL;
	$ret_val = 0;
	$info_camera = '';
	
	if (!ZimAPI_checkCamera($info_camera)) {
		return FALSE;
	}
	if ($info_camera != ZIMAPI_VALUE_MODE_OFF) {
		$CI = &get_instance();
		$CI->load->helper('printerlog');
		PrinterLog_logError('capture can not run when camera is on', __FILE__, __LINE__);
		return FALSE;
	}
	
	$command = $CFG->config['capture'] . ZIMAPI_PRM_CAMERA_CAPTURE;
		
	exec($command, $output, $ret_val);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		$CI = &get_instance();
		$CI->load->helper('printerlog');
		PrinterLog_logError('capture camera command error', __FILE__, __LINE__);
		return FALSE;
	}
	
	$CI = &get_instance();
	$CI->load->helper('detectos');
	if (DectectOS_checkWindows()) {
		$path_capture = $CFG->config['bin'] . 'capture.jpg';
	}
	else {
		$path_capture = ZIMAPI_FILEPATH_CAPTURE;
	}
	
	return TRUE;
}

function ZimAPI_cameraOn($parameter) {
	global $CFG;
	$output = NULL;
	$ret_val = 0;
	$mode_current = '';
	$data_json = array();
	$fp = 0;
	
	$command = $CFG->config['camera'] . $parameter;
	$status_file = $CFG->config['temp'] . ZIMAPI_FILENAME_CAMERA;
	$mode_request = ZimAPI__getModebyParameter($parameter);
	
	$ret_val = ZimAPI_checkCamera($mode_current);
	if ($ret_val == FALSE) {
		return $ret_val;
	}
	if ($mode_current != ZIMAPI_VALUE_MODE_OFF) {
		$CI = &get_instance();
		$CI->load->helper('printerlog');
		
		if ($mode_request != $mode_current) {
			PrinterLog_logError('camera already open with another mode, ' . $mode_current, __FILE__, __LINE__);
			return FALSE;
		}
		PrinterLog_logMessage('camera already open', __FILE__, __LINE__);
		return TRUE;
	}
	
	exec($command, $output, $ret_val);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		$CI = &get_instance();
		$CI->load->helper('printerlog');
		PrinterLog_logError('camera start command error', __FILE__, __LINE__);
		return FALSE;
	}
	$data_json = array(
			ZIMAPI_TITLE_MODE		=> $mode_request,
			ZIMAPI_TITLE_COMMAND	=> $parameter,
	);
	
	// write json file
	$fp = fopen($CFG->config['temp'] . ZIMAPI_FILENAME_CAMERA, 'w');
	if ($fp) {
		fwrite($fp, json_encode($data_json));
		fclose($fp);
	}
	else {
		$CI = &get_instance();
		$CI->load->helper('printerlog');
		PrinterLog_logError('write camera status error', __FILE__, __LINE__);
		return FALSE;
	}
	
	return TRUE;
}

function ZimAPI_cameraOff() {
	global $CFG;
	$output = NULL;
	$ret_val = 0;
	$command = $CFG->config['camera'] . ZIMAPI_PRM_CAMERA_STOP;
	$data_json = array();
	$fp = 0;
	$mode_current = '';
	
	if (!ZimAPI_checkCamera($mode_current)) {
		return FALSE;
	}
	if ($mode_current == ZIMAPI_VALUE_MODE_OFF) {
		return TRUE;
	}
	exec($command, $output, $ret_val);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		$CI = &get_instance();
		$CI->load->helper('printerlog');
		PrinterLog_logError('camera stop command error', __FILE__, __LINE__);
		return FALSE;
	}
	
	$data_json = array(
			ZIMAPI_TITLE_MODE		=> ZIMAPI_VALUE_MODE_OFF,
			ZIMAPI_TITLE_COMMAND	=> NULL,
	);
	
	// write json file
	$fp = fopen($CFG->config['temp'] . ZIMAPI_FILENAME_CAMERA, 'w');
	if ($fp) {
		fwrite($fp, json_encode($data_json));
		fclose($fp);
	}
	else {
		$CI = &get_instance();
		$CI->load->helper('printerlog');
		PrinterLog_logError('write camera status error', __FILE__, __LINE__);
		return FALSE;
	}
	
	return TRUE;
}

function ZimAPI_getPresetList() {
	$array_data = ZimAPI_getPresetListAsArray();
	$CI = &get_instance();
	
	$CI->load->helper('json');
	
	return json_encode_unicode($array_data);
}

function ZimAPI_getPresetListAsArray() {
	$json_data = array();
	$tmp_array = NULL;
	
	$CI = &get_instance();
	$CI->load->helper(array('file', 'directory', 'json'));
	foreach (array(
					$CI->config->item('systempreset'), $CI->config->item('presetlist')
			) as $presetlist_basepath) {
		$preset_array = directory_map($presetlist_basepath, 1);
		
		foreach ($preset_array as $preset_id) {
			$preset_path = $presetlist_basepath . $preset_id . '/';
			
			try {
				$tmp_array = json_read($preset_path . ZIMAPI_FILE_PRESET_JSON, TRUE);
				if ($tmp_array['error']) {
					throw new Exception('read json error');
				}
			} catch (Exception $e) {
				// log internal error
				$CI = &get_instance();
				$CI->load->helper('printerlog');
				PrinterLog_logError('catch exception when getting preset json ' . $preset_id, __FILE__, __LINE__);
				continue; // just jump through the wrong data file
			}
			
			$json_data[] = $tmp_array['json']; //asign final data
		}
	}
	
	return $json_data;
}

function ZimAPI_getPreset(&$id_preset) {
	$tmp_array = array();
	$CI = &get_instance();
	$json_fullpath = $CI->config->item('conf') . ZIMAPI_FILENAME_SOFTWARE;
	
	$CI->load->helper('json');
	try {
		$tmp_array = json_read($json_fullpath);
		if ($tmp_array['error']) {
			throw new Exception('read json error');
		}
	} catch (Exception $e) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('read software json error', __FILE__, __LINE__);
		return FALSE;
	}
	
	if (isset($tmp_array['json'][ZIMAPI_TITLE_PRESET])) {
		$id_preset = $tmp_array['json'][ZIMAPI_TITLE_PRESET];
		return TRUE;
	}
	else {
		$CI->load->helper('printerlog');
		PrinterLog_logError('no preset id setting error', __FILE__, __LINE__);
		return FALSE;
	}
}

function ZimAPI_setPreset($id_preset) {
	$tmp_array = array();
	$data_json = array();
// 	$cr = 0;
	$config_fullpath = '';
	$preset_basepath = '';
	$CI = &get_instance();
	$json_fullpath = $CI->config->item('conf') . ZIMAPI_FILENAME_SOFTWARE;
	
	if (!ZimAPI__checkPreset($id_preset, $preset_basepath)) {
		return ERROR_WRONG_PRM;
	}
	
	$CI->load->helper('json');
	try {
		$tmp_array = json_read($json_fullpath);
		if ($tmp_array['error']) {
			throw new Exception('read json error');
		}
	} catch (Exception $e) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('read software json error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	
	$data_json = $tmp_array['json'];
	$data_json[ZIMAPI_TITLE_PRESET] = $id_preset;
	
	$config_fullpath = $preset_basepath . $id_preset . '/' . ZIMAPI_FILE_PRESET_INI;
	if (file_exists($config_fullpath)) {
		$ret_val = copy($config_fullpath, $CI->config->item('conf') . ZIMAPI_FILE_PRESET_INI);
		if ($ret_val != TRUE) {
			$CI->load->helper('printerlog');
			PrinterLog_logError('copy preset file error', __FILE__, __LINE__);
			return ERROR_INTERNAL;
		}
	}
	else {
		$CI->load->helper('printerlog');
		PrinterLog_logError('not find preset file: ' . ZIMAPI_FILE_PRESET_INI, __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	
// 	$CI->load->helper('slicer');
// 	$cr = Slicer_reloadPreset();
// 	if ($cr != ERROR_OK) {
// 		return $cr;
// 	}
	
	// write json file
	$fp = fopen($json_fullpath, 'w');
	if ($fp) {
		fwrite($fp, json_encode($data_json));
		fclose($fp);
	}
	else {
		$CI->load->helper('printerlog');
		PrinterLog_logError('write preset id error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	
	return ERROR_OK;
}

function ZimAPI_deletePreset($id_preset) {
	$cr = 0;
	$ret_val = 0;
	$preset_basepath = NULL;
	$system_preset = NULL;
	
	$CI = &get_instance();
	$CI->load->helper('file');
	
	if ($id_preset) {
		$ret_val = ZimAPI__checkPreset($id_preset, $preset_basepath, $system_preset);
		if ($ret_val == TRUE) {
			if ($system_preset == TRUE) {
				$cr = ERROR_WRONG_PRM;
				$CI->load->helper('printerlog');
				PrinterLog_logMessage('try to delete system preset', __FILE__, __LINE__);
			}
			else {
				$preset_path = $preset_basepath . $id_preset;
				if (file_exists($preset_path)) {
					delete_files($preset_path, TRUE); //there are no folders inside normally, but we delete all
					rmdir($preset_path);
					$cr = ERROR_OK;
				}
				else {
					$cr = ERROR_INTERNAL;
					$CI->load->helper('printerlog');
					PrinterLog_logError('can not find preset filepath', __FILE__, __LINE__);
				}
			}
		}
		else {
			$cr = ERROR_WRONG_PRM;
			$CI->load->helper('printerlog');
			PrinterLog_logError('can not find preset by id', __FILE__, __LINE__);
		}
	}
	else {
		$cr = ERROR_MISS_PRM;
		$CI->load->helper('printerlog');
		PrinterLog_logError('miss preset id', __FILE__, __LINE__);
	}
	
	return $cr;
}

function ZimAPI_getSerial() {
	$address_mac = NULL;
	
	$CI = &get_instance();
	$CI->load->helper('detectos');
	
	if ($CI->config->item('simulator') && DectectOS_checkWindows()) {
		$address_mac = '00:00:00:00:00:00';
	}
	else {
		try {
			$address_mac = trim(shell_exec(ZIMAPI_CMD_SERIAL));
		} catch (Exception $e) {
			$address_mac = 'ff:ff:ff:ff:ff:ff';
		}
	}
	$address_mac = str_replace(':', '', $address_mac);
	
	return $address_mac;
}

function ZimAPI_getVersion($next_boot = FALSE) {
	$version = NULL;
	
	$CI = &get_instance();
	$CI->load->helper('detectos');
	
	if (DectectOS_checkWindows()) {
		$version = trim(@file_get_contents($CI->config->item('version_file')));
	}
	else if ($next_boot == TRUE) {
		$version = trim(shell_exec(ZIMAPI_CMD_VERSION_REBOOT));
	}
	else {
		$version = trim(shell_exec(ZIMAPI_CMD_VERSION));
	}
	
	return $version;
}

function ZimAPI_getType() {
	global $CFG;
	
	return trim(@file_get_contents($CFG->config['type_file']));
}

function ZimAPI_getPrinterSSOName(&$value) {
	$CI = &get_instance();
	$filename = $CI->config->item('conf') . ZIMAPI_FILE_SSO_NAME;
	
	$value = NULL;
	if (file_exists($filename)) {
		try {
			$value = @file_get_contents($filename);
		}
		catch (Exception $e) {
			$CI->load->helper('printerlog');
			PrinterLog_logError('read printer sso name error', __FILE__, __LINE__);
			
			return ERROR_INTERNAL;
		}
	}
	
	return ERROR_OK;
}

function ZimAPI_setPrinterSSOName($value) {
	$CI = &get_instance();
	$filename = $CI->config->item('conf') . ZIMAPI_FILE_SSO_NAME;
	
	if ($value == NULL) {
		unlink($filename);
		
		//TODO disactivate the tromboning
	}
	else {
		try {
			$fp = fopen($filename, 'w');
			if ($fp) {
				fwrite($fp, $value);
				fclose($fp);
			}
			else {
				$CI->load->helper('printerlog');
				PrinterLog_logError('open sso name file error', __FILE__, __LINE__);
				
				return ERROR_INTERNAL;
			}
		} catch (Exception $e) {
			$CI->load->helper('printerlog');
			PrinterLog_logError('write printer sso name error', __FILE__, __LINE__);
			
			return ERROR_INTERNAL;
		}
	}
	
	return ERROR_OK;
}

function ZimAPI_getPresetInfoAsArray($preset_id, &$array_info, &$system_preset = NULL) {
	$presetlist_basepath = NULL;
	$tmp_array = NULL;
	$system_preset = FALSE;
	
	$CI = &get_instance();
	$CI->load->helper(array('file', 'json'));
	
	if (!ZimAPI__checkPreset($preset_id, $presetlist_basepath, $system_preset)) {
		return ERROR_WRONG_PRM;
	}
// 	$presetlist_basepath = $CI->config->item('presetlist');
	
	try {
		$preset_path = $presetlist_basepath . $preset_id . '/';
		$tmp_array = json_read($preset_path . ZIMAPI_FILE_PRESET_JSON, TRUE);
		if ($tmp_array['error']) {
			throw new Exception('read json error');
		}
	} catch (Exception $e) {
		// log internal error
		$CI->load->helper('printerlog');
		PrinterLog_logError('catch exception when getting preset json ' . $preset_id, __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	
	$array_info = $tmp_array['json']; //asign final data
	
	return ERROR_OK;
}

function ZimAPI_getPresetSettingAsArray($id_preset, &$array_setting) {
	//TODO finish me, the following function is only for test
	$preset_basepath = '';
	
	// check if preset exists
	if (!ZimAPI__checkPreset($id_preset, $preset_basepath)) {
		return ERROR_WRONG_PRM;
	}
	$array_setting = @parse_ini_file($preset_basepath . $id_preset . '/' . ZIMAPI_FILE_PRESET_INI);
	
	if ($array_setting == FALSE) {
		return ERROR_INTERNAL; // read ini file error
	}
	if (!ZimAPI_checkPresetSetting($array_setting, FALSE)) {
		return ERROR_INTERNAL; // internal settings file error
	}
	
	return ERROR_OK;
}

function ZimAPI_setPresetSetting($id_preset, $array_input, $name_preset = NULL) {
	// $name_preset is NULL when creating preset from an old id
	$ret_val = 0;
	$array_setting = array();
	$preset_path = NULL;
	$CI = &get_instance();
	
	if (!is_array($array_input)) {
		return ERROR_INTERNAL;
	}
	
	// check if we have same name, and define preset path
	if ($name_preset != NULL) {
		$ret_val = ZimAPI__checkPreset(ZimAPI__codePresetHash($name_preset));
		if ($ret_val == TRUE) {
			$CI->load->helper('printerlog');
			PrinterLog_logMessage('system has already the same preset name: ' . $name_preset);
			return ERROR_FULL_PRTLST; // just use another error code
		}
		
		$preset_path = $CI->config->item('presetlist') . ZimAPI__codePresetHash($name_preset) . '/';
	}
	else {
		$system_preset = FALSE;
		$ret_val = ZimAPI__checkPreset($id_preset, $preset_path, $system_preset);
		if ($ret_val == FALSE) {
			$CI->load->helper('printerlog');
			PrinterLog_logError('system can not find preset: ' . $id_preset);
			return ERROR_WRONG_PRM; // just use another error code
		}
		
		if ($system_preset == TRUE) {
			$CI->load->helper('printerlog');
			PrinterLog_logMessage('system can not modify default preset');
			return ERROR_WRONG_PRM;
		}
		
		$preset_path .= '/' . $id_preset . '/';
	}
	
	$ret_val = ZimAPI_checkPresetSetting($array_input);
	if ($ret_val != TRUE) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('user input preset setting has wrong parameter');
		return ERROR_WRONG_PRM;
	}
	
	$ret_val = ZimAPI_getPresetSettingAsArray($id_preset, $array_setting);
	if ($ret_val != ERROR_OK) {
		return $ret_val;
	}
	
	// assign new setting
	foreach ($array_input as $key => $value) {
		$array_setting[$key] = $value;
	}
	
	// save preset
	if (!file_exists($preset_path)) {
		mkdir($preset_path);
	}
	if ($name_preset != NULL) {
		$json_data = array(
				ZIMAPI_TITLE_PRESET_ID		=> ZimAPI__codePresetHash($name_preset),
				ZIMAPI_TITLE_PRESET_NAME	=> $name_preset,
		);
		
		$CI->load->helper('json');
		//write model json info
		try {
			$fp = fopen($preset_path . ZIMAPI_FILE_PRESET_JSON, 'w');
			if ($fp) {
				fwrite($fp, json_encode_unicode($json_data));
				fclose($fp);
			}
			else {
				return ERROR_INTERNAL;
			}
		} catch (Exception $e) {
			return ERROR_INTERNAL;
		}
	}
	//write config ini file
	try {
		$fp = fopen($preset_path . ZIMAPI_FILE_PRESET_INI, 'w');
		if ($fp) {
			foreach ($array_setting as $key => $value) {
				fwrite($fp, $key . " = " . $value . "\r\n");
			}
			fclose($fp);
		}
		else {
			return ERROR_INTERNAL;
		}
	} catch (Exception $e) {
		return ERROR_INTERNAL;
	}
	
	return ERROR_OK;
}

function ZimAPI_checkPresetSetting(&$array_setting, $input = TRUE) {
	// check no any extra settings for user input preset setting only
	if ($input == TRUE) {
		$array_check = array(
				'layer_height',
				'first_layer_height',
				'perimeters',
				'spiral_vase',
				'top_solid_layers',
				'bottom_solid_layers',
				'extra_perimeters',
				'avoid_crossing_perimeters',
				'start_perimeters_at_concave_points',
				'start_perimeters_at_non_overhang',
				'thin_walls',
				'overhangs',
				'randomize_start',
				'external_perimeters_first',
				'fill_density',
				'fill_pattern',
				'solid_fill_pattern',
				'infill_every_layers',
				'infill_only_where_needed',
				'solid_infill_every_layers',
				'fill_angle',
				'solid_infill_below_area',
				'only_retract_when_crossing_perimeters',
				'infill_first',
				'perimeter_speed',
				'small_perimeter_speed',
				'external_perimeter_speed',
				'infill_speed',
				'solid_infill_speed',
				'top_solid_infill_speed',
				'support_material_speed',
				'bridge_speed',
				'gap_fill_speed',
				'travel_speed',
				'first_layer_speed',
				'skirts',
				'skirt_distance',
				'skirt_height',
				'min_skirt_length',
				'brim_width',
				'support_material',
				'support_material_threshold',
				'support_material_enforce_layers',
				'raft_layers',
				'support_material_pattern',
				'support_material_spacing',
				'support_material_angle',
				'support_material_interface_layers',
				'support_material_interface_spacing',
				'perimeter_extruder',
				'infill_extruder',
				'support_material_extruder',
				'support_material_interface_extruder',
				'ooze_prevention',
				'standby_temperature_delta',
				'extrusion_width',
				'first_layer_extrusion_width',
				'perimeter_extrusion_width',
				'infill_extrusion_width',
				'solid_infill_extrusion_width',
				'top_infill_extrusion_width',
				'support_material_extrusion_width',
				'bridge_flow_ratio',
				'resolution',
		);
		foreach ($array_check as $value) {
			if (!array_key_exists($value, $array_setting)) {
				return FALSE;
				break; // never reach here
			}
		}
	}
	
	// check no any losing settings
	//TODO add value checking
	// layers and perimeters
	if (!array_key_exists('layer_height', $array_setting)) {
		$array_setting['layer_height'] = 0.4;
	}
	if (!array_key_exists('first_layer_height', $array_setting)) {
		$array_setting['first_layer_height'] = 0.35;
	}
	if (!array_key_exists('perimeters', $array_setting)) {
		$array_setting['perimeters'] = 3;
	}
	if (!array_key_exists('spiral_vase', $array_setting)) {
		$array_setting['spiral_vase'] = 0;
	}
	if (!array_key_exists('top_solid_layers', $array_setting)) {
		$array_setting['top_solid_layers'] = 3;
	}
	if (!array_key_exists('bottom_solid_layers', $array_setting)) {
		$array_setting['bottom_solid_layers'] = 3;
	}
	if (!array_key_exists('extra_perimeters', $array_setting)) {
		$array_setting['extra_perimeters'] = 1;
	}
	if (!array_key_exists('avoid_crossing_perimeters', $array_setting)) {
		$array_setting['avoid_crossing_perimeters'] = 0;
	}
	if (!array_key_exists('start_perimeters_at_concave_points', $array_setting)) {
		$array_setting['start_perimeters_at_concave_points'] = 0;
	}
	if (!array_key_exists('start_perimeters_at_non_overhang', $array_setting)) {
		$array_setting['start_perimeters_at_non_overhang'] = 0;
	}
	if (!array_key_exists('thin_walls', $array_setting)) {
		$array_setting['thin_walls'] = 1;
	}
	if (!array_key_exists('overhangs', $array_setting)) {
		$array_setting['overhangs'] = 1;
	}
	if (!array_key_exists('randomize_start', $array_setting)) {
		$array_setting['randomize_start'] = 0;
	}
	if (!array_key_exists('external_perimeters_first', $array_setting)) {
		$array_setting['external_perimeters_first'] = 0;
	}
	// infill
	if (!array_key_exists('fill_density', $array_setting)) {
		$array_setting['fill_density'] = 0.4;
	}
	if (!array_key_exists('fill_pattern', $array_setting)) {
		$array_setting['fill_pattern'] = 'honeycomb';
	}
	if (!array_key_exists('solid_fill_pattern', $array_setting)) {
		$array_setting['solid_fill_pattern'] = 'rectilinear';
	}
	if (!array_key_exists('infill_every_layers', $array_setting)) {
		$array_setting['infill_every_layers'] = 1;
	}
	if (!array_key_exists('infill_only_where_needed', $array_setting)) {
		$array_setting['infill_only_where_needed'] = 0;
	}
	if (!array_key_exists('solid_infill_every_layers', $array_setting)) {
		$array_setting['solid_infill_every_layers'] = 0;
	}
	if (!array_key_exists('fill_angle', $array_setting)) {
		$array_setting['fill_angle'] = 45;
	}
	if (!array_key_exists('solid_infill_below_area', $array_setting)) {
		$array_setting['solid_infill_below_area'] = 70;
	}
	if (!array_key_exists('only_retract_when_crossing_perimeters', $array_setting)) {
		$array_setting['only_retract_when_crossing_perimeters'] = 1;
	}
	if (!array_key_exists('infill_first', $array_setting)) {
		$array_setting['infill_first'] = 0;
	}
	// speed
	if (!array_key_exists('perimeter_speed', $array_setting)) {
		$array_setting['perimeter_speed'] = 30;
	}
	if (!array_key_exists('small_perimeter_speed', $array_setting)) {
		$array_setting['small_perimeter_speed'] = 30;
	}
	else
	{
		$tmp = $array_setting['small_perimeter_speed'];
		$pos = strpos($tmp, "%");
		if (($pos !== FALSE && (substr($tmp, 0, $pos) < 20 || substr($tmp, 0, $pos) > 100))
				|| ($pos === FALSE && ($tmp < 10 || $tmp > 200)))
		{
			return (FALSE);
		}
	}
	if (!array_key_exists('external_perimeter_speed', $array_setting)) {
		$array_setting['external_perimeter_speed'] = '70%';
	}
	else
	{
		$tmp = $array_setting['external_perimeter_speed'];
		$pos = strpos($tmp, "%");
		if (($pos !== FALSE && (substr($tmp, 0, $pos) < 20 || substr($tmp, 0, $pos) > 100))
				|| ($pos === FALSE && ($tmp < 10 || $tmp > 200)))
		{
			return (FALSE);
		}
	}
	if (!array_key_exists('infill_speed', $array_setting)) {
		$array_setting['infill_speed'] = 60;
	}
	if (!array_key_exists('solid_infill_speed', $array_setting)) {
		$array_setting['solid_infill_speed'] = 60;
	}
	else
	{
		$tmp = $array_setting['solid_infill_speed'];
		$pos = strpos($tmp, "%");
		if (($pos !== FALSE && (substr($tmp, 0, $pos) < 20 || substr($tmp, 0, $pos) > 100))
				|| ($pos === FALSE && ($tmp < 10 || $tmp > 200)))
		{
			return (FALSE);
		}
	}
	if (!array_key_exists('top_solid_infill_speed', $array_setting)) {
		$array_setting['top_solid_infill_speed'] = 50;
	}
	else
	{
		$tmp = $array_setting['top_solid_infill_speed'];
		$pos = strpos($tmp, "%");
		if (($pos !== FALSE && (substr($tmp, 0, $pos) < 20 || substr($tmp, 0, $pos) > 100))
				|| ($pos === FALSE && ($tmp < 10 || $tmp > 200)))
		{
			return (FALSE);
		}
	}
	if (!array_key_exists('support_material_speed', $array_setting)) {
		$array_setting['support_material_speed'] = 60;
	}
	if (!array_key_exists('bridge_speed', $array_setting)) {
		$array_setting['bridge_speed'] = 60;
	}
	if (!array_key_exists('gap_fill_speed', $array_setting)) {
		$array_setting['gap_fill_speed'] = 20;
	}
	if (!array_key_exists('travel_speed', $array_setting)) {
		$array_setting['travel_speed'] = 130;
	}
	if (!array_key_exists('first_layer_speed', $array_setting)) {
		$array_setting['first_layer_speed'] = '30%';
	}
	else
	{
		$tmp = $array_setting['first_layer_speed'];
		$pos = strpos($tmp, "%");
		if (($pos !== FALSE && (substr($tmp, 0, $pos) < 20 || substr($tmp, 0, $pos) > 100))
				|| ($pos === FALSE && ($tmp < 10 || $tmp > 200)))
		{
			return (FALSE);
		}
	}
	// skirt and brim
	if (!array_key_exists('skirts', $array_setting)) {
		$array_setting['skirts'] = 1;
	}
	if (!array_key_exists('skirt_distance', $array_setting)) {
		$array_setting['skirt_distance'] = 6;
	}
	if (!array_key_exists('skirt_height', $array_setting)) {
		$array_setting['skirt_height'] = 1;
	}
	if (!array_key_exists('min_skirt_length', $array_setting)) {
		$array_setting['min_skirt_length'] = 0;
	}
	if (!array_key_exists('brim_width', $array_setting)) {
		$array_setting['brim_width'] = 0;
	}
	// support material
	if (!array_key_exists('support_material', $array_setting)) {
		$array_setting['support_material'] = 0;
	}
	if (!array_key_exists('material_threshold', $array_setting)) {
		$array_setting['material_threshold'] = 0;
	}
	if (!array_key_exists('support_material_enforce_layers', $array_setting)) {
		$array_setting['support_material_enforce_layers'] = 0;
	}
	if (!array_key_exists('raft_layers', $array_setting)) {
		$array_setting['raft_layers'] = 0;
	}
	if (!array_key_exists('support_material_pattern', $array_setting)) {
		$array_setting['support_material_pattern'] = 'honeycomb';
	}
	if (!array_key_exists('support_material_spacing', $array_setting)) {
		$array_setting['support_material_spacing'] = 2.5;
	}
	if (!array_key_exists('support_material_angle', $array_setting)) {
		$array_setting['support_material_angle'] = 0;
	}
	if (!array_key_exists('support_material_interface_layers', $array_setting)) {
		$array_setting['support_material_interface_layers'] = 3;
	}
	if (!array_key_exists('support_material_interface_spacing', $array_setting)) {
		$array_setting['support_material_interface_spacing'] = 0;
	}
	// multiple extruders
	if (!array_key_exists('perimeter_extruder', $array_setting)) {
		$array_setting['perimeter_extruder'] = 1;
	}
	if (!array_key_exists('infill_extruder', $array_setting)) {
		$array_setting['infill_extruder'] = 1;
	}
	if (!array_key_exists('support_material_extruder', $array_setting)) {
		$array_setting['support_material_extruder'] = 1;
	}
	if (!array_key_exists('support_material_interface_extruder', $array_setting)) {
		$array_setting['support_material_interface_extruder'] = 1;
	}
	if (!array_key_exists('ooze_prevention', $array_setting)) {
		$array_setting['ooze_prevention'] = 0;
	}
	if (!array_key_exists('standby_temperature_delta', $array_setting)) {
		$array_setting['standby_temperature_delta'] = -5;
	}
	// advanced
	if (!array_key_exists('extrusion_width', $array_setting)) {
		$array_setting['extrusion_width'] = 0;
	}
	else
	{
		$tmp = $array_setting['extrusion_width'];
		$pos = strpos($tmp, "%");
		if (($pos !== FALSE && (substr($tmp, 0, $pos) < 50 || substr($tmp, 0, $pos) > 150))
				|| ($pos === FALSE && ($tmp != 0 && ($tmp < 0.25 || $tmp > 0.5))))
		{
			return (FALSE);
		}
	}
	if (!array_key_exists('first_layer_extrusion_width', $array_setting)) {
		$array_setting['first_layer_extrusion_width'] = '200%';
	}
	else
	{
		$tmp = $array_setting['first_layer_extrusion_width'];
		$pos = strpos($tmp, "%");
		if (($pos !== FALSE && (substr($tmp, 0, $pos) < 50 || substr($tmp, 0, $pos) > 150))
				|| ($pos === FALSE && ($tmp != 0 && ($tmp < 0.25 || $tmp > 0.5))))
		{
			return (FALSE);
		}
	}
	if (!array_key_exists('perimeter_extrusion_width', $array_setting)) {
		$array_setting['perimeter_extrusion_width'] = 0;
	}
	else
	{
		$tmp = $array_setting['perimeter_extrusion_width'];
		$pos = strpos($tmp, "%");
		if (($pos !== FALSE && (substr($tmp, 0, $pos) < 50 || substr($tmp, 0, $pos) > 150))
				|| ($pos === FALSE && ($tmp != 0 && ($tmp < 0.25 || $tmp > 0.5))))
		{
			return (FALSE);
		}
	}
	if (!array_key_exists('infill_extrusion_width', $array_setting)) {
		$array_setting['infill_extrusion_width'] = 0;
	}
	else
	{
		$tmp = $array_setting['infill_extrusion_width'];
		$pos = strpos($tmp, "%");
		if (($pos !== FALSE && (substr($tmp, 0, $pos) < 50 || substr($tmp, 0, $pos) > 150))
				|| ($pos === FALSE && ($tmp != 0 && ($tmp < 0.25 || $tmp > 0.5))))
		{
			return (FALSE);
		}
	}
	if (!array_key_exists('solid_infill_extrusion_width', $array_setting)) {
		$array_setting['solid_infill_extrusion_width'] = 0;
	}
	else
	{
		$tmp = $array_setting['solid_infill_extrusion_width'];
		$pos = strpos($tmp, "%");
		if (($pos !== FALSE && (substr($tmp, 0, $pos) < 50 || substr($tmp, 0, $pos) > 150))
				|| ($pos === FALSE && ($tmp != 0 && ($tmp < 0.25 || $tmp > 0.5))))
		{
			return (FALSE);
		}
	}
	if (!array_key_exists('top_infill_extrusion_width', $array_setting)) {
		$array_setting['top_infill_extrusion_width'] = 0;
	}
	else
	{
		$tmp = $array_setting['top_infill_extrusion_width'];
		$pos = strpos($tmp, "%");
		if (($pos !== FALSE && (substr($tmp, 0, $pos) < 50 || substr($tmp, 0, $pos) > 150))
				|| ($pos === FALSE && ($tmp != 0 && ($tmp < 0.25 || $tmp > 0.5))))
		{
			return (FALSE);
		}
	}
	if (!array_key_exists('support_material_extrusion_width', $array_setting)) {
		$array_setting['support_material_extrusion_width'] = 0;
	}
	else
	{
		$tmp = $array_setting['support_material_extrusion_width'];
		$pos = strpos($tmp, "%");
		if (($pos !== FALSE && (substr($tmp, 0, $pos) < 50 || substr($tmp, 0, $pos) > 150))
				|| ($pos === FALSE && ($tmp != 0 && ($tmp < 0.25 || $tmp > 0.5))))
		{
			return (FALSE);
		}
	}
	if (!array_key_exists('bridge_flow_ratio', $array_setting)) {
		$array_setting['bridge_flow_ratio'] = 1;
	}
	if (!array_key_exists('resolution', $array_setting)) {
		$array_setting['resolution'] = 0;
	}
	
	return TRUE;
}

//internal function
function ZimAPI__getModebyParameter($parameter) {
	switch ($parameter) {
		case ZIMAPI_PRM_CAMERA_PRINTSTART:
			return ZIMAPI_VALUE_MODE_HLS;
			break;
			
		default:
			return 'on'; //TODO edit here
			break;
	}
	
	return ZIMAPI_VALUE_MODE_OFF; // never reach here
}

function ZimAPI__checkPreset($id_preset, &$preset_basepath = NULL, &$system_preset = NULL) {
	$CI = &get_instance();
	$CI->load->helper('directory');
	
	foreach (array(
					$CI->config->item('systempreset'), $CI->config->item('presetlist')
			) as $presetlist_basepath) {
		$preset_array = directory_map($presetlist_basepath, 1);
		
		foreach ($preset_array as $check_id) {
			if ($check_id == $id_preset) {
				$preset_basepath = $presetlist_basepath;
				if ($CI->config->item('systempreset') == $preset_basepath) {
					$system_preset = TRUE;
				}
				return TRUE;
				break; // never reach here
			}
		}
	}
	
	return FALSE;
}

function ZimAPI__filterCharacter($raw) {
	$filtered = "'" . str_replace("'", "'\"'\"'", $raw) . "'";
	
	return $filtered;
}

function ZimAPI__codePresetHash($raw_name) {
	$CI = &get_instance();
	$CI->load->helper(array('detectos'));

	if (DectectOS_checkWindows()) {
		return md5(utf8_encode($raw_name));
	}
	else {
		return md5($raw_name);
	}
}
