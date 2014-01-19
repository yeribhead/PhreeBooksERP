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
//  Path: /modules/shipping/classes/shipping.php
//
namespace shipping\classes;
class shipping {
	public $id;
  	public $text;  
  	public $description;
  	public $sort_order;
  	public $key             = array();
  	public $installed		= false; 
  	public $version;
  	public $shipping_cost;
  	public $handling_cost;

// class constructor
	function __construct() {
		$this->key[] = array('key' => 'MODULE_SHIPPING_'.strtoupper($this->id).'_TITLE', 		'default' => $this->text,			'text' => SHIPPING_TITLE_DESC);
		$this->key[] = array('key' => 'MODULE_SHIPPING_'.strtoupper($this->id).'_SORT_ORDER',   'default' => $this->sort_order,     'text' => SORT_ORDER_DESC);
		$this->key[] = array('key' => 'MODULE_SHIPPING_'.strtoupper($this->id).'_COST',		 	'default' => $this->shipping_cost,  'text' => SHIPPING_COST_DESC);
		if($this->handling_cost || defined('MODULE_SHIPPING_'.strtoupper($this->id).'_HANDLING')) $this->key[] = array('key' => 'MODULE_SHIPPING_'.strtoupper($this->id).'_HANDLING',   'default' => $this->handling_cost,     'text' => SHIPPING_HANDLING_DESC);
		$this->installed = defined('MODULE_SHIPPING_' . strtoupper($this->id) . '_STATUS');
  	}
/**
 * 
 * this method is used when you update config settings.
 */  
  	function update() {
    	foreach ($this->keys() as $key) {
	  		$field = strtolower($key['key']);
	  		if (isset($_POST[$field])) write_configure($key['key'], $_POST[$field]);
		}
  	}
  	
  	/**
  	 * this method is called to get all the configurable settings.
  	 */
	function keys() {
        return $this->key;
  	}
  	
  	/**
  	 * this method is called by the admin page to define how the input field should look.
  	 * @param unknown_type $key
  	 */
  	function configure($key) {
    	switch ($key) {
        	default:
                return html_input_field(strtolower($key), constant($key));
    	}
  	}
  	
  	/**
  	 * 
  	 * this method returns the current sort order of the module
  	 */
  	
  	function getsortorder(){
        if(!defined('MODULE_SHIPPING_'.strtoupper($this->id).'_SORT_ORDER')){
                return $this->sort_order;
        } else {
                return constant('MODULE_SHIPPING_'.strtoupper($this->id).'_SORT_ORDER');
        }
  	}
}
?>
