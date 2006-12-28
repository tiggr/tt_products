<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005-2006 Franz Holzinger <kontakt@fholzinger.com>
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
 * functions for the display of forms
 *
 * $Id$
 *
 * @author  Franz Holzinger <kontakt@fholzinger.com>
 * @package TYPO3
 * @subpackage tt_products
 *
 *
 */

class tx_ttproducts_form_div {


	function createSelect (&$pibase, &$tcaArray, $name) {
		global $TYPO3_DB;

		$text = '';
		foreach ($tcaArray as $key => $parts) {
			$tmp = tx_fhlibrary_language::sL($parts[0]);
			$text = $pibase->pi_getLL($tmp);
			$totaltext .= '<OPTION value="'.$parts[1].'">'.$text.'</OPTION>';
		}
		$text = '<SELECT name="'.$name.'">' . $totaltext.'</SELECT>';  

		return $text;
	}

}




?>