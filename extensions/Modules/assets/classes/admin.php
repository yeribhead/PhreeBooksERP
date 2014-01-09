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
//  Path: /modules/assets/classes/admin.php
//
namespace assets\classes; 
class admin extends \core\classes\admin {

  function __construct() {
	$this->prerequisites = array( // modules required and rev level for this module to work properly
	  'phreedom'   => 3.5,
	  'phreebooks' => 3.3,
	);
	// add new directories to store images and data
	$this->dirlist = array(
	  'assets',
	  'assets/images',
	  'assets/main',
	);
	// Load tables
	$this->tables = array(
	  TABLE_ASSETS => "CREATE TABLE " . TABLE_ASSETS  . " (
		id int(11) NOT NULL auto_increment,
		asset_id varchar(32) collate utf8_unicode_ci NOT NULL default '',
		inactive enum('0','1') collate utf8_unicode_ci NOT NULL default '0',
		asset_type char(2) collate utf8_unicode_ci NOT NULL default 'si',
		purch_cond enum('n','u') collate utf8_unicode_ci NOT NULL default 'n',
		serial_number varchar(32) collate utf8_unicode_ci NOT NULL default '',
		description_short varchar(32) collate utf8_unicode_ci NOT NULL default '',
		description_long varchar(255) collate utf8_unicode_ci default NULL,
		image_with_path varchar(255) collate utf8_unicode_ci default NULL,
		account_asset varchar(15) collate utf8_unicode_ci default NULL,
		account_depreciation varchar(15) collate utf8_unicode_ci default '',
		account_maintenance varchar(15) collate utf8_unicode_ci default NULL,
		asset_cost float NOT NULL default '0',
		depreciation_method enum('a','f','l') collate utf8_unicode_ci NOT NULL default 'f',
		full_price float NOT NULL default '0',
		acquisition_date date NOT NULL default '0000-00-00',
		maintenance_date date NOT NULL default '0000-00-00',
		terminal_date date NOT NULL default '0000-00-00',
        attachments text,
		PRIMARY KEY  (id)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
    );
  }

  function install($module) {
	$error = false;
	require_once(DIR_FS_MODULES . 'phreedom/functions/phreedom.php');
	xtra_field_sync_list('assets', TABLE_ASSETS);
    return $error;
  }

  function initialize($module) {
  }

  function update($module) {
    global $db, $messageStack;
	$error = false;
    if (MODULE_ASSETS_STATUS < 3.1) {
	  $tab_map = array('0' => '0');
	  if(db_table_exists(DB_PREFIX . 'assets_tabs')){
		  $result = $db->Execute("select * from " . DB_PREFIX . 'assets_tabs');
		  while (!$result->EOF) {
		    $updateDB = $db->Execute("insert into " . TABLE_EXTRA_TABS . " set 
			  module_id = 'assets',
			  tab_name = '"    . $result->fields['category_name']        . "',
			  description = '" . $result->fields['category_description'] . "',
			  sort_order = '"  . $result->fields['sort_order']           . "'");
		    $tab_map[$result->fields['category_id']] = db_insert_id();
		    $result->MoveNext();
		  }
		  $db->Execute("DROP TABLE " . DB_PREFIX . "assets_tabs");
	  }
	  if(db_table_exists(DB_PREFIX . 'assets_fields')){
		  $result = $db->Execute("select * from " . DB_PREFIX . 'assets_fields');
		  while (!$result->EOF) {
		    $updateDB = $db->Execute("insert into " . TABLE_EXTRA_FIELDS . " set 
			  module_id = 'assets',
			  tab_id = '"      . $tab_map[$result->fields['category_id']] . "',
			  entry_type = '"  . $result->fields['entry_type']  . "',
			  field_name = '"  . $result->fields['field_name']  . "',
			  description = '" . $result->fields['description'] . "',
			  params = '"      . $result->fields['params']      . "'");
		    $result->MoveNext();
		  } 
		  $db->Execute("DROP TABLE " . DB_PREFIX . "assets_fields");
	  }
	  xtra_field_sync_list('assets', TABLE_ASSETS);
	}
    if (MODULE_ASSETS_STATUS < 3.3) {
	  if (!db_field_exists(TABLE_ASSETS, 'attachments')) $db->Execute("ALTER TABLE " . TABLE_ASSETS . " ADD attachments TEXT NOT NULL AFTER terminal_date");
	  require_once(DIR_FS_MODULES . 'phreedom/functions/phreedom.php');
	  xtra_field_sync_list('assets', TABLE_ASSETS);
    }
	if (!$error) {
	  write_configure('MODULE_' . strtoupper($module) . '_STATUS', constant('MODULE_' . strtoupper($module) . '_VERSION'));
   	  $messageStack->add(sprintf(GEN_MODULE_UPDATE_SUCCESS, $module, constant('MODULE_' . strtoupper($module) . '_VERSION')), 'success');
	}
	return $error;
  }

  function remove($module) {
    global $db;
	$error = false;
	$db->Execute("delete from " . TABLE_EXTRA_FIELDS . " where module_id = 'assets'");
	$db->Execute("delete from " . TABLE_EXTRA_TABS   . " where module_id = 'assets'");
	return $error;
  }
}
?>