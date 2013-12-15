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
//  Path: /modules/cp_action/classes/admin.php
//
namespace cp_action;
class admin extends \core\admin {
  function __construct() {
	$this->notes = array(); // placeholder for any operational notes
	$this->prerequisites = array( // modules required and rev level for this module to work properly
	  'phreedom'   => '3.3',
	);
	// Load configuration constants for this module, must match entries in admin tabs
    $this->keys = array(
	);
	// add new directories to store images and data
	$this->dirlist = array(
	);
	// Load tables
	$this->tables = array(
	  TABLE_CAPA => "CREATE TABLE " . TABLE_CAPA  . " (
			id int(11) NOT NULL auto_increment,
			capa_num varchar(16) NOT NULL,
			capa_type enum('c','p') NOT NULL default 'c',
			requested_by int(11) NOT NULL default '0',
			capa_status int(11) NOT NULL default '0',
			entered_by int(11) NOT NULL default '0',
			creation_date date NOT NULL default '0000-00-00',
			notes_issue text,
			customer_name varchar(32) default NULL,
			customer_id varchar(20) default NULL,
			customer_telephone varchar(16) default NULL,
			customer_invoice varchar(16) default NULL,
			customer_email varchar(32) default NULL,
			notes_customer text,
			analyze_due_id int(11) NOT NULL default '0',
			analyze_due date NOT NULL default '0000-00-00',
			analyze_close_id int(11) NOT NULL default '0',
			analyze_date date NOT NULL default '0000-00-00',
			repair_due_id int(11) NOT NULL default '0',
			repair_due date NOT NULL default '0000-00-00',
			repair_close_id int(11) NOT NULL default '0',
			repair_date date NOT NULL default '0000-00-00',
			audit_due_id int(11) NOT NULL default '0',
			audit_due date NOT NULL default '0000-00-00',
			audit_close_id int(11) NOT NULL default '0',
			audit_date date NOT NULL default '0000-00-00',
			closed_due_id int(11) NOT NULL default '0',
			closed_due date NOT NULL default '0000-00-00',
			closed_close_id int(11) NOT NULL default '0',
			closed_date date NOT NULL default '0000-00-00',
			action_date date NOT NULL default '0000-00-00',
			notes_investigation text,
			agreed_by int(11) NOT NULL default '0',
			notes_action text,
			capa_closed enum('y','n') NOT NULL default 'y',
			next_capa_num varchar(255) default NULL,
			notes_audit text,
			PRIMARY KEY (id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
    );
  }

  function install($module) {
    global $db, $messageStack;
	$error = false;
    // add a current status field for the next ca/pa number
    if (!db_field_exists(TABLE_CURRENT_STATUS, 'next_capa_num')) {
	  $db->Execute("ALTER TABLE " . TABLE_CURRENT_STATUS . " ADD next_capa_num  VARCHAR(16) NOT NULL DEFAULT 'CAPA0001'");
    }
    return $error;
  }

  function initialize($module) {
  }

  function update($module) {
    global $db, $messageStack;
	$error = false;
    if (db_field_exists(TABLE_CURRENT_STATUS, 'next_capa_desc')) $db->Execute("ALTER TABLE " . TABLE_CURRENT_STATUS . " DROP next_capa_desc");
	if (!$error) {
	  write_configure('MODULE_' . strtoupper($module) . '_STATUS', constant('MODULE_' . strtoupper($module) . '_VERSION'));
   	  $messageStack->add(sprintf(GEN_MODULE_UPDATE_SUCCESS, $module, constant('MODULE_' . strtoupper($module) . '_VERSION')), 'success');
	}
	return $error;
  }

  function remove($module) {
    global $db, $messageStack;
	$error = false;
    if (db_field_exists(TABLE_CURRENT_STATUS, 'next_capa_num')) $db->Execute("ALTER TABLE " . TABLE_CURRENT_STATUS . " DROP next_capa_num");
    if (db_field_exists(TABLE_CURRENT_STATUS, 'next_capa_desc')) $db->Execute("ALTER TABLE " . TABLE_CURRENT_STATUS . " DROP next_capa_desc");
	return $error;
  }

  function load_reports($module) {
  }

  function load_demo() {
  }

}
?>