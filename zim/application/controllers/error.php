<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Error extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('form', 'url', 'json'));
    }

    public function index() {
        global $CFG;

        $this->lang->load('master', $this->config->item('language'));
        $this->lang->load('home', $this->config->item('language'));
        $this->lang->load('error', $this->config->item('language'));

        $this->template->set('lang', $CFG->config['language_abbr']);
        $this->template->set('header', "<title>" . t('ZeePro Personal Printer 21 - Error') . "</title>");

        $arr = json_read($CFG->config['conf'] . 'Work.json');

        if ($arr["error"]) {
            // Json decoding error
            $data['message'] = message($arr["error"]);
        } else {
            if (!array_key_exists("Version", $arr["json"])) {
                // Malformed file
                $data['message'] = t("Internal error #7 (if this message persists, thank you to contact our maintenance service)");
            } else {
                switch ($arr["json"]["Version"]) {
                    case "1.0":
                        if (array_key_exists("Message", $arr["json"])) {
                            $data['message'] = message($arr["json"]["Message"]);
                        } else {
                            // Malformed 1.0 file
                            $data['message'] = t("Internal error #8 (if this message persists, thank you to contact our maintenance service)");
                        }
                        break;
                    default:
                        // Not supported version
                        $data['message'] = t("Internal error #9 (if this message persists, thank you to contact our maintenance service)");
                }
            }
        }
        $this->template->load('master', 'error', $data);
    }

}