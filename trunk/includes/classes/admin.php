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
	public $id;
	public $text; 
	public $description;
	public $notes 			= array();// placeholder for any operational notes
	public $prerequisites 	= array();// modules required and rev level for this module to work properly
	public $keys			= array();// Load configuration constants for this module, must match entries in admin tabs
	public $dirlist			= array();// add new directories to store images and data
	public $tables			= array();// Load tables
	public $methods			= array();// holds all classes in a array
	public $status			= 1.0; // stores the moduel status
	public $version			= 1.0; // stores availible version of the module
	public $installed		= false; 
	public $core			= false;

	function __construct(){
		if (defined('MODULE_' . strtoupper($this->id) . '_STATUS')){
			$this->installed = true;
			$this->status  = constant('MODULE_' . strtoupper($this->id) . '_STATUS');
		}
		$this->version = constant('MODULE_' . strtoupper($this->id) . '_VERSION');
	}
	
	function install() {
	}

  	function initialize() {
  	}

	function update() {
	    write_configure('MODULE_' . strtoupper($this->id) . '_STATUS', constant('MODULE_' . strtoupper($this->id) . '_VERSION'));
	   	$messageStack->add(sprintf(GEN_MODULE_UPDATE_SUCCESS, $this->id, constant('MODULE_' . strtoupper($this->id) . '_VERSION')), 'success');
	}

	function remove() {
		
	}
	
  	function release_update($version, $path = '') {
    	global $db, $messageStack;
		if (file_exists($path)) { include_once ($path); }
		write_configure('MODULE_' . strtoupper($this->id) . '_STATUS', $version);
		//@todo should not return error but throw them
		return $this->error ? false : $version;
  	}
	
	function load_reports() {
	}

	function load_demo() {
	}
	
	function should_update(){
		if (!$this->installed) return false;
		if (constant('MODULE_' . strtoupper($this->id) . '_STATUS') <> constant('MODULE_' . strtoupper($this->id) . '_VERSION')) return true;
		else return false;
	}
}
?>