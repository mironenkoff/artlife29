<?
######################################################
# Name: energosoft.newyearsnow                       #
# File: .parameters.php                              #
# (c) 2005-2011 Energosoft, Maksimov M.A.            #
# Dual licensed under the MIT and GPL                #
# http://energo-soft.ru/                             #
# mailto:support@energo-soft.ru                      #
######################################################
?>
<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	"PARAMETERS" => array(
		"ES_INCLUDE_JQUERY" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_INCLUDE_JQUERY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"ES_INTENSIVE" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("ES_INTENSIVE"),
			"TYPE" => "STRING",
			"DEFAULT" => "400",
		),
		"ES_SPEED" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("ES_SPEED"),
			"TYPE" => "STRING",
			"DEFAULT" => "20000",
		),
	),
);
?>