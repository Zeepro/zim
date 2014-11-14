<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

define('PRINTERSTORING_FILE_STL1_BZ2',	'model1.stl.bz2');
define('PRINTERSTORING_FILE_STL2_BZ2',	'model2.stl.bz2');
define('PRINTERSTORING_FILE_STL1_EXT',	'model1.stl');
define('PRINTERSTORING_FILE_STL2_EXT',	'model2.stl');
define('PRINTERSTORING_FILE_GCODE_BZ2',	'model.gcode.bz2');
define('PRINTERSTORING_FILE_GCODE_EXT',	'model.gcode');
define('PRINTERSTORING_FILE_IMG_PNG',	'image.png');
define('PRINTERSTORING_FILE_IMG_JPG',	'image.jpg');
define('PRINTERSTORING_FILE_INFO_JSON',	'info.json');

function PrinterStoring_initialFile() {
	$ret_val = TRUE;
	$CI = &get_instance();
	$array_check = array(
			$CI->config->item('base_library'),
			$CI->config->item('stl_library'),
			$CI->config->item('gcode_library'),
	);
	
	foreach ($array_check as $folder_check) {
		if (file_exists($folder_check)) {
			if (is_dir($folder_check)) {
				continue;
			}
			else {
				$ret_val = FALSE;
				$CI->load->helper('printerlog');
				PrinterLog_logMessage('illegal file detected, try to unlink: ' . $folder_check, __FILE__, __LINE__);
				@unlink($folder_check);
				usleep(3); // make sure that file is deleted
			}
		}
		
		$ret_val = mkdir($folder_check);
		if ($ret_val == FALSE) {
			$CI->load->helper('printerlog');
			PrinterLog_logError('library initialization create error: ' . $folder_check, __FILE__, __LINE__);
			
			break;
		}
	}
	
	return $ret_val;
}

//internal function
function PrinterStoring__getLastId($folderpath) {
	$id = -1;
	$folder_array = @scandir($folderpath);
	if ($folder_array) {
	$id = 1;
		foreach ($folder_array as $file) {
			if ($file !== "." and $file !== "..") {
				if ($file !== sprintf('%06d', $id)) {
					break;
				}
				$id++;
			}
		}
	}
	return $id;
}

//internal function
function PrinterStoring__createInfoFile($info_file, $info) {
	try {
		$fp = @fopen($info_file, 'w+');
		@fwrite($fp, json_encode($info));
		@fclose($fp);
	}
	catch (Exception $e) {
		return false;
	}
	return true;
}

//internal function
function PrinterStoring__storeModelFile($model_storing_path, $file_path) {
	$command = 'bzip2 -zckf "' . $file_path . '" > "' . $model_storing_path . '"';
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
	$color1 = '#FFFFFF';
	$color2 = '#FF0000';

	$file_info = array();
	$file_cartridge = NULL;
	
	$CI->load->helper('slicer');

	$cr = Slicer_rendering((int)$rho, (int)$theta, (int)$delta, $path_image, $color1, $color2);

	if ($cr == ERROR_OK) {
		$CI->load->helper('file');
		$file_info = get_file_info(realpath($path_image));
		$display = $file_info['server_path'];
	}

	return $display;
}

// internal function
function 	PrinterStoring__storeRendering($f1, $f2, $image_storing_path) {
	global $CFG;
	$CI = &get_instance();
	$CI->load->helper('slicer');
	Slicer_checkOnline(TRUE);

	// generate rendering
	if (($image_path = PrinterStoring__generateRendering($f1, $f2)) === NULL) {
		return false;
	}

	// store the rendering image
	if (@copy($image_path, $image_storing_path) == false) {
		return false;
	}
	return true;
}


function PrinterStoring_storeStl($name, $f1, $f2) {
	global $CFG;
	$CI = &get_instance();
	$stl_library_path = $CFG->config['stl_library'];

	// check if library folder exist
	if (@is_dir($stl_library_path) == false) {
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
	$model_folder = $stl_library_path . sprintf('%06d', $model_id) . '/';

	if (@mkdir($model_folder) == false) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('could not create the model folder : '.$model_folder, __FILE__, __LINE__);
		return ERROR_DISK_FULL;
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
		PrinterStoring_deleteStl($model_id);
		$CI->load->helper('printerlog');
		PrinterLog_logError('could not create the model info file', __FILE__, __LINE__);
		return ERROR_DISK_FULL;
	}

	// store rendering image
	if (!PrinterStoring__storeRendering($f1, $f2, $model_folder . PRINTERSTORING_FILE_IMG_PNG)) {
		PrinterStoring_deleteStl($model_id);
		$CI->load->helper('printerlog');
		PrinterLog_logError('could not store the rendering image', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	// store file(s)
	if (!PrinterStoring__storeModelFile($model_folder . PRINTERSTORING_FILE_STL1_BZ2, $f1["full_path"]) ||
	($f2 !== NULL && !PrinterStoring__storeModelFile($model_folder . PRINTERSTORING_FILE_STL2_BZ2, $f2["full_path"]))) {
		PrinterStoring_deleteStl($model_id);
		$CI->load->helper('printerlog');
		PrinterLog_logError('could not store the file(s)', __FILE__, __LINE__);
		return ERROR_DISK_FULL;
	}
	return ERROR_OK;
}

function PrinterStoring_renameStl($id, $name) {
	global $CFG;
	$CI = &get_instance();
	$info_file = $CFG->config['stl_library'] . sprintf('%06d', $id) . '/' . PRINTERSTORING_FILE_INFO_JSON;

	//rename Stl
	try {
		if (($str = @file_get_contents($info_file)) === false || ($info = json_decode($str, true)) != TRUE || !array_key_exists('name', $info)) {
			$CI->load->helper('printerlog');
			PrinterLog_logError('stl model id not found', __FILE__, __LINE__);
			return ERROR_UNKNOWN_MODEL;
		}
		$info['name'] = $name;
		$fp = @fopen($info_file, 'w+');
		@fwrite($fp, json_encode($info));
		@fclose($fp);
	}
	catch (Exception $e) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('could not rename the stl model', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}

	return ERROR_OK;
}

function PrinterStoring_deleteStl($id) {
	global $CFG;
	$CI = &get_instance();
	$model_folder = $CFG->config['stl_library'] . sprintf('%06d', $id) . '/';

	// check if library folder exist
	if (@is_dir($model_folder) == false) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('stl model id not found', __FILE__, __LINE__);
		return ERROR_UNKNOWN_MODEL;
	}
	if (($t = system("rm -rf " . escapeshellarg($model_folder))) === false || !empty($t)) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('could not delete the stl model', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}

	return ERROR_OK;
}

function PrinterStoring_getInfo($type, $id) {
	global $CFG;
	$CI = &get_instance();
	if ($type == "stl") {
		$info_file = $CFG->config['stl_library'] . sprintf('%06d', $id) . '/' . PRINTERSTORING_FILE_INFO_JSON;
	}
	else if ($type == "gcode") {
		$info_file = $CFG->config['gcode_library'] . sprintf('%06d', $id) . '/' . PRINTERSTORING_FILE_INFO_JSON;
	}
	else {
		return null;
	}

	try {
		if (($str = @file_get_contents($info_file)) === false || ($info = json_decode($str, true)) != TRUE || !array_key_exists('name', $info)) {
			return null;
		}
		return $info;
	}
	catch (Exception $e) {
		return null;
	}

	return null;
}

function PrinterStoring_renameGcode($id, $name) {
	global $CFG;
	$CI = &get_instance();
	$info_file = $CFG->config['gcode_library'] . sprintf('%06d', $id) . '/' . PRINTERSTORING_FILE_INFO_JSON;

	//rename Stl
	try {
		if (($str = @file_get_contents($info_file)) === false || ($info = json_decode($str, true)) != TRUE || !array_key_exists('name', $info)) {
			$CI->load->helper('printerlog');
			PrinterLog_logError('gcode model id not found', __FILE__, __LINE__);
			return ERROR_UNKNOWN_MODEL;
		}
		$info['name'] = $name;
		$fp = @fopen($info_file, 'w+');
		@fwrite($fp, json_encode($info));
		@fclose($fp);
	}
	catch (Exception $e) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('could not rename the gcode model', __FILE__, __LINE__);
		return ERROR_UNKNOWN_MODEL;
	}

	return ERROR_OK;
}

function PrinterStoring_deleteGcode($id) {
	global $CFG;
	$CI = &get_instance();
	$model_folder = $CFG->config['gcode_library'] . sprintf('%06d', $id) . '/';

	// check if library folder exist
	if (@is_dir($model_folder) == false) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('gcode model id not found', __FILE__, __LINE__);
		return ERROR_UNKNOWN_MODEL;
	}
	if (($t = system("rm -rf " . escapeshellarg($model_folder))) === false || !empty($t)) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('could not delete the gcode model', __FILE__, __LINE__);
		return ERROR_UNKNOWN_MODEL;
	}
	return ERROR_OK;
}

function PrinterStoring_listStl() {
	global $CFG;
	$CI = &get_instance();
	$modellist = array();
	$stl_library_path = $CFG->config['stl_library'];

	if (@is_dir($CFG->config['base_library']) && @is_dir($stl_library_path) && $dh = @opendir($stl_library_path)) {
		while (($file = @readdir($dh)) !== false) {
			if ($file !== "." and $file !== "..") {
				$stl_model_folder = $stl_library_path . $file . '/';
				try {
//					$str = file_get_contents($stl_model_folder . PRINTERSTORING_FILE_INFO_JSON);
					if (($str = @file_get_contents($stl_model_folder . PRINTERSTORING_FILE_INFO_JSON)) && ($info = json_decode($str, true)) && array_key_exists('name', $info)
					&& array_key_exists('id', $info)) {
						$model = array(
							'id' => $info['id'],
							'name' => $info['name'],
							'imglink' => NULL,
							'creation_date' => $info['creation_date']
						);
						if (@is_file($stl_model_folder . PRINTERSTORING_FILE_IMG_PNG)) {
							$model['imglink'] = $CFG->config['stl_library'] . sprintf('%06d', $model['id']) . '/' . PRINTERSTORING_FILE_IMG_PNG;
						}

						array_push($modellist, $model);
					}
				}
				catch(Exception $e) {
					$CI->load->helper('printerlog');
					PrinterLog_logError('could not list stl models', __FILE__, __LINE__);
				}
			}
		}
		closedir($dh);
	}

	return json_encode($modellist);
}

function PrinterStoring_listGcode() {
	global $CFG;
	$CI = &get_instance();
	$modellist = array();
	$gcode_library_path = $CFG->config['gcode_library'];

	if (@is_dir($CFG->config['base_library']) && @is_dir($gcode_library_path) && $dh = @opendir($gcode_library_path)) {
		while (($file = @readdir($dh)) !== false) {
			if ($file !== "." and $file !== "..") {
				$gcode_model_folder = $gcode_library_path . $file . '/';
				try {
//					$str = file_get_contents($gcode_model_folder . PRINTERSTORING_FILE_INFO_JSON);
					if (($str = @file_get_contents($gcode_model_folder . PRINTERSTORING_FILE_INFO_JSON)) && ($info = json_decode($str, true)) && array_key_exists('name', $info)
					&& array_key_exists('id', $info)) {
						$model = array(
							'id' => $info['id'],
							'name' => $info['name'],
							'imglink' => NULL,
							'creation_date' => $info['creation_date']
						);
						if (is_file($gcode_model_folder . PRINTERSTORING_FILE_IMG_JPG)) {
							$model['imglink'] = $gcode_library_path . sprintf('%06d', $model['id']) . '/' . PRINTERSTORING_FILE_IMG_JPG;
						}

						array_push($modellist, $model);
					}
				}
				catch(Exception $e) {
					$CI->load->helper('printerlog');
					PrinterLog_logError('could not list gcode models', __FILE__, __LINE__);
				}
			}
		}
		closedir($dh);
	}

	return json_encode($modellist);
}

//internal function
function PrinterStoring__extractFile($filepath, $filename) {
	global $CFG;
	$CI = &get_instance();

	$extracted_path = $CI->config->item('temp') . $filename;
	$command = 'bzip2 -dkcf ' . $filepath . ' > ' . $extracted_path;
	exec($command, $output, $ret_val);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		return null;
	}
	return $extracted_path;
}

function PrinterStoring_printStl($id) {
	global $CFG;
	$CI = &get_instance();
	$CI->load->helper('slicer');

	$info_file = $CFG->config['stl_library'] . sprintf('%06d', $id) . '/' . PRINTERSTORING_FILE_INFO_JSON;
	try {
//		$str = file_get_contents($info_file);
		if (($str = @file_get_contents($info_file)) === false || ($info = json_decode($str, true)) != TRUE || !array_key_exists('name', $info)) {
			$CI->load->helper('printerlog');
			PrinterLog_logError('stl model id not found', __FILE__, __LINE__);
			return ERROR_UNKNOWN_MODEL;
		}

		Slicer_checkOnline(TRUE);

		// extract, copy model file(s) to tmp and addModel to slicer
		$model_file1 = $CFG->config['stl_library'] . sprintf('%06d', $id) . '/' . PRINTERSTORING_FILE_STL1_BZ2;
		if (($file_path = PrinterStoring__extractFile($model_file1, PRINTERSTORING_FILE_STL1_EXT)) !== null) {
			if ($info["multiple"] === true) {
				$model_file2 = $CFG->config['stl_library'] . sprintf('%06d', $id) . '/' . PRINTERSTORING_FILE_STL2_BZ2;
				if (($file2_path = PrinterStoring__extractFile($model_file2, PRINTERSTORING_FILE_STL2_EXT)) !== null) {
					return Slicer_addModel(array($file_path, $file2_path));
				}
				else {
					$CI->load->helper('printerlog');
					PrinterLog_logError('could not extract the stl model', __FILE__, __LINE__);
					return ERROR_INTERNAL;
				}
			}
			else {
				return Slicer_addModel(array($file_path));
			}
		}
		else {
			$CI->load->helper('printerlog');
			PrinterLog_logError('could not extract the stl model', __FILE__, __LINE__);
			return ERROR_INTERNAL;
		}
	}
	catch (Exception $e) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('stl model print error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}

	return ERROR_OK;
}

function PrinterStoring_storeGcode($name, $length = 0) {
	global $CFG;
	$CI = &get_instance();
	$gcode_library_path = $CFG->config['gcode_library'];

	// check if library folder exist
	if (@is_dir($gcode_library_path) == false) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('gcode library folder does not exist', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}

	// get last unused ID
	if (($model_id = PrinterStoring__getLastId($gcode_library_path)) < 0) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('could not get the last id from gcode library', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	
	// create model folder
	$model_folder = $gcode_library_path . sprintf('%06d', $model_id) . '/';

	if (@mkdir($model_folder) == false) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('could not create the model folder', __FILE__, __LINE__);
		return ERROR_DISK_FULL;
	}	

	// create info file
	$info_file = $model_folder . PRINTERSTORING_FILE_INFO_JSON;

	$info = array(
		"id" => $model_id,
		"name" => $name,
		"creation_date" => date("Y-m-d"),
		"length" => $length
	);

	if (!PrinterStoring__createInfoFile($info_file, $info)) {
		PrinterStoring_deleteGcode($model_id);
		$CI->load->helper('printerlog');
		PrinterLog_logError('could not create the model info file', __FILE__, __LINE__);
		return ERROR_DISK_FULL;
	}

	// store captured image
	$CI->load->helper('zimapi');
	$image_path = ZIMAPI_FILEPATH_CAPTURE;
	if (file_exists($image_path) === false) {
	 	$CI->load->helper('printerlog');
	 	PrinterLog_logError('could not find the captured image', __FILE__, __LINE__);
	 	return ERROR_IMG_NOTFOUND;
	}
	elseif (!copy($image_path, $model_folder . PRINTERSTORING_FILE_IMG_JPG)) {
		$CI->load->helper('printerlog');
	 	PrinterLog_logError('could not store the captured image', __FILE__, __LINE__);
	 	return ERROR_DISK_FULL;
	}

	// store file(s)
	$CI->load->helper('slicer');
	$file_path = $CI->config->item('temp') . SLICER_FILE_MODEL;
	if (@file_exists($file_path) == false) {
		PrinterStoring_deleteGcode($model_id);
		$CI->load->helper('printerlog');
		PrinterLog_logError('could not find the gcode model', __FILE__, __LINE__);
		return ERROR_GCODE_NOTFOUND;
	}
	elseif(!PrinterStoring__storeModelFile($model_folder . PRINTERSTORING_FILE_GCODE_BZ2, $file_path)) {
		PrinterStoring_deleteGcode($model_id);
		$CI->load->helper('printerlog');
		PrinterLog_logError('disk full, could not store the gcode file', __FILE__, __LINE__);
		return ERROR_DISK_FULL;
	}

	return ERROR_OK;
}

//FIXME this function doesn't verify any conditions before launching (filament, temperature, etc.)
//TODO transfer this function to proper helper: printer_helper.php with adaption with corestatus_helper.php
function PrinterStoring_printGcode($id) {
	global $CFG;
	$CI = &get_instance();
	$CI->load->helper('slicer');

	$model_path = $CFG->config['gcode_library'] . sprintf('%06d', $id) . '/';
	$info_file = $model_path . PRINTERSTORING_FILE_INFO_JSON;
	try {
//		$str = file_get_contents($info_file);
		if (($str = @file_get_contents($info_file)) === false || ($info = json_decode($str, true)) != TRUE || !array_key_exists('name', $info)) {
			$CI->load->helper('printerlog');
			PrinterLog_logError('gcode model id not found', __FILE__, __LINE__);
			return ERROR_UNKNOWN_MODEL;
		}

		// extract, copy model file(s) to tmp and addModel to slicer
		$model_file = $model_path . PRINTERSTORING_FILE_GCODE_BZ2;
		if (($file_path = PrinterStoring__extractFile($model_file, PRINTERSTORING_FILE_GCODE_EXT)) !== null) {
			$CI->load->helper('printer');
			$cr = Printer_printFromFile($file_path, $id);
			if ($cr != TRUE) {
				$CI->load->helper('printerlog');
				PrinterLog_logError('gcode model print error', __FILE__, __LINE__);
				return ERROR_INTERNAL;
			}
		}
		else {
			$CI->load->helper('printerlog');
			PrinterLog_logError('could not extract the gcode model', __FILE__, __LINE__);
			return ERROR_INTERNAL;
		}
	}
	catch (Exception $e) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('gcode model print error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}

	return ERROR_OK;
}