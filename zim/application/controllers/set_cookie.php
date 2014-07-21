<?php

class Set_cookie extends MY_Controller
{
	public function index()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST')
		{
			var_dump($_POST);
			echo '<script>document.cookie = "auth=' . $_POST['token'] . '"; console.log(document.cookie);</script>';
			
			header('Location: /');
		}
		else
			echo '<script>alert("get out")</script>';
	}
}