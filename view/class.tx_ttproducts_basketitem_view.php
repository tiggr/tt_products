<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2006 Kasper Sk�rh�j (kasperYYYY@typo3.com)
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Part of the tt_products (Shop System) extension.
 *
 * view functions for a basket item object
 *
 * $Id$
 *
 * @author	Franz Holzinger <kontakt@fholzinger.com>
 * @package TYPO3
 * @subpackage tt_products
 *
 *
 */



class tx_ttproducts_basketitem_view {
	var $tt_products_cat; // element of class tx_table_db
	var $basketExt; 	// basket
	var $basketVar = 'ttp_basket';

	
	/**
	 * Initialized the basket, setting the deliveryInfo if a users is logged in
	 * $basket is the TYPO3 default shopping basket array from ses-data
	 *
	 * @param		string		  $fieldname is the field in the table you want to create a JavaScript for
	 * @return	  void
 	 */

	function init(&$tt_products_cat, &$basketExt)	{
 		$this->tt_products_cat = &$tt_products_cat;
 		$this->basketExt = &$basketExt;
	} // init


	/**
	 * Template marker substitution
	 * Fills in the markerArray with data for a product
	 *
	 * @param	array		reference to an item array with all the data of the item
	 * @param	string		title of the category
	 * @param	integer		number of images to be shown
	 * @param	object		the image cObj to be used
	 * @param	array		information about the parent HTML form
	 * @return	array
	 * @access private
	 */
	function &getItemMarkerArray (&$viewTable, &$item, &$markerArray, &$tagArray, $code='', $id='1')	{
			// Returns a markerArray ready for substitution with information for the tt_producst record, $row

		$row = &$item['rec'];
		$basketQuantityName = $this->basketVar.'['.$row['uid'].'][quantity]';
		$quantity = $item['count'];
		$markerArray['###FIELD_NAME###']=$basketQuantityName;
		$markerArray['###FIELD_NAME_BASKET###'] = $this->basketVar.'['.$row['uid'].']['.md5($row['extVars']).']';
		$markerArray['###FIELD_QTY###']= $quantity ? $quantity : '';
		$markerArray['###FIELD_ID###'] = TT_PRODUCTS_EXTkey.'_'.strtolower($code).'_id_'.$id;

		$fieldArray = $viewTable->variant->getFieldArray();
		$keyAdditional = '';
		foreach ($fieldArray as $k => $field)	{
			if ($field == 'additional')	{
				$keyAdditional = $k;
			} else {
				$fieldMarker = strtoupper($field);
				$markerArray['###FIELD_'.$fieldMarker.'_NAME###'] = $this->basketVar.'['.$row['uid'].']['.$field.']';
				$markerArray['###FIELD_'.$fieldMarker.'_VALUE###'] = $row[$field];
				$markerArray['###FIELD_'.$fieldMarker.'_ONCHANGE'] = ''; // TODO:  use $forminfoArray['###FORM_NAME###' in something like onChange="Go(this.form.Auswahl.options[this.form.Auswahl.options.selectedIndex].value)"
			}
			if (isset($row['extVars']))	{	
				$markerArray['###PRODUCT_'.strtoupper($field).'###'] = $row[$field];
			}
		}
		// $markerArray['###FIELD_ADDITIONAL_NAME###'] = 'ttp_basket['.$row['uid'].'][additional]';
		$prodAdditionalText['single'] = '';	
		if (isset($keyAdditional)) {
			$isSingleProduct = $viewTable->hasAdditional($row,'isSingle');
			if ($isSingleProduct)	{
				$message = $this->pibase->pi_getLL('additional_single');
				$prodAdditionalText['single'] = $message.'<input type="checkbox" name="'.$basketQuantityName.'" '.($quantity ? 'checked="checked"':'').'onchange = "this.form[this.name+\'[1]\'].value=(this.checked ? 1 : 0);"'.' value="1">';
				$prodAdditionalText['single'] .= '<input type="hidden" name="'.$basketQuantityName.'[1]" value="'.($quantity ? '1' : '0') .'">';
			}
 		}
		$markerArray['###PRODUCT_ADDITIONAL_SINGLE###'] = $prodAdditionalText['single'];
	} // getItemMarkerArray

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tt_products/view/class.tx_ttproducts_basketitem_view.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tt_products/view/class.tx_ttproducts_basketitem_view.php']);
}


?>
