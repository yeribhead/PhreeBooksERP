<?php
namespace inventory;
class sa extends \inventory\inventory {//Serialized Assembly
	public $inventory_type			= 'sa';
	public $title 					= INV_TYPES_SA;
	public $serialize 				= 1;
    public $account_sales_income	= INV_SERIALIZE_DEFAULT_SALES;
	public $account_inventory_wage	= INV_SERIALIZE_DEFAULT_INVENTORY;
	public $account_cost_of_sales	= INV_SERIALIZE_DEFAULT_COS; 
	public $cost_method				= 'f';
	public $bom		 				= array();
	public $allow_edit_bom			= true;
	public $posible_cost_methodes   = array('f');	
	
	function __construct(){
		parent::__construct();
		$this->tab_list['bom'] = array('file'=>'template_tab_bom',	'tag'=>'bom',    'order'=>30, 'text'=>INV_BOM);
	}
	
	function get_item_by_id($id){
		parent::get_item_by_id($id);
		$this->get_bom_list();
		$this->allow_edit_bom = (($this->last_journal_date == '0000-00-00 00:00:00' || $this->last_journal_date == '') && ($this->quantity_on_hand == 0|| $this->quantity_on_hand == '')) ? true : false;
	}
	
	function get_item_by_sku($sku){
		parent::get_item_by_sku($sku);
		$this->get_bom_list();
		$this->allow_edit_bom = (($this->last_journal_date == '0000-00-00 00:00:00' || $this->last_journal_date == '') && ($this->quantity_on_hand == 0|| $this->quantity_on_hand == '')) ? true : false;
	}
	
	function get_bom_list(){
		global $db;
		$this->assy_cost = 0;
		$result = $db->Execute("select i.id as inventory_id, l.id, l.sku, l.description, l.qty from " . TABLE_INVENTORY_ASSY_LIST . " l join " . TABLE_INVENTORY . " i on l.sku = i.sku where l.ref_id = " . $this->id . " order by l.id");
		$x =0;
		while (!$result->EOF) {
	  		$this->bom[$x] = $result->fields;
	  		$prices = inv_calculate_sales_price(abs($result->fields['qty']), $result->fields['inventory_id'], 0, 'v');
			$this->bom[$x]['item_cost'] = strval($prices['price']);
			$this->assy_cost += $result->fields['qty'] * strval($prices['price']);
	  		$prices = inv_calculate_sales_price(abs($result->fields['qty']), $result->fields['inventory_id'], 0, 'c');
			$this->bom[$x]['full_price'] = strval($prices['price']);
	  		$x++;
	  		$result->MoveNext();
		}
	}
	
	function remove(){
		global $db;
		parent::remove();
		$db->Execute("delete from " . TABLE_INVENTORY_ASSY_LIST . " where sku = '" . $this->sku . "'");
	}
	
	function save(){
		global $db, $currencies, $messageStack;
		$bom_list = array();
		for($x=0; $x < count($_POST['assy_sku']); $x++) {
			$bom_list[$x] = array(
			  	'ref_id'      => $this->id,
			  	'sku'         => db_prepare_input($_POST['assy_sku'][$x]),
				'description' => db_prepare_input($_POST['assy_desc'][$x]),
				'qty'         => $currencies->clean_value(db_prepare_input($_POST['assy_qty'][$x])),
			);
		  	$result = $db->Execute("select id from " . TABLE_INVENTORY . " where sku = '". $_POST['assy_sku'][$x]."'" );
		  	if (($result->RecordCount() == 0 || $currencies->clean_value($_POST['assy_qty'][$x]) == 0) && $_POST['assy_sku'][$x] =! '') { 
		  		// show error, bad sku, negative quantity. error check sku is valid and qty > 0
				$error = $messageStack->add(INV_ERROR_BAD_SKU . db_prepare_input($_POST['assy_sku'][$x]), 'error');
		  	}else{
		  		$prices = inv_calculate_sales_price(abs($this->bom[$x]['qty']), $result->fields['id'], 0, 'v');
				$bom_list[$x]['item_cost'] = strval($prices['price']);
		  		$prices = inv_calculate_sales_price(abs($this->bom[$x]['qty']), $result->fields['id'], 0, 'c');
				$bom_list[$x]['full_price'] = strval($prices['price']);
		  	}
		}
		$this->bom = $bom_list;
		if (!parent::save()) return false;	
		$result = $db->Execute("select last_journal_date, quantity_on_hand  from " . TABLE_INVENTORY . " where id = " . $this->id);
		$this->allow_edit_bom = (($result->fields['last_journal_date'] == '0000-00-00 00:00:00' || $result->fields['last_journal_date'] == '') && ($result->fields['quantity_on_hand'] == 0|| $result->fields['quantity_on_hand'] == '')) ? true : false;
		if($error) return false;
	  	if ($this->allow_edit_bom == true) { // only update if no posting has been performed
	  		$result = $db->Execute("delete from " . TABLE_INVENTORY_ASSY_LIST . " where ref_id = " . $this->id);
			while ($list_array = array_shift($bom_list)) {
				unset($list_array['item_cost']);
				unset($list_array['full_price']);
				db_perform(TABLE_INVENTORY_ASSY_LIST, $list_array, 'insert');
			}
	  	}
	  	return true;
	}
}