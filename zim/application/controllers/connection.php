<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Connection extends MY_Controller {

	private function _generate_framePage($body_page) {
		$template_data = array();
		
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . t('ZeePro Personal Printer 21 - Connection configuration') . '</title>',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
		
		return;
	}
	
	private function ip_check($ip) {
		if (filter_var ( $ip, FILTER_VALIDATE_IP )) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	private function mask_check($mask) {
		if (! $m = ip2long ( $mask ))
			return false;
		
		$m = ~ $m;
		return $m && ~ $m && ! ($m & ($m + 1));
	}
	
	private function gateway_check($ip) {
	// @todo The gateway should be within the mask
		if (filter_var ( $ip, FILTER_VALIDATE_IP )) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	public function index() {
		$template_data = array();
		$body_page = NULL;
		
		$this->load->library('parser');
		
		$this->lang->load('connection/master', $this->config->item('language'));
		$this->lang->load('connection/index', $this->config->item('language'));
		
		// parse the main body
		$template_data = array(
				'title'			=> t('Connection configuration'),
				'hint'			=> t('Welcome...'),
				'wifissid'		=> htmlspecialchars(t('Option 1')),
				'wifip2p'		=> htmlspecialchars(t('Option 3')),
				'wired'			=> htmlspecialchars(t('Option 2')),
				'set_hostname'	=> t('set_hostname'),
		);
		
		$body_page = $this->parser->parse('template/connection/index', $template_data, TRUE);
		
		// parse all page
		$this->_generate_framePage($body_page);
		
		return;
	}
	
	public function wifissid() {
		$template_data = array();
		$list_ssid = array();
		$body_page = NULL;
		
		$this->load->helper(array(
				'form',
				'url',
				'zimapi'
		));
		
		$this->load->library(array('form_validation', 'parser'));
		
		$this->lang->load('connection/master', $this->config->item('language'));
		$this->lang->load('connection/wifissid', $this->config->item('language'));

		if ($this->form_validation->run() == FALSE) {
			foreach(ZimAPI_listSSIDAsArray() as $ssid) {
				$list_ssid[] = array(
						'name'	=> htmlspecialchars($ssid),
						'link'	=> htmlspecialchars(rawurlencode($ssid)),
				);
			}
			
			// parse the main body
			$template_data = array(
					'title'			=> t('WiFi network connected to the Internet'),
					'back'			=> t('Back'),
					'list_ssid'		=> $list_ssid,
					'no_visable'	=> htmlspecialchars(t("Not visible...")),
			);
			
			$body_page = $this->parser->parse('template/connection/wifissid', $template_data, TRUE);
			
			// parse all page
			$this->_generate_framePage($body_page);
		} else {
// 			header("Location:/connection/wifipswd");
			$this->output->set_header('Location: /connection/wifipswd');
		}
		
		return;
	}
	
	public function wifinotvisiblessid() {
		$template_data = array();
		$body_page = NULL;
		
		$this->lang->load('connection/master', $this->config->item('language'));
		$this->lang->load('connection/wifinotvisiblessid', $this->config->item('language'));
		
		$this->load->helper(array(
				'form',
				'url'
		));

		$this->load->library(array('form_validation', 'parser'));

		$this->form_validation->set_rules('ssid', 'SSID', 'required');
		$this->form_validation->set_message('required', t('required ssid'));

		if ($this->form_validation->run () == FALSE) {
			$this->template->load ( 'connectionmaster', 'connectionwifinotvisiblessid');
			
			// parse the main body
			$template_data = array(
					'title'		=> t('WiFi network connected to the Internet'),
					'back'		=> t('Back'),
					'error'		=> form_error('ssid'),
					'submit'	=> htmlspecialchars(t("OK")),
			);
			
			$body_page = $this->parser->parse('template/connection/wifinotvisiblessid', $template_data, TRUE);
			
			// parse all page
			$this->_generate_framePage($body_page);
		} else {
// 			header("Location:/connection/wifipswd?ssid=" . rawurlencode($this->input->post('ssid')));
			$this->output->set_header('Location: /connection/wifipswd?ssid=' . rawurlencode($this->input->post('ssid')));
		}
		
		return;
	}
	
	public function wifipswd() {
		$template_data = array();
		$body_page = NULL;
		
		$this->load->library('form_validation');
		$this->load->helper(array('zimapi'));
		
		$this->form_validation->set_rules('password', 'password', '');
		
		$this->lang->load('connection/master', $this->config->item('language'));
		$this->lang->load('connection/wifipswd', $this->config->item('language'));
		
// 		$this->template->set ( 'lang', $this->config->item('language_abbr') );
// 		$this->template->set ( 'header', "<title>" . t ( 'ZeePro Personal Printer 21 - Connection configuration' ) . "</title>" );
		
		//TODO finish me
		if ($this->form_validation->run() == FALSE) {
			$ssid = $this->input->get('ssid');
			
			$this->load->library('parser');
			$template_data = array(
					'title'		=> htmlspecialchars(t("network", $ssid)),
					'ssid'		=> $ssid,
					'label'		=> htmlspecialchars(t("network password")),
					'back'		=> t('Back'),
					'submit'	=> htmlspecialchars(t("OK")),
			);
// 			$this->template->load ('connectionmaster', 'connectionwifipswd', array('ssid' => $ssid));
			$body_page = $this->parser->parse('template/connection/wifipswd', $template_data, TRUE);
				
			// parse all page
			$this->_generate_framePage($body_page);
		} else {
			$ssid = $this->input->post('ssid');
			$passwd = $this->input->post('password');
			
			$ret_val = ZimAPI_setcWifi($ssid, $passwd);
			if ($ret_val != ERROR_OK) {
// 				$error = t('invalid data');
				$this->output->set_header("Location:/connection/wifissid");
				return;
			}
			else {
// 				$this->output->set_header("Location:/connection/confirmation");
				$this->confirmation();
				return; // end generating if successed
			}
// 			$arr = array (
// 					"Connection.Topology" => "Network",
// 					"Connection.Support" => "WiFi",
// 					"IP.addresses.V4" => array (
// 							array (
// 									"Address" => "0.0.0.0" 
// 							) 
// 					) 
// 			);
			
// 			$fh = fopen ( $CFG->config ['conf'] . 'Connection.json', 'w' );
// 			fwrite ( $fh, json_encode ( $arr ) );
// 			fclose ( $fh );
			
// 			header ( "Location:/connection/confirmation" );
		}
	}

	public function wired() {
        global $CFG;
    	
    	$this->load->helper ( array (
				'form',
				'url'
		) );
		
		$this->load->library ( 'form_validation' );
				
		$this->lang->load ( 'connection/master', $this->config->item ( 'language' ) );
		$this->lang->load ( 'connection/wired', $this->config->item ( 'language' ) );
		
		$this->template->set ( 'lang', $this->config->item('language_abbr') );
		$this->template->set ( 'header', "<title>" . t ( 'ZeePro Personal Printer 21 - Connection configuration' ) . "</title>" );
		
		if ($this->form_validation->run () == FALSE) {
			$data = array();
			$this->template->load ( 'connectionmaster', 'connectionwired', $data );
		} else {
// 			$arr = array("Connection.Topology" => "Network",
//             "Connection.Support" => "RJ45"
// 			);

// 	        $fh = fopen($CFG->config['conf'] . 'Connection.json', 'w');
// 	        fwrite($fh, json_encode($arr));
// 	        fclose($fh);
	
			header ( "Location:/connection/confirmation" );
	    }
    }
        
	public function wiredauto() {
		global $CFG;

        // To be managed by API...

//         $arr = array("Connection.Topology" => "P2P",
//             "Connection.Support" => "WiFi",
//             "IP.addresses.V4" => array(array("Address" => "0.0.0.0")),
//             "IP.lease" => standard_date("DATE_ISO8601", local_to_gmt(time()))
//         );

//         $fh = fopen($CFG->config['conf'] . 'Connection.json', 'w');
//         fwrite($fh, json_encode($arr));
//         fclose($fh);

		$this->load->helper(array('zimapi'));
		ZimAPI_setpEth();
// 		header ( "Location:/connection/confirmation" );
		$this->confirmation();
    }
    
    public function wiredadvanced() {
        global $CFG;
    	
    	$this->load->helper ( array (
				'form',
				'url'
		) );
		
		$this->load->library ( 'form_validation' );
				
		$this->lang->load ( 'connection/master', $this->config->item ( 'language' ) );
		$this->lang->load ( 'connection/wiredadvanced', $this->config->item ( 'language' ) );
		
		$this->template->set ( 'lang', $this->config->item('language_abbr') );
		$this->template->set ( 'header', "<title>" . t ( 'ZeePro Personal Printer 21 - Connection configuration' ) . "</title>" );
		
 		$this->form_validation->set_rules('ip', 'ip error', 'callback_ip_check');
		$this->form_validation->set_message('ip_check', t("ip error"));
 		$this->form_validation->set_rules('mask', 'mask error', 'callback_mask_check');
		$this->form_validation->set_message('mask_check', t("mask error"));
 		$this->form_validation->set_rules('gateway', 'gateway error', 'callback_gateway_check');
		$this->form_validation->set_message('gateway_check', t("gateway error"));
 		$this->form_validation->set_rules('dns', 'dns error', 'callback_ip_check');
 		$this->form_validation->set_message('ip_check', t("dns error"));
 		$this->form_validation->set_error_delimiters('<i>', '</i>');
 		 			
		if ($this->form_validation->run () == FALSE) {
			$this->template->load ( 'connectionmaster', 'connectionwiredadvanced', $data );
		} else {
// 			$arr = array (
// 					"Topology" => "Network",
// 					"Support" => "RJ45",
// 					"IPV4" => array (
// 							array (
// 									"Address" => set_value('ip'), 
// 									"Mask" => set_value('mask'), 
// 									"Gateway" => set_value('gateway'), 
// 									"DNS" => set_value('dns'), 
// 							) 
// 					) 
// 			);

// 	        $fh = fopen($CFG->config['conf'] . 'Connection.json', 'w');
// 	        fwrite($fh, json_encode($arr));
// 	        fclose($fh);
	
			header ( "Location:/connection/confirmation" );
        }
    }
    
	public function wifip2p() {
		$ret_val = 0;
		$error = '';
		$template_data = array();
		$body_page = NULL;
		
		$this->load->helper(array('zimapi'));
		$this->lang->load('connection/wifip2p', $this->config->item('language'));
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('ssid', 'SSID', 'required');
			
			if ($this->form_validation->run() == FALSE) {
				// Here is where you do stuff when the submitted form is invalid.
				$error = t('invalid SSID');
			}
			else {
				$ssid = $this->input->post('ssid');
				$passwd = $this->input->post('pwd');
				
				if (!ctype_print($ssid) || ($passwd && !ctype_print($passwd))) {
					$error = t('invalid data (special character)');
				}
				else {
					$ret_val = ZimAPI_setsWifi($ssid, $passwd);
					if ($ret_val != ERROR_OK) {
						$error = t('invalid data');
					}
					else {
// 						$this->output->set_header("Location:/connection/confirmation");
						$this->confirmation();
						return; // end generating if successed
					}
				}
			}
		}
		
		// generate form to submit when not in post method
		$this->load->library('parser');

		// parse the main body
		$template_data = array(
				'title'			=> t('Personalize the printer\'s network'),
				'ssid_title'	=> t('Write your network\'s name'),
				'pwd_title'		=> t('Write your password'),
				'error'			=> $error,
				'ok'			=> t('OK'),
				'back'			=> t('back'),
		);
		
		$body_page = $this->parser->parse('template/connection/wifip2p', $template_data, TRUE);
		
		// parse all page
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . t('ZeePro Personal Printer 21 - Connection setting') . '</title>',
				'contents'		=> $body_page,
		);
		
		$this->parser->parse('template/basetemplate', $template_data);
		
		return;
	}
    
	public function confirmation() {
        global $CFG;

        $this->lang->load('connection/master', $this->config->item('language'));
        $this->lang->load('connection/confirmation', $this->config->item('language'));

        $this->template->set('lang', $CFG->config['language_abbr']);
        $this->template->set('header', "<title>" . t('ZeePro Personal Printer 21 - Connection configuration') . "</title>");
        $this->template->load('connectionmaster', 'confirmation');

	}
    
}