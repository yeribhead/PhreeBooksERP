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
	public $sort_order  	= 99; 
	public $notes 			= array();// placeholder for any operational notes
	public $prerequisites 	= array();// modules required and rev level for this module to work properly
	public $keys			= array();// Load configuration constants for this module, must match entries in admin tabs
	public $dirlist			= array();// add new directories to store images and data
	public $tables			= array();// Load tables
	public $dashboards		= array();// holds all classes in a array
	public $methods			= array();// holds all classes in a array
	public $status			= 1.0; // stores the moduel status
	public $version			= 1.0; // stores availible version of the module
	public $installed		= false; 
	public $core			= false;

	/**
	 * this is the general construct function called when the class is created.
	 */
	function __construct(){
		if (defined('MODULE_' . strtoupper($this->id) . '_STATUS')){
			$this->installed = true;
			$this->status  = constant('MODULE_' . strtoupper($this->id) . '_STATUS');
		}
		$this->version = constant('MODULE_' . strtoupper($this->id) . '_VERSION');
		$this->methods = return_all_methods($this->id, false, 'methods');
		$this->dashboards = return_all_methods($this->id, false, 'dashboards');
	}
	
	/**
	 * this will install a module
	 * @param bool $demo
	 * @param string $path_my_files location to the my_files folder
	 */
	
	function install($path_my_files, $demo = false) {
		$this->check_prerequisites_versions();
		$this->install_dirs($path_my_files);
		$this->install_update_tables();
		foreach ($this->keys as $key => $value) write_configure($key, $value);
  		if ($demo) $this->load_demo(); // load demo data
  		$this->load_reports();
  		admin_add_reports($this->id);
  		$this->after_install();
		foreach ($this->methods as $method) {
	  		write_configure('MODULE_' . strtoupper($this->id) . '_' . strtoupper($method->id) . '_STATUS', $method->version);
	  		foreach ($method->key as $key) write_configure($key['key'], $key['default']);
	  		if (method_exists($method, 'install')) $method->install();
		}
		$this->installed = true;
		$this->status = $this->version;
	}
	
	/**
	 * this function will be called after you log in. 
	 */
	
  	function initialize() {
  	}
	
  	/**
  	 * this used to be the update function.
  	 * this function will be called when a module is upgraded. 
  	 */
  	
	function upgrade() {
		$this->install_update_tables();
		foreach ($this->methods as $method) {
			if ($method->installed && $method->should_update()){
	    		foreach ($method->key() as $key) if(!defined($key['key'])) write_configure($key['key'], $key['default']);
				if (method_exists($method, 'upgrade')) $method->upgrade();
				write_configure('MODULE_' . strtoupper($this->id) . '_' . strtoupper($method->id) . '_STATUS', $method->version);
				gen_add_audit_log(sprintf(GEN_LOG_INSTALL_SUCCESS, $method->text) . TEXT_UPDATE, $method->version);
	   			$messageStack->add(sprintf(GEN_MODULE_UPDATE_SUCCESS, $method->id, $method->version), 'success');
			}
		}
		$this->status = $this->version;
	}
	
	function delete($path_my_files) {//@todo add to childeren
		if ($this->core) throw new \Exception("can not delete core module " .$this->text);
		foreach ($this->methods as $method) {
			if ($method->installed){
	    		remove_configure('MODULE_' . strtoupper($module) . '_' . strtoupper($method->id) . '_STATUS');
	    		foreach ($method->key() as $key) remove_configure($key['key']);
	    		if (method_exists($method, 'remove')) $method->remove();//@todo rename to delete
			}
	  	}
	    $this->remove_tables();
	    $this->remove_dirs($path_my_files);
	    remove_configure('MODULE_' . strtoupper($this->id) . '_STATUS');
	    $this->installed = false;
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
		if (constant('MODULE_' . strtoupper($this->id) . '_STATUS') <> $this->version) return true;
		else return false;
	}
	
	/**
	 * This function checks if a module is allowed to install using the prerequisites
	 * @throws \Exception
	 */
	
	function check_prerequisites_versions() {
		if (is_array($this->prerequisites) && sizeof($this->prerequisites) > 0) {
			foreach ($this->prerequisites as $mod => $version) {
		  		if (!defined('MODULE_' . strtoupper($mod) . '_VERSION')) {
		    		throw new \Exception (sprintf(ERROR_MODULE_NOT_INSTALLED, $this->id, $mod));
		  		} elseif ( (int)constant('MODULE_' . strtoupper($mod) . '_VERSION') < (int)$version) {
		    		throw new \Exception (sprintf(ERROR_MODULE_VERSION_TOO_LOW, $this->id, $mod, $version, $cur_rev));
		  		}
			}
		}
	}
	
	/**
	 * this function installes the required dirs under my_files\mycompany 
	 * @throws \Exception
	 */
	
	function install_dirs($path_my_files) {
		foreach ($this->dirlist as $dir) {
			if (!file_exists($path_my_files . $dir)) {
		  		if (!@mkdir($path_my_files . $dir, 0755, true)) throw new \Exception (sprintf(ERROR_CANNOT_CREATE_MODULE_DIR, $path_my_files . $dir));
	    	}
	  	}
	}
	
	function remove_dirs($path_my_files) {
		foreach(array_reverse($this->dirlist) as $dir) {
			if (!@rmdir($path_my_files . $dir)) throw new \Exception (sprintf(ERROR_CANNOT_REMOVE_MODULE_DIR, $path_my_files . $dir));
	  	}
	}
	
	/**
	 * This funtion installs the tables.
	 * If table exists nothing will happen.
	 * @throws \Exception
	 */
	function install_update_tables() {
	  	global $db;
	  	foreach ($this->tables as $table => $create_table_sql) {
	    	if (!db_table_exists($table)) {
		  		if (!$db->Execute($create_table_sql)) throw new \Exception (sprintf("Error installing table: %s", $table));
			}
	  	}
	}
	
	function remove_tables() {
	  global $db;
	  foreach ($this->tables as $table) {
		if (db_table_exists($table)){
			if ($db->Execute('DROP TABLE ' . $table)) throw new \Exception (sprintf("Error deleting table: %s", $table));
		}
	  }
	}
}
?>