<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Set_cookie extends MY_Controller
{
	public function index()
	{
		$this->load->helper('printerlog');
		PrinterLog_logDebug("In SetCookie/Index");		
		if ($this->input->server('REQUEST_METHOD') == 'POST')
		{
			echo '<script>document.cookie = "auth=' . $_POST['token'] . '"; console.log(document.cookie);</script>';
			
			header('Location: /');
		}
		else
		{
			PrinterLog_logError("SetCookie: method != POST");
			echo '<script>alert("get out")</script>';
		}
	}
}