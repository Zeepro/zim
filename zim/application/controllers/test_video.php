<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

define('CORESTATUS_PRM_CAMERA_START',
		' -v quiet -r 25 -s 320x240 -f video4linux2 -i /dev/video0 -vf "crop=240:240:40:0, transpose=2" -minrate 256k -maxrate 256k -bufsize 256k -map 0 -force_key_frames "expr:gte(t,n_forced*5)" -c:v libx264 -crf 35 -profile:v baseline -b:v 256k -pix_fmt yuv420p -flags -global_header -f segment -segment_list /var/www/tmp/zim.m3u8 -segment_time 1 -segment_format mpeg_ts -segment_list_type m3u8 -segment_list_flags live -segment_list_size 5 -segment_wrap 5 /var/www/tmp/zim%d.ts');
define('CORESTATUS_PRM_CAMERA_STOP',	' stop ');


class Test_video extends CI_Controller {

	public function index() {

		$output = NULL;
		$ret_val = 0;
//		$command = 'sudo nice -n 20 ffmpeg' . CORESTATUS_PRM_CAMERA_START;
//		pclose(popen($command . ' &', 'r'));
		$command = 'sudo nice -n 20 ffmpeg' . CORESTATUS_PRM_CAMERA_START;
		pclose(popen($command . ' &', 'r'));
		
//		exec($command, $output, $ret_val);
//		if ($ret_val != 0) {
//			$CI = &get_instance();
//			$CI->load->helper('printerlog');
//			PrinterLog_logError('camera start command error', __FILE__, __LINE__);
//			return FALSE;
//		}



		$this->load->library('parser');
		$this->parser->parse('template/test_video', array('video_url' => $this->config->item('video_url')));
		
		return;
	}

}
