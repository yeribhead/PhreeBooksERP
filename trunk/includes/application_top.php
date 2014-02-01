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
//  Path: /includes/application_top.php
//
define('PAGE_EXECUTION_START_TIME', microtime(true));
if (!get_cfg_var('safe_mode')) {
	if (ini_get('max_execution_time') < 60) set_time_limit(60);
}
$force_reset_cache = false;
// set php_self in the local scope
if (!isset($PHP_SELF)) $PHP_SELF = $_SERVER['PHP_SELF'];
// Check for application configuration parameters
if     (file_exists('includes/configure.php')) { require('includes/configure.php'); } 
elseif (file_exists('install/index.php')) { header('Location: install/index.php'); exit(); ob_end_flush(); }
else   die('Phreedom cannot find the configuration file. Aborting!');
// Load some path constants
$path = (ENABLE_SSL_ADMIN == 'true' ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_ADMIN;
if (!defined('PATH_TO_MY_FILES')) define('PATH_TO_MY_FILES','my_files/');
define('DIR_WS_FULL_PATH', $path);	// full http path (or https if secure)
define('DIR_WS_MODULES',   'modules/');
define('DIR_WS_MY_FILES',  PATH_TO_MY_FILES);
// load some file system constants
define('DIR_FS_INCLUDES',  DIR_FS_ADMIN . 'includes/');
define('DIR_FS_MODULES',   DIR_FS_ADMIN . 'modules/');
define('DIR_FS_MY_FILES',  DIR_FS_ADMIN . PATH_TO_MY_FILES);
define('DIR_FS_THEMES',    DIR_FS_ADMIN . 'themes/');
define('FILENAME_DEFAULT', 'index');
// set the type of request (secure or not)
$request_type = (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on' || $_SERVER['HTTPS'] == '1' || strstr(strtoupper($_SERVER['HTTP_X_FORWARDED_BY']),'SSL') || strstr(strtoupper($_SERVER['HTTP_X_FORWARDED_HOST']),'SSL'))) ? 'SSL' : 'NONSSL';
// define the inventory types that are tracked in cost of goods sold
define('COG_ITEM_TYPES','si,sr,ms,mi,ma,sa');
@ini_set('session.gc_maxlifetime', (SESSION_TIMEOUT_ADMIN < 900 ? (SESSION_TIMEOUT_ADMIN + 900) : SESSION_TIMEOUT_ADMIN));
$_REQUEST = array_merge($_GET, $_POST);
session_start();
$session_started = true;
// set the language
if ( !isset($_SESSION['language']) && isset($_GET['language'])) {
	$_SESSION['language'] = $_GET['language']; 
}elseif (!isset($_SESSION['language'])) { 
	$_SESSION['language'] = defined('DEFAULT_LANGUAGE') ? DEFAULT_LANGUAGE : 'en_us'; 
}
// load general language translation, Check for global define overrides first
$path = DIR_FS_MODULES . 'phreedom/custom/language/' . $_SESSION['language'] . '/language.php';
if (file_exists($path)) { require_once($path); }
$path = DIR_FS_MODULES . 'phreedom/language/' . $_SESSION['language'] . '/language.php';
if (file_exists($path)) { require_once($path); } 
else { require_once(DIR_FS_MODULES . 'phreedom/language/en_us/language.php'); }
// define general functions and classes used application-wide
require_once(DIR_FS_MODULES  . 'phreedom/defaults.php');
require_once(DIR_FS_INCLUDES . 'common_functions.php');
set_error_handler("PhreebooksErrorHandler");
set_exception_handler('PhreebooksExceptionHandler');
spl_autoload_register('Phreebooks_autoloader', true, false);
// pull in the custom language over-rides for this module/page
$custom_path = DIR_FS_MODULES . "$module/custom/pages/$page/extra_defines.php";
if (file_exists($custom_path)) { include($custom_path); }
gen_pull_language($module);
define('DIR_WS_THEMES', 'themes/' . (isset($_SESSION['admin_prefs']['theme']) ? $_SESSION['admin_prefs']['theme'] : DEFAULT_THEME) . '/');
define('MY_COLORS',isset($_SESSION['admin_prefs']['colors'])?$_SESSION['admin_prefs']['colors']:DEFAULT_COLORS);
define('MY_MENU',  isset($_SESSION['admin_prefs']['menu'])  ?$_SESSION['admin_prefs']['menu']  :DEFAULT_MENU);
define('DIR_WS_IMAGES', DIR_WS_THEMES . 'images/');
if (file_exists(DIR_WS_THEMES . 'icons/')) { define('DIR_WS_ICONS',  DIR_WS_THEMES . 'icons/'); }
else { define('DIR_WS_ICONS', 'themes/default/icons/'); } // use default
$messageStack 	= new \core\classes\messageStack;
$toolbar      	= new \core\classes\toolbar;
$currencies  	= new \core\classes\currencies;
// determine what company to connect to
if ($_REQUEST['action']=="validate") $_SESSION['company'] = $_POST['company'];
if (isset($_SESSION['company']) && $_SESSION['company'] != '' && file_exists(DIR_FS_MY_FILES . $_SESSION['company'] . '/config.php')) {
	define('DB_DATABASE', $_SESSION['company']);
	require_once(DIR_FS_MY_FILES . $_SESSION['company'] . '/config.php');
  	define('DB_SERVER_HOST',DB_SERVER); // for old PhreeBooks installs
	//registry::storeCoreObjects();
	// Load queryFactory db classes
	$dsn = DB_TYPE.":dbname=".$db_company.";host=".DB_SERVER_HOST;
	try {
		$db = new PDO($dsn, DB_SERVER_USERNAME, DB_SERVER_PASSWORD);
		$db->setAttribute(PDO::ERRMODE_EXCEPTION, PDO::CASE_NATURAL); 
	} catch (PDOException $e) {
		trigger_error('database connection failed: ' . $e->getMessage() , E_USER_ERROR);
	}
	try{
	 	$db->repare("select configuration_key, configuration_value from " . DB_PREFIX . "configuration");
	    $db->execute();
	    foreach($db->fetch(PDO::FETCH_LAZY) as $row){
	  		define($row['configuration_key'],$row['configuration_value']);
	  	}
    }catch (PDOException $e) {
    	trigger_error(LOAD_CONFIG_ERROR . $e->getMessage(), E_USER_ERROR);
    }
  	// search the list modules and load configuration files and language files
  	gen_pull_language('phreedom', 'menu');
  	gen_pull_language('phreebooks', 'menu');
  	require(DIR_FS_MODULES . 'phreedom/config.php');
  	$loaded_modules = array();
  	$admin_classes = array();
  	$dirs = scandir(DIR_FS_MODULES);
  	foreach ($dirs as $dir) { // first pull all module language files, loaded or not
    	if ($dir == '.' || $dir == '..') continue;
    	gen_pull_language($dir, 'menu');
		if (defined('MODULE_' . strtoupper($dir) . '_STATUS')) { // module is loade
	  		$loaded_modules[] = $dir;
      		require_once(DIR_FS_MODULES . $dir . '/config.php');
    	} 
  		if (is_dir(DIR_FS_MODULES . $dir)){
    		$class = "\\$dir\classes\admin";
	  		$admin_classes[$dir]  = new $class;
		}
  	}
  	uasort($admin_classes, "arange_object_by_sort_order");
	// pull in the custom language over-rides for this module (to pre-define the standard language)
  	$path = DIR_FS_MODULES . "$module/custom/pages/$page/extra_menus.php";
  	if (file_exists($path)) { include($path); }
  	$currencies->load_currencies();
}
$prefered_type = ENABLE_SSL_ADMIN == 'true' ? 'SSL' : 'NONSSL';
if ($request_type <> $prefered_type) gen_redirect(html_href_link(FILENAME_DEFAULT, '', 'SSL')); // re-direct if SSL request not matching actual request
?>