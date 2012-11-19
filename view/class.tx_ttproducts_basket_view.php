<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2010 Kasper Skårhøj (kasperYYYY@typo3.com)
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
 * basket functions for a basket object
 *
 * $Id$
 *
 * @author	Kasper Skårhøj <kasperYYYY@typo3.com>
 * @author	René Fritz <r.fritz@colorcube.de>
 * @author	Franz Holzinger <franz@ttproducts.de>
 * @author	Klaus Zierer <zierer@pz-systeme.de>
 * @author	Els Verberne <verberne@bendoo.nl>
 * @package TYPO3
 * @subpackage tt_products
 *
 *
 */


global $TYPO3_CONF_VARS;


require_once (PATH_BE_ttproducts.'model/class.tx_ttproducts_model_activity.php');
require_once (PATH_BE_ttproducts.'lib/class.tx_ttproducts_creditpoints_div.php');

class tx_ttproducts_basket_view {
	var $pibase; // reference to object of pibase
	var $conf;
	var $config;
	var $page;
	var $tt_content; // element of class tx_table_db
	var $tt_products; // element of class tx_table_db
	var $tt_products_articles; // element of class tx_table_db
	var $tt_products_cat; // element of class tx_table_db
	var $tx_dam; // element of class tx_table_db
	var $tx_dam_cat; // element of class tx_table_db
	var $fe_users; // element of class tx_table_db
	var $price; // price object
	var $paymentshipping; 		// object of the type tx_ttproducts_paymentshipping
	var $basket; 	// the basket object
	var $templateCode='';		// In init(), set to the content of the templateFile. Used by default in getView()
	var $marker; // marker functions
	var $viewTable;
	var $error_code;


	/**
	 * Initialized the basket, setting the deliveryInfo if a users is logged in
	 * $basket is the TYPO3 default shopping basket array from ses-data
	 *
	 * @param		string		  $fieldname is the field in the table you want to create a JavaScript for
	 * @return	  void
	 */
	function init (&$basket, &$templateCode, &$error_code )	{
		$this->pibase = &$basket->pibase;
		$this->cnf = &$basket->cnf;
		$this->conf = &$this->cnf->conf;
//  		$this->config = &$this->cnf->config;
		$this->basket = &$basket;
		$this->page = &$basket->page;
		$this->tt_content = &$basket->tt_content;
		$this->tt_products = &$basket->tt_products;
		$this->tt_products_articles = &$basket->tt_products_articles;
		$this->tt_products_cat = &$basket->tt_products_cat;
		$this->fe_users = &$basket->fe_users;
 		$this->tx_dam = &$basket->tx_dam;
		$this->tx_dam_cat = &$basket->tx_dam_cat;
		$this->viewTable = &$basket->viewTable;
		$this->price = &$basket->price;
		$this->paymentshipping = &$basket->paymentshipping;
		$this->templateCode = &$templateCode;
		$this->error_code = &$error_code;
		$this->marker = t3lib_div::makeInstance('tx_ttproducts_marker');
		$this->marker->init($this->pibase, $this->cnf, $this->basket);
	} // init


	function getMarkerArray ()	{
		$markerArray = array();

			// This is the total for the goods in the basket.
		$markerArray['###PRICE_GOODSTOTAL_TAX###'] = $this->price->priceFormat($this->basket->calculatedArray['priceTax']['goodstotal']);
		$markerArray['###PRICE_GOODSTOTAL_NO_TAX###'] = $this->price->priceFormat($this->basket->calculatedArray['priceNoTax']['goodstotal']);
		$markerArray['###PRICE_GOODSTOTAL_ONLY_TAX###'] = $this->price->priceFormat($this->basket->calculatedArray['priceTax']['goodstotal']-$this->basket->calculatedArray['priceNoTax']['goodstotal']);

		$markerArray['###PRICE2_GOODSTOTAL_TAX###'] = $this->price->priceFormat($this->basket->calculatedArray['price2Tax']['goodstotal']);
		$markerArray['###PRICE2_GOODSTOTAL_NO_TAX###'] = $this->price->priceFormat($this->basket->calculatedArray['price2NoTax']['goodstotal']);
		$markerArray['###PRICE2_GOODSTOTAL_ONLY_TAX###'] = $this->price->priceFormat($this->basket->calculatedArray['price2Tax']['goodstotal']-$this->basket->calculatedArray['price2NoTax']['goodstotal']);

		$markerArray['###PRICE_DISCOUNT_GOODSTOTAL_TAX###']    = $this->price->priceFormat($this->basket->calculatedArray['noDiscountPriceTax']['goodstotal']-$this->basket->calculatedArray['priceTax']['goodstotal']);
		$markerArray['###PRICE_DISCOUNT_GOODSTOTAL_NO_TAX###'] = $this->price->priceFormat($this->basket->calculatedArray['noDiscountPriceNoTax']['goodstotal']-$this->basket->calculatedArray['priceNoTax']['goodstotal']);
		$markerArray['###PRICE2_DISCOUNT_GOODSTOTAL_TAX###']    = $this->price->priceFormat($this->basket->calculatedArray['noDiscountPriceTax']['goodstotal']-$this->basket->calculatedArray['price2Tax']['goodstotal']);
		$markerArray['###PRICE2_DISCOUNT_GOODSTOTAL_NO_TAX###'] = $this->price->priceFormat($this->basket->calculatedArray['noDiscountPriceNoTax']['goodstotal']-$this->basket->calculatedArray['price2NoTax']['goodstotal']);

			// This is the total for everything
		$taxRateArray = t3lib_div::trimExplode(',', $this->conf['TAXrates']);
		if (isset($taxRateArray) && is_array($taxRateArray))	{
			foreach ($taxRateArray as $k => $taxrate)	{
				$taxstr = strval(number_format(floatval($taxrate),2));
				$label = chr(ord('A')+$k);
				$markerArray['###PRICE_TAXRATE_NAME'.($k+1).'###'] = $label;
				$markerArray['###PRICE_TAXRATE_TAX'.($k+1).'###'] = $taxrate;
				$label = $this->price->priceFormat($this->basket->calculatedArray['priceNoTax']['sametaxtotal'][$taxstr]);
				$markerArray['###PRICE_TAXRATE_TOTAL'.($k+1).'###'] = $label;
				$label = $this->price->priceFormat($this->basket->calculatedArray['priceNoTax']['sametaxtotal'][$taxstr] * ($taxrate/100));
				$markerArray['###PRICE_TAXRATE_ONLY_TAX'.($k+1).'###'] = $label;
			}
		}
		// This is for the Basketoverview
		$markerArray['###NUMBER_GOODSTOTAL###'] = $this->basket->calculatedArray['count'];
		$fileresource = $this->pibase->cObj->fileResource($this->conf['basketPic']);
		$markerArray['###IMAGE_BASKET###'] = $fileresource;
		return $markerArray;
	}


	/**
	 * This generates the shopping basket layout and also calculates the totals. Very important function.
	 */
	function getView (&$templateCode, $code, &$infoObj, $bSelectSalutation, $bSelectVariants, $bHtml=TRUE, $subpartMarker='###BASKET_TEMPLATE###', $mainMarkerArray=array())	{
			/*
				Very central function in the library.
				By default it extracts the subpart, ###BASKET_TEMPLATE###, from the $templateCode (if given, else the default $this->templateCode)
				and substitutes a lot of fields and subparts.
				Any pre-preparred fields can be set in $mainMarkerArray, which is substituted in the subpart before the item-and-categories part is substituted.
			*/
		global $TSFE, $TCA;
		global $TYPO3_DB, $TYPO3_CONF_VARS;

		$out = '';
		$langObj = t3lib_div::getUserObj('&tx_ttproducts_language');

		if (!$templateCode)	{
			$templateCode = &$this->templateCode;
		}

		$creditpointsObj = t3lib_div::getUserObj('&tx_ttproducts_field_creditpoints');

			// Getting subparts from the template code.
		$t=array();
		$t['basketFrameWork'] = $this->pibase->cObj->getSubpart($templateCode,$this->marker->spMarker($subpartMarker));

		$SubpartEmptyArray = array('###EMAIL_PLAINTEXT_TEMPLATE_SHOP###', '###BASKET_ORDERCONFIRMATION_NOSAVE_TEMPLATE###');
		if (!$t['basketFrameWork'] && !in_array($subpartMarker, $SubpartEmptyArray)) {
			$this->error_code[0] = 'no_subtemplate';
			$this->error_code[1] = $subpartMarker;
			$this->error_code[2] = $this->conf['templateFile'];
			return '';
		}

		if ($t['basketFrameWork'])	{
			if (!$bHtml)	{
				$t['basketFrameWork'] = html_entity_decode($t['basketFrameWork'],ENT_QUOTES,$TSFE->renderCharset);
			}

				// If there is a specific section for the billing address if user is logged in (used because the address may then be hardcoded from the database
			if (trim($this->pibase->cObj->getSubpart($t['basketFrameWork'],'###BILLING_ADDRESS_LOGIN###')))	{
				//if ($GLOBALS['TSFE']->loginUser)	{
				if ($TSFE->loginUser && $this->conf['lockLoginUserInfo']) {
					$t['basketFrameWork'] = $this->pibase->cObj->substituteSubpart($t['basketFrameWork'], '###BILLING_ADDRESS###', '');
				} else {
					$t['basketFrameWork'] = $this->pibase->cObj->substituteSubpart($t['basketFrameWork'], '###BILLING_ADDRESS_LOGIN###', '');
				}
			}
			$t['categoryFrameWork'] = $this->pibase->cObj->getSubpart($t['basketFrameWork'],'###ITEM_CATEGORY###');
			$t['itemFrameWork'] = $this->pibase->cObj->getSubpart($t['basketFrameWork'],'###ITEM_LIST###');
			$t['item'] = $this->pibase->cObj->getSubpart($t['itemFrameWork'],'###ITEM_SINGLE###');

			$currentP='';
			$out='';
			$itemsOut='';
			$viewTagArray = array();
			$markerFieldArray = array('BULKILY_WARNING' => 'bulkily',
				'PRODUCT_SPECIAL_PREP' => 'special_preparation',
				'PRODUCT_ADDITIONAL_SINGLE' => 'additional',
				'LINK_DATASHEET' => 'datasheet');
			$parentArray = array();
			$fieldsArray = $this->marker->getMarkerFields(
				$t['item'],
				$this->viewTable->table->tableFieldArray,
				$this->viewTable->table->requiredFieldArray,
				$markerFieldArray,
				$this->viewTable->marker,
				$viewTagArray,
				$parentArray
			);
			$count = 0;
			$basketItemView = '';
			$this->basket->checkMinPrice = false;

			$damViewTagArray = array();
			if (is_object ($this->tx_dam))	{
				$damParentArray = array();
				$fieldsArray = $this->marker->getMarkerFields(
					$itemFrameWork,
					$this->tx_dam->table->tableFieldArray,
					$this->tx_dam->table->requiredFieldArray,
					$markerFieldArray,
					$this->tx_dam->marker,
					$damViewTagArray,
					$damParentArray
				);
				$damCatMarker = $this->tx_dam_cat->marker;
				$this->tx_dam_cat->marker = 'DAM_CAT';

				$viewDamCatTagArray = array();
				$catParentArray = array();
				$catfieldsArray = $this->marker->getMarkerFields(
					$itemFrameWork,
					$this->tx_dam_cat->table->tableFieldArray,
					$this->tx_dam_cat->table->requiredFieldArray,
					$tmp = array(),
					$this->tx_dam_cat->marker,
					$viewDamCatTagArray,
					$catParentArray
				);
			}

			// loop over all items in the basket indexed by sorting text
			foreach ($this->basket->itemArray as $sort => $actItemArray) {
				foreach ($actItemArray as $k1=>$actItem) {
					$row = $actItem['rec'];

					$this->viewTable->table->substituteMarkerArray($row);
					$actItem['rec'] = $row;	// fix bug with PHP 5.2.1

					$pid = intval($row['pid']);
					if (!isset($this->page->pageArray[$pid]))	{
						// product belongs to another basket
						continue;
					}
					$count++;

					if (!$this->viewTable->hasAdditional($row,'noMinPrice'))	{
						$this->basket->checkMinPrice = true;
					}
					$pidcategory = ($this->pibase->pageAsCategory == 1 ? $pid : '');
					$currentPnew = $pidcategory.'_'.$actItem['rec']['category'];
						// Print Category Title
					if ($currentPnew!=$currentP)	{
						if ($itemsOut)	{
							$out .= $this->pibase->cObj->substituteSubpart($t['itemFrameWork'], '###ITEM_SINGLE###', $itemsOut);
						}
						$itemsOut='';		// Clear the item-code var
						$currentP = $currentPnew;
						if ($this->conf['displayBasketCatHeader'])	{
							$markerArray=array();
							$pageCatTitle = '';
							if ($this->pibase->pageAsCategory == 1) {
								$pageTmp = $this->page->get($pid);
								$pageCatTitle = $pageTmp['title'].'/';
							}
							$catTmp = '';
							if ($actItem['rec']['category']) {
								$catTmp = $this->tt_products_cat->get($actItem['rec']['category']);
								$catTmp = $catTmp['title'];
							}
							$catTitle = $pageCatTitle.$catTmp;
							$this->pibase->cObj->setCurrentVal($catTitle);
							$markerArray['###CATEGORY_TITLE###']=$this->pibase->cObj->cObjGetSingle($this->conf['categoryHeader'],$this->conf['categoryHeader.'], 'categoryHeader');

							$out .= $this->pibase->cObj->substituteMarkerArray($t['categoryFrameWork'], $markerArray);
						}
					}

						// Fill marker arrays
					$wrappedSubpartArray = array();
					$subpartArray = array();
					$markerArray = array();

					if (!is_object($basketItemView))	{
						include_once (PATH_BE_ttproducts.'view/class.tx_ttproducts_basketitem_view.php');
						$basketItemView = t3lib_div::getUserObj('tx_ttproducts_basketitem_view');
						$basketItemView->init($this->pibase, $this->tt_products_cat, $this->basket->basketExt, $this->tx_dam, $this->tx_dam_cat);
					}

					// $extRow = array('extTable' => $row['extTable'], 'extUid' => $row['extUid']);

					$basketItemView->getItemMarkerArray($this->viewTable, $actItem, $markerArray, $code, $count);
					$catRow = $row['category'] ? $this->tt_products_cat->get($row['category']) : array();

					$catTitle = $catRow['title'];
					$tmp = array();
// 					$this->viewTable->getItemSubpartArrays (
// 							$t['item'],
// 							$row,
// 							$subpartArray,
// 							$wrappedSubpartArray,
// 							$viewTagArray,
// 							$code,
// 							$count
// 						);
					$this->viewTable->getItemMarkerArray(
						$actItem,
						$markerArray,
						$catTitle,
						$this->basket->basketExt,
						1,
						'basketImage',
						$viewTagArray,
						$tmp,
						$code,
						$count,
						'',
						'',
						false,
						$bHtml
					);

					$this->pibase->cObj->setCurrentVal($catTitle);
					$markerArray['###CATEGORY_TITLE###'] = $this->pibase->cObj->cObjGetSingle($this->conf['categoryHeader'],$this->conf['categoryHeader.'], 'categoryHeader');
					$markerArray['###PRICE_TOTAL_TAX###'] = $this->price->priceFormat($actItem['totalTax']);
					$markerArray['###PRICE_TOTAL_NO_TAX###'] = $this->price->priceFormat($actItem['totalNoTax']);
					$markerArray['###PRICE_TOTAL_ONLY_TAX###'] = $this->price->priceFormat($actItem['totalTax']-$actItem['totalNoTax']);

					if ($row['category'] == $this->conf['creditsCategory']) {
						// creditpoint system start
						$pricecredits_total_totunits_no_tax = $actItem['totalNoTax']*$row['unit_factor'];
						$pricecredits_total_totunits_tax = $actItem['totalTax']*$row['unit_factor'];
					} else if ($row['price'] > 0 && $row['price2'] > 0 && $row['unit_factor'] > 0) {
						$pricecredits_total_totunits_no_tax = 0;
						$pricecredits_total_totunits_tax = 0;
						$unitdiscount = ($row['price'] - $row['price2']) * $row['unit_factor'] * $actItem['count'];
						$sum_pricediscount_total_totunits += $unitdiscount;
					}
					$markerArray['###PRICE_TOTAL_TOTUNITS_NO_TAX###'] = $this->price->priceFormat($pricecredits_total_totunits_no_tax);
					$markerArray['###PRICE_TOTAL_TOTUNITS_TAX###'] = $this->price->priceFormat($pricecredits_total_totunits_tax);

					$sum_pricecredits_total_totunits_no_tax += $pricecredits_total_totunits_no_tax;
					$sum_price_total_totunits_no_tax += $pricecredits_total_totunits_no_tax;
					$sum_pricecreditpoints_total_totunits += $pricecredits_total_totunits_no_tax;

					// creditpoint system end

					$pid = $this->page->getPID($this->conf['PIDitemDisplay'], $this->conf['PIDitemDisplay.'], $row, $TSFE->rootLine[1]);
					$addQueryString=array();
					// $addQueryString[$this->pibase->prefixId.'['.$this->viewTable->type.']'] = intval($row['uid']);
					$addQueryString[$this->viewTable->type] = intval($row['uid']);
					$extArray = $row['ext'];

					if (is_array($extArray) && is_array($extArray[$this->viewTable->conftablename]))	{
//						$addQueryString[$this->pibase->prefixId.'[variants]'] = htmlspecialchars($extArray[$this->viewTable->conftablename][0]['vars']);
						$addQueryString['variants'] = htmlspecialchars($extArray[$this->viewTable->conftablename][0]['vars']);
					}
					$isImageProduct = $this->viewTable->hasAdditional($row,'isImage');
					$damMarkerArray = array();
					$damCategoryMarkerArray = array();
					if (($isImageProduct || $this->viewTable->conftablename == 'tt_products') && is_array($extArray) && is_array($extArray['tx_dam']))	{
						$damext = current($extArray['tx_dam']);
						$damUid = $damext['uid'];
						$damRow = $this->tx_dam->get($damUid);
						$damItem = array();
						$damItem['rec'] = $damRow;
						$damCategoryArray = $this->tx_dam_cat->getCategoryArray ($damUid);
						if (count($damCategoryArray))	{
							$damCat = current($damCategoryArray);
						}

						$this->tx_dam_cat->getMarkerArray (
							$damCategoryMarkerArray,
							$this->page,
							$damCat,
							$damRow['pid'],
							$this->config['limitImage'],
							'basketImage',
							$viewDamCatTagArray,
							array(),
							$this->pibase->pageAsCategory,
							'SINGLE',
							1,
							''
						);
						$this->tx_dam->getItemMarkerArray ($damItem, $damMarkerArray, $damCatRow['title'], $this->basket->basketExt, 1, 'basketImage', $damViewTagArray, $tmp, $code, $count, '', '', false, $bHtml);
					}
					$markerArray = array_merge($markerArray, $damMarkerArray, $damCategoryMarkerArray);
					// $addQueryString['ttp_extvars'] = htmlspecialchars($actItem['rec']['extVars']);

					$wrappedSubpartArray['###LINK_ITEM###'] = array('<a href="'. $this->pibase->pi_getPageLink($pid,'',$this->marker->getLinkParams('', $addQueryString, true), array('useCacheHash' => true)).'"'.$css_current.'>','</a>');

					// Substitute
					$tempContent = $this->pibase->cObj->substituteMarkerArrayCached($t['item'],$markerArray,$subpartArray,$wrappedSubpartArray);
					$this->viewTable->variant->getVariantSubpartArray (
						$subpartArray,
						$row,
						$tempContent,
						$bSelectVariants,
						$this->conf
					);
					$this->basket->fe_users->setCondition($row, $this->viewTable->conftablename);
					$this->basket->fe_users->getWrappedSubpartArray($subpartArray, $wrappedSubpartArray, $this->viewTable->conftablename);
					$tempContent = $this->pibase->cObj->substituteMarkerArrayCached($tempContent,$markerArray,$subpartArray,$wrappedSubpartArray);
					$itemsOut .= $tempContent;
				}
				if ($itemsOut)	{
					$tempContent=$this->pibase->cObj->substituteSubpart($t['itemFrameWork'], '###ITEM_SINGLE###', $itemsOut);
					$out .= $tempContent;
					$itemsOut='';	// Clear the item-code var
				}
			}

			if (is_object ($this->tx_dam))	{
				$this->tx_dam_cat->marker = $damCatMarker; // restore original value
			}
			$subpartArray = array();
			$wrappedSubpartArray = array();

				// Initializing the markerArray for the rest of the template
			$markerArray = $mainMarkerArray;
			$basketMarkerArray = $this->getMarkerArray();
			$markerArray = array_merge($markerArray,$basketMarkerArray);
			$pid = ($this->conf['PIDbasket'] ? $this->conf['PIDbasket'] : $TSFE->id);

/*
			$tmpLinkParam = $this->marker->getLinkParams(
				'',
				array(),
				TRUE,
				TRUE
			);
			$wrappedSubpartArray['###LINK_BASKET###'] = array(
				'<a href="' . htmlspecialchars(
					$this->pibase->pi_getPageLink(
						$pid,
						'',
						$tmpLinkParam
					)
				) . '">',
				'</a>'
			);*/

			$conf = array('useCacheHash' => FALSE);
			$url = tx_div2007_alpha::getTypoLink_URL_fh002(
				$this->pibase->cObj,
				$pid,
				$this->marker->getLinkParams(
					'',
					array(),
					TRUE,
					TRUE,
					''
				),
				$target = '',
				$conf
			);
			$htmlUrl = htmlspecialchars(
					$url,
					ENT_NOQUOTES,
					$GLOBALS['TSFE']->renderCharset
				);

			$wrappedSubpartArray['###LINK_BASKET###'] = array('<a href="'. $htmlUrl .'">','</a>');

			$activityArray = tx_ttproducts_model_activity::getActivityArray();
			$hiddenFields = '';
			if (is_array($activityArray))	{
				$activity = '';
				if ($activityArray['products_payment'])	{
					$activity = 'payment';
				} else if ($activityArray['products_info']) {
					$activity = 'infoObj';
				}
				if ($activity)	{
					$bUseXHTML = $TSFE->config['config']['xhtmlDoctype'] != '';
					$hiddenFields .= '<input type="hidden" name="' . TT_PRODUCTS_EXTkey . '[activity]" value="' . $activity . '" ' . ($bUseXHTML ? '/' : '') . '>';
				}
			}
			$markerArray['###HIDDENFIELDS###'] .= $hiddenFields;

			// shipping
			//$markerArray['###PRICE_SHIPPING_PERCENT###'] = $perc;
			$markerArray['###PRICE_SHIPPING_TAX###'] = $this->price->priceFormat($this->basket->calculatedArray['priceTax']['shipping']);
			$markerArray['###PRICE_SHIPPING_NO_TAX###'] = $this->price->priceFormat($this->basket->calculatedArray['priceNoTax']['shipping']);
			$markerArray['###PRICE_SHIPPING_ONLY_TAX###'] = $this->price->priceFormat($this->basket->calculatedArray['priceTax']['shipping']-$this->basket->calculatedArray['priceNoTax']['shipping']);

			$markerArray['###SHIPPING_SELECTOR###'] = $this->paymentshipping->generateRadioSelect('shipping', $this->basket->calculatedArray);
			$markerArray['###SHIPPING_IMAGE###'] = $this->pibase->cObj->IMAGE($this->basket->basketExtra['shipping.']['image.']);

			$shippingTitle = $this->basket->basketExtra['shipping.']['title'];
			$markerArray['###SHIPPING_TITLE###'] = $shippingTitle;
			$markerArray['###SHIPPING_WEIGHT###'] = doubleval($this->basket->calculatedArray['weight']);
			$markerArray['###DELIVERYCOSTS###'] = $this->price->priceFormat($this->paymentshipping->getDeliveryCosts());

			//$markerArray['###PRICE_PAYMENT_PERCENT###'] = $perc;
			$markerArray['###PRICE_PAYMENT_TAX###'] = $this->price->priceFormat($this->basket->calculatedArray['priceTax']['payment']);
			$markerArray['###PRICE_PAYMENT_NO_TAX###'] = $this->price->priceFormat($this->basket->calculatedArray['priceNoTax']['payment']);
			$markerArray['###PRICE_PAYMENT_ONLY_TAX###'] = $this->price->priceFormat($this->basket->calculatedArray['priceTax']['payment']-$this->basket->calculatedArray['priceNoTax']['payment'] );

			$markerArray['###PAYMENT_SELECTOR###'] = $this->paymentshipping->generateRadioSelect('payment', $this->basket->calculatedArray);
			$markerArray['###PAYMENT_IMAGE###'] = $this->pibase->cObj->IMAGE($this->basket->basketExtra['payment.']['image.']);
			$markerArray['###PAYMENT_TITLE###'] = $this->basket->basketExtra['payment.']['title'];
			$markerArray['###PAYMENT_NUMBER###'] = htmlspecialchars(t3lib_div::_GP('payment_number'));
			$markerArray['###PAYMENT_NAME###'] = htmlspecialchars(t3lib_div::_GP('payment_name'));
			$markerArray['###PAYMENT_CITY###'] = htmlspecialchars(t3lib_div::_GP('payment_city'));

			// for receipt from DIBS script
			$markerArray['###TRANSACT_CODE###'] = htmlspecialchars(t3lib_div::_GP('transact'));

				// Fill the Currency Symbol or not
			if ($this->conf['showcurSymbol']) {
				$markerArray['###CUR_SYM###'] = ' '.$this->conf['currencySymbol'];
			} else {
				$markerArray['###CUR_SYM###'] = '';
			}

			$markerArray['###PRICE_DISCOUNT###'] = $this->price->priceFormat($this->basket->calculatedArray['oldPriceNoTax']-$this->basket->calculatedArray['priceNoTax']['goodstotal']);
			$markerArray['###PRICE_VAT###'] = $this->price->priceFormat($this->basket->calculatedArray['priceTax']['goodstotal']-$this->basket->calculatedArray['priceNoTax']['goodstotal']);

			$markerArray['###PRICE_TOTUNITS_DISCOUNT###'] = $this->price->priceFormat($sum_pricediscount_total_totunits);

			include_once (PATH_BE_ttproducts.'model/class.tx_ttproducts_order.php');
				// order
			$order = t3lib_div::makeInstance('tx_ttproducts_order');
			$order->init(
				$this->pibase,
				$this->cnf,
				$this->tt_products,
				$this->tt_products_articles,
				$this->tt_products_cat,
				$this->basket,
				$this->conf['useArticles']
			);
				// Order:	NOTE: Data exist only if the order->getBlankUid() has been called. Therefore this field in the template should be used only when an order has been established
			$markerArray['###ORDER_UID###'] = $order->getNumber($this->basket->order['orderUid']);
			$markerArray['###ORDER_DATE###'] = $this->pibase->cObj->stdWrap($this->basket->order['orderDate'],$this->conf['orderDate_stdWrap.']);
			$markerArray['###ORDER_TRACKING_NO###'] = $this->basket->order['orderTrackingNo'];

				// URL
			$markerArray =  $this->marker->addURLMarkers(0, $markerArray);

			$taxFromShipping = $this->paymentshipping->getReplaceTaxPercentage();
			$taxInclExcl = (isset($taxFromShipping) && is_double($taxFromShipping) && $taxFromShipping == 0 ? 'tax_zero' : 'tax_included');
			$markerArray['###TAX_INCL_EXCL###'] = ($taxInclExcl ? tx_div2007_alpha5::getLL_fh002($langObj, $taxInclExcl) : '');

			if ($TSFE->fe_user->user['tt_products_vouchercode'] == '') {
				$subpartArray['###SUB_VOUCHERCODE###'] = '';
				$markerArray['###INSERT_VOUCHERCODE###'] = 'recs[tt_products][vouchercode]';
				$markerArray['###VALUE_VOUCHERCODE###'] = htmlspecialchars($this->basket->recs['tt_products']['vouchercode']);
				if ($this->basket->recs['tt_products']['vouchercode'] == '') {
					$subpartArray['###SUB_VOUCHERCODE_DISCOUNT###'] = '';
					$subpartArray['###SUB_VOUCHERCODE_DISCOUNTOWNID###'] = '';
					$subpartArray['###SUB_VOUCHERCODE_DISCOUNTWRONG###'] = '';
				} else {
					$res = $TYPO3_DB->exec_SELECTquery('uid', 'fe_users', $TYPO3_DB->fullQuoteStr($this->basket->recs['tt_products']['vouchercode'], 'fe_users'));

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
							//$this->basket->calculatedArray['priceTax']['voucher'] = $this->price->priceFormat($this->basket->calculatedArray['priceTax']['shipping']);
							$this->basket->calculatedArray['priceTax']['voucher'] = $this->conf['voucher.']['price'];
							$markerArray['###VOUCHER_DISCOUNT###'] = $this->price->priceFormat($this->basket->calculatedArray['priceTax']['voucher']);
							$subpartArray['###SUB_VOUCHERCODE_NODISCOUNT###'] = '';
						}
					} else {
						$subpartArray['###SUB_VOUCHERCODE_DISCOUNTOWNID###'] = '';
						$subpartArray['###SUB_VOUCHERCODE_DISCOUNT###'] = '';
					}
				}
			} else {
				$subpartArray['###SUB_VOUCHERCODE_EMPTY###'] = '';
				$markerArray['###VOUCHER_DISCOUNT###'] = '0.00';
			}

			if ($subpartMarker != '###BASKET_OVERVIEW_TEMPLATE###') {

	// Added Franz: GIFT CERTIFICATE
				$markerArray['###GIFT_CERTIFICATE_UNIQUE_NUMBER_NAME###']='recs[tt_products][giftcode]'; // deprecated
				$markerArray['###FORM_NAME###']='BasketForm';
				$markerArray['###FORM_NAME_GIFT_CERTIFICATE###']='BasketGiftForm';

				$markerArray['###INSERT_GIFTCODE###'] = 'recs[tt_products][giftcode]';
				$markerArray['###VALUE_GIFTCODE###'] = htmlspecialchars($this->basket->recs['tt_products']['giftcode']);
				$cpArray = $TSFE->fe_user->getKey('ses','cp');
				$creditpointsGifts = $cpArray['gift']['amount'];
				$markerArray['###CREDITPOINTS_GIFTS###'] = $creditpointsGifts;

				if ($this->basket->recs['tt_products']['giftcode'] == '') {
					$subpartArray['###SUB_GIFTCODE_DISCOUNT###'] = '';
					$subpartArray['###SUB_GIFTCODE_DISCOUNTWRONG###'] = '';
					if ($creditpointsGifts == '') {
						$subpartArray['###SUB_GIFTCODE_DISCOUNT_TRUE###'] = '';
					}
				} else {
					$uniqueId = t3lib_div::trimExplode ('-', $this->basket->recs['tt_products']['giftcode'], true);
					$query='uid=\'' . intval($uniqueId[0]) . '\' AND crdate=\'' . intval($uniqueId[1]) . '\'';
					$giftRes = $TYPO3_DB->exec_SELECTquery('*', 'tt_products_gifts', $query);
					$row = $TYPO3_DB->sql_fetch_assoc($giftRes);
					$TYPO3_DB->sql_free_result($giftRes);
					$pricefactor = doubleval($this->conf['creditpoints.']['pricefactor']);
					$creditpointsDiscount = $creditpointsGifts * $pricefactor;
					$markerArray['###GIFT_DISCOUNT###'] = $creditpointsDiscount;
					$markerArray['###VALUE_GIFTCODE_USED###'] = htmlspecialchars($this->basket->recs['tt_products']['giftcode']);

					if ($row && $creditpointsGifts && $pricefactor > 0) {
						$subpartArray['###SUB_GIFTCODE_DISCOUNTWRONG###']= '';
						if ($creditpointsGifts == '') {
							$subpartArray['###SUB_GIFTCODE_DISCOUNT_TRUE###'] = '';
						}
					} else {
						$markerArray['###VALUE_GIFTCODE_USED###'] = '**********';
						if (t3lib_div::_GP('creditpoints_gifts') == '') {
							$subpartArray['###SUB_GIFTCODE_DISCOUNT_TRUE###'] = '';
						}
					}
				}
			}
			$amountCreditpoints = $TSFE->fe_user->user['tt_products_creditpoints']+$creditpointsGifts;

			$markerArray['###AMOUNT_CREDITPOINTS###'] = $amountCreditpoints;

// #### START
			$pricefactor = doubleval($this->conf['creditpoints.']['priceprod']);
 			$autoCreditpointsTotal = $creditpointsObj->getBasketTotal();
 			$markerArray['###AUTOCREDITPOINTS_TOTAL###'] = number_format($autoCreditpointsTotal,'0');
 			$markerArray['###AUTOCREDITPOINTS_PRICE_TOTAL_TAX###'] = $this->price->priceFormat($autoCreditpointsTotal * $pricefactor);
 			$markerArray['###USERCREDITPOINTS_PRICE_TOTAL_TAX###'] = $this->price->priceFormat(($autoCreditpointsTotal < $amountCreditpoints ? $autoCreditpointsTotal : $amountCreditpoints) * $pricefactor);

			$markerArray['###CREDITPOINTS_AVAILABLE###'] = number_format($TSFE->fe_user->user['tt_products_creditpoints'],'0');

			// maximum1 amount of creditpoint to change is amount on account minus amount already spended in the credit-shop
			if ($autoCreditpointsTotal > $TSFE->fe_user->user['tt_products_creditpoints'])	{
				$autoCreditpointsTotal = $TSFE->fe_user->user['tt_products_creditpoints'];
			}

			$creditpoints = $autoCreditpointsTotal + $sum_pricecreditpoints_total_totunits * tx_ttproducts_creditpoints_div::getCreditPoints($sum_pricecreditpoints_total_totunits, $this->conf['creditpoints.']);

// #### ENDE

//			$pricefactor = doubleval($this->conf['creditpoints.']['pricefactor']);

			$max1_creditpoints = $TSFE->fe_user->user['tt_products_creditpoints'] + $creditpointsGifts;

			// maximum2 amount of creditpoint to change is amount bought multiplied with creditpointfactor
			if ($pricefactor > 0) {
				$max2_creditpoints = intval(($this->basket->calculatedArray['priceTax']['total'] - $this->basket->calculatedArray['priceTax']['vouchertotal']) / $pricefactor );
			}

			// real maximum amount of creditpoint to change is minimum of both maximums
			$markerArray['###AMOUNT_CREDITPOINTS_MAX###'] = number_format( min ($max1_creditpoints,$max2_creditpoints),0);

			// if quantity is 0 than
			if ($amountCreditpoints == '0') {
				$subpartArray['###SUB_CREDITPOINTS_DISCOUNT###'] = '';
				$wrappedSubpartArray['###SUB_CREDITPOINTS_DISCOUNT_EMPTY###'] = '';
				$subpartArray['###SUB_CREDITPOINTS_AMOUNT###'] = '';
			} else {
				$wrappedSubpartArray['###SUB_CREDITPOINTS_DISCOUNT###'] = '';
				$subpartArray['###SUB_CREDITPOINTS_DISCOUNT_EMPTY###'] = '';
				$wrappedSubpartArray['###SUB_CREDITPOINTS_AMOUNT_EMPTY###'] = '';
				$wrappedSubpartArray['###SUB_CREDITPOINTS_AMOUNT###'] = '';
			}
			$markerArray['###CHANGE_AMOUNT_CREDITPOINTS###'] = 'recs[tt_products][creditpoints]';

			if ($this->basket->recs['tt_products']['creditpoints'] == '') {
				$markerArray['###AMOUNT_CREDITPOINTS_QTY###'] = 0;
				$subpartArray['###SUB_CREDITPOINTS_DISCOUNT###'] = '';
				$markerArray['###CREDIT_DISCOUNT###'] = '0.00';
			} else {

				// quantity chosen can not be larger than the maximum amount, above calculated
				if ($this->basket->recs['tt_products']['creditpoints'] > min ($max1_creditpoints,$max2_creditpoints))	{
					$this->basket->recs['tt_products']['creditpoints'] = min ($max1_creditpoints,$max2_creditpoints);
				}
				// $this->basket->calculatedArray['priceTax']['creditpoints'] = $this->price->priceFormat($this->basket->recs['tt_products']['creditpoints']*$pricefactor);

				$markerArray['###AMOUNT_CREDITPOINTS_QTY###'] = htmlspecialchars($this->basket->recs['tt_products']['creditpoints']);
				$subpartArray['###SUB_CREDITPOINTS_DISCOUNT_EMPTY###'] = '';
				$markerArray['###CREDIT_DISCOUNT###'] = $this->basket->calculatedArray['priceTax']['creditpoints'];
			}

	/* Added els5: CREDITPOINTS_SPENDED: creditpoint needed, check if user has this amount of creditpoints on his account (winkelwagen.tmpl), only if user has logged in */
			$markerArray['###CREDITPOINTS_SPENDED###'] = $sum_pricecredits_total_totunits_no_tax;
			if ($sum_pricecredits_total_totunits_no_tax <= $amountCreditpoints) {
				$subpartArray['###SUB_CREDITPOINTS_SPENDED_EMPTY###'] = '';
				$markerArray['###CREDITPOINTS_SPENDED###'] = $sum_pricecredits_total_totunits_no_tax;
				// new saldo: creditpoints
				$markerArray['###AMOUNT_CREDITPOINTS###'] = $amountCreditpoints - $markerArray['###CREDITPOINTS_SPENDED###'];
			} else {
				if (!$markerArray['###FE_USER_UID###']) {
					$subpartArray['###SUB_CREDITPOINTS_SPENDED_EMPTY###'] = '';
				} else {
					$markerArray['###CREDITPOINTS_SPENDED_ERROR###'] = 'Wijzig de artikelen in de kurkenshop: onvoldoende kurken op uw saldo ('.$amountCreditpoints.').'; // TODO
					$markerArray['###CREDITPOINTS_SPENDED###'] = '&nbsp;';
				}
			}

			// creditpoint system end

			// check the basket limits
			$basketConf = $this->cnf->getBasketConf('minPrice');
			$minPriceSuccess = true;
			if ($this->basket->checkMinPrice && $basketConf['type'] == 'price')	{
				$value = $this->basket->calculatedArray['priceTax'][$basketConf['collect']];
				if (isset($value) && isset($basketConf['collect']) && $value < doubleval($basketConf['value']))	{
					$subpartArray['###MESSAGE_MINPRICE###'] = '';
					$tmpSubpart = $this->pibase->cObj->getSubpart($t['basketFrameWork'],'###MESSAGE_MINPRICE_ERROR###');
					$subpartArray['###MESSAGE_MINPRICE_ERROR###'] = $this->pibase->cObj->substituteMarkerArray($tmpSubpart,$markerArray);
					$minPriceSuccess = false;
				}
			}
			if ($minPriceSuccess)	{
				$subpartArray['###MESSAGE_MINPRICE_ERROR###'] = '';
				$tmpSubpart = $this->pibase->cObj->getSubpart($t['basketFrameWork'],'###MESSAGE_MINPRICE###');
				$subpartArray['###MESSAGE_MINPRICE###'] = $this->pibase->cObj->substituteMarkerArray($tmpSubpart,$markerArray);
			}

			$markerArray['###CREDITPOINTS_SAVED###'] = number_format($creditpoints,'0');
			$agb_url=array();
			$pidagb = intval($this->conf['PIDagb']);
			// $addQueryString['id'] = $pidagb;
			if ($TSFE->type)	{
				$addQueryString['type'] = $TSFE->type;
			}
			$wrappedSubpartArray['###LINK_AGB###'] = array(
				'<a href="'. $this->pibase->pi_getPageLink($pidagb,'',$this->marker->getLinkParams('', $addQueryString, true)) .'" target="'.$this->conf['AGBtarget'].'">',
				'</a>'
			);

			$pidRevocation = intval($this->conf['PIDrevocation']);
			$wrappedSubpartArray['###LINK_REVOCATION###'] = array(
				'<a href="' . htmlspecialchars(
					$this->pibase->pi_getPageLink(
						$pidRevocation,
						'',
						$this->marker->getLinkParams(
							'',
							$addQueryString,
							TRUE
						)
					)
				) . '" target="' . $this->conf['AGBtarget'] . '">',
				'</a>'
			);


				// Final substitution:
			if (!$TSFE->loginUser)	{	// Remove section for FE_USERs only, if there are no fe_user
				$subpartArray['###FE_USER_SECTION###']='';
			}

			if (is_object($infoObj))	{
				$infoObj->getItemMarkerArray($markerArray, $bSelectSalutation);
			}

			$markerArray['###PRICE_TOTAL_TAX###'] = $this->price->priceFormat($this->basket->calculatedArray['priceTax']['total']);
			$markerArray['###PRICE_TOTAL_NO_TAX###'] = $this->price->priceFormat($this->basket->calculatedArray['priceNoTax']['total']);
			$markerArray['###PRICE_TOTAL_ONLY_TAX###'] = $this->price->priceFormat($this->basket->calculatedArray['priceTax']['total']-$this->basket->calculatedArray['priceNoTax']['total']);
			$markerArray['###PRICE_VOUCHERTOTAL_TAX###'] = $this->price->priceFormat($this->basket->calculatedArray['priceTax']['vouchertotal']);
			$markerArray['###PRICE_VOUCHERTOTAL_NO_TAX###'] = $this->price->priceFormat($this->basket->calculatedArray['priceNoTax']['vouchertotal']);
			$markerArray['###PRODUCT_RELATED_UID###'] = '';

				// Call all getItemMarkerArrays hooks at the end of this method
			if (is_array ($TYPO3_CONF_VARS['EXTCONF'][TT_PRODUCTS_EXTkey]['getBasketView'])) {
				foreach ($TYPO3_CONF_VARS['EXTCONF'][TT_PRODUCTS_EXTkey]['getBasketView'] as $classRef) {
					$hookObj= t3lib_div::getUserObj($classRef);
					if (method_exists($hookObj, 'getItemMarkerArrays')) {
						$hookObj->getItemMarkerArrays($this, $templateCode, $code, $markerArray,$subpartArray,$wrappedSubpartArray, $code, $mainMarkerArray, $count);
					}
				}
			}

			$this->paymentshipping->getSubpartArray($subpartArray, $markerArray, $t['basketFrameWork']);
			$this->basket->fe_users->getWrappedSubpartArray($subpartArray, $wrappedSubpartArray, $this->viewTable->conftablename);

			$bFrameWork=$this->pibase->cObj->substituteMarkerArrayCached($t['basketFrameWork'],$markerArray,$subpartArray,$wrappedSubpartArray);

				// substitute the main subpart with the rendered content.
			$out = $this->pibase->cObj->substituteSubpart($bFrameWork, '###ITEM_CATEGORY_AND_ITEMS###', $out);
		}

		return $out;
	} // getView
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tt_products/view/class.tx_ttproducts_basket_view.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tt_products/view/class.tx_ttproducts_basket_view.php']);
}

?>
