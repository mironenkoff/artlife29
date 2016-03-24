<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("sale"))
{
	ShowError(GetMessage("SALE_MODULE_NOT_INSTALL"));
	return;
}
$arParams["PATH_TO_BASKET"] = trim($arParams["PATH_TO_BASKET"]);
$arParams["PATH_TO_ORDER"] = trim($arParams["PATH_TO_ORDER"]);
if (array_key_exists('SHOW_DELAY', $arParams) && 'N' == $arParams['SHOW_DELAY'])
	$arParams['SHOW_DELAY'] = 'N';
else
	$arParams['SHOW_DELAY'] = 'Y';
if (array_key_exists('SHOW_NOTAVAIL', $arParams) && 'N' == $arParams['SHOW_NOTAVAIL'])
	$arParams['SHOW_NOTAVAIL'] = 'N';
else
	$arParams['SHOW_NOTAVAIL'] = 'Y';
if (array_key_exists('SHOW_SUBSCRIBE', $arParams) && 'N' == $arParams['SHOW_SUBSCRIBE'])
	$arParams['SHOW_SUBSCRIBE'] = 'N';
else
	$arParams['SHOW_SUBSCRIBE'] = 'Y';


$bReady = false;
$bDelay = false;
$bNotAvail = false;
$bSubscribe = false;
$arItems = array();
$arReadyItems = array();
$allSum = 0.0;
$allWeight = 0.0;

$rsBaskets = CSaleBasket::GetList(
	array("ID" => "ASC"),
	array("FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL"),
	false,
	false,
	array(
		"ID", "NAME", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY",
		"PRICE", "WEIGHT", "DETAIL_PAGE_URL", "NOTES", "CURRENCY", "VAT_RATE", "CATALOG_XML_ID",
		"PRODUCT_XML_ID", "SUBSCRIBE", "DISCOUNT_PRICE", "PRODUCT_PROVIDER_CLASS"
	)
);

while ($arBasket = $rsBaskets->GetNext())
{
	$boolOneReady = false;
	if ($arBasket["DELAY"]=="N" && $arBasket["CAN_BUY"]=="Y")
	{
		$boolOneReady = true;
		$bReady = true;
		$allSum += ($arBasket["PRICE"] * $arBasket["QUANTITY"]);
		$allWeight += ($arBasket["WEIGHT"] * $arBasket["QUANTITY"]);
	}
	elseif ($arBasket["DELAY"]=="Y" && $arBasket["CAN_BUY"]=="Y")
	{
		if ('N' == $arParams['SHOW_DELAY'])
			continue;
		$bDelay = true;
	}
	elseif ($arBasket["CAN_BUY"]=="N" && $arBasket["SUBSCRIBE"]=="N")
	{
		if ('N' == $arParams['SHOW_NOTAVAIL'])
			continue;
		$bNotAvail = true;
	}
	elseif ($arBasket["CAN_BUY"]=="N" && $arBasket["SUBSCRIBE"]=="Y")
	{
		if ('N' == $arParams['SHOW_SUBSCRIBE'])
			continue;
		$bSubscribe = true;
	}

	if (!$boolOneReady)
	{
		$arBasket["PRICE_FORMATED"] = SaleFormatCurrency($arBasket["PRICE"], $arBasket["CURRENCY"]);
		$arItems[] = $arBasket;
	}
	else
	{
		$arReadyItems[] = $arBasket;
	}
}

if (!empty($arReadyItems))
{
	$arOrder = array(
		'SITE_ID' => SITE_ID,
		'USER_ID' => $USER->GetID(),
		'ORDER_PRICE' => $allSum,
		'ORDER_WEIGHT' => $allWeight,
		'BASKET_ITEMS' => $arReadyItems
	);

	$arOptions = array();

	$arErrors = array();

	CSaleDiscount::DoProcessOrder($arOrder, $arOptions, $arErrors);

	foreach ($arOrder['BASKET_ITEMS'] as &$arOneItem)
	{
		$arOneItem["PRICE_FORMATED"] = SaleFormatCurrency($arOneItem["PRICE"], $arOneItem["CURRENCY"]);
	}
	if (isset($arOneItem))
		unset($arOneItem);

	$arItems = array_merge($arOrder['BASKET_ITEMS'], $arItems);
}

$arResult = array(
	'READY' => ($bReady ? "Y" : "N"),
	'DELAY' => ($bDelay ? "Y" : "N"),
	'NOTAVAIL' => ($bNotAvail ? "Y" : "N"),
	'SUBSCRIBE' => ($bSubscribe ? "Y" : "N"),
	'ITEMS' => $arItems
);

$this->IncludeComponentTemplate();
?>