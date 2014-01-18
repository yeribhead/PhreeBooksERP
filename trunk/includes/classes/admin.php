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
//  Path: /includes/classes/admin.php
//
namespace core\classes;
class admin {
	public $notes 			= array();// placeholder for any operational notes
	public $prerequisites 	= array();// modules required and rev level for this module to work properly
	public $keys			= array();// Load configuration constants for this module, must match entries in admin tabs
	public $dirlist			= array();// add new directories to store images and data
	public $tables			= array();// Load tables
	public $module 			= ''; //@todo rename to id
	public $text; //@todo add to other files

	function __construct(){
		$this->version = constant('MODULE_' . strtoupper($this->module) . '_VERSION');
		$this->status  = constant('MODULE_' . strtoupper($this->module) . '_STATUS');
	}
	
	function install() {
		$error = false;
	    return $error;
	}

  	function initialize() {
  	}

	function update() {
	    
		if (!$this->error) {
		  write_configure('MODULE_' . strtoupper($this->module) . '_STATUS', constant('MODULE_' . strtoupper($this->module) . '_VERSION'));
	   	  $messageStack->add(sprintf(GEN_MODULE_UPDATE_SUCCESS, $this->module, constant('MODULE_' . strtoupper($this->module) . '_VERSION')), 'success');
		}
		return $error;
	}

	function remove() {
		return $this->error;
	}
	
  	function release_update($version, $path = '') {
    	global $db, $messageStack;
		if (file_exists($path)) { include_once ($path); }
		write_configure('MODULE_' . strtoupper($this->module) . '_STATUS', $version);
		return $this->error ? false : $version;
  	}
	
	function load_reports() {
	}

	function load_demo() {
	}
	
	function should_update(){
		if (constant('MODULE_' . strtoupper($this->module) . '_STATUS') <> constant('MODULE_' . strtoupper($this->module) . '_VERSION')) return true;
		else return false;
	}
}
?>