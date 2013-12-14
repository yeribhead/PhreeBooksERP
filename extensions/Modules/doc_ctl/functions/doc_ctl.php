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
//  Path: /modules/doc_ctl/functions/functions.php
//

function get_mime_image($ext, $type = 'default') {
  if ($type == 'folder') return 'places/folder.png';
  if ($type == 'drive')  return 'devices/drive-harddisk.png';
  switch ($ext) {
    case 'odt':
	case 'doc':  return 'mimetypes/x-office-document.png';
	case 'xls':  return 'mimetypes/x-office-spreadsheet.png';
	case 'ppt':  return 'mimetypes/x-office-presentation.png';
	case 'drw':
	case 'png':
	case 'gif':
	case 'jpg':
	case 'jpeg': return 'mimetypes/image-x-generic.png';
	case 'htm':
	case 'html': return 'mimetypes/text-html.png';
	case 'pdf':  return 'phreebooks/pdficon_small.gif';
	case 'txt':
	default:     return 'mimetypes/text-x-generic.png';
  }
}

function dc_validate_upload($filename) {
  	global $messageStack;
	if ($_FILES[$filename]['error']) { // php error uploading file
		switch ($_FILES[$filename]['error']) {
			case '1': $messageStack->add(TEXT_IMP_ERMSG1, 'error'); break;
			case '2': $messageStack->add(TEXT_IMP_ERMSG2, 'error'); break;
			case '3': $messageStack->add(TEXT_IMP_ERMSG3, 'error'); break;
			case '4': $messageStack->add(TEXT_IMP_ERMSG4, 'error'); break;
			default:  $messageStack->add(TEXT_IMP_ERMSG5 . $_FILES[$filename]['error'] . '.', 'error');
		}
		return false;
	} elseif (!is_uploaded_file($_FILES[$filename]['tmp_name'])) { // file uploaded
		$messageStack->add(TEXT_IMP_ERMSG13, 'error');
		return false;
	} elseif ($_FILES[$filename]['size'] == 0) { // report contains no data, error
		$messageStack->add(TEXT_IMP_ERMSG7, 'error');
		return false;
	}
	return true;
}

function build_dir_path($id) {
	global $db;
	$result = $db->Execute("select parent_id, title from " . TABLE_DC_DOCUMENT . " where id = '" . $id . "'");
	$title  = ($id) ? $result->fields['title'] : '';
	if ($result->fields['parent_id']) $title = build_dir_path($result->fields['parent_id']) . '/' . $title;
	return $title;
}

function load_bookmarks() {
	global $db;
	$contents = NULL;
	$result = $db->Execute("select id, title from " . TABLE_DC_DOCUMENT . " where bookmarks like '%:" . $_SESSION['admin_id'] . ":%'");
	if ($result->RecordCount() == 0) {
	  $contents .= TEXT_NO_BOOKMARKS . '<br />';
	} else {
	  while (!$result->EOF) {
		$contents .= '  <div>';
		$contents .= html_icon('phreebooks/dashboard-remove.png', TEXT_REMOVE, 'small', 'onclick="if (confirm(\'' . DOC_CTL_JS_DEL_BOOKMARK . '\')) deleteBookmark(' . $result->fields['id'] . ')"');
		$contents .= '    <a href="javascript:fetch_doc(' . $result->fields['id'] . ');">' . $result->fields['title'] . '</a>' . chr(10);
		$contents .= '  </div>' . chr(10);
	    $result->MoveNext();
	  }
	}
	return $contents;
}

function load_checked_out() {
	global $db;
	$contents = NULL;
	$result = $db->Execute("select id, title from " . TABLE_DC_DOCUMENT . " where checkout_id = " . $_SESSION['admin_id']);
	if ($result->RecordCount() == 0) {
	  $contents .= TEXT_NO_CHECKED_OUT . '<br />';
	} else {
	  while (!$result->EOF) {
		$contents .= '  <div>';
		$contents .= '    <a href="javascript:fetch_doc(' . $result->fields['id'] . ');">' . $result->fields['title'] . '</a>' . chr(10);
		$contents .= '  </div>' . chr(10);
	    $result->MoveNext();
	  }
	}
	return $contents;
}

function load_recently_added() {
  global $db;
  $contents = NULL;
  $result = $db->Execute("select id, title, type, doc_ext from " . TABLE_DC_DOCUMENT . " where type = 'default' order by create_date desc, id desc limit 20");
  if ($result->RecordCount() == 0) {
    $contents .= TEXT_NO_DOCUMENTS . '<br />';
  } else {
    while (!$result->EOF) {
	  $contents .= '  <div>';
	  $contents .= html_icon(get_mime_image($result->fields['doc_ext']), $result->fields['title'], 'small');
	  $contents .= '    <a href="javascript:fetch_doc(' . $result->fields['id'] . ');">' . $result->fields['title'] . '</a>' . chr(10);
	  $contents .= '  </div>' . chr(10);
	  $result->MoveNext();
    }
  }
  return $contents;
}

function get_owner_name($id) {
  global $db;
  $result = $db->Execute("select display_name from " . TABLE_USERS . " where admin_id = '" . $id . "'");
  return $result->RecordCount() ? $result->fields['display_name'] : '';
}

function test_bookmark() {
  global $db;
  $result = $db->Execute("select id from " . TABLE_DC_DOCUMENT . " where bookmarks like '%:" . $_SESSION['admin_id'] . ":%'");
  return ($result->RecordCount() > 0) ? true : false;
}

function get_doc_history($id, $revision_level = 0) {
  $output = array();
  for ($i = 0; $i < $revision_level+1; $i++) {
    $filename = DOC_CTL_DIR_MY_DOCS . str_pad($id, 8, '0', STR_PAD_LEFT) . '_' . $i .'.dc';
    if (file_exists($filename)) $output[] = stat($filename);
  }
  return $output;
}

function security_check($tokens) {
	$categories = explode(';', $tokens);
	$user_str = ':' . $_SESSION['admin_id'] . ':';
	$dept_str = ':' . ($_SESSION['department'] ? $_SESSION['department'] : '0') . ':';
	foreach ($categories as $category) {
		$type = substr($category, 0, 1);
		$approved_ids = substr($category, 1) . ':';
		if (strpos($approved_ids, ':0:') !== false) return true; // for 'all' field
		switch ($type) {
			case 'u': if (strpos($approved_ids, $user_str) !== false) return true; break;
			case 'd': if (strpos($approved_ids, $dept_str) !== false) return true; break;
		}
	}
	return false;
}

?>