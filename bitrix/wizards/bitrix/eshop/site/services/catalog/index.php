<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$catalogSmartFilter = $wizard->GetVar("catalogSmartFilter");
$catalogSmartFilter = ($catalogSmartFilter == "Y") ? "Y" : "N";
COption::SetOptionString("eshop", "catalogSmartFilter", $catalogSmartFilter, false, WIZARD_SITE_ID);

$catalogCompare = $wizard->GetVar("catalogCompare");
$catalogCompare = ($catalogCompare == "Y") ? "Y" : "N";
COption::SetOptionString("eshop", "catalogCompare", $catalogCompare, false, WIZARD_SITE_ID);

$catalogSubscribe = $wizard->GetVar("catalogSubscribe");
$curSiteSubscribe = ($catalogSubscribe == "Y") ? array("use" => "Y", "del_after" => "100") : array("del_after" => "100");
$subscribe = COption::GetOptionString("sale", "subscribe_prod", "");
$arSubscribe = unserialize($subscribe);
$arSubscribe[WIZARD_SITE_ID] = $curSiteSubscribe;
COption::SetOptionString("sale", "subscribe_prod", serialize($arSubscribe));

$catalogElementCount = intval($wizard->GetVar("catalogElementCount"));
if (!in_array($catalogElementCount, array("15", "25", "50"))) $catalogElementCount = "25";
COption::SetOptionInt("eshop", "catalogElementCount", $catalogElementCount, false, WIZARD_SITE_ID);

$catalogView = $wizard->GetVar("catalogView");  
if (!in_array($catalogView, array("bar", "list", "price_list"))) $catalogView = "list";     
COption::SetOptionString("eshop", "catalogView", $catalogView, false, WIZARD_SITE_ID);

$catalogDetailDescr = $wizard->GetVar("catalogDetailDescr");
$catalogDetailDescr = ($catalogDetailDescr == "list") ? "list" : "tabs";
COption::SetOptionString("eshop", "catalogDetailDescr", $catalogDetailDescr, false,  WIZARD_SITE_ID);

$catalogDetailSku = $wizard->GetVar("catalogDetailSku");
$catalogDetailSku = ($catalogDetailSku == "list") ? "list" : "select"; 
COption::SetOptionString("eshop", "catalogDetailSku", $catalogDetailSku, false, WIZARD_SITE_ID);

$useIdea = $wizard->GetVar("useIdea");
$useIdea = ($useIdea == "Y") ? "Y" : "N";
COption::SetOptionString("eshop", "useIdea", $useIdea, false, WIZARD_SITE_ID);

if(COption::GetOptionString("eshop", "wizard_installed", "N", WIZARD_SITE_ID) == "Y" && !WIZARD_INSTALL_DEMO_DATA)
	return;
	
COption::SetOptionString("catalog", "allow_negative_amount", "Y");
COption::SetOptionString("catalog", "default_can_buy_zero", "Y");
COption::SetOptionString("catalog", "default_quantity_trace", "Y");


if (CModule::IncludeModule("catalog"))
{
	$arStores = array();
	$dbStore= CCatalogStore::GetList(array(), array("XML_ID" => "mebel"), false, false, array("ID"));
	if (!$arStore = $dbStore->Fetch())
	    $arNewStores[] =  array(
			"TITLE" => GetMessage("STORE_NAME_1"),
			"ACTIVE" => "N",
			"ADDRESS" => GetMessage("STORE_ADR_1"),
			"DESCRIPTION" => GetMessage("STORE_DESCR_1"),
			"USER_ID" => $USER->GetID(),
			"GPS_N" => "54.71411",
			"GPS_S" => "20.56675",
			"PHONE" => "+74955523456",
			"SCHEDULE" => "24/7",
			"XML_ID" => "mebel",
		);
	$dbStore= CCatalogStore::GetList(array(), array("XML_ID" => "armada"), false, false, array("ID"));
	if (!$arStore = $dbStore->Fetch())
		$arNewStores[] = array(
			"TITLE" => GetMessage("STORE_NAME_2"),
			"ACTIVE" => "N",
			"ADDRESS" => GetMessage("STORE_ADR_2"),
			"DESCRIPTION" => GetMessage("STORE_DESCR_2"),
			"USER_ID" => $USER->GetID(),
			"GPS_N" => "55.896919",
			"GPS_S" => "37.57983",
			"PHONE" => "+74954123456",
			"SCHEDULE" => "24/7",
			"XML_ID" => "armada",
		); 
	if (count($arNewStores) > 0)
	foreach($arNewStores as $arFields)           
		CCatalogStore::Add($arFields);
}
?>