<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("");
?> <?$APPLICATION->IncludeComponent(
	"bitrix:catalog",
	".default",
	Array(
		"PATH_TO_SHIPPING" => "#SITE_DIR#about/delivery/",
		"DISPLAY_IMG_WIDTH" => "180",
		"DISPLAY_IMG_HEIGHT" => "225",
		"DISPLAY_DETAIL_IMG_WIDTH" => "280",
		"DISPLAY_DETAIL_IMG_HEIGHT" => "280",
		"DISPLAY_MORE_PHOTO_WIDTH" => "280",
		"DISPLAY_MORE_PHOTO_HEIGHT" => "280",
		"SHARPEN" => "30",
		"AJAX_MODE" => "N",
		"SEF_MODE" => "Y",
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "3",
		"USE_FILTER" => "Y",
		"USE_REVIEW" => "Y",
		"USE_COMPARE" => "N",
		"SHOW_TOP_ELEMENTS" => "N",
		"SECTION_COUNT_ELEMENTS" => "N",
		"SECTION_TOP_DEPTH" => "1",
		"PAGE_ELEMENT_COUNT" => COption::GetOptionInt("eshop","catalogElementCount","25",SITE_ID),
		"LINE_ELEMENT_COUNT" => "1",
		"ELEMENT_SORT_FIELD" => "sort",
		"ELEMENT_SORT_ORDER" => "asc",
		"LIST_PROPERTY_CODE" => array("SPECIALOFFER", "NEWPRODUCT", "SALELEADER"),
		"INCLUDE_SUBSECTIONS" => "Y",
		"LIST_META_KEYWORDS" => "-",
		"LIST_META_DESCRIPTION" => "-",
		"LIST_BROWSER_TITLE" => "-",
		"DETAIL_PROPERTY_CODE" => array("ARTNUMBER", "MANUFACTURER", "MATERIAL", "SIZE", "RECOMMEND", "MORE_PHOTO"),
		"DETAIL_META_KEYWORDS" => "-",
		"DETAIL_META_DESCRIPTION" => "-",
		"DETAIL_BROWSER_TITLE" => "-",
		"BASKET_URL" => "/personal/cart/",
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"SET_TITLE" => "N",
		"SET_STATUS_404" => "Y",
		"PRICE_CODE" => array("BASE"),
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "Y",
		"PRICE_VAT_SHOW_VALUE" => "N",
		"USE_PRODUCT_QUANTITY" => "N",
		"LINK_IBLOCK_TYPE" => "",
		"LINK_IBLOCK_ID" => "",
		"LINK_PROPERTY_SID" => "",
		"LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#",
		"USE_ALSO_BUY" => "Y",
		"USE_STORE" => "Y",
		"USE_ELEMENT_COUNTER" => "Y",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Товары",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "arrows",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000000",
		"PAGER_SHOW_ALL" => "N",
		"LIST_OFFERS_FIELD_CODE" => array("NAME"),
		"LIST_OFFERS_PROPERTY_CODE" => array("COLOR", "WIDTH"),
		"LIST_OFFERS_LIMIT" => "5",
		"DETAIL_OFFERS_FIELD_CODE" => array("NAME"),
		"DETAIL_OFFERS_PROPERTY_CODE" => array("COLOR", "WIDTH"),
		"FILTER_NAME" => "",
		"FILTER_FIELD_CODE" => array("NAME"),
		"FILTER_PROPERTY_CODE" => array(),
		"FILTER_PRICE_CODE" => array("BASE"),
		"FILTER_OFFERS_FIELD_CODE" => array(),
		"FILTER_OFFERS_PROPERTY_CODE" => array(),
		"MESSAGES_PER_PAGE" => "10",
		"USE_CAPTCHA" => "Y",
		"REVIEW_AJAX_POST" => "Y",
		"PATH_TO_SMILE" => "/bitrix/images/forum/smile/",
		"FORUM_ID" => "1",
		"URL_TEMPLATES_READ" => "",
		"SHOW_LINK_TO_FORUM" => "Y",
		"POST_FIRST_MESSAGE" => "N",
		"USE_STORE_PHONE" => "N",
		"USE_STORE_SCHEDULE" => "N",
		"USE_MIN_AMOUNT" => "Y",
		"MIN_AMOUNT" => "10",
		"STORE_PATH" => "/store/#store_id#",
		"MAIN_TITLE" => "Наличие на складах",
		"ALSO_BUY_ELEMENT_COUNT" => "3",
		"ALSO_BUY_MIN_BUYES" => "2",
		"CONVERT_CURRENCY" => "N",
		"OFFERS_CART_PROPERTIES" => array(),
		"OFFERS_SORT_FIELD" => "sort",
		"OFFERS_SORT_ORDER" => "asc",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"SEF_FOLDER" => "/catalog/",
		"SEF_URL_TEMPLATES" => Array(
			"section" => "#SECTION_CODE#/",
			"element" => "#SECTION_CODE#/#ELEMENT_CODE#/",
			"compare" => "compare/"
		),
		"VARIABLE_ALIASES" => Array(
			"section" => Array(),
			"element" => Array(),
			"compare" => Array(),
		)
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>