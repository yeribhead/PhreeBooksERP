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
//  Path: /modules/phreebooks/pages/popup_bills/template_main.php
//
echo html_form('popup_bills', FILENAME_DEFAULT, gen_get_all_get_params(array('action', 'list'))) . chr(10);
// include hidden fields
echo html_hidden_field('action', '')   . chr(10);
echo html_hidden_field('rowSeq', '') . chr(10);
// customize the toolbar actions
$toolbar->icon_list['cancel']['params'] = 'onclick="self.close()"';
$toolbar->icon_list['open']['show']     = false;
$toolbar->icon_list['save']['show']     = false;
$toolbar->icon_list['delete']['show']   = false;
$toolbar->icon_list['print']['show']    = false;
if (count($extra_toolbar_buttons) > 0) foreach ($extra_toolbar_buttons as $key => $value) $toolbar->icon_list[$key] = $value;
switch (JOURNAL_ID) {
  case 18: $toolbar->add_help('07.05.02'); break;
  case 20: $toolbar->add_help('07.05.01'); break;
}
$toolbar->search_period = $acct_period;
echo $toolbar->build_toolbar($add_search = true, $add_period = true); 
// Build the page
?>
<h1><?php echo GEN_HEADING_PLEASE_SELECT; ?></h1>
<div style="height:19px"><?php echo $query_split->display_count(TEXT_DISPLAY_NUMBER . (JOURNAL_ID == 18 ? TEXT_RECEIPTS : TEXT_PAYMENTS)); ?>
<div style="float:right"><?php echo $query_split->display_links(); ?></div>
</div>
<table class="ui-widget" style="border-collapse:collapse;width:100%">
 <thead class="ui-widget-header">
  <tr valign="top"><?php echo $list_header; ?></tr>
 </thead>
 <tbody class="ui-widget-content">
  <?php // build the javascript constructor for creating each address object
  $odd = true;
  while (!$query_result->EOF) { ?>
  <tr class="<?php echo $odd?'odd':'even'; ?>" style="cursor:pointer" onclick='setReturnEntry(<?php echo $query_result->fields['id']; ?>)'>
	<td><?php echo gen_locale_date($query_result->fields['post_date']); ?></td>
	<?php if (ENABLE_MULTI_BRANCH) { ?>
	  <td><?php echo $query_result->fields['store_id'] ? gen_get_contact_name($query_result->fields['store_id']) : COMPANY_ID; ?></td>
	<?php } ?>
	<td><?php echo $query_result->fields['purchase_invoice_id']; ?></td>
	<td><?php echo $currencies->format($query_result->fields['total_amount']); ?></td>
	<td><?php echo $query_result->fields['bill_primary_name']; ?></td>
  </tr>
  <?php
	$query_result->MoveNext();
	$odd = !$odd;
  } ?>
 </tbody>
</table>
<div style="float:right"><?php echo $query_split->display_links(); ?></div>
<div><?php echo $query_split->display_count(TEXT_DISPLAY_NUMBER . (JOURNAL_ID == 18 ? TEXT_RECEIPTS : TEXT_PAYMENTS)); ?></div>
</form>
