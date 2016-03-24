<?
define("ADMIN_MODULE_NAME", "security");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/security/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/security/prolog.php");
IncludeModuleLangFile(__FILE__);

/**
 * @global CMain $APPLICATION
 * @global CUser $USER
 */
if(!$USER->IsAdmin())
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

if($_SERVER['REQUEST_METHOD'] == "POST" && check_bitrix_sessid() && $_POST["action"].$_POST["save"].$_POST["apply"] != "")
{
	$result = "error";

	if(isset($_POST["action"]) && $_POST["action"] == "save")
	{
		if(isset($_POST["results"]) && is_array($_POST["results"]))
		{
			CUtil::JSPostUnescape();
			$resultsForSave = $_POST["results"];
		}
		else
		{
			$resultsForSave = array();
		}
		if(CSecuritySiteChecker::addResults($resultsForSave))
		{
			$result = "ok";
		}
	}
	elseif(isset($_POST["action"]) && $_POST["action"] == "check")
	{
		$isFirstStart = isset($_POST["first_start"]) && $_POST["first_start"] == "Y";
		$isCheckRequirementsNeeded = !isset($_REQUEST["check_requirements"]) || $_REQUEST["check_requirements"] != "N";
		$neededTestPackages = "";
		$result = CSecuritySiteChecker::runTestPackage($neededTestPackages, $isFirstStart, $isCheckRequirementsNeeded);
	}
	else
	{
			$result = "Action not found!";
	}

$APPLICATION->RestartBuffer();
header('Content-Type: application/x-javascript; charset='.LANG_CHARSET);
die(CUtil::PhpToJsObject($result));
}

$APPLICATION->AddHeadScript('/bitrix/js/security/admin/scanner.js');
CSecuritySiteChecker::clearTemporaryData();

$aTabs = array(
	array(
		"DIV" => "main",
		"TAB" => GetMessage("SEC_SCANNER_MAIN_TAB"),
		"TITLE"=>GetMessage("SEC_SCANNER_TITLE"),
	),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs, false, true);

$lastTestingInfo = CSecuritySiteChecker::getLastTestingInfo();
if(isset($lastTestingInfo["results"]))
{
	$lastResults = $lastTestingInfo["results"];
}
else
{
	$lastResults = array();
}

if(!empty($lastResults))
{
	$criticalResultsCount = CSecuritySiteChecker::calculateCriticalResults($lastResults);
}
else
{
	$criticalResultsCount = 0;
}

if(isset($lastTestingInfo["test_date"]))
{
	$lastDate = $lastTestingInfo["test_date"];
}
else
{
	$lastDate = "";
}

$APPLICATION->SetTitle(GetMessage("SEC_SCANNER_TITLE"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
<div id="error_container" class="adm-security-error-container" style="display:none;">
	<?
	CAdminMessage::ShowMessage(array(
		"MESSAGE" => GetMessage("SEC_SCANNER_CRITICAL_ERRORS_TITLE"),
		"TYPE" => "ERROR",
		"DETAILS" => "",
		"HTML"=>true
	));
	?>
</div>
<form method="POST" action="security_scanner.php?lang=<?=LANG?><?=$_GET["return_url"]? "&amp;return_url=".urlencode($_GET["return_url"]): ""?>" name="settings_form">
<?$tabControl->Begin();?>
<?$tabControl->BeginNextTab();?>
<div class="adm-security-wrap">
	<div id="start_container" class="adm-security-first-step">
		<div id="first_start" class="adm-security-text-block" <?=(!CSecuritySiteChecker::isNewTestNeeded())? "style=\"display:none;\"" : ""?>>
		<?=GetMessage("SEC_SCANNER_CRITICAL_FIRST_START")?>
		</div>
		<span id="start_button" class="adm-btn adm-btn-green" onclick="securityScanner.startStopChecking()"><?=GetMessage("SEC_SCANNER_START_BUTTON")?></span>
	</div>
	<div id="status_bar" class="adm-security-status-bar" style="display:none;">
		<div id="progress_bar" style="width: 500px;" class="adm-progress-bar-outer">
			<div id="progress_bar_inner" style="width: 0px;" class="adm-progress-bar-inner"></div>
			<div id="progress_text" style="width: 500px;" class="adm-progress-bar-inner-text">0%</div>
		</div>
		<div id="current_test"></div>
		<span id="stop_button" class="adm-btn stop-button" onclick="securityScanner.startStopChecking()"><?=GetMessage("SEC_SCANNER_STOP_BUTTON")?></span>
	</div>
	<div id="results_info" class="adm-security-results-info adm-security-title" <?=(empty($lastResults) && empty($lastDate))? "style=\"display:none;\"" : ""?>>
		<div id="problems_count" style="width: 500px; float: left;"><?=(!empty($lastResults))? (GetMessage("SEC_SCANNER_PROBLEMS_COUNT").count($lastResults).GetMessage("SEC_SCANNER_CRITICAL_PROBLEMS_COUNT").$criticalResultsCount): ""?></div>
		<div id="last_activity" style="width: 100%; text-align: right;"><?=($lastDate != "")? GetMessage("SEC_SCANNER_TEST_DATE", array("#DATE#" => $lastDate)): ""?></div>
		<div style="clear:both;"></div>
	</div>
	<div id="results" class="adm-security-third-step" <?=(empty($lastResults))? "style=\"display:none;\"" : ""?>></div>
</div>
<?$tabControl->End();?>
</form>

<script>
	BX.message(<?=CUtil::PhpToJSObject(IncludeModuleLangFile(__FILE__, false, true))?>);
	var results = <?=Cutil::PhpToJsObject($lastResults)?>;
	var securityScanner = new JCSecurityScanner(results);
</script>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>