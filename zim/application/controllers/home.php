<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('form', 'url', 'json'));
    }

    public function index() {
        global $CFG;

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $this->session->set_userdata('action', $_POST['action']);
                $cmd = '"' . $CFG->config['bin'] . 'ChangeModel" -callbackurl "/wait"';
                switch ($_POST['action']) {
                    case "width":
                        switch ($_POST['submitbutton']) {
                            case "<<":
                                $cmd = $cmd . " -xtrans -20";
                                break;
                            case "<":
                                $cmd = $cmd . " -xtrans -5";
                                break;
                            case ">":
                                $cmd = $cmd . " -xtrans 5";
                                break;
                            case ">>":
                                $cmd = $cmd . " -xtrans 20";
                                break;
                        }
                        break;
                    case "depth":
                        switch ($_POST['submitbutton']) {
                            case "<<":
                                $cmd = $cmd . " -ytrans -20";
                                break;
                            case "<":
                                $cmd = $cmd . " -ytrans -5";
                                break;
                            case ">":
                                $cmd = $cmd . " -ytrans 5";
                                break;
                            case ">>":
                                $cmd = $cmd . " -ytrans 20";
                                break;
                        }
                        break;
                    case "height":
                        switch ($_POST['submitbutton']) {
                            case "<<":
                                $cmd = $cmd . " -ztrans -20";
                                break;
                            case "<":
                                $cmd = $cmd . " -ztrans -5";
                                break;
                            case ">":
                                $cmd = $cmd . " -ztrans 5";
                                break;
                            case ">>":
                                $cmd = $cmd . " -ztrans 20";
                                break;
                        }
                        break;
                    case "X axis":
                        switch ($_POST['submitbutton']) {
                            case "<<":
                                $cmd = $cmd . " -xrot -90";
                                break;
                            case "<":
                                $cmd = $cmd . " -xrot -10";
                                break;
                            case ">":
                                $cmd = $cmd . " -xrot 10";
                                break;
                            case ">>":
                                $cmd = $cmd . " -xrot 90";
                                break;
                        }
                        break;
                    case "Y axis":
                        switch ($_POST['submitbutton']) {
                            case "<<":
                                $cmd = $cmd . " -yrot -90";
                                break;
                            case "<":
                                $cmd = $cmd . " -yrot -10";
                                break;
                            case ">":
                                $cmd = $cmd . " -yrot 10";
                                break;
                            case ">>":
                                $cmd = $cmd . " -yrot 90";
                                break;
                        }
                        break;
                    case "Z axis":
                        switch ($_POST['submitbutton']) {
                            case "<<":
                                $cmd = $cmd . " -zrot -90";
                                break;
                            case "<":
                                $cmd = $cmd . " -zrot -10";
                                break;
                            case ">":
                                $cmd = $cmd . " -zrot 10";
                                break;
                            case ">>":
                                $cmd = $cmd . " -zrot 90";
                                break;
                        }
                        break;
                    case "Size":
                        switch ($_POST['submitbutton']) {
                            case "<<":
                                $cmd = $cmd . " -factor 0.5";
                                break;
                            case "<":
                                $cmd = $cmd . " -factor 0.870550563";
                                break;
                            case ">":
                                $cmd = $cmd . " -factor 1.148698355";
                                break;
                            case ">>":
                                $cmd = $cmd . " -factor 2";
                                break;
                        }
                        break;
                    case "reset":
                        $cmd = '"' . $CFG->config['bin'] . 'ResetModel" -callbackurl "/wait"';
                        break;
                    case "clear":
                        $cmd = '"' . $CFG->config['bin'] . 'ClearModel" -callbackurl "/wait"';
                        break;
                    case "print":
                        $cmd = '"' . $CFG->config['bin'] . 'PrintModel" -callbackurl "/wait/printing"';
                        exec($cmd);
                        header("Location:/wait/printing");
                        exit;
                    default:
                        // ??
                        header("Location:/" . $CFG->config['language_abbr']);
                        exit;
                }
                exec($cmd);
                header("Location:/wait");
                exit;
                break;
            default: // GET
                $this->lang->load('master', $this->config->item('language'));
                $this->lang->load('home', $this->config->item('language'));

                if (!file_exists($CFG->config['model'] . 'Model.json')) {
                    $this->load->view('home', array("lang" => $CFG->config['language_abbr'],
                        'img' => null,
                        "wait" => false));
                } else {
                    $arr = json_read($CFG->config['model'] . 'Model.json');

                    if ($arr["error"] or !array_key_exists("Version", $arr["json"])) {
                        // Something went wrong... Wiping out
                        exec('"' . $CFG->config['bin'] . 'ClearModel" -callbackurl "/wait"');
                        header("Location:/wait");
                        exit;
                    }

                    switch ($arr["json"]["Version"]) {
                        case "1.0":
                            if (array_key_exists("File", $arr["json"]) and
                                    array_key_exists("xTrans", $arr["json"]) and
                                    array_key_exists("yTrans", $arr["json"]) and
                                    array_key_exists("zTrans", $arr["json"]) and
                                    array_key_exists("xRot", $arr["json"]) and
                                    array_key_exists("yRot", $arr["json"]) and
                                    array_key_exists("zRot", $arr["json"]) and
                                    array_key_exists("Factor", $arr["json"])) {
                                $img = substr(sha1($arr["json"]["File"]), 0, 10) . "xt" . $arr["json"]["xTrans"] . "yt" . $arr["json"]["yTrans"] . "zt" . $arr["json"]["zTrans"] . "xr" . $arr["json"]["xRot"] . "yr" . $arr["json"]["yRot"] . "zr" . $arr["json"]["zRot"] . "f" . rtrim(rtrim(number_format($arr["json"]["Factor"], 6), '0'), '.') . ".png";
                                if (!file_exists($CFG->config['model'] . $img)) {
                                    // Rendering the view
                                    exec('"' . $CFG->config['bin'] . 'RenderModel" -callbackurl "/wait"');
                                    header("Location:/wait");
                                    exit;
                                } else {
                                    // The view allready exists
                                    $this->load->view('home', array("lang" => $CFG->config['language_abbr'],
                                        "img" => "/model/" . $img,
                                        "action" => $this->session->userdata('action'),
                                        "wait" => false));
                                }
                            } else {
                                // Malformed 1.0 file
                                exec('"' . $CFG->config['bin'] . 'ClearModel" -callbackurl "/wait"');
                                header("Location:/wait");
                                exit;
                            }
                            break;
                        default:
                            // Not supported version
                            exec('"' . $CFG->config['bin'] . 'ClearModel" -callbackurl "/wait"');
                            header("Location:/wait");
                            exit;
                    }
                }
        }
    }

}