<?php
// +-----------------------------------------------------------------+
// |                   PhreeBooks Open Source ERP                    |
// +-----------------------------------------------------------------+
// | Copyright(c) 2008-2013 PhreeSoft, LLC (www.PhreeSoft.com)       |
// +-----------------------------------------------------------------+
// | This program is free software: you can redistribute it and/or   |
// | modify it under the terms of the GNU General Public License as  |
// | published by the Free Software Foundation, either version 3 of  |
// | the License, or any later version.                              |
// |                                                                 |
// | This program is distributed in the hope that it will be useful, |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of  |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the   |
// | GNU General Public License for more details.                    |
// +-----------------------------------------------------------------+
// Path: /index.php
//
ob_start();
ini_set('log_errors','1'); 
ini_set('display_errors', '1');
error_reporting(E_ALL ^ E_NOTICE);
if (isset($_POST['module']))    $module = $_POST['module'];
elseif (isset($_GET['module'])) $module = $_GET['module'];
else                            $module = 'phreedom';
if (isset($_POST['page']))      $page = $_POST['page'];
elseif (isset($_GET['page']))   $page = $_GET['page'];
else                     		$page = 'main';

require_once('includes/application_top.php');
if (!\core\classes\user::is_validated()) {
  	if ($page == 'ajax'){
		echo createXmlHeader() . xmlEntry('error', SORRY_YOU_ARE_LOGGED_OUT) . createXmlFooter();
		die;
  	}
  	if (isset($_REQUEST['module'])	&& !$_SESSION['pb_module'])	$_SESSION['pb_module']	= $_REQUEST['module'];
  	if (isset($_REQUEST['page']) 	&& !$_SESSION['pb_page']) 	$_SESSION['pb_page'] 	= $_REQUEST['page'];
  	if (isset($_REQUEST['jID']) 	&& !$_SESSION['pb_jID'])	$_SESSION['pb_jID']		= $_REQUEST['jID'];
  	if (isset($_REQUEST['type']) 	&& !$_SESSION['pb_type'])	$_SESSION['pb_type']	= $_REQUEST['type'];
  	if (isset($_REQUEST['list'])	&& !$_SESSION['pb_list'])	$_SESSION['pb_list']	= $_REQUEST['list'];
	$module = 'phreedom';
	$page   = 'main';
  	if (!isset($_REQUEST['action']) || !in_array($_REQUEST['action'], array('validate','pw_lost_sub','pw_lost_req'))){
   		$_REQUEST['action'] = 'login';
  	}
}
if ($page == 'ajax') {
  $pre_process_path = DIR_FS_MODULES . $module . 'custom/ajax/' . $_GET['op'] . '.php';
  if (file_exists($pre_process_path)) { require($pre_process_path); die; }
  $pre_process_path = DIR_FS_MODULES . $module . '/ajax/' . $_GET['op'] . '.php';
  if (file_exists($pre_process_path)) { require($pre_process_path); die; }
  die; // go no further
}
$custom_html      = false;
$include_header   = false;
$include_footer   = false;
$include_template = 'template_main.php';
$pre_process_path = DIR_FS_MODULES . $module . '/pages/' . $page . '/pre_process.php';
if (file_exists($pre_process_path)) { define('DIR_FS_WORKING', DIR_FS_MODULES . $module . '/'); }
  else trigger_error('No pre_process file, looking for the file: ' . $pre_process_path, E_USER_ERROR);
require($pre_process_path); 
if (file_exists(DIR_FS_WORKING . 'custom/pages/' . $page . '/' . $include_template)) {
  $template_path = DIR_FS_WORKING . 'custom/pages/' . $page . '/' . $include_template;
} else {
  $template_path = DIR_FS_WORKING . 'pages/' . $page . '/' . $include_template;
}
require('includes/template_index.php');
require('includes/application_bottom.php');
ob_end_flush();
?>