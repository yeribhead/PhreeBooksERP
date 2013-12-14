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
//  Path: /modules/work_orders/classes/install.php
//
class work_orders_admin {
  function __construct() {
	$this->notes = array(); // placeholder for any operational notes
	$this->prerequisites = array( // modules required and rev level for this module to work properly
	  'phreedom'   => 3.6,
	  'inventory'  => 3.6,
	  'phreebooks' => 3.6,
	);
	// Load configuration constants for this module, must match entries in admin tabs
    $this->keys = array(
	);
	// add new directories to store images and data
	$this->dirlist = array(
	);
	// Load tables
	$this->tables = array(
	  TABLE_WO_MAIN => "CREATE TABLE " . TABLE_WO_MAIN  . " (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  inactive enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
		  wo_title varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
		  sku_id int(11) NOT NULL DEFAULT '0',
		  description varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
		  allocate ENUM('0', '1') NOT NULL DEFAULT '0',
		  ref_doc varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
		  ref_spec varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
		  revision int(3) NOT NULL,
		  revision_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  last_usage date NOT NULL DEFAULT '0000-00-00',
		  PRIMARY KEY (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
	  TABLE_WO_STEPS => "CREATE TABLE " . TABLE_WO_STEPS  . " (
		  id int(11) NOT NULL auto_increment,
		  ref_id int(11) NOT NULL default '0',
		  step int(3) NOT NULL,
		  task_id int(11) NOT NULL,
		  PRIMARY KEY  (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
	  TABLE_WO_JOURNAL_MAIN => "CREATE TABLE " . TABLE_WO_JOURNAL_MAIN  . " (
		  id int(11) NOT NULL auto_increment,
		  wo_num varchar(16) NOT NULL default 'WO-00001',
		  wo_id int(11) NOT NULL default '0',
		  priority int(1) NOT NULL default '0',
		  sku_id int(11) NOT NULL default '0',
		  qty float NOT NULL default '0',
		  wo_title varchar(32) default NULL,
		  post_date date default NULL,
		  closed enum('0','1') NOT NULL default '0',
		  close_date date default NULL,
		  notes text collate utf8_unicode_ci,
		  PRIMARY KEY  (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
	  TABLE_WO_JOURNAL_ITEM => "CREATE TABLE " . TABLE_WO_JOURNAL_ITEM  . " (
		  id int(11) NOT NULL auto_increment,
		  ref_id int(11) NOT NULL default '0',
		  step int(11) NOT NULL default '0',
		  task_id int(11) NOT NULL default '0',
		  task_name varchar(32) default NULL,
		  mfg enum('0','1') NOT NULL default '0',
		  mfg_id int(11) NOT NULL default '0',
		  mfg_date datetime default NULL,
		  qa enum('0','1') NOT NULL default '0',
		  qa_id int(11) NOT NULL default '0',
		  qa_date datetime default NULL,
		  data_entry enum('0','1') NOT NULL default '0',
		  data_value varchar(64) default NULL,
		  admin_id int(11) NOT NULL default '0',
		  complete enum('0','1') NOT NULL default '0',
		  PRIMARY KEY  (id),
		  KEY ref_id (ref_id),
		  KEY step (step)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
	  TABLE_WO_TASK => "CREATE TABLE " . TABLE_WO_TASK  . " (
		  id int(11) NOT NULL auto_increment,
		  task_name varchar(32) NOT NULL,
		  description varchar(255) default NULL,
		  ref_doc varchar(64) NOT NULL,
		  ref_spec varchar(64) default NULL,
		  dept_id varchar(16) NOT NULL,
		  job_time float NOT NULL,
		  job_unit int(11) NOT NULL,
		  mfg enum('0','1') NOT NULL default '0',
		  qa enum('0','1') NOT NULL default '0',
		  data_entry enum('0','1') NOT NULL default '0',
		  erp_entry enum('0','1') NOT NULL default '0',
		  PRIMARY KEY  (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
    );
  }

  function install($module) {
    global $db, $messageStack;
    if (!db_field_exists(TABLE_CURRENT_STATUS, 'next_wo_num')) {
	  $db->Execute("ALTER TABLE " . TABLE_CURRENT_STATUS . " ADD next_wo_num VARCHAR(16) NOT NULL DEFAULT 'WO-0001';");
    }
	write_configure('PHREEHELP_FORCE_RELOAD', '1');
  }

  function initialize($module) {
  }

  function update($module) {
    global $db, $messageStack;
	$error = false;
    if (MODULE_WORK_ORDERS_STATUS < '3.1') {
	  if (!db_field_exists(TABLE_WO_TASK, 'erp_entry')) {
	    $db->Execute("ALTER TABLE " . TABLE_WO_TASK . " ADD erp_entry ENUM('0', '1') NOT NULL DEFAULT '0'");
	    $db->Execute("ALTER TABLE " . TABLE_WO_MAIN . " ADD allocate ENUM('0', '1') NOT NULL DEFAULT '0' AFTER description");
	    $db->Execute("ALTER TABLE " . TABLE_CURRENT_STATUS . " ADD next_wo_num VARCHAR(16)  NOT NULL DEFAULT 'WO0001';");
	    $db->Execute("ALTER TABLE " . TABLE_WO_JOURNAL_MAIN . " ADD wo_num VARCHAR(16) NOT NULL DEFAULT 'WO-00001' AFTER id");
		$result = $db->Execute("select id from " . TABLE_WO_JOURNAL_MAIN);
		while(!$result->EOF) {
		  $id = $result->fields['id'];
		  $db->Execute("update " . TABLE_WO_JOURNAL_MAIN . " set wo_num = 'WO-" . str_pad($id, 5, '0', STR_PAD_LEFT) . "' where id = " . $id);
		  $result->MoveNext();
		}
		$db->Execute("update " . TABLE_CURRENT_STATUS . " set next_wo_num = 'WO-" . str_pad($id+1, 5, '0', STR_PAD_LEFT) . "'");
	  }
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
    if (db_field_exists(TABLE_CURRENT_STATUS, 'next_wo_num')) $db->Execute("ALTER TABLE " . TABLE_CURRENT_STATUS . " DROP next_wo_num");
	write_configure('PHREEHELP_FORCE_RELOAD', '1');
    return $error;
  }

  function load_reports($module) {
	global $db;
	$error = false;
	$result = $db->Execute("select id from " . TABLE_PHREEFORM . " where doc_group = 'inv' and doc_ext = '0'");
	if (admin_add_report_folder($result->fields['id'], TEXT_WORK_ORDER_FORMS, 'inv:wo', 'ff')) $error = true;
	return $error;
  }

  function load_demo() {
  }

}
?>