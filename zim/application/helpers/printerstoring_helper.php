<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

define('PRINTERSTORING_FILE_STL1_BZ2',	'model1.stl.bz2');
define('PRINTERSTORING_FILE_STL2_BZ2',	'model2.stl.bz2');
define('PRINTERSTORING_FILE_GCODE_BZ2',	'model.gcode.bz2');
define('PRINTERSTORING_FILE_IMG_PNG',	'image.png');
define('PRINTERSTORING_FILE_IMG_JPG',	'image.jpg');
define('PRINTERSTORING_FILE_INFO_JSON',	'info.json');

//internal function
function PrinterStoring__getLastId($folderpath) {
	$id = -1;
	if ($dh = opendir($folderpath)) {
		$id = 0;
		while ($file = readdir($dh)) {
			if ($file !== "." and $file !== ".." and $file !== sprintf('%03d', $id)) {
				break;
			}
			$id++;
		}
	}
	return $id;
}

//internal function
function PrinterStoring__createInfoFile($info_file, $info) {
	try {
		$fp = fopen($info_file, 'w+');
		fwrite($fp, json_encode($info));
		fclose($fp);
	}
	catch (Exception $e) {
		return false;
	}
	return true;
}

//internal function
function PrinterStoring__storeModelFile($model_storing_path, $file_path) {
	$command = 'bzip2 -zcf "' . $file_path . '" > "' . $model_storing_path . '"';
	exec($command, $output, $ret_val);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		return false;
	}
	return true;
}

//intternal function
function PrinterStoring__generateRendering($f1, $f2) {
	// call error list if we want
	$CI = &get_instance();
	$CI->load->helper(array('zimapi', 'printerstate'));
	$CI->load->helper('slicer');

	if ($f2 !== NULL) {
		Slicer_addModel(array($f1['full_path'], $f2['full_path']));
	}
	else {
		Slicer_addModel(array($f1['full_path']));	
	}

	$path_image = NULL;
	$display = NULL;
		
	$rho = ZIMAPI_VALUE_DEFAULT_RHO;
	$theta = ZIMAPI_VALUE_DEFAULT_DELTA;
	$delta = ZIMAPI_VALUE_DEFAULT_THETA;
	$color1 = '#000000';
	$color2 = '#000000';

	// check color input
	// if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color1)) {
	// 	$color1 = NULL;
	// }
	// if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color2)) {
	// 	$color2 = NULL;
	// }
	// if ($rho === FALSE || $theta === FALSE || $delta === FALSE) {
	// 	$cr = ERROR_MISS_PRM;
	// }
	// else if ((int)$rho < 0) {
	// 	$cr = ERROR_WRONG_PRM;
	// }
	// else {
		$file_info = array();
		$file_cartridge = NULL;
		
		$CI->load->helper('slicer');
//		 echo $rho.'-'.$theta.'-'.$delta.'-'.$path_image.'-'.$color1.'-'.$color2;
		 //die;
		$cr = Slicer_rendering((int)$rho, (int)$theta, (int)$delta, $path_image, $color1, $color2);
		echo $cr; die;
		if ($cr == ERROR_OK) {
			echo 'ok'; die;
			//TODO add the possibility of making path everywhere, but not only in /var/www/tmp/
			$CI->load->helper('file');

			$file_info = get_file_info(realpath($path_image), array('name'));
			$display = $file_info['server_path'];
		}
	//}
	return $display;
}

// internal function
function 	PrinterStoring__storeRendering($f1, $f2, $image_storing_path) {

		// $this->load->helper('slicer');
		// if (0 == strlen(@file_get_contents($this->config->item('temp') . SLICER_FILE_HTTP_PORT))
		// 		&& FALSE == $this->config->item('simulator')) {
		// 	return false;
		// }
		// else if (!Slicer_checkOnline(FALSE)) {
		// 	return false;
		// }
		
		// // cleanup old upload temporary files
		// $this->_clean_upload_folder();

	// generate rendering
	if (($image_path = PrinterStoring__generateRendering($f1, $f2)) === NULL) {
		return false;
	}

	// store the rendering image
	if (!copy($image_path, $image_storing_path)) {
		return false;
	}
	return true;
}


function PrinterStoring_storeStl($name, $f1, $f2) {
	global $CFG;
	$CI = &get_instance();
	$stl_library_path = $CFG->config['stl_library'];

	// check if library folder exist
	if (!is_dir($stl_library_path)) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('stl library folder does not exist', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}

	// get last unused ID
	if (($model_id = PrinterStoring__getLastId($stl_library_path)) < 0) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('could not get the last id from stl library', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	
	// create model folder
	$model_folder = $stl_library_path . sprintf('%03d', $model_id) . '/';

	if (!mkdir($model_folder)) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('could not create the model folder', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}	

	// create info file
	$info_file = $model_folder . PRINTERSTORING_FILE_INFO_JSON;

	$info = array(
		"id" => $model_id,
		"name" => $name,
		"creation_date" => date("Y-m-d"),
		"multiple" => ($f2 === NULL ? false : true)
	);

	if (!PrinterStoring__createInfoFile($info_file, $info)) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('could not create the model info file', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}

	// store rendering image
	if (!PrinterStoring__storeRendering($f1, $f2, $model_folder . PRINTERSTORING_FILE_IMG_PNG)) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('could not store the rendering image', __FILE__, __LINE__);
		return ERROR_DISK_FULL;
	}

	// store file(s)
	if (!PrinterStoring__storeModelFile($model_folder . PRINTERSTORING_FILE_STL1_BZ2, $f1["full_path"]) ||
	($f2 !== NULL && !PrinterStoring__storeModelFile($model_folder . PRINTERSTORING_FILE_STL2_BZ2, $f2["full_path"]))) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('could not store the file(s)', __FILE__, __LINE__);
		return ERROR_DISK_FULL;
	}

	return ERROR_OK;
}

function PrinterStoring_renameStl($id, $name) {
	global $CFG;
	$info_file = $CFG->config['stl_library'] . sprintf('%03d', $id) . '/' . PRINTERSTORING_FILE_INFO_JSON;

	//rename Stl
	try {
		$str = file_get_contents($info_file);
		if (($info = json_decode($str)) !== TRUE || !array_key_exists('name', $info)) {
			return ERROR_UNKNOWN_MODEL;
		}
		$info['name'] = $name;
		$fp = fopen($info_file, 'w+');
		fwrite($fp, json_encode($info));
		fclose($fp);
	}
	catch (Exception $e) {
		return ERROR_UNKNOWN_MODEL;
	}

	return ERROR_OK;
}

function PrinterStoring_deleteStl($id) {
	$model_folder = $CFG->config['stl_library'] . sprintf('%03d', $id) . '/';

	// check if library folder exist
	if (!is_dir($model_folder)) {
		return ERROR_UNKNOWN_MODEL;
	}
	if (system("rm -rf ".escapeshellarg($model_folder))) {
		return ERROR_UNKNOWN_MODEL;
	}
	return ERROR_OK;
}

function PrinterStoring_renameGcode($id, $name) {
	global $CFG;
	$info_file = $CFG->config['gcode_library'] . sprintf('%03d', $id) . '/' . PRINTERSTORING_FILE_INFO_JSON;

	//rename Stl
	try {
		$str = file_get_contents($info_file);
		if (($info = json_decode($str)) !== TRUE || !array_key_exists('name', $info)) {
			return ERROR_UNKNOWN_MODEL;
		}
		$info['name'] = $name;
		$fp = fopen($info_file, 'w+');
		fwrite($fp, json_encode($info));
		fclose($fp);
	}
	catch (Exception $e) {
		return ERROR_UNKNOWN_MODEL;
	}

	return ERROR_OK;
}

function PrinterStoring_deleteGcode($id) {
	$model_folder = $CFG->config['gcode_library'] . sprintf('%03d', $id) . '/';

	// check if library folder exist
	if (!is_dir($model_folder)) {
		return ERROR_UNKNOWN_MODEL;
	}
	if (system("rm -rf ".escapeshellarg($model_folder))) {
		return ERROR_UNKNOWN_MODEL;
	}
	return ERROR_OK;
}

function PrinterStoring_listStl() {
	global $CFG;
	$modellist = array();
	$stl_library_path = $CFG->config['stl_library'];

	if (is_dir($CFG->config['base_library']) && is_dir($stl_library_path) && $dh = opendir($stl_library_path)) {
		while (($file = readdir($dh)) !== false) {
			if ($file !== "." and $file !== "..") {
				$stl_model_folder = $stl_library_path . $file . '/';
				try {
					$str = file_get_contents($stl_model_folder . PRINTERSTORING_FILE_INFO_JSON);
					if (($info = json_decode($str)) && array_key_exists('name', $info)
					&& array_key_exists('id', $info)) {
						$model = array(
							'id' => $info['id'],
							'name' => $info['name'],
							'imglink' => NULL
						);
						if (is_file($stl_model_folder . PRINTERSTORING_FILE_IMG_PNG)) {
							$model['imglink'] = '/data/library/stl/' . sprintf('%03d', $model['id']) . '/' . PRINTERSTORING_FILE_IMG_PNG;
						}

						array_push($modellist, $model);
					}
				}
				catch(Exception $e) {	}
			}
		}
		closedir($dh);
	}

	return json_encode($modellist);
}

function PrinterStoring_listGcode() {
	global $CFG;
	$modellist = array();
	$gcode_library_path = $CFG->config['gcode_library'];

	if (is_dir($CFG->config['base_library']) && is_dir($gcode_library_path) && $dh = opendir($gcode_library_path)) {
		while (($file = readdir($dh)) !== false) {
			if ($file !== "." and $file !== "..") {
				$gcode_model_folder = $gcode_library_path . $file . '/';
				try {
					$str = file_get_contents($gcode_model_folder . PRINTERSTORING_FILE_INFO_JSON);
					if (($info = json_decode($str)) && array_key_exists('name', $info)
					&& array_key_exists('id', $info)) {
						$model = array(
							'id' => $info['id'],
							'name' => $info['name'],
							'imglink' => NULL
						);
						if (is_file($gcode_model_folder . PRINTERSTORING_FILE_IMG_PNG)) {
							$model['imglink'] = '/data/library/gcode/' . sprintf('%03d', $model['id']) . '/' . PRINTERSTORING_FILE_IMG_PNG;
						}

						array_push($modellist, $model);
					}
				}
				catch(Exception $e) {	}
			}
		}
		closedir($dh);
	}

	return json_encode($modellist);
}