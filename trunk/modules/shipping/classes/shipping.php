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
		if(defined('MODULE_SHIPPING_' . strtoupper($this->id) . '_STATUS')){
			$this->installed = true;
			$this->sort_order = constant('MODULE_SHIPPING_'.strtoupper($this->id).'_SORT_ORDER');
		}

// 		$this->service_levels[] = array('id' => 'GND', 		'text' => SHIPPING_GND, 	'quote' => '', 'book' => '', 'cost' => '', 'note' => '');
// 		$this->service_levels[] = array('id' => 'GDR', 		'text' => SHIPPING_GDR, 	'quote' => '', 'book' => '', 'cost' => '', 'note' => '');
// 		$this->service_levels[] = array('id' => 'GndFrt', 	'text' => SHIPPING_GNDFRT, 	'quote' => '', 'book' => '', 'cost' => '', 'note' => '');
// 		$this->service_levels[] = array('id' => 'EcoFrt', 	'text' => SHIPPING_ECOFRT, 	'quote' => '', 'book' => '', 'cost' => '', 'note' => '');
// 		$this->service_levels[] = array('id' => '1DEam', 	'text' => SHIPPING_1DEAM, 	'quote' => '', 'book' => '', 'cost' => '', 'note' => '');
// 		$this->service_levels[] = array('id' => '1Dam', 	'text' => SHIPPING_1DAM, 	'quote' => '', 'book' => '', 'cost' => '', 'note' => '');
// 		$this->service_levels[] = array('id' => '1Dpm', 	'text' => SHIPPING_1DPM, 	'quote' => '', 'book' => '', 'cost' => '', 'note' => '');
// 		$this->service_levels[] = array('id' => '1DFrt',	'text' => SHIPPING_1DFRT,	'quote' => '', 'book' => '', 'cost' => '', 'note' => '');
// 		$this->service_levels[] = array('id' => '2Dam', 	'text' => SHIPPING_2DAM, 	'quote' => '', 'book' => '', 'cost' => '', 'note' => '');
// 		$this->service_levels[] = array('id' => '2Dpm', 	'text' => SHIPPING_2DPM, 	'quote' => '', 'book' => '', 'cost' => '', 'note' => '');
// 		$this->service_levels[] = array('id' => '2DFrt', 	'text' => SHIPPING_2DFRT, 	'quote' => '', 'book' => '', 'cost' => '', 'note' => '');
// 		$this->service_levels[] = array('id' => '3Dam', 	'text' => SHIPPING_3DAM, 	'quote' => '', 'book' => '', 'cost' => '', 'note' => '');
// 		$this->service_levels[] = array('id' => '3Dpm', 	'text' => SHIPPING_3DPM, 	'quote' => '', 'book' => '', 'cost' => '', 'note' => '');
// 		$this->service_levels[] = array('id' => 'I2DEam', 	'text' => SHIPPING_I2DEAM, 	'quote' => '', 'book' => '', 'cost' => '', 'note' => '');
// 		$this->service_levels[] = array('id' => 'I2Dam', 	'text' => SHIPPING_I2DAM, 	'quote' => '', 'book' => '', 'cost' => '', 'note' => '');
// 		$this->service_levels[] = array('id' => 'I3D', 		'text' => SHIPPING_I3D, 	'quote' => '', 'book' => '', 'cost' => '', 'note' => '');
// 		$this->service_levels[] = array('id' => 'IGND', 	'text' => SHIPPING_IGND, 	'quote' => '', 'book' => '', 'cost' => '', 'note' => '');
  		  		
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
  		global $currencies;
    	switch ($key) {
    		case 'MODULE_SHIPPING_'.strtoupper($this->id).'_COST':
    		case 'MODULE_SHIPPING_'.strtoupper($this->id).'_HANDLING':
    			return html_input_field(strtolower($key), $currencies->format( constant($key)));	
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
