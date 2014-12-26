<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

session_start();
set_include_path(get_include_path() . PATH_SEPARATOR . BASEPATH . '../assets/facebook_api/src');
require_once BASEPATH . '../assets/facebook_api/autoload.php';
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookSDKException;
use Facebook\GraphObject;

class Share extends MY_Controller
{	
	public function index($fb_title = "", $fb_desc = "")
	{
		FacebookSession::setDefaultApplication('406644606152497', 'adc295f31a84a0fb8999ff0d59769118');
		$helper = new FacebookRedirectLoginHelper('http://localhost:81/share');
		if (isset($_SESSION) && isset($_SESSION['fb_token']))
		{
			// create new session from the existing PHP sesson
			$session = new FacebookSession($_SESSION['fb_token']);
			try
			{
				// validate the access_token to make sure it's still valid
				if (!$session->validate())
					$session = null;
			}
			catch (Exception $e)
			{
				// catch any exceptions and set the sesson null
				$session = null;
				echo 'No session: '.$e->getMessage();
			}
		}
		else if (empty($session))
		{
			// the session is empty, we create a new one
			try
			{
				// the visitor is redirected from the login, let's pickup the session
				$session = $helper->getSessionFromRedirect();
			}
			catch( FacebookRequestException $e )
			{
				// Facebook has returned an error
				echo 'Facebook (session) request error: '.$e->getMessage();
			}
			catch( Exception $e )
			{
				// Any other error
				echo 'Other (session) request error: '.$e->getMessage();
			}
		}
		if (isset( $session ))
		{
			// store the session token into a PHP session
			$_SESSION['fb_token'] = $session->getToken();
			// and create a new Facebook session using the cururent token
			// or from the new token we got after login
			$session = new FacebookSession($session->getToken());
			try
			{ 
				$this->lang->load('share/facebook_share', $this->config->item('language'));
				$this->upload($fb_title == "" ? t('fb_title') : $fb_title, $fb_desc == "" ? t('fb_desc') : $fb_desc);
			}
			catch (FacebookRequestException $e)
			{
				// show any error for this facebook request
				echo 'Facebook (post) request error: '.$e->getMessage();
			}
		}
		else 
		{
			$loginUrl = $helper->getLoginUrl(array('publish_actions'));
			echo '<a href="' . $loginUrl . '">Click</a>';
		}
	}
	
	private function upload($video_title, $video_desc)
	{
		$this->load->helper('zimapi');
		$file_url = ZIMAPI_FILEPATH_TIMELAPSE;
		$file = fopen($file_url, "rb");
		$video = fread($file, filesize($file_url));
		fclose($file);
		$destination = "https://graph-video.facebook.com/me/videos?access_token=" . $_SESSION['fb_token'];
		$eol = "\r\n";
		$data = '';
		
		$mime_boundary=md5(time());
		
		$data .= '--' . $mime_boundary . $eol;
		$data .= 'Content-Disposition: form-data; name="access_token"' . $eol . $eol;
		$data .= $_SESSION['fb_token'] . $eol;
		$data .= '--' . $mime_boundary . $eol;
		$data .= 'Content-Disposition: form-data; name="description"' . $eol . $eol;
		$data .= $video_desc . $eol;
		$data .= '--' . $mime_boundary . $eol;
		$data .= 'Content-Disposition: form-data; name="title"' . $eol . $eol;
		$data .= $video_title. $eol;
		$data .= '--' . $mime_boundary . $eol;
		$data .= 'Content-Disposition: form-data; name="source"; filename="timelapse.mp4"' . $eol;
		$data .= 'Content-Type: text/plain' . $eol;
		$data .= 'Content-Transfer-Encoding: base64' . $eol . $eol;
		$data .= $video . $eol;
		$data .= "--" . $mime_boundary . "--" . $eol . $eol;

		$params = array('http' => array(
				'method' => 'POST',
				'ignore_errors' => true,
				'header' => 'Content-Type: multipart/form-data; boundary=' . $mime_boundary . $eol,
				'content' => $data
		));
		
		$ctx = stream_context_create($params);
		$response = @file_get_contents($destination, FILE_TEXT, $ctx);
		$this->output->set_header("Location: /printdetail/timelapse");
	}

	public function facebook_form()
	{
		$this->load->library('parser');
		$this->lang->load('share/facebook_share', $this->config->item('language'));

		if ($this->input->server('REQUEST_METHOD') == 'POST')
		{
			$this->index($_POST['fb_title'], $_POST['fb_desc']);
			return;
		}
		$this->load->helper('zimapi');
		$array_status = array();
		$model_displayname = NULL;
		if (CoreStatus_checkInIdle($status_current, $array_status) && array_key_exists(CORESTATUS_TITLE_PRINTMODEL, $array_status))
		{
			$model_id = NULL;
			$abb_cartridge = NULL;
				
			switch ($array_status[CORESTATUS_TITLE_PRINTMODEL])
			{
				case CORESTATUS_VALUE_MID_SLICE:
					$preset_id = NULL;
					$model_filename = array();
					$this->load->helper('slicer');
					if (ERROR_OK == Slicer_getModelFile(0, $model_filename, TRUE))
					{
						foreach($model_filename as $model_basename)
						{
							if (strlen($model_displayname))
								$model_displayname .= ' + ' . $model_basename;
							else
								$model_displayname = $model_basename;
						}
					}
					else
						$model_displayname = t('timelapse_info_modelname_slice');
					break;
				default:
					// treat as pre-sliced model
					$model_data = array();
					$model_displayname = "";
					if (is_null($model_id))
					{
						$this->load->helper('printlist');
						$model_id = $array_status[CORESTATUS_TITLE_PRINTMODEL];
					}
					if (ERROR_OK == ModelList__getDetailAsArray($model_id, $model_data, TRUE))
					{
						$model_displayname = $model_data[PRINTLIST_TITLE_NAME];
					}
					break;
			}
		}
		
		$data = array(
				'fb_title'				=> t('fb_title') . $model_displayname,
				'fb_desc'				=> htmlspecialchars(t('fb_desc')),
				'title_label'			=> t('title_label'),
				'desc_label'			=> t('desc_label'),
				'back'					=> t('back'),
				'upload_to_fb'			=> t('upload_to_fb')
		);
		
		$body_page = $this->parser->parse('template/share/facebook_share', $data, TRUE);
		
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . t('facebook_title') . '</title>',
				'contents'		=> $body_page
		);
		$this->parser->parse('template/basetemplate', $template_data);
		return;
	}

	public function youtube_form()
	{
		$this->load->library('parser');
		$this->lang->load('youtube_form', $this->config->item('language'));
	
		$this->load->helper('zimapi');
		$array_status = array();
		$model_displayname = NULL;
		if (CoreStatus_checkInIdle($status_current, $array_status) && array_key_exists(CORESTATUS_TITLE_PRINTMODEL, $array_status))
		{
			$model_id = NULL;
			$abb_cartridge = NULL;
				
			switch ($array_status[CORESTATUS_TITLE_PRINTMODEL])
			{
				case CORESTATUS_VALUE_MID_SLICE:
					$preset_id = NULL;
					$model_filename = array();
					$this->load->helper('slicer');
					if (ERROR_OK == Slicer_getModelFile(0, $model_filename, TRUE))
					{
						foreach($model_filename as $model_basename)
						{
							if (strlen($model_displayname))
								$model_displayname .= ' + ' . $model_basename;
							else
								$model_displayname = $model_basename;
						}
					}
					else
						$model_displayname = t('timelapse_info_modelname_slice');
					break;
				default:
					// treat as pre-sliced model
					$model_data = array();
					$model_displayname = "";
					if (is_null($model_id))
					{
						$this->load->helper('printlist');
						$model_id = $array_status[CORESTATUS_TITLE_PRINTMODEL];
					}
					if (ERROR_OK == ModelList__getDetailAsArray($model_id, $model_data, TRUE))
					{
						$model_displayname = $model_data[PRINTLIST_TITLE_NAME];
					}
					break;
			}
		}
		if ($this->input->server('REQUEST_METHOD') == 'POST')
		{
			$this->load->library("session");
				
			$title = isset($_POST['yt_title']) ? $_POST['yt_title'] : t('yt_title');
			$description = isset($_POST['yt_description']) ? $_POST['yt_description'] : t('yt_desc');
				
			$tags = explode(',', $_POST['yt_tags'] ? $_POST['yt_tags'] : t('yt_tags'));
			$tags = array_map('trim', $tags);
			$video_infos = array(
					'yt_title'		=> $title,
					'yt_tags'		=> $tags,
					'yt_desc'		=> $description,
					'yt_privacy'	=> $_POST["yt_privacy"]
			);
			$this->session->set_userdata($video_infos);
			$this->output->set_header("Location: /share/connect_google");
		}
	
		$data = array(
				'yt_title'				=> t('yt_title') . $model_displayname,
				'yt_tags'				=> t('yt_tags'),
				'yt_desc'				=> t('yt_desc'),
				'yt_privacy_public'		=> t('yt_privacy_public'),
				'yt_privacy_private'	=> t('yt_privacy_private'),
				'yt_privacy_unlisted'	=> t('yt_privacy_unlisted'),
				'upload_to_yt'			=> t('upload_to_yt'),
				'title_label'			=> t('title_label'),
				'desc_label'			=> t('desc_label'),
				'tags_label'			=> t('tags_label'),
				'privacy_label'			=> t('privacy_label'),
				'back'					=> t('back')
		);
		$body_page = $this->parser->parse('template/share/youtube_form', $data, TRUE);
	
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . t('youtube_title') . '</title>',
				'contents'		=> $body_page
		);
		$this->parser->parse('template/basetemplate', $template_data);
		return;
	}
	
	public function video_upload()
	{
		$state = $_GET['state'];
		$code = $_GET['code'];
		$this->load->library('parser');
		$this->lang->load('printdetail', $this->config->item('language'));
		$data = array(
				'state'					=> $state,
				'code'					=> $code,
				'uploading'				=> t('uploading'),
				'yt_upload_popup_text'	=> t('yt_upload_popup_text'),
				'yt_callback_ok'		=> t('yt_callback_ok'),
		);
		$body_page = $this->parser->parse('template/share/video_upload', $data, TRUE);
	
		// parse all page
		$template_data = array(
				'lang'			=> $this->config->item('language_abbr'),
				'headers'		=> '<title>' . 'Zim - Zim-motion' . '</title>',
				'contents'		=> $body_page
		);
		$this->parser->parse('template/basetemplate', $template_data);
		return;
	}
	
	public function connect_google($in_upload_state = "")
	{
		set_include_path(get_include_path() . PATH_SEPARATOR . BASEPATH . '../assets/google_api/src');
		require_once 'Google/Client.php';
		require_once 'Google/Service/YouTube.php';
		$this->load->library('session');
	
		$client = new Google_Client();
		$client->setApplicationName("Test youtube upload");
		$client->setClientId("607574717756-dki0mh9seat7g8rsj34rtd79aeh47oo3.apps.googleusercontent.com");
		$client->setClientSecret("yX_irSTlQVjGM5miuHlhaDHg");
		$client->setScopes('https://www.googleapis.com/auth/youtube');
		$redirect = filter_var('https://sso.zeepro.com/redirect.ashx', FILTER_SANITIZE_URL);
		$client->setRedirectUri($redirect);
		$client->setAccessType('offline');
	
		$youtube = new Google_Service_YouTube($client);
	
		if (isset($_GET['code']))
		{
			if (strval($this->session->userdata('state')) !== strval($_GET['state']))
			{
				var_dump($this->session->all_userdata());
				die('The session state did not match.');
			}
			$client->authenticate($_GET['code']);
			$this->session->set_userdata('token', $client->getAccessToken());
			$this->session->set_userdata('code', $_GET['code']);
		}
	
		if ($this->session->userdata('token') !== FALSE)
		{
			$client->setAccessToken($this->session->userdata('token'));
			if ($client->isAccessTokenExpired())
			{
				$currentTokenData = json_decode($this->session->userdata('token'));
				if (isset($currentTokenData->refresh_token))
				{
					$client->refreshToken($tokenData->refresh_token);
				}
			}
		}
		if ($client->getAccessToken() && $in_upload_state != "")
		{
			$this->load->helper('zimapi');
			try
			{
				$videoPath = ZIMAPI_FILEPATH_TIMELAPSE;
	
				// Create a snippet with title, description, tags and category ID
				// Create an asset resource and set its snippet metadata and type.
				// This example sets the video's title, description, keyword tags, and
				// video category.
				$snippet = new Google_Service_YouTube_VideoSnippet();
				$snippet->setTitle($this->session->userdata('yt_title'));
				$snippet->setDescription($this->session->userdata("yt_desc"));
				$snippet->setTags($this->session->userdata("yt_tags"));
					
				// Numeric video category. See https://developers.google.com/youtube/v3/docs/videoCategories/list
				$snippet->setCategoryId("22");
					
				// Set the video's status to "public". Valid statuses are "public", "private" and "unlisted".
				$status = new Google_Service_YouTube_VideoStatus();
				$status->privacyStatus = $this->session->userdata('yt_privacy');
					
				// Associate the snippet and status objects with a new video resource.
				$video = new Google_Service_YouTube_Video();
				$video->setSnippet($snippet);
				$video->setStatus($status);
					
				// Specify the size of each chunk of data, in bytes. Set a higher value for
				// reliable connection as fewer chunks lead to faster uploads. Set a lower
				// value for better recovery on less reliable connections.
				$chunkSizeBytes = 1 * 1024 * 1024;
					
				// Setting the defer flag to true tells the client to return a request which can be called
				// with ->execute(); instead of making the API call immediately.
				$client->setDefer(true);
					
				// Create a request for the API's videos.insert method to create and upload the video.
				$insertRequest = $youtube->videos->insert("status,snippet", $video);
					
				// Create a MediaFileUpload object for resumable uploads.
				$media = new Google_Http_MediaFileUpload($client, $insertRequest, 'video/mp4', null, true, $chunkSizeBytes);
				$media->setFileSize(filesize($videoPath));
	
				// Read the media file and upload it chunk by chunk.
				$status = false;
				$handle = fopen($videoPath, "rb");
				while (!$status && !feof($handle))
				{
					$chunk = fread($handle, $chunkSizeBytes);
					$status = $media->nextChunk($chunk);
				}
				fclose($handle);
				$client->setDefer(false);
				$this->session->unset_userdata(array('yt_title', 'yt_desc', 'yt_tags', 'yt_privacy'));
				echo "<h3>Video Uploaded</h3><ul>";
				echo sprintf('<li>%s (%s)</li>', $status['snippet']['title'], $status['id']);
				echo '</ul>';
			}
			catch (Google_ServiceException $e)
			{
				$this->_exitWithError500(sprintf('<p>A service error occurred: <code>%s</code></p>',
						htmlspecialchars($e->getMessage())));
			}
			catch (Google_Exception $e)
			{
				$this->_exitWithError500(sprintf('<p>An client error occurred: <code>%s</code></p>',
						htmlspecialchars($e->getMessage())));
			}
			$this->session->set_userdata('token', $client->getAccessToken());
		}
		else
		{
			$this->load->helper(array('zimapi', 'corestatus'));
			$prefix = CoreStatus_checkTromboning() ? 'https://' : 'http://';
			$data = array('printersn' => ZimAPI_getSerial(), 'URL' => $prefix . $_SERVER['HTTP_HOST'] . '/share/video_upload');
	
			$options = array('http' => array('header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					'method'  => 'POST',
					'content' => http_build_query($data)));
			$context = stream_context_create($options);
			@file_get_contents('https://sso.zeepro.com/url.ashx', false, $context);
			$result = substr($http_response_header[0], 9, 3);
			if ($result == 200)
			{
				//echo 'ca marche';
			}
			$state = ZimAPI_getSerial();
			$client->setState($state);
			$this->session->set_userdata('state', $state);
			$authUrl = $client->createAuthUrl();
			$this->output->set_header("Location: " . $authUrl);
		}
		return;
	}
}