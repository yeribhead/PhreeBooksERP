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
//  Path: /includes/classes/currencies.php
//
namespace core\classes;
class currencies {
  public $currencies = array();
  
  function __construct() {
    global $db;
    $currencies = $db->Execute("select * from " . TABLE_CURRENCIES);
    while (!$currencies->EOF) {
	  $this->currencies[$currencies->fields['code']] = array(
	    'title'           => $currencies->fields['title'],
	    'symbol_left'     => $currencies->fields['symbol_left'],
	    'symbol_right'    => $currencies->fields['symbol_right'],
	    'decimal_point'   => $currencies->fields['decimal_point'],
	    'thousands_point' => $currencies->fields['thousands_point'],
	    'decimal_places'  => $currencies->fields['decimal_places'],
	    'decimal_precise' => $currencies->fields['decimal_precise'],
	    'value'           => $currencies->fields['value'],
	  );
      $currencies->MoveNext();
    }
	if (DEFAULT_CURRENCY == '') { // do not put this in the translation file, it is loaded before the language file is loaded.
	  trigger_error('You do not have a default currency set, PhreeBooks requires a default currency to operate properly! Please set the default currency in Setup -> Currencies.');
	}
  }

  // omits the symbol_left and symbol_right (just the formattted number))
  function format($number, $calculate_currency_value = true, $currency_type = DEFAULT_CURRENCY, $currency_value = '') {
    if ($calculate_currency_value) {
      $rate = ($currency_value) ? $currency_value : $this->currencies[$currency_type]['value'];
      $format_string = number_format($number * $rate, $this->currencies[$currency_type]['decimal_places'], $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']);
    } else {
      $format_string = number_format($number, $this->currencies[$currency_type]['decimal_places'], $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']);
    }
    return $format_string;
  }

  // omits the symbol_left and symbol_right (just the formattted number to the precision number of decimals))
  function precise($number, $calculate_currency_value = true, $currency_type = DEFAULT_CURRENCY, $currency_value = '') {
    if ($calculate_currency_value) {
	  $rate = ($currency_value) ? $currency_value : $this->currencies[$currency_type]['value'];
	  $format_string = number_format($number * $rate, $this->currencies[$currency_type]['decimal_precise'], $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']);
    } else {
	  $format_string = number_format($number, $this->currencies[$currency_type]['decimal_precise'], $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']);
    }
    return $format_string;
  }

  function format_full($number, $calculate_currency_value = true, $currency_type = DEFAULT_CURRENCY, $currency_value = '', $output_format = PDF_APP) {
    if ($calculate_currency_value) {
	  $rate = ($currency_value) ? $currency_value : $this->currencies[$currency_type]['value'];
	  $format_number = number_format($number * $rate, $this->currencies[$currency_type]['decimal_places'], $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']);
    } else {
	  $format_number = number_format($number, $this->currencies[$currency_type]['decimal_places'], $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']);
    }
	$zero = number_format(0, $this->currencies[$currency_type]['decimal_places']); // to handle -0.00
	if ($format_number == '-'.$zero) $format_number = $zero;
	$format_string = $this->currencies[$currency_type]['symbol_left'] . ' ' . $format_number . ' ' . $this->currencies[$currency_type]['symbol_right'];
    switch ($output_format) {
	  case 'FPDF': // assumes default character set
	    $format_string = str_replace('&euro;', chr(128),  $format_string); // Euro
	    break;
	  default:
    }
    return $format_string;
  }

  function get_value($code) {
    return $this->currencies[$code]['value'];
  }

  function clean_value($number, $currency_type = DEFAULT_CURRENCY) {
    // converts the number to standard float format (period as decimal, no thousands separator)
    $temp  = str_replace($this->currencies[$currency_type]['thousands_point'], '', trim($number));
    $value = str_replace($this->currencies[$currency_type]['decimal_point'], '.', $temp);
    $value = preg_replace("/[^-0-9.]+/","",$value);
    return $value;
  }

  function build_js_currency_arrays() {
	$js_codes  = 'var js_currency_codes = new Array(';
	$js_values = 'var js_currency_values = new Array(';
	foreach ($this->currencies as $code => $values) {
		$js_codes  .= "'" . $code . "',";
		$js_values .= $this->currencies[$code]['value'] . ",";
	}
	$js_codes  = substr($js_codes, 0, -1) . ");";
	$js_values = substr($js_values, 0, -1) . ");";
	return $js_codes . chr(10) . $js_values . chr(10);
  }
}