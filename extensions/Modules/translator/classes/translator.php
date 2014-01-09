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
//  Path: /modules/phreedom/classes/translator.php
//
namespace translator\classes;
class translator {
	function __construct() {
		global $db;
	}

  function fetch_stats($mod, $lang, $ver) {
	global $db, $messageStack;
	$total = 0;
	$trans = 0;
	$result = $db->Execute("select translated from " . TABLE_TRANSLATOR . " 
	  where module = '" . $mod . "' and language = '" . $lang . "' and version = '" . $ver . "'");
	while(!$result->EOF) {
	  if ($result->fields['translated'] == '1') $trans++;
	  $total++;
	  $result->MoveNext();
	}
	if ($total == 0) $total++;
	return array('total' => $total, 'trans' => $trans);
  }

  function upload_language($dir_dest, $mod, $lang) {
	global $db, $backup, $messageStack;
	$upload_filename = DIR_FS_MY_FILES . 'translator/translate.zip';
	if (!validate_upload('zipfile', 'zip', 'zip')) {
	  $messageStack->add(TEXT_IMP_ERMSG7, 'error');
	  return false;
	}
	if (file_exists($upload_filename)) unlink ($upload_filename);
	if (!copy($_FILES['zipfile']['tmp_name'], $upload_filename)) {
	  $messageStack->add('Error copying to ' . $upload_filename, 'error');
	  return false;
	} 
	if (!is_dir($dir_dest)) mkdir($dir_dest);
	if ($backup->unzip_file($upload_filename, $dir_dest)) {
	  $messageStack->add('Error unzipping file', 'error');
	  return false;
	}
	$this->import_language($dir_dest, $mod, $lang);
	if (file_exists($upload_filename)) unlink ($upload_filename);
	$backup->delete_dir($dir_dest); // remove unzipped files
	return true;
  }

  function import_language($dir_source=DIR_FS_MODULES, $mod='all', $lang='en_us', $ver=false, $module_dir=false, $chk_method=false, $method_dir=false) {
	global $db, $messageStack;
    if (!is_dir($dir_source)) return;
	$files = scandir($dir_source);
	foreach ($files as $file) {
	  if ($file == "." || $file == "..") continue;
	  if (is_file($dir_source . $file) && substr($dir_source . $file, -4) == '.php') {
		$langfile = file_get_contents($dir_source . $file);
		if (!$ver) { // try to pull version form language file, upload mode
		  $temp = substr($langfile, strpos($langfile, 'Version:')+8, 5);
          $temp = preg_replace("/[^0-9.]+/", "", $temp);
		  $ver  = $temp > 0 ? $temp : '0.1';
		}
		$pathtofile = str_replace(DIR_FS_ADMIN, '', $dir_source . $file);
		$langtemp = $this->extractUTF8($langfile);
//		preg_match_all("|define\('(.*)',[\s]*'(.*)'\);|imU", $langfile, $langtemp); // broken for UTF-8
		$db->Execute("DELETE FROM ".TABLE_TRANSLATOR." WHERE module='$mod' AND language='$lang' AND version='$ver' AND pathtofile='$pathtofile'");
		foreach ($langtemp as $const => $value) {
if ($const == 'GEN_COUNTRY_CODE')echo 'writing const = '.$const.' with value = '.$value.'<br>';
			$sql = "INSERT INTO ".TABLE_TRANSLATOR." SET 
			module='$mod', language='$lang', version='$ver', pathtofile='$pathtofile', 
			defined_constant='".db_input($const)."', translation='".db_input($value)."', translated='1'";
		  $db->Execute($sql);
		}
	  } elseif (is_dir($dir_source . $file)) {
	    $tmp_module = $mod;
	    $tmp_module_dir = $module_dir;
	    $tmp_chk_method = $chk_method;
		$tmp_method_dir = $method_dir;
		if ($module_dir) {
		  $tmp_module = $file;
		  $tmp_module_dir = false;
	      $tmp_chk_method = true;
	    } elseif ($chk_method) {
		  if ($file == 'methods' || $file == 'dashboards') {
	        $tmp_chk_method = false;
		    $tmp_method_dir = true;
		  }
	    } elseif ($method_dir) {
		  $tmp_method_dir = false;
		  $tmp_module = $mod . '-' . $file;
		} elseif ($file == 'soap' || $file == 'install') {
		  $tmp_module = $file;
		} elseif ($file == 'modules') {
		  $tmp_module_dir = true;
		}
//echo 'looking at mod = ' . $mod . ' and dir = ' . $dir_source . $file . '/' . '<br>';
		$this->import_language($dir_source . $file . "/", $tmp_module, $lang, $ver, $tmp_module_dir, $tmp_chk_method, $tmp_method_dir);
	  }
	}
  }

  function convert_language($mod, $lang, $source = 'en_us', $history = '', $subs = array()) {
	global $db, $messageStack;
	// retrieve highest version
	$result = $db->Execute("select max(version) as version from " . TABLE_TRANSLATOR . " 
	  where module = '" . $mod . "' and language = '" . $source . "'");
	if ($result->RecordCount() == 0) {
	  $messageStack->add(TRANS_ERROR_NO_SOURCE,'error');
	  return false;
	}
	$ver = $result->fields['version'];
	// delete all from the version being written, prevents dups
	$db->Execute("delete from " . TABLE_TRANSLATOR . " 
	  where module = '" . $mod . "' and language = '" . $lang . "' and version = '" . $ver . "'");
	// load the source language
	$result = $db->Execute("select pathtofile, defined_constant, translation from " . TABLE_TRANSLATOR . " 
	  where module = '" . $mod . "' and language = '" . $source . "' and version = '" . $ver . "' order by id");
	while (!$result->EOF) {
	  // fix some fields
	  $const        = $result->fields['defined_constant'];
	  $trans        = $result->fields['translation'];
	  $translated   = false;
	  if (isset($subs[$history][$const])) {
	    $temp       = $this->pull_latest_ver($subs[$history][$const], $subs[$source][$const], $trans);
		$trans      = $temp['translation'];
		$translated = $temp['translated'];
	  }
	  if (isset($subs[$lang][$const])) {
	    $temp       = $this->pull_latest_ver($subs[$lang][$const], $subs[$source][$const], $trans);
		$trans      = $temp['translation'];
		$translated = $temp['translated'];
	  }
	  $path  = str_replace($source, $lang, $result->fields['pathtofile']);
	  $sql   = "INSERT INTO " . TABLE_TRANSLATOR . " set 
		module  = '"          . $mod . "',
		language = '"         . $lang . "',
		version = '"          . $ver . "',
		pathtofile = '"       . $path . "',
		defined_constant = '" . db_input($const) . "',
		translation = '"      . db_input($trans) . "',
		translated = '"       . $translated . "'";
	  $db->Execute($sql);
	  $result->MoveNext();
	}
	return true;
  }

  function export_language($mod, $lang, $ver, $hide_error = false) {
	global $db, $backup, $messageStack;
	$result = $db->Execute("select pathtofile, defined_constant, translation from " . TABLE_TRANSLATOR . " 
	  where module = '" . $mod . "' and language = '" . $lang . "' and version = '" . $ver . "'");
	if ($result->RecordCount() == 0) {
	  if (!$hide_error) $messageStack->add(GEN_BACKUP_DOWNLOAD_EMPTY,'error');
	  return false; // no rows, return
	}
	$output  = array();
	$header  = '<' . '?' . 'php'  . chr(10);
	$header .= '// +-----------------------------------------------------------------+' . chr(10);
	$header .= '// ' . TRANSLATION_HEADER . chr(10);
	$header .= '// Generated: '     . date('Y-m-d h:i:s') . chr(10);
	$header .= '// Module/Method: ' . $mod  . chr(10);
	$header .= '// ISO Language: '  . $lang . chr(10);
	$header .= '// Version: '       . $ver  . chr(10);
	$header .= '// +-----------------------------------------------------------------+' . chr(10);
	while (!$result->EOF) {
	  if (!isset($output[$result->fields['pathtofile']])) {
	    $output[$result->fields['pathtofile']]  = $header;
		$output[$result->fields['pathtofile']] .= '// Path: /' . $result->fields['pathtofile'] . chr(10) . chr(10);
	  }
	  $temp  = 'define(\'' . $result->fields['defined_constant'] . '\',\'';
	  $temp .= addslashes($result->fields['translation']) . '\');';
	  $output[$result->fields['pathtofile']] .= $temp . chr(10);
	  $result->MoveNext();
	}
	foreach ($output as $path => $content) {
	  $content .= chr(10) . '?' . '>' . chr(10); // terminate the file
	  $new_dir  = $backup->source_dir . substr ($path, 0, strrpos($path, '/'));
	  $filename = substr ($path, strrpos($path,'/')+1);
	  if (!is_dir($new_dir)) mkdir($new_dir, 0777, true);
	  if (!$fp = fopen($new_dir . '/' . $filename, 'w')) {
	    if (!$hide_error) $messageStack->add('Error opening ' . $new_dir . '/' . $filename,'error');
		return false;
	  }
	  fwrite($fp, $content);
	  fclose($fp);
	}
	return true;
  }

  function pull_latest_ver($versions, $sources, $cur_trans) {
	$translation = '';
    $translated  = '0';
    if (is_array($versions)) {
	  krsort($versions);
	  foreach ($versions as $ver => $value) { // just need the first value, i.e. highest version
	    $translation = $value;
		$translated  = $sources[$ver] == $cur_trans ? '1' : '0';
		break;
	  }
	}
	return array('translation' => $translation, 'translated' => $translated);
  }

  function extractUTF8($langFile) { // handles both single and double quotes
  	// replaced preg_match_all which failed for unicode characters
  	$runaway = 0;
  	$output = array();
  	while(true) {
  		if ($runaway++ > 50000) break;
  		if (strpos($langFile, 'define') === false) break;
  		$langFile = trim(substr($langFile, strpos($langFile, 'define')+6)); // find first define
  		$langFile = trim(substr($langFile, 1)); // remove '('
  		$quotChar = $langFile[0]; // quote character for constant
  		$const = substr($langFile, 1, strpos($langFile, $quotChar, 1)-1); // from after first quotechar to just before second
  		$langFile = trim(substr($langFile, strpos($langFile, $quotChar, 1)+1)); // remove constant and quotes from input string
  		
  		$langFile = trim(substr($langFile, 1)); // remove ',' between define statement

  		$quotChar = $langFile[0]; // quote character for value
  		$value = trim(substr($langFile, 1, strpos($langFile, ');'))); // ASSUME define statment ends with no space between ) and ; 
  		$value = substr($value, 0, strrpos($value, $quotChar)); // remove closing quote character
  		$langFile = trim(substr($langFile, strpos($langFile, ');')+2)); // remove ');' at end of define
  		$output[$const] = $value;
  	}
  	return $output;
  }
  
}
?>