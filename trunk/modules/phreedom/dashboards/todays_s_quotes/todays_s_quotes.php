<?php
// +-----------------------------------------------------------------+
// |                   PhreeBooks Open Source ERP                    |
// +-----------------------------------------------------------------+
// | Copyright (c) 2008, 2009, 2010 PhreeSoft, LLC                   |
// | http://www.PhreeSoft.com                                        |
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
// |                                                                 |
// | The license that is bundled with this package is located in the |
// | file: /doc/manual/ch01-Introduction/license.html.               |
// | If not, see http://www.gnu.org/licenses/                        |
// +-----------------------------------------------------------------+
//  Path: /modules/phreebooks/dashboards/todays_s_quotes/todays_s_quotes.php
//
namespace phreedom\dashboards\todays_s_quotes;
class todays_s_quotes extends \core\classes\ctl_panel {

  function __construct() {
    $this->max_length = 20;
  }

  function Install($column_id = 1, $row_id = 0) {
	global $db;
	if (!$row_id) $row_id = $this->get_next_row();
	$params['num_rows'] = '';	// defaults to unlimited rows
	$result = $db->Execute("insert into " . TABLE_USERS_PROFILES . " set 
	  user_id = "       . $_SESSION['admin_id'] . ", 
	  menu_id = '"      . $this->menu_id . "', 
	  module_id = '"    . $this->module_id . "', 
	  dashboard_id = '" . $this->dashboard_id . "', 
	  column_id = "     . $column_id . ", 
	  row_id = "        . $row_id . ", 
	  params = '"       . serialize($params) . "'");
  }

  function Remove() {
	global $db;
	$result = $db->Execute("delete from " . TABLE_USERS_PROFILES . " 
      where user_id = " . $_SESSION['admin_id'] . " and menu_id = '" . $this->menu_id . "' 
	    and dashboard_id = '" . $this->dashboard_id . "'");
  }

  function Output($params) {
	global $db, $currencies;
	$list_length = array();
	$contents = '';
	$control  = '';
	for ($i = 0; $i <= $this->max_length; $i++) $list_length[] = array('id' => $i, 'text' => $i);
	// Build control box form data
	$control  = '<div class="row">';
	$control .= '<div style="white-space:nowrap">' . TEXT_SHOW . TEXT_SHOW_NO_LIMIT;
	$control .= html_pull_down_menu('todays_s_quotes_field_0', $list_length, $params['num_rows']);
	$control .= html_submit_field('sub_todays_s_quotes', TEXT_SAVE);
	$control .= '</div></div>';

	// Build content box
	$total = 0;
	$sql = "select id, purchase_invoice_id, total_amount, bill_primary_name, currencies_code, currencies_value 
	  from " . TABLE_JOURNAL_MAIN . " 
	  where journal_id = 9 and post_date = '" . date('Y-m-d') . "' order by purchase_invoice_id";
	if ($params['num_rows']) $sql .= " limit " . $params['num_rows'];
	$result = $db->Execute($sql);
	if ($result->RecordCount() < 1) {
	  $contents = CP_TODAYS_S_QUOTES_NO_RESULTS;
	} else {
	  while (!$result->EOF) {
	 	$total += $result->fields['total_amount'];
		$contents .= '<div style="float:right">' . $currencies->format_full($result->fields['total_amount'], true, $result->fields['currencies_code'], $result->fields['currencies_value']) . '</div>';
		$contents .= '<div>';
//		$contents .= '<a href="' . html_href_link(FILENAME_DEFAULT, 'module=phreebooks&amp;page=orders&amp;oID=' . $result->fields['id'] . '&amp;jID=10&amp;action=edit', 'SSL') . '">';
                $contents .= '<a href="' . html_href_link(FILENAME_DEFAULT, 'module=phreebooks&amp;page=orders&amp;oID=' . $result->fields['id'] . '&amp;jID=9&amp;action=edit', 'SSL') . '">';

//              $contents .= '<a href="' . html_href_link(FILENAME_DEFAULT, 'cat=orders&amp;module=orders&amp;oID=' . $result->fields['id'] . '&amp;jID=9&amp;action=edit', 'SSL') . '">';                
		$contents .= $result->fields['purchase_invoice_id'] . ' - ';
		$contents .= htmlspecialchars($result->fields['bill_primary_name']);
		$contents .= '</a></div>' . chr(10);
		$result->MoveNext();
	  }
	}
	if (!$params['num_rows'] && $result->RecordCount() > 0) {
	  $contents .= '<div style="float:right">' . $currencies->format_full($total, true, $result->fields['currencies_code'], $result->fields['currencies_value']) . '</div>';
	  $contents .= '<div><b>' . TEXT_TOTAL . '</b></div>' . chr(10);
	}
	return $this->build_div(CP_TODAYS_S_QUOTES_TITLE, $contents, $control);
  }

  function Update() {
	global $db;
	$params['num_rows'] = db_prepare_input($_POST['todays_s_quotes_field_0']);
	$db->Execute("update " . TABLE_USERS_PROFILES . " set params = '" . serialize($params) . "' 
	  where user_id = " . $_SESSION['admin_id'] . " and menu_id = '" . $this->menu_id . "' 
	    and dashboard_id = '" . $this->dashboard_id . "'");
  }
}
?>
