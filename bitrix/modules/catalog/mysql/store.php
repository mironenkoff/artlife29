<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/general/store.php");

class CCatalogStore
	extends CAllCatalogStore
{
	/** Add new store in table b_catalog_store,
	* @static
	* @param $arFields
	* @return bool|int
	*/
	static function Add($arFields)
	{
		/** @global CDataBase $DB */

		global $DB;

		if (!CBXFeatures::IsFeatureEnabled('CatMultiStore'))
		{
			$dbResultList = CCatalogStore::GetList(array());
			if($arResult = $dbResultList->Fetch())
			{
				$GLOBALS["APPLICATION"]->ThrowException(GetMessage("CS_ALREADY_HAVE_STORE"));
				return false;
			}
		}

		foreach (GetModuleEvents("catalog", "OnBeforeCatalogStoreAdd", true) as $arEvent)
		{
			if (ExecuteModuleEventEx($arEvent, array(&$arFields))===false)
				return false;
		}

		if(array_key_exists('DATE_CREATE', $arFields))
			unset($arFields['DATE_CREATE']);
		if(array_key_exists('DATE_MODIFY', $arFields))
			unset($arFields['DATE_MODIFY']);

		$arFields['~DATE_MODIFY'] = $DB->GetNowFunction();
		$arFields['~DATE_CREATE'] = $DB->GetNowFunction();

		if(!self::CheckFields('ADD',$arFields))
			return false;

		$arInsert = $DB->PrepareInsert("b_catalog_store", $arFields);

		$strSql = "INSERT INTO b_catalog_store (".$arInsert[0].") VALUES(".$arInsert[1].")";

		$res = $DB->Query($strSql, False, "File: ".__FILE__."<br>Line: ".__LINE__);
		if(!$res)
			return false;
		$lastId = intval($DB->LastID());

		foreach(GetModuleEvents("catalog", "OnCatalogStoreAdd", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array($lastId, $arFields));

		return $lastId;
	}

	static function GetList($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB;
		if (empty($arSelectFields))
			$arSelectFields = array("ID", "ACTIVE", "TITLE", "PHONE", "SCHEDULE", "ADDRESS", "DESCRIPTION", "GPS_N", "GPS_S", "IMAGE_ID", "DATE_CREATE", "DATE_MODIFY", "USER_ID", "XML_ID"/*, "BASE"*/);

		if (!isset($arFilter["PRODUCT_ID"]) && isset($arSelectFields["PRODUCT_AMOUNT"]))
		{
			unset($arSelectFields["PRODUCT_AMOUNT"]);
		}
		$productID=intval($arFilter["PRODUCT_ID"]);
		$arFields = array(
			"ID" => array("FIELD" => "CS.ID", "TYPE" => "int"),
			"ACTIVE" => array("FIELD" => "CS.ACTIVE", "TYPE" => "string"),
			"TITLE" => array("FIELD" => "CS.TITLE", "TYPE" => "string"),
			"PHONE" => array("FIELD" => "CS.PHONE", "TYPE" => "string"),
			"SCHEDULE" => array("FIELD" => "CS.SCHEDULE", "TYPE" => "string"),
			"ADDRESS" => array("FIELD" => "CS.ADDRESS", "TYPE" => "string"),
			"DESCRIPTION" => array("FIELD" => "CS.DESCRIPTION", "TYPE" => "string"),
			"GPS_N" => array("FIELD" => "CS.GPS_N", "TYPE" => "string"),
			"GPS_S" => array("FIELD" => "CS.GPS_S", "TYPE" => "string"),
			"IMAGE_ID" => array("FIELD" => "CS.IMAGE_ID", "TYPE" => "int"),
			"LOCATION_ID" => array("FIELD" => "CS.LOCATION_ID", "TYPE" => "int"),
			"DATE_CREATE" => array("FIELD" => "CS.DATE_CREATE", "TYPE" => "datetime"),
			"DATE_MODIFY" => array("FIELD" => "CS.DATE_MODIFY", "TYPE" => "datetime"),
			"USER_ID" => array("FIELD" => "CS.USER_ID", "TYPE" => "int"),
			"MODIFIED_BY" => array("FIELD" => "CS.MODIFIED_BY", "TYPE" => "int"),
			"XML_ID" => array("FIELD" => "CS.XML_ID", "TYPE" => "string"),
			'PRODUCT_AMOUNT' => array("FIELD" => "CP.AMOUNT", "TYPE" => "double", "FROM" => "LEFT JOIN b_catalog_store_product CP ON (CS.ID = CP.STORE_ID AND CP.PRODUCT_ID=$productID)"),
			//"BASE" => array("FIELD" => "CS.BASE", "TYPE" => "char"),
		);
		$arSqls = CCatalog::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);
		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

		if (empty($arGroupBy) && is_array($arGroupBy))
		{
			$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_catalog_store CS ".$arSqls["FROM"];
			if (!empty($arSqls["WHERE"]))
				$strSql .= " WHERE ".$arSqls["WHERE"];
			if (!empty($arSqls["GROUPBY"]))
				$strSql .= " GROUP BY ".$arSqls["GROUPBY"];

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			if ($arRes = $dbRes->Fetch())
				return $arRes["CNT"];
			else
				return false;
		}

		$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_catalog_store CS ".$arSqls["FROM"];
		if (!empty($arSqls["WHERE"]))
			$strSql .= " WHERE ".$arSqls["WHERE"];
		if (!empty($arSqls["GROUPBY"]))
			$strSql .= " GROUP BY ".$arSqls["GROUPBY"];
		if (!empty($arSqls["ORDERBY"]))
			$strSql .= " ORDER BY ".$arSqls["ORDERBY"];

		$intTopCount = 0;
		$boolNavStartParams = (!empty($arNavStartParams) && is_array($arNavStartParams));
		if ($boolNavStartParams && array_key_exists('nTopCount', $arNavStartParams))
		{
			$intTopCount = intval($arNavStartParams["nTopCount"]);
		}
		if ($boolNavStartParams && 0 >= $intTopCount)
		{
			$strSql_tmp = "SELECT COUNT('x') as CNT FROM b_catalog_store CS ".$arSqls["FROM"];
			if (!empty($arSqls["WHERE"]))
				$strSql_tmp .= " WHERE ".$arSqls["WHERE"];
			if (!empty($arSqls["GROUPBY"]))
				$strSql_tmp .= " GROUP BY ".$arSqls["GROUPBY"];

			$dbRes = $DB->Query($strSql_tmp, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			$cnt = 0;
			if (empty($arSqls["GROUPBY"]))
			{
				if ($arRes = $dbRes->Fetch())
					$cnt = $arRes["CNT"];
			}
			else
			{
				$cnt = $dbRes->SelectedRowsCount();
			}

			$dbRes = new CDBResult();

			$dbRes->NavQuery($strSql, $cnt, $arNavStartParams);
		}
		else
		{
			if ($boolNavStartParams && 0 < $intTopCount)
			{
				$strSql .= " LIMIT ".$intTopCount;
			}
			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}

		return $dbRes;
	}
}