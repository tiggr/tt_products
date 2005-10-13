<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2005 Kasper Skaarhoj (kasperYYYY@typo3.com)
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
 * Part of the tt_products (Shopping System) extension.
 *
 * basket functions without a basket object
 *
 * $Id$
 *
 * @author	Kasper Sk�rh�j <kasperYYYY@typo3.com>
 * @author	Ren� Fritz <r.fritz@colorcube.de>
 * @author	Franz Holzinger <kontakt@fholzinger.com>
 * @author	Klaus Zierer <zierer@pz-systeme.de>
 * @author	Els Verberne <verberne@bendoo.nl>
 * @package TYPO3
 * @subpackage tt_products
 *
 *
 */
 

require_once (PATH_BE_ttproducts.'lib/class.tx_ttproducts_article_div.php');
require_once (PATH_BE_ttproducts.'lib/class.tx_ttproducts_creditpoints_div.php');
require_once (PATH_BE_ttproducts.'lib/class.tx_ttproducts_finalize_div.php');
require_once (PATH_BE_ttproducts.'lib/class.tx_ttproducts_order_div.php');
require_once (PATH_BE_ttproducts.'lib/class.tx_ttproducts_page_div.php');
require_once (PATH_BE_ttproducts.'lib/class.tx_ttproducts_paymentshipping_div.php');
require_once (PATH_BE_ttproducts.'lib/class.tx_ttproducts_price_div.php');
require_once (PATH_BE_ttproducts.'lib/class.tx_ttproducts_pricecalc_div.php');
require_once (PATH_BE_ttproducts.'lib/class.tx_ttproducts_view_div.php');


class tx_ttproducts_basket_div {

	/**
	 * Initialized the basket, setting the deliveryInfo if a users is logged in
	 * $basket is the TYPO3 default shopping basket array from ses-data
     *
     * @param       string          $fieldname is the field in the table you want to create a JavaScript for
     * @return      void
 	 */

	function initBasket($basket, $updateMode)	{
		global $TSFE;

		$this->recs = $basket;	// Sets it internally
		$this->basket=array();

		$tmpBasketExt = $TSFE->fe_user->getKey('ses','basketExt');

		if (is_array($tmpBasketExt)) {
			$this->basketExt = $tmpBasketExt;
		} else {
			$this->basketExt = array();
		}

		$basketExtRaw = t3lib_div::_GP('ttp_basket');

		$this->giftnumber = count ($this->basketExt['gift']) + 1;

		$newGiftData = t3lib_div::_GP('ttp_gift');
		$extVars= t3lib_div::_GP('ttp_extvars');
		$uid = t3lib_div::_GP('tt_products');

		$sameGiftData = true;
		$identGiftnumber = 0;
		if ($newGiftData) {
	 		$giftnumber = t3lib_div::_GP('giftnumber');
			if (!$updateMode) {
			 	if (is_array($this->basketExt['gift'])) {
					foreach ($this->basketExt['gift'] as $prevgiftnumber => $rec) {
						$sameGiftData = true;
						foreach ($rec as $field => $value) {
							// only the 'field' field can be different
							if ($field != 'item' && $field != 'note' && $value != $newGiftData[$field]) {
								$sameGiftData = false;
								break;
							}
						}
						if ($sameGiftData) {
							$identGiftnumber = $prevgiftnumber;
							// always use the latest note
							$this->basketExt['gift'][$identGiftnumber]['note'] = $newGiftData['note'];
							break;
						}
					}

			 	} else {
					$sameGiftData = false;
				}
				if (!$sameGiftData) {
					$this->basketExt['gift'][$this->giftnumber] = $newGiftData;
				}
			} else {
				$this->basketExt['gift'][$giftnumber] = $newGiftData;
				$count = 0;
				foreach ($this->basketExt['gift'] as $prevgiftnumber => $rec) {
					$count += $rec['item'][$uid][$extVars];
				}
				// update the general basket entry for this product
				$this->basketExt[$uid][$extVars] = $count;
			}

		}

		if (is_array($basketExtRaw)) {
			while(list($uid,$basketItem)=each($basketExtRaw)) {
				$variant = tx_ttproducts_article_div::getVariantFromRow($basketItem);
				if (t3lib_div::testInt($uid))   {
					if (!$updateMode) {
						$count=t3lib_div::intInRange($basketItem['quantity'],0,100000,0);
						if ($count>=0) {
							$this->basketExt[$uid][$variant] = $count;
							if ($newGiftData) {
								if ($sameGiftData) {
									$this->basketExt['gift'][$identGiftnumber]['item'][$uid][$variant] = $count;
								}
								else {
									$this->basketExt['gift'][$this->giftnumber]['item'][$uid][$variant] = $count;
								}
							}
						}
					}
					else {
						reset($basketItem);

						while(list($md5,$quantity)=each($basketItem)) {
							if (is_array($this->basketExt[$uid]))
							{
								reset($this->basketExt[$uid]);
								while(list($beVars,)=each($this->basketExt[$uid])) {
									 // useArticles if you have different prices and therefore articles for color, size, accessory and gradings
									if (/*!$this->conf['useArticles'] ||*/ md5($beVars)==$md5) {
										$this->basketExt[$uid][$beVars] = $quantity;
									}
								}
							}
						}
					}
				}
			}
		}


		// I did not find another possibility to delete elements complete from a multidimensional array
		// than to recreate the array
		$basketExtNew = array();
		reset($this->basketExt);
		while(list($tmpUid,$tmpSubArr)=each($this->basketExt)) {
			while(list($tmpExtVar,$tmpCount)=each($tmpSubArr)) {
				if ($tmpCount > 0) {
					$basketExtNew[$tmpUid][$tmpExtVar] = $tmpCount;
				}
			}
		}
		$this->basketExt = $basketExtNew;

		if (is_array($this->basketExt) && count($this->basketExt))
			$TSFE->fe_user->setKey('ses','basketExt',$this->basketExt);
		else
			$TSFE->fe_user->setKey('ses','basketExt',array());
		$TSFE->fe_user->storeSessionData(); // Franz: The basket shall not get lost

		tx_ttproducts_paymentshipping_div::setBasketExtras($basket);

		$this->personInfo = $basket['personinfo'];
		$this->deliveryInfo = $basket['delivery'];
		if ($TSFE->loginUser && !$this->personInfo && $this->conf['lockLoginUserInfo'])	{
			$address = '';

			if ($this->conf['loginUserInfoAddress']) {
				$address = implode(chr(10),
					t3lib_div::trimExplode(chr(10),
						$TSFE->fe_user->user['address'].chr(10).
						$TSFE->fe_user->user['zip'].' '.$TSFE->fe_user->user['city'].chr(10).
						($this->conf['useStaticInfoCountry'] ? $TSFE->fe_user->user['static_info_country']:$TSFE->fe_user->user['country'])
						,1)
					);
			}
			else {
				$address = $TSFE->fe_user->user['address'];
			}

/* Added Els: getting the field uid form fe_users and introducing the field feusers_uid into sys_products_orders */
			$this->personInfo['feusers_uid'] = $TSFE->fe_user->user['uid'];
			$this->personInfo['name'] = $TSFE->fe_user->user['name'];

			$this->personInfo['address'] = $address;
			$this->personInfo['email'] = $TSFE->fe_user->user['email'];

			$this->personInfo['telephone'] = $TSFE->fe_user->user['telephone'];
			$this->personInfo['fax'] = $TSFE->fe_user->user['fax'];
			$this->personInfo['zip'] = $TSFE->fe_user->user['zip'];
			$this->personInfo['city'] = $TSFE->fe_user->user['city'];
			$this->personInfo['country'] = ($this->conf['useStaticInfoCountry'] ? $TSFE->fe_user->user['static_info_country']:$TSFE->fe_user->user['country']);
			$this->personInfo['agb'] = $TSFE->fe_user->user['agb'];
			$this->personInfo['date_of_birth'] = date( 'd-m-Y', $TSFE->fe_user->user["date_of_birth"]);
			$this->personInfo['company'] = $TSFE->fe_user->user['company'];

			if ($this->feuserextrafields) {
				/* Added Els: getting the fields for displaying in the BASKET_PAYMENT_TEMPLATE  from fe_user */
				$this->personInfo['tx_feuserextrafields_initials_name'] = $TSFE->fe_user->user['tx_feuserextrafields_initials_name'];
				$this->personInfo['tx_feuserextrafields_prefix_name'] = $TSFE->fe_user->user['tx_feuserextrafields_prefix_name'];
				$this->personInfo['tx_feuserextrafields_gsm_tel'] = $TSFE->fe_user->user['tx_feuserextrafields_gsm_tel'];
				$this->personInfo['tx_feuserextrafields_company_deliv'] = $TSFE->fe_user->user['tx_feuserextrafields_company_deliv'];
				$this->personInfo['tx_feuserextrafields_address_deliv'] = $TSFE->fe_user->user['tx_feuserextrafields_address_deliv'];
				$this->personInfo['tx_feuserextrafields_housenumber'] = $TSFE->fe_user->user['tx_feuserextrafields_housenumber'];
				$this->personInfo['tx_feuserextrafields_housenumber_deliv'] = $TSFE->fe_user->user['tx_feuserextrafields_housenumber_deliv'];
				$this->personInfo['tx_feuserextrafields_housenumberadd'] = $TSFE->fe_user->user['tx_feuserextrafields_housenumberadd'];
				$this->personInfo['tx_feuserextrafields_housenumberadd_deliv'] = $TSFE->fe_user->user['tx_feuserextrafields_housenumberadd_deliv'];
				$this->personInfo['tx_feuserextrafields_pobox'] = $TSFE->fe_user->user['tx_feuserextrafields_pobox'];
				$this->personInfo['tx_feuserextrafields_pobox_deliv'] = $TSFE->fe_user->user['tx_feuserextrafields_pobox_deliv'];
				$this->personInfo['tx_feuserextrafields_zip_deliv'] = $TSFE->fe_user->user['tx_feuserextrafields_zip_deliv'];
				$this->personInfo['tx_feuserextrafields_city_deliv'] = $TSFE->fe_user->user['tx_feuserextrafields_city_deliv'];
				$this->personInfo['tx_feuserextrafields_country'] = $TSFE->fe_user->user['tx_feuserextrafields_country'];
				$this->personInfo['tx_feuserextrafields_country_deliv'] = $TSFE->fe_user->user['tx_feuserextrafields_country_deliv'];
			}

/* Added Els: getting the field tt_products_creditpoints and tt_products_vouchercode from fe_user */
			$this->personInfo['tt_products_creditpoints'] = $TSFE->fe_user->user['tt_products_creditpoints'];
			$this->personInfo['tt_products_vouchercode'] = $TSFE->fe_user->user['tt_products_vouchercode'];
		}
	} // initBasket



	/**
	 * Takes care of basket, address info, confirmation and gate to payment
	 * Also the 'products_...' script parameters are used here.
     *
     * @param       array          CODEs for display mode
     * @return      void
 	 */
	function products_basket($codes, &$error_message)	{
		global $TSFE;
		global $TYPO3_DB;

		$content = '';
		reset ($codes);
		
		$activityArr=array();

		if (t3lib_div::_GP('products_redeem_gift'))	{
		 	$activityArr['products_redeem_gift']=true;
		}
		if (t3lib_div::_GP('products_payment'))	{
			$activityArr['products_payment']=true;
		}

		if (is_array($codes)) {
			foreach ($codes as $k => $code) {
				if ($code=='BASKET')	{
					$activityArr['products_basket']=true;
				} elseif ($code=='INFO')	{
					$activityArr['products_info']=true;
				} elseif ($code=='OVERVIEW') {
					$activityArr['products_overview']=true;
				} elseif ($code=='PAYMENT')	{
					$activityArr['products_payment']=true;
				} elseif ($code=='FINALIZE')	{
					$activityArr['products_finalize']=true;
				}
			}
		}

		if (t3lib_div::_GP('products_overview'))	{
			$activityArr['products_overview']=true;
		}
		if (t3lib_div::_GP('products_info'))	{
			$activityArr['products_info']=true;
		}
		if (t3lib_div::_GP('products_finalize'))	{
			$activityArr['products_finalize']=true;
		}
		
		if (count($this->basketExt) && count($activityArr))	{	// If there is content in the shopping basket, we are going display some basket code
				// prepare action

			reset ($activityArr);
			$basket_tmpl = '';
			if (count($activityArr)) {
				if (!$this->pid_list) {
					tx_ttproducts_page_div::setPidlist($this->config['storeRootPid']);	// Set list of page id's to the storeRootPid.
				}
				tx_ttproducts_page_div::initRecursive(999);		// This add's all subpart ids to the pid_list based on the rootPid set in previous line
				tx_ttproducts_page_div::generatePageArray();		// Creates an array with page titles from the internal pid_list. Used for the display of category titles.
				tx_ttproducts_basket_div::getCalculatedBasket();  // all the basket calculation is done in this function once and not multiple times here

				$mainMarkerArray=array();
				$mainMarkerArray['###EXTERNAL_COBJECT###'] = $this->externalCObject.'';  // adding extra preprocessing CObject

				foreach ($activityArr as $activity=>$value) {
						// perform action
					switch($activity)	{
						case 'products_basket':
							if (count($activityArr) == 1) {
								$content.=tx_ttproducts_basket_div::getBasket();
							}
						break;
						case 'products_overview':
							$this->load_noLinkExtCobj();
							$basket_tmpl  = 'BASKET_OVERVIEW_TEMPLATE';
						break;
						case 'products_redeem_gift': 	// this shall never be the only activity
							if (trim($TSFE->fe_user->user['username']) == '') {
								$basket_tmpl = 'BASKET_TEMPLATE_NOT_LOGGED_IN';
							} else {
								$uniqueId = t3lib_div::trimExplode ('-', $this->recs['tt_products']['gift_certificate_unique_number'], true);

								$query='uid=\''.intval($uniqueId[0]).'\' AND crdate=\''.$uniqueId[1].'\''.' AND NOT deleted' ;
								$giftRes = $TYPO3_DB->exec_SELECTquery('*', 'tt_products_gifts', $query);

								$row = $TYPO3_DB->sql_fetch_assoc($giftRes);

								if ($row) {
									$money = $row['amount'];
									$uid = $row['uid'];
									$fieldsArray = array();
									$fieldsArray['deleted']=1;
										// Delete the gift record
									$TYPO3_DB->exec_UPDATEquery('tt_products_gifts', 'uid='.intval($uid), $fieldsArray);

									$creditpoints = $money / $this->conf['creditpoints.']['pricefactor'];

									tx_ttproducts_creditpoints_div::addCreditPoints($TSFE->fe_user->user['username'], $creditpoints);

/* Added els5: extra markers for inline comments */
									// Fill marker arrays
									$markerArray=Array();
									$subpartArray=Array();
									$markerArray['###GIFT_DISCOUNT###'] = $creditpoints;
									$markerArray['###VALUE_GIFTCODE###'] = $this->recs['tt_products']['gift_certificate_unique_number'];
									$subpartArray['###SUB_GIFTCODE_DISCOUNTWRONG###']= '';
									$content = $this->cObj->substituteMarkerArrayCached($content,$markerArray,$subpartArray);

								} else {
/* Added els5: inline comments and errors in stead of new page */
									//$basket_tmpl = 'BASKET_TEMPLATE_INVALID_GIFT_UNIQUE_ID';

									// Fill marker arrays
									$markerArray=Array();
									$subpartArray=Array();
									$markerArray['###VALUE_GIFTCODE###'] = $this->recs['tt_products']['gift_certificate_unique_number'];
									$subpartArray['###SUB_GIFTCODE_DISCOUNT###']= '';
									$content = $this->cObj->substituteMarkerArrayCached($content,$markerArray,$subpartArray);

								}
							}
						break;
						case 'products_info':
							if (!$activityArr['products_payment'] && !$activityArr['products_finalize']) {
								$this->load_noLinkExtCobj();
								$content.=tx_ttproducts_basket_div::getBasket('###BASKET_INFO_TEMPLATE###','',$mainMarkerArray);
							}
						break;
						case 'products_payment':
							$this->load_noLinkExtCobj();
							$pidagb = intval($this->conf['PIDagb']);
							$check = tx_ttproducts_view_div::checkRequired();
							if ($check=='' &&
								(empty($pidagb) || isset($_REQUEST['recs']['personinfo']['agb']))) {
								tx_ttproducts_view_div::mapPersonIntoToDelivery();
								$content.=tx_ttproducts_basket_div::getBasket('###BASKET_PAYMENT_TEMPLATE###', '', $mainMarkerArray);

								$handleScript = $TSFE->tmpl->getFileName($this->basketExtra['payment.']['handleScript']);
								$orderUid = tx_ttproducts_order_div::getBlankOrderUid();
								if (trim($this->conf['paymentActivity'])=='payment' && $handleScript)	{
									tx_ttproducts_basket_div::getCalculateSums();
									$content.= tx_ttproducts_pricecalc_div::includeHandleScript($handleScript,$this->basketExtra['payment.']['handleScript.']);
								}

							} else {	// If not all required info-fields are filled in, this is shown instead:
								$content.=$this->cObj->getSubpart($this->templateCode,tx_ttproducts_view_div::spMarker('###BASKET_REQUIRED_INFO_MISSING###'));
								$markerArray = tx_ttproducts_view_div::addURLMarkers(array());
								$label = '';
								if ($check=='') {
									 // so AGB has not been accepted 
									$label = $this->pi_getLL('accept AGB');
								} else {
									if (t3lib_extMgm::isLoaded('sr_feuser_register')) {
										$label = $TSFE->sL('LLL:EXT:sr_feuser_register/pi1/locallang.php:missing_'.$check);
									} else {
										$label = 'field: '.$check;
									}
								}
								$markerArray['###ERROR_DETAILS###'] = $label;
								$content = $this->cObj->substituteMarkerArray($content, $markerArray);
							}
						break;
						case 'products_finalize':
							$check = tx_ttproducts_view_div::checkRequired();
							if ($check=='')	{
								$this->load_noLinkExtCobj();
								tx_ttproducts_view_div::mapPersonIntoToDelivery();
								$handleScript = $TSFE->tmpl->getFileName($this->basketExtra['payment.']['handleScript']);
								$orderUid = tx_ttproducts_order_div::getBlankOrderUid();
								if (trim($this->conf['paymentActivity'])=='finalize' && $handleScript)	{
									//tx_ttproducts_basket_div::getCalculatedBasket();
									tx_ttproducts_basket_div::getCalculateSums();
									$content = tx_ttproducts_pricecalc_div::includeHandleScript($handleScript,$this->basketExtra['payment.']['handleScript.']);
								}

								// Added Els4: to get the orderconfirmation template as html email and the thanks template as thanks page
								$tmpl = 'BASKET_ORDERCONFIRMATION_TEMPLATE';
								$orderConfirmationHTML=tx_ttproducts_basket_div::getBasket('###'.$tmpl.'###', '', $mainMarkerArray);
								$contentTmp = $orderConfirmationHTML;
								tx_ttproducts_finalize_div::finalizeOrder($orderUid, $orderConfirmationHTML); // Important: 	 MUST come after the call of prodObj->getBasket, because this function, getBasket, calculates the order! And that information is used in the finalize-function

								if ($this->conf['PIDthanks'] > 0) {
									$tmpl = 'BASKET_ORDERTHANKS_TEMPLATE';
									$contentTmp = tx_ttproducts_basket_div::getBasket('###'.$tmpl.'###', '', $mainMarkerArray);
								}
								$content.=$contentTmp;
								// Empties the shopping basket!
								tx_ttproducts_basket_div::clearBasket();
							} else {	// If not all required info-fields are filled in, this is shown instead:
								$content.=$this->cObj->getSubpart($this->templateCode,tx_ttproducts_view_div::spMarker('###BASKET_REQUIRED_INFO_MISSING###'));
								$content = $this->cObj->substituteMarkerArray($content, tx_ttproducts_view_div::$this->addURLMarkers(array()));
							}
						break;
						default:
							// nothing yet
						break;
					} // switch
					if ($basket_tmpl) {
						$content.=tx_ttproducts_basket_div::getBasket('###'.$basket_tmpl.'###');
						break; // foreach
					}
				} // foreach ($activityArr as $activity=>$value)
			} else { // if (count($activityArr))
				// nothing. no BASKET code or similar thing
			}
		} else { // if (count($this->basketExt))
			if ($activityArr['products_overview']) {
				$this->load_noLinkExtCobj();
				$content.=$this->cObj->getSubpart($this->templateCode,tx_ttproducts_view_div::spMarker('###BASKET_OVERVIEW_EMPTY###'));
			}
			else if ($activityArr['products_basket']) {
				$content.=$this->cObj->getSubpart($this->templateCode,tx_ttproducts_view_div::spMarker('###BASKET_TEMPLATE_EMPTY###'));
			}
		}
		$markerArray=array();
		$markerArray['###EXTERNAL_COBJECT###'] = $this->externalCObject;	// adding extra preprocessing CObject
		$content= $this->cObj->substituteMarkerArray($content, $markerArray);

		return $content;
	} // products_basket


	/**
	 * Returns a clear 'recs[tt_products]' array - so clears the basket.
	 */
	function getClearBasketRecord()	{
			// Returns a basket-record cleared of tt_product items
		unset($this->recs['tt_products']);
		return ($this->recs);
	} // getClearBasketRecord


	/**
	 * Empties the shopping basket!
	 */
	function clearBasket()	{
		global $TSFE;
		
			// Empties the shopping basket!
		$TSFE->fe_user->setKey('ses','recs',tx_ttproducts_basket_div::getClearBasketRecord());
		$TSFE->fe_user->setKey('ses','basketExt',array());
	} // clearBasket



	/**
	 * This calculates the totals. Very important function.
	This function also calculates the internal arrays

	$this->itemArray				- The basked elements, how many (quantity, count) and the price
	$this->calculatedArray	- Sums of goods, shipping, payment and total amount WITH TAX included

	... which holds the total amount, the final list of products and the price of payment and shipping!!

	 */
	function getCalculatedBasket()	{
		if ($this->itemArray[0]) {// the item array contains all the data for the elements found in the basket
			return;	// this function is called in a loop from getBasket
					// all the calculations however need to be done only once
					// the global member variables must already be filled in
		}
		
		$uidArr = array();
		reset($this->basketExt);
		while(list($uidTmp,)=each($this->basketExt))
			if ($uidTmp != 'gift' && !in_array($uidTmp, $uidArr))
				$uidArr[] = $uidTmp;

		if (count($uidArr) == 0) {
			return;
		}
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tt_products', 'uid IN ('.implode(',',$uidArr).') AND pid IN ('.$this->pid_list.')'.$this->cObj->enableFields('tt_products'));

		$this->productsArray = array(); // TODO: use only local products array
		$productsArray = &$this->productsArray;
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))		{
			reset($this->basketExt[$row['uid']]);
			while(list($bextVars,)=each($this->basketExt[$row['uid']])) {
				tx_ttproducts_article_div::getRowFromVariant ($row, $bextVars);
				$row['extVars'] = $bextVars;
				if ($this->conf['useArticles']) {
					// get the article uid with these colors, sizes and gradings
					$query='uid_product=\''.intval($row['uid']).'\' AND color=\''.$row['color'].'\' AND size=\''.$row['size'].'\' AND gradings=\''.$row['gradings'].'\'';
					$articleRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tt_products_articles', $query);
					if ($articleRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($articleRes)) {
							// use the fields of the article instead of the product
						foreach ($articleRow as $field => $fieldValue) {
							if ($field != 'uid') {
								$row[$field] = $fieldValue;
							}
						}
					}
				}
				$productsArray[$row['pid']][]=$row;
			}
		}

		$pageArr=explode(',',$this->pid_list);

		$this->itemArray = array(); // array of the items in the basket
		$this->calculatedArray = array(); // this array is usede for all calculated things

		while(list(,$v)=each($pageArr))	{
			if (is_array($productsArray[$v]))	{
				reset($productsArray[$v]);
				while(list(,$row)=each($productsArray[$v]))	{
					// if reseller is logged in then take 'price2', default is 'price'
					$newItem = tx_ttproducts_basket_div::getItem($row);
					$this->itemArray [intval($row['pid'])] [intval($row['itemnumber'])][] = $newItem;
					$count = $newItem['count'];
					$priceTax = $newItem['priceTax'];
					$priceNoTax = $newItem['priceNoTax'];

					$this->calculatedArray['count']			+= $count;
					$this->calculatedArray['weight']		+= $row['weight']*$count;

					$oldPriceTax = tx_ttproducts_price_div::getPrice($row['price'],1,$row['tax']);
					$oldPriceNoTax = tx_ttproducts_price_div::getPrice($row['price'],0,$row['tax']);

					/* Added Els3: if oldpricenotax is 0 -> oldpricenotax = pricenotax, otherwise price_discount doesn't calculate correctly */
					if (doubleval($oldPriceNoTax) == 0) {
					   $oldPriceNoTax = $priceNoTax;
					}

					$this->calculatedArray['oldPriceTax']	+= $oldPriceTax * $count;
					$this->calculatedArray['oldPriceNoTax']	+= $oldPriceNoTax * $count;
				}
			}
		}

		// set the 'calcprice' in itemArray
		if ($this->conf['pricecalc.'] || $this->conf['discountprice.']) {
			// do the price calculation
			tx_ttproducts_pricecalc_div::GetCalculatedData();
		}

		// loop over all items in the basket indexed by page and itemnumber
		foreach ($this->itemArray as $pid=>$pidItem) {
			foreach ($pidItem as $itemnumber=>$actItemArray) {
				foreach ($actItemArray as $k1=>$actItem) {
					// has the price been calculated before take it if it gets cheaper now
					if (($actItem['calcprice'] > 0) && ($actItem['calcprice'] < $actItem['priceTax'])) {
						$this->itemArray[$pid][$itemnumber][$k1]['priceTax'] = tx_ttproducts_price_div::getPrice($actItem['calcprice'],1,$actItem['rec']['tax']);
						$this->itemArray[$pid][$itemnumber][$k1]['priceNoTax'] = tx_ttproducts_price_div::getPrice($actItem['calcprice'],0,$actItem['rec']['tax']);
					}
					// If accesssory has been selected, add the price of it, multiplicated with the count :
					if($actItem['rec']['accessory'] > 0 ) {
						$this->itemArray[$pid][$itemnumber][$k1]['totalTax'] = ($this->itemArray[$pid][$itemnumber][$k1]['priceTax']+ tx_ttproducts_price_div::getPrice($actItem['rec']['accessory'],1,$actItem['rec']['tax']))*$actItem['count'];
						$this->itemArray[$pid][$itemnumber][$k1]['totalNoTax'] = ($this->itemArray[$pid][$itemnumber][$k1]['priceNoTax']+tx_ttproducts_price_div::getPrice($actItem['rec']['accessory'],0,$actItem['rec']['tax']))*$actItem['count'];
					} else {
						$this->itemArray[$pid][$itemnumber][$k1]['totalTax'] = $this->itemArray[$pid][$itemnumber][$k1]['priceTax'] * $actItem['count'];
						$this->itemArray[$pid][$itemnumber][$k1]['totalNoTax'] = $this->itemArray[$pid][$itemnumber][$k1]['priceNoTax'] * $actItem['count'];
					}
							// Fills this array with the product records. Reason: Sorting them by category (based on the page, they reside on)
					$this->calculatedArray['priceTax']['goodstotal'] += $this->itemArray[$pid][$itemnumber][$k1]['totalTax'];
					$this->calculatedArray['priceNoTax']['goodstotal'] += $this->itemArray[$pid][$itemnumber][$k1]['totalNoTax'];

					$this->calculatedArray['categoryPriceTax']['goodstotal'][$actItem['rec']['category']]+= $this->itemArray[$pid][$itemnumber][$k1]['totalTax'];
					$this->calculatedArray['categoryPriceNoTax']['goodstotal'][$actItem['rec']['category']]+= $this->itemArray[$pid][$itemnumber][$k1]['totalNoTax'];
				}
			}
		}

		foreach ($this->productsArray as $prodUID) {
			foreach ($prodUID as $prodSingle)
			{
				if ($prodSingle['bulkily'])
				{
					$value = ($this->conf['bulkilyAddition'] * $this->basketExt[$prodSingle['uid']][$prodSingle['color'].';'.$prodSingle['size'].';'.intval(100*$prodSingle['accessory']).';'.$prodSingle['gradings']]);
					$this->calculatedArray['priceTax']['shipping'] += $value  * (1+$conf['bulkilyFeeTax']/100);
					$this->calculatedArray['priceNoTax']['shipping'] += $value;
				}
			}
		}

		// TAXpercentage replaces priceNoTax
		if ($this->conf['shipping.']['TAXpercentage']) {
			$this->calculatedArray['priceNoTax']['shipping'] = $this->calculatedArray['priceTax']['shipping']/(1+doubleVal($this->conf['shipping.']['TAXpercentage'])/100);
		}

			// Shipping must be at the end in order to use the calculated values from before
		tx_ttproducts_paymentshipping_div::GetPaymentShippingData(
			$this->calculatedArray['count'],
/* Added Els: necessary to calculate shipping price which depends on total no-tax price */
			$this->calculatedArray['priceNoTax']['goodstotal'],
			$this->calculatedArray['priceTax']['shipping'],
			$this->calculatedArray['priceNoTax']['shipping'],
			$this->calculatedArray['priceTax']['payment'],
			$this->calculatedArray['priceNoTax']['payment']
			);

	} // getCalculatedBasket


	function getValue(&$basketElement, $basketProperties, $countTotal)
	{
		$result = 0;

		// to remain downwards compatible
		if (is_string($basketElement))	{
        	$result = $basketElement;
        }

		if(is_array($basketProperties) && count($basketProperties) > 0) {
			foreach ($basketProperties as $lowKey => $lowValue)	{
				if (strlen($lowKey) > 0 && $countTotal >= $lowKey)	{
					$result = doubleVal($lowValue);
				}
			}
		}

		return $result;
	} // getValue


	function &getItem (&$row) {
		$count = intval($this->basketExt[$row['uid']][tx_ttproducts_article_div::getVariantFromRow ($row)]);
		$priceTax = tx_ttproducts_price_div::getResellerPrice($row,1);
		$priceNoTax = tx_ttproducts_price_div::getResellerPrice($row,0);
		$item = array (
			'calcprice' => 0,
			'count' => $count,
			'priceTax' => $priceTax,
			'priceNoTax' => $priceNoTax,
			'totalTax' => 0,
			'totalNoTax' => 0,
			'rec' => $row,
			);
		return $item;
	}

	// This calculates the total for everything in the basket
	function getCalculateSums () {
		$this->calculatedArray['priceTax']['total'] = $this->calculatedArray['priceTax']['goodstotal'];
		$this->calculatedArray['priceTax']['total']+= $this->calculatedArray['priceTax']['payment'];
		$this->calculatedArray['priceTax']['total']+= $this->calculatedArray['priceTax']['shipping'];
/* Added Els: $this->calculatedArray['priceTax']['creditpoints'] and coucher */
		$this->calculatedArray['priceTax']['total']-= $this->calculatedArray['priceTax']['creditpoints'];
		$this->calculatedArray['priceTax']['total']-= $this->calculatedArray['priceTax']['voucher'];

		$this->calculatedArray['priceNoTax']['total']  = $this->calculatedArray['priceNoTax']['goodstotal'];
		$this->calculatedArray['priceNoTax']['total'] += $this->calculatedArray['priceNoTax']['payment'];
		$this->calculatedArray['priceNoTax']['total'] += $this->calculatedArray['priceNoTax']['shipping'];
	} // getItem


	/**
	 * This generates the shopping basket layout and also calculates the totals. Very important function.
	 */
	function getBasket($subpartMarker='###BASKET_TEMPLATE###', $templateCode='', 		$mainMarkerArray=array())	{
			/*
				Very central function in the library.
				By default it extracts the subpart, ###BASKET_TEMPLATE###, from the $templateCode (if given, else the default $this->templateCode)
				and substitutes a lot of fields and subparts.
				Any pre-preparred fields can be set in $mainMarkerArray, which is substituted in the subpart before the item-and-categories part is substituted.
			*/

		global $TSFE;
/* ADDED Els: need for vouchercode conditions */
		global $TYPO3_DB;
		$templateCode = $templateCode ? $templateCode : $this->templateCode;

//		tx_ttproducts_basket_div::getCalculatedBasket();  // all the basket calculation is done in this function once and not multiple times here


			// Getting subparts from the template code.
		$t=array();
		$t['basketFrameWork'] = $this->cObj->getSubpart($templateCode,tx_ttproducts_view_div::spMarker($subpartMarker));

			// If there is a specific section for the billing address if user is logged in (used because the address may then be hardcoded from the database
		if (trim($this->cObj->getSubpart($t['basketFrameWork'],'###BILLING_ADDRESS_LOGIN###')))	{
			//if ($GLOBALS['TSFE']->loginUser)	{
			if ($TSFE->loginUser && $this->conf['lockLoginUserInfo']) {
				$t['basketFrameWork'] = $this->cObj->substituteSubpart($t['basketFrameWork'], '###BILLING_ADDRESS###', '');
			} else {
				$t['basketFrameWork'] = $this->cObj->substituteSubpart($t['basketFrameWork'], '###BILLING_ADDRESS_LOGIN###', '');
			}
		}

		$t['categoryTitle'] = $this->cObj->getSubpart($t['basketFrameWork'],'###ITEM_CATEGORY###');
		$t['itemFrameWork'] = $this->cObj->getSubpart($t['basketFrameWork'],'###ITEM_LIST###');
		$t['item'] = $this->cObj->getSubpart($t['itemFrameWork'],'###ITEM_SINGLE###');

		$currentP='';
		$out='';
		$itemsOut='';

		reset ($this->itemArray);
		// loop over all items in the basket indexed by page and itemnumber
		foreach ($this->itemArray as $pid=>$pidItem) {
			foreach ($pidItem as $itemnumber=>$actItemArray) {
				foreach ($actItemArray as $k1=>$actItem) {

					$pidcategory = ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_products']['pageAsCategory'] == 1 ? $pid : '');
					$currentPnew = $pidcategory.'_'.$actItem['rec']['category'];
						// Print Category Title
					if ($currentPnew!=$currentP)	{
						if ($itemsOut)	{
							$out.=$this->cObj->substituteSubpart($t['itemFrameWork'], '###ITEM_SINGLE###', $itemsOut);
						}
						$itemsOut='';			// Clear the item-code var
						$currentP = $currentPnew;
						if ($this->conf['displayBasketCatHeader'])	{
							$markerArray=array();
							$pageCatTitle = '';
							if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_products']['pageAsCategory'] == 1) {
									$pageCatTitle = $this->pageArray[$pid]['title'].'/';
							}							
							$catTmp = '';
							if ($actItem['rec']['category']) {
								$catTmp = $this->category->getCategory($actItem['rec']['category']);
								$catTmp = $catTmp['title'];	
							}
							$catTitle = $pageCatTitle.$catTmp;
							
							$this->cObj->setCurrentVal($catTitle);
							$markerArray['###CATEGORY_TITLE###']=$this->cObj->cObjGetSingle($this->conf['categoryHeader'],$this->conf['categoryHeader.'], 'categoryHeader');
							$out.= $this->cObj->substituteMarkerArray($t['categoryTitle'], $markerArray);
						}
					}

						// Fill marker arrays
					$wrappedSubpartArray=array();
					$subpartArray=array();
					$markerArray = tx_ttproducts_view_div::getItemMarkerArray ($actItem,$catTitle,1,'basketImage');

					$markerArray['###PRODUCT_COLOR###'] = $actItem['rec']['color'];
					$markerArray['###PRODUCT_SIZE###'] = $actItem['rec']['size'];
					$markerArray['###PRODUCT_GRADINGS###'] = $actItem['rec']['gradings'];

	                $catTitle= $actItem['rec']['category'] ? $this->category->getCategory($actItem['rec']['category']) : '';
					$this->cObj->setCurrentVal($catTitle);
					$markerArray['###CATEGORY_TITLE###']=$this->cObj->cObjGetSingle($this->conf['categoryHeader'],$this->conf['categoryHeader.'], 'categoryHeader');

					// If accesssory has been selected, add the price of it, multiplicated with the count :
					if($actItem['rec']['accessory'] > 0 ){
						$markerArray['###PRICE_ACCESSORY_TEXT###']= $this->conf['accessoryText'];
						$markerArray['###PRICE_ACCESSORY_COUNT###']= '<INPUT size="3" maxlength="4" type="text" class="readonly" name="'.$actItem['count'].'" value="'.$actItem['count'].'" readonly="readonly">';
						$markerArray['###ACCESSORY_VALUE_TAX###']= tx_ttproducts_price_div::printPrice(tx_ttproducts_view_div::priceFormat(tx_ttproducts_price_div::getPrice($actItem['rec']['accessory'.$this->config['priceNoReseller']],1,$actItem['rec']['tax'])));
						$markerArray['###ACCESSORY_VALUE_NO_TAX###']= tx_ttproducts_price_div::printPrice(tx_ttproducts_view_div::priceFormat(tx_ttproducts_price_div::getPrice($actItem['rec']['accessory'.$this->config['priceNoReseller']],0,$actItem['rec']['tax'])));
					}
					else {
						$markerArray['###PRICE_ACCESSORY_TEXT###']= '';
						$markerArray['###PRICE_ACCESSORY_COUNT###']= '';
						$markerArray['###ACCESSORY_VALUE_TAX###']= '';
						$markerArray['###ACCESSORY_VALUE_NO_TAX###']= '';
					}

					$markerArray['###PRICE_TOTAL_TAX###']=tx_ttproducts_view_div::priceFormat($actItem['totalTax']);
					$markerArray['###PRICE_TOTAL_NO_TAX###']=tx_ttproducts_view_div::priceFormat($actItem['totalNoTax']);

/* Added els4: calculating of price_discount necessary in winkelwagen.tmpl (articles in kurkenshop are excluded, because these articled will be payed with creditpoints) */
					if ( ($actItem['rec']['price'] != '0.00') && doubleval($actItem['rec']['price2']) && ($actItem['rec']['category'] != $this->conf['creditsCategory']) ) {
						$pricediscount_total_tot_units = "";
						$oldprice_total_tot_units = ($actItem['totalNoTax']/$actItem['rec']['price2'])*$actItem['rec']['price'];
						$pricediscount_total_tot_units = ($oldprice_total_tot_units - $actItem['totalNoTax']) * $actItem['rec']['unit_factor'];
						$sum_pricediscount_total_totunits += $pricediscount_total_tot_units;
					}

/* Added els4: TOTUNITS_: both prices mulitplied by unit_factor and third line is calculating the sum, necessary in winkelwagen.tmpl. All articles in kurkenshop are payed with creditpoints*/
					$markerArray['###PRICE_TOTAL_TOTUNITS_TAX###']=tx_ttproducts_view_div::priceFormat($actItem['totalTax']*$actItem['rec']['unit_factor']);
					if ($actItem['rec']['category'] == $this->conf['creditsCategory']) {
						$markerArray['###PRICECREDITS_TOTAL_TOTUNITS_NO_TAX###']=tx_ttproducts_view_div::priceFormat($actItem['totalNoTax']*$actItem['rec']['unit_factor']);
					} else {
						$markerArray['###PRICE_TOTAL_TOTUNITS_NO_TAX###']=$actItem['totalNoTax']*$actItem['rec']['unit_factor'];
					}
					$sum_pricecredits_total_totunits_no_tax += $markerArray['###PRICECREDITS_TOTAL_TOTUNITS_NO_TAX###'];
					$sum_price_total_totunits_no_tax += $markerArray['###PRICE_TOTAL_TOTUNITS_NO_TAX###'];

/* Added els4: calculating of price_creditpoints necessary in winkelwagen.tmpl, only if article contains special_prep then one can gains creditpoints */
					if ($actItem['rec']['special_preparation'] != '0.00') {
						$sum_pricecreditpoints_total_totunits += $markerArray['###PRICE_TOTAL_TOTUNITS_NO_TAX###'];
					}

/* Added els4: if catTitle = shop-a-box then handling costs exists (winkelwagen.tmpl)*/
					if ($shopabox != "1") {
						if ($actItem['rec']['category'] == $this->conf['shopaboxCategory']) {
						   $show_handling_cost = 1;
						   $shopabox = 1;
						}
					}

					$pid = tx_ttproducts_page_div::getPID($this->conf['PIDitemDisplay'], $this->conf['PIDitemDisplay.'], $actItem['rec']);
					$splitMark = md5(microtime());
					$addQueryString=array();
					$addQueryString['tt_products'] = intval($actItem['rec']['uid']);
					$addQueryString['ttp_extvars'] = htmlspecialchars($actItem['rec']['extVars']);
					$wrappedSubpartArray['###LINK_ITEM###'] =  array('<a href="'. $this->pi_getPageLink($pid,'',tx_ttproducts_view_div::getLinkParams('', $addQueryString)).'"'.$css_current.'>','</a>'); // // array('<a href="'.$this->getLinkUrl($pid).'&tt_products='.$actItem['rec']['uid'].'&ttp_extvars='.htmlspecialchars($actItem['rec']['extVars']).'">','</a>');

/* Added Els5: different display for quantities and recycle output in shop-a-box and gifts (in payment_template, winkelwagen.tmpl) */
					if ($actItem['rec']['category'] == $this->conf['shopaboxCategory']) {
						$subpartArray['###QUANTITY_CAT###'] = "";
						$wrappedSubpartArray['###LINK_ITEM###'] = array('<a href="index.php?id=shopabox">','</a>');
						$subpartArray['###RECYCLE_CAT###'] = "";
						$subpartArray['###RECYCLE_CAT_GIFT###'] = "";
					} else {
						$subpartArray['###RECYCLE_CAT_SHOPABOX###'] = "";
						//  in case of gift certificates
						if ($actItem['rec']['uid'] == $this->conf['giftID']) {
							$subpartArray['###QUANTITY_CAT###'] = "";
						} else {
							$subpartArray['###QUANTITY_SHOPABOX_GIFT###'] = "";
							$subpartArray['###RECYCLE_CAT_GIFT###'] = "";
						}
					}

					// Substitute
					$tempContent = $this->cObj->substituteMarkerArrayCached($t['item'],$markerArray,$subpartArray,$wrappedSubpartArray);

					tx_ttproducts_article_div::getVariantSubpartArray ($subpartArray, $actItem['rec'], $tempContent, ($subpartMarker == '###EMAIL_PLAINTEXT_TEMPLATE###'));
					$tempContent = $this->cObj->substituteMarkerArrayCached($tempContent,$markerArray,$subpartArray,$wrappedSubpartArray);

					$itemsOut .= $tempContent;
				}
				if ($itemsOut)	{
					$tempContent=$this->cObj->substituteSubpart($t['itemFrameWork'], '###ITEM_SINGLE###', $itemsOut);
					$out .= $tempContent;
					$itemsOut='';			// Clear the item-code var
				}
			}
		}

		$subpartArray = array();
		$wrappedSubpartArray = array();

			// Initializing the markerArray for the rest of the template
		$markerArray=$mainMarkerArray;

			// This is the total for the goods in the basket.
		$markerArray['###PRICE_GOODSTOTAL_TAX###'] = tx_ttproducts_view_div::priceFormat($this->calculatedArray['priceTax']['goodstotal']);
		$markerArray['###PRICE_GOODSTOTAL_NO_TAX###'] = tx_ttproducts_view_div::priceFormat($this->calculatedArray['priceNoTax']['goodstotal']);

		// This is for the Basketoverview
		$markerArray['###NUMBER_GOODSTOTAL###'] = $this->calculatedArray['count'];
		$markerArray['###IMAGE_BASKET###'] = '<img src="'.$this->conf['basketPic'].'">';

		$splitMark = md5(microtime());
		$pid = ( $this->conf['PIDbasket'] ? $this->conf['PIDbasket'] : $TSFE->id);
		$tempUrl = $this->pi_linkToPage($splitMark,$pid,'',tx_ttproducts_view_div::getLinkParams());
		$wrappedSubpartArray['###LINK_BASKET###'] = explode ($splitMark, $tempUrl);
		//$wrappedSubpartArray['###LINK_BASKET###']= array('<a href="'.$this->getLinkUrl($this->conf['PIDbasket']).'">','</a>');

		$markerArray['###PRICE_SHIPPING_PERCENT###'] = $perc;
		$markerArray['###PRICE_SHIPPING_TAX###'] = tx_ttproducts_view_div::priceFormat($this->calculatedArray['priceTax']['shipping']);
		$markerArray['###PRICE_SHIPPING_NO_TAX###'] = tx_ttproducts_view_div::priceFormat($this->calculatedArray['priceNoTax']['shipping']);

		$markerArray['###SHIPPING_SELECTOR###'] = tx_ttproducts_paymentshipping_div::generateRadioSelect('shipping', $countTotal);
		$markerArray['###SHIPPING_IMAGE###'] = $this->cObj->IMAGE($this->basketExtra['shipping.']['image.']);
		$markerArray['###SHIPPING_TITLE###'] = $this->basketExtra['shipping.']['title'];

		$markerArray['###SHIPPING_WEIGHT###'] = doubleval($this->calculatedArray['weight']);


		$markerArray['###DELIVERYCOSTS###']=tx_ttproducts_view_div::priceFormat($this->calculatedArray['priceTax']['shipping'] + $this->calculatedArray['priceTax']['payment']);

		$markerArray['###PRICE_PAYMENT_PERCENT###'] = $perc;
		$markerArray['###PRICE_PAYMENT_TAX###'] = tx_ttproducts_view_div::priceFormat($this->calculatedArray['priceTax']['payment']);
		$markerArray['###PRICE_PAYMENT_NO_TAX###'] = tx_ttproducts_view_div::priceFormat($this->calculatedArray['priceNoTax']['payment']);

/* Added els4: payment layout (used in basket_payment_template, winkelwagen.tmpl) */
		$markerArray['###PAYMENT_SELECTOR###'] = tx_ttproducts_paymentshipping_div::generateRadioSelect('payment', $countTotal);
		$markerArray['###PAYMENT_IMAGE###'] = $this->cObj->IMAGE($this->basketExtra['payment.']['image.']);
		$markerArray['###PAYMENT_TITLE###'] = $this->basketExtra['payment.']['title'];
		if ($markerArray['###PAYMENT_TITLE###'] == "Vooruitbetaling") {
 		   $subpartArray['###SUB_PAYMENT_MACHT###'] = '';
 		} elseif ($markerArray['###PAYMENT_TITLE###'] == "E�nmalige machtiging") {
 		   $subpartArray['###SUB_PAYMENT_VOORUIT###'] = '';
 		} elseif ($markerArray['###PAYMENT_TITLE###'] == "Factuur") {
 		   $subpartArray['###SUB_PAYMENT_MACHT###'] = '';
 		   $subpartArray['###SUB_PAYMENT_VOORUIT###'] = '';
 		}

/* Added els4: output for payment (used in basket_payment_template, winkelwagen.tmpl)*/
		$markerArray['###PAYMENT_NUMBER###'] = t3lib_div::_GP('payment_number');
		$markerArray['###PAYMENT_NAME###'] = t3lib_div::_GP('payment_name');
		$markerArray['###PAYMENT_CITY###'] = t3lib_div::_GP('payment_city');

		$markerArray['###TRANSACT_CODE###'] = t3lib_div::_GP('transact');

			// Fill the Currency Symbol or not
		if ($this->conf['showcurSymbol']) {
			$markerArray['###CUR_SYM###'] = ' '.$this->conf['currencySymbol'];
		} else {
			$markerArray['###CUR_SYM###'] = '';
		}

/* Added Els: below 3 lines after creditpoint calculation! */
		// This is the total for everything
//		tx_ttproducts_basket_div::getCalculateSums();
//		$markerArray['###PRICE_TOTAL_TAX###'] = tx_ttproducts_view_div::priceFormat($this->calculatedArray['priceTax']['total']);
//		$markerArray['###PRICE_TOTAL_NO_TAX###'] = tx_ttproducts_view_div::priceFormat($this->calculatedArray['priceNoTax']['total']);
/* Added Els: marker PRICE_DISCOUNT += uw voordeel (PRICE_GOODSTOTAL_NO_TAX-oldPriceNoTax) and PRICE_VAT = BTW (= ['priceTax']['goodstotal'] - ['priceNoTax']['goodstotal']*/
		$markerArray['###PRICE_DISCOUNT###'] = tx_ttproducts_view_div::priceFormat($this->calculatedArray['oldPriceNoTax']-$this->calculatedArray['priceNoTax']['goodstotal']);
		$markerArray['###PRICE_VAT###'] = tx_ttproducts_view_div::priceFormat($this->calculatedArray['priceTax']['goodstotal']-$this->calculatedArray['priceNoTax']['goodstotal']);

/* Added els4: discount based on total units (without articles in kurkenshop), necessary in winkelwagen.tmpl */
		$markerArray['###PRICE_TOTUNITS_DISCOUNT###'] = tx_ttproducts_view_div::priceFormat($sum_pricediscount_total_totunits);

/* Added Els4: if show_handling_cost (calculated above) then handling costs exists (winkelwagen.tmpl) */
		if ($show_handling_cost == "1") {
		   $markerArray['###CAT3_HANDLING_COSTS###'] = $this->conf['shopaboxPrice'];
		} else {
			$subpartArray['###SUB_CAT3_HANDLING_COSTS###'] = '';
		}

			// Personal and delivery info:
/* Add ELS: more fields */
		$list = 'name,address,telephone,fax,email,company,city,zip,state,country';
		if ($this->feuserextrafields) {
			$list .= ',tx_feuserextrafields_initials_name,tx_feuserextrafields_prefix_name,tx_feuserextrafields_gsm_tel,name,date_of_birth,tx_feuserextrafields_company_deliv,address,tx_feuserextrafields_address_deliv,tx_feuserextrafields_housenumber,tx_feuserextrafields_housenumber_deliv,tx_feuserextrafields_housenumberadd,tx_feuserextrafields_housenumberadd_deliv,tx_feuserextrafields_pobox,tx_feuserextrafields_pobox_deliv,zip,tx_feuserextrafields_zip_deliv,city,tx_feuserextrafields_city_deliv,tx_feuserextrafields_country,tx_feuserextrafields_country_deliv';
		}
		$infoFields = explode(',',$list); // Fields...
// mkl: 	$infoFields = explode(',','forename,name,address,telephone,fax,email,company,city,zip,state,street,street_n1,street_n2,country_code,vat_id');

		while(list(,$fName)=each($infoFields))	{
			$markerArray['###PERSON_'.strtoupper($fName).'###'] = $this->personInfo[$fName];
			$markerArray['###DELIVERY_'.strtoupper($fName).'###'] = $this->deliveryInfo[$fName];

/* mkl:
			if( $fName == 'country_code' ) 	{
				$markerArray['###PERSON_'.strtoupper($fName).'###'] =
					$this->staticInfo->buildStaticInfoSelector('COUNTRIES', 'recs[personinfo][country_code]', '',$this->personInfo['country_code'], '');
				$markerArray['###PERSON_COUNTRY###'] =
				        $this->staticInfo->getStaticInfoName('COUNTRIES', $this->personInfo['country_code'],'','');
				$markerArray['###DELIVERY_'.strtoupper($fName).'###'] =
					$this->staticInfo->buildStaticInfoSelector('COUNTRIES', 'recs[delivery][country_code]', '',$this->deliveryInfo['country_code'], '');
				$markerArray['###DELIVERY_COUNTRY###'] =
				        $this->staticInfo->getStaticInfoName('COUNTRIES', $this->deliveryInfo['country_code'],'','');
			}
			else   {
				$markerArray['###PERSON_'.strtoupper($fName).'###'] = $this->personInfo[$fName];
				$markerArray['###DELIVERY_'.strtoupper($fName).'###'] = $this->deliveryInfo[$fName];
			}
*/

		}
			// Markers for use if you want to output line-broken address information
		$markerArray['###PERSON_ADDRESS_DISPLAY###'] = nl2br($markerArray['###PERSON_ADDRESS###']);
		$markerArray['###DELIVERY_ADDRESS_DISPLAY###'] = nl2br($markerArray['###DELIVERY_ADDRESS###']);
			// Delivery note.
		$markerArray['###DELIVERY_NOTE###'] = $this->deliveryInfo['note'];
		$markerArray['###DELIVERY_NOTE_DISPLAY###'] = nl2br($markerArray['###DELIVERY_NOTE###']);

			// Order:	NOTE: Data exist only if the getBlankOrderUid() has been called. Therefore this field in the template should be used only when an order has been established
		$markerArray['###ORDER_UID###'] = tx_ttproducts_order_div::getOrderNumber($this->recs['tt_products']['orderUid']);
		$markerArray['###ORDER_DATE###'] = $this->cObj->stdWrap($this->recs['tt_products']['orderDate'],$this->conf['orderDate_stdWrap.']);
		$markerArray['###ORDER_TRACKING_NO###'] = $this->recs['tt_products']['orderTrackingNo'];

			// Fe users:
		$markerArray['###FE_USER_USERNAME###'] = $TSFE->fe_user->user['username'];
		$markerArray['###FE_USER_UID###'] = $TSFE->fe_user->user['uid'];

		$markerArray['###PERSON_AGB###'] = $TSFE->fe_user->user['agb'] ? ' checked' : '';

		$markerArray['###USERNAME###'] = $this->personInfo['email'];
		$markerArray['###PASSWORD###'] = $this->password;
		$markerArray['###PID_TRACKING###'] = $this->conf['PIDtracking'];

			// URL
		$markerArray = tx_ttproducts_view_div::addURLMarkers($markerArray);

		$agb_url=array();
		$pidagb = intval($this->conf['PIDagb']);
		$agb_url['id'] = 'id='.$pidagb;
		$agb_url['type']= $TSFE->type ? 'type='.$TSFE->type : '';
		$agb_url['backPID']= 'backPID='.$TSFE->id;

/* Added Els: creditpoint inclusive conditions*/
		$creditpoints_integer = explode('.',$TSFE->fe_user->user['tt_products_creditpoints']);
		$markerArray['###AMOUNT_CREDITPOINTS###'] = $creditpoints_integer[0];
		// if quantity is 0 than
		if ($TSFE->fe_user->user['tt_products_creditpoints'] == '0') {
			$subpartArray['###SUB_CREDITPOINTS_DISCOUNT###'] = '';
			$subpartArray['###SUB_CREDITPOINTS_DISCOUNT_EMPTY###'] = '';
			$subpartArray['###SUB_CREDITPOINTS_AMOUNT###'] = '';
		} else {
			$subpartArray['###SUB_CREDITPOINTS_AMOUNT_EMPTY###'] = '';
		}
		$markerArray['###CHANGE_AMOUNT_CREDITPOINTS###'] = 'recs[tt_products][creditpoints]';
		if ($this->recs['tt_products']['creditpoints'] == '') {
			$markerArray['###AMOUNT_CREDITPOINTS_QTY###'] = 0;
			$subpartArray['###SUB_CREDITPOINTS_DISCOUNT###'] = '';
		} else {
			// quantity chosen can not be larger than the amount of creditpoints
			if ($this->recs['tt_products']['creditpoints'] > $creditpoints_integer[0]) $this->recs['tt_products']['creditpoints'] = $creditpoints_integer[0];
			$this->calculatedArray['priceTax']['creditpoints'] = tx_ttproducts_view_div::priceFormat($this->recs['tt_products']['creditpoints']*$this->conf['creditpoints.']['pricefactor']);
			$markerArray['###AMOUNT_CREDITPOINTS_QTY###'] = $this->recs['tt_products']['creditpoints'];
			$subpartArray['###SUB_CREDITPOINTS_DISCOUNT_EMPTY###'] = '';
		    $markerArray['###CREDIT_DISCOUNT###'] = $this->calculatedArray['priceTax']['creditpoints'];
		}

/* Added Els: voucher marker inclusive conditions */
/* Added Els5: small changes in voucher marker inclusive conditions */
		if ($TSFE->fe_user->user['tt_products_vouchercode'] == '') {
			$subpartArray['###SUB_VOUCHERCODE###'] = '';
			$markerArray['###INSERT_VOUCHERCODE###'] = 'recs[tt_products][vouchercode]';
			$markerArray['###VALUE_VOUCHERCODE###'] = $this->recs['tt_products']['vouchercode'];
			if ($this->recs['tt_products']['vouchercode'] == '') {
				$subpartArray['###SUB_VOUCHERCODE_DISCOUNT###'] = '';
				$subpartArray['###SUB_VOUCHERCODE_DISCOUNTOWNID###'] = '';
				$subpartArray['###SUB_VOUCHERCODE_DISCOUNTWRONG###'] = '';
			} else {
				$res = $TYPO3_DB->exec_SELECTquery('uid', 'fe_users', 'username="'.$this->recs['tt_products']['vouchercode'].'"');
				if ($row = $TYPO3_DB->sql_fetch_assoc($res)) {
					$uid_voucher = $row['uid'];
				}
				if ($uid_voucher != '') {
					// first check if not inserted own vouchercode
					if ($TSFE->fe_user->user['uid'] == $uid_voucher) {
						$subpartArray['###SUB_VOUCHERCODE_DISCOUNT###'] = '';
						$subpartArray['###SUB_VOUCHERCODE_DISCOUNTWRONG###'] = '';
					} else {
						$subpartArray['###SUB_VOUCHERCODE_DISCOUNTOWNID###'] = '';
						$subpartArray['###SUB_VOUCHERCODE_DISCOUNTWRONG###'] = '';
						//$this->calculatedArray['priceTax']['voucher'] = tx_ttproducts_view_div::priceFormat($this->calculatedArray['priceTax']['shipping']);
						$this->calculatedArray['priceTax']['voucher'] = $this->conf['voucherPrice'];
						$markerArray['###VOUCHER_DISCOUNT###'] = tx_ttproducts_view_div::priceFormat($this->calculatedArray['priceTax']['voucher']);
						$subpartArray['###SUB_VOUCHERCODE_NODISCOUNT###'] = '';
					}
				} else {
					$subpartArray['###SUB_VOUCHERCODE_DISCOUNTOWNID###'] = '';
					$subpartArray['###SUB_VOUCHERCODE_DISCOUNT###'] = '';
				}
			}
		} else {
			$subpartArray['###SUB_VOUCHERCODE_EMPTY###'] = '';
		}

// Added Franz: GIFT CERTIFICATE
		$markerArray['###GIFT_CERTIFICATE_UNIQUE_NUMBER_NAME###']='recs[tt_products][gift_certificate_unique_number]';
		$markerArray['###FORM_NAME###']='BasketForm';
		$markerArray['###FORM_NAME_GIFT_CERTIFICATE###']='BasketGiftForm';

/* Added els5: markerarrays for gift certificates */
		if (t3lib_div::_GP('gift_certificate_number') == 'empty') {
			$subpartArray['###SUB_GIFTCODE_DISCOUNTWRONG###'] = '';
			$subpartArray['###SUB_GIFTCODE_DISCOUNT###'] = '';
			$markerArray['###VALUE_GIFTCODE###']='';
		}


/* Added Els: below 3 lines moved from above */
			// This is the total for everything
		tx_ttproducts_basket_div::getCalculateSums();
		$markerArray['###PRICE_TOTAL_TAX###'] = tx_ttproducts_view_div::priceFormat($this->calculatedArray['priceTax']['total']);
		$markerArray['###PRICE_TOTAL_NO_TAX###'] = tx_ttproducts_view_div::priceFormat($this->calculatedArray['priceNoTax']['total']);

/* Added els4: PRICE_GOODSTOTAL_TOTUNITS_NO_TAX: sum total price (winkelwagen.tmpl) */
		$markerArray['###PRICE_GOODSTOTAL_TOTUNITS_NO_TAX###'] = tx_ttproducts_view_div::priceFormat($sum_price_total_totunits_no_tax);

/* Added els5: CREDITPOINTS_SPENDED: creditpoint needed, check if user has this amount of creditpoints on his account (winkelwagen.tmpl), only if user has logged in */
		$markerArray['###CREDITPOINTS_SPENDED###'] = $sum_pricecredits_total_totunits_no_tax;
		if ($sum_pricecredits_total_totunits_no_tax <= $markerArray['###AMOUNT_CREDITPOINTS###']) {
			$subpartArray['###SUB_CREDITPOINTS_SPENDED_EMPTY###'] = '';
			$markerArray['###CREDITPOINTS_SPENDED###'] = $sum_pricecredits_total_totunits_no_tax;
			// new saldo: creditpoints
			$markerArray['###AMOUNT_CREDITPOINTS###'] = $markerArray['###AMOUNT_CREDITPOINTS###'] - $markerArray['###CREDITPOINTS_SPENDED###'];
		} else {
			if (!$markerArray['###FE_USER_UID###']) {
				$subpartArray['###SUB_CREDITPOINTS_SPENDED_EMPTY###'] = '';
			} else {
				$markerArray['###CREDITPOINTS_SPENDED_ERROR###'] = "Wijzig de artikelen in de kurkenshop: onvoldoende kurken op uw saldo (".$markerArray['###AMOUNT_CREDITPOINTS###'].").";
				$markerArray['###CREDITPOINTS_SPENDED###'] = '&nbsp;';
			}
		}

/* Added els4: CREDITPOINTS_SAVED (winkelwagen.tmpl): depends on $sum_pricecreditpoints_total_totunits and the factor defined in the typo3-setup
   Code below has to be rewritten as follows
     creditpoints {
     10.where =
     10.type = ??
     10.prod.1   = 0.02
     10.prod.101 = 0.04
     10.prod.501 = 0.06
   }
*/
		if ($sum_pricecreditpoints_total_totunits <= 100) {
		   $creditpoints = 0.02;
		} elseif ($sum_pricecreditpoints_total_totunits <= 500) {
		   $creditpoints = 0.04;
		} else {
		   $creditpoints = 0.06;
		}

		$markerArray['###CREDITPOINTS_SAVED###'] = tx_ttproducts_view_div::priceFormat($creditpoints * $sum_pricecreditpoints_total_totunits);

/* Added Els4: total price = subtotal - bezorgkosten + voucher + coupon + giftcertificate (winkelwagen.tmpl) */
		$markerArray['###PRICE_TOTAL_MEERWIJN###'] = tx_ttproducts_view_div::priceFormat($markerArray['###PRICE_GOODSTOTAL_TOTUNITS_NO_TAX###'] + $markerArray['###PRICE_SHIPPING_NO_TAX###'] - $markerArray['###VOUCHER_DISCOUNT###'] - $markerArray['###COUPON_DISCOUNT###'] - $markerArray['###CREDIT_DISCOUNT###']);

		$wrappedSubpartArray['###LINK_AGB###']=array('<a href="'.$TSFE->absRefPrefix.'index.php?'.implode($agb_url,'&').'" target="'.$this->conf['agbtarget'].'">','</a>');

			// Final substitution:
		if (!$TSFE->loginUser)	{		// Remove section for FE_USERs only, if there are no fe_user
			$subpartArray['###FE_USER_SECTION###']='';
		}
		$bFrameWork = $t['basketFrameWork'];
		$subpartArray['###MESSAGE_SHIPPING###'] = $this->cObj->substituteMarkerArrayCached($this->cObj->getSubpart($bFrameWork,'###MESSAGE_SHIPPING_'.$this->basketExtra['shipping'].'###'),$markerArray);
		$subpartArray['###MESSAGE_PAYMENT###'] = $this->cObj->substituteMarkerArrayCached($this->cObj->getSubpart($bFrameWork,'###MESSAGE_PAYMENT_'.$this->basketExtra['payment'].'###'),$markerArray);

		$bFrameWork=$this->cObj->substituteMarkerArrayCached($t['basketFrameWork'],$markerArray,$subpartArray,$wrappedSubpartArray);

			// substitute the main subpart with the rendered content.
		$out=$this->cObj->substituteSubpart($bFrameWork, '###ITEM_CATEGORY_AND_ITEMS###', $out);

		return $out;
	} // getBasket



}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tt_products/lib/class.tx_ttproducts_basket_div.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tt_products/lib/class.tx_ttproducts_basket_div.php']);
}


?>
