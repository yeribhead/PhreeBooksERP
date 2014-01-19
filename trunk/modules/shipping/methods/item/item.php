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
//  Path: /modules/shipping/methods/item/item.php
//
namespace shipping\methods\item;
// Revision history
// 2011-07-01 - Added version number for revision control
define('MODULE_SHIPPING_ITEM_VERSION','3.2');

class item extends \shipping\classes\shipping {
	public $id				= 'item'; // needs to match class name
  	public $text			= MODULE_SHIPPING_ITEM_TEXT_TITLE;
  	public $description		= MODULE_SHIPPING_ITEM_TEXT_DESCRIPTION;
  	public $sort_order		= 30;
  	public $version			= 3.2;
  	public $shipping_cost	= 0.00;
  	public $handling_cost	= 1.00;
  	
	function __construct() {
    	parent::__construct();
  	}

	function quote($pkg = '') {
		if (!$pkg->pkg_item_count) $pkg->pkg_item_count = 1;
		$arrRates = array();
		$arrRates[$this->id]['GND']['book']  = '';
		$arrRates[$this->id]['GND']['quote'] = ($pkg->pkg_item_count * MODULE_SHIPPING_ITEM_COST) + MODULE_SHIPPING_ITEM_HANDLING;
		$arrRates[$this->id]['GND']['cost']  = '';
		return array('result' => 'success', 'rates' => $arrRates);
  	}
}
?>