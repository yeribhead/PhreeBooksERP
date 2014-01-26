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
//  Path: /modules/payment/pages/admin/pre_process.php
//
$security_level = validate_user(SECURITY_ID_CONFIGURATION);
/**************  include page specific files    *********************/
gen_pull_language($module, 'admin');
/**************   page specific initialization  *************************/
$error      = false; 
// see if installing or removing a method
if (substr($_REQUEST['action'], 0, 8) == 'install_') {
  $method = substr($_REQUEST['action'], 8);
  $_REQUEST['action'] = 'install';
} elseif (substr($_REQUEST['action'], 0, 7) == 'remove_') {
  $method = substr($_REQUEST['action'], 7);
  $_REQUEST['action'] = 'remove';
}
/***************   Act on the action request   *************************/
switch ($_REQUEST['action']) {
  case 'install':
	validate_security($security_level, 4);
	write_configure('MODULE_PAYMENT_' . strtoupper($method) . '_STATUS', '1');
	foreach ($admin_classes['payment']->methods[$method]->keys() as $key) write_configure($key['key'], $key['default']);
	if (method_exists($admin_classes['payment']->methods[$method], 'install')) $admin_classes['payment']->methods[$method]->install(); // handle special case install, db, files, etc
	gen_redirect(html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('action')), 'SSL'));
	break;
  case 'remove';
	validate_security($security_level, 4);
	if (method_exists($admin_classes['payment']->methods[$method], 'remove')) $admin_classes['payment']->methods[$method]->remove(); // handle special case removal, db, files, etc
	foreach ($admin_classes['payment']->methods[$method]->keys() as $key) { // remove all of the keys from the configuration table
      $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key = '" . $key['key'] . "'");
	}
	remove_configure('MODULE_PAYMENT_' . strtoupper($method) . '_STATUS');
	gen_redirect(html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('action')), 'SSL'));
	break;
  case 'save':
	validate_security($security_level, 3);
  	// foreach method if enabled, save info
	if (sizeof($admin_classes['payment']->methods) > 0) foreach ($admin_classes['payment']->methods as $method) {
	  	if ($method->installed) $method->update();
	}
	// save general tab
	foreach ($admin_classes['payment']->keys as $key => $default) {
	  $field = strtolower($key);
      if (isset($_POST[$field])) write_configure($key, $_POST[$field]);
    }
	gen_redirect(html_href_link(FILENAME_DEFAULT, gen_get_all_get_params(array('action')), 'SSL'));
    break;
  default:
}

/*****************   prepare to display templates  *************************/
// build some general pull down arrays
$sel_yes_no = array(
 array('id' => '0', 'text' => TEXT_NO),
 array('id' => '1', 'text' => TEXT_YES),
);

$include_header   = true;
$include_footer   = true;
$include_template = 'template_main.php';
define('PAGE_TITLE', MENU_HEADING_PHREEPAY);

?>