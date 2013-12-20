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
//  Path: /modules/phreeform/defaults.php
//

define('DEFAULT_MODULE','phreebooks'); // for importing selection dropdown
define('PF_DIR_MY_REPORTS',  DIR_FS_MY_FILES . $_SESSION['company'] . '/phreeform/');
define('PF_WEB_MY_REPORTS',  DIR_WS_MY_FILES . $_SESSION['company'] . '/phreeform/');
define('PF_DIR_DEF_REPORTS', 'modules/' . DEFAULT_MODULE . '/language/' . DEFAULT_LANGUAGE . '/reports/');
define('PF_DIR_DEF_IMAGE_LINK', DIR_FS_MY_FILES . $_SESSION['company'] . '/');
define('nl',"\n");

$phreeformTypes = array(
  'frm' => TEXT_FORM,
  'rpt' => TEXT_REPORT,
);

$Fonts = array (
  'helvetica' => PF_FONT_HELVETICA,
  'courier'   => PF_FONT_COURIER,
  'times'     => PF_FONT_TIMES,
);
if (PDF_APP == 'TCPDF') {
  $Fonts['freeserif'] = PF_FONT_SERIF;
  define('PDF_DEFAULT_FONT','freeserif');
} else {
  define('PDF_DEFAULT_FONT','helvetica');
}

// Paper sizes supported in fpdf class, includes dimensions width, length in mm for page setup
$PaperSizes = array (
  'Legal:216:357'  => TEXT_LEGAL,
  'Letter:216:282' => TEXT_LETTER,
  'A3:297:420'     => 'A3',
  'A4:210:297'     => 'A4',
  'A5:148:210'     => 'A5',
);
if (PDF_APP == 'TCPDF') { // TCPDF supports more paper sizes, see website for all available
  $PaperSizes['A0:841x1189']     = 'A0';
  $PaperSizes['A1:594:841']      = 'A1';
  $PaperSizes['A2:420:594']      = 'A2';
  $PaperSizes['A6:105:148']      = 'A6';
  $PaperSizes['A7:74:105']       = 'A7';
  $PaperSizes['A8:52:74']        = 'A8';
  $PaperSizes['A9:37:52']        = 'A9';
  $PaperSizes['Tabloid:279:432'] = TEXT_TABLOID;
}

// Available font sizes in units: points
$FontSizes = array (
  '8'  => '8', 
  '9'  => '9', 
  '10' => '10', 
  '11' => '11', 
  '12' => '12', 
  '14' => '14', 
  '16' => '16', 
  '18' => '18', 
  '20' => '20', 
  '24' => '24', 
  '28' => '28', 
  '32' => '32', 
  '36' => '36', 
  '40' => '40', 
  '50' => '50',
);

// Available font sizes in units: points
$LineSizes = array (
  '1' => '1', 
  '2' => '2', 
  '3' => '3', 
  '4' => '4', 
  '5' => '5', 
  '6' => '6', 
  '7' => '7', 
  '8' => '8', 
  '9' => '9', 
  '10'=>'10',
);

// Font colors keyed by color Red:Green:Blue
$FontColors = array (
  'custom'      => TEXT_CUSTOM, // must be at the beginning
  '0:0:0'       => TEXT_BLACK, // Leave black first as it is typically the default value
  '255:0:0'     => TEXT_RED,
  '255:128:0'   => TEXT_ORANGE,
  '255:255:0'   => TEXT_YELLOW,
  '0:255:0'     => TEXT_GREEN,
  '0:0:255'     => TEXT_BLUE,
  '255:255:255' => TEXT_WHITE,
);

$NoYesChoice = array(
  '0' => TEXT_NO,
  '1' => TEXT_YES,
);


// The below functions are used to convert a number to language for USD (primarily for checks)
function value_to_words_en_us($number) {
  $number   = round($number, 2);
  $position = array('', ' '.TEXT_THOUSAND, ' '.TEXT_MILLION, ' '.TEXT_BILLION, ' '.TEXT_TRILLION);
  $dollars  = intval($number);
  $cents    = round(($number - $dollars) * 100);
  if (strlen($cents) == 1) $cents = '0' . $cents;
  if ($dollars < 1) {
	$output = TEXT_ZERO;
  } else {
	$output = build_1000_words($dollars, $position);
  }
  return strtoupper($output . ' ' . TEXT_DOLLARS . ' ' . TEXT_AND . ' ' . $cents . '/100');
}

function build_1000_words($number, $position) {
  $output   = '';
  $suffix   = array_shift($position);
  $tens     = $number % 100;
  $number   = intval($number / 100);
  $hundreds = $number % 10;
  $number   = intval($number / 10);
  if ($number >= 1) $output = build_1000_words($number, $position);
  switch ($hundreds) {
	case 1: $output .= ' ' . TEXT_ONE   . ' ' . TEXT_HUNDERD; break;
	case 2: $output .= ' ' . TEXT_TWO   . ' ' . TEXT_HUNDERD; break;
	case 3: $output .= ' ' . TEXT_THREE . ' ' . TEXT_HUNDERD; break;
	case 4: $output .= ' ' . TEXT_FOUR  . ' ' . TEXT_HUNDERD; break;
	case 5: $output .= ' ' . TEXT_FIVE  . ' ' . TEXT_HUNDERD; break;
	case 6: $output .= ' ' . TEXT_SIX   . ' ' . TEXT_HUNDERD; break;
	case 7: $output .= ' ' . TEXT_SEVEN . ' ' . TEXT_HUNDERD; break;
	case 8: $output .= ' ' . TEXT_EIGHT . ' ' . TEXT_HUNDERD; break;
	case 9: $output .= ' ' . TEXT_NINE  . ' ' . TEXT_HUNDERD; break;
  }
  $output .= build_100_words($tens);
  return $output . $suffix;
}

function build_100_words($number) {
  if ($number > 9 && $number < 20) {
	switch ($number) {
	  case 10: return ' ' . TEXT_TEN;
	  case 11: return ' ' . TEXT_ELEVEN;
	  case 12: return ' ' . TEXT_TWELVE;
	  case 13: return ' ' . TEXT_THIRTEEN;
	  case 14: return ' ' . TEXT_FOURTEEN;
	  case 15: return ' ' . TEXT_FIFTEEN;
	  case 16: return ' ' . TEXT_SIXTEEN;
	  case 17: return ' ' . TEXT_SEVENTEEN;
	  case 18: return ' ' . TEXT_EIGHTEEN;
	  case 19: return ' ' . TEXT_NINETEEN;
	}
  }
  $output = '';
  $tens = intval($number / 10);
  switch ($tens) {
	case 2: $output .= ' ' . TEXT_TWENTY;  break;
	case 3: $output .= ' ' . TEXT_THIRTY;  break;
	case 4: $output .= ' ' . TEXT_FORTY;   break;
	case 5: $output .= ' ' . TEXT_FIFTY;   break;
	case 6: $output .= ' ' . TEXT_SIXTY;   break;
	case 7: $output .= ' ' . TEXT_SEVENTY; break;
	case 8: $output .= ' ' . TEXT_EIGHTY;  break;
	case 9: $output .= ' ' . TEXT_NINETY;  break;
  }
  $ones = $number % 10;
  switch ($ones) {
	case 1: $output .= (($output) ? '-' : ' ') . TEXT_ONE;   break;
	case 2: $output .= (($output) ? '-' : ' ') . TEXT_TWO;   break;
	case 3: $output .= (($output) ? '-' : ' ') . TEXT_THREE; break;
	case 4: $output .= (($output) ? '-' : ' ') . TEXT_FOUR;  break;
	case 5: $output .= (($output) ? '-' : ' ') . TEXT_FIVE;  break;
	case 6: $output .= (($output) ? '-' : ' ') . TEXT_SIX;   break;
	case 7: $output .= (($output) ? '-' : ' ') . TEXT_SEVEN; break;
	case 8: $output .= (($output) ? '-' : ' ') . TEXT_EIGHT; break;
	case 9: $output .= (($output) ? '-' : ' ') . TEXT_NINE;  break;
  }
  return $output;
}

// This array is imploded with the first entry = number of text boxes to build (0, 1 or 2), 
// the remaining is the dropdown menu listings
$CritChoices = array(
   0 => '2:ALL:RANGE:EQUAL',
   1 => '0:YES:NO',
   2 => '0:ALL:YES:NO',
   3 => '0:ALL:ACTIVE:INACTIVE',
   4 => '0:ALL:PRINTED:UNPRINTED',
// 5 => NOT_USED_AVAILABLE,
   6 => '1:EQUAL',
   7 => '2:RANGE',
   8 => '1:NOT_EQUAL',
   9 => '1:IN_LIST',
  10 => '1:LESS_THAN',
  11 => '1:GREATER_THAN',
);

$PaperOrientation = array (
  'P' => TEXT_PORTRAIT,
  'L' => TEXT_LANDSCAPE,
);
	
$FontAlign = array (
  'L' => TEXT_LEFT,
  'R' => TEXT_RIGHT,
  'C' => TEXT_CENTER,
);

// DataTypes
// A corresponding class function needs to be generated for each new function added.
// The index code is also used to identify the form to include to set the properties.
$FormEntries = array(
  'Data'    => PF_FRM_DATALINE,
  'TBlk'    => PF_FRM_DATABLOCK,
  'Tbl'     => PF_FRM_DATATABLE,
  'TDup'    => PF_FRM_DATATABLEDUP,
  'Ttl'     => PF_FRM_DATATOTAL,
  'LtrTpl'  => PF_FRM_LETTER_TEMPLATE,
  'LtrData' => PF_FRM_LETTER_DATA,
  'Text'    => PF_FRM_FIXEDTXT,
  'Img'     => PF_FRM_IMAGE,
  'ImgLink' => PF_FRM_IMAGE_LINK,
  'Rect'    => PF_FRM_RECTANGLE,
  'Line'    => PF_FRM_LINE,
  'CDta'    => PB_PF_COMPANY_DATA,
  'CBlk'    => PB_PF_COMPANY_BLOCK,
  'PgNum'   => PF_FRM_PAGENUM,
);
if (PDF_APP == 'TCPDF') {
  $FormEntries['BarCode'] = PF_FRM_BAR_CODE;
}

// The function to process these values is: ProcessData
// A case statement needs to be generated to process each new value
$FormProcessing = array(
  ''         => TEXT_NONE,
  'uc'       => PF_FRM_UPPERCASE,
  'lc'       => PF_FRM_LOWERCASE,
  'neg'      => PF_FRM_NEGATE,
  'n2wrd'    => PF_FRM_NUM_2_WORDS,
  'rnd2d'    => PF_FRM_RNDR2,
  'date'     => PF_FRM_DATE,
  'dlr'      => PF_FRM_CNVTDLR,
  'null-dlr' => PF_FRM_NULLDLR,
  'euro'     => PF_FRM_CNVTEURO,
  'yesBno'   => PB_PF_YES_SKIP_NO,
  'blank'    => PF_FRM_BLANK_DATA,
  'printed'  => TEXT_PRINTED_INDICATOR,
);

// The function to process these values is: AddSep
// A case statement needs to be generated to process each new value
$TextProcessing = array(
  ''        => TEXT_NONE,
  'sp'      => PF_FRM_SPACE1,
  '2sp'     => PF_FRM_SPACE2,
  'comma'   => PF_FRM_COMMA,
  'com-sp'  => PF_FRM_COMMASP,
  'nl'      => PF_FRM_NEWLINE,
  'semi-sp' => PF_FRM_SEMISP,
  'del-nl'  => PF_FRM_DELNL,
);

// Bar code Types (for use with TCPDF)
$BarCodeTypes = array(
  'C39'     => 'CODE 39',
  'C39+'    => 'CODE 39 w/checksum',
  'C39E'    => 'CODE 39 EXTENDED',
  'C39E+'   => 'CODE 39 EXT w/checksum',
  'I25'     => 'Interleaved 2 of 5',
  'C128A'   => 'CODE 128 A',
  'C128B'   => 'CODE 128 B',
  'C128C'   => 'CODE 128 C',
  'EAN13'   => 'EAN 13',
  'UPCA'    => 'UPC-A',
  'POSTNET' => 'POSTNET',
  'CODABAR' => 'CODABAR',
);

$joinSyntax = array(
  'JOIN'                     => 'JOIN',
  'LEFT JOIN'                => 'LEFT JOIN',
  'RIGHT JOIN'               => 'RIGHT JOIN',
  'INNER JOIN'               => 'INNER JOIN',
  'CROSS JOIN'               => 'CROSS JOIN',
  'STRAIGHT_JOIN'            => 'STRAIGHT_JOIN',
  'LEFT OUTER JOIN'          => 'LEFT OUTER JOIN',
  'RIGHT OUTER JOIN'         => 'RIGHT OUTER JOIN',
  'NATURAL LEFT JOIN'        => 'NATURAL LEFT JOIN',
  'NATURAL RIGHT JOIN'       => 'NATURAL RIGHT JOIN',
  'NATURAL LEFT OUTER JOIN'  => 'NATURAL LEFT OUTER JOIN',
  'NATURAL RIGHT OUTER JOIN' => 'NATURAL RIGHT OUTER JOIN',
);

/***************************************************************************************************/
// Include custom additions and overrides for module specific enhancement
/***************************************************************************************************/
if (file_exists(DIR_FS_MODULES."phreeform/custom/extra_phreeform.php")) { // user custom additions
	require_once (DIR_FS_MODULES . "phreeform/custom/extra_phreeform.php");
}
if (is_array($loaded_modules)) foreach ($loaded_modules as $mod) {
  if (file_exists(DIR_FS_MODULES . "$mod/config_phreeform.php")) {
	gen_pull_language($mod, 'admin');
    require_once (DIR_FS_MODULES . "$mod/config_phreeform.php");
  }
}
/***************************************************************************************************/

// Processing functions
function ProcessData($strData, $Process) {
  global $loaded_modules;
//echo 'process = ' . $Process . ' and posted cur = '; print_r($posted_currencies); echo '<br />';
  switch ($Process) {
	case "uc":      return strtoupper_utf8($strData);
	case "lc":      return strtolower_utf8($strData);
	case "neg":     return -$strData;
	case "n2wrd":   return value_to_words_en_us($strData);
	case "rnd2d":   if (!is_numeric($strData)) return $strData;
	                return number_format(round($strData, 2), 2, '.', '');
	case "date":    return gen_locale_date($strData);
	case "null-dlr":return (real)$strData == 0 ? '' : '$ ' . number_format($strData, 2);
	case "dlr":     if (!is_numeric($strData)) return $strData;
	                return '$ ' . number_format(round($strData, 2), 2);
	case "euro":    if (!is_numeric($strData)) return $strData;
	                return chr(128) . ' ' . number_format(round($strData, 2), 2); // assumes standard FPDF fonts
	case 'yesBno':  return ($strData) ? TEXT_YES : '';
	case 'blank':   return '';
	case 'printed': return ($strData) ? '' : TEXT_DUPLICATE;
  }
  // now try loaded modules for processing
  foreach ($loaded_modules as $mod) {
    $mod_function = "pf_process_" . $mod;
    if (function_exists($mod_function)) $strData = $mod_function($strData, $Process);
  }
  if (function_exists('pf_extra_process')) $strData = pf_extra_process($strData, $Process);
  return $strData;
}

function AddSep($value, $Process) {
  switch ($Process) {
	case "sp":      return $value . ' ';
	case "2sp":     return $value . '  ';
	case "comma":   return $value . ',';
	case "com-sp":  return $value . ', ';
	case "nl":      return $value . chr(10);
	case "semi-sp": return $value . '; ';
	case "del-nl":  return ($value == '') ? '' : $value . chr(10);
  }
  $separator_value = false;
  if (function_exists('pf_extra_separators')) $separator_value = pf_extra_separators($value, $Process);
  return ($separator_value === false) ? $value : $separator_value; // do nothing if Process not recognized
}

function TextReplace($text_string) {
  global $report;
  // substitutes a command string with dynamic information
  $text_string = str_replace('%date%',       gen_locale_date(date('Y-m-d')),$text_string);
  $text_string = str_replace('%reportname%', $report->title,                $text_string);
  $text_string = str_replace('%company%',    COMPANY_NAME,                  $text_string);
  return $text_string;
}

?>
