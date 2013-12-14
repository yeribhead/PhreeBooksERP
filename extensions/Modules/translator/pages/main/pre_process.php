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
//  Path: /modules/translator/pages/main/pre_process.php
//
$security_level = validate_user(SECURITY_TRANSLATOR_MGT);
/**************  include page specific files    *********************/
gen_pull_language('phreedom', 'admin');
require_once(DIR_FS_WORKING . 'functions/translator.php');
require_once(DIR_FS_WORKING . 'classes/translator.php');
require_once(DIR_FS_MODULES . 'phreedom/classes/backup.php');

/**************   page specific initialization  *************************/
$error       = false;
$translator  = new translator();
$backup      = new backup();
$replace     = array();
$criteria    = array();
if(!isset($_REQUEST['list'])) $_REQUEST['list'] = 1;
if ($_REQUEST['search_text'] == TEXT_SEARCH) $_REQUEST['search_text'] = '';
// load the filters
$f0 = isset($_POST['action']) ? $_POST['f0'] : $_GET['f0'];
$f1 = isset($_POST['action']) ? $_POST['f1'] : $_GET['f1'];
$f2 = isset($_POST['action']) ? $_POST['f2'] : $_GET['f2'];
$f3 = isset($_POST['action']) ? $_POST['f3'] : $_GET['f3'];

/***************   Act on the action request   *************************/
switch ($_REQUEST['action']) {
  case 'import_go': // imports existing language file constants from current install
    $mod  = db_prepare_input($_POST['mod']);
	$lang = db_prepare_input($_POST['lang']);
	switch($mod) {
	  case 'all':
	    $dirs = scandir(DIR_FS_MODULES);
		foreach ($dirs as $value) {
		  if ($value == '.' || $value == '..' || !file_exists(DIR_FS_MODULES . $value . '/config.php')) continue;
	      $dir = DIR_FS_MODULES . "$value/language/$lang/";
		  include_once(DIR_FS_MODULES . "$value/config.php"); // fetch the version number
		  $ver = defined('MODULE_'.strtoupper($value).'_VERSION') ? constant('MODULE_'.strtoupper($value).'_VERSION') : '0.1';
		  $translator->import_language($dir, $value, $lang, $ver);
		  if (is_dir(DIR_FS_MODULES . $value . '/methods')) { // there are methods to load languages
			$meths = scandir(DIR_FS_MODULES . $value . '/methods');
			foreach ($meths as $val) {
			  if ($val == '.' || $val == '..') continue;
			  $dir = DIR_FS_MODULES . "$value/methods/$val/language/$lang/";
			  @include_once(DIR_FS_MODULES . "$value/methods/$val/$val" . ".php"); // fetch the version number
			  $ver = defined('MODULE_'.strtoupper($value).'_'.strtoupper($val).'_VERSION') ? constant('MODULE_'.strtoupper($value).'_'.strtoupper($val).'_VERSION') : '0.1';
			  $translator->import_language($dir, $value.'-'.$val, $lang, $ver);
			}
		  }
		  if (is_dir(DIR_FS_MODULES . $value . '/dashboards')) { // there are dashboards to load languages
			$meths = scandir(DIR_FS_MODULES . $value . '/dashboards');
			foreach ($meths as $val) {
			  if ($val == '.' || $val == '..') continue;
			  $dir = DIR_FS_MODULES . "$value/dashboards/$val/language/$lang/";
			  @include_once(DIR_FS_MODULES . "$value/dashboards/$val/$val" . ".php"); // fetch the version number
			  $ver = defined('DASHBOARD_'.strtoupper($val).'_VERSION') ? constant('DASHBOARD_'.strtoupper($val).'_VERSION') : '0.1';
			  $translator->import_language($dir, $value.'-'.$val, $lang, $ver);
			}
		  }
		}
		// fall through to do install dir
		$mod = 'install';
	  case 'install':
	    $install_dir = db_prepare_input($_POST['install_dir']);
	    $dir = DIR_FS_ADMIN . "$install_dir/language/$lang/";
		$ver = constant('MODULE_PHREEDOM_VERSION');
		$translator->import_language($dir, $mod, $lang, $ver);
		if ($_REQUEST['action'] == 'install') break; // else fall through and do the soap directory
	  case 'soap':
	    $dir = DIR_FS_ADMIN . "soap/language/$lang/";
		$ver = constant('MODULE_PHREEDOM_VERSION');
		$translator->import_language($dir, $mod, $lang, $ver);
		break;
	  default:
	    if (strpos($mod, '-') !== false) { // it's a method or dashboard within a module
		  $parts = explode('-', $mod);
	      $dir   = DIR_FS_MODULES.$parts[0]."/methods/".$parts[1]."/language/$lang/";
		  if (is_dir($dir)) { // look for methods to load languages, else dashboards
			include_once(DIR_FS_MODULES.$parts[0]."/methods/".$parts[1]."/".$parts[1].".php");
		    $ver = defined('MODULE_'.strtoupper($parts[0]).'_'.strtoupper($parts[1]).'_VERSION') ? constant('MODULE_'.strtoupper($parts[0]).'_'.strtoupper($parts[1]).'_VERSION') : '0.1';
		  } else {
	        $dir       = DIR_FS_MODULES.$parts[0]."/dashboards/".$parts[1]."/language/$lang/";
			include_once(DIR_FS_MODULES.$parts[0]."/dashboards/".$parts[1]."/".$parts[1].".php");
		    $ver = defined('DASHBOARD_'.strtoupper($parts[1]).'_VERSION') ? constant('DASHBOARD_'.strtoupper($parts[1]).'_VERSION') : '0.1';
		  }
		} else {
	      $dir = DIR_FS_MODULES . "$mod/language/$lang/";
		  include_once(DIR_FS_MODULES . "$value/config.php");
		  $ver = defined('MODULE_'.strtoupper($mod).'_VERSION') ? constant('MODULE_'.strtoupper($mod).'_VERSION') : '0.1';
		}
		$translator->import_language($dir, $mod, $lang, $ver);
		break;
	}
	$f0 = $mod  = '';
	$f1 = $lang = '';
	$f2 = $ver  = '';
	$_REQUEST['action'] = '';
	break;
  case 'upload_go':
	$dir  = DIR_FS_MY_FILES . 'translator/upload/';
    $mod  = db_prepare_input($_POST['mod']);
	$lang = db_prepare_input($_POST['lang']);
	$translator->upload_language($dir, $mod, $lang);
	$_REQUEST['action'] = '';
	break;
  case 'create_new':
    $mod     = db_prepare_input($_POST['mod']);
	$lang    = db_prepare_input($_POST['lang']);
	$source  = db_prepare_input($_POST['source_lang']);
	$replace = isset($_POST['replace']) ? true : false;
	$history = db_prepare_input($_POST['history_lang']);
	$subs    = array();
	// pull the source language current and historical values
	$lang_list = $replace ? ("('".$lang."','".$source."','".$history."')") : ("('".$lang."','".$source."')"); 
	$sql  = "select language, version, defined_constant, translation from " . TABLE_TRANSLATOR . " 
	  where language in " . $lang_list;
	if ($mod <> 'all') $sql .= " and module = '" . $mod . "'";
	$result = $db->Execute($sql);
	while (!$result->EOF) {
	  $subs[$result->fields['language']][$result->fields['defined_constant']][$result->fields['version']] = $result->fields['translation'];
	  $result->MoveNext();
	}
	if ($mod == 'all') {
	  $sel_modules = build_mod_list();
	  array_shift($sel_modules); // remove 'all' option
	  foreach ($sel_modules as $value) {
	    $translator->convert_language($value['id'], $lang, $source, $history, $subs);
	  }
	} else {
	  $translator->convert_language($mod, $lang, $source, $history, $subs);
	}
	if ($mod <> 'all') $_REQUEST['action'] = 'edit';
	break;
  case 'edit':
	$pieces    = explode(':', $_POST['rowSeq']);
    $mod  = $pieces[0];
	$lang = $pieces[1];
	$ver  = $pieces[2];
	$f0   = $mod;
	$f1   = $lang;
	$f2   = $ver;
	break;
  case 'save':
    $mod  = db_prepare_input($_POST['mod']);
	$lang = db_prepare_input($_POST['lang']);
	$ver  = db_prepare_input($_POST['ver']);
    foreach ($_POST as $key => $trans) {
	  if (substr($key, 0, 2) <> 't:') continue; // not interested in this value
	  $temp   = explode(':', $key);
	  $id     = $temp[1];
	  $const  = $temp[2];
	  $status = isset($_POST['d:' . $id . ':' . $const]) ? '1' : '0';
	  $db->Execute("update " . TABLE_TRANSLATOR . " 
		set translation = '" . db_input($trans) . "', translated = '" . $status . "' where id = '" . $id . "'");
	}
	$messageStack->add(TEXT_TRANSLATIONS_SAVED,'success');
	$_REQUEST['action'] = 'edit';
	break;
  case 'delete':
	$pieces = explode(':', $_POST['rowSeq']);
	$db->Execute("delete from " . TABLE_TRANSLATOR . " 
	  where module = '" . $pieces[0] . "' and language = '" . $pieces[1] . "' and version = '" . $pieces[2] . "'");
	$_REQUEST['action'] = '';
	break;
  case 'export':
	$dir    = DIR_FS_MY_FILES . 'translator/';
	$pieces = explode(':', $_POST['rowSeq']);
    $mod    = $pieces[0];
	$lang   = $pieces[1];
	$ver    = $pieces[2];

	$backup->source_dir = $dir . 'export/';
	$backup->dest_dir   = $dir;
	$backup->dest_file  = $mod . '_' . $lang . '_R' . str_replace('.', '', $ver) . '.zip';
	if (!$result = $translator->export_language($mod, $lang, $ver)) {
	  $error = true;
	}
	if ($backup->make_zip('dir')) $error = true;
	if ($backup->delete_dir($backup->source_dir)) $error = true;
	if (!$error) {
	  gen_add_audit_log(TEXT_EDIT_TRANSLATION . ' (' . TEXT_EXPORT . ')', $mod . ' ' . lang . ' R' . $ver);
	  $backup->download($backup->dest_dir, $backup->dest_file); // will not return if successful
	}
	break;
  case 'export_all_go':
	$dir  = DIR_FS_MY_FILES . 'translator/';
	$lang = db_prepare_input($_POST['lang']);
	$backup->source_dir = $dir . 'export/';
	$backup->dest_dir   = $dir;
	$backup->dest_file  = 'language_' . $lang . '_Rxx.zip';
	$sel_modules = build_mod_list();
	array_shift($sel_modules); // remove 'all' option
	$have_data = false;
	foreach ($sel_modules as $value) {
	  $result = $db->Execute("select max(version) as version from " . TABLE_TRANSLATOR . " 
	    where module = '" . $value['id'] . "' and language = '" . $lang . "'");
	  $ver = $result->fields['version'];
	  if ($ver) {
	    if ($result = $translator->export_language($value['id'], $lang, $ver, true)) $have_data = true;
	  }
	}
	if ($have_data) {
	  if ($backup->make_zip('dir')) $error = true;
	  if ($backup->delete_dir($backup->source_dir)) $error = true;
	  if (!$error) {
	    gen_add_audit_log(TEXT_EDIT_TRANSLATION . ' (' . TEXT_EXPORT . ')', 'all ' . lang . ' R' . $ver);
	    $backup->download($backup->dest_dir, $backup->dest_file); // will not return if successful
	  }
	}
	break;
  case 'go_first':    $_REQUEST['list'] = 1;       break;
  case 'go_previous': $_REQUEST['list'] = max($_REQUEST['list']-1, 1); break;
  case 'go_next':     $_REQUEST['list']++;         break;
  case 'go_last':     $_REQUEST['list'] = 99999;   break;
  case 'search':
  case 'search_reset':
  case 'go_page':
  case 'filter':
  case 'filter_main':
  default:
    $mod  = $f0;
	$lang = $f1;
	$ver  = $f2;
	break;
}

/*****************   prepare to display templates  *************************/
$sel_modules      = build_mod_list();
$sel_version      = build_ver_list();
$sel_language     = build_lang_list();
$sel_translated   = build_trans_list();

$include_header   = true;
$include_footer   = false;
$include_template = 'template_main.php';

switch ($_REQUEST['action']) {
  case 'new':
	$include_template = 'template_new.php';
	define('PAGE_TITLE', TEXT_NEW_TRANSLATION);
    break;
  case 'edit':
  case 'filter':
	if (strpos($mod, '-') !== false) { // it's a module, load that language file
      $parts = explode('-', $mod);
	  load_method_language(DIR_FS_MODULES . $parts[0] . '/methods/', $parts[1]);
	} else {
	  gen_pull_language($mod);
	  gen_pull_language($mod, 'admin');	
	} 
	$heading_array = array(
	  TEXT_TRANSLATED,
	  TEXT_TRANSLATION,
	  TEXT_DEFAULT_TRANSLATION,
	  TEXT_CONSTANT,
	);
	$result      = html_heading_bar(array(), $heading_array);
	$list_header = $result['html_code'];
	if ($mod)  $criteria[] = "module = '"   . $mod  . "'";
	if ($lang) $criteria[] = "language = '" . $lang . "'";
	if ($ver)  $criteria[] = "version = '"  . $ver  . "'";
	if ($f3)   $criteria[] = $f3=='y' ? "translated = '1'" : "translated = '0'";
	$query_raw = "select id, module, language, version, pathtofile, defined_constant, translation, translated 
	  from " . TABLE_TRANSLATOR . ' where ' . implode(' and ', $criteria);
    $query_result = $db->Execute($query_raw);
	$include_template = 'template_edit.php';
	define('PAGE_TITLE', TEXT_EDIT_TRANSLATION);
	break;
  case 'import':
	$include_template = 'template_import.php';
	define('PAGE_TITLE', TEXT_IMPORT_TRANSLATION);
	break;
  case 'export_all':
	$include_template = 'template_export.php';
	define('PAGE_TITLE', TEXT_EXPORT_TRANSLATION);
	break;
  case 'upload':
	$include_template = 'template_upload.php';
	define('PAGE_TITLE', TEXT_UPLOAD_TRANSLATION);
	break;
  case 'filter_main':
  default:
	$heading_array = array(
	  'module'   => TEXT_MODULE,
	  'language' => TEXT_LANGUAGE_CODE,
	  'version'  => TEXT_VERSION,
	);
	$result      = html_heading_bar($heading_array, array(TEXT_STATISTICS, TEXT_ACTION));
	$list_header = $result['html_code'];
	$disp_order  = $result['disp_order'];
	if (!isset($_GET['list_order'])) $disp_order = 'language';
	if ($mod && $mod <> 'all')  $criteria[] = "module = '"   . $mod  . "'";
	if ($lang) $criteria[] = "language = '" . $lang . "'";
	if ($ver && $ver <> 'L') $criteria[] = "version = '"  . $ver  . "'";
	// build the list for the page selected
    if (isset($_REQUEST['search_text']) && $_REQUEST['search_text'] <> '') {
      $search_fields = array('module', 'language', 'version');
	  $criteria[] = '(' . implode(' like \'%' . $_REQUEST['search_text'] . '%\' or ', $search_fields) . ' like \'%' . $_REQUEST['search_text'] . '%\')';
    }
	$search = (sizeof($criteria) == 0) ? '' : ' where ' . implode(' and ', $criteria);
    if ($ver=='L') {
	  $query_raw  = "select SQL_CALC_FOUND_ROWS module, max(version) as version, language from " . TABLE_TRANSLATOR . $search . " group by module order by $disp_order";	
	} else {
	  $query_raw  = "select SQL_CALC_FOUND_ROWS distinct module, version, language from " . TABLE_TRANSLATOR . $search . " order by $disp_order";
	}
    $query_result = $db->Execute($query_raw, (MAX_DISPLAY_SEARCH_RESULTS * ($_REQUEST['list'] - 1)).", ".  MAX_DISPLAY_SEARCH_RESULTS);
    $query_split  = new splitPageResults($_REQUEST['list'], '');
	define('PAGE_TITLE', BOX_TRANSLATOR_MODULE);
	break;
}

?>
