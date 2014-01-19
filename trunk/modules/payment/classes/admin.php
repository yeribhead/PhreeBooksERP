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
//  Path: /modules/payment/classes/admin.php
//
namespace payment\classes;
class admin extends \core\classes\admin {
	public $methods = array();
	public $module 			= 'payment';
	
  	function __construct() {
		$this->prerequisites = array( // modules required and rev level for this module to work properly
	  	  'contacts'   => 3.71,
	  	  'phreedom'   => 3.6,
	  	  'phreebooks' => 3.6,
		);
		//load and remove all modules
		$this->methods = return_all_methods($this->module, false);
		parent::__construct();
  	}

  	function install() {
		$error = false;
		foreach ($this->methods as $method) {
	  		write_configure('MODULE_' . strtoupper($module) . '_' . strtoupper($method->id) . '_STATUS', '1');
	  		foreach ($method->key as $key) write_configure($key['key'], $key['default']);
	  		if (method_exists($method, 'install')) $method->install();
		}
    	return $error;
  	}

	function update() {
	    global $db, $messageStack;
		$error = false;
		foreach ($this->methods as $method) {
	    	foreach ($method->keys() as $key) {
	    		if(!defined($key['key'])) write_configure($key['key'], $key['default']);
			}
		}
		if (!$error) {
		  write_configure('MODULE_' . strtoupper($this->module) . '_STATUS', constant('MODULE_' . strtoupper($this->module) . '_VERSION'));
	   	  $messageStack->add(sprintf(GEN_MODULE_UPDATE_SUCCESS, $this->module, constant('MODULE_' . strtoupper($this->module) . '_VERSION')), 'success');
		}
		return $error;
	}

	function remove() {
		$error = false;
	  	foreach ($this->methods as $method) {
	    	remove_configure('MODULE_' . strtoupper($module) . '_' . strtoupper($method->id) . '_STATUS');
	    	foreach ($method->keys() as $key) remove_configure($key['key']);
	    	if (method_exists($method, 'remove')) $method->remove();
	  	}
		return $error;
	}

}
?>