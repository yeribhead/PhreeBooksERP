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
// |                                                                 |
// | The license that is bundled with this package is located in the |
// | file: /doc/manual/ch01-Introduction/license.html.               |
// | If not, see http://www.gnu.org/licenses/                        |
// +-----------------------------------------------------------------+
//  Path: /modules/phreebooks/dashboards/todays_audit_log/todays_audit_log.php
//
namespace phreedom\dashboards\todays_audit_log;
class todays_audit_log extends \core\classes\ctl_panel {
	public $dashboard_id 		= 'todays_audit_log';
	public $description	 		= CP_TODAYS_AUDIT_LOG_DESCRIPTION;
	public $max_length   		= 50;
	public $security_id  		= SECURITY_ID_CONFIGURATION;
	public $title		 		= CP_TODAYS_AUDIT_LOG_TITLE;
	public $version      		= 3.5;
	public $size_params			= 1;
	public $default_params 		= array('num_rows'=> 0);
	
 
	function Output($params) {
		global $db, $currencies;
		if(count($params) != $this->size_params){ //upgrading
			$params = $this->Upgrade($params);
		}
		$list_length = array();
		$contents = '';
		$control  = '';
		for ($i = 0; $i <= $this->max_length; $i++) $list_length[] = array('id' => $i, 'text' => $i);
	 
	// Build control box form data
	    $control  = '<div class="row">';
	    $control .= '<div style="white-space:nowrap">' . TEXT_SHOW . TEXT_SHOW_NO_LIMIT;
	    $control .= html_pull_down_menu('todays_audit_log_num_rows', $list_length, $params['num_rows']);
	    $control .= html_submit_field('sub_todays_audit_log', TEXT_SAVE);
	    $control .= '</div></div>';
	
	// Build content box
	    $sql = "select a.action_date, a.action, a.reference_id, a.amount, u.display_name from ".TABLE_AUDIT_LOG." as a, ".TABLE_USERS." as u where a.user_id = u.admin_id and a.action_date >= '" . date('Y-m-d',  time()) . "' order by a.action_date desc";
	    if ($params['num_rows']) $sql .= " limit " . $params['num_rows'];
	    $result = $db->Execute($sql);
	    if ($result->RecordCount() < 1) {
	    	$contents = ACT_NO_RESULTS;
	    } else {
	    	while (!$result->EOF) {
	        	$contents .= '<div style="float:right">' . $currencies->format_full($result->fields['amount'], true, DEFAULT_CURRENCY, 1, 'fpdf') . '</div>';
	            $contents .= '<div>';
	            $contents .= $result->fields['display_name'] . '-->';
	            $contents .= $result->fields['action'] . '-->';
	            $contents .= $result->fields['reference_id'];
	            $contents .= '</div>' . chr(10);
	            $result->MoveNext();
	        }
	    }
		return $this->build_div('', $contents, $control);
	}
 
 	function Update() {
 		if(count($this->params) == 0){
        	$this->params['num_rows'] = db_prepare_input($_POST['todays_audit_log_num_rows']);
 		}
		parent::Update();
 	}
  
}

?>
