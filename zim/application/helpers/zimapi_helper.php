<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

function Getnetwork() {
	/* zeepro-netconf */
}

function Getnetworkip() {
}

function ListSSID() {
	try {
//		$list_ssid = shell_exec ( 'sudo /usr/sbin/zeepro-list-ssid' );
 		$list_ssid = "\"ssidA\"\n\"ssidB\"\n\"ssidC\"";
		$ssid = preg_split('/[\s]+/', str_replace('"', '', $list_ssid));
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