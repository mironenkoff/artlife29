<?
IncludeModuleLangFile(__FILE__);
class CBitrixCloudCDN
{
	/**
	 *
	 * @var CBitrixCloudCDNConfig $config
	 *
	 */
	private static $config = /*.(CBitrixCloudCDNConfig).*/ null;
	private static $proto = "";
	private static $ajax = false;
	private static $domain_changed = false;
	/**
	 *
	 * @param string &$content
	 * @return void
	 *
	 */
	public function OnEndBufferContent(&$content)
	{
		if (isset($_GET["nocdn"]))
			return;

		self::$proto = CMain::IsHTTPS() ? "https" : "http";
		self::$config = CBitrixCloudCDNConfig::getInstance()->loadFromOptions();
		if (self::$config->isExpired())
		{
			if (self::$config->lock())
			{
				$delayExpiration = true;
				try
				{
					try
					{
						self::$config = CBitrixCloudCDNConfig::getInstance()->loadRemoteXML();
						self::$config->saveToOptions();
						self::$config->unlock();
					}
					catch(CBitrixCloudException $e)
					{
						//In case of documented XML error we'll disable CDN
						if($e->getErrorCode() !== "")
						{
							self::SetActive(false);
							$delayExpiration = false;
						}
						throw $e;
					}
				}
				catch(exception $e)
				{
					if($delayExpiration)
						self::$config->setExpired(time() + 1800);
					CAdminNotify::Add(array(
						"MESSAGE" => GetMessage("BCL_CDN_NOTIFY", array(
							"#HREF#" => "/bitrix/admin/bitrixcloud_cdn.php?lang=".LANGUAGE_ID,
						)),
						"TAG" => "bitrixcloud_off",
						"MODULE_ID" => "bitrixcloud",
						"ENABLE_CLOSE" => "Y",
					));
					self::$config->unlock();
					return;
				}
			}
		}

		if(!self::$config->isActive())
			return;

		$sites = self::$config->getSites();
		if (defined("ADMIN_SECTION"))
		{
			if (!isset($sites["admin"]))
				return;
		}
		elseif (defined("SITE_ID"))
		{
			if (!isset($sites[SITE_ID]))
				return;
		}
		else
		{
			return;
		}

		self::$ajax = preg_match("/<head>/i", substr($content, 0, 512)) === 0;

		$arPrefixes = array_map(array(
			"CBitrixCloudCDN",
			"_preg_quote",
		), self::$config->getLocationsPrefixes(self::$config->isKernelRewriteEnabled(), self::$config->isContentRewriteEnabled()));

		$arExtensions = array_map(array(
			"CBitrixCloudCDN",
			"_preg_quote",
		), self::$config->getLocationsExtensions());

		if (!empty($arPrefixes) && !empty($arExtensions))
		{
			$prefix_regex = "(?:".implode("|", $arPrefixes).")";
			$extension_regex = "(?:".implode("|", $arExtensions).")";
			$regex = "/
				((?i:
					href=
					|src=
					|BX\\.loadCSS\\(
					|BX\\.loadScript\\(
					|jsUtils\\.loadJSFile\\(
					|background\\s*:\\s*url\\(
				))                                                   #attribute
				(\"|')                                               #open_quote
				(".$prefix_regex.")                                  #prefix
				([^?'\"]+\\.)                                        #href body
				(".$extension_regex.")                               #extension
				(|\\?\\d+|\\?v=\\d+)                                 #params
				(\\2)                                                #close_quote
			/x";
			$content = preg_replace_callback($regex, array(
				"CBitrixCloudCDN",
				"_filter",
			), $content);
		}
	}

	/**
	 *
	 * @return void
	 *
	 */
	public static function domainChanged()
	{
		self::$domain_changed = true;
	}
	/**
	 *
	 * @param string $str
	 * @return string
	 *
	 */
	private function _preg_quote($str)
	{
		return preg_quote($str, "/");
	}
	/**
	 *
	 * @param array[int]string $match
	 * @return string
	 *
	 */
	private function _filter($match)
	{
		$attribute = $match[1];
		$open_quote = $match[2];
		$prefix = $match[3];
		$link = $match[4];
		$extension = $match[5];
		$params = $match[6];
		$close_quote = $match[7];
		$location = /*.(CBitrixCloudCDNLocation).*/ null;

		if(self::$ajax && $extension === "js")
			return $match[0];

		//if(preg_match("/^background/i", $attribute))
		//	$proto = self::$proto."://";
		//else
			$proto = "//";

		foreach (self::$config->getLocations() as $location)
		{
			/** @var CBitrixCloudCDNLocation $location */
			if ($location->getProto() === self::$proto)
			{
				$server = $location->getServerNameByPrefixAndExtension($prefix, $extension, $link);
				if ($server !== "")
				{
					if ($params === '')
					{
						if (file_exists($_SERVER["DOCUMENT_ROOT"].$prefix.$link.$extension))
							$params = "?".filemtime($_SERVER["DOCUMENT_ROOT"].$prefix.$link.$extension).$params;
					}
					//Fix spaces in the link
					$link = str_replace(" ", "%20", $link);
					return $attribute.$open_quote.$proto.$server.$prefix.$link.$extension.$params.$close_quote;
				}
			}
		}
		return $match[0];
	}
	/**
	 *
	 * @return void
	 *
	 */
	private static function stop() /*. throws CBitrixCloudException .*/
	{
		$o = CBitrixCloudCDNConfig::getInstance()->loadFromOptions();
		$a = new CBitrixCloudCDNWebService($o->getDomain());
		$a->actionStop();
	}
	/**
	 *
	 * @return bool
	 *
	 */
	public static function IsActive()
	{
		$bActive = false;
		foreach (GetModuleEvents("main", "OnEndBufferContent", true) as $arEvent)
		{
			if ($arEvent["TO_MODULE_ID"] === "bitrixcloud" && $arEvent["TO_CLASS"] === "CBitrixCloudCDN")
			{
				$bActive = true;
				break;
			}
		}
		return $bActive;
	}
	/**
	 *
	 * @param bool $bActive
	 * @return bool
	 *
	 */
	public static function SetActive($bActive)
	{
		/** @global CMain $APPLICATION */
		global $APPLICATION;
		if ($bActive)
		{
			if (!self::IsActive())
			{
				try
				{
					$o = CBitrixCloudCDNConfig::getInstance()->loadRemoteXML();
					$o->saveToOptions();
					RegisterModuleDependences("main", "OnEndBufferContent", "bitrixcloud", "CBitrixCloudCDN", "OnEndBufferContent");
					self::$domain_changed = false;
				}
				catch(CBitrixCloudException $e)
				{
					$ex = new CApplicationException($e->getMessage()."\n".$e->getDebugInfo());
					$APPLICATION->ThrowException($ex);
					return false;
				}
			}
			elseif (self::$domain_changed)
			{
				try
				{
					$o = CBitrixCloudCDNConfig::getInstance()->loadRemoteXML();
					$o->saveToOptions();
					self::$domain_changed = false;
				}
				catch(CBitrixCloudException $e)
				{
					$ex = new CApplicationException($e->getMessage()."\n".$e->getDebugInfo());
					$APPLICATION->ThrowException($ex);
					return false;
				}
			}
		}
		else
		{
			if (self::IsActive())
			{
				try
				{
					self::stop();
					UnRegisterModuleDependences("main", "OnEndBufferContent", "bitrixcloud", "CBitrixCloudCDN", "OnEndBufferContent");
				}
				catch(CBitrixCloudException $e)
				{
					UnRegisterModuleDependences("main", "OnEndBufferContent", "bitrixcloud", "CBitrixCloudCDN", "OnEndBufferContent");
					$ex = new CApplicationException($e->getMessage()."\n".$e->getDebugInfo());
					$APPLICATION->ThrowException($ex);
					return false;
				}
			}
		}
		return true;
	}
	/**
	 * Shows information about CDN free space in Admin's informer popup
	 * @return void
	 */
	public function OnAdminInformerInsertItems()
	{
		if (IsModuleInstalled('intranet'))
			return;

		$CDNAIParams = array(
			"TITLE" => GetMessage("BCL_CDN_AI_TITLE"),
			"COLOR" => "green",
		);

		if (CBitrixCloudCDN::IsActive())
		{
			$CDNAIParams["FOOTER"] = '<a href="/bitrix/admin/bitrixcloud_cdn.php?lang='.LANGUAGE_ID.'">'.GetMessage("BCL_CDN_AI_SETT").'</a>';

			$cdn_config = CBitrixCloudCDNConfig::getInstance()->loadFromOptions();
			$cdn_quota = $cdn_config->getQuota();
			$PROGRESS_TOTAL = $cdn_quota->getAllowedSize();
			$PROGRESS_VALUE = $cdn_quota->getTrafficSize();

			if ($PROGRESS_TOTAL > 0.0 || $PROGRESS_VALUE > 0.0)
			{
				$PROGRESS_AVAILABLE = $PROGRESS_TOTAL-$PROGRESS_VALUE;
				if($PROGRESS_AVAILABLE < 0.0)
					$PROGRESS_AVAILABLE = 0.0;

				$PROGRESS_FREE = 0.0;
				if($PROGRESS_TOTAL > 0.0)
					$PROGRESS_FREE = round(($PROGRESS_TOTAL-$PROGRESS_VALUE)/$PROGRESS_TOTAL*100);

				$PROGRESS_FREE_BAR = $PROGRESS_FREE > 100.0? 100: intval($PROGRESS_FREE);
				$PROGRESS_FREE_BAR = $PROGRESS_FREE < 0.0? 0: intval($PROGRESS_FREE_BAR);

				$CDNAIParams["ALERT"] = false;
				if ($PROGRESS_FREE < 10.0)
					$CDNAIParams["ALERT"] = true;
				elseif (!$cdn_config->isActive())
					$CDNAIParams["ALERT"] = true;

				$CDNAIParams["HTML"] = '
					<div class="adm-informer-item-section">
						<span class="adm-informer-item-l">
							<span class="adm-informer-strong-text">'.GetMessage("BCL_CDN_AI_USAGE_TOTAL").'</span> '.CFile::FormatSize($PROGRESS_TOTAL, 0).'
						</span>
						<span class="adm-informer-item-r">
								<span class="adm-informer-strong-text">'.GetMessage("BCL_CDN_AI_USAGE_AVAIL").'</span> '.CFile::FormatSize($PROGRESS_AVAILABLE, 0).'
						</span>
					</div>
					<div class="adm-informer-status-bar-block" >
						<div class="adm-informer-status-bar-indicator" style="width:'.(100-$PROGRESS_FREE_BAR).'%; "></div>
						<div class="adm-informer-status-bar-text">'.(100-$PROGRESS_FREE).'%</div>
					</div>
				';
			}
		}
		else
		{
			$CDNAIParams["HTML"] = '
				<div class="adm-informer-item-section">
					<span class="adm-informer-strong-text">'.GetMessage("BCL_CDN_AI_IS_OFF").'</span>
				</div>
				<div class="adm-informer-status-bar-block" >
					<div class="adm-informer-status-bar-indicator" style="width:0%; "></div>
					<div class="adm-informer-status-bar-text">0%</div>
				</div>
			';
			$CDNAIParams["ALERT"] = true;
			$CDNAIParams["FOOTER"] = '<a href="/bitrix/admin/bitrixcloud_cdn.php?lang='.LANGUAGE_ID.'">'.GetMessage("BCL_CDN_AI_TURN_ON").'</a>';
		}

		CAdminInformer::AddItem($CDNAIParams);
	}
}
?>