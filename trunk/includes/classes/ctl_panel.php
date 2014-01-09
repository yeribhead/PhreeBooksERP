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
//  Path: /includes/classes/ctl_panel.php
//
namespace core\classes;
class ctl_panel {
	public $dashboard_id 		= '';
	public $default_num_rows 	= 20;
	public $description	 		= '';
	public $max_length   		= 20;
	public $menu_id				= 'index';
	public $module_id 			= '';
	public $params				= '';
	public $security_id  		= '';
	public $title		 		= '';
	public $version      		= 1;
	public $valid_user			= false;
	public $size_params			= 0;
	public $default_params 		= array();
	public $row_started			= false;
	
  	function __construct() {
  		if ($this->security_id <> '' ) $this->valid_user = ($_SESSION['admin_security'][$this->security_id] > 0)? true : false;
  		else $this->valid_user = true;	
  	}
  
  	function pre_install($odd, $my_profile){
  		if(!$this->valid_user) return false;
		$output  = '<tr class="'.($odd?'odd':'even').'"><td align="center">';
		$checked = (in_array($this->dashboard_id, $my_profile)) ? ' selected' : '';
		$output .=  html_checkbox_field($this->dashboard_id, '1', $checked, '', $parameters = '');
		$output .=' </td><td>' . $this->title . '</td><td>' . $this->description . '</td></tr>';
		return $output;
	}
  
  	function Install($column_id = 1, $row_id = 0) {
		global $db;
		if (!$row_id) $row_id 		= $this->get_next_row();
		//$this->params['num_rows']   = $this->default_num_rows;	// defaults to unlimited rows
		$result = $db->Execute("insert into " . TABLE_USERS_PROFILES . " set 
			user_id = "       . $_SESSION['admin_id'] . ", 
			menu_id = '"      . $this->menu_id . "', 
		  	module_id = '"    . $this->module_id . "', 
		  	dashboard_id = '" . $this->dashboard_id . "', 
		  	column_id = "     . $column_id . ", 
		  	row_id = "        . $row_id . ", 
		  	params = '"       . serialize($this->default_params) . "'");
  	}

  	function Remove() {
		global $db;
		$result = $db->Execute("delete from " . TABLE_USERS_PROFILES . " 
	  	where user_id = " . $_SESSION['admin_id'] . " and menu_id = '" . $this->menu_id . "' and dashboard_id = '" . $this->dashboard_id . "'");
  	}

  	function Update() {
  		global $db;
  		$db->Execute("update " . TABLE_USERS_PROFILES . " set params = '" . serialize($this->params) . "' 
	  		where user_id = " . $_SESSION['admin_id'] . " and menu_id = '" . $this->menu_id . "' 
	    	and dashboard_id = '" . $this->dashboard_id . "'");
  	}
  
  	function build_div($title, $contents, $controls) {
	  	if(!$this->valid_user) return false;
	  	$output = '';
	  	if($this->version < 3.5 || ! $this->version ) $output .= 'update dashboard ' . $this->title . '<br/>';
		$output .= '<!--// start: ' . $this->dashboard_id . ' //-->' . chr(10);
		$output .= '<div id="'.$this->dashboard_id.'" style="position:relative;" class="easyui-panel" title="'.$this->title.'" data-options="collapsible:true,tools:\'#'.$this->dashboard_id.'_tt\'">' . chr(10);
		// heading text
		$output .= '<div id="'.$this->dashboard_id.'_tt">' . chr(10);
		if ($this->column_id > 1) 				$output .= '	<a href="javascript:void(0)" class="icon-go_previous"	onclick="return move_box(\'' . $this->dashboard_id . '\', \'move_left\')"></a>' . chr(10);
		if ($this->column_id < MAX_CP_COLUMNS)	$output .= '	<a href="javascript:void(0)" class="icon-go_next"		onclick="return move_box(\'' . $this->dashboard_id . '\', \'move_right\')"></a>' . chr(10);
		if ($this->row_started == false)		$output .= '	<a href="javascript:void(0)" class="icon-go_up"    onclick="return move_box(\'' . $this->dashboard_id . '\', \'move_up\')"></a>' . chr(10);
		if ($this->row_id < $this->get_next_row($this->column_id) - 1)
												$output .= '	<a href="javascript:void(0)" class="icon-go_down"    onclick="return move_box(\'' . $this->dashboard_id . '\', \'move_down\')"></a>' . chr(10);
		$output .= '	<a id="'.$this->dashboard_id.'_add" href="javascript:void(0)" class="icon-edit"    onclick="return box_edit(\''.$this->dashboard_id.'\')"></a>' . chr(10);
		$output .= '	<a id="'.$this->dashboard_id.'_can" href="javascript:void(0)" class="icon-undo"    onclick="return box_cancel(\'' . $this->dashboard_id . '\')" style="display:none"></a>' . chr(10);
		$output .= '	<a id="'.$this->dashboard_id.'_del" href="javascript:void(0)" class="icon-cancel"  onclick="return del_box(\'' . $this->dashboard_id . '\')"></a>' . chr(10);
		//$output .= '	<a href="javascript:void(0)" class="icon-help" onclick="javascript:alert(help)"></a>' . chr(10);
		$output .= '</div>' . chr(10);
		$output .= '<table style="border-collapse:collapse;width:100%">'. chr(10);
		// properties contents
		$output .= '<tbody class="ui-widget-content">' . chr(10);
		$output .= '<tr id="' . $this->dashboard_id . '_prop" style="display:none"><td colspan="4">' . chr(10);
		$output .= html_form($this->dashboard_id . '_frm', FILENAME_DEFAULT, gen_get_all_get_params(array('action'))) . chr(10);
		$output .= $controls . chr(10);
		$output .= '<input type="hidden" name="dashboard_id" value="' . $this->dashboard_id . '" />' . chr(10);
		$output .= '<input type="hidden" name="column_id" value="' . $this->column_id . '" />' . chr(10);
		$output .= '<input type="hidden" name="row_id" value="' . $this->row_id . '" />' . chr(10);
		$output .= '<input type="hidden" name="action" id="' . $this->dashboard_id . '_action" value="save" />' . chr(10);
		$output .= '</form></td></tr>' . chr(10);
		$output .= '<tr id="' . $this->dashboard_id . '_hr" style="display:none"><td colspan="4"><hr /></td></tr>' . chr(10);
		// box contents
		$output .= '<tr><td colspan="4">' . chr(10);
		$output .= '<div id="' . $this->dashboard_id . '_body">' . chr(10);
		$output .= $contents;
		$output .= '</div>';
		$output .= '</td></tr></tbody></table>' . chr(10);
		// finish it up
		$output .= '</div>' . chr(10);
		$output .= '<!--// end: ' . $this->dashboard_id . ' //--><br />' . chr(10) . chr(10);
		return $output;
  	}

	function get_next_row($column_id = 1) {
		global $db;
		$result = $db->Execute("select max(row_id) as max_row from " . TABLE_USERS_PROFILES . " 
		  where user_id = " . $_SESSION['admin_id'] . " and menu_id = '" . $this->menu_id . "' and column_id = " . $column_id);
		return ($result->fields['max_row'] + 1);
	}
	
	function Upgrade($params){
		foreach ($this->default_params as $key => $value){
			if(in_array($key, $params, false)){
				$this->params[$key] =  $params[$key];
			}else{
				$this->params[$key] =  $value;
			}
		}
		$this->Update();
		return $this->params;
	}
}