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
//  Path: /modules/phreedom/pages/users/js_include.php
//

?>
<script type="text/javascript">
<!--
// pass any php variables generated during pre-process that are used in the javascript functions.
// Include translations here as well.
var delete_pushed = false;

function init() {
}

function check_form() {
  return true;
}

// Insert other page specific functions here.
function copyItem(id) {
  var adminName = prompt('<?php echo GEN_MSG_COPY_INTRO; ?>', '');
  if (adminName) {
	location.href = 'index.php?module=phreedom&page=users&action=copy&cID='+id+'&name='+adminName;
  } else {
	return false;
  }
}

// -->
</script>