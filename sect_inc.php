<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
/*$APPLICATION->IncludeComponent("bitrix:eshop.slider", ".default", array(
		"IBLOCK_TYPE_ID" => "catalog",
		"IBLOCK_ID" => array(
			0 => "3",
		),
		"FLAG_PROPERTY_CODE" => "SPECIALOFFER",
		"PROPERTY_CODE" => array(
			0 => "MINIMUM_PRICE",
			1 => "MAXIMUM_PRICE",
		),
		"RAND_COUNT" => "6",
		"DETAIL_URL" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "180",
		"CACHE_GROUPS" => "Y",
		"PARENT_SECTION" => "",
		"PRICE_CODE" => array(
			0 => "BASE",
		)
	),
	false
);    */
$APPLICATION->IncludeComponent("bitrix:eshop.catalog.top", "recommend", array(
	"IBLOCK_TYPE_ID" => "catalog",
	"IBLOCK_ID" => "3",
	"ELEMENT_SORT_FIELD" => "RAND",
	"ELEMENT_SORT_ORDER" => "asc",
	"ELEMENT_COUNT" => "6",
	"FLAG_PROPERTY_CODE" => "SPECIALOFFER",
	"OFFERS_LIMIT" => "0",
	"OFFERS_FIELD_CODE" => array(
		0 => "NAME",
		1 => "",
	),
	"OFFERS_PROPERTY_CODE" => array(
		0 => "WIDTH",
		1 => "",
	),
	"OFFERS_SORT_FIELD" => "sort",
	"OFFERS_SORT_ORDER" => "asc",
	"ACTION_VARIABLE" => "action",
	"PRODUCT_ID_VARIABLE" => "id",
	"PRODUCT_QUANTITY_VARIABLE" => "quantity",
	"PRODUCT_PROPS_VARIABLE" => "prop",
	"SECTION_ID_VARIABLE" => "SECTION_ID",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "180",
	"CACHE_GROUPS" => "Y",
	"DISPLAY_COMPARE" => "N",
	"PRICE_CODE" => array(
		0 => "BASE",
	),
	"USE_PRICE_COUNT" => "N",
	"SHOW_PRICE_COUNT" => "1",
	"PRICE_VAT_INCLUDE" => "Y",
	"DISPLAY_IMG_WIDTH" => "130",
	"DISPLAY_IMG_HEIGHT" => "130",
	"SHARPEN" => "30"
	),
	false
);
?>