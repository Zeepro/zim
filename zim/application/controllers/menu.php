<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Menu extends MY_Controller {

    public function index() {
        global $CFG;

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $arr = array('fill_option' => $_POST['fill_option']);
                $fh = fopen($CFG->config['base_data'] . 'conf\\Settings.json', 'w');
                fwrite($fh, json_encode($arr));
                fclose($fh);
                break;
            default: // GET
                $arr = json_decode(file_get_contents($CFG->config['base_data'] . 'conf\\Settings.json'), true);
                break;
        }

        $this->lang->load('master', $this->config->item('language'));
        $this->lang->load('menu', $this->config->item('language'));

        switch ($CFG->config['language_abbr']) {
            case "fr":
                $language = t("french");
                $flag_url = "/images/fr.png";
                break;
            default:
                $language = t("english");
                $flag_url = "/images/gb.png";
                break;
        }
        $this->load->view('menu', array("lang" => $CFG->config['language_abbr'],
            "fill_option" => $arr["fill_option"],
            "language" => $language,
            "flag_url" => $flag_url)
        );
    }

}