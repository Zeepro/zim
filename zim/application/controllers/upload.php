<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Upload extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
    }

    public function index() {
        global $CFG;

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $config['upload_path'] = $CFG->config['base_data'] . 'model/';
                // 3D files mime extensions are unreliable
                $config['allowed_types'] = '*';

                $this->load->library('upload', $config);

                if (!$this->upload->do_upload()) {
                    $this->lang->load('master', $this->config->item('language'));
                    $this->lang->load('upload', $this->config->item('language'));

                    $this->load->view('upload', array('error' => $this->upload->display_errors(),
                        "lang" => $CFG->config['language_abbr']));
                } else {
                    $arr = $this->upload->data();
                    $r = curl_init('http://localhost:8080/loadmodel');
                    curl_setopt($r, CURLOPT_POST, 3);
                    curl_setopt($r, CURLOPT_POSTFIELDS,
						'version=1.0&' .
                    	'url=' . urlencode('/home/wait') . "&" .
                    	'param=' . json_encode(array("file" => $arr["file_name"]))
                    );
                    $result = curl_exec($r);
                    curl_close($r);
                    
//                     $r = new HttpRequest('http://localhost:8080/loadmodel', HttpRequest::METH_POST);
//                     $r->addPostFields(array('version' => '1.0',
//                     		'url' => '/home/wait',
//                     		'param' => '{"file", "'. $arr["file_name"] . '"}'));
//                     try {
//                     	$r->send()->getBody();
//                     } catch (HttpException $ex) {
//                     }

//                     exec('"' . $CFG->config['bin'] . 'LoadModel" -file "' . $arr["file_name"] . '" -callbackurl "/home/wait"');

                    header("Location:/wait");
                }
                break;
            default: // GET
                $this->lang->load('master', $this->config->item('language'));
                $this->lang->load('upload', $this->config->item('language'));

                if (!file_exists($CFG->config['base_data'] . 'model/Model.json')) {
                    $this->load->view('upload', array('error' => '',
                        "lang" => $CFG->config['language_abbr']));
                } else {
                    $this->load->view('upload', array('error' => '',
                        "lang" => $CFG->config['language_abbr']));
                }
        }
    }

}