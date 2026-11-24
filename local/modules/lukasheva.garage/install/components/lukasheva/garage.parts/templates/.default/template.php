<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
use	Bitrix\Main\Localization\Loc;
$this->setFrameMode(true);
\Bitrix\Main\UI\Extension::load('ui.dialogs.messagebox');
$this->addExternalCss($templateFolder . '/style.css');
?>
<h2><?=Loc::getMessage('GARAGE_PARTS_TITLE',[
	'#ID#' => htmlspecialcharsbx($arResult['DEAL_ID'])
]);?></h2>

<?php

$dealId = (int)$arResult['DEAL_ID'];
// здесь мы модключаем штатный компонет грид и передаем ему данные
$APPLICATION->includeComponent(
	"bitrix:main.ui.grid",
	"",
	[
		"GRID_ID" => "STOCK_GRID",
		"COLUMNS" => $arResult['COLUMNS'],
		"ROWS" => $arResult['LISTS'],
		"AJAX_MODE" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_HISTORY" => "N",
		"SHOW_ROW_CHECKBOXES" => true,
		"SHOW_SELECTED_COUNTER" => true,
		"SHOW_PAGESIZE" => false,
		"SHOW_ROW_ACTIONS_MENU" =>true,
		"TOTAL_ROWS_COUNT" =>$arResult['COUNT'],

	]
);
?>
<div class="action-buttons">
    <button type="button" class="ui-btn ui-btn-primary" 
		onclick="BX.Lukasheva.GarageParts.send(<?=$dealId; ?>, 'STOCK_GRID');"><?=  htmlspecialcharsbx(Loc::getMessage('GARAGE_PARTS_ACTION_BP'))?>
	</button>
</div>

<script>
   BX.message({
		GARAGE_PARTS_SEND_ERROR: '<?= CUtil::JSEscape(Loc::getMessage('GARAGE_PARTS_SEND_ERROR')) ?>',
		GARAGE_PARTS_GRID_ERROR: '<?= CUtil::JSEscape(Loc::getMessage('GARAGE_PARTS_GRID_ERROR')) ?>',
		GARAGE_PARTS_SEND_SUCCESS: '<?= CUtil::JSEscape(Loc::getMessage('GARAGE_PARTS_SEND_SUCCESS')) ?>',
		
		
   });
</script>
