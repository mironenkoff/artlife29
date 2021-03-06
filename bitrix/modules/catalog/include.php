<?
IncludeModuleLangFile(__FILE__);

define("CATALOG_PATH2EXPORTS", "/bitrix/php_interface/include/catalog_export/");
define("CATALOG_PATH2EXPORTS_DEF", "/bitrix/modules/catalog/load/");
define('CATALOG_DEFAULT_EXPORT_PATH', '/bitrix/catalog_export/');

define("CATALOG_PATH2IMPORTS", "/bitrix/php_interface/include/catalog_import/");
define("CATALOG_PATH2IMPORTS_DEF", "/bitrix/modules/catalog/load_import/");

define("YANDEX_SKU_EXPORT_ALL",1);
define("YANDEX_SKU_EXPORT_MIN_PRICE",2);
define("YANDEX_SKU_EXPORT_PROP",3);
define("YANDEX_SKU_TEMPLATE_PRODUCT",1);
define("YANDEX_SKU_TEMPLATE_OFFERS",2);
define("YANDEX_SKU_TEMPLATE_CUSTOM",3);

define("EXPORT_VERSION_OLD", 1);
define("EXPORT_VERSION_NEW", 2);

define('DISCOUNT_TYPE_STANDART',0);
define('DISCOUNT_TYPE_SAVE',1);

define("CATALOG_DISCOUNT_OLD_VERSION", 1);
define("CATALOG_DISCOUNT_NEW_VERSION", 2);

define('BX_CATALOG_FILENAME_REG','/[^a-zA-Z0-9\s!#\$%&\(\)\[\]\{\}+\.;=@\^_\~\/\\\\\-]/i');

// Constants for the store control: //
define('CONTRACTOR_INDIVIDUAL', 1);
define('CONTRACTOR_JURIDICAL', 2);
define('DOC_ARRIVAL', 'A');
define('DOC_MOVING', 'M');
define('DOC_RETURNS', 'R');
define('DOC_DEDUCT', 'D');
define('DOC_INVENTORY', 'I');
//**********************************//

global $APPLICATION;

if (defined('CATALOG_GLOBAL_VARS') && 'Y' == CATALOG_GLOBAL_VARS)
{
	global $CATALOG_CATALOG_CACHE;
	$CATALOG_CATALOG_CACHE = null;

	global $CATALOG_ONETIME_COUPONS_ORDER;
	$CATALOG_ONETIME_COUPONS_ORDER = null;

	global $CATALOG_PRODUCT_CACHE;
	$CATALOG_PRODUCT_CACHE = null;

	global $MAIN_EXTRA_LIST_CACHE;
	$MAIN_EXTRA_LIST_CACHE = null;
}
$GLOBALS['CATALOG_ONETIME_COUPONS_BASKET'] = null;

if (!CModule::IncludeModule("iblock"))
{
	$APPLICATION->ThrowException(GetMessage('CAT_ERROR_IBLOCK_NOT_INSTALLED'));
	return false;
}

if (!CModule::IncludeModule("currency"))
{
	$APPLICATION->ThrowException(GetMessage('CAT_ERROR_CURRENCY_NOT_INSTALLED'));
	return false;
}

$arTreeDescr = array(
	'js' => '/bitrix/js/catalog/core_tree.js',
	'css' => '/bitrix/panel/catalog/catalog_cond.css',
	'lang' => '/bitrix/modules/catalog/lang/'.LANGUAGE_ID.'/js_core_tree.php',
);
CJSCore::RegisterExt('core_condtree', $arTreeDescr);

global $CATALOG_TIME_PERIOD_TYPES;

global $DB;
$strDBType = strtolower($DB->type);

$CATALOG_TIME_PERIOD_TYPES = array(
		"H" => GetMessage("I_PERIOD_HOUR"),
		"D" => GetMessage("I_PERIOD_DAY"),
		"W" => GetMessage("I_PERIOD_WEEK"),
		"M" => GetMessage("I_PERIOD_MONTH"),
		"Q" => GetMessage("I_PERIOD_QUART"),
		"S" => GetMessage("I_PERIOD_SEMIYEAR"),
		"Y" => GetMessage("I_PERIOD_YEAR")
	);

define("CATALOG_VALUE_PRECISION", 2);
define("CATALOG_CACHE_DEFAULT_TIME", 10800);

CModule::AddAutoloadClasses(
	"catalog",
	array(
		"CCatalog" => $strDBType."/catalog.php",
		"CCatalogGroup" => $strDBType."/cataloggroup.php",
		"CExtra" => $strDBType."/extra.php",
		"CPrice" => $strDBType."/price.php",
		"CCatalogProduct" => $strDBType."/product.php",
		"CCatalogProductGroups" => $strDBType."/product_group.php",
		"CCatalogLoad" => $strDBType."/catalog_load.php",
		"CCatalogExport" => $strDBType."/catalog_export.php",
		"CCatalogImport" => $strDBType."/catalog_import.php",
		"CCatalogDiscount" => $strDBType."/discount.php",
		"CCatalogDiscountCoupon" => $strDBType."/discount_coupon.php",
		"CCatalogVat" => $strDBType."/vat.php",
		"CCatalogEvent" => "general/catalog_event.php",
		"CCatalogSKU" => $strDBType."/catalog_sku.php",
		"CCatalogDiscountSave" => $strDBType."/discount_save.php",
		"CCatalogStore" => $strDBType."/store.php",
		"CCatalogStoreProduct" => $strDBType."/store_product.php",
		"CCatalogAdmin" => "general/admin.php",
		"CGlobalCondCtrl" => "general/catalog_cond.php",
		"CGlobalCondCtrlComplex" => "general/catalog_cond.php",
		"CGlobalCondCtrlGroup" => "general/catalog_cond.php",
		"CGlobalCondTree" => "general/catalog_cond.php",
		"CCatalogCondCtrl" => "general/catalog_cond.php",
		"CCatalogCondCtrlComplex" => "general/catalog_cond.php",
		"CCatalogCondCtrlGroup" => "general/catalog_cond.php",
		"CCatalogCondCtrlIBlockFields" => "general/catalog_cond.php",
		"CCatalogCondCtrlIBlockProps" => "general/catalog_cond.php",
		"CCatalogCondTree" => "general/catalog_cond.php",
		"CCatalogCondCtrlBasketProductFields" => "general/sale_cond.php",
		"CCatalogCondCtrlBasketProductProps" => "general/sale_cond.php",
		"CCatalogActionCtrlBasketProductFields" => "general/sale_act.php",
		"CCatalogActionCtrlBasketProductProps" => "general/sale_act.php",
		"CCatalogDiscountConvert" => "general/discount_convert.php",
		"CCatalogDiscountConvertTmp" => $strDBType."/discount_convert.php",
		"CCatalogProductProvider" => "general/product_provider.php",
		"CCatalogStoreBarCode" => $strDBType."/store_barcode.php",
		"CCatalogContractor" => $strDBType."/contractor.php",
		"CCatalogArrivalDocs" => $strDBType."/store_docs_type.php",
		"CCatalogMovingDocs" => $strDBType."/store_docs_type.php",
		"CCatalogDeductDocs" => $strDBType."/store_docs_type.php",
		"CCatalogReturnsDocs" => $strDBType."/store_docs_type.php",
		"CCatalogUnReservedDocs" => $strDBType."/store_docs_type.php",
		"CCatalogDocs" => $strDBType."/store_docs.php",
		"CCatalogStoreControlUtil" => "general/store_utility.php",
		"CCatalogStoreDocsElement" => $strDBType."/store_docs_element.php",
		"CCatalogStoreDocsBarcode" => $strDBType."/store_docs_barcode.php",
		"Bitrix\\Catalog\\StoreTable" => "lib/store.php",
		"CCatalogIBlockParameters" => "general/comp_parameters.php",
	)
);

/*************************************************************/
global $arCatalogAvailProdFields, $defCatalogAvailProdFields, $arCatalogAvailGroupFields, $defCatalogAvailGroupFields, $defCatalogAvailCurrencies, $arCatalogAvailPriceFields, $defCatalogAvailPriceFields, $arCatalogAvailValueFields, $defCatalogAvailValueFields;

$arCatalogAvailProdFields = array(
		array("value"=>"IE_XML_ID", "field"=>"XML_ID", "important"=>"Y", "name"=>GetMessage("CATI_FI_UNIXML_EXT")." (B_IBLOCK_ELEMENT.XML_ID)"),
		array("value"=>"IE_NAME", "field"=>"NAME", "important"=>"Y", "name"=>GetMessage("CATI_FI_NAME")." (B_IBLOCK_ELEMENT.NAME)"),
		array("value"=>"IE_ACTIVE", "field"=>"ACTIVE", "important"=>"N", "name"=>GetMessage("CATI_FI_ACTIV")." (B_IBLOCK_ELEMENT.ACTIVE)"),
		array("value"=>"IE_ACTIVE_FROM", "field"=>"ACTIVE_FROM", "important"=>"N", "name"=>GetMessage("CATI_FI_ACTIVFROM")." (B_IBLOCK_ELEMENT.ACTIVE_FROM)"),
		array("value"=>"IE_ACTIVE_TO", "field"=>"ACTIVE_TO", "important"=>"N", "name"=>GetMessage("CATI_FI_ACTIVTO")." (B_IBLOCK_ELEMENT.ACTIVE_TO)"),
		array("value"=>"IE_SORT", "field"=>"SORT", "important"=>"N", "name"=>GetMessage("CATI_FI_SORT_EXT")." (B_IBLOCK_ELEMENT.SORT)"),
		array("value"=>"IE_PREVIEW_PICTURE", "field"=>"PREVIEW_PICTURE", "important"=>"N", "name"=>GetMessage("CATI_FI_CATIMG_EXT")." (B_IBLOCK_ELEMENT.PREVIEW_PICTURE)"),
		array("value"=>"IE_PREVIEW_TEXT", "field"=>"PREVIEW_TEXT", "important"=>"N", "name"=>GetMessage("CATI_FI_CATDESCR_EXT")." (B_IBLOCK_ELEMENT.PREVIEW_TEXT)"),
		array("value"=>"IE_PREVIEW_TEXT_TYPE", "field"=>"PREVIEW_TEXT_TYPE", "important"=>"N", "name"=>GetMessage("CATI_FI_CATDESCRTYPE_EXT")." (B_IBLOCK_ELEMENT.PREVIEW_TEXT_TYPE)"),
		array("value"=>"IE_DETAIL_PICTURE", "field"=>"DETAIL_PICTURE", "important"=>"N", "name"=>GetMessage("CATI_FI_DETIMG_EXT")." (B_IBLOCK_ELEMENT.DETAIL_PICTURE)"),
		array("value"=>"IE_DETAIL_TEXT", "field"=>"DETAIL_TEXT", "important"=>"N", "name"=>GetMessage("CATI_FI_DETDESCR_EXT")." (B_IBLOCK_ELEMENT.DETAIL_TEXT)"),
		array("value"=>"IE_DETAIL_TEXT_TYPE", "field"=>"DETAIL_TEXT_TYPE", "important"=>"N", "name"=>GetMessage("CATI_FI_DETDESCRTYPE_EXT")." (B_IBLOCK_ELEMENT.DETAIL_TEXT_TYPE)"),
		array("value"=>"IE_CODE", "field"=>"CODE", "important"=>"N", "name"=>GetMessage("CATI_FI_CODE_EXT")." (B_IBLOCK_ELEMENT.CODE)"),
		array("value"=>"IE_TAGS", "field"=>"TAGS", "important"=>"N", "name"=>GetMessage("CATI_FI_TAGS")." (B_IBLOCK_ELEMENT.TAGS)"),
		array("value"=>"IE_ID", "field"=>"ID", "important"=>"N", "name"=>GetMessage("CATI_FI_ID")." (B_IBLOCK_ELEMENT.ID)")
	);
$defCatalogAvailProdFields = "IE_XML_ID,IE_NAME,IE_PREVIEW_TEXT,IE_DETAIL_TEXT";

$arCatalogAvailPriceFields = array(
		array("value"=>"CP_QUANTITY", "field"=>"QUANTITY", "important"=>"N", "name"=>GetMessage("CATI_FI_QUANT")." (B_CATALOG_PRODUCT.QUANTITY)"),
		array("value"=>"CP_QUANTITY_TRACE", "field"=>"QUANTITY_TRACE", "important"=>"N", "name"=>GetMessage("CATI_FI_QUANTITY_TRACE")." (B_CATALOG_PRODUCT.QUANTITY_TRACE)"),
		array("value"=>"CP_CAN_BUY_ZERO", "field"=>"CAN_BUY_ZERO", "important"=>"N", "name"=>GetMessage("CATI_FI_CAN_BUY_ZERO")." (B_CATALOG_PRODUCT.CAN_BUY_ZERO)"),
		array("value"=>"CP_NEGATIVE_AMOUNT_TRACE", "field"=>"NEGATIVE_AMOUNT_TRACE", "important"=>"N", "name"=>GetMessage("CATI_FI_NEGATIVE_AMOUNT_TRACE")." (B_CATALOG_PRODUCT.NEGATIVE_AMOUNT_TRACE)"),
		array("value"=>"CP_WEIGHT", "field"=>"WEIGHT", "important"=>"N", "name"=>GetMessage("CATI_FI_WEIGHT")." (B_CATALOG_PRODUCT.WEIGHT)"),
		array("value"=>"CP_PURCHASING_PRICE", "field"=>"PURCHASING_PRICE", "important"=>"N", "name"=>GetMessage("CATI_FI_PURCHASING_PRICE")." (B_CATALOG_PRODUCT.PURCHASING_PRICE)"),
		array("value"=>"CP_PURCHASING_CURRENCY", "field"=>"PURCHASING_CURRENCY", "important"=>"N", "name"=>GetMessage("CATI_FI_PURCHASING_CURRENCY")." (B_CATALOG_PRODUCT.PURCHASING_CURRENCY)"),
		array("value"=>"CP_PRICE_TYPE", "field"=>"PRICE_TYPE", "important"=>"N", "name"=>GetMessage("I_PAY_TYPE")." (B_CATALOG_PRODUCT.PRICE_TYPE)"),
		array("value"=>"CP_RECUR_SCHEME_LENGTH", "field"=>"RECUR_SCHEME_LENGTH", "important"=>"N", "name"=>GetMessage("I_PAY_PERIOD_LENGTH")." (B_CATALOG_PRODUCT.RECUR_SCHEME_LENGTH)"),
		array("value"=>"CP_RECUR_SCHEME_TYPE", "field"=>"RECUR_SCHEME_TYPE", "important"=>"N", "name"=>GetMessage("I_PAY_PERIOD_TYPE")." (B_CATALOG_PRODUCT.RECUR_SCHEME_TYPE)"),
		array("value"=>"CP_TRIAL_PRICE_ID", "field"=>"TRIAL_PRICE_ID", "important"=>"N", "name"=>GetMessage("I_TRIAL_FOR")." (B_CATALOG_PRODUCT.TRIAL_PRICE_ID)"),
		array("value"=>"CP_WITHOUT_ORDER", "field"=>"WITHOUT_ORDER", "important"=>"N", "name"=>GetMessage("I_WITHOUT_ORDER")." (B_CATALOG_PRODUCT.WITHOUT_ORDER)"),
		array("value"=>"CP_VAT_ID", "field"=>"VAT_ID", "important"=>"N", "name"=>GetMessage("I_VAT_ID")." (B_CATALOG_PRODUCT.VAT_ID)"),
		array("value"=>"CP_VAT_INCLUDED", "field"=>"VAT_INCLUDED", "important"=>"N", "name"=>GetMessage("I_VAT_INCLUDED")." (B_CATALOG_PRODUCT.VAT_INCLUDED)"),
	);
$defCatalogAvailPriceFields = "CP_QUANTITY,CP_WEIGHT";

$arCatalogAvailValueFields = array(
		array("value"=>"CV_PRICE", "field"=>"PRICE", "important"=>"N", "name"=>GetMessage("I_NAME_PRICE")." (B_CATALOG_PRICE.PRICE)"),
		array("value"=>"CV_CURRENCY", "field"=>"CURRENCY", "important"=>"N", "name"=>GetMessage("I_NAME_CURRENCY")." (B_CATALOG_PRICE.CURRENCY)"),
		array("value"=>"CV_EXTRA_ID", "field"=>"EXTRA_ID", "important"=>"N", "name"=>GetMessage("I_NAME_EXTRA_ID")." (B_CATALOG_PRICE.EXTRA_ID)"),
	);
$defCatalogAvailValueFields = "CV_PRICE,CV_CURRENCY"; // CV_QUANTITY_FROM,CV_QUANTITY_TO,

$arCatalogAvailQuantityFields = array(
		array("value"=>"CV_QUANTITY_FROM", "field"=>"QUANTITY_FROM", "important"=>"N", "name"=>GetMessage("I_NAME_QUANTITY_FROM")." (B_CATALOG_PRICE.QUANTITY_FROM)"),
		array("value"=>"CV_QUANTITY_TO", "field"=>"QUANTITY_TO", "important"=>"N", "name"=>GetMessage("I_NAME_QUANTITY_TO")." (B_CATALOG_PRICE.QUANTITY_TO)"),
	);
$defCatalogAvailQuantityFields = "CV_QUANTITY_FROM,CV_QUANTITY_TO";

$arCatalogAvailGroupFields = array(
		array("value"=>"IC_XML_ID", "field"=>"XML_ID", "important"=>"Y", "name"=>GetMessage("CATI_FG_UNIXML_EXT")." (B_IBLOCK_SECTION.XML_ID)"),
		array("value"=>"IC_GROUP", "field"=>"NAME", "important"=>"Y", "name"=>GetMessage("CATI_FG_NAME")." (B_IBLOCK_SECTION.NAME)"),
		array("value"=>"IC_ACTIVE", "field"=>"ACTIVE", "important"=>"N", "name"=>GetMessage("CATI_FG_ACTIV")." (B_IBLOCK_SECTION.ACTIVE)"),
		array("value"=>"IC_SORT", "field"=>"SORT", "important"=>"N", "name"=>GetMessage("CATI_FG_SORT_EXT")." (B_IBLOCK_SECTION.SORT)"),
		array("value"=>"IC_DESCRIPTION", "field"=>"DESCRIPTION", "important"=>"N", "name"=>GetMessage("CATI_FG_DESCR")." (B_IBLOCK_SECTION.DESCRIPTION)"),
		array("value"=>"IC_DESCRIPTION_TYPE", "field"=>"DESCRIPTION_TYPE", "important"=>"N", "name"=>GetMessage("CATI_FG_DESCRTYPE")." (B_IBLOCK_SECTION.DESCRIPTION_TYPE)"),
		array("value"=>"IC_CODE", "field"=>"CODE", "important"=>"N", "name"=>GetMessage("CATI_FG_CODE_EXT2")." (B_IBLOCK_SECTION.CODE)"),
		array("value"=>"IC_PICTURE", "field" => "PICTURE", "important" => "N", "name" => GetMessage("CATI_FG_PICTURE")." (B_IBLOCK_SECTION.PICTURE)"),
		array("value"=>"IC_DETAIL_PICTURE", "field" => "DETAIL_PICTURE", "important" => "N", "name" => GetMessage("CATI_FG_DETAIL_PICTURE")." (B_IBLOCK_SECTION.DETAIL_PICTURE)"),
	);
$defCatalogAvailGroupFields = "IC_GROUP";

$defCatalogAvailCurrencies = "USD";

/*************************************************************/
function GetCatalogGroups($by = "SORT", $order = "ASC")
{
	$res = CCatalogGroup::GetList(array($by => $order));
	return $res;
}

function GetCatalogGroup($CATALOG_GROUP_ID)
{
	$CATALOG_GROUP_ID = intval($CATALOG_GROUP_ID);
	return CCatalogGroup::GetByID($CATALOG_GROUP_ID);
}

function GetCatalogGroupName($CATALOG_GROUP_ID)
{
	$rn = GetCatalogGroup($CATALOG_GROUP_ID);
	return $rn["NAME_LANG"];
}

function GetCatalogProduct($PRODUCT_ID)
{
	$PRODUCT_ID = intval($PRODUCT_ID);
	return CCatalogProduct::GetByID($PRODUCT_ID);
}

function GetCatalogProductEx($PRODUCT_ID, $boolAllValues = false)
{
	$PRODUCT_ID = intval($PRODUCT_ID);
	return CCatalogProduct::GetByIDEx($PRODUCT_ID, $boolAllValues);
}

function GetCatalogProductPrice($PRODUCT_ID, $CATALOG_GROUP_ID)
{
	$PRODUCT_ID = intval($PRODUCT_ID);
	$CATALOG_GROUP_ID = intval($CATALOG_GROUP_ID);

	$db_res = CPrice::GetList(($by="CATALOG_GROUP_ID"), ($order="ASC"), Array("PRODUCT_ID"=>$PRODUCT_ID, "CATALOG_GROUP_ID"=>$CATALOG_GROUP_ID));

	if ($res = $db_res->Fetch())
		return $res;

	return false;
}

function GetCatalogProductPriceList($PRODUCT_ID, $by = "SORT", $order = "ASC")
{
	$PRODUCT_ID = intval($PRODUCT_ID);

	$db_res = CPrice::GetList(
			array($by => $order),
			array("PRODUCT_ID" => $PRODUCT_ID)
		);

	$arPrice = array();
	while ($res = $db_res->Fetch())
	{
		$arPrice[] = $res;
	}

	return $arPrice;
}

function GetCatalogProductTable($IBLOCK, $SECT_ID=false, $arOrder=Array("sort"=>"asc"), $cnt=0)
{
	return false;
	$arFilter = Array("IBLOCK_ID"=>intval($IBLOCK), "ACTIVE"=>"Y", "ACTIVE_DATE"=>"Y");
	if ($SECT_ID!==false)
		$arFilter["SECTION_ID"]=intval($SECT_ID);

	$res = CCatalogProduct::GetListEx($arOrder, $arFilter);
	$dbr = new CIBlockResult($res->result);
	if ($cnt>0)
		$dbr->NavStart($cnt);

	return $dbr;
}

function FormatCurrency($fSum, $strCurrency)
{
	return CurrencyFormat($fSum, $strCurrency);
}

function CatalogBasketCallback($productID, $quantity = 0, $renewal = "N", $intUserID = 0, $strSiteID = false)
{
	global $USER;
	global $APPLICATION;

	global $CATALOG_ONETIME_COUPONS_BASKET;

	$productID = intval($productID);
	$quantity = doubleval($quantity);
	$renewal = (($renewal == "Y") ? "Y" : "N");
	$intUserID = intval($intUserID);
	if (0 > $intUserID)
		$intUserID = 0;

	$arResult = array();

	static $arUserCache = array();
	if (0 < $intUserID)
	{
		if (!array_key_exists($intUserID,$arUserCache))
		{
			$rsUsers = CUser::GetList(($by = 'ID'),($order = 'DESC'),array("ID_EQUAL_EXACT"=>$intUserID),array('FIELDS' => array('ID')));
			if ($arUser = $rsUsers->Fetch())
			{
				$arUserCache[$arUser['ID']] = CUser::GetUserGroup($arUser['ID']);
			}
			else
			{
				$intUserID = 0;
				return $arResult;
			}
		}

		$dbIBlockElement = CIBlockElement::GetList(
			array(),
			array(
				"ID" => $productID,
				"ACTIVE" => "Y",
				"ACTIVE_DATE" => "Y",
				"CHECK_PERMISSIONS" => "N",
				),
			false,
			false,
			array('ID', 'IBLOCK_ID', 'NAME', 'DETAIL_PAGE_URL')
			);
		if(!($arProduct = $dbIBlockElement->GetNext()))
			return $arResult;
		if ('E' == CIBlock::GetArrayByID($arProduct['IBLOCK_ID'], "RIGHTS_MODE"))
		{
			$arUserRights = CIBlockElementRights::GetUserOperations($productID, $intUserID);
			if (empty($arUserRights))
			{
				return $arResult;
			}
			elseif (!is_array($arUserRights) || !array_key_exists('element_read', $arUserRights))
			{
				return $arResult;
			}
		}
		else
		{
			if ('R' > CIBlock::GetPermission($arProduct['IBLOCK_ID'], $intUserID))
			{
				return $arResult;
			}
		}
	}
	else
	{
		$dbIBlockElement = CIBlockElement::GetList(
			array(),
			array(
				"ID" => $productID,
				"ACTIVE" => "Y",
				"ACTIVE_DATE" => "Y",
				"CHECK_PERMISSIONS" => "Y",
				"MIN_PERMISSION" => "R",
				),
			false,
			false,
			array('ID', 'IBLOCK_ID', 'NAME', 'DETAIL_PAGE_URL')
			);
		if(!($arProduct = $dbIBlockElement->GetNext()))
			return $arResult;
	}

	$rsCatalogs = CCatalog::GetList(
		array(),
		array('IBLOCK_ID' => $arProduct["IBLOCK_ID"]),
		false,
		false,
		array(
			'IBLOCK_ID',
			'SUBSCRIPTION'
		)
	);
	if (!($arCatalog = $rsCatalogs->Fetch()))
	{
		return $arResult;
	}
	if ('Y' == $arCatalog["SUBSCRIPTION"])
	{
		$quantity = 1;
	}

	$dblQuantity = 0;
	$boolQuantity = false;

	$rsProducts = CCatalogProduct::GetList(
		array(),
		array('ID' => $productID),
		false,
		false,
		array(
			'ID',
			'CAN_BUY_ZERO',
			'QUANTITY_TRACE',
			'QUANTITY',
			'WEIGHT',
		)
	);

	if ($arCatalogProduct = $rsProducts->Fetch())
	{
		$dblQuantity = doubleval($arCatalogProduct["QUANTITY"]);
		$boolQuantity = ('Y' != $arCatalogProduct["CAN_BUY_ZERO"] && 'Y' == $arCatalogProduct["QUANTITY_TRACE"]);

		if ($boolQuantity && 0 >= $dblQuantity)
		{
			$APPLICATION->ThrowException(GetMessage("CATALOG_NO_QUANTITY_PRODUCT", array("#NAME#" => htmlspecialcharsbx($arProduct["~NAME"]))), "CATALOG_NO_QUANTITY_PRODUCT");
			return $arResult;
		}
	}
	else
	{
		$APPLICATION->ThrowException(GetMessage("CATALOG_ERR_NO_PRODUCT"), "CATALOG_NO_QUANTITY_PRODUCT");
		return $arResult;
	}

	if (0 < $intUserID)
	{
		$arCoupons = CCatalogDiscountCoupon::GetCouponsByManage($intUserID);
		CCatalogDiscountSave::SetDiscountUserID($intUserID);
	}
	else
	{
		$arCoupons = CCatalogDiscountCoupon::GetCoupons();
	}

	if (is_array($arCoupons) && isset($CATALOG_ONETIME_COUPONS_BASKET) && is_array($CATALOG_ONETIME_COUPONS_BASKET))
	{
		foreach ($arCoupons as $key => $coupon)
		{
			if (array_key_exists($coupon, $CATALOG_ONETIME_COUPONS_BASKET))
			{
				if ($CATALOG_ONETIME_COUPONS_BASKET[$coupon] == $productID)
				{
					$arCoupons = array($coupon);
					break;
				}
				else
				{
					unset($arCoupons[$key]);
				}
			}
		}
	}

	$arPrice = CCatalogProduct::GetOptimalPrice($productID, $quantity, (0 < $intUserID ? $arUserCache[$intUserID] : $USER->GetUserGroupArray()), $renewal, array(), (0 < $intUserID ? $strSiteID : false), $arCoupons);

	if (empty($arPrice))
	{
		if ($nearestQuantity = CCatalogProduct::GetNearestQuantityPrice($productID, $quantity, (0 < $intUserID ? $arUserCache[$intUserID] : $USER->GetUserGroupArray())))
		{
			$quantity = $nearestQuantity;
			$arPrice = CCatalogProduct::GetOptimalPrice($productID, $quantity, (0 < $intUserID ? $arUserCache[$intUserID] : $USER->GetUserGroupArray()), $renewal, array(), (0 < $intUserID ? $strSiteID : false), $arCoupons);
		}
	}

	if (empty($arPrice))
	{
		if (0 < $intUserID)
		{
			CCatalogDiscountSave::ClearDiscountUserID();
		}
		return $arResult;
	}

	$boolDiscountVat = ('N' != COption::GetOptionString('catalog', 'discount_vat', 'Y'));

	$currentPrice = $arPrice["PRICE"]["PRICE"];
	$currentDiscount = 0.0;

	$arPrice['PRICE']['ORIG_VAT_INCLUDED'] = $arPrice['PRICE']['VAT_INCLUDED'];

	if ($boolDiscountVat)
	{
		if ('N' == $arPrice['PRICE']['VAT_INCLUDED'])
		{
			$currentPrice *= (1 + $arPrice['PRICE']['VAT_RATE']);
			$arPrice['PRICE']['VAT_INCLUDED'] = 'Y';
		}
	}
	else
	{
		if ('Y' == $arPrice['PRICE']['VAT_INCLUDED'])
		{
			$currentPrice /= (1 + $arPrice['PRICE']['VAT_RATE']);
			$arPrice['PRICE']['VAT_INCLUDED'] = 'N';
		}
	}

	$arDiscountList = array();

	if (!empty($arPrice["DISCOUNT_LIST"]))
	{
		$dblStartPrice = $currentPrice;

		foreach ($arPrice["DISCOUNT_LIST"] as &$arOneDiscount)
		{
			switch ($arOneDiscount['VALUE_TYPE'])
			{
			case 'F':
				if ($arOneDiscount['CURRENCY'] == $arPrice["PRICE"]["CURRENCY"])
					$currentDiscount = $arOneDiscount['VALUE'];
				else
					$currentDiscount = CCurrencyRates::ConvertCurrency($arOneDiscount["VALUE"], $arOneDiscount["CURRENCY"], $arPrice["PRICE"]["CURRENCY"]);
				$currentPrice = $currentPrice - $currentDiscount;
				break;
			case 'P':
				$currentDiscount = $currentPrice*$arOneDiscount["VALUE"]/100.0;
				if (0 < $arOneDiscount['MAX_DISCOUNT'])
				{
					if ($arOneDiscount['CURRENCY'] == $arPrice["PRICE"]["CURRENCY"])
						$dblMaxDiscount = $arOneDiscount['MAX_DISCOUNT'];
					else
						$dblMaxDiscount = CCurrencyRates::ConvertCurrency($arOneDiscount['MAX_DISCOUNT'], $arOneDiscount["CURRENCY"], $arPrice["PRICE"]["CURRENCY"]);;
					if ($currentDiscount > $dblMaxDiscount)
						$currentDiscount = $dblMaxDiscount;
				}
				$currentPrice = $currentPrice - $currentDiscount;
				break;
			case 'S':
				if ($arOneDiscount['CURRENCY'] == $arPrice["PRICE"]["CURRENCY"])
					$currentPrice = $arOneDiscount['VALUE'];
				else
					$currentPrice = CCurrencyRates::ConvertCurrency($arOneDiscount['VALUE'], $arOneDiscount["CURRENCY"], $arPrice["PRICE"]["CURRENCY"]);
				break;
			}

			$arOneList = array(
				'ID' => $arOneDiscount['ID'],
				'NAME' => $arOneDiscount['NAME'],
				'COUPON' => '',
				'MODULE_ID' => 'catalog',
			);

			if ($arOneDiscount['COUPON'])
			{
				$arOneList['COUPON'] = $arOneDiscount['COUPON'];
				$dbRes = CCatalogDiscountCoupon::GetList(array(), array('COUPON' => $arOneDiscount['COUPON'], 'ONE_TIME' => 'Y'), false, array('nTopCount' => 1), array('ID'));

				if ($arRes = $dbRes->Fetch())
				{
					$CATALOG_ONETIME_COUPONS_BASKET[$arOneDiscount['COUPON']] = $productID;
				}
			}
			$arDiscountList[] = $arOneList;
		}
		if (isset($arOneDiscount))
			unset($arOneDiscount);

		$currentDiscount = $dblStartPrice - $currentPrice;
	}

	if (empty($arPrice["PRICE"]["CATALOG_GROUP_NAME"]))
	{
		if (!empty($arPrice["PRICE"]["CATALOG_GROUP_ID"]))
		{
			$rsCatGroups = CCatalogGroup::GetListEx(array(),array('ID' => $arPrice["PRICE"]["CATALOG_GROUP_ID"]),false,false,array('ID','NAME','NAME_LANG'));
			if ($arCatGroup = $rsCatGroups->Fetch())
			{
				$arPrice["PRICE"]["CATALOG_GROUP_NAME"] = (!empty($arCatGroup['NAME_LANG']) ? $arCatGroup['NAME_LANG'] : $arCatGroup['NAME']);
			}
		}
	}

	if (!$boolDiscountVat)
	{
		$currentPrice *= (1 + $arPrice['PRICE']['VAT_RATE']);
		$currentDiscount *= (1 + $arPrice['PRICE']['VAT_RATE']);
		$arPrice['PRICE']['VAT_INCLUDED'] = 'Y';
	}

	$arResult = array(
		"PRODUCT_PRICE_ID" => $arPrice["PRICE"]["ID"],
		"PRICE" => $currentPrice,
		"VAT_RATE" => $arPrice['PRICE']['VAT_RATE'],
		"CURRENCY" => $arPrice["PRICE"]["CURRENCY"],
		"QUANTITY" => ($boolQuantity && $dblQuantity < $quantity ? $dblQuantity : $quantity),
		"DISCOUNT_PRICE" => $currentDiscount,
		"WEIGHT" => intval($arCatalogProduct['WEIGHT']),
		"NAME" => $arProduct["~NAME"],
		"CAN_BUY" => "Y",
		"DETAIL_PAGE_URL" => $arProduct['~DETAIL_PAGE_URL'],
		"NOTES" => $arPrice["PRICE"]["CATALOG_GROUP_NAME"],
		"BARCODE_MULTI" => $arCatalogProduct["BARCODE_MULTI"]
	);

	if ($boolQuantity && $dblQuantity < $quantity)
	{
		$APPLICATION->ThrowException(GetMessage("CATALOG_QUANTITY_NOT_ENOGH", array("#NAME#" => htmlspecialcharsbx($arProduct["~NAME"]), "#CATALOG_QUANTITY#" => $arCatalogProduct["QUANTITY"], "#QUANTITY#" => $quantity)), "CATALOG_QUANTITY_NOT_ENOGH");
	}

	if (0 < $intUserID)
	{
		CCatalogDiscountSave::ClearDiscountUserID();
	}

	if (!empty($arPrice["DISCOUNT_LIST"]))
	{
		$arResult['DISCOUNT_LIST'] = $arDiscountList;
	}

	return $arResult;
}

function CatalogBasketOrderCallback($productID, $quantity, $renewal = "N", $intUserID = 0, $strSiteID = false)
{
	global $USER;
	global $DB;

	$productID = intval($productID);
	$quantity = doubleval($quantity);
	$renewal = (($renewal == "Y") ? "Y" : "N");

	$intUserID = intval($intUserID);
	if (0 > $intUserID)
		$intUserID = 0;

	$arResult = array();

	static $arUserCache = array();
	if (0 < $intUserID)
	{
		if (!array_key_exists($intUserID,$arUserCache))
		{
			$rsUsers = CUser::GetList(($by = 'ID'),($order = 'DESC'),array("ID_EQUAL_EXACT"=>$intUserID),array('FIELDS' => array('ID')));
			if ($arUser = $rsUsers->Fetch())
			{
				$arUserCache[$arUser['ID']] = CUser::GetUserGroup($arUser['ID']);
			}
			else
			{
				$intUserID = 0;
				return $arResult;
			}
		}

		$dbIBlockElement = CIBlockElement::GetList(
			array(),
			array(
					"ID" => $productID,
					"ACTIVE" => "Y",
					"ACTIVE_DATE" => "Y",
					"CHECK_PERMISSION" => "N",
				),
			false,
			false,
			array('ID', 'IBLOCK_ID', 'NAME', 'DETAIL_PAGE_URL')
			);
		if(!($arProduct = $dbIBlockElement->GetNext()))
			return $arResult;

		if ('E' == CIBlock::GetArrayByID($arProduct['IBLOCK_ID'], "RIGHTS_MODE"))
		{
			$arUserRights = CIBlockElementRights::GetUserOperations($productID,$intUserID);
			if (empty($arUserRights))
			{
				return $arResult;
			}
			elseif (!is_array($arUserRights) || !array_key_exists('element_read',$arUserRights))
			{
				return $arResult;
			}
		}
		else
		{
			if ('R' > CIBlock::GetPermission($arProduct['IBLOCK_ID'], $intUserID))
			{
				return $arResult;
			}
		}
	}
	else
	{
		$dbIBlockElement = CIBlockElement::GetList(
			array(),
			array(
				"ID" => $productID,
				"ACTIVE" => "Y",
				"ACTIVE_DATE" => "Y",
				"CHECK_PERMISSIONS" => "Y",
				"MIN_PERMISSION" => "R",
			),
			false,
			false,
			array('ID', 'IBLOCK_ID', 'NAME', 'DETAIL_PAGE_URL')
		);
		if(!($arProduct = $dbIBlockElement->GetNext()))
			return $arResult;
	}

	$rsProducts = CCatalogProduct::GetList(
		array(),
		array('ID' => $productID),
		false,
		false,
		array(
			'ID',
			'CAN_BUY_ZERO',
			'QUANTITY_TRACE',
			'QUANTITY',
			'WEIGHT',
		)
	);

	if ($arCatalogProduct = $rsProducts->Fetch())
	{
		if ('Y' != $arCatalogProduct["CAN_BUY_ZERO"] && 'Y' == $arCatalogProduct["QUANTITY_TRACE"] && 0 >= doubleval($arCatalogProduct["QUANTITY"]))
			return $arResult;
	}
	else
	{
		return $arResult;
	}

	if (0 < $intUserID)
	{
		$arCoupons = CCatalogDiscountCoupon::GetCouponsByManage($intUserID);
		CCatalogDiscountSave::SetDiscountUserID($intUserID);
	}
	else
	{
		$arCoupons = CCatalogDiscountCoupon::GetCoupons();
	}

	$arPrice = CCatalogProduct::GetOptimalPrice($productID, $quantity, (0 < $intUserID ? $arUserCache[$intUserID] : $USER->GetUserGroupArray()), $renewal, array(), (0 < $intUserID ? $strSiteID : false), $arCoupons);

	if (empty($arPrice))
	{
		if ($nearestQuantity = CCatalogProduct::GetNearestQuantityPrice($productID, $quantity, (0 < $intUserID ? $arUserCache[$intUserID] : $USER->GetUserGroupArray())))
		{
			$quantity = $nearestQuantity;
			$arPrice = CCatalogProduct::GetOptimalPrice($productID, $quantity, (0 < $intUserID ? $arUserCache[$intUserID] : $USER->GetUserGroupArray()), $renewal, array(), (0 < $intUserID ? $strSiteID : false), $arCoupons);
		}
	}

	if (empty($arPrice))
	{
		if (0 < $intUserID)
		{
			CCatalogDiscountSave::ClearDiscountUserID();
		}
		return $arResult;
	}

	$boolDiscountVat = ('N' != COption::GetOptionString('catalog', 'discount_vat', 'Y'));

	$currentPrice = $arPrice["PRICE"]["PRICE"];
	$currentDiscount = 0.0;

	if ($boolDiscountVat)
	{
		if ('N' == $arPrice['PRICE']['VAT_INCLUDED'])
		{
			$currentPrice *= (1 + $arPrice['PRICE']['VAT_RATE']);
			$arPrice['PRICE']['VAT_INCLUDED'] = 'Y';
		}
	}
	else
	{
		if ('Y' == $arPrice['PRICE']['VAT_INCLUDED'])
		{
			$currentPrice /= (1 + $arPrice['PRICE']['VAT_RATE']);
			$arPrice['PRICE']['VAT_INCLUDED'] = 'N';
		}
	}

	$arDiscountList = array();
	$arCouponList = array();

	if (!empty($arPrice["DISCOUNT_LIST"]))
	{
		$dblStartPrice = $currentPrice;

		foreach ($arPrice["DISCOUNT_LIST"] as &$arOneDiscount)
		{
			switch ($arOneDiscount['VALUE_TYPE'])
			{
			case 'F':
				if ($arOneDiscount['CURRENCY'] == $arPrice["PRICE"]["CURRENCY"])
					$currentDiscount = $arOneDiscount['VALUE'];
				else
					$currentDiscount = CCurrencyRates::ConvertCurrency($arOneDiscount["VALUE"], $arOneDiscount["CURRENCY"], $arPrice["PRICE"]["CURRENCY"]);
				$currentPrice = $currentPrice - $currentDiscount;
				break;
			case 'P':
				$currentDiscount = $currentPrice*$arOneDiscount["VALUE"]/100.0;
				if (0 < $arOneDiscount['MAX_DISCOUNT'])
				{
					if ($arOneDiscount['CURRENCY'] == $arPrice["PRICE"]["CURRENCY"])
						$dblMaxDiscount = $arOneDiscount['MAX_DISCOUNT'];
					else
						$dblMaxDiscount = CCurrencyRates::ConvertCurrency($arOneDiscount['MAX_DISCOUNT'], $arOneDiscount["CURRENCY"], $arPrice["PRICE"]["CURRENCY"]);;
					if ($currentDiscount > $dblMaxDiscount)
						$currentDiscount = $dblMaxDiscount;
				}
				$currentPrice = $currentPrice - $currentDiscount;
				break;
			case 'S':
				if ($arOneDiscount['CURRENCY'] == $arPrice["PRICE"]["CURRENCY"])
					$currentPrice = $arOneDiscount['VALUE'];
				else
					$currentPrice = CCurrencyRates::ConvertCurrency($arOneDiscount['VALUE'], $arOneDiscount["CURRENCY"], $arPrice["PRICE"]["CURRENCY"]);
				break;
			}

			$arOneList = array(
				'ID' => $arOneDiscount['ID'],
				'NAME' => $arOneDiscount['NAME'],
				'COUPON' => '',
				'MODULE_ID' => 'catalog',
			);

			if ($arOneDiscount['COUPON'])
			{
				$arOneList['COUPON'] = $arOneDiscount['COUPON'];
				$arCouponList[] = $arOneDiscount['COUPON'];
			}

			$arDiscountList[] = $arOneList;
		}
		if (isset($arOneDiscount))
			unset($arOneDiscount);

		$currentDiscount = $dblStartPrice - $currentPrice;
	}

	if (empty($arPrice["PRICE"]["CATALOG_GROUP_NAME"]))
	{
		if (!empty($arPrice["PRICE"]["CATALOG_GROUP_ID"]))
		{
			$rsCatGroups = CCatalogGroup::GetListEx(array(),array('ID' => $arPrice["PRICE"]["CATALOG_GROUP_ID"]),false,false,array('ID','NAME','NAME_LANG'));
			if ($arCatGroup = $rsCatGroups->Fetch())
			{
				$arPrice["PRICE"]["CATALOG_GROUP_NAME"] = (!empty($arCatGroup['NAME_LANG']) ? $arCatGroup['NAME_LANG'] : $arCatGroup['NAME']);
			}
		}
	}

	if (!$boolDiscountVat)
	{
		$currentPrice *= (1 + $arPrice['PRICE']['VAT_RATE']);
		$currentDiscount *= (1 + $arPrice['PRICE']['VAT_RATE']);
		$arPrice['PRICE']['VAT_INCLUDED'] = 'Y';
	}

	$arResult = array(
		"PRODUCT_PRICE_ID" => $arPrice["PRICE"]["ID"],
		"PRICE" => $currentPrice,
		"VAT_RATE" => $arPrice['PRICE']['VAT_RATE'],
		"CURRENCY" => $arPrice["PRICE"]["CURRENCY"],
		"QUANTITY" => $quantity,
		"WEIGHT" => intval($arCatalogProduct["WEIGHT"]),
		"NAME" => $arProduct["~NAME"],
		"CAN_BUY" => "Y",
		"DETAIL_PAGE_URL" => $arProduct['~DETAIL_PAGE_URL'],
		"NOTES" => $arPrice["PRICE"]["CATALOG_GROUP_NAME"],
		"DISCOUNT_PRICE" => $currentDiscount,
	);
	if (!empty($arPrice["DISCOUNT_LIST"]))
	{
		$arResult["DISCOUNT_VALUE"] = (100*$currentDiscount/($currentDiscount+$currentPrice))."%";
		$arResult["DISCOUNT_NAME"] = "[".$arPrice["DISCOUNT"]["ID"]."] ".$arPrice["DISCOUNT"]["NAME"];
		$arResult['DISCOUNT_LIST'] = $arDiscountList;

		if (!empty($arPrice["DISCOUNT"]["COUPON"]))
		{
			$arResult["DISCOUNT_COUPON"] = $arPrice["DISCOUNT"]["COUPON"];
		}
		if (!empty($arCouponList))
		{
			$mxApply = CCatalogDiscountCoupon::CouponApply($intUserID, $arCouponList);
		}
	}

	CCatalogProduct::QuantityTracer($productID, $quantity);

	if (0 < $intUserID)
	{
		CCatalogDiscountSave::ClearDiscountUserID();
	}
	return $arResult;
}

function CatalogViewedProductCallback($productID, $UserID, $strSiteID = SITE_ID)
{
	global $USER;
	global $CATALOG_ONETIME_COUPONS_BASKET;

	$productID = intval($productID);
	$UserID = intval($UserID);

	if ($productID <= 0)
		return false;

	$arResult = array();

	static $arUserCache = array();
	if ($UserID > 0)
	{
		if (!array_key_exists($UserID, $arUserCache))
		{
			$rsUsers = CUser::GetList(($by = 'ID'), ($order = 'DESC'), array("ID_EQUAL_EXACT"=>$UserID), array('FIELDS' => array('ID')));
			if ($arUser = $rsUsers->Fetch())
				$arUserCache[$arUser['ID']] = CUser::GetUserGroup($arUser['ID']);
			else
				return false;
		}

		CCatalogDiscountSave::SetDiscountUserID($UserID);

		$dbIBlockElement = CIBlockElement::GetList(
			array(),
			array(
					"ID" => $productID,
					"ACTIVE" => "Y",
					"ACTIVE_DATE" => "Y",
					"CHECK_PERMISSIONS" => "N",
				),
			false,
			false,
			array('ID', 'IBLOCK_ID', 'NAME', 'DETAIL_PAGE_URL', 'TIMESTAMP_X', 'PREVIEW_PICTURE', 'DETAIL_PICTURE')
			);
		if(!($arProduct = $dbIBlockElement->GetNext()))
			return false;

		if (CIBlock::GetArrayByID($arProduct['IBLOCK_ID'], "RIGHTS_MODE") == 'E')
		{
			$arUserRights = CIBlockElementRights::GetUserOperations($productID,$UserID);
			if (empty($arUserRights))
				return false;
			elseif (!is_array($arUserRights) || !array_key_exists('element_read',$arUserRights))
				return false;
		}
		else
		{
			if (CIBlock::GetPermission($arProduct['IBLOCK_ID'], $UserID) < 'R')
				return false;
		}
	}
	else
	{
		$dbIBlockElement = CIBlockElement::GetList(
			array(),
			array(
					"ID" => $productID,
					"ACTIVE" => "Y",
					"ACTIVE_DATE" => "Y",
					"CHECK_PERMISSIONS" => "Y",
					"MIN_PERMISSION" => "R",
				),
			false,
			false,
			array('ID', 'IBLOCK_ID', 'NAME', 'DETAIL_PAGE_URL', 'TIMESTAMP_X', 'PREVIEW_PICTURE', 'DETAIL_PICTURE')
			);
		if(!($arProduct = $dbIBlockElement->GetNext()))
			return false;
	}

	$bTrace = true;
	if ($arCatalogProduct = CCatalogProduct::GetByID($productID))
	{
		if ($arCatalogProduct["CAN_BUY_ZERO"] != "Y" && ($arCatalogProduct["QUANTITY_TRACE"] == "Y" && doubleval($arCatalogProduct["QUANTITY"]) <= 0))
		{
			$currentPrice = 0.0;
			$currentDiscount = 0.0;
			$bTrace = false;
		}
	}

	if ($bTrace)
	{
		$arPrice = CCatalogProduct::GetOptimalPrice($productID, 1, ($UserID > 0 ? $arUserCache[$UserID] : $USER->GetUserGroupArray()), "N", array(), ($UserID > 0 ? $strSiteID : false), array());

		if (count($arPrice) > 0)
		{
			$currentPrice = $arPrice["PRICE"]["PRICE"];
			$currentDiscount = 0.0;

			if ($arPrice['PRICE']['VAT_INCLUDED'] == 'N')
			{
				if(doubleval($arPrice['PRICE']['VAT_RATE']) > 0)
				{
					$currentPrice *= (1 + $arPrice['PRICE']['VAT_RATE']);
					$arPrice['PRICE']['VAT_INCLUDED'] = 'Y';
				}
			}

			if (!empty($arPrice["DISCOUNT"]))
			{
				$currentDiscount_tmp = 0;
				if ($arPrice["DISCOUNT"]["VALUE_TYPE"]=="F")
				{
					if ($arPrice["DISCOUNT"]["CURRENCY"] == $arPrice["PRICE"]["CURRENCY"])
						$currentDiscount = $arPrice["DISCOUNT"]["VALUE"];
					else
						$currentDiscount = CCurrencyRates::ConvertCurrency($arPrice["DISCOUNT"]["VALUE"], $arPrice["DISCOUNT"]["CURRENCY"], $arPrice["PRICE"]["CURRENCY"]);
				}
				elseif ($arPrice["DISCOUNT"]["VALUE_TYPE"]=="S")
				{
					if ($arPrice["DISCOUNT"]["CURRENCY"] == $arPrice["PRICE"]["CURRENCY"])
						$currentDiscount = $arPrice["DISCOUNT"]["VALUE"];
					else
						$currentDiscount = CCurrencyRates::ConvertCurrency($arPrice["DISCOUNT"]["VALUE"], $arPrice["DISCOUNT"]["CURRENCY"], $arPrice["PRICE"]["CURRENCY"]);
				}
				else
				{
					$currentDiscount = $currentPrice * $arPrice["DISCOUNT"]["VALUE"] / 100.0;

					if (doubleval($arPrice["DISCOUNT"]["MAX_DISCOUNT"]) > 0)
					{
						if ($arPrice["DISCOUNT"]["CURRENCY"] == $arPrice["PRICE"]["CURRENCY"])
							$maxDiscount = $arPrice["DISCOUNT"]["MAX_DISCOUNT"];
						else
							$maxDiscount = CCurrencyRates::ConvertCurrency($arPrice["DISCOUNT"]["MAX_DISCOUNT"], $arPrice["DISCOUNT"]["CURRENCY"], $arPrice["PRICE"]["CURRENCY"]);

						if ($currentDiscount > $maxDiscount)
							$currentDiscount = $maxDiscount;
					}
				}

				if ($arPrice["DISCOUNT"]["VALUE_TYPE"] == "S")
				{
					$currentDiscount_tmp = $currentPrice - $currentDiscount;
					$currentPrice = $currentDiscount;
					$currentDiscount = $currentDiscount_tmp;
				}
				else
				{
					$currentPrice = $currentPrice - $currentDiscount;
				}
			}

			if (empty($arPrice["PRICE"]["CATALOG_GROUP_NAME"]))
			{
				if (!empty($arPrice["PRICE"]["CATALOG_GROUP_ID"]))
				{
					$rsCatGroups = CCatalogGroup::GetList(array(),array('ID' => $arPrice["PRICE"]["CATALOG_GROUP_ID"]),false,array('nTopCount' => 1),array('ID','NAME','NAME_LANG'));
					if ($arCatGroup = $rsCatGroups->Fetch())
					{
						$arPrice["PRICE"]["CATALOG_GROUP_NAME"] = (!empty($arCatGroup['NAME_LANG']) ? $arCatGroup['NAME_LANG'] : $arCatGroup['NAME']);
					}
				}
			}
		}
		else
		{
			$currentPrice = 0.0;
			$currentDiscount = 0.0;
		}
	}

	$arResult = array(
			"PREVIEW_PICTURE" => $arProduct['PREVIEW_PICTURE'],
			"DETAIL_PICTURE" => $arProduct['DETAIL_PICTURE'],
			"PRODUCT_PRICE_ID" => $arPrice["PRICE"]["ID"],
			"PRICE" => $currentPrice,
			"VAT_RATE" => $arPrice['PRICE']['VAT_RATE'],
			"CURRENCY" => $arPrice["PRICE"]["CURRENCY"],
			"DISCOUNT_PRICE" => $currentDiscount,
			"NAME" => $arProduct["~NAME"],
			"DETAIL_PAGE_URL" => $arProduct['~DETAIL_PAGE_URL'],
			"NOTES" => $arPrice["PRICE"]["CATALOG_GROUP_NAME"]
	);

	if ($UserID > 0)
		CCatalogDiscountSave::ClearDiscountUserID();

	return $arResult;
}

/*
* @deprecated deprecated since catalog 12.5.6
* @see CCatalogDiscountCoupon::CouponOneOrderDisable()
*/
function CatalogDeactivateOneTimeCoupons($intOrderID = 0)
{
	CCatalogDiscountCoupon::CouponOneOrderDisable($intOrderID);
}

function CatalogPayOrderCallback($productID, $userID, $bPaid, $orderID)
{
	global $DB;
	global $USER;

	$productID = intval($productID);
	$userID = intval($userID);
	$bPaid = ($bPaid ? true : false);
	$orderID = intval($orderID);

	if ($userID <= 0)
		return false;

	$dbIBlockElement = CIBlockElement::GetList(
		array(),
		array(
			"ID" => $productID,
			"ACTIVE" => "Y",
			"ACTIVE_DATE" => "Y",
			"CHECK_PERMISSIONS" => "N",
		),
		false,
		false,
		array('ID', 'IBLOCK_ID', 'NAME', 'DETAIL_PAGE_URL')
	);
	if ($arIBlockElement = $dbIBlockElement->GetNext())
	{
		$arCatalog = CCatalog::GetByID($arIBlockElement["IBLOCK_ID"]);
		if ($arCatalog["SUBSCRIPTION"] == "Y")
		{
			$arProduct = CCatalogProduct::GetByID($productID);

			if ($bPaid)
			{
				if ('E' == CIBlock::GetArrayByID($arIBlockElement['IBLOCK_ID'], "RIGHTS_MODE"))
				{
					$arUserRights = CIBlockElementRights::GetUserOperations($productID, $userID);
					if (empty($arUserRights))
					{
						return false;
					}
					elseif (!is_array($arUserRights) || !array_key_exists('element_read', $arUserRights))
					{
						return false;
					}
				}
				else
				{
					if ('R' > CIBlock::GetPermission($arIBlockElement['IBLOCK_ID'], $userID))
					{
						return false;
					}
				}

				$arUserGroups = array();
				$arTmp = array();
				$ind = -1;
				$curTime = time();
				$dbProductGroups = CCatalogProductGroups::GetList(
						array(),
						array("PRODUCT_ID" => $productID),
						false,
						false,
						array("GROUP_ID", "ACCESS_LENGTH", "ACCESS_LENGTH_TYPE")
					);
				while ($arProductGroups = $dbProductGroups->Fetch())
				{
					$ind++;

					$arProductGroups['GROUP_ID'] = intval($arProductGroups['GROUP_ID']);
					$accessType = $arProductGroups["ACCESS_LENGTH_TYPE"];
					$accessLength = intval($arProductGroups["ACCESS_LENGTH"]);

					$accessVal = 0;
					if (0 < $accessLength)
					{
						if ($accessType == "H")
							$accessVal = mktime(date("H") + $accessLength, date("i"), date("s"), date("m"), date("d"), date("Y"));
						elseif ($accessType == "D")
							$accessVal = mktime(date("H"), date("i"), date("s"), date("m"), date("d") + $accessLength, date("Y"));
						elseif ($accessType == "W")
							$accessVal = mktime(date("H"), date("i"), date("s"), date("m"), date("d") + 7 * $accessLength, date("Y"));
						elseif ($accessType == "M")
							$accessVal = mktime(date("H"), date("i"), date("s"), date("m") + $accessLength, date("d"), date("Y"));
						elseif ($accessType == "Q")
							$accessVal = mktime(date("H"), date("i"), date("s"), date("m") + 3 * $accessLength, date("d"), date("Y"));
						elseif ($accessType == "S")
							$accessVal = mktime(date("H"), date("i"), date("s"), date("m") + 6 * $accessLength, date("d"), date("Y"));
						elseif ($accessType == "Y")
							$accessVal = mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y") + $accessLength);
						elseif ($accessType == "T")
							$accessVal = mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y") + 2 * $accessLength);
					}

					$arUserGroups[$ind] = array(
							"GROUP_ID" => $arProductGroups["GROUP_ID"],
							"DATE_ACTIVE_FROM" => date($DB->DateFormatToPHP(CLang::GetDateFormat("FULL", SITE_ID)), $curTime),
							"DATE_ACTIVE_TO" => (0 < $accessLength ? date($DB->DateFormatToPHP(CLang::GetDateFormat("FULL", SITE_ID)), $accessVal) : false)
						);

					$arTmp[$arProductGroups["GROUP_ID"]] = $ind;
				}

				if (!empty($arUserGroups))
				{
					$dbOldGroups = CUser::GetUserGroupEx($userID);
					while ($arOldGroups = $dbOldGroups->Fetch())
					{
						$arOldGroups["GROUP_ID"] = intval($arOldGroups["GROUP_ID"]);
						if (array_key_exists($arOldGroups["GROUP_ID"], $arTmp))
						{
							if (strlen($arOldGroups["DATE_ACTIVE_FROM"]) <= 0)
							{
								$arUserGroups[$arTmp[$arOldGroups["GROUP_ID"]]]["DATE_ACTIVE_FROM"] = false;
							}
							else
							{
								$oldDate = CDatabase::FormatDate($arOldGroups["DATE_ACTIVE_FROM"], CSite::GetDateFormat("SHORT", SITE_ID), "YYYYMMDDHHMISS");
								$newDate = CDatabase::FormatDate($arUserGroups[$arTmp[$arOldGroups["GROUP_ID"]]]["DATE_ACTIVE_FROM"], CSite::GetDateFormat("SHORT", SITE_ID), "YYYYMMDDHHMISS");
								if ($oldDate > $newDate)
									$arUserGroups[$arTmp[$arOldGroups["GROUP_ID"]]]["DATE_ACTIVE_FROM"] = $arOldGroups["DATE_ACTIVE_FROM"];
							}

							if (strlen($arOldGroups["DATE_ACTIVE_TO"]) <= 0)
							{
								$arUserGroups[$arTmp[$arOldGroups["GROUP_ID"]]]["DATE_ACTIVE_TO"] = false;
							}
							elseif (false !== $arUserGroups[$arTmp[$arOldGroups["GROUP_ID"]]]["DATE_ACTIVE_TO"])
							{
								$oldDate = CDatabase::FormatDate($arOldGroups["DATE_ACTIVE_TO"], CSite::GetDateFormat("SHORT", SITE_ID), "YYYYMMDDHHMISS");
								$newDate = CDatabase::FormatDate($arUserGroups[$arTmp[$arOldGroups["GROUP_ID"]]]["DATE_ACTIVE_TO"], CSite::GetDateFormat("SHORT", SITE_ID), "YYYYMMDDHHMISS");
								if ($oldDate > $newDate)
									$arUserGroups[$arTmp[$arOldGroups["GROUP_ID"]]]["DATE_ACTIVE_TO"] = $arOldGroups["DATE_ACTIVE_TO"];
							}
						}
						else
						{
							$ind++;

							$arUserGroups[$ind] = array(
									"GROUP_ID" => $arOldGroups["GROUP_ID"],
									"DATE_ACTIVE_FROM" => $arOldGroups["DATE_ACTIVE_FROM"],
									"DATE_ACTIVE_TO" => $arOldGroups["DATE_ACTIVE_TO"]
								);
						}
					}

					CUser::SetUserGroup($userID, $arUserGroups);
					if (CCatalog::IsUserExists())
					{
						if (intval($USER->GetID()) == $userID)
						{
							$arUserGroupsTmp = array();
							foreach ($arUserGroups as &$arOneGroup)
							{
								$arUserGroupsTmp[] = $arOneGroup["GROUP_ID"];
							}
							if (isset($arOneGroup))
								unset($arOneGroup);

							$USER->SetUserGroupArray($arUserGroupsTmp);
						}
					}
				}
			}
			else
			{
				$arUserGroups = array();
				$ind = -1;
				$arTmp = array();

				$dbOldGroups = CUser::GetUserGroupEx($userID);
				while ($arOldGroups = $dbOldGroups->Fetch())
				{
					$ind++;
					$arOldGroups["GROUP_ID"] = intval($arOldGroups["GROUP_ID"]);
					$arUserGroups[$ind] = array(
							"GROUP_ID" => $arOldGroups["GROUP_ID"],
							"DATE_ACTIVE_FROM" => $arOldGroups["DATE_ACTIVE_FROM"],
							"DATE_ACTIVE_TO" => $arOldGroups["DATE_ACTIVE_FROM"]
						);

					$arTmp[$arOldGroups["GROUP_ID"]] = $ind;
				}

				$bNeedUpdate = false;
				$dbProductGroups = CCatalogProductGroups::GetList(
						array(),
						array("PRODUCT_ID" => $productID),
						false,
						false,
						array("GROUP_ID")
					);
				while ($arProductGroups = $dbProductGroups->Fetch())
				{
					$arProductGroups["GROUP_ID"] = intval($arProductGroups["GROUP_ID"]);
					if (array_key_exists($arProductGroups["GROUP_ID"], $arTmp))
					{
						unset($arUserGroups[$arProductGroups["GROUP_ID"]]);
						$bNeedUpdate = true;
					}
				}

				if ($bNeedUpdate)
				{
					CUser::SetUserGroup($userID, $arUserGroups);

					if (CCatalog::IsUserExists())
					{
						if (intval($USER->GetID()) == $userID)
						{
							$arUserGroupsTmp = array();
							foreach ($arUserGroups as &$arOneGroup)
							{
								$arUserGroupsTmp[] = $arOneGroup["GROUP_ID"];
							}
							if (isset($arOneGroup))
								unset($arOneGroup);

							$USER->SetUserGroupArray($arUserGroupsTmp);
						}
					}
				}
			}

			if ($arProduct["PRICE_TYPE"] != "S")
			{
				if ($bPaid)
				{
					$recurType = $arProduct["RECUR_SCHEME_TYPE"];
					$recurLength = intval($arProduct["RECUR_SCHEME_LENGTH"]);

					$recurSchemeVal = 0;
					if ($recurType == "H")
						$recurSchemeVal = mktime(date("H") + $recurLength, date("i"), date("s"), date("m"), date("d"), date("Y"));
					elseif ($recurType == "D")
						$recurSchemeVal = mktime(date("H"), date("i"), date("s"), date("m"), date("d") + $recurLength, date("Y"));
					elseif ($recurType == "W")
						$recurSchemeVal = mktime(date("H"), date("i"), date("s"), date("m"), date("d") + 7 * $recurLength, date("Y"));
					elseif ($recurType == "M")
						$recurSchemeVal = mktime(date("H"), date("i"), date("s"), date("m") + $recurLength, date("d"), date("Y"));
					elseif ($recurType == "Q")
						$recurSchemeVal = mktime(date("H"), date("i"), date("s"), date("m") + 3 * $recurLength, date("d"), date("Y"));
					elseif ($recurType == "S")
						$recurSchemeVal = mktime(date("H"), date("i"), date("s"), date("m") + 6 * $recurLength, date("d"), date("Y"));
					elseif ($recurType == "Y")
						$recurSchemeVal = mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y") + $recurLength);
					elseif ($recurType == "T")
						$recurSchemeVal = mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y") + 2 * $recurLength);

					$arFields = array(
							"USER_ID" => $userID,
							"MODULE" => "catalog",
							"PRODUCT_ID" => $productID,
							"PRODUCT_NAME" => $arIBlockElement["~NAME"],
							"PRODUCT_URL" => $arIBlockElement["DETAIL_PAGE_URL"],
							"PRODUCT_PRICE_ID" => false,
							"PRICE_TYPE" => $arProduct["PRICE_TYPE"],
							"RECUR_SCHEME_TYPE" => $recurType,
							"RECUR_SCHEME_LENGTH" => $recurLength,
							"WITHOUT_ORDER" => $arProduct["WITHOUT_ORDER"],
							"PRICE" => false,
							"CURRENCY" => false,
							"CANCELED" => "N",
							"CANCELED_REASON" => false,
							"PRODUCT_PROVIDER_CLASS" => "CCatalogProductProvider",
							"DESCRIPTION" => false,
							"PRIOR_DATE" => false,
							"NEXT_DATE" => Date(
									$DB->DateFormatToPHP(CLang::GetDateFormat("FULL", SITE_ID)),
									$recurSchemeVal
								)
						);

					return $arFields;
				}
			}
		}

		return true;
	}

	return false;
}

function CatalogRecurringCallback($productID, $userID)
{
	global $APPLICATION;
	global $USER;
	global $DB;

	$productID = intval($productID);
	if ($productID <= 0)
		return false;

	$userID = intval($userID);
	if ($userID <= 0)
		return false;

	$arProduct = CCatalogProduct::GetByID($productID);
	if (!$arProduct)
	{
		$APPLICATION->ThrowException(str_replace("#ID#", $productID, GetMessage("I_NO_PRODUCT")), "NO_PRODUCT");
		return false;
	}

	if ($arProduct["PRICE_TYPE"] == "T")
	{
		$arProduct = CCatalogProduct::GetByID($arProduct["TRIAL_PRICE_ID"]);
		if (!$arProduct)
		{
			$APPLICATION->ThrowException(str_replace("#TRIAL_ID#", $productID, str_replace("#ID#", $arProduct["TRIAL_PRICE_ID"], GetMessage("I_NO_TRIAL_PRODUCT"))), "NO_PRODUCT_TRIAL");
			return false;
		}
	}
	$productID = intval($arProduct["ID"]);

	if ($arProduct["PRICE_TYPE"] != "R")
	{
		$APPLICATION->ThrowException(str_replace("#ID#", $productID, GetMessage("I_PRODUCT_NOT_SUBSCR")), "NO_IBLOCK_SUBSCR");
		return false;
	}

	$dbIBlockElement = CIBlockElement::GetList(
		array(),
		array(
			"ID" => $productID,
			"ACTIVE" => "Y",
			"ACTIVE_DATE" => "Y",
			"CHECK_PERMISSIONS" => "N",
		),
		false,
		false,
		array('ID', 'IBLOCK_ID', 'NAME', 'DETAIL_PAGE_URL')
	);
	if(!($arIBlockElement = $dbIBlockElement->GetNext()))
	{
		$APPLICATION->ThrowException(str_replace("#ID#", $productID, GetMessage("I_NO_IBLOCK_ELEM")), "NO_IBLOCK_ELEMENT");
		return false;
	}
	if ('E' == CIBlock::GetArrayByID($arIBlockElement['IBLOCK_ID'], "RIGHTS_MODE"))
	{
		$arUserRights = CIBlockElementRights::GetUserOperations($productID, $userID);
		if (empty($arUserRights))
		{
			$APPLICATION->ThrowException(str_replace("#ID#", $productID, GetMessage("I_NO_IBLOCK_ELEM")), "NO_IBLOCK_ELEMENT");
			return false;
		}
		elseif (!is_array($arUserRights) || !array_key_exists('element_read', $arUserRights))
		{
			$APPLICATION->ThrowException(str_replace("#ID#", $productID, GetMessage("I_NO_IBLOCK_ELEM")), "NO_IBLOCK_ELEMENT");
			return false;
		}
	}
	else
	{
		if ('R' > CIBlock::GetPermission($arIBlockElement['IBLOCK_ID'], $userID))
		{
			$APPLICATION->ThrowException(str_replace("#ID#", $productID, GetMessage("I_NO_IBLOCK_ELEM")), "NO_IBLOCK_ELEMENT");
			return false;
		}
	}

	$arCatalog = CCatalog::GetByID($arIBlockElement["IBLOCK_ID"]);
	if ($arCatalog["SUBSCRIPTION"] != "Y")
	{
		$APPLICATION->ThrowException(str_replace("#ID#", $arIBlockElement["IBLOCK_ID"], GetMessage("I_CATALOG_NOT_SUBSCR")), "NOT_SUBSCRIPTION");
		return false;
	}

	if ($arProduct["CAN_BUY_ZERO"]!="Y" && ($arProduct["QUANTITY_TRACE"] == "Y" && doubleval($arProduct["QUANTITY"]) <= 0))
	{
		$APPLICATION->ThrowException(str_replace("#ID#", $productID, GetMessage("I_PRODUCT_SOLD")), "PRODUCT_END");
		return false;
	}

	$arUserGroups = CUser::GetUserGroup($userID);
	$arUserGroups = array_values(array_unique($arUserGroups));

	CCatalogDiscountSave::Disable();

	$arPrice = CCatalogProduct::GetOptimalPrice($productID, 1, $arUserGroups, "Y");
	if (empty($arPrice))
	{
		if ($nearestQuantity = CCatalogProduct::GetNearestQuantityPrice($productID, 1, $arUserGroups))
		{
			$quantity = $nearestQuantity;
			$arPrice = CCatalogProduct::GetOptimalPrice($productID, $quantity, $arUserGroups, "Y");
		}
	}

	CCatalogDiscountSave::Enable();

	if (empty($arPrice))
	{
		return false;
	}

	$currentPrice = $arPrice["PRICE"]["PRICE"];
	$currentDiscount = 0.0;

	//SIGURD: logic change. see mantiss 5036.
	// discount applied to a final price with VAT already included.
	if (doubleval($arPrice['PRICE']['VAT_RATE']) > 0)
		$currentPrice *= (1 + $arPrice['PRICE']['VAT_RATE']);

	$arDiscountList = array();

	if (!empty($arPrice["DISCOUNT_LIST"]))
	{
		$dblStartPrice = $currentPrice;

		foreach ($arPrice["DISCOUNT_LIST"] as &$arOneDiscount)
		{
			switch ($arOneDiscount['VALUE_TYPE'])
			{
			case 'F':
				if ($arOneDiscount['CURRENCY'] == $arPrice["PRICE"]["CURRENCY"])
					$currentDiscount = $arOneDiscount['VALUE'];
				else
					$currentDiscount = CCurrencyRates::ConvertCurrency($arOneDiscount["VALUE"], $arOneDiscount["CURRENCY"], $arPrice["PRICE"]["CURRENCY"]);
				$currentPrice = $currentPrice - $currentDiscount;
				break;
			case 'P':
				$currentDiscount = $currentPrice*$arOneDiscount["VALUE"]/100.0;
				if (0 < $arOneDiscount['MAX_DISCOUNT'])
				{
					if ($arOneDiscount['CURRENCY'] == $arPrice["PRICE"]["CURRENCY"])
						$dblMaxDiscount = $arOneDiscount['MAX_DISCOUNT'];
					else
						$dblMaxDiscount = CCurrencyRates::ConvertCurrency($arOneDiscount['MAX_DISCOUNT'], $arOneDiscount["CURRENCY"], $arPrice["PRICE"]["CURRENCY"]);;
					if ($currentDiscount > $dblMaxDiscount)
						$currentDiscount = $dblMaxDiscount;
				}
				$currentPrice = $currentPrice - $currentDiscount;
				break;
			case 'S':
				if ($arOneDiscount['CURRENCY'] == $arPrice["PRICE"]["CURRENCY"])
					$currentPrice = $arOneDiscount['VALUE'];
				else
					$currentPrice = CCurrencyRates::ConvertCurrency($arOneDiscount['VALUE'], $arOneDiscount["CURRENCY"], $arPrice["PRICE"]["CURRENCY"]);
				break;
			}

			$arOneList = array(
				'ID' => $arOneDiscount['ID'],
				'NAME' => $arOneDiscount['NAME'],
				'COUPON' => '',
				'MODULE_ID' => 'catalog',
			);

			if ($arOneDiscount['COUPON'])
			{
				$arOneList['COUPON'] = $arOneDiscount['COUPON'];
				$dbRes = CCatalogDiscountCoupon::GetList(array(), array('COUPON' => $arOneDiscount['COUPON'], 'ONE_TIME' => 'Y'), false, array('nTopCount' => 1), array('ID'));

				if ($arRes = $dbRes->Fetch())
				{
					$CATALOG_ONETIME_COUPONS_BASKET[$arOneDiscount['COUPON']] = $productID;
				}
			}
			$arDiscountList[] = $arOneList;
		}
		if (isset($arOneDiscount))
			unset($arOneDiscount);

		$currentDiscount = $dblStartPrice - $currentPrice;
	}

	$recurType = $arProduct["RECUR_SCHEME_TYPE"];
	$recurLength = intval($arProduct["RECUR_SCHEME_LENGTH"]);

	$recurSchemeVal = 0;
	if ($recurType == "H")
		$recurSchemeVal = mktime(date("H") + $recurLength, date("i"), date("s"), date("m"), date("d"), date("Y"));
	elseif ($recurType == "D")
		$recurSchemeVal = mktime(date("H"), date("i"), date("s"), date("m"), date("d") + $recurLength, date("Y"));
	elseif ($recurType == "W")
		$recurSchemeVal = mktime(date("H"), date("i"), date("s"), date("m"), date("d") + 7 * $recurLength, date("Y"));
	elseif ($recurType == "M")
		$recurSchemeVal = mktime(date("H"), date("i"), date("s"), date("m") + $recurLength, date("d"), date("Y"));
	elseif ($recurType == "Q")
		$recurSchemeVal = mktime(date("H"), date("i"), date("s"), date("m") + 3 * $recurLength, date("d"), date("Y"));
	elseif ($recurType == "S")
		$recurSchemeVal = mktime(date("H"), date("i"), date("s"), date("m") + 6 * $recurLength, date("d"), date("Y"));
	elseif ($recurType == "Y")
		$recurSchemeVal = mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y") + $recurLength);
	elseif ($recurType == "T")
		$recurSchemeVal = mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y") + 2 * $recurLength);

	$arResult = array(
		"WEIGHT" => $arProduct["WEIGHT"],
		"VAT_RATE" => $arPrice["PRICE"]["VAT_RATE"],
		"QUANTITY" => 1,
		"PRICE" => $currentPrice,
		"WITHOUT_ORDER" => $arProduct["WITHOUT_ORDER"],
		"PRODUCT_ID" => $productID,
		"PRODUCT_NAME" => $arIBlockElement["~NAME"],
		"PRODUCT_URL" => $arIBlockElement["DETAIL_PAGE_URL"],
		"PRODUCT_PRICE_ID" => $arPrice["PRICE"]["ID"],
		"CURRENCY" => $arPrice["PRICE"]["CURRENCY"],
		"NAME" => $arIBlockElement["NAME"],
		"MODULE" => "catalog",
		"PRODUCT_PROVIDER_CLASS" => "CCatalogProductProvider",
		"CATALOG_GROUP_NAME" => $arPrice["PRICE"]["CATALOG_GROUP_NAME"],
		"DETAIL_PAGE_URL" => $arIBlockElement["~DETAIL_PAGE_URL"],
		"PRICE_TYPE" => $arProduct["PRICE_TYPE"],
		"RECUR_SCHEME_TYPE" => $arProduct["RECUR_SCHEME_TYPE"],
		"RECUR_SCHEME_LENGTH" => $arProduct["RECUR_SCHEME_LENGTH"],
		"PRODUCT_XML_ID" => $arIBlockElement["XML_ID"],
		"NEXT_DATE" => Date(
				$DB->DateFormatToPHP(CLang::GetDateFormat("FULL", SITE_ID)),
				$recurSchemeVal
			)
	);
	if (!empty($arPrice["DISCOUNT_LIST"]))
	{
		$arResult['DISCOUNT_LIST'] = $arDiscountList;
	}

	return $arResult;
}

function CatalogBasketCancelCallback($PRODUCT_ID, $QUANTITY, $bCancel)
{
	$PRODUCT_ID = intval($PRODUCT_ID);
	$QUANTITY = doubleval($QUANTITY);
	$bCancel = ($bCancel ? true : false);

	if ($bCancel)
		CCatalogProduct::QuantityTracer($PRODUCT_ID, -$QUANTITY);
	else
	{
		CCatalogProduct::QuantityTracer($PRODUCT_ID, $QUANTITY);
	}
}

function Add2Basket($PRICE_ID, $QUANTITY = 1, $arRewriteFields = array(), $arProductParams = array())
{
	global $APPLICATION;

	$PRICE_ID = intval($PRICE_ID);
	if (0 >= $PRICE_ID)
		return false;
	$QUANTITY = doubleval($QUANTITY);
	if (0 >= $QUANTITY)
		$QUANTITY = 1;

	if (!CModule::IncludeModule("sale"))
		return false;
	if (CModule::IncludeModule("statistic") && array_key_exists('SESS_SEARCHER_ID', $_SESSION) && 0 < intval($_SESSION["SESS_SEARCHER_ID"]))
	{
		$APPLICATION->ThrowException(GetMessage('CATALOG_ERR_SESS_SEARCHER'), "SESS_SEARCHER");
		return false;
	}

	$rsPrices = CPrice::GetList(
		array(),
		array('ID' => $PRICE_ID),
		false,
		false,
		array(
			'ID',
			'PRODUCT_ID',
			'PRICE',
			'CURRENCY',
			'CATALOG_GROUP_ID'
		)
	);
	if (!($arPrice = $rsPrices->Fetch()))
	{
		$APPLICATION->ThrowException(GetMessage('CATALOG_PRODUCT_PRICE_NOT_FOUND'), "NO_PRODUCT_PRICE");
		return false;
	}
	$arPrice['CATALOG_GROUP_NAME'] = '';
	$rsCatGroups = CCatalogGroup::GetListEx(
		array(),
		array('ID' => $arPrice['CATALOG_GROUP_ID']),
		false,
		false,
		array(
			'ID',
			'NAME',
			'NAME_LANG'
		)
	);
	if ($arCatGroup = $rsCatGroups->Fetch())
	{
		$arPrice['CATALOG_GROUP_NAME'] = (!empty($arCatGroup['NAME_LANG']) ? $arCatGroup['NAME_LANG'] : $arCatGroup['NAME']);
	}
	$rsProducts = CCatalogProduct::GetList(
		array(),
		array('ID' => $arPrice["PRODUCT_ID"]),
		false,
		false,
		array(
			'ID',
			'CAN_BUY_ZERO',
			'QUANTITY_TRACE',
			'QUANTITY',
			'WEIGHT',
		)
	);
	if (!($arCatalogProduct = $rsProducts->Fetch()))
		return false;

	$dblQuantity = doubleval($arCatalogProduct["QUANTITY"]);
	$intQuantity = intval($arCatalogProduct["QUANTITY"]);
	$boolQuantity = ('Y' != $arCatalogProduct["CAN_BUY_ZERO"] && 'Y' == $arCatalogProduct["QUANTITY_TRACE"]);
	if ($boolQuantity && 0 >= $dblQuantity)
	{
		$APPLICATION->ThrowException(GetMessage('CATALOG_ERR_PRODUCT_RUN_OUT'), "PRODUCT_RUN_OUT");
		return false;
	}

	$rsItems = CIBlockElement::GetList(
		array(),
		array(
			"ID" => $arPrice["PRODUCT_ID"],
			"ACTIVE" => "Y",
			"ACTIVE_DATE" => "Y",
			"CHECK_PERMISSIONS" => "Y",
			"MIN_PERMISSION" => "R"
		),
		false,
		false,
		array(
			'ID',
			'IBLOCK_ID',
			'NAME',
			'XML_ID',
			'DETAIL_PAGE_URL'
		)
	);
	if (!($arProduct = $rsItems->Fetch()))
	{
		$APPLICATION->ThrowException(GetMessage('CATALOG_ERR_NO_PRODUCT'), "NO_PRODUCT");
		return false;
	}

	$arProps = array();

	$strIBlockXmlID = strval(CIBlock::GetArrayByID($arProduct['IBLOCK_ID'], 'XML_ID'));
	if ('' != $strIBlockXmlID)
	{
		$arProps[] = array(
			"NAME" => "Catalog XML_ID",
			"CODE" => "CATALOG.XML_ID",
			"VALUE" => $strIBlockXmlID
		);
	}

	$arProps[] = array(
		"NAME" => "Product XML_ID",
		"CODE" => "PRODUCT.XML_ID",
		"VALUE" => $arProduct["XML_ID"]
	);

	if (!empty($arProductParams) && is_array($arProductParams))
	{
		foreach ($arProductParams as &$arOneProductParams)
		{
			$arProps[] = array(
				"NAME" => $arOneProductParams["NAME"],
				"CODE" => $arOneProductParams["CODE"],
				"VALUE" => $arOneProductParams["VALUE"],
				"SORT" => $arOneProductParams["SORT"],
			);
		}
		if (isset($arOneProductParams))
			unset($arOneProductParams);
	}

	$arFields = array(
		"PRODUCT_ID" => $arPrice["PRODUCT_ID"],
		"PRODUCT_PRICE_ID" => $PRICE_ID,
		"PRICE" => $arPrice["PRICE"],
		"CURRENCY" => $arPrice["CURRENCY"],
		"WEIGHT" => $arCatalogProduct["WEIGHT"],
		"QUANTITY" => ($boolQuantity && $dblQuantity < $QUANTITY ? $dblQuantity : $QUANTITY),
		"LID" => SITE_ID,
		"DELAY" => "N",
		"CAN_BUY" => "Y",
		"NAME" => $arProduct["NAME"],
		"MODULE" => "catalog",
		"PRODUCT_PROVIDER_CLASS" => "CCatalogProductProvider",
		"NOTES" => $arPrice["CATALOG_GROUP_NAME"],
		"DETAIL_PAGE_URL" => $arProduct["DETAIL_PAGE_URL"],
		"CATALOG_XML_ID" => $strIBlockXmlID,
		"PRODUCT_XML_ID" => $arProduct["XML_ID"],
		"PROPS" => $arProps,
	);

	if (!empty($arRewriteFields) && is_array($arRewriteFields))
	{
		$arFields = array_merge($arFields, $arRewriteFields);
	}
	$addres = CSaleBasket::Add($arFields);

	if (CModule::IncludeModule("statistic"))
		CStatistic::Set_Event("eStore", "add2basket", $arFields["PRODUCT_ID"]);

	return $addres;
}

function Add2BasketByProductID($PRODUCT_ID, $QUANTITY = 1, $arRewriteFields = array(), $arProductParams = false)
{
	global $APPLICATION;

	/* for old use */
	if (false === $arProductParams)
	{
		$arProductParams = $arRewriteFields;
		$arRewriteFields = array();
	}

	$boolRewrite = (!empty($arRewriteFields) && is_array($arRewriteFields));

	if ($boolRewrite && array_key_exists('SUBSCRIBE', $arRewriteFields) && 'Y' == $arRewriteFields['SUBSCRIBE'])
	{
		return SubscribeProduct($PRODUCT_ID, $arRewriteFields, $arProductParams);
	}

	$PRODUCT_ID = intval($PRODUCT_ID);
	if (0 >= $PRODUCT_ID)
	{
		$APPLICATION->ThrowException(GetMessage('CATALOG_ERR_EMPTY_PRODUCT_ID'), "EMPTY_PRODUCT_ID");
		return false;
	}

	$QUANTITY = doubleval($QUANTITY);
	if ($QUANTITY <= 0)
		$QUANTITY = 1;

	if (!CModule::IncludeModule("sale"))
	{
		$APPLICATION->ThrowException(GetMessage('CATALOG_ERR_NO_SALE_MODULE'), "NO_SALE_MODULE");
		return false;
	}

	if (CModule::IncludeModule("statistic") && array_key_exists('SESS_SEARCHER_ID', $_SESSION) && 0 < intval($_SESSION["SESS_SEARCHER_ID"]))
	{
		$APPLICATION->ThrowException(GetMessage('CATALOG_ERR_SESS_SEARCHER'), "SESS_SEARCHER");
		return false;
	}

	$rsProducts = CCatalogProduct::GetList(
		array(),
		array('ID' => $PRODUCT_ID),
		false,
		false,
		array(
			'ID',
			'CAN_BUY_ZERO',
			'QUANTITY_TRACE',
			'QUANTITY',
			'WEIGHT',
		)
	);
	if (!($arCatalogProduct = $rsProducts->Fetch()))
	{
		$APPLICATION->ThrowException(GetMessage('CATALOG_ERR_NO_PRODUCT'), "NO_PRODUCT");
		return false;
	}

	$dblQuantity = doubleval($arCatalogProduct["QUANTITY"]);
	$intQuantity = intval($arCatalogProduct["QUANTITY"]);
	$boolQuantity = ('Y' != $arCatalogProduct["CAN_BUY_ZERO"] && 'Y' == $arCatalogProduct["QUANTITY_TRACE"]);
	if ($boolQuantity && 0 >= $dblQuantity)
	{
		$APPLICATION->ThrowException(GetMessage('CATALOG_ERR_PRODUCT_RUN_OUT'), "PRODUCT_RUN_OUT");
		return false;
	}

	$rsItems = CIBlockElement::GetList(
		array(),
		array(
			"ID" => $PRODUCT_ID,
			"ACTIVE" => "Y",
			"ACTIVE_DATE" => "Y",
			"CHECK_PERMISSIONS" => "Y",
			"MIN_PERMISSION" => "R",
		),
		false,
		false,
		array(
			"ID",
			"IBLOCK_ID",
			"XML_ID",
			"NAME",
			"DETAIL_PAGE_URL",
		)
	);
	if (!($arProduct = $rsItems->Fetch()))
	{
		$APPLICATION->ThrowException(GetMessage('CATALOG_ERR_NO_IBLOCK_ELEMENT'), "NO_IBLOCK_ELEMENT");
		return false;
	}

	$strCallbackFunc = "CatalogBasketCallback";
	$strProductProviderClass = "CCatalogProductProvider";

	if ($boolRewrite)
	{
		if (array_key_exists('CALLBACK_FUNC', $arRewriteFields))
			$strCallbackFunc = $arRewriteFields['CALLBACK_FUNC'];
		if (array_key_exists('PRODUCT_PROVIDER_CLASS', $arRewriteFields))
			$strProductProviderClass = $arRewriteFields['PRODUCT_PROVIDER_CLASS'];
	}

	$arCallbackPrice = CSaleBasket::ReReadPrice($strCallbackFunc, "catalog", $PRODUCT_ID, $QUANTITY, "N", $strProductProviderClass);
	if (!is_array($arCallbackPrice) || empty($arCallbackPrice))
	{
		$APPLICATION->ThrowException(GetMessage('CATALOG_PRODUCT_PRICE_NOT_FOUND'), "NO_PRODUCT_PRICE");
		return false;
	}

	$arProps = array();

	$strIBlockXmlID = strval(CIBlock::GetArrayByID($arProduct['IBLOCK_ID'], 'XML_ID'));
	if ('' != $strIBlockXmlID)
	{
		$arProps[] = array(
			"NAME" => "Catalog XML_ID",
			"CODE" => "CATALOG.XML_ID",
			"VALUE" => $strIBlockXmlID
		);
	}

	$arProps[] = array(
		"NAME" => "Product XML_ID",
		"CODE" => "PRODUCT.XML_ID",
		"VALUE" => $arProduct["XML_ID"]
	);

	if (!empty($arProductParams) && is_array($arProductParams))
	{
		foreach ($arProductParams as &$arOneProductParams)
		{
			$arProps[] = array(
				"NAME" => $arOneProductParams["NAME"],
				"CODE" => $arOneProductParams["CODE"],
				"VALUE" => $arOneProductParams["VALUE"],
				"SORT" => $arOneProductParams["SORT"]
			);
		}
		if (isset($arOneProductParams))
			unset($arOneProductParams);
	}

	$arFields = array(
		"PRODUCT_ID" => $PRODUCT_ID,
		"PRODUCT_PRICE_ID" => $arCallbackPrice["PRODUCT_PRICE_ID"],
		"PRICE" => $arCallbackPrice["PRICE"],
		"CURRENCY" => $arCallbackPrice["CURRENCY"],
		"WEIGHT" => $arCatalogProduct["WEIGHT"],
		"QUANTITY" => ($boolQuantity && $dblQuantity < $QUANTITY ? $dblQuantity : $QUANTITY),
		"LID" => SITE_ID,
		"DELAY" => "N",
		"CAN_BUY" => "Y",
		"NAME" => $arProduct["NAME"],
		"MODULE" => "catalog",
		"PRODUCT_PROVIDER_CLASS" => "CCatalogProductProvider",
		"NOTES" => $arCallbackPrice["NOTES"],
		"DETAIL_PAGE_URL" => $arProduct["DETAIL_PAGE_URL"],
		"CATALOG_XML_ID" => $strIBlockXmlID,
		"PRODUCT_XML_ID" => $arProduct["XML_ID"],
		"VAT_RATE" => $arCallbackPrice['VAT_RATE'],
		"PROPS" => $arProps,
	);

	if ($boolRewrite)
	{
		$arFields = array_merge($arFields, $arRewriteFields);
	}

	$addres = CSaleBasket::Add($arFields);
	if ($addres)
	{
		if (CModule::IncludeModule("statistic"))
			CStatistic::Set_Event("sale2basket", "catalog", $arFields["DETAIL_PAGE_URL"]);
	}

	return $addres;
}

function SubscribeProduct($intProductID, $arRewriteFields = array(), $arProductParams = array())
{
	global $USER;

	if (!CCatalog::IsUserExists())
		return false;
	if (!$USER->IsAuthorized())
		return false;
	$intUserID = intval($USER->GetID());

	$intProductID = intval($intProductID);
	if (0 >= $intProductID)
	{
		$APPLICATION->ThrowException(GetMessage('CATALOG_ERR_EMPTY_PRODUCT_ID'), "EMPTY_PRODUCT_ID");
		return false;
	}

	if (!CModule::IncludeModule("sale"))
	{
		$APPLICATION->ThrowException(GetMessage('CATALOG_ERR_NO_SALE_MODULE'), "NO_SALE_MODULE");
		return false;
	}

	if (CModule::IncludeModule("statistic") && array_key_exists('SESS_SEARCHER_ID', $_SESSION) && 0 < intval($_SESSION["SESS_SEARCHER_ID"]))
	{
		$APPLICATION->ThrowException(GetMessage('CATALOG_ERR_SESS_SEARCHER'), "SESS_SEARCHER");
		return false;
	}

	$rsProducts = CCatalogProduct::GetList(
		array(),
		array('ID' => $intProductID),
		false,
		false,
		array(
			'ID',
			'WEIGHT',
		)
	);
	if (!($arCatalogProduct = $rsProducts->Fetch()))
	{
		$APPLICATION->ThrowException(GetMessage('CATALOG_ERR_NO_PRODUCT'), "NO_PRODUCT");
		return false;
	}

	$rsItems = CIBlockElement::GetList(
		array(),
		array(
			"ID" => $intProductID,
			"ACTIVE" => "Y",
			"ACTIVE_DATE" => "Y",
			"CHECK_PERMISSIONS" => "Y",
			"MIN_PERMISSION" => "R"
		),
		false,
		false,
		array(
			'ID',
			'IBLOCK_ID',
			'NAME',
			'XML_ID',
			'DETAIL_PAGE_URL'
		)
	);
	if (!($arProduct = $rsItems->Fetch()))
		return false;

	$arPrice = array(
		'PRICE' => 0.0,
		'CURRENCY' => CSaleLang::GetLangCurrency(SITE_ID),
		'VAT_RATE' => 0,
		'PRODUCT_PRICE_ID' => 0,
		'CATALOG_GROUP_NAME' => '',
	);
	$arBuyerGroups = $USER->GetUserGroupArray();
	$arSubscrPrice = CCatalogProduct::GetOptimalPrice($intProductID, 1, $arBuyerGroups, "N", array(), SITE_ID, array());
	if (!empty($arSubscrPrice) && is_array($arSubscrPrice))
	{
		$arPrice['PRICE'] = $arSubscrPrice['DISCOUNT_PRICE'];
		$arPrice['CURRENCY'] = CCurrency::GetBaseCurrency();
		$arPrice['VAT_RATE'] = $arSubscrPrice['PRICE']['VAT_RATE'];
		$arPrice['PRODUCT_PRICE_ID'] = $arSubscrPrice["PRICE"]["ID"];
		$arPrice['CATALOG_GROUP_NAME'] = $arSubscrPrice["PRICE"]["CATALOG_GROUP_NAME"];
	}

	$arProps = array();

	$strIBlockXmlID = strval(CIBlock::GetArrayByID($arProduct['IBLOCK_ID'], 'XML_ID'));
	if ('' != $strIBlockXmlID)
	{
		$arProps[] = array(
			"NAME" => "Catalog XML_ID",
			"CODE" => "CATALOG.XML_ID",
			"VALUE" => $strIBlockXmlID
		);
	}

	$arProps[] = array(
		"NAME" => "Product XML_ID",
		"CODE" => "PRODUCT.XML_ID",
		"VALUE" => $arProduct["XML_ID"]
	);

	if (!empty($arProductParams) && is_array($arProductParams))
	{
		foreach ($arProductParams as &$arOneProductParams)
		{
			$arProps[] = array(
				"NAME" => $arOneProductParams["NAME"],
				"CODE" => $arOneProductParams["CODE"],
				"VALUE" => $arOneProductParams["VALUE"],
				"SORT" => $arOneProductParams["SORT"],
			);
		}
		if (isset($arOneProductParams))
			unset($arOneProductParams);
	}

	$arFields = array(
		"PRODUCT_ID" => $intProductID,
		"PRODUCT_PRICE_ID" => $arPrice['PRODUCT_PRICE_ID'],
		"PRICE" => $arPrice['PRICE'],
		"CURRENCY" => $arPrice['CURRENCY'],
		"WEIGHT" => $arCatalogProduct["WEIGHT"],
		"QUANTITY" => 1,
		"LID" => SITE_ID,
		"DELAY" => "N",
		"CAN_BUY" => "N",
		"SUBSCRIBE" => "Y",
		"NAME" => $arProduct["NAME"],
		"MODULE" => "catalog",
		"PRODUCT_PROVIDER_CLASS" => "CCatalogProductProvider",
		"NOTES" => $arPrice["CATALOG_GROUP_NAME"],
		"DETAIL_PAGE_URL" => $arProduct["DETAIL_PAGE_URL"],
		"CATALOG_XML_ID" => $strIBlockXmlID,
		"PRODUCT_XML_ID" => $arProduct["XML_ID"],
		"PROPS" => $arProps,
	);

	if (!empty($arRewriteFields) && is_array($arRewriteFields))
	{
		if (array_key_exists('SUBSCRIBE', $arRewriteFields))
			unset($arRewriteFields['SUBSCRIBE']);
		if (array_key_exists('CAN_BUY', $arRewriteFields))
			unset($arRewriteFields['CAN_BUY']);
		if (array_key_exists('DELAY', $arRewriteFields))
			unset($arRewriteFields['DELAY']);
		if (!empty($arRewriteFields))
			$arFields = array_merge($arFields, $arRewriteFields);
	}

	$mxBasketID = CSaleBasket::Add($arFields);
	if ($mxBasketID)
	{
		if (!array_key_exists('NOTIFY_PRODUCT', $_SESSION))
		{
			$_SESSION['NOTIFY_PRODUCT'] = array(
				$intUserID = array(),
			);
		}
		elseif (!array_key_exists($intUserID, $_SESSION['NOTIFY_PRODUCT']))
		{
			$_SESSION['NOTIFY_PRODUCT'][$intUserID] = array();
		}
		$_SESSION["NOTIFY_PRODUCT"][$intUserID][$intProductID] = $intProductID;

		if (CModule::IncludeModule("statistic"))
			CStatistic::Set_Event("sale2basket", "subscribe", $intProductID);
	}
	return $mxBasketID;
}

function CatalogGetPriceTableEx($ID, $filterQauntity = 0, $arFilterType = array(), $VAT_INCLUDE = 'Y', $arCurrencyParams = array())
{
	global $USER;
	$ID = intval($ID);
	if ($ID <= 0)
		return False;

	$filterQauntity = intval($filterQauntity);

	if (!is_array($arFilterType))
		$arFilterType = array($arFilterType);

	$boolConvert = false;
	$strCurrencyID = '';
	$arCurrencyList = array();
	if (is_array($arCurrencyParams) && !empty($arCurrencyParams) && !empty($arCurrencyParams['CURRENCY_ID']))
	{
		$boolConvert = true;
		$strCurrencyID = $arCurrencyParams['CURRENCY_ID'];
	}

	$arResult = array();
	$arResult["ROWS"] = array();
	$arResult["COLS"] = array();
	$arResult["MATRIX"] = array();
	$arResult["CAN_BUY"] = array();
	$arResult["AVAILABLE"] = "N";

	$cacheTime = CATALOG_CACHE_DEFAULT_TIME;
	if (defined("CATALOG_CACHE_TIME"))
		$cacheTime = intval(CATALOG_CACHE_TIME);

	$arUserGroups = $USER->GetUserGroupArray();

	$arPriceGroups = CCatalogGroup::GetGroupsPerms($arUserGroups, array());

	if (empty($arPriceGroups["view"]))
		return $arResult;

	$currentQuantity = -1;
	$rowsCnt = -1;

	$arFilter = array("PRODUCT_ID" => $ID);
	if ($filterQauntity > 0)
	{
		$arFilter["+<=QUANTITY_FROM"] = $filterQauntity;
		$arFilter["+>=QUANTITY_TO"] = $filterQauntity;
	}
	if (!empty($arFilterType))
	{
		$arTmp = array();
		foreach ($arPriceGroups["view"] as &$intOneGroup)
		{
			if (in_array($intOneGroup, $arFilterType))
				$arTmp[] = $intOneGroup;
		}
		if (isset($intOneGroup))
			unset($intOneGroup);

		if (empty($arTmp))
			return $arResult;

		$arFilter["CATALOG_GROUP_ID"] = $arTmp;
	}
	else
	{
		$arFilter["CATALOG_GROUP_ID"] = $arPriceGroups["view"];
	}

	$productQuantity = 0;
	$productQuantityTrace = "N";

	$dbRes = CCatalogProduct::GetVATInfo($ID);
	if ($arVatInfo = $dbRes->Fetch())
	{
		$fVatRate = floatval($arVatInfo['RATE'] * 0.01);
		$bVatIncluded = $arVatInfo['VAT_INCLUDED'] == 'Y';
	}
	else
	{
		$fVatRate = 0.00;
		$bVatIncluded = false;
	}

	$dbPrice = CPrice::GetListEx(
		array("QUANTITY_FROM" => "ASC", "QUANTITY_TO" => "ASC"),
		$arFilter,
		false,
		false,
		array("ID", "CATALOG_GROUP_ID", "PRICE", "CURRENCY", "QUANTITY_FROM", "QUANTITY_TO", "PRODUCT_QUANTITY", "PRODUCT_QUANTITY_TRACE", "PRODUCT_CAN_BUY_ZERO", "ELEMENT_IBLOCK_ID")
	);

	while ($arPrice = $dbPrice->Fetch())
	{
		if ($VAT_INCLUDE == 'N')
		{
			if ($bVatIncluded)
				$arPrice['PRICE'] /= (1 + $fVatRate);
		}
		else
		{
			if (!$bVatIncluded)
				$arPrice['PRICE'] *= (1 + $fVatRate);
		}

		$arPrice['VAT_RATE'] = $fVatRate;

		CCatalogDiscountSave::Disable();
		$arDiscounts = CCatalogDiscount::GetDiscount($ID, $arPrice["ELEMENT_IBLOCK_ID"], $arPrice["CATALOG_GROUP_ID"], $arUserGroups, "N", SITE_ID);
		CCatalogDiscountSave::Enable();

		$discountPrice = CCatalogProduct::CountPriceWithDiscount($arPrice["PRICE"], $arPrice["CURRENCY"], $arDiscounts);
		$arPrice["DISCOUNT_PRICE"] = $discountPrice;

		$productQuantity = $arPrice["PRODUCT_QUANTITY"];
		$productQuantityTrace = $arPrice["PRODUCT_QUANTITY_TRACE"];
		$productUseStoreFlag = $arPrice["PRODUCT_CAN_BUY_ZERO"];

		$arPrice["QUANTITY_FROM"] = DoubleVal($arPrice["QUANTITY_FROM"]);
		if ($currentQuantity != $arPrice["QUANTITY_FROM"])
		{
			$rowsCnt++;
			$arResult["ROWS"][$rowsCnt]["QUANTITY_FROM"] = $arPrice["QUANTITY_FROM"];
			$arResult["ROWS"][$rowsCnt]["QUANTITY_TO"] = DoubleVal($arPrice["QUANTITY_TO"]);
			$currentQuantity = $arPrice["QUANTITY_FROM"];
		}

		if ($boolConvert && $strCurrencyID != $arPrice["CURRENCY"])
		{
			$arResult["MATRIX"][intval($arPrice["CATALOG_GROUP_ID"])][$rowsCnt] = array(
				"ID" => $arPrice["ID"],
				"ORIG_PRICE" => $arPrice["PRICE"],
				"ORIG_DISCOUNT_PRICE" => $arPrice["DISCOUNT_PRICE"],
				"ORIG_CURRENCY" => $arPrice["CURRENCY"],
				"ORIG_VAT_RATE" => $arPrice["VAT_RATE"],
				'PRICE' => CCurrencyRates::ConvertCurrency($arPrice["PRICE"], $arPrice["CURRENCY"], $strCurrencyID),
				'DISCOUNT_PRICE' => CCurrencyRates::ConvertCurrency($arPrice["DISCOUNT_PRICE"], $arPrice["CURRENCY"], $strCurrencyID),
				'CURRENCY' => $strCurrencyID,
				'VAT_RATE' => CCurrencyRates::ConvertCurrency($arPrice["VAT_RATE"], $arPrice["CURRENCY"], $strCurrencyID),
			);
			$arCurrencyList[] = $arPrice["CURRENCY"];
		}
		else
		{
			$arResult["MATRIX"][intval($arPrice["CATALOG_GROUP_ID"])][$rowsCnt] = array(
				"ID" => $arPrice["ID"],
				"PRICE" => $arPrice["PRICE"],
				"DISCOUNT_PRICE" => $arPrice["DISCOUNT_PRICE"],
				"CURRENCY" => $arPrice["CURRENCY"],
				"VAT_RATE" => $arPrice["VAT_RATE"]
			);
		}
	}

	$colsCnt = -1;
	$arCatalogGroups = CCatalogGroup::GetListArray();
	foreach ($arCatalogGroups as $key => $value)
	{
		if (array_key_exists($key, $arResult["MATRIX"]))
			$arResult["COLS"][$value["ID"]] = $value;
	}

	$arResult["CAN_BUY"] = $arPriceGroups["buy"];
	$arResult["AVAILABLE"] = (($productUseStoreFlag == 'Y' || ($productQuantityTrace == "N" || $productQuantityTrace == "Y" && $productQuantity > 0)) ? "Y" : "N");

	if ($boolConvert)
	{
		if (!empty($arCurrencyList))
			$arCurrencyList[] = $strCurrencyID;
		$arResult['CURRENCY_LIST'] = $arCurrencyList;
	}

	return $arResult;
}

function CatalogGetPriceTable($ID)
{
	global $USER;

	$ID = IntVal($ID);
	if ($ID <= 0)
		return False;

	$arResult = array();

	$arPriceGroups = array();
	$cacheKey = LANGUAGE_ID."_".($USER->GetGroups());
	if (isset($GLOBALS["CATALOG_PRICE_GROUPS_CACHE"])
		&& is_array($GLOBALS["CATALOG_PRICE_GROUPS_CACHE"])
		&& isset($GLOBALS["CATALOG_PRICE_GROUPS_CACHE"][$cacheKey])
		&& is_array($GLOBALS["CATALOG_PRICE_GROUPS_CACHE"][$cacheKey]))
	{
		$arPriceGroups = $GLOBALS["CATALOG_PRICE_GROUPS_CACHE"][$cacheKey];
	}
	else
	{
		$dbPriceGroupsList = CCatalogGroup::GetList(
			array("SORT" => "ASC"),
			array(
				"CAN_ACCESS" => "Y",
				"LID" => LANGUAGE_ID
			),
			array("ID", "NAME_LANG", "SORT"),
			false,
			array("ID", "NAME_LANG", "CAN_BUY", "SORT")
		);
		while ($arPriceGroupsList = $dbPriceGroupsList->Fetch())
		{
			$arPriceGroups[] = $arPriceGroupsList;
			$GLOBALS["CATALOG_PRICE_GROUPS_CACHE"][$cacheKey][] = $arPriceGroupsList;
		}
	}

	if (empty($arPriceGroups))
		return false;

	$arBorderMap = array();
	$arPresentGroups = array();
	$bMultiQuantity = False;

	$dbPrice = CPrice::GetList(
		array("QUANTITY_FROM" => "ASC", "QUANTITY_TO" => "ASC", "SORT" => "ASC"),
		array("PRODUCT_ID" => $ID),
		false,
		false,
		array("ID", "CATALOG_GROUP_ID", "PRICE", "CURRENCY", "QUANTITY_FROM", "QUANTITY_TO", "ELEMENT_IBLOCK_ID", "SORT")
	);
	while ($arPrice = $dbPrice->Fetch())
	{
		CCatalogDiscountSave::Disable();
		$arDiscounts = CCatalogDiscount::GetDiscount($ID, $arPrice["ELEMENT_IBLOCK_ID"], $arPrice["CATALOG_GROUP_ID"], $USER->GetUserGroupArray(), "N", SITE_ID);
		CCatalogDiscountSave::Enable();

		$discountPrice = CCatalogProduct::CountPriceWithDiscount($arPrice["PRICE"], $arPrice["CURRENCY"], $arDiscounts);
		$arPrice["DISCOUNT_PRICE"] = $discountPrice;

		if (array_key_exists($arPrice["QUANTITY_FROM"]."-".$arPrice["QUANTITY_TO"], $arBorderMap))
			$jnd = $arBorderMap[$arPrice["QUANTITY_FROM"]."-".$arPrice["QUANTITY_TO"]];
		else
		{
			$jnd = count($arBorderMap);
			$arBorderMap[$arPrice["QUANTITY_FROM"]."-".$arPrice["QUANTITY_TO"]] = $jnd;
		}

		$arResult[$jnd]["QUANTITY_FROM"] = DoubleVal($arPrice["QUANTITY_FROM"]);
		$arResult[$jnd]["QUANTITY_TO"] = DoubleVal($arPrice["QUANTITY_TO"]);
		if (DoubleVal($arPrice["QUANTITY_FROM"]) > 0 || DoubleVal($arPrice["QUANTITY_TO"]) > 0)
			$bMultiQuantity = True;

		$arResult[$jnd]["PRICE"][$arPrice["CATALOG_GROUP_ID"]] = $arPrice;
	}

	$numGroups = count($arPriceGroups);
	for ($i = 0; $i < $numGroups; $i++)
	{
		$bNeedKill = True;
		for ($j = 0, $intCount = count($arResult); $j < $intCount; $j++)
		{
			if (!array_key_exists($arPriceGroups[$i]["ID"], $arResult[$j]["PRICE"]))
				$arResult[$j]["PRICE"][$arPriceGroups[$i]["ID"]] = False;

			if ($arResult[$j]["PRICE"][$arPriceGroups[$i]["ID"]] != false)
				$bNeedKill = False;
		}

		if ($bNeedKill)
		{
			for ($j = 0, $intCount = count($arResult); $j < $intCount; $j++)
				unset($arResult[$j]["PRICE"][$arPriceGroups[$i]["ID"]]);

			unset($arPriceGroups[$i]);
		}
	}

	return array(
		"COLS" => $arPriceGroups,
		"MATRIX" => $arResult,
		"MULTI_QUANTITY" => ($bMultiQuantity ? "Y" : "N")
	);
}

function __CatalogGetMicroTime()
{
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}

function __CatalogSetTimeMark($text, $startStop = "")
{
	global $__catalogTimeMarkTo, $__catalogTimeMarkFrom, $__catalogTimeMarkGlobalFrom;

	if (StrToUpper($startStop) == "START")
	{
		$hFile = fopen($_SERVER["DOCUMENT_ROOT"]."/__catalog_debug.txt", "a");
		fwrite($hFile, date("H:i:s")." - ".$text."\n");
		fclose($hFile);

		$__catalogTimeMarkGlobalFrom = __CatalogGetMicroTime();
		$__catalogTimeMarkFrom = __CatalogGetMicroTime();
	}
	elseif (StrToUpper($startStop) == "STOP")
	{
		$__catalogTimeMarkTo = __CatalogGetMicroTime();

		$hFile = fopen($_SERVER["DOCUMENT_ROOT"]."/__catalog_debug.txt", "a");
		fwrite($hFile, date("H:i:s")." - ".Round($__catalogTimeMarkTo - $__catalogTimeMarkFrom, 3)." s - ".$text."\n");
		fwrite($hFile, date("H:i:s")." - ".Round($__catalogTimeMarkTo - $__catalogTimeMarkGlobalFrom, 3)." s\n\n");
		fclose($hFile);
	}
	else
	{
		$__catalogTimeMarkTo = __CatalogGetMicroTime();

		$hFile = fopen($_SERVER["DOCUMENT_ROOT"]."/__catalog_debug.txt", "a");
		fwrite($hFile, date("H:i:s")." - ".Round($__catalogTimeMarkTo - $__catalogTimeMarkFrom, 3)." s - ".$text."\n");
		fclose($hFile);

		$__catalogTimeMarkFrom = __CatalogGetMicroTime();
	}
}

function CatalogGetVATArray($arFilter = array(), $bInsertEmptyLine = false)
{
	$bInsertEmptyLine = (true == $bInsertEmptyLine ? true : false);

	if (!is_array($arFilter))
		$arFilter = array();

	$arFilter['ACTIVE'] = 'Y';
	$dbResult = CCatalogVat::GetListEx(array(), $arFilter, false, false, array('ID', 'NAME'));

	$arReference = array();

	if ($bInsertEmptyLine)
		$arList = array('REFERENCE' => array(0 => GetMessage('CAT_VAT_REF_NOT_SELECTED')), 'REFERENCE_ID' => array(0 => ''));
	else
		$arList = array('REFERENCE' => array(), 'REFERENCE_ID' => array());

	$bEmpty = true;
	while ($arRes = $dbResult->Fetch())
	{
		$bEmpty = false;
		$arList['REFERENCE'][] = $arRes['NAME'];
		$arList['REFERENCE_ID'][] = $arRes['ID'];
	}

	if ($bEmpty && !$bInsertEmptyLine)
		return false;
	else
		return $arList;
}

function CurrencyModuleUnInstallCatalog()
{
	global $APPLICATION;
	$APPLICATION->ThrowException(GetMessage("CAT_INCLUDE_CURRENCY"), "CAT_DEPENDS_CURRENCY");
	return false;
}

function CatalogGenerateCoupon()
{
	foreach (GetModuleEvents("catalog", "OnGenerateCoupon", true) as $arEvent)
	{
		return ExecuteModuleEventEx($arEvent);
	}

	$allchars = 'ABCDEFGHIJKLNMOPQRSTUVWXYZ0123456789';
	$string1 = '';
	$string2 = '';
	for ($i = 0; $i < 5; $i++)
		$string1 .= substr($allchars, rand(0, StrLen($allchars) - 1), 1);

	for ($i = 0; $i < 7; $i++)
		$string2 .= substr($allchars, rand(0, StrLen($allchars) - 1), 1);

	return "CP-".$string1."-".$string2;
}

function __GetCatLangMessages($strBefore, $strAfter, $MessID, $strDefMess = false, $arLangList = array())
{
	$arResult = false;

	if (empty($MessID))
		return $arResult;
	if (!is_array($MessID))
		$MessID = array($MessID);
	if (!is_array($arLangList))
		$arLangList = array($arLangList);

	if (empty($arLangList))
	{
		$rsLangs = CLanguage::GetList(($by="lid"), ($order="asc"), array("ACTIVE" => "Y"));
		while ($arLang = $rsLangs->Fetch())
		{
			$arLangList[] = $arLang['LID'];
		}
	}
	foreach ($arLangList as &$strLID)
	{
		@include(GetLangFileName($strBefore, $strAfter, $strLID));
		foreach ($MessID as &$strMessID)
		{
			if (empty($strMessID))
				continue;
			$arResult[$strMessID][$strLID] = (isset($MESS[$strMessID]) ? $MESS[$strMessID] : $strDefMess);
		}
		if (isset($strMessID))
			unset($strMessID);
	}
	if (isset($strLID))
		unset($strLID);
	return $arResult;
}

function CatalogClearArray(&$arMap, $boolSort = true)
{
	if (empty($arMap) || !is_array($arMap))
		return;

	$boolSort = !!$boolSort;
	$arValues = array();
	foreach ($arMap as &$intOneValue)
	{
		$intOneValue = intval($intOneValue);
		if (0 < $intOneValue)
			$arValues[$intOneValue] = true;
	}
	if (isset($intOneValue))
		unset($intOneValue);
	if (!empty($arValues))
	{
		$arMap = array_keys($arValues);
		if ($boolSort)
			sort($arMap);
	}
	else
	{
		$arMap = array();
	}
}
?>