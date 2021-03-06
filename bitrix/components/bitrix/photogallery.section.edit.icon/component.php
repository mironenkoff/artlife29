<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if (!CModule::IncludeModule("photogallery")):
	ShowError(GetMessage("P_MODULE_IS_NOT_INSTALLED"));
	return 0;
elseif (!CModule::IncludeModule("iblock")):
	ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
	return;
elseif ($arParams["BEHAVIOUR"] == "USER" && empty($arParams["USER_ALIAS"])):
	ShowError(GetMessage("P_GALLERY_EMPTY"));
	return 0;
endif;
CPageOption::SetOptionString("main", "nav_page_in_session", "N");
// **************************************************************************************
//	$arParams["ALBUM_PHOTO"]["WIDTH"]
//	$arParams["ALBUM_PHOTO"]["HEIGHT"]
//	$arParams["ALBUM_PHOTO_THUMBS"]["WIDTH"]
//	$arParams["ALBUM_PHOTO_THUMBS"]["HEIGHT"]
if(!function_exists("__UnEscape"))
{
	function __UnEscape(&$item, $key)
	{
		if(is_array($item))
			array_walk($item, '__UnEscape');
		elseif (strpos($item, "%u") !== false)
			$item = $GLOBALS["APPLICATION"]->UnJSEscape($item);
		elseif (LANG_CHARSET != "UTF-8" && preg_match("/^.{1}/su", $item) == 1)
			$item = $GLOBALS["APPLICATION"]->ConvertCharset($item, "UTF-8", LANG_CHARSET);
	}
}
function UnsharpMask(&$img/*, $amount, $radius, $threshold*/)
{ 
	$amount = intVal($amount);
	$amount = ($amount > 500 ? 500 : $amount) * 0.016;
	$radius = round(intVal($radius > 50 ? 50 : $radius) * 2); 
	if ($radius <= 0): 
		return false;
	endif;
	$threshold = intVal($threshold > 255 ? 255 : $threshold);
	$amount = 150;
	$radius = 2;
	$threshold = 70;
	$w = imagesx($img); $h = imagesy($img); 
	$imgCanvas = false;
	$imgBlur = imagecreatetruecolor($w, $h); 
/*	if (function_exists('imageconvolution')) // PHP >= 5.1
	{ 
		$matrix = array(
			array( 1, 2, 1 ),
			array( 2, 4, 2 ), 
			array( 1, 2, 1 ) 
		); 
		imagecopy ($imgBlur, $img, 0, 0, 0, 0, $w, $h); 
		imageconvolution($imgBlur, $matrix, 16, 0); 
	} 
	else 
*/	{
		$imgCanvas = imagecreatetruecolor($w, $h); 
		for ($i = 0; $i < $radius; $i++) 
		{ 
			imagecopy ($imgBlur, $img, 0, 0, 1, 0, $w - 1, $h); // left 
			imagecopymerge ($imgBlur, $img, 1, 0, 0, 0, $w, $h, 50); // right 
			imagecopymerge ($imgBlur, $img, 0, 0, 0, 0, $w, $h, 50); // center 
			imagecopy ($imgCanvas, $imgBlur, 0, 0, 0, 0, $w, $h); 
			
			imagecopymerge ($imgBlur, $imgCanvas, 0, 0, 0, 1, $w, $h - 1, 33.33333 ); // up 
			imagecopymerge ($imgBlur, $imgCanvas, 0, 1, 0, 0, $w, $h, 25); // down 
		} 
	} 
	
	if($threshold > 0)
	{ 
		// Calculate the difference between the blurred pixels and the original 
		// and set the pixels 
		for ($x = 0; $x < $w-1; $x++) // each row 
		{ 
			for ($y = 0; $y < $h; $y++) // each pixel 
			{ 
				$rgbOrig = ImageColorAt($img, $x, $y); 
				$rOrig = (($rgbOrig >> 16) & 0xFF); 
				$gOrig = (($rgbOrig >> 8) & 0xFF); 
				$bOrig = ($rgbOrig & 0xFF); 
	
				$rgbBlur = ImageColorAt($imgBlur, $x, $y); 
	
				$rBlur = (($rgbBlur >> 16) & 0xFF); 
				$gBlur = (($rgbBlur >> 8) & 0xFF); 
				$bBlur = ($rgbBlur & 0xFF); 
	
				$rNew = (abs($rOrig - $rBlur) >= $threshold) ? max(0, min(255, ($amount * ($rOrig - $rBlur)) + $rOrig)) : $rOrig; 
				$gNew = (abs($gOrig - $gBlur) >= $threshold) ? max(0, min(255, ($amount * ($gOrig - $gBlur)) + $gOrig)) : $gOrig; 
				$bNew = (abs($bOrig - $bBlur) >= $threshold) ? max(0, min(255, ($amount * ($bOrig - $bBlur)) + $bOrig)) : $bOrig; 
				if (($rOrig != $rNew) || ($gOrig != $gNew) || ($bOrig != $bNew)) 
				{ 
					$pixCol = ImageColorAllocate($img, $rNew, $gNew, $bNew); 
					ImageSetPixel($img, $x, $y, $pixCol); 
				}
			} 
		} 
	}
	else
	{ 
		for ($x = 0; $x < $w; $x++) // each row 
		{ 
			for ($y = 0; $y < $h; $y++) // each pixel 
			{ 
				$rgbOrig = ImageColorAt($img, $x, $y); 
				$rOrig = (($rgbOrig >> 16) & 0xFF); 
				$gOrig = (($rgbOrig >> 8) & 0xFF); 
				$bOrig = ($rgbOrig & 0xFF); 
	
				$rgbBlur = ImageColorAt($imgBlur, $x, $y); 
	
				$rBlur = (($rgbBlur >> 16) & 0xFF); 
				$gBlur = (($rgbBlur >> 8) & 0xFF); 
				$bBlur = ($rgbBlur & 0xFF); 
	
				$rNew = ($amount * ($rOrig - $rBlur)) + $rOrig; 
				$rNew = ($rNew > 255 ? 255 : ($rNew < 0 ? 0 : $rNew));
				$gNew = ($amount * ($gOrig - $gBlur)) + $gOrig; 
				$gNew = ($gNew > 255 ? 255 : ($gNew < 0 ? 0 : $gNew));
				$bNew = ($amount * ($bOrig - $bBlur)) + $bOrig; 
				$bNew = ($bNew > 255 ? 255 : ($bNew < 0 ? 0 : $bNew));
				$rgbNew = ($rNew << 16) + ($gNew <<8) + $bNew; 
				ImageSetPixel($img, $x, $y, $rgbNew); 
			} 
		} 
	}
	@imagedestroy($imgCanvas); 
	@imagedestroy($imgBlur); 
	return $img; 
} 
if (empty($arParams["INDEX_URL"]) && !empty($arParams["SECTIONS_TOP_URL"]))
	$arParams["INDEX_URL"] = $arParams["SECTIONS_TOP_URL"]; 
/********************************************************************
				Input params
********************************************************************/
//***************** BASE *******************************************/
	$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
	$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);
	$arParams["SECTION_ID"] = intVal($arParams["SECTION_ID"]);
	$arParams["USER_ALIAS"] = trim($arParams["USER_ALIAS"]);
	$arParams["PERMISSION_EXTERNAL"] = trim($arParams["PERMISSION"]);
	$arParams["BEHAVIOUR"] = ($arParams["BEHAVIOUR"] == "USER" ? "USER" : "SIMPLE");
	
	$arParams["ELEMENT_SORT_FIELD"] = (empty($arParams["ELEMENT_SORT_FIELD"]) ? "ID" : strToUpper($arParams["ELEMENT_SORT_FIELD"]));
	$arParams["ELEMENT_SORT_ORDER"] = (strToUpper($arParams["ELEMENT_SORT_ORDER"]) != "DESC" ? "ASC" : "DESC");
	$arParams["PATH_TMP"] = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/tmp/uploader/";
//***************** URL ********************************************/
	$URL_NAME_DEFAULT = array(
			"index" => "",
			"gallery" => "PAGE_NAME=gallery&USER_ALIAS=#USER_ALIAS#",
			"section" => "PAGE_NAME=section".($arParams["BEHAVIOUR"] == "USER" ? "&USER_ALIAS=#USER_ALIAS#" : "" ).
				"&SECTION_ID=#SECTION_ID#", 
			"section_edit_icon" => "PAGE_NAME=section_edit_icon".($arParams["BEHAVIOUR"] == "USER" ? "&USER_ALIAS=#USER_ALIAS#" : "" ).
				"&SECTION_ID=#SECTION_ID#");
		
	foreach ($URL_NAME_DEFAULT as $URL => $URL_VALUE)
	{
		$arParams[strToUpper($URL)."_URL"] = trim($arParams[strToUpper($URL)."_URL"]);
		if (empty($arParams[strToUpper($URL)."_URL"]))
			$arParams[strToUpper($URL)."_URL"] = $APPLICATION->GetCurPage()."?".$URL_VALUE;
		$arParams["~".strToUpper($URL)."_URL"] = $arParams[strToUpper($URL)."_URL"];
		$arParams[strToUpper($URL)."_URL"] = htmlspecialchars($arParams["~".strToUpper($URL)."_URL"]);
	}
//***************** ADDITIONAL **************************************/
	$arParams["ALBUM_PHOTO"] = array(
		"WIDTH" => (intVal($arParams["ALBUM_PHOTO_WIDTH"]) > 0 ? intVal($arParams["ALBUM_PHOTO_WIDTH"]) : 200), 
		"HEIGHT" => (intVal($arParams["ALBUM_PHOTO_HEIGHT"]) > 0 ? intVal($arParams["ALBUM_PHOTO_HEIGHT"]) : 200)); 
	$arParams["ALBUM_PHOTO"]["HEIGHT"] = $arParams["ALBUM_PHOTO"]["WIDTH"];

	$arParams["PAGE_ELEMENTS"] = intVal($arParams["PAGE_ELEMENTS"] > 0 ? $arParams["PAGE_ELEMENTS"] : 20);
	$arParams["PAGE_NAVIGATION_TEMPLATE"] = trim($arParams["PAGE_NAVIGATION_TEMPLATE"]);

	$arParams["ALBUM_PHOTO_THUMBS"] = array(
		"WIDTH" => (intVal($arParams["ALBUM_PHOTO_THUMBS_WIDTH"]) > 0 ? intVal($arParams["ALBUM_PHOTO_THUMBS_WIDTH"]) : 120), 
		"HEIGHT" => (intVal($arParams["ALBUM_PHOTO_THUMBS_HEIGHT"]) > 0 ? intVal($arParams["ALBUM_PHOTO_THUMBS_HEIGHT"]) : 120));
	$arParams["ALBUM_PHOTO_THUMBS"]["HEIGHT"] = $arParams["ALBUM_PHOTO_THUMBS"]["WIDTH"];
	$arParams["SET_STATUS_404"] = ($arParams["SET_STATUS_404"] == "Y" ? "Y" : "N");
	$arParams["AJAX_CALL"] = ($_REQUEST["AJAX_CALL"] == "Y" ? "Y" : "N");
//***************** STANDART ****************************************/
	$arParams["SET_TITLE"] = $arParams["SET_TITLE"]!="N"; //Turn on by default
	$arParams["SET_NAV_CHAIN"] = ($arParams["SET_NAV_CHAIN"] == "N" ? "N" : "Y"); //Turn on by default
	$arParams["DISPLAY_PANEL"] = $arParams["DISPLAY_PANEL"]=="Y"; //Turn off by default
/********************************************************************
				/Input params
********************************************************************/

/********************************************************************
				Default values
********************************************************************/
$cache = new CPHPCache;
$cache_path_main = str_replace(array(":", "//"), "/", "/".SITE_ID."/".$componentName."/".$arParams["IBLOCK_ID"]."/");
/********************************************************************
				/Default values
********************************************************************/

if ($arParams["AJAX_CALL"] == "Y"): 
	$GLOBALS['APPLICATION']->RestartBuffer();
endif;

/********************************************************************
				Main data
********************************************************************/
$oPhoto = new CPGalleryInterface( 
	array(
		"IBlockID" => $arParams["IBLOCK_ID"], 
		"GalleryID" => $arParams["USER_ALIAS"], 
		"Permission" => $arParams["PERMISSION_EXTERNAL"]), 
	array(
		"cache_time" => $arParams["CACHE_TIME"], 
		"cache_path" => $cache_path_main, 
		"show_error" => "Y", 
		"set_404" => $arParams["SET_STATUS_404"]
		)
	);

$bError = true;
if ($oPhoto)
{
	$bError = false;
	$arResult["GALLERY"] = $oPhoto->Gallery;
	$arParams["PERMISSION"] = $oPhoto->User["Permission"];
	
	if ($oPhoto->GetSection($arParams["SECTION_ID"], $arResult["SECTION"]) > 200):
		$bError = true;
	elseif ($arParams["PERMISSION"] < "U"):
		ShowError(GetMessage("P_ACCESS_DENIED"));
		$bError = true;
	endif;
}

if ($bError)
{
	if ($arParams["AJAX_CALL"] == "Y")
		die();
	return false;
}
/********************************************************************
				Main data
********************************************************************/

/********************************************************************
				Default values
********************************************************************/
$arResult["ITEMS"] = array();
$arResult["ELEMENTS"] = array("MAX_WIDTH" => 0, "MAX_HEIGHT" => 0);
$arError = array();
$arResult["ERROR_MESSAGE"] = "";
$bVarsFromForm = false;
$bGD2 = false;
if (function_exists("gd_info")):
	$arGDInfo = gd_info();
	$bGD2 = ((strpos($arGDInfo['GD Version'], "2.") !== false) ? true : false);
endif;
$arSelect = array("ID", "CODE", "IBLOCK_ID", "NAME", "PREVIEW_PICTURE"/*, "DETAIL_PICTURE", "PROPERTY_REAL_PICTURE"*/);
//WHERE
$arFilter = array(
	"IBLOCK_ID" => $arParams["IBLOCK_ID"],  
	"SECTION_ID" => $arResult["SECTION"]["ID"], 
	"INCLUDE_SUBSECTIONS" => "Y", 
	"CHECK_PERMISSIONS" => "Y");
/************** URL ************************************************/
if (intVal($arResult["SECTION"]["IBLOCK_SECTION_ID"]) <= 0):
	$arResult["SECTION"]["BACK_LINK"] = CComponentEngine::MakePathFromTemplate($arParams["~INDEX_URL"], array());
elseif ($arResult["SECTION"]["IBLOCK_SECTION_ID"] != $arResult["GALLERY"]["ID"]):
	$arResult["SECTION"]["BACK_LINK"] = CComponentEngine::MakePathFromTemplate($arParams["~GALLERY_URL"], 
		array("USER_ALIAS" => $arParams["USER_ALIAS"]));
else:
	$arResult["SECTION"]["BACK_LINK"] = CComponentEngine::MakePathFromTemplate($arParams["~SECTION_URL"], 
		array("USER_ALIAS" => $arParams["USER_ALIAS"], "SECTION_ID" => $arResult["SECTION"]["IBLOCK_SECTION_ID"]));
endif;
$arResult["SECTION"]["SECTION_LINK"] = CComponentEngine::MakePathFromTemplate($arParams["~SECTION_URL"], 
		array("USER_ALIAS" => $arParams["USER_ALIAS"], "SECTION_ID" => $arResult["SECTION"]["ID"]));
foreach ($arResult["SECTION"]["PATH"] as $key => $arPath)
{
	$arPath["~SECTION_PAGE_URL"] = CComponentEngine::MakePathFromTemplate($arParams["~SECTION_URL"], 
		array("USER_ALIAS" => $arParams["USER_ALIAS"], "SECTION_ID" => $arPath["ID"]));
	$arPath["SECTION_PAGE_URL"] = htmlSpecialChars($arPath["~SECTION_PAGE_URL"]);
	$arResult["SECTION"]["PATH"][$key] = $arPath;
}
/********************************************************************
				/Default values
********************************************************************/

/********************************************************************
				Action
********************************************************************/
if ($_REQUEST["save_edit"] == "Y" || $_REQUEST["edit"] == "Y") 
{
	if ($_REQUEST["edit"] == "cancel")
	{
		LocalRedirect(CComponentEngine::MakePathFromTemplate(
			$arParams["~SECTION_URL"], 
				array("USER_ALIAS" => $arParams["USER_ALIAS"], "SECTION_ID" => $arResult["SECTION"]["ID"])));
	}
	elseif(!check_bitrix_sessid())
	{
		$arError[] = array(
			"id" => "bad sessid", 
			"text" => GetMessage("IBLOCK_WRONG_SESSION"));
	}
	elseif (count($_REQUEST["photos"]) <= 0)
	{
		$arError[] = array(
			"id" => "empty data", 
			"text" => GetMessage("P_NO_PHOTO"));
	}
	else
	{
		array_walk($_REQUEST, '__UnEscape');
		$arImages = array();
		$arrFilter = $arFilter;
		if (count($_REQUEST["photos"]) == 1):
			reset($_REQUEST["photos"]);
			$arrFilter["ID"] = current($_REQUEST["photos"]);
		endif;
		$db_res = CIBlockElement::GetList(array("ID" => "DESC"), $arrFilter, false, false, 
			array("ID", "CODE", "IBLOCK_ID", "NAME", "PREVIEW_PICTURE", "DETAIL_PICTURE", "PROPERTY_REAL_PICTURE"));
		if ($db_res && $arItem = $db_res->Fetch())
		{
			do 
			{
				if (!in_array($arItem["ID"], $_REQUEST["photos"])):
					continue;
				endif;
				foreach (array("PREVIEW_PICTURE", "DETAIL_PICTURE", "PROPERTY_REAL_PICTURE_VALUE") as $key):
					$iImage = intVal($arItem[$key]);
					if ($iImage <= 0):
						continue;
					endif;
					$arImage = CFile::GetFileArray($iImage);
					if ($arImage["WIDTH"] >= $arParams["ALBUM_PHOTO"]["WIDTH"] && $arImage["HEIGHT"] >= $arParams["ALBUM_PHOTO"]["HEIGHT"]):
						break;
					endif;
				endforeach;
				
				if ($arImage):
					$arImages[] = $arImage;
				endif;
			} while ($arItem = $db_res->Fetch());
		}
		
		if (empty($arImages))
		{
			$arError[] = array(
				"id" => "empty data", 
				"text" => GetMessage("P_NO_PHOTO"));
		}
		else 
		{
			$iCount = ceil(sqrt(count($arImages)));
			$arPhoto = array(
				"w" => ($arParams["ALBUM_PHOTO"]["WIDTH"]), 
				"h" => ($arParams["ALBUM_PHOTO"]["HEIGHT"]), 
				"width" => ($arParams["ALBUM_PHOTO"]["WIDTH"] / $iCount),
				"height" => ($arParams["ALBUM_PHOTO"]["HEIGHT"] / $iCount));
				
			$row = 0; $cell = 0; $count = 1;
			if ($bGD2):
				$picture = ImageCreateTrueColor($arPhoto["w"], $arPhoto["h"]);
			else:
				$picture = ImageCreate($arPhoto["w"], $arPhoto["h"]);
			endif;
			
			foreach ($arImages as $key => $arImage)
			{
				if ($cell >= $iCount):
					$cell = 0;
					$row++;
				endif;
	
				$dst = array(
					"width" => $arPhoto["width"], 
					"height" => $arPhoto["height"],
					"x" => ($cell * $arPhoto["width"]),
					"y" => ($row * $arPhoto["height"]));
				$src = array(
					"width" => $dst["width"],
					"height" => $dst["height"], 
					"x" => 0,
					"y" => 0);
	
				$cell++;
				$iResizeCoeff = 1;
				
				if ($arImage["WIDTH"] > 0 && $arImage["HEIGHT"] > 0) :
					$iResizeCoeff = max(
						($dst["width"] / $arImage["WIDTH"]), 
						($dst["height"] / $arImage["HEIGHT"]));
				endif;
				
				if ($iResizeCoeff > 0)
				{
					$src["x"] = ((($arImage["WIDTH"]*$iResizeCoeff - $dst["width"])/2)/$iResizeCoeff);
					$src["y"] = ((($arImage["HEIGHT"]*$iResizeCoeff - $dst["height"])/2)/$iResizeCoeff);
					$src["width"] = $dst["width"] / $iResizeCoeff;
					$src["height"] = $dst["height"] / $iResizeCoeff;
				}
				
				$src["pathinfo"] = pathinfo($arImage["SRC"]);
				$src["SRC"] = str_replace("//", "/", $_SERVER['DOCUMENT_ROOT']."/".$arImage["SRC"]);
				
				$imageInput = false;
				switch (strToLower($src["pathinfo"]["extension"]))
				{
					case 'gif':
						$imageInput = imagecreatefromgif($src["SRC"]);
					break;
					case 'png':
						$imageInput = imagecreatefrompng($src["SRC"]);
					break;
					case 'bmp':
						$imageInput = imagecreatefromgif($src["SRC"]);
					break;
					default:
						$imageInput = imagecreatefromjpeg($src["SRC"]);
					break;
				}
				$src["image"] = $imageInput;
				if ($bGD2):
					imagecopyresampled($picture, $src["image"], 
						$dst["x"], $dst["y"], $src["x"], $src["y"], 
						$dst["width"], $dst["height"], $src["width"], $src["height"]);
	//				UnsharpMask($picture);
				else:
					imagecopyresized($picture, $src["image"], 
						$dst["x"], $dst["y"], $src["x"], $src["y"], 
						$dst["width"], $dst["height"], $src["width"], $src["height"]);
				endif;
			}
			
			if ($bGD2):
				$thumbnail = ImageCreateTrueColor($arParams["ALBUM_PHOTO_THUMBS"]["WIDTH"], $arParams["ALBUM_PHOTO_THUMBS"]["HEIGHT"]);
				imagecopyresampled($thumbnail, $picture, 0, 0, 0, 0, 
					$arParams["ALBUM_PHOTO_THUMBS"]["WIDTH"], $arParams["ALBUM_PHOTO_THUMBS"]["HEIGHT"], 
					$arParams["ALBUM_PHOTO"]["HEIGHT"], $arParams["ALBUM_PHOTO"]["HEIGHT"]);
			else:
				$thumbnail = ImageCreate($arParams["ALBUM_PHOTO_THUMBS"]["WIDTH"], $arParams["ALBUM_PHOTO_THUMBS"]["HEIGHT"]);
				imagecopyresized($thumbnail, $picture, 0, 0, 0, 0, 
					$arParams["ALBUM_PHOTO_THUMBS"]["WIDTH"], $arParams["ALBUM_PHOTO_THUMBS"]["HEIGHT"], 
					$arParams["ALBUM_PHOTO"]["HEIGHT"], $arParams["ALBUM_PHOTO"]["HEIGHT"]);
			endif;
			
			CheckDirPath($arParams["PATH_TMP"]);
			
			imagejpeg($picture, $arParams["PATH_TMP"]."iblock_section_".$arResult["SECTION"]["ID"].".jpg", 95);
			imagejpeg($thumbnail, $arParams["PATH_TMP"]."iblock_section_thumbnail_".$arResult["SECTION"]["ID"].".jpg", 95);
			imagedestroy($picture);
			imagedestroy($thumbnail);
			
			$arFields = Array(
				"PICTURE" => array(
					"name" => "iblock_section_thumbnail_".$arResult["SECTION"]["ID"].".jpg",
		            "type" => "image/jpeg",
		            "tmp_name" => $arParams["PATH_TMP"]."iblock_section_thumbnail_".$arResult["SECTION"]["ID"].".jpg",
		            "size" => filesize($arParams["PATH_TMP"]."iblock_section_thumbnail_".$arResult["SECTION"]["ID"].".jpg"),
		            "MODULE_ID" => "iblock"),
				"DETAIL_PICTURE" => array(
					"name" => "iblock_section_".$arResult["SECTION"]["ID"].".jpg",
		            "type" => "image/jpeg",
		            "tmp_name" => $arParams["PATH_TMP"]."iblock_section_".$arResult["SECTION"]["ID"].".jpg",
		            "size" => filesize($arParams["PATH_TMP"]."iblock_section_".$arResult["SECTION"]["ID"].".jpg"),
		            "MODULE_ID" => "iblock"));
			
			$bs = new CIBlockSection;
			$res = $bs->Update($arResult["SECTION"]["ID"], $arFields);
			
			@unlink($arFields["PICTURE"]["tmp_name"]);
			@unlink($arFields["DETAIL_PICTURE"]["tmp_name"]);
			
			if(!$res)
			{
				$arError[] = array(
					"id" => "bad_update", 
					"text" => $bs->LAST_ERROR); 
			}
			else
			{
				PClearComponentCache(array("photogallery.section", "photogallery.section.list"));
				if ($arParams["AJAX_CALL"] == "Y")
				{
					$rsSection = CIBlockSection::GetList(Array(), array("ID" => $arResult["SECTION"]["ID"]));
					$arResult["SECTION"] = $rsSection->Fetch();
					$arResult["SECTION"]["DETAIL_PICTURE"] = CFile::GetFileArray($arResult["SECTION"]["DETAIL_PICTURE"]);
					$arFields = array(
						"ID" => $arResult["SECTION"]["ID"],
						"SRC" => $arResult["SECTION"]["DETAIL_PICTURE"]["SRC"],
						"error" => "");
					$APPLICATION->RestartBuffer();
					?><?=CUtil::PhpToJSObject($arFields);?><?
					die();
				}
				else 
				{
					LocalRedirect(CComponentEngine::MakePathFromTemplate($arParams["SECTION_URL"], 
						array("USER_ALIAS" => $arParams["USER_ALIAS"], "SECTION_ID" => $arResult["SECTION"]["ID"])));
				}
			}
		}
	}
	if (!empty($arError)):
		$e = new CAdminException($arError);
		$arResult["ERROR_MESSAGE"] = $e->GetString();
		$bVarsFromForm = true;
	endif;
}
/********************************************************************
				/Action
********************************************************************/

/********************************************************************
				Data
********************************************************************/
//PAGENAVIGATION
$arNavParams = array("nPageSize" => $arParams["PAGE_ELEMENTS"], "bDescPageNumbering" => true, "bShowAll" => false);
$arNavigation = CDBResult::GetNavParams($arNavParams);
$rsElements = CIBlockElement::GetList(array($arParams["ELEMENT_SORT_FIELD"] => $arParams["ELEMENT_SORT_ORDER"]), $arFilter, false, $arNavParams, $arSelect);
$rsElements->NavStart($arParams["PAGE_ELEMENTS"], false);
$arResult["NAV_STRING"] = $rsElements->GetPageNavStringEx($navComponentObject, GetMessage("P_PHOTO"), $arParams["PAGE_NAVIGATION_TEMPLATE"]);
$arResult["NAV_RESULT"] = $rsElements;
while($obElement = $rsElements->GetNextElement())
{
	$arItem = $obElement->GetFields();
	$arItem["PICTURE"] = CFile::GetFileArray($arItem["PREVIEW_PICTURE"]);
	$arResult["ELEMENTS"]["MAX_WIDTH"]	= max($arResult["ELEMENTS"]["MAX_WIDTH"], $arItem["PICTURE"]["WIDTH"]);
	$arResult["ELEMENTS"]["MAX_HEIGHT"]	= max($arResult["ELEMENTS"]["MAX_HEIGHT"], $arItem["PICTURE"]["HEIGHT"]);
	$arResult["ITEMS"][$arItem["ID"]] = $arItem;
}
/********************************************************************
				/Data
********************************************************************/

$this->IncludeComponentTemplate();

/********************************************************************
				Standart
********************************************************************/
/************** Title **********************************************/
$arResult["PAGE_TITLE"] = $arResult["SECTION"]["NAME"].GetMessage("P_TITLE");
if ($arParams["SET_TITLE"] == "Y")
	$APPLICATION->SetTitle($arResult["PAGE_TITLE"]);
/************** Chain Items ****************************************/
if ($arParams["SET_NAV_CHAIN"] != "N")
{
	$bFound = ($arParams["BEHAVIOUR"] != "USER");
	foreach($arResult["SECTION"]["PATH"] as $arPath)
	{
		if (!$bFound):
			$bFound = ($arResult["GALLERY"]["ID"] == $arPath["ID"]);
			continue;
		endif;
		$APPLICATION->AddChainItem($arPath["NAME"], CComponentEngine::MakePathFromTemplate($arParams["~SECTION_URL"], 
			array("USER_ALIAS" => $arParams["USER_ALIAS"], "SECTION_ID" => $arPath["ID"])));
	}
	$APPLICATION->AddChainItem(GetMessage("P_NAV_TITLE"));
}
/************** Admin panel ****************************************/
// if($arParams["DISPLAY_PANEL"] && $USER->IsAuthorized())
	// CIBlock::ShowPanel($arParams["IBLOCK_ID"], 0, $arResult["SECTION"]["ID"], $arParams["IBLOCK_TYPE"], false, $this->GetName());
/********************************************************************
				/Standart
********************************************************************/
?>