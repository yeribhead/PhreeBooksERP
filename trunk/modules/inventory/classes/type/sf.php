<?php
namespace inventory;
class sf extends \inventory\inventory {//Flat Rate - Service
	public $inventory_type			= 'sf';
	public $title			       	= INV_TYPES_SF;
	public $account_sales_income	= INV_SERVICE_DEFAULT_SALES;
	public $account_inventory_wage	= INV_SERVICE_DEFAULT_INVENTORY;
	public $account_cost_of_sales	= INV_SERVICE_DEFAULT_COS; 
	public $cost_method				= 'f';
	public $posible_cost_methodes   = array('f');
		  
	function update_inventory_status($sku, $field, $adjustment, $item_cost, $vendor_id, $desc){
		return true;
	}
}
