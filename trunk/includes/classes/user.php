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
//  Path: /includes/classes/user.php
//
namespace core\classes;
class user {
	private $language  = 'en_us';
	
	function __construct(){
	}
	
	static public function is_validated(){
		if(isset($_SESSION['admin_id'])){
			return true;
		}else {
			return false;	
		}
	}
	
	static public function get_language(){
		if   (isset($_REQUEST['language'])) {
			 $_SESSION['language'] = $_REQUEST['language']; 
		} elseif (!isset($_SESSION['language'])) { 
			$_SESSION['language'] = defined('DEFAULT_LANGUAGE') ? DEFAULT_LANGUAGE : $this->language; 
		}
		return $_SESSION['language'];
	}
	
}