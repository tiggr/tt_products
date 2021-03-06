<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007-2007 Franz Holzinger <kontakt@fholzinger.com>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
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
 * functions for the static_info_countries table view
 *
 * $Id$
 *
 * @author	Franz Holzinger <kontakt@fholzinger.com>
 * @maintainer	Franz Holzinger <kontakt@fholzinger.com>
 * @package TYPO3
 * @subpackage tt_products
 */
class tx_ttproducts_country_view extends tx_ttproducts_table_base_view {


	/**
	 * Template marker substitution
	 * Fills in the markerArray with data for a country
	 *
	 * @param	array		reference to an item array with all the data of the item
	 * @param	array		marker array
	 * @param	[type]		$fieldsArray: ...
	 * @return	array
	 * @access private
	 */
	function getItemMarkerArray (&$row, &$markerArray, &$fieldsArray)	{
		global $TSFE;

		$markerTable = implode('',t3lib_div::trimExplode('_',$this->getTableObj()->name));

		foreach ($fieldsArray as $k => $field)	{
			$markerArray['###'.strtoupper($markerTable.'_'.$field).'###'] = htmlentities($row [$field],ENT_QUOTES,$TSFE->renderCharset);
		}
	}

}



if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/tt_products/view/class.tx_ttproducts_country_view.php'])	{
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/tt_products/view/class.tx_ttproducts_country_view.php']);
}


?>