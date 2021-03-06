<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
if ($saleModulePermissions=="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

if (!($USER->CanDoOperation('catalog_read')))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");
IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/prolog.php");

if (!CModule::IncludeModule('catalog'))
	return;

ClearVars("str_iblock_");
ClearVars("s_");

function _ShowGroupPropertyFieldList($name, $property_fields, $values)
{
	if(!is_array($values)) $values = Array();

	$res = "";
	$result = "";
	$bWas = false;
	$sections = CIBlockSection::GetTreeList(Array("IBLOCK_ID"=>$property_fields["LINK_IBLOCK_ID"]));
	while($ar = $sections->GetNext())
	{
		$res .= '<option value="'.$ar["ID"].'"';
		if(in_array($ar["ID"], $values))
		{
			$bWas = true;
			$res .= ' selected';
		}
		$res .= '>'.str_repeat(" . ", $ar["DEPTH_LEVEL"]).$ar["NAME"].'</option>';
	}
	$result .= '<select name="'.$name.'[]" size="'.($property_fields["MULTIPLE"]=="Y" ? "5":"1").'" '.($property_fields["MULTIPLE"]=="Y"?"multiple":"").'>';
	$result .= '<option value=""'.(!$bWas?' selected':'').'>'.GetMessage("SPS_A_PROP_NOT_SET").'</option>';
	$result .= $res;
	$result .= '</select>';
	return $result;
}

$adminMenu->Init('iblock');

$addDefault = ($addDefault == "N") ? "N" : "Y";

$iblockID = intval($IBLOCK_ID);

$LID = htmlspecialcharsbx($LID);
if (strlen($LID) <= 0)
	$LID = false;

$func_name = preg_replace("/[^a-zA-Z0-9_-]/is", "", $func_name);

$sTableID = "tbl_sale_product_search";
$oSort = new CAdminSorting($sTableID, "ID", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

$dbIBlock = CIBlock::GetByID($iblockID);
if (!($arIBlock = $dbIBlock->Fetch()))
{
	$arFilterTmp = array("MIN_PERMISSION"=>"R");

	if (strlen($LID) > 0)
		$arFilterTmp["LID"] = $LID;

	$dbItem = CCatalog::GetList();
	while($arItems = $dbItem->Fetch())
		$arFilterTmp["ID"][] = $arItems["IBLOCK_ID"];

	foreach(GetModuleEvents("sale", "OnProductSearchFormIBlock", true) as $arEvent)
		$arFilterTmp = ExecuteModuleEventEx($arEvent, Array($arFilterTmp));

	$dbIBlock = CIBlock::GetList(Array("ID"=>"ASC"), $arFilterTmp);
	if($arIBlock = $dbIBlock->Fetch())
		$iblockID = intval($arIBlock["ID"]);
	else
	{
		unset($arFilterTmp["LID"]);
		$dbIBlock = CIBlock::GetList(Array("ID"=>"ASC"), $arFilterTmp);
		if($arIBlock = $dbIBlock->Fetch())
			$iblockID = intval($arIBlock["ID"]);
	}
}

$bBadBlock = !CIBlockRights::UserHasRightTo($iblockID, $iblockID, "iblock_admin_display");

$BUYER_ID = intval($BUYER_ID);
$arBuyerGroups = CUser::GetUserGroup($BUYER_ID);

$QUANTITY = intval($QUANTITY);
if ($QUANTITY <= 0)
	$QUANTITY = 1;


if (!$bBadBlock)
{
	$arFilterFields = array(
		"IBLOCK_ID",
		"filter_section",
		"filter_subsections",
		"filter_id_start",
		"filter_id_end",
		"filter_timestamp_from",
		"filter_timestamp_to",
		"filter_intext",
		"filter_product_name",
		"filter_xml_id",
		"filter_code"
	);
	$lAdmin->InitFilter($arFilterFields);

	//filter props
	$dbrFProps = CIBlockProperty::GetList(
		array(
			"SORT"=>"ASC",
			"NAME"=>"ASC"
		),
		array(
			"IBLOCK_ID"=>$iblockID,
			"ACTIVE"=>"Y",
			"FILTRABLE"=>"Y",
			"!PROPERTY_TYPE" => "F",
			"CHECK_PERMISSIONS"=>"N",
		)
	);

	$arProps = array();
	while($arProp = $dbrFProps->GetNext())
	{
		$arProp["PROPERTY_USER_TYPE"] = (!empty($arProp["USER_TYPE"]) ? CIBlockProperty::GetUserType($arProp["USER_TYPE"]) : array());
		$arProps[] = $arProp;
	}

	//filter sku props
	$arSKUProps = array();
	$arCatalog = CCatalogSKU::GetInfoByProductIBlock($iblockID);
	if (!empty($arCatalog) && is_array($arCatalog))
	{
		$dbrFProps = CIBlockProperty::GetList(
			array(
				"SORT"=>"ASC",
				"NAME"=>"ASC"
			),
			array(
				"IBLOCK_ID"=>$arCatalog["IBLOCK_ID"],
				"ACTIVE"=>"Y",
				"FILTRABLE"=>"Y",
				"!PROPERTY_TYPE" => "F",
				"CHECK_PERMISSIONS"=>"N",
			)
		);

		while($arProp = $dbrFProps->GetNext())
		{
			if ($arCatalog['SKU_PROPERTY_ID'] == $arProp['ID'])
				continue;
			$arProp["PROPERTY_USER_TYPE"] = (!empty($arProp["USER_TYPE"]) ? CIBlockProperty::GetUserType($arProp["USER_TYPE"]) : array());
			$arSKUProps[] = $arProp;
		}
	}

	$arFilter = array(
		"IBLOCK_ID" => $iblockID,
		"SECTION_ID" => $filter_section,
		"ACTIVE" => 'Y',
		"WF_PARENT_ELEMENT_ID" => false,
	);
	if ('' != trim($filter_product_name))
		$arFilter["%NAME"] = $filter_product_name;
	if ('' != trim($filter_intext))
		$arFilter["%SEARCHABLE_CONTENT"] = $filter_intext;
	$arFilter["SHOW_NEW"] = "Y";

	if (!empty($arProps))
	{
		foreach($arProps as &$arProp)
		{
			$value = ${"filter_el_property_".$arProp["ID"]};

			if(array_key_exists("AddFilterFields", $arProp["PROPERTY_USER_TYPE"]))
			{
				call_user_func_array($arProp["PROPERTY_USER_TYPE"]["AddFilterFields"], array(
					$arProp,
					array("VALUE" => "filter_el_property_".$arProp["ID"]),
					&$arFilter,
					&$filtered,
				));
			}
			elseif(is_array($value) || strlen($value))
			{
				if($value === "NOT_REF")
					$value = false;
				$arFilter["?PROPERTY_".$arProp["ID"]] = $value;
			}
		}
		if (isset($arProp))
			unset($arProp);
	}

	if (!empty($arSKUProps))
	{
		$arSubQuery = array("IBLOCK_ID" => $arCatalog['IBLOCK_ID']);

		foreach ($arSKUProps as &$arOneSKUProp)
		{
			if (('Y' == $arOneSKUProp["FILTRABLE"]) && ('F' != $arOneSKUProp["PROPERTY_TYPE"]) && ($arCatalog['SKU_PROPERTY_ID'] != $arOneSKUProp["ID"]))
			{
				if (array_key_exists("AddFilterFields", $arOneSKUProp["PROPERTY_USER_TYPE"]))
				{
					call_user_func_array($arOneSKUProp["PROPERTY_USER_TYPE"]["AddFilterFields"], array(
						$arOneSKUProp,
						array("VALUE" => "filter_sub_el_property_".$arOneSKUProp["ID"]),
						&$arSubQuery,
						&$filtered,
					));
				}
				else
				{
					$value = ${"filter_sub_el_property_".$arOneSKUProp["ID"]};
					if(strlen($value) || is_array($value))
					{
						if($value === "NOT_REF")
							$value = false;
						$arSubQuery["?PROPERTY_".$arOneSKUProp["ID"]] = $value;
					}
				}
			}
		}
		if (isset($arOneSKUProp))
			unset($arOneSKUProp);
	}

	if (!empty($arSKUProps) && count($arSubQuery) > 1)
	{
		$arFilter['ID'] = CIBlockElement::SubQuery('PROPERTY_'.$arCatalog['SKU_PROPERTY_ID'], $arSubQuery);
	}

	if (intval($filter_section) < 0 || strlen($filter_section) <= 0)
		unset($arFilter["SECTION_ID"]);
	elseif ($filter_subsections=="Y")
	{
		if ($arFilter["SECTION_ID"]==0)
			unset($arFilter["SECTION_ID"]);
		else
			$arFilter["INCLUDE_SUBSECTIONS"] = "Y";
	}

	if (!empty(${"filter_id_start"})) $arFilter[">=ID"] = ${"filter_id_start"};
	if (!empty(${"filter_id_end"})) $arFilter["<=ID"] = ${"filter_id_end"};
	if (!empty(${"filter_timestamp_from"})) $arFilter["DATE_MODIFY_FROM"] = ${"filter_timestamp_from"};
	if (!empty(${"filter_timestamp_to"})) $arFilter["DATE_MODIFY_TO"] = ${"filter_timestamp_to"};
	if (!empty(${"filter_xml_id"})) $arFilter["XML_ID"] = ${"filter_xml_id"};
	if (!empty(${"filter_code"})) $arFilter["CODE"] = ${"filter_code"};

	//select subsection
	if ($arFilter["SECTION_ID"] > 0)
		$arFilter["INCLUDE_SUBSECTIONS"] = "Y";

	$arNavParams = array("nPageSize"=>CAdminResult::GetNavSize($sTableID));

	$dbResultList = CIBlockElement::GetList(
		array($by => $order),
		$arFilter,
		false,
		$arNavParams,
		array('ID', 'NAME', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'PREVIEW_PICTURE', 'DETAIL_PICTURE', 'DETAIL_PAGE_URL', 'CODE', 'XML_ID')
	);

	$dbResultList = new CAdminResult($dbResultList, $sTableID);
	$dbResultList->NavStart();

	$lAdmin->NavText($dbResultList->GetNavPrint(GetMessage("sale_prod_search_nav")));

	$arHeaders = array(
		array("id"=>"ID", "content"=>"ID", "sort"=>"id", "default"=>true),
		array("id"=>"ACTIVE", "content"=>GetMessage("SOPS_ACTIVE"), "sort"=>"ACTIVE", "default"=>true),
		array("id"=>"NAME", "content"=>GetMessage("SPS_NAME"), "sort"=>"name", "default"=>true),
		array("id"=>"QUANTITY", "content"=>GetMessage("SOPS_QUANTITY"), "default"=>true),
		array("id"=>"BALANCE", "content"=>GetMessage("SOPS_BALANCE"), "sort"=>"", "default"=>true, "align" => "right"),
		array("id"=>"PRICE", "content"=>GetMessage("SOPS_PRICE"), "default"=>true, "align" => "right"),
		array("id"=>"ACT", "content"=>"&nbsp;", "default"=>true),
	);

	$lAdmin->AddHeaders($arHeaders);

	$arDiscountCoupons = array();

	if (strlen($LID) > 0)
	{
		$BASE_LANG_CURR = CSaleLang::GetLangCurrency($LID);
		$arCurFormat = CCurrencyLang::GetCurrencyFormat($BASE_LANG_CURR);
		$priceValutaFormat = str_replace("#", '', $arCurFormat["FORMAT_STRING"]);
	}

	$arSku = array();
	$OfferIblockId = "";
	$aReplace = array("\xe2\x80\xa9", "\\", "'", "\"", "\r\n", "\r", "\n", "\xe2\x80\xa8");
	$aSearch = array(" ", "\\\\", "\\'", '\\"', "\n", "\n", "\\n'+\n'", "\\n'+\n'");

	CCatalogDiscountSave::Disable();

	while ($arItems = $dbResultList->Fetch())
	{
		$row =& $lAdmin->AddRow($arItems["ID"], $arItems);
		$arResult = CSaleProduct::GetProductSku($BUYER_ID, $LID, $arItems["ID"], $arItems["NAME"], '', $arItems);

		if (!empty($arResult["SKU_ELEMENTS"]))
		{
			$OfferIblockId = $arResult["OFFERS_IBLOCK_ID"];

			$row->AddField("PRICE", GetMessage("SPS_PROCE_FROM")." ".$arResult["SKU_ELEMENTS"][0]["MIN_PRICE"]);
			$row->AddField("ACTIVE", '&nbsp;');
			$skuTmp = array();
			foreach ($arResult["SKU_ELEMENTS"] as $val)
			{
				$skuTmp[] = array("ID" => $val["ID"]);
			}
			$row->AddField("ACT", '<input type="button" onclick="fShowSku(this, '.CUtil::PhpToJSObject($skuTmp).');" name="btn_show_sku_'.$arItems["ID"].'" value="'.GetMessage("SPS_SKU_SHOW").'">');
			foreach ($arResult["SKU_ELEMENTS"] as $val)
			{
				$skuProperty = "";
				$arSkuProperty = array();
				foreach ($val as $kk => $vv)
				{
					if (is_int($kk) && '' != $vv)
					{
						if ($skuProperty != "")
							$skuProperty .= " <br> ";
						$vv =  str_replace($aSearch, $aReplace, $vv);

						$skuProperty .= '<span style="color: grey;">'.$arResult["SKU_PROPERTIES"][$kk]["NAME"].'</span>: '.$vv;
						$arSkuProperty[] = array(
							'NAME' => $arResult["SKU_PROPERTIES"][$kk]["NAME"],
							'CODE' => $arResult["SKU_PROPERTIES"][$kk]["CODE"],
							'VALUE' => $vv
						);
					}
				}

				$arSku[] = $val["ID"];
				$row =& $lAdmin->AddRow($val["ID"], $val);
				$row->AddField("NAME", $skuProperty.'<input type="hidden" name="prd" id="sku-'.$val["ID"].'">');

				$row->AddField("ID", "&nbsp;&nbsp;".$arItems["ID"]."-".$val["ID"]);

				if (floatval($val["DISCOUNT_PRICE"]) > 0)
				{
					$price = $val["DISCOUNT_PRICE"];
					$priceFormated = $val["DISCOUNT_PRICE_FORMATED"];
				}
				else
				{
					$price = $val["PRICE"];
					$priceFormated = $val["PRICE_FORMATED"];
				}

				$row->AddField("PRICE", $priceFormated." ".$val["VALUTA_FORMAT"]);

				if ($addDefault == "Y" || ($val["CAN_BUY"] == "Y" && $addDefault == "N"))
				{
					$balance = $val["BALANCE"];

					$arStores = array();
					/** @var $productProvider IBXSaleProductProvider */
					if ($productProvider = CSaleBasket::GetProductProvider(array("MODULE" => "catalog", "PRODUCT_PROVIDER_CLASS" => "CCatalogProductProvider")))
					{
						$arStores = $productProvider::GetProductStores(array("PRODUCT_ID" => $val["ID"]));
					}

					$res = CIBlockElement::GetByID($val["ID"]);
					$arProduct = $res->GetNext();

					$quantity = 1;
					$summa = $price * $quantity;
					$summaFormated = CurrencyFormatNumber($summa, $val["CURRENCY"]);

					$arParams = array(
						'id' => $val["ID"],
						'name' => str_replace($aSearch, $aReplace, htmlspecialcharsback($val["NAME"])),
						'url' => $arProduct["DETAIL_PAGE_URL"],
						'urlImg' => $val["ImageUrl"],
						'urlEdit' => $val["URL_EDIT"],
						'price' => $price,
						'priceFormated' => $priceFormated,
						'valutaFormat' => $val["VALUTA_FORMAT"],
						'priceDiscount' => $val["PRICE"] - $val["DISCOUNT_PRICE"],
						'priceBase' => $val["PRICE"],
						'priceBaseFormat' => $val["PRICE_FORMATED"],
						'discountPercent' => $val["DISCOUNT_PERCENT"],
						'summaFormated' => $summaFormated,
						'quantity' => $quantity,
						'module' => 'catalog',
						'currency' => $val["CURRENCY"],
						'balance' => $val["BALANCE"],
						'weight' => $val["WEIGHT"],
						'priceType' => $val["PRICE_TYPE"],
						'vatRate' => $val["VAT_RATE"],
						'skuProps' => $arSkuProperty,
						'catalogXmlID' => $arIBlock["XML_ID"],
						'productXmlID' => $arProduct["XML_ID"],
						'barcodeMulti' => $val["BARCODE_MULTI"],
						'stores' => $arStores,
						'productProviderClass' => 'CCatalogProductProvider',
						'callback' => '',
						'orderCallback' => '',
						'cancelCallback' => '',
						'payCallback' => '',
					);
					$arParams = CUtil::PhpToJSObject($arParams);
					foreach(GetModuleEvents("sale", "OnProductSearchForm", true) as $arEvent)
						$arParams = ExecuteModuleEventEx($arEvent, array($val["ID"], $arParams));
					$arParams = "var el".$val["ID"]." = ".$arParams;

					$countField = '<input type="text" name="quantity_'.$val["ID"].'" id="quantity_'.$val["ID"].'" value="1" size="3" >';
					$active = GetMEssage('SPS_PRODUCT_ACTIVE');
					$act = '<script type="text/javascript">'.$arParams.'</script><input type="button" onclick="SelEl(el'.$val["ID"].', '.$val["ID"].')" name="btn_select_'.$val["ID"].'" id="btn_select_'.$val["ID"].'" value="'.GetMessage("SPS_SELECT").'" >';
				}
				else
				{
					$countField = "&nbsp;";
					$balance = "&nbsp;";
					$active = GetMEssage('SPS_PRODUCT_NO_ACTIVE');
					$act = GetMessage("SPS_CAN_BUY_NOT_PRODUCT");
				}

				$row->AddField("ACT", $act);
				$row->AddField("QUANTITY", $countField);
				$row->AddField("BALANCE", $balance);
				$row->AddField("ACTIVE", $active);
			}
		}
		else
		{
			$fieldValue = "";
			$nearestQuantity = $QUANTITY;
			$arPrice = CCatalogProduct::GetOptimalPrice($arItems["ID"], $nearestQuantity, $arBuyerGroups, "N", array(), $LID, $arDiscountCoupons);

			if (empty($arPrice))
			{
				if ($nearestQuantity = CCatalogProduct::GetNearestQuantityPrice($arItems["ID"], $nearestQuantity, $arBuyerGroups))
					$arPrice = CCatalogProduct::GetOptimalPrice($arItems["ID"], $nearestQuantity, $arBuyerGroups, "N", array(), $LID, $arDiscountCoupons);
			}

			if (empty($arPrice))
			{
				$fieldValue = "&nbsp;";
			}
			else
			{
				$currentPrice = $arPrice["PRICE"]["PRICE"];
				$currentBasePrice = $arPrice["PRICE"]["PRICE"];

				if($arPrice["PRICE"]["VAT_INCLUDED"] == "N" && DoubleVal($arPrice["PRICE"]["VAT_RATE"]) > 0 )
						$currentPrice = (1+DoubleVal($arPrice["PRICE"]["VAT_RATE"])) * $currentPrice;

				$currentDiscount = 0.0;
				if (isset($arPrice["DISCOUNT"]) && count($arPrice["DISCOUNT"]) > 0)
				{
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
							$maxDiscount = roundEx($maxDiscount, CATALOG_VALUE_PRECISION);

							if ($currentDiscount > $maxDiscount)
								$currentDiscount = $maxDiscount;
						}
					}

					$currentDiscount = roundEx($currentDiscount, CATALOG_VALUE_PRECISION);
					if ($arPrice["DISCOUNT"]["VALUE_TYPE"]=="S")
						$currentPrice = $currentDiscount;
					else
						$currentPrice = $currentPrice - $currentDiscount;
				}
				$vatRate = $arPrice["PRICE"]["VAT_RATE"];
				$fieldValue = FormatCurrency($currentPrice, $arPrice["PRICE"]["CURRENCY"]);
				if (DoubleVal($nearestQuantity) != DoubleVal($QUANTITY))
					$fieldValue .= str_replace("#CNT#", $nearestQuantity, GetMessage("SOPS_PRICE1"));
			}

			if(strlen($BASE_LANG_CURR) <= 0)
			{
				$arCurFormat = CCurrencyLang::GetCurrencyFormat($arPrice["PRICE"]["CURRENCY"]);
				$priceValutaFormat = str_replace("#", '', $arCurFormat["FORMAT_STRING"]);
			}

			$row->AddField("PRICE", $fieldValue);

			$arCatalogProduct = CCatalogProduct::GetByID($arItems["ID"]);
			$balance = FloatVal($arCatalogProduct["QUANTITY"]);
			$row->AddField("BALANCE", $balance);

			$URL = CIBlock::ReplaceDetailUrl($arItems["DETAIL_PAGE_URL"], $arItems, true);

			$arPriceType = GetCatalogGroup($arPrice["PRICE"]["CATALOG_GROUP_ID"]);
			$PriceType = $arPriceType["NAME_LANG"];

			$productImg = "";
			if($arItems["PREVIEW_PICTURE"] != "")
				$productImg = $arItems["PREVIEW_PICTURE"];
			elseif($arItems["DETAIL_PICTURE"] != "")
				$productImg = $arItems["DETAIL_PICTURE"];

			$ImgUrl = "";
			if ($productImg != "")
			{
				$arFile = CFile::GetFileArray($productImg);
				$productImg = CFile::ResizeImageGet($arFile, array('width'=>80, 'height'=>80), BX_RESIZE_IMAGE_PROPORTIONAL, false, false);
				$ImgUrl = $productImg["src"];
			}

			$currentTotalPrice = ($currentPrice + $currentDiscount) * $QUANTITY;

			$discountPercent = 0;
			if ($currentDiscount > 0)
				$discountPercent = roundEx(($currentDiscount * 100) / $currentTotalPrice, SALE_VALUE_PRECISION);

			if (strlen($BASE_LANG_CURR) > 0 && $BASE_LANG_CURR != $arPrice["PRICE"]["CURRENCY"])
			{
				$currentTotalPrice = roundEx(CCurrencyRates::ConvertCurrency($currentTotalPrice, $arPrice["PRICE"]["CURRENCY"], $BASE_LANG_CURR), SALE_VALUE_PRECISION);
				$currentPrice = roundEx(CCurrencyRates::ConvertCurrency($currentPrice, $arPrice["PRICE"]["CURRENCY"], $BASE_LANG_CURR), SALE_VALUE_PRECISION);
				$currentBasePrice = roundEx(CCurrencyRates::ConvertCurrency($currentBasePrice, $arPrice["PRICE"]["CURRENCY"], $BASE_LANG_CURR), SALE_VALUE_PRECISION);
				$currentDiscount = roundEx(CCurrencyRates::ConvertCurrency($currentDiscount, $arPrice["PRICE"]["CURRENCY"], $BASE_LANG_CURR), SALE_VALUE_PRECISION);
				$arPrice["PRICE"]["CURRENCY"] = $BASE_LANG_CURR;
			}

			$currentTotalPriceFormat = CurrencyFormatNumber($currentTotalPrice, $arPrice["PRICE"]["CURRENCY"]);
			$summaFormated = CurrencyFormatNumber(($currentPrice * $QUANTITY), $arPrice["PRICE"]["CURRENCY"]);

			$urlEdit = CIBlock::GetAdminElementEditLink(
				$arItems["IBLOCK_ID"],
				$arItems["ID"],
				array(
					'find_section_section' => $arItems["IBLOCK_SECTION_ID"],
					'WF' => 'Y',
				)
			);

			$bCanBuy = true;
			if ($arCatalogProduct["CAN_BUY_ZERO"]!="Y" && ($arCatalogProduct["QUANTITY_TRACE"]=="Y" && doubleval($arCatalogProduct["QUANTITY"])<=0))
				$bCanBuy = false;

			$arStores = array();
			/** @var $productProvider IBXSaleProductProvider */
			if ($productProvider = CSaleBasket::GetProductProvider(array("MODULE" => "catalog", "PRODUCT_PROVIDER_CLASS" => "CCatalogProductProvider")))
			{
				$arStores = $productProvider::GetProductStores(array("PRODUCT_ID" => $arItems["ID"]));
			}

			if ($addDefault == "Y" || ($bCanBuy && $addDefault == "N"))
			{
				$arParams = array(
					'id' => $arItems["ID"],
					'name' => $arItems["NAME"],
					'url' => $URL,
					'urlEdit' => $urlEdit,
					'urlImg' => $ImgUrl,
					'price' => $currentPrice,
					'priceFormated' => CurrencyFormatNumber($currentPrice, $arPrice["PRICE"]["CURRENCY"]),
					'valutaFormat' => $priceValutaFormat,
					'priceDiscount' => $currentDiscount,
					'priceBase' => $currentBasePrice,
					'priceBaseFormat' => CurrencyFormatNumber($currentBasePrice, $arPrice["PRICE"]["CURRENCY"]),
					'discountPercent' => $discountPercent,
					'summaFormated' => $summaFormated,
					'quantity' => $QUANTITY,
					'module' => 'catalog',
					'currency' => $arPrice["PRICE"]["CURRENCY"],
					'weight' => doubleval($arCatalogProduct["WEIGHT"]),
					'vatRate' => doubleval($vatRate),
					'priceType' => $PriceType,
					'balance' => $balance,
					'catalogXmlID' => $arIBlock["XML_ID"],
					'productXmlID' => $arItems["XML_ID"],
					'barcodeMulti' => $arCatalogProduct["BARCODE_MULTI"],
					'stores' => $arStores,
					'orderCallback' => '',
					'cancelCallback' => '',
					'payCallback' => '',
					'callback' => '',
					'productProviderClass' => 'CCatalogProductProvider',
					'skuProps' => ''
				);
				$arParams = CUtil::PhpToJSObject($arParams);

				foreach(GetModuleEvents("sale", "OnProductSearchForm", true) as $arEvent)
					$arParams = ExecuteModuleEventEx($arEvent, Array($arItems["ID"], $arParams));
				$arParams = "var el".$arItems["ID"]." = ".$arParams;

				$act = '<script type="text/javascript">'.$arParams.'</script><input type="button" onclick="SelEl(el'.$arItems["ID"].', '.$arItems["ID"].')" name="btn_select_'.$arItems["ID"].'" id="btn_select_'.$arItems["ID"].'" value="'.GetMessage("SPS_SELECT").'">';
				$countField = '<input type="text" name="quantity_'.$arItems["ID"].'" id="quantity_'.$arItems["ID"].'" value="1" size="3">';
				$active = GetMEssage('SPS_PRODUCT_ACTIVE');
			}
			else
			{
				$act = GetMessage("SPS_CAN_BUY_NOT_PRODUCT");
				$countField = "&nbsp;";
				$active = GetMEssage('SPS_PRODUCT_NO_ACTIVE');
			}

			$row->AddField("ACT", $act);
			$row->AddField("QUANTITY", $countField);
			$row->AddField("ACTIVE", $active);
		}
	}

	CCatalogDiscountSave::Enable();

	$lAdmin->BeginEpilogContent();

	?>
	<script type="text/javascript">
	<?if(!empty($arSku))
	{
		foreach($arSku as $k => $v)
		{
			?>
			if(BX('sku-<?=$v?>'))
				BX.hide(BX('sku-<?=$v?>').parentNode.parentNode);
			<?
		}
	}
	?>
	</script>
	<?
	$lAdmin->EndEpilogContent();
}
else
{
	echo ShowError(GetMessage("SPS_NO_PERMS").".");
}

$lAdmin->CheckListMode();



/****************************************************************************/
/***********  MAIN PAGE  ****************************************************/
/****************************************************************************/

$APPLICATION->SetTitle(GetMessage("SPS_SEARCH_TITLE"));
CJSCore::Init(array('admin_interface'));
$APPLICATION->AddHeadScript('/bitrix/js/main/dd.js');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_popup_admin.php");
?>
<script type="text/javascript">
BX.InitializeAdmin();
</script>
<script type="text/javascript">
function SelEl(arParams, el)
{
	var count = 1;
	if (BX('quantity_'+el))
		count = BX('quantity_'+el).value;

	arParams['quantity'] = count;

	window.opener.<?= $func_name ?>(<?= intval($index) ?>, arParams, <?= intval($iblockID) ?>);
	BX('btn_select_'+el).value ='<?=GetMessageJS("SPS_PRODUCT_SELECTED")?>';
	//window.close();
}

function showCanBuy()
{
	alert('<?=GetMessageJS("SPS_CAN_BUY_NOT")?>');
}

function fShowSku(el, sku)
{
	for(var i in sku)
	{
		if (BX('sku-'+sku[i]['ID']))
		{
			if (BX('sku-'+sku[i]['ID']).parentNode.parentNode.style.display == "none")
			{
				BX.addClass(BX('sku-'+sku[i]['ID']).parentNode.parentNode, "border_sku");

				BX.show(BX('sku-'+sku[i]['ID']).parentNode.parentNode);
				BX(el).value = '<?=GetMessageJS("SPS_SKU_HIDE")?>';
			}
			else
			{
				BX.removeClass(BX('sku-'+sku[i]['ID']).parentNode.parentNode, "border_sku");
				BX.hide(BX('sku-'+sku[i]['ID']).parentNode.parentNode);
				BX(el).value = '<?=GetMessageJS("SPS_SKU_SHOW")?>';
			}
		}
	}
}
</script>

<table width="100%">
<tr>
	<td valign="top" align="left" width="230">
		<div style="overflow-x: auto;max-width:220px;">
			<?
			function fReplaseUrl($arCatalog, $urlCurrent)
			{
				$urlCurrentDefault = $urlCurrent;

				foreach ($arCatalog as $key => $submenu)
				{
					$arUrlAdd = array("set_filter" => "Y");

					$url = $submenu["url"];
					$urlParse = parse_url($url);
					$arUrlTag = explode("&", $urlParse["query"]);

					foreach ($arUrlTag as $tag)
					{
						$tmp = explode("=", $tag);
						if ($tmp[0] == "IBLOCK_ID" || $tmp[0] == "find_section_section")
						{
							if ($tmp[0] == "find_section_section")
								$tmp[0] = "filter_section";

							$urlCurrent = CHTTP::urlDeleteParams($urlCurrent, array($tmp[0]));
							$arUrlAdd[$tmp[0]] = $tmp[1];
						}
					}

					$url = CHTTP::urlAddParams($urlCurrent, $arUrlAdd, array("encode","skip_empty"));
					$arCatalog[$key]["url"] = $url;

					if (isset($submenu["items"]) && count($submenu["items"]) > 0)
					{
						$subCatal = fReplaseUrl($submenu["items"], $urlCurrentDefault);
						$arCatalog[$key]["items"] = $subCatal;
					}
				}

				return $arCatalog;
			}

			$urlCurrent = $APPLICATION->GetCurPageParam();
			$arCatalog = CCatalogAdmin::get_sections_menu('', $iblockID, '', 0);
			$arCatalog = fReplaseUrl($arCatalog, $urlCurrent);

			foreach ($arCatalog as $key => $submenu)
			{
				$adminMenu->_SetActiveItems($submenu, array());
				$adminMenu->Show($submenu);
			}

			?>
		</div>
	</td>
	<td valign="top" align="left" style="border-left: 1px solid rgb(164, 185, 204);padding-left:15px;">
		<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
				<input type="hidden" name="__BX_CRM_QUERY_STRING_PREFIX" value="<?echo $APPLICATION->GetCurPage() ?>?">
				<input type="hidden" name="field_name" value="<?echo htmlspecialcharsbx($field_name)?>">
				<input type="hidden" name="field_name_name" value="<?echo htmlspecialcharsbx($field_name_name)?>">
				<input type="hidden" name="field_name_url" value="<?echo htmlspecialcharsbx($field_name_url)?>">
				<input type="hidden" name="alt_name" value="<?echo htmlspecialcharsbx($alt_name)?>">
				<input type="hidden" name="form_name" value="<?echo htmlspecialcharsbx($form_name)?>">
				<input type="hidden" name="func_name" value="<?echo htmlspecialcharsbx($func_name)?>">
				<input type="hidden" name="index" value="<?echo htmlspecialcharsbx($index)?>">
				<input type="hidden" name="BUYER_ID" value="<?echo htmlspecialcharsbx($BUYER_ID)?>">
				<input type="hidden" name="QUANTITY" value="<?echo htmlspecialcharsbx($QUANTITY)?>">
				<input type="hidden" name="lang" value="<?echo LANG?>">
				<input type="hidden" name="LID" value="<?echo $LID?>">
			<?

			$arFindFields = array(
					"find_iblock_id" => GetMessage("SPS_CATALOG"),
					"find_id" => "ID (".GetMessage("SPS_ID_FROM_TO").")",
					"find_xml_id" => GetMessage("SPS_XML_ID"),
					"find_code" => GetMessage("SPS_CODE"),
					"find_time" => GetMessage("SPS_TIMESTAMP"),
					"find_name" => GetMessage("SPS_NAME"),
					"find_descr" => GetMessage("SPS_DESCR"),
				);

			if (count($arProps) > 0)
			{
				foreach($arProps as $arProp)
					$arFindFields["find_prop_".$arProp["ID"]] = $arProp["NAME"];
			}

			if (count($arSKUProps) > 0)
			{
				foreach($arSKUProps as $arProp)
				{
					if($arProp["FILTRABLE"]=="Y" && $arProp["PROPERTY_TYPE"]!="F")
						$arFindFields["IBLIST_A_SUB_PROP_".$arProp["ID"]] = $arProp["NAME"];
				}
			}

			$oFilter = new CAdminFilter(
				$sTableID."_filter",
				$arFindFields
			);
			$oFilter->SetDefaultRows("find_iblock_id", "find_name");

			$oFilter->Begin();
			?>
				<tr>
					<td><?= GetMessage("SPS_CATALOG") ?>:</td>
					<td>
						<select name="IBLOCK_ID">
						<?
						$catalogID = Array();
						$dbItem = CCatalog::GetList();
						while($arItems = $dbItem->Fetch())
							$catalogID[] = $arItems["IBLOCK_ID"];
						$db_iblocks = CIBlock::GetList(Array("ID"=>"ASC"), Array("ID" => $catalogID));
						while ($db_iblocks->ExtractFields("str_iblock_"))
						{
							?><option value="<?=$str_iblock_ID?>"<?if($iblockID==$str_iblock_ID)echo " selected"?>><?=$str_iblock_NAME?> [<?=$str_iblock_LID?>] (<?=$str_iblock_ID?>)</option><?
						}
						?>
						</select>
					</td>
				</tr>

				<tr>
					<td>ID (<?= GetMessage("SPS_ID_FROM_TO") ?>):</td>
					<td>
						<input type="text" name="filter_id_start" size="10" value="<?echo htmlspecialcharsex($filter_id_start)?>">
						...
						<input type="text" name="filter_id_end" size="10" value="<?echo htmlspecialcharsex($filter_id_end)?>">
					</td>
				</tr>

				<tr>
					<td nowrap><?= GetMessage("SPS_XML_ID") ?>:</td>
					<td nowrap>
						<input type="text" name="filter_xml_id" size="50" value="<?echo htmlspecialcharsex(${"filter_xml_id"})?>">
					</td>
				</tr>

				<tr>
					<td nowrap><?= GetMessage("SPS_CODE") ?>:</td>
					<td nowrap>
						<input type="text" name="filter_code" size="50" value="<?echo htmlspecialcharsex(${"filter_code"})?>">
					</td>
				</tr>
				<tr>
					<td nowrap><?= GetMessage("SPS_TIMESTAMP") ?>:</td>
					<td nowrap><? echo CalendarPeriod("filter_timestamp_from", htmlspecialcharsex($filter_timestamp_from), "filter_timestamp_to", htmlspecialcharsex($filter_timestamp_to), "form1")?></td>
				</tr>
				<tr>
					<td nowrap><?= GetMessage("SPS_NAME") ?>:</td>
					<td nowrap>
						<input type="text" name="filter_product_name" value="<?echo htmlspecialcharsex($filter_product_name)?>" size="30">
					</td>
				</tr>
				<tr>
					<td nowrap><?= GetMessage("SPS_DESCR") ?>:</td>
					<td nowrap>
						<input type="text" name="filter_intext" size="50" value="<?echo htmlspecialcharsex(${"filter_intext"})?>" size="30">&nbsp;<?=ShowFilterLogicHelp()?>
					</td>
				</tr>

			<?if (count($arProps) > 0):
				foreach ($arProps as $arProp):?>
				<tr>
					<td><?=$arProp["NAME"]?>:</td>
					<td>
						<?if(array_key_exists("GetAdminFilterHTML", $arProp["PROPERTY_USER_TYPE"])):
							echo "<script>var arClearHiddenFields = [];</script>";
							echo call_user_func_array($arProp["PROPERTY_USER_TYPE"]["GetAdminFilterHTML"], array(
								$arProp,
								array("VALUE" => "filter_el_property_".$arProp["ID"]),
							));
						elseif($arProp["PROPERTY_TYPE"]=='S'):?>
							<input type="text" name="filter_el_property_<?=$arProp["ID"]?>" value="<?echo htmlspecialcharsex(${"filter_el_property_".$arProp["ID"]})?>" size="30">&nbsp;<?=ShowFilterLogicHelp()?>
						<?elseif($arProp["PROPERTY_TYPE"]=='N' || $arProp["PROPERTY_TYPE"]=='E'):?>
							<input type="text" name="filter_el_property_<?=$arProp["ID"]?>" value="<?echo htmlspecialcharsex(${"filter_el_property_".$arProp["ID"]})?>" size="30">
						<?elseif($arProp["PROPERTY_TYPE"]=='L'):?>
							<select name="filter_el_property_<?=$arProp["ID"]?>">
								<option value=""><?echo GetMessage("SPS_VALUE_ANY")?></option>
								<option value="NOT_REF"><?echo GetMessage("SPS_A_PROP_NOT_SET")?></option><?
								$dbrPEnum = CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC", "NAME"=>"ASC"), Array("PROPERTY_ID"=>$arProp["ID"]));
								while($arPEnum = $dbrPEnum->GetNext()):
								?>
									<option value="<?=$arPEnum["ID"]?>"<?if(${"filter_el_property_".$arProp["ID"]} == $arPEnum["ID"])echo " selected"?>><?=$arPEnum["VALUE"]?></option>
								<?
								endwhile;
						?></select>
						<?
						elseif($arProp["PROPERTY_TYPE"]=='G'):
							echo _ShowGroupPropertyFieldList('filter_el_property_'.$arProp["ID"], $arProp, ${'filter_el_property_'.$arProp["ID"]});
						endif;
						?>
					</td>
				</tr>
				<?endforeach;
			endif;

			if (!empty($arSKUProps)):
				foreach($arSKUProps as $arProp)
				{
					if($arProp["FILTRABLE"]=="Y" && $arProp["PROPERTY_TYPE"]!="F" && $arCatalog['SKU_PROPERTY_ID'] != $arProp['ID'])
					{
				?>
				<tr>
					<td><? echo ('' != $strSKUName ? $strSKUName.' - ' : ''); ?><? echo $arProp["NAME"]?>:</td>
					<td>
						<?if(array_key_exists("GetAdminFilterHTML", $arProp["PROPERTY_USER_TYPE"])):
							echo "<script>var arClearHiddenFields = [];</script>";
							echo call_user_func_array($arProp["PROPERTY_USER_TYPE"]["GetAdminFilterHTML"], array(
								$arProp,
								array("VALUE" => "find_sub_el_property_".$arProp["ID"]),
							));
						elseif($arProp["PROPERTY_TYPE"]=='S'):?>
							<input type="text" name="filter_sub_el_property_<?=$arProp["ID"]?>" value="<?echo htmlspecialcharsex(${"filter_sub_el_property_".$arProp["ID"]})?>" size="30">&nbsp;<?=ShowFilterLogicHelp()?>
						<?elseif($arProp["PROPERTY_TYPE"]=='N' || $arProp["PROPERTY_TYPE"]=='E'):?>
							<input type="text" name="filter_sub_el_property_<?=$arProp["ID"]?>" value="<?echo htmlspecialcharsex(${"filter_sub_el_property_".$arProp["ID"]})?>" size="30">
						<?elseif($arProp["PROPERTY_TYPE"]=='L'):?>
							<select name="filter_sub_el_property_<?=$arProp["ID"]?>">
								<option value=""><?echo GetMessage("SPS_VALUE_ANY")?></option>
								<option value="NOT_REF"><?echo GetMessage("SPS_A_PROP_NOT_SET")?></option><?
								$dbrPEnum = CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC", "NAME"=>"ASC"), Array("PROPERTY_ID"=>$arProp["ID"]));
								while($arPEnum = $dbrPEnum->GetNext()):
								?>
									<option value="<?=$arPEnum["ID"]?>"<?if(${"filter_sub_el_property_".$arProp["ID"]} == $arPEnum["ID"])echo " selected"?>><?=$arPEnum["VALUE"]?></option>
								<?
								endwhile;
						?></select>
						<?
						elseif($arProp["PROPERTY_TYPE"]=='G'):
							echo _ShowGroupPropertyFieldList('filter_sub_el_property_'.$arProp["ID"], $arProp, ${'filter_sub_el_property_'.$arProp["ID"]});
						endif;
						?>
					</td>
				</tr>
				<?
					}
				}
			endif;

			$oFilter->Buttons();
			?>
			<input type="submit" name="set_filter" value="<?echo GetMessage("prod_search_find")?>" title="<?echo GetMessage("prod_search_find_title")?>">
			<input type="submit" name="del_filter" value="<?echo GetMessage("prod_search_cancel")?>" title="<?echo GetMessage("prod_search_cancel_title")?>">
			<?
			$oFilter->End();
			?>
		</form>
		<?
		$lAdmin->DisplayList();
		?>
		<br>
		<input type="button" class="typebutton" value="<?= GetMessage("SPS_CLOSE") ?>" onClick="window.close();">
	</td>
</tr>
</table>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_popup_admin.php");?>