<?
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/wizard_sol/wizard.php");

class SelectSiteStep extends CSelectSiteWizardStep
{
	function InitStep()
	{
		parent::InitStep();

		$wizard =& $this->GetWizard();
		$wizard->solutionName = "eshop";
	}
}


class SelectTemplateStep extends CSelectTemplateWizardStep
{
	function InitStep()
	{
		$this->SetStepID("select_template");
		$this->SetTitle(GetMessage("SELECT_TEMPLATE_TITLE"));
		$this->SetSubTitle(GetMessage("SELECT_TEMPLATE_SUBTITLE"));
		//$this->SetPrevStep("welcome_step");
		$this->SetNextStep("select_theme");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
	}

	function OnPostForm()
	{
		$wizard =& $this->GetWizard();
		
		$proactive = COption::GetOptionString("statistic", "DEFENCE_ON", "N");
		if ($proactive == "Y")
		{
			COption::SetOptionString("statistic", "DEFENCE_ON", "N");
			$wizard->SetVar("proactive", "Y");
		}
		else
		{
			$wizard->SetVar("proactive", "N");			
		}

		if ($wizard->IsNextButtonClick())
		{
			$arTemplates = array("eshop_vertical", "eshop_horizontal", "eshop_vertical_popup");

			$templateID = $wizard->GetVar("wizTemplateID");

			if (!in_array($templateID, $arTemplates))
				$this->SetError(GetMessage("wiz_template"));

		}
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();
			
       /*
		$templatesPath = WizardServices::GetTemplatesPath($wizard->GetPath()."/site");
		$arTemplates = WizardServices::GetTemplates($templatesPath);

		if (empty($arTemplates))
			return;  */

		$arTemplateOrder = array("eshop_horizontal", "eshop_vertical", "eshop_vertical_popup");

		$defaultTemplateID = COption::GetOptionString("main", "wizard_template_id", "eshop_horizontal", $wizard->GetVar("siteID"));
		if (!in_array($defaultTemplateID, array("eshop_vertical", "eshop_horizontal", "eshop_vertical_popup"))) $defaultTemplateID = "eshop_horizontal";
        $wizard->SetDefaultVar("wizTemplateID", $defaultTemplateID);

		$arTemplateInfo = array(
			"eshop_horizontal" => array(
				"NAME" => GetMessage("WIZ_TEMPLATE_HORIZONTAL"),
				"DESCRIPTION" => "",
				"PREVIEW" => $wizard->GetPath()."/site/templates/eshop/lang/".LANGUAGE_ID."/preview_horizontal.gif",
				"SCREENSHOT" => $wizard->GetPath()."/site/templates/eshop/lang/".LANGUAGE_ID."/screen_horizontal.gif",
			),
			"eshop_vertical" => array(
				"NAME" => GetMessage("WIZ_TEMPLATE_VERTICAL"),
				"DESCRIPTION" => "",
				"PREVIEW" => $wizard->GetPath()."/site/templates/eshop/lang/".LANGUAGE_ID."/preview_vertical.gif",
				"SCREENSHOT" => $wizard->GetPath()."/site/templates/eshop/lang/".LANGUAGE_ID."/screen_vertical.gif",
			),	
			"eshop_vertical_popup" => array(
				"NAME" => GetMessage("WIZ_TEMPLATE_VERTICAL_POPUP"),
				"DESCRIPTION" => "",
				"PREVIEW" => $wizard->GetPath()."/site/templates/eshop/lang/".LANGUAGE_ID."/preview_vertical_popup.gif",
				"SCREENSHOT" => $wizard->GetPath()."/site/templates/eshop/lang/".LANGUAGE_ID."/screen_vertical_popup.gif",
			),		
		);

		$wizard->SetVar("templateID", "eshop");
		$this->content .= "<input type='hidden' value='eshop' name='templateID' id='templateID'>";//$this->ShowInputField('hidden', 'templateID', array("id" => "templateID", "value" => "eshop"));

		$this->content .= '<table width="100%" cellspacing="4" cellpadding="8">'; // echo $defaultTemplateID;
		foreach ($arTemplateOrder as $templateID)
		{
			$arTemplate = $arTemplateInfo[$templateID];

			if (!$arTemplate)
				continue;

			$this->content .= "<tr>";
			$this->content .= '<td width="25">'.$this->ShowRadioField("wizTemplateID", $templateID, Array("id" => $templateID))."</td>";

			if ($arTemplate["SCREENSHOT"] && $arTemplate["PREVIEW"])
				$this->content .= '<td width="160" valign="top">'.CFile::Show2Images($arTemplate["PREVIEW"], $arTemplate["SCREENSHOT"], 150, 150, ' border="0"')."</td>";
			else
				$this->content .= '<td width="160" valign="top">'.CFile::ShowImage($arTemplate["SCREENSHOT"], 150, 150, ' border="0"', "", true)."</td>";

			$this->content .= '<td valign="top"><label for="'.$templateID.'"><b>'.$arTemplate["NAME"]."</b><p>".$arTemplate["DESCRIPTION"]."</p></label></td>";

			$this->content .= "</tr>";
			$this->content .= "<tr><td><br /></td></tr>";
		}

		$this->content .= "</table>";
		$this->content .= '<script>
			function ImgShw(ID, width, height, alt)
			{
				var scroll = "no";
				var top=0, left=0;
				if(width > screen.width-10 || height > screen.height-28) scroll = "yes";
				if(height < screen.height-28) top = Math.floor((screen.height - height)/2-14);
				if(width < screen.width-10) left = Math.floor((screen.width - width)/2-5);
				width = Math.min(width, screen.width-10);
				height = Math.min(height, screen.height-28);
				var wnd = window.open("","","scrollbars="+scroll+",resizable=yes,width="+width+",height="+height+",left="+left+",top="+top);
				wnd.document.write(
					"<html><head>"+
						"<"+"script type=\"text/javascript\">"+
						"function KeyPress()"+
						"{"+
						"	if(window.event.keyCode == 27) "+
						"		window.close();"+
						"}"+
						"</"+"script>"+
						"<title></title></head>"+
						"<body topmargin=\"0\" leftmargin=\"0\" marginwidth=\"0\" marginheight=\"0\" onKeyPress=\"KeyPress()\">"+
						"<img src=\""+ID+"\" border=\"0\" alt=\""+alt+"\" />"+
						"</body></html>"
				);
				wnd.document.close();
			}
		</script>';
	}
}

class SelectThemeStep extends CSelectThemeWizardStep
{
	function InitStep()
	{
		$this->SetStepID("select_theme");
		$this->SetTitle(GetMessage("SELECT_THEME_TITLE"));
		$this->SetSubTitle(GetMessage("SELECT_THEME_SUBTITLE"));
		$this->SetPrevStep("select_template");
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));
		$this->SetNextStep("site_settings");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
	}

	function OnPostForm()
	{
		$wizard =& $this->GetWizard();

		if ($wizard->IsNextButtonClick())
		{
			$templateID = $wizard->GetVar("templateID");
			$themeVarName = $templateID."_themeID";
			$themeID = $wizard->GetVar($themeVarName);

			$templatesPath = WizardServices::GetTemplatesPath($wizard->GetPath()."/site");
			$arThemes = WizardServices::GetThemes($templatesPath."/".$templateID."/themes");

			if (!array_key_exists($themeID, $arThemes))
				$this->SetError(GetMessage("wiz_template_color"));
		}
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();
		$templateID = $wizard->GetVar("templateID");

		$templatesPath = WizardServices::GetTemplatesPath($wizard->GetPath()."/site");
		$arThemes = WizardServices::GetThemes($templatesPath."/".$templateID."/themes");

		if (empty($arThemes))
			return;

		$themeVarName = $templateID."_themeID";
		$ThemeID = $wizard->GetVar($templateID."_themeID");

		if(isset($ThemeID) && array_key_exists($ThemeID, $arThemes)){
			$defaultThemeID = $ThemeID;
			$wizard->SetDefaultVar($themeVarName, $ThemeID);
		} else {
			$defaultThemeID = COption::GetOptionString("main", "wizard_".$templateID."_theme_id", "", $wizard->GetVar("siteID"));

			if (!(strlen($defaultThemeID) > 0 && array_key_exists($defaultThemeID, $arThemes)))
			{
				$defaultThemeID = COption::GetOptionString("main", "wizard_".$templateID."_theme_id", "");
				if (strlen($defaultThemeID) > 0 && array_key_exists($defaultThemeID, $arThemes))
					$wizard->SetDefaultVar($themeVarName, $defaultThemeID);
				else
					$defaultThemeID = "";
			}
		}

		$this->content =
			'<script type="text/javascript">
				
				function SelectTheme(element, solutionId, imageUrl)
				{
					var container = document.getElementById("solutions-container");
					var anchors = container.getElementsByTagName("A");
					for (var i = 0; i < anchors.length; i++)
					{
						if (anchors[i].parentNode.parentNode.parentNode.parentNode.parentNode != container)
							continue;
						anchors[i].className = "solution-item solution-picture-item";
					}
					element.className = "solution-item  solution-picture-item solution-item-selected";
					var hidden = document.getElementById("selected-solution");
					if (!hidden)
					{
						hidden = document.createElement("INPUT");
						hidden.type = "hidden"
						hidden.id = "selected-solution";
						hidden.name = "selected-solution";
						container.appendChild(hidden);
					}
					hidden.value = solutionId;
				
					var preview = document.getElementById("solution-preview");
					if (!imageUrl)
						preview.style.display = "none";
					else
					{
						document.getElementById("solution-preview-image").src = imageUrl;
						preview.style.display = "";
					}
				}
				</script>'.
				'<div id="html_container">'.
				'<div style="overflow: hidden; margin:0 auto;text-align:center" id="solutions-container">';
				
		$arDefaultTheme = array();
		$arThemesOrder = array("blue", "yellow", "green", "wood", "red", "gray");

		$this->content .= '<table><tr>';
		foreach ($arThemesOrder as $themeID)
		{
			$arTheme = $arThemes[$themeID];
			if ($defaultThemeID == "")
			{
				$defaultThemeID = $themeID;
				$wizard->SetDefaultVar($themeVarName, $defaultThemeID);
			}
			if ($defaultThemeID == $themeID)
				$arDefaultTheme = $arTheme;
	
			$this->content .= 
				'<td class="solution-item-wrapper">'.
				'<a ondblclick="SubmitForm(\'next\'); return false;" onclick="SelectTheme(this, \''.$themeID.'\', \''.$arTheme["SCREENSHOT"].'\'); return false;" '.
					'href="javascript:void(0);" class="solution-item solution-picture-item'.($defaultThemeID == $themeID ? " solution-item-selected" : "").'">'.
					'<b class="r3"></b><b class="r1"></b><b class="r1"></b>'.
					'<div class="solution-inner-item">'.
					CFile::ShowImage($arTheme["PREVIEW"], 70, 70, ' border="0" class="solution-image"').
					'</div>'.
					'<b class="r1"></b><b class="r1"></b><b class="r3"></b>'.
					'<div class="solution-description">'.$arTheme["NAME"].'</div>'.
					'</a>'
				.'</td>';	
		}
		$this->content .= '</tr></table>';

		$this->content .= $this->ShowHiddenField($themeVarName, $defaultThemeID, array("id" => "selected-solution"));
		$this->content .=
			'</div>'.
				'<div id="solution-preview">'.
				'<b class="r3"></b><b class="r1"></b><b class="r1"></b>'.
				'<div class="solution-inner-item">'.
				CFile::ShowImage($arDefaultTheme["SCREENSHOT"], 450, 450, ' border="0" id="solution-preview-image"').
				'</div>'.
				'<b class="r1"></b><b class="r1"></b><b class="r3"></b>'.
				'</div>'.
				'</div>';
	}
}

class SiteSettingsStep extends CSiteSettingsWizardStep
{
	function InitStep()
	{
		$wizard =& $this->GetWizard();
		$wizard->solutionName = "eshop";
		parent::InitStep();

		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetTitle(GetMessage("WIZ_STEP_SITE_SET"));

		$siteID = $wizard->GetVar("siteID");
		
		if(COption::GetOptionString("eshop", "wizard_installed", "N", $siteID) == "Y" && !WIZARD_INSTALL_DEMO_DATA)
			$this->SetNextStep("data_install");
		else
		{
			if(LANGUAGE_ID != "ru")
				$this->SetNextStep("pay_system");
			else
			$this->SetNextStep("catalog_settings");
		}
		
		$templateID = $wizard->GetVar("templateID");
		
		$wizard->SetDefaultVars(Array("siteNameSet" => true));

		
		/*if($wizard->GetVar('siteLogoSet', true)){
			$themeID = $wizard->GetVar($templateID."_themeID");
			$siteLogo = $this->GetFileContentImgSrc(WIZARD_SITE_PATH."include/company_logo.php", "/bitrix/wizards/bitrix/eshop/site/templates/store_light/themes/".$themeID."/lang/".LANGUAGE_ID."/logo.jpg");
			if (!file_exists(WIZARD_SITE_PATH."include/logo.jpg"))
			$siteLogo = "/bitrix/wizards/bitrix/eshop/site/templates/store_light/themes/".$themeID."/lang/".LANGUAGE_ID."/logo.jpg";
			$wizard->SetDefaultVars(Array("siteLogo" => $siteLogo));
		}   */
		$wizard->SetDefaultVars(
			Array(
				"siteName" => $this->GetFileContent(WIZARD_SITE_PATH."include/company_name.php", GetMessage("WIZ_COMPANY_NAME_DEF")),
				"siteSchedule" => $this->GetFileContent(WIZARD_SITE_PATH."include/schedule.php", GetMessage("WIZ_COMPANY_SCHEDULE_DEF")),
				"siteTelephone" => $this->GetFileContent(WIZARD_SITE_PATH."include/telephone.php", GetMessage("WIZ_COMPANY_TELEPHONE_DEF")),
				"siteCopy" => $this->GetFileContent(WIZARD_SITE_PATH."include/copyright.php", GetMessage("WIZ_COMPANY_COPY_DEF")),
				"shopEmail" => COption::GetOptionString("eshop", "shopEmail", "sale@".$_SERVER["SERVER_NAME"], $siteID),
				"siteMetaDescription" => GetMessage("wiz_site_desc"),
				"siteMetaKeywords" => GetMessage("wiz_keywords"),
				"shopFacebook" => COption::GetOptionString("eshop", "shopFacebook", "http://www.facebook.com/1CBitrix", $siteID),
				"shopTwitter" => COption::GetOptionString("eshop", "shopTwitter", "http://twitter.com/1C_Bitrix", $siteID),
				"shopVk" => COption::GetOptionString("eshop", "shopVK", "http://vk.com/bitrix_1c", $siteID),
				"shopGooglePlus" => COption::GetOptionString("eshop", "shopGooglePlus", "https://plus.google.com/111119180387208976312/", $siteID),	
			)
		);
		
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();
		
		$this->content .= '<div class="wizard-input-form">';
		if($wizard->GetVar('siteNameSet', true)){
			$this->content .= '
			<div class="wizard-input-form-block">
				<h4><label for="siteName">'.GetMessage("WIZ_COMPANY_NAME").'</label></h4>
				<div class="wizard-input-form-block-content">
					<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'siteName', array("id" => "siteName")).'</div>
				</div>
			</div>';
		}
		
		if($wizard->GetVar('siteLogoSet', true)){
			$siteLogo = $wizard->GetVar("siteLogo", true);
	
			$this->content .= '
			<div class="wizard-input-form-block">
				<h4><label for="siteName">'.GetMessage("WIZ_COMPANY_LOGO").'</label></h4>
				<div class="wizard-input-form-block-content">
					<div class="wizard-input-form-field wizard-input-form-field-text">'.CFile::ShowImage($siteLogo, 280, 40, "border=0 vspace=15") . '<br>' . $this->ShowFileField("siteLogo", Array("show_file_info" => "N", "id" => "siteLogo")).'</div>
				</div>
			</div>';
		}
		
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4><label for="siteTelephone">'.GetMessage("WIZ_COMPANY_TELEPHONE").'</label></h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'siteTelephone', array("id" => "siteTelephone")).'</div>
			</div>
		</div>';
		
		if(LANGUAGE_ID != "ru")
		{
			$this->content .= '<div class="wizard-input-form-block">
				<h4><label for="shopEmail">'.GetMessage("WIZ_SHOP_EMAIL").'</label></h4>
				<div class="wizard-input-form-block-content">
					<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopEmail', array("id" => "shopEmail")).'</div>
				</div>
			</div>';	
		}
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4><label for="siteSchedule">'.GetMessage("WIZ_COMPANY_SCHEDULE").'</label></h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-textarea">'.$this->ShowInputField('textarea', 'siteSchedule', array("rows"=>"3", "id" => "siteSchedule")).'</div>
			</div>
		</div>';	
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4><label for="siteCopy">'.GetMessage("WIZ_COMPANY_COPY").'</label></h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-textarea">'.$this->ShowInputField('textarea', 'siteCopy', array("rows"=>"3", "id" => "siteCopy")).'</div>
			</div>
		</div>';
//SocNets
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4><label for="shopFacebook">'.GetMessage("WIZ_SHOP_FACEBOOK").'</label></h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopFacebook', array("id" => "shopFacebook")).'</div>
			</div>
		</div>';
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4><label for="shopTwitter">'.GetMessage("WIZ_SHOP_TWITTER").'</label></h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopTwitter', array("id" => "shopTwitter")).'</div>
			</div>
		</div>';
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4><label for="shopGooglePlus">'.GetMessage("WIZ_SHOP_GOOGLE_PLUS").'</label></h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopGooglePlus', array("id" => "shopGooglePlus")).'</div>
			</div>
		</div>';
		if(LANGUAGE_ID == "ru"):
			$this->content .= '
			<div class="wizard-input-form-block">
				<h4><label for="shopVk">'.GetMessage("WIZ_SHOP_VK").'</label></h4>
				<div class="wizard-input-form-block-content">
					<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopVk', array("id" => "shopVk")).'</div>
				</div>
			</div>';
		endif;		
/*---*/		
		$firstStep = COption::GetOptionString("main", "wizard_first" . substr($wizard->GetID(), 7)  . "_" . $wizard->GetVar("siteID"), false, $wizard->GetVar("siteID")); 
		$styleMeta = 'style="display:block"';
		if($firstStep == "Y") $styleMeta = 'style="display:none"';

		$this->content .= '
		<div  id="bx_metadata" '.$styleMeta.'>
			<div class="wizard-input-form-block">
				<h4 style="margin-top:0"><label for="siteMetaDescription">'.GetMessage("wiz_meta_data").'</label></h4>
				<label for="siteMetaDescription">'.GetMessage("wiz_meta_description").'</label>
				<div class="wizard-input-form-block-content" style="margin-top:7px;">
					<div class="wizard-input-form-field wizard-input-form-field-textarea">'.
						$this->ShowInputField("textarea", "siteMetaDescription", Array("id" => "siteMetaDescription", "style" => "width:100%", "rows"=>"3")).'</div>
				</div>
			</div>';
		$this->content .= '
			<div class="wizard-input-form-block">
				<label for="siteMetaKeywords">'.GetMessage("wiz_meta_keywords").'</label><br>
				<div class="wizard-input-form-block-content" style="margin-top:7px;">
					<div class="wizard-input-form-field wizard-input-form-field-text">'.
						$this->ShowInputField('text', 'siteMetaKeywords', array("id" => "siteMetaKeywords")).'</div>
				</div>
			</div>
		</div>';
		
//install Demo data		
		if($firstStep == "Y")
		{
			$this->content .= '
			<div class="wizard-input-form-block">
				<div class="wizard-input-form-block-content">'.
						$this->ShowCheckboxField(
							"installDemoData", 
							"Y", 
							(array("id" => "installDemoData", "onClick" => "if(this.checked == true){document.getElementById('bx_metadata').style.display='block';}else{document.getElementById('bx_metadata').style.display='none';}"))
						).
				'
				<label for="installDemoData">'.GetMessage("wiz_structure_data").'</label>
				</div>
			</div>';
			}
		else
		{
			$this->content .= $this->ShowHiddenField("installDemoData","Y");
		}
	/*	$defaultTemplateID = COption::GetOptionString("main", "wizard_template_id", "", $wizard->GetVar("siteID")); 
		if(!empty($defaultTemplateID) && $defaultTemplateID != $wizard->GetVar("templateID")){
			$this->content .= '
			<div class="wizard-input-form-block">
				<h4><label for="siteSchedule">'.GetMessage("WIZ_REWRITE_INDEX_DESC").'</label></h4>
				<div class="wizard-input-form-block-content">'.
						$this->ShowCheckboxField(
							"rewriteIndex", 
							"Y", 
							(array("id" => "rewriteIndex"))
						).
				'
				<label for="installDemoData">'.GetMessage("wiz_rewrite_index").'</label>
				</div>
			</div>';	
		}       */
		
		if(LANGUAGE_ID != "ru")
		{
			CModule::IncludeModule("catalog");
			$db_res = CCatalogGroup::GetGroupsList(array("CATALOG_GROUP_ID"=>'1', "BUY"=>"Y", "GROUP_ID"=>2));
			if (!$db_res->Fetch())
			{
				$this->content .= '
				<div class="wizard-input-form-block">
					<h4><label for="shopAdr">'.GetMessage("WIZ_SHOP_PRICE_BASE_TITLE").'</label></h4>
					<div class="wizard-input-form-block-content">
						'. GetMessage("WIZ_SHOP_PRICE_BASE_TEXT1") .'<br><br>
						'. $this->ShowCheckboxField("installPriceBASE", "Y", 
						(array("id" => "install-demo-data")))
						. ' <label for="install-demo-data">'.GetMessage("WIZ_SHOP_PRICE_BASE_TEXT2").'</label><br />
						
					</div>
				</div>';	
			}
		}
		
		$this->content .= '</div>';
	}
	function OnPostForm()
	{
		$wizard =& $this->GetWizard();
		$res = $this->SaveFile("siteLogo", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 40, "max_width" => 280, "make_preview" => "Y"));
	}
}

class CatalogSettings extends CWizardStep
{
	function InitStep()
	{
		$this->SetStepID("catalog_settings");
		$this->SetTitle(GetMessage("WIZ_STEP_CT"));
		$this->SetNextStep("shop_settings");
		$this->SetPrevStep("site_settings");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));

		$wizard =& $this->GetWizard();
		$siteID = $wizard->GetVar("siteID");
		
		$subscribe = COption::GetOptionString("sale", "subscribe_prod", "");
		$arSubscribe = unserialize($subscribe);

		$wizard->SetDefaultVars(
			Array(
				"catalogSmartFilter" => COption::GetOptionString("eshop", "catalogSmartFilter", "Y", $siteID),
				"catalogCompare" => COption::GetOptionString("eshop", "catalogCompare", "Y", $siteID),
				"catalogSubscribe" => (isset($arSubscribe[$siteID])) ? ($arSubscribe[$siteID]['use'] == "Y" ? "Y" : false) : "Y",//COption::GetOptionString("eshop", "catalogSubscribe", "Y", $siteID),
				"catalogView" => COption::GetOptionString("eshop", "catalogView", "list", $siteID),
				"catalogElementCount" => COption::GetOptionInt("eshop", "catalogElementCount", "25", $siteID),
				"catalogDetailDescr" => COption::GetOptionString("eshop", "catalogDetailDescr", "list", $siteID),
				"catalogDetailSku" => COption::GetOptionString("eshop", "catalogDetailSku", "select", $siteID),
				"useIdea" => COption::GetOptionString("eshop", "useIdea", "N", $siteID),
			)
		);
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();   // echo $wizard->GetDefaultVar("catalogView");
		$this->content .= '
			<div class="wizard-input-form-block">
				<h4><label for="shopAdr">'.GetMessage("WIZ_STEP_CT").'</label></h4>
				<div class="wizard-input-form-block-content">
					'. $this->ShowCheckboxField("catalogSmartFilter", "Y", (array("id" => "catalog-filter")))
					.' <label for="catalog-filter">'.GetMessage("WIZ_CATALOG_FILTER").'</label><br />'
					.' <p style="color:grey; margin: 3px 0 7px 24px;">'.GetMessage("WIZ_CATALOG_FILTER_DESCR").'</p>
					'. $this->ShowCheckboxField("catalogCompare", "Y", (array("id" => "catalog-compare")))
					.' <label for="catalog-compare">'.GetMessage("WIZ_CATALOG_COMPARE").'</label><br />'
					.' <p style="color:grey; margin: 3px 0 7px 24px;">'.GetMessage("WIZ_CATALOG_COMPARE_DESCR").'</p>
					'. $this->ShowCheckboxField("catalogSubscribe", "Y", (array("id" => "catalog-suscribe")))
					.' <label for="catalog-suscribe">'.GetMessage("WIZ_CATALOG_SUBSCRIBE").'</label><br />'
					.' <p style="color:grey; margin: 3px 0 7px 24px;">'.GetMessage("WIZ_CATALOG_SUBSCRIBE_DESCR").'</p>
					'. $this->ShowSelectField("catalogElementCount", array("15" => "15", "25" => "25", "50" => "50",))
					.' <label for="catalogElementCount">'.GetMessage("WIZ_CATALOG_COUNT").'</label><br />
				</div>
			</div>';

		$this->content .= '
			<div class="wizard-input-form-block">
				<h4><label for="shopAdr">'.GetMessage("WIZ_CATALOG_VIEW").'</label></h4>
				<div class="wizard-input-form-block-content">
				'. GetMessage("WIZ_CATALOG_VIEWS_TEXT") .'<br><br>';

		$this->content .= '<table width="100%" cellspacing="4" cellpadding="8">';
		$arCatalogViews = array(
			"bar" => array(
				"NAME" => GetMessage("WIZ_CATALOG_VIEW_BAR"),
				"DESCRIPTION" => GetMessage("WIZ_CATALOG_VIEW_BAR_DESCR"),
				"PREVIEW" => $wizard->GetPath()."/images/".LANGUAGE_ID."/view-bar-small.png",
				"SCREENSHOT" => $wizard->GetPath()."/images/".LANGUAGE_ID."/view-bar.png",
			),
			"list" => array(
				"NAME" => GetMessage("WIZ_CATALOG_VIEW_LIST"),
				"DESCRIPTION" => GetMessage("WIZ_CATALOG_VIEW_LIST_DESCR"),
				"PREVIEW" => $wizard->GetPath()."/images/".LANGUAGE_ID."/view-list-small.png",
				"SCREENSHOT" => $wizard->GetPath()."/images/".LANGUAGE_ID."/view-list.png",
			),
			"price_list" => array(
				"NAME" => GetMessage("WIZ_CATALOG_VIEW_PRICE_LIST"),
				"DESCRIPTION" => GetMessage("WIZ_CATALOG_VIEW_PRICE_LIST_DESCR"),
				"PREVIEW" => $wizard->GetPath()."/images/".LANGUAGE_ID."/view-price-list-small.png",
				"SCREENSHOT" => $wizard->GetPath()."/images/".LANGUAGE_ID."/view-price-list.png",
			)
		);

		/*foreach ($arCatalogViews as $catalogViewID => $catalogView)
		{
			$this->content .= "<tr>";
			$this->content .= '<td width="25">'.$this->ShowCheckboxField("catalogView[]", $catalogViewID, Array("id" => $catalogID))."</td>";

			if ($catalogView["SCREENSHOT"] && $catalogView["PREVIEW"])
				$this->content .= '<td width="160" valign="top">'.CFile::Show2Images($catalogView["PREVIEW"], $catalogView["SCREENSHOT"], 150, 150, ' border="0"')."</td>";
			else
				$this->content .= '<td width="160" valign="top">'.CFile::ShowImage($catalogView["SCREENSHOT"], 150, 150, ' border="0"', "", true)."</td>";

			$this->content .= '<td valign="top"><label for="'.$catalogViewID.'"><b>'.$catalogView["NAME"]."</b><p>".$catalogView["DESCRIPTION"]."</p></label></td>";

			$this->content .= "</tr>";
			$this->content .= "<tr><td><br /></td></tr>";
		}      */
		$this->content .= "<tr>";
		foreach ($arCatalogViews as $catalogViewID => $catalogView)
		{

			$this->content .= '<td >'.$this->ShowRadioField("catalogView", $catalogViewID, Array("id" => $catalogViewID))."</td>";

			if ($catalogView["SCREENSHOT"] && $catalogView["PREVIEW"])
				$this->content .= '<td valign="top">'.CFile::Show2Images($catalogView["PREVIEW"], $catalogView["SCREENSHOT"], 100, 100, ' border="0"');
			else
				$this->content .= '<td valign="top">'.CFile::ShowImage($catalogView["SCREENSHOT"], 100, 100, ' border="0"', "", true);

			$this->content .= '<br><label for="'.$catalogViewID.'"><b>'.$catalogView["NAME"]."</b></label>
			<p style='color:grey'>".$catalogView["DESCRIPTION"]."</p>
			</td>";
			

		}
		$this->content .= "</tr>";
		$this->content .= "</table>";

		$this->content .=
			'</div>
		</div>';

		$this->content .= '
			<div class="wizard-input-form-block">
				<h4><label for="shopAdr">'.GetMessage("WIZ_CATALOG_DETAIL_DESCR").'</label></h4>
				<div class="wizard-input-form-block-content">
					'. $this->ShowRadioField("catalogDetailDescr", "list", (array("id" => "catalog-detail-list")))
					.'<label for="catalog-detail-list">'.GetMessage("WIZ_CATALOG_DETAIL_DESCR_LIST").'</label><br />'
					.'<p style="color:grey; margin: 3px 0 7px 21px;">'.GetMessage("WIZ_CATALOG_DETAIL_DESCR_LIST_DESCR").'</p>
					'. $this->ShowRadioField("catalogDetailDescr", "tabs", (array("id" => "catalog-detail-tabs")))
					.'<label for="catalog-detail-tabs">'.GetMessage("WIZ_CATALOG_DETAIL_DESCR_TABS").'</label><br />'
					.'<p style="color:grey; margin: 3px 0 7px 21px;">'.GetMessage("WIZ_CATALOG_DETAIL_DESCR_TABS_DESCR").'</p>
				</div>
			</div>';
		$this->content .= '
			<div class="wizard-input-form-block">
				<h4><label for="shopAdr">'.GetMessage("WIZ_CATALOG_DETAIL_SKU").'</label></h4>
				<div class="wizard-input-form-block-content">
					'. $this->ShowRadioField("catalogDetailSku", "select", (array("id" => "catalog-sku-select")))
					.'<label for="catalog-sku-select">'.GetMessage("WIZ_CATALOG_DETAIL_SKU_SELECT").'</label><br />'
					.'<p style="color:grey; margin: 3px 0 7px 21px;">'.GetMessage("WIZ_CATALOG_DETAIL_SKU_SELECT_DESCR").'</p>
					'. $this->ShowRadioField("catalogDetailSku", "list", (array("id" => "catalog-sku-list")))
					.'<label for="catalog-sku-list">'.GetMessage("WIZ_CATALOG_DETAIL_SKU_LIST").'</label><br />'
					.'<p style="color:grey; margin: 3px 0 7px 21px;">'.GetMessage("WIZ_CATALOG_DETAIL_SKU_LIST_DESCR").'</p>
				</div>
			</div>';
		$this->content .= '
			<div class="wizard-input-form-block">
				<h4><label for="shopAdr">'.GetMessage("WIZ_CATALOG_USE_IDEA").'</label></h4>
				<div class="wizard-input-form-block-content">
					'.$this->ShowCheckboxField("useIdea", "Y", array("id" => "use-idea"))
					.'<label for="use-idea">'.GetMessage("WIZ_CATALOG_IDEA").'</label><br />					
				</div>
			</div>';
		
		$this->content .= '<script>
			function ImgShw(ID, width, height, alt)
			{
				var scroll = "no";
				var top=0, left=0;
				if(width > screen.width-10 || height > screen.height-28) scroll = "yes";
				if(height < screen.height-28) top = Math.floor((screen.height - height)/2-14);
				if(width < screen.width-10) left = Math.floor((screen.width - width)/2-5);
				width = Math.min(width, screen.width-10);
				height = Math.min(height, screen.height-28);
				var wnd = window.open("","","scrollbars="+scroll+",resizable=yes,width="+width+",height="+height+",left="+left+",top="+top);
				wnd.document.write(
					"<html><head>"+
						"<"+"script type=\"text/javascript\">"+
						"function KeyPress()"+
						"{"+
						"	if(window.event.keyCode == 27) "+
						"		window.close();"+
						"}"+
						"</"+"script>"+
						"<title></title></head>"+
						"<body topmargin=\"0\" leftmargin=\"0\" marginwidth=\"0\" marginheight=\"0\" onKeyPress=\"KeyPress()\">"+
						"<img src=\""+ID+"\" border=\"0\" alt=\""+alt+"\" />"+
						"</body></html>"
				);
				wnd.document.close();
			}
		</script>';
		
	}

	function OnPostForm()
	{
		$wizard =& $this->GetWizard();

	}
}

class ShopSettings extends CWizardStep
{
	function InitStep()
	{
		$this->SetStepID("shop_settings");
		$this->SetTitle(GetMessage("WIZ_STEP_SS"));
		$this->SetNextStep("person_type");
		$this->SetPrevStep("catalog_settings");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));

		$wizard =& $this->GetWizard();

		$siteStamp =$wizard->GetPath()."/site/templates/minimal/images/pechat.gif";
		$siteID = $wizard->GetVar("siteID");
		
		$wizard->SetDefaultVars(
			Array(
				"shopLocalization" => COption::GetOptionString("eshop", "shopLocalization", "ru", $siteID),
				"shopEmail" => COption::GetOptionString("eshop", "shopEmail", "sale@".$_SERVER["SERVER_NAME"], $siteID),
				"shopOfName" => COption::GetOptionString("eshop", "shopOfName", GetMessage("WIZ_SHOP_OF_NAME_DEF"), $siteID),
				"shopLocation" => COption::GetOptionString("eshop", "shopLocation", GetMessage("WIZ_SHOP_LOCATION_DEF"), $siteID),
				//"shopZip" => 101000,
				"shopAdr" => COption::GetOptionString("eshop", "shopAdr", GetMessage("WIZ_SHOP_ADR_DEF"), $siteID),
				"shopINN" => COption::GetOptionString("eshop", "shopINN", "1234567890", $siteID),
				"shopKPP" => COption::GetOptionString("eshop", "shopKPP", "123456789", $siteID),
				"shopNS" => COption::GetOptionString("eshop", "shopNS", "0000 0000 0000 0000 0000", $siteID),
				"shopBANK" => COption::GetOptionString("eshop", "shopBANK", GetMessage("WIZ_SHOP_BANK_DEF"), $siteID),
				"shopBANKREKV" => COption::GetOptionString("eshop", "shopBANKREKV", GetMessage("WIZ_SHOP_BANKREKV_DEF"), $siteID),
				"shopKS" => COption::GetOptionString("eshop", "shopKS", "30101 810 4 0000 0000225", $siteID),
				"siteStamp" => COption::GetOptionString("eshop", "siteStamp", $siteStamp, $siteID),

				"shopCompany_ua" => COption::GetOptionString("eshop", "shopCompany_ua", "", $siteID),
				"shopEGRPU_ua" =>  COption::GetOptionString("eshop", "shopCompany_ua", "", $siteID),
				"shopINN_ua" =>  COption::GetOptionString("eshop", "shopINN_ua", "", $siteID),
				"shopNDS_ua" =>  COption::GetOptionString("eshop", "shopNDS_ua", "", $siteID),
				"shopNS_ua" =>  COption::GetOptionString("eshop", "shopNS_ua", "", $siteID),
				"shopBank_ua" =>  COption::GetOptionString("eshop", "shopBank_ua", "", $siteID),
				"shopMFO_ua" =>  COption::GetOptionString("eshop", "shopMFO_ua", "", $siteID),
				"shopPlace_ua" =>  COption::GetOptionString("eshop", "shopPlace_ua", "", $siteID),
				"shopFIO_ua" =>  COption::GetOptionString("eshop", "shopFIO_ua", "", $siteID),
				"shopTax_ua" =>  COption::GetOptionString("eshop", "shopTax_ua", "", $siteID),

				"installPriceBASE" => COption::GetOptionString("eshop", "installPriceBASE", "Y", $siteID),
			)
		);
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();
		$siteStamp = $wizard->GetVar("siteStamp", true);

		if (!CModule::IncludeModule("catalog"))
		{
			$this->content .= "<p style='color:red'>".GetMessage("WIZ_NO_MODULE_CATALOG")."</p>";
			$this->SetNextStep("shop_settings");
		}
		else
		{
			/*$this->content .=
				$this->ShowSelectField("shopLocalization", array("ru" => GetMessage("WIZ_SHOP_LOCALIZATION_RUSSIA"), "ua" => GetMessage("WIZ_SHOP_LOCALIZATION_UKRAINE")), array("onchange" => "langReload()", "id" => "localization_select"))
				.' <label for="shopLocalization">'.GetMessage("WIZ_SHOP_LOCALIZATION").'</label><br />';*/

			$this->content .= '<div class="wizard-input-form">';

			$this->content .= '<div class="wizard-input-form-block">
				<h4><label for="shopEmail">'.GetMessage("WIZ_SHOP_EMAIL").'</label></h4>
				<div class="wizard-input-form-block-content">
					<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopEmail', array("id" => "shopEmail")).'</div>
				</div>
			</div>';	
			$this->content .= '<div class="wizard-input-form-block">
				<h4><label for="shopOfName">'.GetMessage("WIZ_SHOP_OF_NAME").'</label></h4>
				<div class="wizard-input-form-block-content">
					<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopOfName', array("id" => "shopOfName")).'</div>
				</div>
			</div>';			
	
			$this->content .= '<div class="wizard-input-form-block">
				<h4><label for="shopLocation">'.GetMessage("WIZ_SHOP_LOCATION").'</label></h4>';
				
			$this->content .= '
				<div class="wizard-input-form-block-content">
					<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopLocation', array("id" => "shopLocation")).'</div>
				</div>';
			$this->content .= '</div>';			
	
			$this->content .= '
			<div class="wizard-input-form-block">
				<h4><label for="shopAdr">'.GetMessage("WIZ_SHOP_ADR").'</label></h4>
				<div class="wizard-input-form-block-content">
					<div class="wizard-input-form-field wizard-input-form-field-textarea">'.$this->ShowInputField('textarea', 'shopAdr', array("rows"=>"3", "id" => "shopAdr")).'</div>
				</div>
			</div>';


			$currentLocalization = $wizard->GetVar("shopLocalization");
			if (empty($currentLocalization))
				$currentLocalization = $wizard->GetDefaultVar("shopLocalization");
	 //ru
			$this->content .= '
			<div id="ru_bank_details" class="wizard-input-form-block" >
				<h4><label for="shopAdr">'.GetMessage("WIZ_SHOP_BANK_TITLE").'</label></h4>
				<table  class="data-table-no-border" >
					<tr>
						<th width="35%">'.GetMessage("WIZ_SHOP_INN").':</th>
						<td width="65%"><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopINN').'</div></td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_KPP").':</th>
						<td><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopKPP').'</div></td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_NS").':</th>
						<td><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopNS').'</div></td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_BANK").':</th>
						<td><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopBANK').'</div></td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_BANKREKV").':</th>
						<td><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopBANKREKV').'</div></td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_KS").':</th>
						<td><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopKS').'</div></td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_STAMP").':</th>
							<td><div class="">'.$this->ShowFileField("siteStamp", Array("show_file_info"=> "N", "id" => "siteStamp", "style" => "width: 90%; border: solid 1px #CECECE; background-color: #F5F5F5; padding: 3px;")).'<br />'.CFile::ShowImage($siteStamp, 75, 75, "border=0 vspace=5", false, false).'</div></td>
						</tr>
				</table>
			</div>
			';
	//ua
		/*	$this->content .= '
			<div id="ua_bank_details" class="wizard-input-form-block" style="display:'.(($currentLocalization == "ua") ? 'block':'none').'">
				<h4><label for="shopAdr">'.GetMessage("WIZ_SHOP_RECV_UA").'</label></h4>
				<p>'.GetMessage("WIZ_SHOP_RECV_UA_DESC").'</p>
				<table class="data-table-no-border" >
					<tr>
						<th width="35%">'.GetMessage("WIZ_SHOP_COMPANY_UA").':</th>
						<td width="65%"><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopCompany_ua').'</div></td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_EGRPU_UA").':</th>
						<td><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopEGRPU_ua').'</div></td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_INN_UA").':</th>
						<td><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopINN_ua').'</div></td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_NDS_UA").':</th>
						<td><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopNDS_ua').'</div></td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_NS_UA").':</th>
						<td><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopNS_ua').'</div></td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_BANK_UA").':</th>
						<td><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopBank_ua').'</div></td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_MFO_UA").':</th>
							<td><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopMFO_ua').'</div></td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_PLACE_UA").':</th>
						<td><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopPlace_ua').'</div></td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_FIO_UA").':</th>
						<td><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopFIO_ua').'</div></td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_TAX_UA").':</th>
						<td><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopTax_ua').'</div></td>
					</tr>
				</table>
			</div>
			';   */

			if (CModule::IncludeModule("catalog"))
			{
				$db_res = CCatalogGroup::GetGroupsList(array("CATALOG_GROUP_ID"=>'1', "BUY"=>"Y", "GROUP_ID"=>2));
				if (!$db_res->Fetch())
				{
					$this->content .= '
					<div class="wizard-input-form-block">
						<h4><label for="shopAdr">'.GetMessage("WIZ_SHOP_PRICE_BASE_TITLE").'</label></h4>
						<div class="wizard-input-form-block-content">
							'. GetMessage("WIZ_SHOP_PRICE_BASE_TEXT1") .'<br><br>
							'. $this->ShowCheckboxField("installPriceBASE", "Y",
							(array("id" => "install-demo-data")))
							. ' <label for="install-demo-data">'.GetMessage("WIZ_SHOP_PRICE_BASE_TEXT2").'</label><br />

						</div>
					</div>';
				}
			}
			
			$this->content .= '</div>';

			/*$this->content .= '
				<script>
					function langReload()
					{
			            var objSel = document.getElementById("localization_select");
			            var locSelected = objSel.options[objSel.selectedIndex].value;
			            document.getElementById("ru_bank_details").style.display = (locSelected == "ru") ? "block" : "none";
			            document.getElementById("ua_bank_details").style.display = (locSelected == "ua") ? "block" : "none";
			            document.getElementById("kz_bank_details").style.display = (locSelected == "kz") ? "block" : "none";
					}
				</script>
			';   */
		}
	}
	
	function OnPostForm()
	{
		$wizard =& $this->GetWizard();
		$res = $this->SaveFile("siteStamp", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 70, "max_width" => 190, "make_preview" => "Y"));
	}

}

class PersonType extends CWizardStep
{
	function InitStep()
	{
		$this->SetStepID("person_type");
		$this->SetTitle(GetMessage("WIZ_STEP_PT"));
		$this->SetNextStep("pay_system");
		$this->SetPrevStep("shop_settings");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));

		$wizard =& $this->GetWizard();
		$shopLocalization = $wizard->GetVar("shopLocalization", true);

		if ($shopLocalization == "ua")
			$wizard->SetDefaultVars(
				Array(
					"personType" => Array(
						"fiz" => "Y",
						"fiz_ua" => "Y",
						"ur" => "Y",
					)
				)
			);
		else
			$wizard->SetDefaultVars(
				Array(
					"personType" => Array(
						"fiz" => "Y",
						"ur" => "Y",
					)
				)
			);
	}

	function ShowStep()
	{

		$wizard =& $this->GetWizard();
		$shopLocalization = $wizard->GetVar("shopLocalization", true);

		$this->content .= '<div class="wizard-input-form">';
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4>'.GetMessage("WIZ_PERSON_TYPE_TITLE").'</h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-checkbox">
					'.$this->ShowCheckboxField('personType[fiz]', 'Y', (array("id" => "personTypeF"))).' <label for="personTypeF">'.GetMessage("WIZ_PERSON_TYPE_FIZ").'</label><br />
					'.$this->ShowCheckboxField('personType[ur]', 'Y', (array("id" => "personTypeU"))).' <label for="personTypeU">'.GetMessage("WIZ_PERSON_TYPE_UR").'</label><br />';
			//	if ($shopLocalization == "ua")
			//		$this->content .= $this->ShowCheckboxField('personType[fiz_ua]', 'Y', (array("id" => "personTypeFua"))).' <label for="personTypeFua">'.GetMessage("WIZ_PERSON_TYPE_FIZ_UA").'</label><br />';
				$this->content .= '
				</div>
			</div>
			'.GetMessage("WIZ_PERSON_TYPE").'
		</div>';
		$this->content .= '</div>';
	}
	
	function OnPostForm()
	{
		$wizard = &$this->GetWizard();
		$personType = $wizard->GetVar("personType");

		if (empty($personType["fiz"]) && empty($personType["ur"]))
			$this->SetError(GetMessage('WIZ_NO_PT'));
	}

}

class PaySystem extends CWizardStep
{
	function InitStep()
	{
		$this->SetStepID("pay_system");
		$this->SetTitle(GetMessage("WIZ_STEP_PS"));
		$this->SetNextStep("data_install");
		if(LANGUAGE_ID != "ru")
			$this->SetPrevStep("site_settings");
		else
		$this->SetPrevStep("person_type");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));

		$wizard =& $this->GetWizard();
/*payer type
		if(LANGUAGE_ID == "ru")
		{
			$shopLocalization = $wizard->GetVar("shopLocalization", true);
			if ($shopLocalization == "ua")
				$wizard->SetDefaultVars(
					Array(
						"personType" => Array(
							"fiz" => "Y",
							"fiz_ua" => "Y",
							"ur" => "Y",
						)
					)
				);
			else
				$wizard->SetDefaultVars(
					Array(
						"personType" => Array(
							"fiz" => "Y",
							"ur" => "Y",
						)
					)
				);
		}
=====*/
		if(LANGUAGE_ID == "ru")
		{
			/*$shopLocalization = $wizard->GetVar("shopLocalization", true);

			if ($shopLocalization == "ua")
				$wizard->SetDefaultVars(
					Array(
						"paysystem" => Array(
							"cash" => "Y",
							"oshad" => "Y",
							"bill" => "Y",
						),
						"delivery" => Array(
							"courier" => "Y",
							"self" => "Y",
						)
					)
				);
			else  */
				$wizard->SetDefaultVars(
					Array(
						"paysystem" => Array(
							"cash" => "Y",
							"sber" => "Y",
							"bill" => "Y",
						),
						"delivery" => Array(
							"courier" => "Y",
							"self" => "Y",
							"russianpost" => "N",
						)
					)
				);
		}
		else
		{
			$wizard->SetDefaultVars(
				Array(
					"paysystem" => Array(
						"cash" => "Y",	
						"paypal" => "Y",
					),			
					"delivery" => Array(
						"courier" => "Y",	
						"self" => "Y",
						"dhl" => "Y",
						"ups" => "Y",
					)
				)
			);
		}
	}
	
	function OnPostForm()
	{
		$wizard = &$this->GetWizard();
		$paysystem = $wizard->GetVar("paysystem");

		if (empty($paysystem["cash"]) && empty($paysystem["sber"]) && empty($paysystem["bill"]) && empty($paysystem["paypal"]))
			$this->SetError(GetMessage('WIZ_NO_PS'));
/*payer type
		if(LANGUAGE_ID == "ru")
		{
			$personType = $wizard->GetVar("personType");

			if (empty($personType["fiz"]) && empty($personType["ur"]))
				$this->SetError(GetMessage('WIZ_NO_PT'));
		}
===*/
	}

	function ShowStep()
	{

		$wizard =& $this->GetWizard();
		$shopLocalization = $wizard->GetVar("shopLocalization", true);
/*payer type
		if(LANGUAGE_ID == "ru")
		{
			$this->content .= '<div class="wizard-input-form">';
			$this->content .= '
			<div class="wizard-input-form-block">
				<h4>'.GetMessage("WIZ_PERSON_TYPE_TITLE").'</h4>
				<div class="wizard-input-form-block-content">
					<div class="wizard-input-form-field wizard-input-form-field-checkbox">
						'.$this->ShowCheckboxField('personType[fiz]', 'Y', (array("id" => "personTypeF"))).' <label for="personTypeF">'.GetMessage("WIZ_PERSON_TYPE_FIZ").'</label><br />
						'.$this->ShowCheckboxField('personType[ur]', 'Y', (array("id" => "personTypeU"))).' <label for="personTypeU">'.GetMessage("WIZ_PERSON_TYPE_UR").'</label><br />';
			if ($shopLocalization == "ua")
				$this->content .= $this->ShowCheckboxField('personType[fiz_ua]', 'Y', (array("id" => "personTypeFua"))).' <label for="personTypeFua">'.GetMessage("WIZ_PERSON_TYPE_FIZ_UA").'</label><br />';
			$this->content .= '
					</div>
				</div>
				'.GetMessage("WIZ_PERSON_TYPE").'
			</div>';
			$this->content .= '</div>';
		}
===*/
		$personType = $wizard->GetVar("personType");
		
		$this->content .= '<div class="wizard-input-form">';
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4>'.GetMessage("WIZ_PAY_SYSTEM_TITLE").'</h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-checkbox">
					'.$this->ShowCheckboxField('paysystem[cash]', 'Y', (array("id" => "paysystemC"))).' <label for="paysystemC">'.GetMessage("WIZ_PAY_SYSTEM_C").'</label><br />';
				if(LANGUAGE_ID == "ru")
				{
				/*	if($shopLocalization == "ua" && ($personType["fiz"] == "Y" || $personType["fiz_ua"] == "Y"))
						$this->content .= $this->ShowCheckboxField('paysystem[oshad]', 'Y', (array("id" => "paysystemO"))).' <label for="paysystemS">'.GetMessage("WIZ_PAY_SYSTEM_O").'</label><br />';
					else*/if ($personType["fiz"] == "Y")
						$this->content .= $this->ShowCheckboxField('paysystem[sber]', 'Y', (array("id" => "paysystemS"))).' <label for="paysystemS">'.GetMessage("WIZ_PAY_SYSTEM_S").'</label><br />';
					if($personType["ur"] == "Y")
						$this->content .= $this->ShowCheckboxField('paysystem[bill]', 'Y', (array("id" => "paysystemB"))).' <label for="paysystemB">'.GetMessage("WIZ_PAY_SYSTEM_B").'</label><br />';
				}
				else
				{
					$this->content .= $this->ShowCheckboxField('paysystem[paypal]', 'Y', (array("id" => "paysystemP"))).' <label for="paysystemP">PayPal</label><br />';
				}
				$this->content .= '</div>
			</div>
			'.GetMessage("WIZ_PAY_SYSTEM").'
		</div>';
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4>'.GetMessage("WIZ_DELIVERY_TITLE").'</h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-checkbox">
					'.$this->ShowCheckboxField('delivery[courier]', 'Y', (array("id" => "deliveryC"))).' <label for="deliveryC">'.GetMessage("WIZ_DELIVERY_C").'</label><br />
					'.$this->ShowCheckboxField('delivery[self]', 'Y', (array("id" => "deliveryS"))).' <label for="deliveryS">'.GetMessage("WIZ_DELIVERY_S").'</label><br />';
					if(LANGUAGE_ID == "ru")
					{
						//if ($shopLocalization != "ua")
							$this->content .= $this->ShowCheckboxField('delivery[russianpost]', 'Y', (array("id" => "deliveryR"))).' <label for="deliveryR">'.GetMessage("WIZ_DELIVERY_R").'</label><br />';
					}
					else
					{
						$this->content .= $this->ShowCheckboxField('delivery[dhl]', 'Y', (array("id" => "deliveryD"))).' <label for="deliveryD">DHL</label><br />';
						$this->content .= $this->ShowCheckboxField('delivery[ups]', 'Y', (array("id" => "deliveryU"))).' <label for="deliveryU">UPS</label><br />';
					}
					$this->content .= '
				</div>
			</div>
			'.GetMessage("WIZ_DELIVERY").'
		</div>';

		$this->content .= '
		<div class="wizard-input-form-block">
			<h4>'.GetMessage("WIZ_LOCATION_TITLE").'</h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-checkbox">';
		if(LANGUAGE_ID == "ru")
		{
			$this->content .= $this->ShowRadioField("locations_csv", "loc_ussr.csv", array("id" => "loc_ussr", "checked" => "checked"))
				." <label for=\"loc_ussr\">".GetMessage('WSL_STEP2_GFILE_USSR')."</label><br />";
		}
		$this->content .= $this->ShowRadioField("locations_csv", "loc_usa.csv", array("id" => "loc_usa"))
			." <label for=\"loc_usa\">".GetMessage('WSL_STEP2_GFILE_USA')."</label><br />";
		$this->content .= $this->ShowRadioField("locations_csv", "loc_cntr.csv", array("id" => "loc_cntr"))
			." <label for=\"loc_cntr\">".GetMessage('WSL_STEP2_GFILE_CNTR')."</label><br />";
		$this->content .= $this->ShowRadioField("locations_csv", "", array("id" => "none"))
			." <label for=\"none\">".GetMessage('WSL_STEP2_GFILE_NONE')."</label>";
					$this->content .= '
				</div>
			</div>
		</div>';

		$this->content .= '<div class="wizard-input-form-block">'.GetMessage("WIZ_DELIVERY_HINT").'</div>';

		$this->content .= '</div>';
	}
}
class DataInstallStep extends CDataInstallWizardStep
{
	function CorrectServices(&$arServices)
	{
		if($_SESSION["BX_ESHOP_LOCATION"] == "Y")
			$this->repeatCurrentService = true;
		else
			$this->repeatCurrentService = false;

		$wizard =& $this->GetWizard();
		if($wizard->GetVar("installDemoData") != "Y")
		{
		}
	}
}

class FinishStep extends CFinishWizardStep
{
	function InitStep()
	{
		$this->SetStepID("finish");
		$this->SetNextStep("finish");
		$this->SetTitle(GetMessage("FINISH_STEP_TITLE"));
		$this->SetNextCaption(GetMessage("wiz_go"));  
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();
		   
		if ($wizard->GetVar("proactive") == "Y")
			COption::SetOptionString("statistic", "DEFENCE_ON", "Y");
		
		$siteID = WizardServices::GetCurrentSiteID($wizard->GetVar("siteID"));
		$rsSites = CSite::GetByID($siteID);
		$siteDir = "/"; 
		if ($arSite = $rsSites->Fetch())
			$siteDir = $arSite["DIR"]; 

		$wizard->SetFormActionScript(str_replace("//", "/", $siteDir."/?finish"));

		$this->CreateNewIndex();
		
		COption::SetOptionString("main", "wizard_solution", $wizard->solutionName, false, $siteID); 
		
		$this->content .= GetMessage("FINISH_STEP_CONTENT");
		$this->content .= "<br clear=\"all\"><a href=\"/bitrix/admin/wizard_install.php?lang=".LANGUAGE_ID."&site_id=".$siteID."&wizardName=bitrix:eshop.mobile&".bitrix_sessid_get()."\" class=\"button-next\"><span id=\"next-button-caption\">".GetMessage("wizard_store_mobile")."</span></a>";
		
		if ($wizard->GetVar("installDemoData") == "Y")
			$this->content .= GetMessage("FINISH_STEP_REINDEX");		
			
		
	}

}
?>