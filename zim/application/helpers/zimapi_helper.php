<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

if (!defined('ZIMAPI_CMD_LIST_SSID')) {
	define('ZIMAPI_CMD_LIST_SSID',		'sudo /usr/sbin/zeepro-list-ssid');
	define('ZIMAPI_CMD_RESET_NETWORK',	'sudo /usr/sbin/zeepro-netconf sWifi "Zeepro Initialisation" >> /var/log/network.log 2>&1');
	define('ZIMAPI_CMD_RESTART_WEB',	'sudo /etc/init.d/zeepro-network delayed-restart >>/var/log/network.log 2>&1 &');
}

function Getnetwork() {
	/* zeepro-netconf */
}

function Getnetworkip() {
}

function ListSSID() {
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

function Setnetwork() {
}

function Resetnetwork() {
}


/* End of file Someclass.php */