<?
IncludeModuleLangFile(__FILE__);

class CAllCatalogSKU
{
	static protected $arOfferCache = array();
	static protected $arProductCache = array();
	static protected $arPropertyCache = array();

	public function GetProductInfo($intOfferID, $intIBlockID = 0)
	{
		$intOfferID = intval($intOfferID);
		if (0 >= $intOfferID)
			return false;

		$intIBlockID = intval($intIBlockID);
		if (0 >= $intIBlockID)
		{
			$intIBlockID = intval(CIBlockElement::GetIBlockByID($intOfferID));
		}
		if (0 >= $intIBlockID)
			return false;

		if (!array_key_exists($intIBlockID, self::$arOfferCache))
		{
			$arSkuInfo = CCatalogSKU::GetInfoByOfferIBlock($intIBlockID);
		}
		else
		{
			$arSkuInfo = self::$arOfferCache[$intIBlockID];
		}
		if (empty($arSkuInfo) || empty($arSkuInfo['SKU_PROPERTY_ID']))
			return false;

		$rsItems = CIBlockElement::GetProperty($intIBlockID,$intOfferID,array(),array('ID' => $arSkuInfo['SKU_PROPERTY_ID']));
		if ($arItem = $rsItems->Fetch())
		{
			$arItem['VALUE'] = intval($arItem['VALUE']);
			if (0 < $arItem['VALUE'])
			{
				return array(
					'ID' => $arItem['VALUE'],
					'IBLOCK_ID' => $arSkuInfo['PRODUCT_IBLOCK_ID'],
				);
			}
		}
		return false;
	}

	public function GetInfoByOfferIBlock($intIBlockID)
	{
		$intIBlockID = intval($intIBlockID);
		if (0 >= $intIBlockID)
			return false;

		if (!array_key_exists($intIBlockID, self::$arOfferCache))
		{
			$rsOffers = CCatalog::GetList(
				array(),
				array('IBLOCK_ID' => $intIBlockID, '!PRODUCT_IBLOCK_ID' => 0),
				false,
				false,
				array('IBLOCK_ID','PRODUCT_IBLOCK_ID','SKU_PROPERTY_ID')
			);
			$arResult = $rsOffers->Fetch();
			if (!empty($arResult))
			{
				$arResult['IBLOCK_ID'] = intval($arResult['IBLOCK_ID']);
				$arResult['PRODUCT_IBLOCK_ID'] = intval($arResult['PRODUCT_IBLOCK_ID']);
				$arResult['SKU_PROPERTY_ID'] = intval($arResult['SKU_PROPERTY_ID']);
			}
			self::$arOfferCache[$intIBlockID] = $arResult;
		}
		else
		{
			$arResult = self::$arOfferCache[$intIBlockID];
		}
		return $arResult;
	}

	public function GetInfoByProductIBlock($intIBlockID)
	{
		$intIBlockID = intval($intIBlockID);
		if (0 >= $intIBlockID)
			return false;
		if (!array_key_exists($intIBlockID, self::$arProductCache))
		{
			$rsProducts = CCatalog::GetList(
				array(),
				array('PRODUCT_IBLOCK_ID' => $intIBlockID),
				false,
				false,
				array('IBLOCK_ID','PRODUCT_IBLOCK_ID','SKU_PROPERTY_ID')
			);
			$arResult = $rsProducts->Fetch();
			if (!empty($arResult))
			{
				$arResult['IBLOCK_ID'] = intval($arResult['IBLOCK_ID']);
				$arResult['PRODUCT_IBLOCK_ID'] = intval($arResult['PRODUCT_IBLOCK_ID']);
				$arResult['SKU_PROPERTY_ID'] = intval($arResult['SKU_PROPERTY_ID']);
			}
			self::$arProductCache[$intIBlockID] = $arResult;
		}
		else
		{
			$arResult = self::$arProductCache[$intIBlockID];
		}
		return $arResult;
	}

	public function GetInfoByLinkProperty($intPropertyID)
	{
		$intPropertyID = intval($intPropertyID);
		if (0 >= $intPropertyID)
			return false;
		if (!array_key_exists($intPropertyID, self::$arPropertyCache))
		{
			$rsProducts = CCatalog::GetList(
				array(),
				array('SKU_PROPERTY_ID' => $intPropertyID),
				false,
				false,
				array('IBLOCK_ID','PRODUCT_IBLOCK_ID','SKU_PROPERTY_ID')
			);
			$arResult = $rsProducts->Fetch();
			if (!empty($arResult))
			{
				$arResult['IBLOCK_ID'] = intval($arResult['IBLOCK_ID']);
				$arResult['PRODUCT_IBLOCK_ID'] = intval($arResult['PRODUCT_IBLOCK_ID']);
				$arResult['SKU_PROPERTY_ID'] = intval($arResult['SKU_PROPERTY_ID']);
			}
			self::$arPropertyCache[$intPropertyID] = $arResult;
		}
		else
		{
			$arResult = self::$arPropertyCache[$intPropertyID];
		}
		return $arResult;
	}

	public function IsExistOffers($intProductID, $intIBlockID = 0)
	{
		$intProductID = intval($intProductID);
		if (0 >= $intProductID)
			return false;

		$intIBlockID = intval($intIBlockID);
		if (0 >= $intIBlockID)
		{
			$intIBlockID = intval(CIBlockElement::GetIBlockByID($intProductID));
		}
		if (0 >= $intIBlockID)
			return false;

		if (!array_key_exists($intIBlockID, self::$arProductCache))
		{
			$arSkuInfo = CCatalogSKU::GetInfoByProductIBlock($intIBlockID);
		}
		else
		{
			$arSkuInfo = self::$arProductCache[$intIBlockID];
		}
		if (empty($arSkuInfo) || empty($arSkuInfo['SKU_PROPERTY_ID']))
			return false;

		$intCount = CIBlockElement::GetList(
			array(),
			array('IBLOCK_ID' => $arSkuInfo['IBLOCK_ID'], '=PROPERTY_'.$arSkuInfo['SKU_PROPERTY_ID'] => $intProductID),
			array()
		);
		return (0 < $intCount);
	}

	public static function ClearCache()
	{
		self::$arOfferCache = array();
		self::$arProductCache = array();
		self::$arPropertyCache = array();
	}
}
?>