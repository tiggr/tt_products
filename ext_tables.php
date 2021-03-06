<?php
if (!defined ('TYPO3_MODE'))	die ('Access denied.');

if (TYPO3_MODE=="BE" || $loadTcaAdditions == TRUE) {

	$tempColumns = Array (
		'tt_products_memoItems' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:'.TT_PRODUCTS_EXT.'/locallang_db.xml:fe_users.tt_products_memoItems',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'max' => '256'
			)
		),
		'tt_products_discount' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:'.TT_PRODUCTS_EXT.'/locallang_db.xml:fe_users.tt_products_discount',
			'config' => Array (
				'type' => 'input',
				'size' => '4',
				'max' => '8',
				'eval' => 'trim,double2',
				'checkbox' => '0',
				'range' => Array (
					'upper' => '1000',
					'lower' => '1'
				),
				'default' => 0
			)
		),
		'tt_products_creditpoints' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:'.TT_PRODUCTS_EXT.'/locallang_db.xml:fe_users.tt_products_creditpoints',
			'config' => Array (
				'type' => 'input',
				'size' => '5',
				'max' => '20',
				'eval' => 'trim,integer',
			)
		),
		'tt_products_vouchercode' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:'.TT_PRODUCTS_EXT.'/locallang_db.xml:fe_users.tt_products_vouchercode',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
				'max' => '256'
			)
		),
		'tt_products_vat' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:'.TT_PRODUCTS_EXT.'/locallang_db.xml:fe_users.tt_products_vat',
			'config' => Array (
				'type' => 'input',
				'size' => '15',
				'max' => '15'
			)
		),
		'tt_products_business_partner' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_business_partner',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_business_partner.I.0', '0'),
					Array('LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_business_partner.I.1', '1'),
				),
				'size' => 1,
				'maxitems' => 1,
			)
		),
		'tt_products_organisation_form' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_organisation_form',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_organisation_form.A1', 'A1'),
					Array('LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_organisation_form.A2', 'A2'),
					Array('LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_organisation_form.A3', 'A3'),
					Array('LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_organisation_form.BH', 'BH'),
					Array('LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_organisation_form.E1', 'E1'),
					Array('LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_organisation_form.E2', 'E2'),
					Array('LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_organisation_form.E3', 'E3'),
					Array('LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_organisation_form.E4', 'E4'),
					Array('LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_organisation_form.G1', 'G1'),
					Array('LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_organisation_form.G2', 'G2'),
					Array('LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_organisation_form.G3', 'G3'),
					Array('LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_organisation_form.G4', 'G4'),
					Array('LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_organisation_form.G5', 'G5'),
					Array('LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_organisation_form.G6', 'G6'),
					Array('LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_organisation_form.G7', 'G7'),
					Array('LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_organisation_form.K2', 'K2'),
					Array('LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_organisation_form.K3', 'K3'),
					Array('LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_organisation_form.KG', 'KG'),
					Array('LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_organisation_form.KO', 'KO'),
					Array('LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_organisation_form.O1', 'O1'),
					Array('LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_organisation_form.P', 'P'),
					Array('LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_organisation_form.S1', 'S1'),
					Array('LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_organisation_form.S2', 'S2'),
					Array('LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_organisation_form.S3', 'S3'),
					Array('LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_organisation_form.U', 'U'),
					Array('LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_organisation_form.V1', 'V1'),
					Array('LLL:EXT:' . TT_PRODUCTS_EXT . '/locallang_db.xml:fe_users.tt_products_organisation_form.Z1', 'Z1'),
				),
				'size' => 1,
				'maxitems' => 1,
			)
		),

	);

	t3lib_div::loadTCA('fe_users');

	t3lib_extMgm::addTCAcolumns('fe_users', $tempColumns);
	t3lib_extMgm::addToAllTCAtypes('fe_users', 'tt_products_creditpoints;;;;1-1-1,tt_products_vouchercode;;;;1-1-1,tt_products_memoItems;;;;1-1-1,tt_products_discount;;;;1-1-1,tt_products_vat;;;;1-1-1,tt_products_business_partner;;;;1-1-1,tt_products_organisation_form;;;;1-1-1');
}


if (!$loadTcaAdditions) {

	t3lib_extMgm::addStaticFile(TT_PRODUCTS_EXT, 'static/old_style/', 'Shop System Old Style');
	t3lib_extMgm::addStaticFile(TT_PRODUCTS_EXT, 'static/css_styled/', 'Shop System CSS Styled');

	//t3lib_extMgm::addStaticFile(TT_PRODUCTS_EXT, 'static/test/', 'Shop System Test');

	$TCA['tt_products'] = Array (
		'ctrl' => Array (
			'title' =>'LLL:EXT:'.TT_PRODUCTS_EXT.'/locallang_db.xml:tt_products',
			'label' => 'title',
			'label_alt' => 'subtitle',
			'default_sortby' => 'ORDER BY title',
			'tstamp' => 'tstamp',
			'prependAtCopy' => 'LLL:EXT:lang/locallang_general.php:LGL.prependAtCopy',
			'crdate' => 'crdate',
			'delete' => 'deleted',
			'enablecolumns' => Array (
				'disabled' => 'hidden',
				'starttime' => 'starttime',
				'endtime' => 'endtime',
				'fe_group' => 'fe_group',
			),
			'thumbnail' => 'image',
			'useColumnsForDefaultValues' => 'category',
			'mainpalette' => 1,
			'dynamicConfigFile' => PATH_BE_ttproducts.'tca.php',
			'iconfile' => PATH_ttproducts_icon_table_rel.'tt_products.gif',
			'dividers2tabs' => '1',
			'transForeignTable' => 'tt_products_language',
			'searchFields' => 'title,subtitle,itemnumber,ean,note,note2,www',
		),
		'feInterface' => Array (
			'fe_admin_fieldList' => 'title,subtitle,itemnumber,price,price2,note,category,address,inStock,tax,weight,bulkily,offer,highlight,directcost,color,size,description,gradings,unit,unit_factor,www,datasheet,special_preparation,image,hidden,starttime,endtime',
		)
	);

	$TCA['tt_products_language'] = Array (
		'ctrl' => Array (
			'title' => 'LLL:EXT:'.TT_PRODUCTS_EXT.'/locallang_db.xml:tt_products_language',
			'label' => 'title',
			'label_alt' => 'subtitle',
			'default_sortby' => 'ORDER BY title',
			'tstamp' => 'tstamp',
			'delete' => 'deleted',
			'enablecolumns' => Array (
				'disabled' => 'hidden',
				'starttime' => 'starttime',
				'endtime' => 'endtime',
				'fe_group' => 'fe_group',
			),
			'prependAtCopy' => 'LLL:EXT:lang/locallang_general.php:LGL.prependAtCopy',
			'crdate' => 'crdate',
	//		'sortby' => 'sorting',
			'iconfile' => PATH_ttproducts_icon_table_rel.'tt_products_language.gif',
			'dynamicConfigFile' => PATH_BE_ttproducts.'tca.php',
			'transOrigPointerField' => 'prod_uid',
			'transOrigPointerTable' => 'tt_products',
			'languageField' => 'sys_language_uid',
			'mainpalette' => 1,
			'searchFields' => 'title,subtitle,itemnumber,ean,note,note2,www',
		),
		'feInterface' => Array (
			'fe_admin_fieldList' => 'title,subtitle,prod_uid,note,unit,www,datasheet,sys_language_uid,hidden,starttime,endtime',
		)
	);

	$TCA['tt_products_mm_graduated_price'] = Array (
		'ctrl' => Array (
			'title' => 'LLL:EXT:'.TT_PRODUCTS_EXT.'/locallang_db.xml:tt_products_mm_graduated_price',
			'label' => 'title',
			'tstamp' => 'tstamp',
			'delete' => 'deleted',
			'enablecolumns' => Array (
				'disabled' => 'hidden'
			),
			'prependAtCopy' => 'LLL:EXT:lang/locallang_general.php:LGL.prependAtCopy',
			'crdate' => 'crdate',
	//		'sortby' => 'sorting',
			'iconfile' => PATH_ttproducts_icon_table_rel.'tt_products_cat.gif',
			'dynamicConfigFile' => PATH_BE_ttproducts.'tca.php'
		),
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden,title',
		)
	);


	$TCA['tt_products_graduated_price'] = Array (
		'ctrl' => Array (
			'title' => 'LLL:EXT:'.TT_PRODUCTS_EXT.'/locallang_db.xml:tt_products_graduated_price',
			'label' => 'title',
			'default_sortby' => 'ORDER BY title',
			'tstamp' => 'tstamp',
			'delete' => 'deleted',
			'prependAtCopy' => 'LLL:EXT:lang/locallang_general.php:LGL.prependAtCopy',
			'crdate' => 'crdate',
	//		'sortby' => 'sorting',
			'delete' => 'deleted',
			'enablecolumns' => Array (
				'disabled' => 'hidden',
				'starttime' => 'starttime',
				'endtime' => 'endtime',
				'fe_group' => 'fe_group',
			),
			'iconfile' => PATH_ttproducts_icon_table_rel.'tt_products_cat.gif',
			'dynamicConfigFile' => PATH_BE_ttproducts.'tca.php',
			'searchFields' => 'title,note',
		),
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden,starttime,endtime,title',
		)
	);


	$TCA['tt_products_cat'] = Array (
		'ctrl' => Array (
			'title' => 'LLL:EXT:'.TT_PRODUCTS_EXT.'/locallang_db.xml:tt_products_cat',
			'label' => 'title',
			'label_alt' => 'subtitle',
			'default_sortby' => 'ORDER BY title',
			'tstamp' => 'tstamp',
			'delete' => 'deleted',
			'enablecolumns' => Array (
				'disabled' => 'hidden',
				'starttime' => 'starttime',
				'endtime' => 'endtime',
				'fe_group' => 'fe_group',
			),
			'thumbnail' => 'image',
			'prependAtCopy' => 'LLL:EXT:lang/locallang_general.php:LGL.prependAtCopy',
			'crdate' => 'crdate',
	//		'sortby' => 'sorting',
			'iconfile' => PATH_ttproducts_icon_table_rel.'tt_products_cat.gif',
			'dynamicConfigFile' => PATH_BE_ttproducts.'tca.php',
			'transForeignTable' => 'tt_products_cat_language',
			'searchFields' => 'title,subtitle,note,note2',
		),
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden,starttime,endtime,title,note,image,email',
		)
	);


	$TCA['tt_products_cat_language'] = Array (
		'ctrl' => Array (
			'title' => 'LLL:EXT:'.TT_PRODUCTS_EXT.'/locallang_db.xml:tt_products_cat_language',
			'label' => 'title',
			'label_alt' => 'subtitle',
			'default_sortby' => 'ORDER BY title',
			'tstamp' => 'tstamp',
			'delete' => 'deleted',
			'enablecolumns' => Array (
				'disabled' => 'hidden',
				'starttime' => 'starttime',
				'endtime' => 'endtime',
				'fe_group' => 'fe_group',
			),
			'prependAtCopy' => 'LLL:EXT:lang/locallang_general.php:LGL.prependAtCopy',
			'crdate' => 'crdate',
	//		'sortby' => 'sorting',
			'iconfile' => PATH_ttproducts_icon_table_rel.'tt_products_cat_language.gif',
			'dynamicConfigFile' => PATH_BE_ttproducts.'tca.php',
			'transOrigPointerField' => 'cat_uid',
			'transOrigPointerTable' => 'tt_products_cat',
			'languageField' => 'sys_language_uid',
			'mainpalette' => 1,
			'searchFields' => 'title,subtitle,note,note2',
		),
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden,starttime,endtime,cat_uid,sys_language_uid,title',
		)
	);


	$TCA['tt_products_texts'] = Array (
		'ctrl' => Array (
			'title' => 'LLL:EXT:'.TT_PRODUCTS_EXT.'/locallang_db.xml:tt_products_texts',
			'label' => 'title',
			'default_sortby' => 'ORDER BY title',
			'tstamp' => 'tstamp',
			'delete' => 'deleted',
			'enablecolumns' => Array (
				'disabled' => 'hidden',
				'starttime' => 'starttime',
				'endtime' => 'endtime',
				'fe_group' => 'fe_group',
			),
			'prependAtCopy' => 'LLL:EXT:lang/locallang_general.php:LGL.prependAtCopy',
			'crdate' => 'crdate',
	//		'sortby' => 'sorting',
			'iconfile' => PATH_ttproducts_icon_table_rel.'tt_products_texts.gif',
			'dynamicConfigFile' => PATH_BE_ttproducts.'tca.php',
			'searchFields' => 'title,marker,note',
		),
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden,starttime,endtime,title,note',
		)
	);


	$TCA['tt_products_texts_language'] = Array (
		'ctrl' => Array (
			'title' => 'LLL:EXT:'.TT_PRODUCTS_EXT.'/locallang_db.xml:tt_products_texts_language',
			'label' => 'title',
			'default_sortby' => 'ORDER BY title',
			'tstamp' => 'tstamp',
			'delete' => 'deleted',
			'enablecolumns' => Array (
				'disabled' => 'hidden',
				'starttime' => 'starttime',
				'endtime' => 'endtime',
				'fe_group' => 'fe_group',
			),
			'prependAtCopy' => 'LLL:EXT:lang/locallang_general.php:LGL.prependAtCopy',
			'crdate' => 'crdate',
	//		'sortby' => 'sorting',
			'iconfile' => PATH_ttproducts_icon_table_rel.'tt_products_texts_language.gif',
			'dynamicConfigFile' => PATH_BE_ttproducts.'tca.php',
			'transOrigPointerField' => 'text_uid',
			'transOrigPointerTable' => 'tt_products_texts',
			'languageField' => 'sys_language_uid',
			'mainpalette' => 1,
			'searchFields' => 'title,note',
		),
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden,starttime,endtime,cat_uid, sys_language_uid,title',
		)
	);


	$TCA['tt_products_articles'] = Array (
		'ctrl' => Array (
			'title' => 'LLL:EXT:'.TT_PRODUCTS_EXT.'/locallang_db.xml:tt_products_articles',
			'label' => 'title',
			'label_alt' => 'subtitle',
			'default_sortby' => 'ORDER BY title',
			'tstamp' => 'tstamp',
			'delete' => 'deleted',
			'enablecolumns' => Array (
				'disabled' => 'hidden',
				'starttime' => 'starttime',
				'endtime' => 'endtime',
				'fe_group' => 'fe_group',
			),
			'thumbnail' => 'image',
			'prependAtCopy' => 'LLL:EXT:lang/locallang_general.php:LGL.prependAtCopy',
			'crdate' => 'crdate',
	//		'sortby' => 'sorting',
			'iconfile' => PATH_ttproducts_icon_table_rel.'tt_products_articles.gif',
			'dividers2tabs' => '1',
			'dynamicConfigFile' => PATH_BE_ttproducts.'tca.php',
			'transForeignTable' => 'tt_products_articles_language',
			'searchFields' => 'title,subtitle,itemnumber,keyword,note,note2',
		),
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden,starttime,endtime,cat_uid, title',
		)
	);


	$TCA['tt_products_articles_language'] = Array (
		'ctrl' => Array (
			'title' => 'LLL:EXT:'.TT_PRODUCTS_EXT.'/locallang_db.xml:tt_products_articles_language',
			'label' => 'title',
			'label_alt' => 'subtitle',
			'default_sortby' => 'ORDER BY title',
			'tstamp' => 'tstamp',
			'delete' => 'deleted',
			'enablecolumns' => Array (
				'disabled' => 'hidden',
				'starttime' => 'starttime',
				'endtime' => 'endtime',
				'fe_group' => 'fe_group',
			),
			'prependAtCopy' => 'LLL:EXT:lang/locallang_general.php:LGL.prependAtCopy',
			'crdate' => 'crdate',
	//		'sortby' => 'sorting',
			'iconfile' => PATH_ttproducts_icon_table_rel.'tt_products_articles_language.gif',
			'dynamicConfigFile' => PATH_BE_ttproducts.'tca.php',
			'transOrigPointerField' => 'article_uid',
			'transOrigPointerTable' => 'tt_products_articles',
			'languageField' => 'sys_language_uid',
			'mainpalette' => 1,
			'searchFields' => 'title,subtitle,itemnumber,keyword,note,note2',
		),
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden,starttime,endtime,article_uid, title',
		)
	);


	$TCA['tt_products_emails'] = Array (
		'ctrl' => Array (
			'title' => 'LLL:EXT:'.TT_PRODUCTS_EXT.'/locallang_db.xml:tt_products_emails',
			'label' => 'name',
			'default_sortby' => 'ORDER BY name',
			'tstamp' => 'tstamp',
			'delete' => 'deleted',
			'enablecolumns' => Array (
				'disabled' => 'hidden',
				'starttime' => 'starttime',
				'endtime' => 'endtime',
				'fe_group' => 'fe_group',
			),
			'prependAtCopy' => 'LLL:EXT:lang/locallang_general.php:LGL.prependAtCopy',
			'crdate' => 'crdate',
	//		'sortby' => 'sorting',
			'mainpalette' => 1,
			'iconfile' => PATH_ttproducts_icon_table_rel.'tt_products_emails.gif',
			'dynamicConfigFile' => PATH_BE_ttproducts.'tca.php',
			'searchFields' => 'name,email',
		),
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden,starttime,endtime',
		)
	);


	$TCA['sys_products_accounts'] = Array (
		'ctrl' => Array (
			'title' => 'LLL:EXT:'.TT_PRODUCTS_EXT.'/locallang_db.xml:sys_products_accounts',
			'label' => 'ac_number',
			'label_userFunc' => 'tx_ttproducts_table_label->getLabel',
			'default_sortby' => 'ORDER BY ac_number',
			'tstamp' => 'tstamp',
			'prependAtCopy' => 'LLL:EXT:lang/locallang_general.php:LGL.prependAtCopy',
			'crdate' => 'crdate',
			'iconfile' => PATH_ttproducts_icon_table_rel.'sys_products_accounts.gif',
			'dynamicConfigFile' => PATH_BE_ttproducts.'tca.php',
			'searchFields' => 'owner_name,ac_number',
		),
		'feInterface' => Array (
			'fe_admin_fieldList' => '',
		)
	);


	$TCA['sys_products_cards'] = Array (
		'ctrl' => Array (
			'title' => 'LLL:EXT:'.TT_PRODUCTS_EXT.'/locallang_db.xml:sys_products_cards',
			'label' => 'cc_number',
			'default_sortby' => 'ORDER BY cc_number',
			'tstamp' => 'tstamp',
			'prependAtCopy' => 'LLL:EXT:lang/locallang_general.php:LGL.prependAtCopy',
			'crdate' => 'crdate',
			'iconfile' => PATH_ttproducts_icon_table_rel.'sys_products_cards.gif',
			'dynamicConfigFile' => PATH_BE_ttproducts.'tca.php',
			'searchFields' => 'owner_name,cc_number',
		),
		'feInterface' => Array (
			'fe_admin_fieldList' => '',
		)
	);


	$TCA['sys_products_orders'] = Array (
		'ctrl' => Array (
			'title' => 'LLL:EXT:'.TT_PRODUCTS_EXT.'/locallang_db.xml:sys_products_orders',
			'label' => 'name',
			'label_alt' => 'last_name',
			'default_sortby' => 'ORDER BY name',
			'tstamp' => 'tstamp',
			'delete' => 'deleted',
			'enablecolumns' => Array (
				'disabled' => 'hidden',
			),
			'prependAtCopy' => 'LLL:EXT:lang/locallang_general.php:LGL.prependAtCopy',
			'crdate' => 'crdate',
			'mainpalette' => 1,
			'iconfile' => PATH_ttproducts_icon_table_rel.'sys_products_orders.gif',
			'dynamicConfigFile' => PATH_BE_ttproducts.'tca.php',
			'searchFields' => 'uid,name,first_name,last_name,vat_id,zip,city,telephone,email,giftcode,bill_no',
		),
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden',
		)
	);

	t3lib_div::loadTCA('tt_content');

	if (
		!isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][TT_PRODUCTS_EXT]['useFlexforms']) ||
		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][TT_PRODUCTS_EXT]['useFlexforms']==1
	)	{
		$TCA['tt_content']['types']['list']['subtypes_excludelist']['5']='layout,select_key';
		$TCA['tt_content']['types']['list']['subtypes_addlist']['5']='pi_flexform';
		t3lib_extMgm::addPiFlexFormValue('5', 'FILE:EXT:'.TT_PRODUCTS_EXT.'/flexform_ds_pi1.xml');
	}

	t3lib_extMgm::addPlugin(Array('LLL:EXT:'.TT_PRODUCTS_EXT.'/locallang_db.xml:tt_content.list_type_pi1','5'),'list_type');

	t3lib_extMgm::addToInsertRecords('tt_products');
	t3lib_extMgm::addToInsertRecords('tt_products_articles');
	t3lib_extMgm::addToInsertRecords('tt_products_articles_language');
	t3lib_extMgm::addToInsertRecords('tt_products_cat');
	t3lib_extMgm::addToInsertRecords('tt_products_cat_language');
	t3lib_extMgm::addToInsertRecords('tt_products_emails');
	t3lib_extMgm::addToInsertRecords('tt_products_graduated_price');
	t3lib_extMgm::addToInsertRecords('tt_products_language');
	t3lib_extMgm::addToInsertRecords('tt_products_mm_graduated_price');
	t3lib_extMgm::addToInsertRecords('tt_products_texts');
	t3lib_extMgm::addToInsertRecords('tt_products_texts_language');

	t3lib_extMgm::allowTableOnStandardPages('tt_products');
	t3lib_extMgm::allowTableOnStandardPages('tt_products_articles');
	t3lib_extMgm::allowTableOnStandardPages('tt_products_articles_language');
	t3lib_extMgm::allowTableOnStandardPages('tt_products_cat');
	t3lib_extMgm::allowTableOnStandardPages('tt_products_cat_language');
	t3lib_extMgm::allowTableOnStandardPages('tt_products_graduated_price');
	t3lib_extMgm::allowTableOnStandardPages('tt_products_emails');
	t3lib_extMgm::allowTableOnStandardPages('tt_products_language');
	t3lib_extMgm::allowTableOnStandardPages('tt_products_mm_graduated_price');
	t3lib_extMgm::allowTableOnStandardPages('tt_products_texts');
	t3lib_extMgm::allowTableOnStandardPages('tt_products_texts_language');
	t3lib_extMgm::allowTableOnStandardPages('sys_products_accounts');
	t3lib_extMgm::allowTableOnStandardPages('sys_products_cards');
	t3lib_extMgm::allowTableOnStandardPages('sys_products_orders');


	//t3lib_extMgm::addToInsertRecords('tt_products');

	if (TYPO3_MODE=='BE')	{
		$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_ttproducts_wizicon'] = PATH_BE_ttproducts.'class.tx_ttproducts_wizicon.php';
	}

	t3lib_extMgm::addLLrefForTCAdescr('tt_products', 'EXT:' . TT_PRODUCTS_EXT . '/locallang_csh_ttprod.php');
	t3lib_extMgm::addLLrefForTCAdescr('tt_products_cat', 'EXT:' . TT_PRODUCTS_EXT . '/locallang_csh_ttprodc.php');
	t3lib_extMgm::addLLrefForTCAdescr('tt_products_articles','EXT:' . TT_PRODUCTS_EXT . '/locallang_csh_ttproda.php');
	t3lib_extMgm::addLLrefForTCAdescr('tt_products_emails','EXT:' . TT_PRODUCTS_EXT . '/locallang_csh_ttprode.php');
	t3lib_extMgm::addLLrefForTCAdescr('tt_products_texts','EXT:' . TT_PRODUCTS_EXT . '/locallang_csh_ttprodt.php');
	t3lib_extMgm::addLLrefForTCAdescr('sys_products_accounts','EXT:' . TT_PRODUCTS_EXT . '/locallang_csh_ttprodac.php');
	t3lib_extMgm::addLLrefForTCAdescr('sys_products_cards','EXT:' . TT_PRODUCTS_EXT . '/locallang_csh_ttprodca.php');
	t3lib_extMgm::addLLrefForTCAdescr('sys_products_orders','EXT:' . TT_PRODUCTS_EXT . '/locallang_csh_ttprodo.php');

	$productsTableArray = array('tt_products', 'tt_products_language', 'tt_products_related_products_products_mm', 'tt_products_accessory_products_products_mm', 'tt_products_products_dam_mm', 'tt_products_products_note_pages_mm', 'tt_products_cat', 'tt_products_cat_language', 'tt_products_articles', 'tt_products_articles_language', 'tt_products_gifts', 'tt_products_gifts_articles_mm', 'tt_products_emails', 'tt_products_texts', 'tt_products_texts_language', 'tt_products_mm_graduated_price', 'tt_products_graduated_price', 'sys_products_accounts', 'sys_products_cards', 'sys_products_orders', 'sys_products_orders_mm_tt_products', 'fe_users', 'pages_language_overlay');

	$orderBySortingTablesArray = t3lib_div::trimExplode(',',$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][TT_PRODUCTS_EXT]['orderBySortingTables']);

	if (isset($orderBySortingTablesArray) && is_array($orderBySortingTablesArray))	{
		foreach ($orderBySortingTablesArray as $k => $productTable)	{
			if (in_array($productTable, $productsTableArray))	{
				$TCA[$productTable]['ctrl']['sortby'] = 'sorting';
			}
		}
	}
}

?>