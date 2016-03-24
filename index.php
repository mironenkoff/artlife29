<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Артлайф-29 - магазин полезных продуктов!");
?> <?$APPLICATION->IncludeComponent(
	"bitrix:eshop.catalog.top",
	"",
	Array(
		"DISPLAY_IMG_WIDTH" => "130",
		"DISPLAY_IMG_HEIGHT" => "130",
		"SHARPEN" => "30",
		"IBLOCK_TYPE_ID" => "catalog",
		"IBLOCK_ID" => "3",
		"ELEMENT_SORT_FIELD" => "RAND",
		"ELEMENT_SORT_ORDER" => "asc",
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"DISPLAY_COMPARE" => "N",
		"ELEMENT_COUNT" => "6",
		"FLAG_PROPERTY_CODE" => "SALELEADER",
		"OFFERS_LIMIT" => "5",
		"OFFERS_FIELD_CODE" => array("NAME"),
		"OFFERS_PROPERTY_CODE" => array("COLOR","WIDTH"),
		"OFFERS_SORT_FIELD" => "sort",
		"OFFERS_SORT_ORDER" => "asc",
		"PRICE_CODE" => array("BASE"),
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "Y",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "180",
		"CACHE_NOTES" => "",
		"CACHE_GROUPS" => "Y"
	)
);?> <?$APPLICATION->IncludeComponent(
	"bitrix:eshop.catalog.top",
	"",
	Array(
		"IBLOCK_TYPE_ID" => "catalog",
		"IBLOCK_ID" => "3",
		"PROPERTY_CODE" => array(0=>"MINIMUM_PRICE",1=>"MAXIMUM_PRICE",),
		"ELEMENT_SORT_FIELD" => "RAND",
		"ELEMENT_SORT_ORDER" => "asc",
		"ELEMENT_COUNT" => "9",
		"FLAG_PROPERTY_CODE" => "NEWPRODUCT",
		"SECTION_URL" => "",
		"DETAIL_URL" => "",
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "180",
		"CACHE_GROUPS" => "Y",
		"DISPLAY_COMPARE" => COption::GetOptionString("eshop","catalogCompare","Y",SITE_ID)=="Y"?"Y":"N",
		"COMPARE_NAME" => "CATALOG_COMPARE_LIST",
		"PRICE_CODE" => array(0=>"BASE",),
		"OFFERS_FIELD_CODE" => array(0=>"NAME",1=>"",),
		"OFFERS_PROPERTY_CODE" => array(0=>"COLOR",1=>"WIDTH",2=>"",),
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "Y",
		"DISPLAY_IMG_WIDTH" => "130",
		"DISPLAY_IMG_HEIGHT" => "130",
		"SHARPEN" => "30",
		"USE_PRODUCT_QUANTITY" => "N"
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>