<?
######################################################
# Name: energosoft.newyearsnow                       #
# File: component.php                                #
# (c) 2005-2011 Energosoft, Maksimov M.A.            #
# Dual licensed under the MIT and GPL                #
# http://energo-soft.ru/                             #
# mailto:support@energo-soft.ru                      #
######################################################
?>
<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!CModule::IncludeModule("energosoft.newyearsnow")) return;

$arParams["ES_INTENSIVE"] = intval($arParams["ES_INTENSIVE"]);
$arParams["ES_SPEED"] = intval($arParams["ES_SPEED"]);

$this->IncludeComponentTemplate();

if($arParams["ES_INCLUDE_JQUERY"]=="Y") $APPLICATION->AddHeadString("<script type=\"text/javascript\" src=\"/bitrix/js/energosoft/jquery-1.6.4.min.js\"></script>", true);
?>