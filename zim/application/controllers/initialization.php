<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Initialization extends MY_Controller {

    public function index() {
        global $CFG;

        do {
            $json = @file_get_contents($CFG->config['conf'] . 'Boot.json');
            if ($json === false) {
                // Can't access file...
                if (file_exists($CFG->config['conf'] . 'Boot.json')) {
                    // ... cause it seems to be locked by the software layer
                    usleep(500000);
                    continue;
                } else {
                    // ... cause it was deleted in the meanwhile
                    header("location: " . $CFG->config['base_url']);
                    exit;
                }
            }
        } while (false);

        $this->lang->load('master', $this->config->item('language'));
        $this->lang->load('initialization', $this->config->item('language'));
        $this->lang->load('error', $this->config->item('language'));

        $this->template->set('lang', $CFG->config['language_abbr']);
        // Refresh every 2 seconds
        $this->template->set('header', "<title>" . t('ZeePro Personal Printer 21 - Initialization in progress...') . "</title>" . '<meta http-equiv="Refresh" content="2" />');

        // Json decoding
        $arr = json_decode($json, true);

        if ($arr === null or !array_key_exists("Version", $arr)) {
            // Json decoding error
            $data['message'] = t("Internal error #1 (if this message persists, thank you to contact our maintenance service)");
        } else {
            switch ($arr["Version"]) {
                case "1.0":
                    if (array_key_exists("Message", $arr)) {
                        $data['message'] = message($arr["Message"]);
                    } else {
                        // Malformed 1.0 file
                        $data['message'] = t("Internal error #2 (if this message persists, thank you to contact our maintenance service)");
                    }
                    break;
                default:
                    // Not supported version
                    $data['message'] = t("Internal error #3 (if this message persists, thank you to contact our maintenance service)");
            }
        }

        $this->template->load('master', 'initialization', $data);
    }
}