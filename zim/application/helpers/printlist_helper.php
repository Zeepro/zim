<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

// call error list if we want
$CI = &get_instance();
$CI->load->helper(array (
		'errorcode',
		'file',
		'directory',
		'json',
		'url',
));

if (!defined('PRINTLIST_MAX_PIC_SIZE')) {
	define('PRINTLIST_MAX_PIC_SIZE',	1024);
	define('PRINTLIST_MAX_GCODE_SIZE',	1024*100);
	define('PRINTLIST_MAX_FILE_PIC',	5);
	
	define('PRINTLIST_TITLE_ID',		'mid');
	define('PRINTLIST_TITLE_NAME',		'name');
	define('PRINTLIST_TITLE_DESP',		'description');
	define('PRINTLIST_TITLE_TIME',		'duration');
	define('PRINTLIST_TITLE_LENG_F1',	'filmament1');
	define('PRINTLIST_TITLE_LENG_F2',	'filmament2');
	define('PRINTLIST_TITLE_PIC',		'picture');
// 	define('PRINTLIST_TITLE_GCODE',		'gcode');
	
	define('PRINTLIST_FILE_GCODE',		'model.gcode');
	define('PRINTLIST_FILE_JSON',		'model.json');
	
// 	define('PRINTLIST_GETPIC_BASE_WEB',	base_url() . 'getpicture');
	if ($_SERVER['SERVER_PORT'] != 80) {
 		define('PRINTLIST_GETPIC_BASE_WEB',	'http://' . $_SERVER['HTTP_HOST'] . ':' . $_SERVER['SERVER_PORT'] . base_url('getpicture'));
	} else {
		define('PRINTLIST_GETPIC_BASE_WEB',	'http://' . $_SERVER['HTTP_HOST'] . base_url('getpicture'));
	}
	define('PRINTLIST_GETPIC_PRM_MID',	'mid');
	define('PRINTLIST_GETPIC_PRM_PIC',	'p');
}


function ModelList_add($data_array) {
	global $CFG;
	$printlist_basepath	= $CFG->config['printlist'];
	$model_path			= '';
	$model_pictures		= array();
	$json_data			= NULL;
	
	$model_name			= '';	// string by $data_array['n'], string
	$model_desp			= '';	// string by $data_array['d'], string
	$model_printtime	= 0;	// int by $data_array['t'], int
	$model_filament1	= 0;	// int by $data_array['l1'], int
	$model_filament2	= 0;	// int by $data_array['l2'], int
	$model_gcode		= NULL;	// file array by $data_array['f'], array
	//other parameters
	//$data_array['p1'] ~ $data_array['p5']: file array which contain images

	//==========================================================
	//check parameters
	//==========================================================
	if (is_array($data_array)) {
		if (!isset($data_array['n']) || !isset($data_array['f'])) {
			return ERROR_MISS_PRM;
		} else {
			//model name
			$model_name = $data_array['n'];
			if (strlen($model_name) > 50 || strlen($model_name) == 0) {
				return ERROR_WRONG_PRM;
			}
			
			//model gcode
			if (is_array($data_array['f'])) {
				$model_gcode = $data_array['f'];
			} else {
				return ERROR_INTERNAL;
			}
			if ($model_gcode['file_size'] > PRINTLIST_MAX_GCODE_SIZE) {
				return ERROR_TOOBIG_MODEL;
			}
			if (($model_gcode['file_type'] != 'application/octet-stream' 
					 && $model_gcode['file_type'] != 'text/plain')
					|| $model_gcode['file_ext'] != '.gcode') {
				return ERROR_WRONG_FORMAT;
			}
			
			//model description
			if (isset($data_array['d'])) {
				$model_desp = $data_array['d'];
				if (strlen($model_desp) > 255) { // || strlen($model_name) == 0 //already check isset()
					return ERROR_WRONG_PRM;
				}
			}
			
			//model print time
			if (isset($data_array['t'])) {
				$model_printtime = (int) $data_array['t'];
				if ($model_printtime <= 0) {
					return ERROR_WRONG_PRM;
				}
			}

			//model filament1 length
			if (isset($data_array['l1'])) {
				$model_filament1 = (int) $data_array['l1'];
				if ($model_filament1 <= 0) {
					return ERROR_WRONG_PRM;
				}
			}

			//model filament1 length
			if (isset($data_array['l2'])) {
				$model_filament2 = (int) $data_array['l2'];
				if ($model_filament2 <= 0) {
					return ERROR_WRONG_PRM;
				}
			}
			
			//model picture 1
			if (isset($data_array['p1'])) {
				if (is_array($data_array['p1'])) {
					$model_pictures[] = $data_array['p1'];
				} else {
					return ERROR_INTERNAL;
				}
			}
			//model picture 2
			if (isset($data_array['p2'])) {
				if (is_array($data_array['p2'])) {
					$model_pictures[] = $data_array['p2'];
				} else {
					return ERROR_INTERNAL;
				}
			}
			//model picture 3
			if (isset($data_array['p3'])) {
				if (is_array($data_array['p3'])) {
					$model_pictures[] = $data_array['p3'];
				} else {
					return ERROR_INTERNAL;
				}
			}
			//model picture 4
			if (isset($data_array['p4'])) {
				if (is_array($data_array['p4'])) {
					$model_pictures[] = $data_array['p4'];
				} else {
					return ERROR_INTERNAL;
				}
			}
			//model picture 1
			if (isset($data_array['p5'])) {
				if (is_array($data_array['p5'])) {
					$model_pictures[] = $data_array['p5'];
				} else {
					return ERROR_INTERNAL;
				}
			}
			
			//model pictures
			foreach ($model_pictures as $picture) {
				if ($picture['file_size'] > PRINTLIST_MAX_PIC_SIZE) {
					return ERROR_TOOBIG_FILE;
				}
				if ($picture['is_image'] != TRUE
						|| ($picture['image_type'] != 'jpeg'
						 && $picture['image_type'] != 'png')) {
					return ERROR_WRONG_FORMAT;
				}
			}
		}
	} else {
		return ERROR_INTERNAL;
	}

	//==========================================================
	//treat parameters
	//==========================================================
	//model name, description, duration, filament1+2
	$json_data = array(
			PRINTLIST_TITLE_ID		=> md5($model_name),
			PRINTLIST_TITLE_NAME	=> $model_name,
			PRINTLIST_TITLE_DESP	=> $model_desp,
			PRINTLIST_TITLE_TIME	=> $model_printtime,
			PRINTLIST_TITLE_LENG_F1	=> $model_filament1,
			PRINTLIST_TITLE_LENG_F2	=> $model_filament2,
	// 		PRINTLIST_TITLE_GCODE	=> NULL,
			PRINTLIST_TITLE_PIC		=> array(),
	);
	$model_path = $printlist_basepath . $model_name . '/';
	//always create a new folder to overwrite the old one
	if (file_exists($model_path)) {
		delete_files($model_path, TRUE); //there are no folders inside normally, but we delete all
		rmdir($model_path);
	}
	mkdir($model_path);
	
	//model gcode
	rename($model_gcode['full_path'], $model_path . PRINTLIST_FILE_GCODE);
// 	//if we don't want to fix the filename of gcode, and then store it in json info
// 	$tmp_string = 'gcode' . time() . $model_gcode['file_ext']; //new gcode name
// 	rename($model_gcode['full_path'], $model_path . $tmp_string);
// 	$json_data[PRINTLIST_TITLE_GCODE] = $model_path . $tmp_string; //full path in json
	
	//model picture
	foreach ($model_pictures as $picture) {
		$i_tmp = isset($i_tmp) ? ++$i_tmp : 1;
		$tmp_string = 'img' . $i_tmp . '_' . time() . $picture['file_ext']; //new picture name
		rename($picture['full_path'], $model_path . $tmp_string);
		$json_data[PRINTLIST_TITLE_PIC][] = $tmp_string;
	}
	
	//write model json info
	try {
		$fp = fopen($model_path . PRINTLIST_FILE_JSON, 'w');
		fwrite($fp, json_encode($json_data));
		fclose($fp);
	} catch (Exception $e) {
		return ERROR_INTERNAL;
	}
	
	return ERROR_OK;
}

function ModelList_delete($id_model_del) {
	$model_cr = ModelList__find($id_model, $model_path);
	if (($model_cr == ERROR_OK) && $model_path) {
		delete_files($model_path, TRUE); //there are no folders inside normally, but we delete all
		rmdir($model_path);
		return ERROR_OK;
	} else {
		return ERROR_UNKNOWN_MODEL;
	}
}

function ModelList_list() {
	global $CFG;
	$printlist_basepath	= $CFG->config['printlist'];
	$json_data = array();
	$tmp_array = NULL;
	
	$model_array = directory_map($printlist_basepath, 1);
	foreach ($model_array as $model_name) {
		$model_path = $printlist_basepath . $model_name . '/';
		$nb_pic = 0;
		
		try {
			$tmp_array = json_read($model_path . PRINTLIST_FILE_JSON, TRUE);
			if ($tmp_array['error']) {
				throw new Exception('read json error');
			}
		} catch (Exception $e) {
			return json_encode($json_data); //TODO how about return ERROR_INTERNAL here?
		}
		$tmp_array['json'][PRINTLIST_TITLE_ID] = md5($model_name); //add model id to data array
		
		//blind picture url
		if (isset($tmp_array['json'][PRINTLIST_TITLE_PIC])
				&& count($tmp_array['json'][PRINTLIST_TITLE_PIC])) {
			$nb_pic = count($tmp_array['json'][PRINTLIST_TITLE_PIC]);
			for ($i=0; $i < $nb_pic; $i++) { //we cannot use foreach to change value
				$tmp_array['json'][PRINTLIST_TITLE_PIC][$i] = PRINTLIST_GETPIC_BASE_WEB
					. '?' . PRINTLIST_GETPIC_PRM_MID . '=' . md5($model_name)
					. '&' . PRINTLIST_GETPIC_PRM_PIC . '=' . $i;
			}
		}
		
		$json_data[] = $tmp_array['json']; //asign final data
	}
	
	return json_encode($json_data);
}

function ModelList_getPic($id_model, $id_picture, &$path_pid) {
	$json_data = NULL;
	$model_path = NULL;
	$model_cr = 0;
	
	if ($id_picture <= 0 || $id_picture > PRINTLIST_MAX_FILE_PIC) {
		return ERROR_UNKNOWN_PIC;
	}
	--$id_picture; //adapt id number

	$model_cr = ModelList__find($id_model, $model_path);
	if (($model_cr == ERROR_OK) && $model_path) {
		try {
			$json_data = json_read($model_path . PRINTLIST_FILE_JSON, TRUE);
			if ($json_data['error']) {
				throw new Exception('read json error');
			}
		} catch (Exception $e) {
			return ERROR_INTERNAL;
		}
		
		if (isset($json_data['json'][PRINTLIST_TITLE_PIC][$id_picture])) {
			$path_pid = $model_path . $json_data['json'][PRINTLIST_TITLE_PIC][$id_picture]; //image file full path
			return ERROR_OK;
		} else {
			return ERROR_UNKNOWN_PIC;
		}
	} else {
		return ERROR_UNKNOWN_MODEL;
	}
}

function ModelList_print($id_model) {
	$json_data = NULL;
	$gcode_path = NULL;
	$model_path = NULL;

	$model_cr = ModelList__find($id_model, $model_path);
	if (($model_cr == ERROR_OK) && $model_path) {
//		//if we don't fix the filename of gcode
// 		try {
// 			$json_data = json_read($model_path . PRINTLIST_FILE_JSON);
// 			if ($json_data['error']) {
// 				throw new Exception('read json error');
// 			}
// 		} catch (Exception $e) {
// 			return ERROR_INTERNAL;
// 		}
// 		$gcode_path = $json_data['json'][PRINTLIST_TITLE_GCODE];
		$gcode_path = $model_path . PRINTLIST_FILE_GCODE;
		
		//TODO finish me ModelList_print($id_model) by passing gcode to printer
		
		return ERROR_OK;
	} else {
		return ERROR_UNKNOWN_MODEL;
	}
}

//internal function
function ModelList__find($id_model_find, &$model_path) {
	global $CFG;
	$printlist_basepath	= $CFG->config['printlist'];
	$model_path = NULL;
	
	if (strlen($id_model_find) != 32) { //default length of md5
		return ERROR_UNKNOWN_MODEL;
	}

	$model_array = directory_map($printlist_basepath, 1);
	foreach ($model_array as $model_name) {
		if (!is_dir($printlist_basepath . $model_name)) { //check whether it is a folder or not
			continue;
		}
		$id_model_cal = md5($model_name);
		if ($id_model_cal == $id_model_find) {
			$model_path = $printlist_basepath . $model_name . '/';
			break; //leave directly the loop when finding the correct folder
		}
	}
	
	return ERROR_OK;
}
