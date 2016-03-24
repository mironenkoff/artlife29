<?
IncludeModuleLangFile(__FILE__);

class CAllCatalogVat
{
/*
* @deprecated deprecated since catalog 12.5.6
*/
	function err_mess()
	{
		return "<br>Module: catalog<br>Class: CCatalogVat<br>File: ".__FILE__;
	}

	function CheckFields($ACTION, &$arFields, $ID = 0)
	{
		global $APPLICATION;
		$arMsg = array();
		$boolResult = true;

		$ACTION = strtoupper($ACTION);
		if ('INSERT' == $ACTION)
			$ACTION = 'ADD';

		if (array_key_exists('SORT', $arFields))
		{
			$arFields['C_SORT'] = $arFields['SORT'];
			unset($arFields['SORT']);
		}

		if (array_key_exists('ID', $arFields))
		{
			unset($arFields['ID']);
		}

		if ('ADD' == $ACTION)
		{
			if (!array_key_exists('NAME', $arFields))
			{
				$boolResult = false;
				$arMsg[] = array('id' => 'NAME', "text" => GetMessage('CVAT_ERROR_BAD_NAME'));
			}
			if (!array_key_exists('RATE', $arFields))
			{
				$boolResult = false;
				$arMsg[] = array('id' => 'RATE', "text" => GetMessage('CVAT_ERROR_BAD_RATE'));
			}
			if (!array_key_exists('C_SORT', $arFields))
			{
				$arFields['C_SORT'] = 100;
			}
			if (!array_key_exists('ACTIVE', $arFields))
			{
				$arFields['ACTIVE'] = 'Y';
			}
		}

		if ($boolResult)
		{
			if (array_key_exists('NAME', $arFields))
			{
				$arFields['NAME'] = trim($arFields['NAME']);
				if ('' == $arFields['NAME'])
				{
					$boolResult = false;
					$arMsg[] = array('id' => 'NAME', "text" => GetMessage('CVAT_ERROR_BAD_NAME'));
				}
			}
			if (array_key_exists('RATE', $arFields))
			{
				if ('' == $arFields['RATE'])
				{
					$boolResult = false;
					$arMsg[] = array('id' => 'RATE', "text" => GetMessage('CVAT_ERROR_BAD_RATE'));
				}
				else
				{
					$arFields['RATE'] = doubleval($arFields['RATE']);
					if (0 > $arFields['RATE'] || 100 < $arFields['RATE'])
					{
						$boolResult = false;
						$arMsg[] = array('id' => 'RATE', "text" => GetMessage('CVAT_ERROR_BAD_RATE'));
					}
				}
			}
			if (array_key_exists('C_SORT', $arFields))
			{
				$arFields['C_SORT'] = intval($arFields['C_SORT']);
				if (0 >= $arFields['C_SORT'])
				{
					$arFields['C_SORT'] = 100;
				}
			}
			if (array_key_exists('ACTIVE', $arFields))
			{
				$arFields['ACTIVE'] = ('Y' == $arFields['ACTIVE'] ? 'Y' : 'N');
			}
		}

		if (!$boolResult)
		{
			$obError = new CAdminException($arMsg);
			$APPLICATION->ResetException();
			$APPLICATION->ThrowException($obError);
		}
		return $boolResult;
	}

	function GetByID($ID)
	{
		return CCatalogVat::GetListEx(array(), array('ID' => $ID));
	}

	function GetList($arOrder = array('CSORT' => 'ASC'), $arFilter = array(), $arFields = array())
	{
		return CCatalogVat::GetListEx($arOrder, $arFilter, false, false, $arFields);
	}

/*
* @deprecated deprecated since catalog 12.5.6
* @see CCatalogVat::Add()
* @see CCatalogVat::Update()
*/
	function Set($arFields)
	{
		if (array_key_exists('ID', $arFields) && 0 < intval($arFields['ID']))
		{
			return CCatalogVat::Update($arFields['ID'], $arFields);
		}
		else
		{
			return CCatalogVat::Add($arFields);
		}
	}

	function GetByProductID($PRODUCT_ID)
	{

	}
}
?>