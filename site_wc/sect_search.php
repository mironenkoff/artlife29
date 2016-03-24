<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="content_search_box">
	<table>
		<tr>
			<td><?=GetMessage("SEARCH_TITLE")?></td>
			<td>
				<?$APPLICATION->IncludeComponent("bitrix:search.title", "eshop", array(
	"NUM_CATEGORIES" => "1",
	"TOP_COUNT" => "5",
	"ORDER" => "date",
	"USE_LANGUAGE_GUESS" => "Y",
	"CHECK_DATES" => "N",
	"SHOW_OTHERS" => "Y",
	"PAGE" => "/site_wc/catalog/",
	"CATEGORY_OTHERS_TITLE" => GetMessage("SEARCH_OTHER"),
	"CATEGORY_0_TITLE" => GetMessage("SEARCH_GOODS"),
	"CATEGORY_0" => array(
		0 => "iblock_catalog",
	),
	"CATEGORY_0_iblock_catalog" => array(
		0 => "all",
	),
	"SHOW_INPUT" => "N",
	"INPUT_ID" => "title-search-input",
	"CONTAINER_ID" => "search"
	),
	false,
	array(
	"ACTIVE_COMPONENT" => "N"
	)
);?>
			</td>
		</tr>
	</table>
</div>