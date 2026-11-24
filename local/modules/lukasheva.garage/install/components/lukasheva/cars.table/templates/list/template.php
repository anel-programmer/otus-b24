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
$this->addExternalCss($templateFolder . '/style.css');
?>
<div class="action-buttons">
    <button type="button" class="ui-btn ui-btn-primary" 
		onclick="BX.Lukasheva.Garage.add(<?= $arParams['CONTACT_ID'] ?>);"><?=  htmlspecialcharsbx(Loc::getMessage('CARS_TABLE_ACTION_ADD'))?>
	</button>
</div>

<?php
// здесь мы модключаем штатный компонет грид и передаем ему данные
$APPLICATION->includeComponent(
	"bitrix:main.ui.grid",
	"",
	[
		"GRID_ID" => "CARS_GRID_ID",
		"COLUMNS" => $arResult['COLUMNS'],
		"ROWS" => $arResult['LISTS'],
		'NAV_OBJECT' => $arResult['NAV'],
		"AJAX_MODE" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_HISTORY" => "N",
		"SHOW_ROW_CHECKBOXES" =>$arResult['SHOW_ROW_CHECKBOXES'],
		"SHOW_SELECTED_COUNTER" => true,
		"SHOW_PAGESIZE" => false,
		"SHOW_ROW_ACTIONS_MENU" =>true,
		"SHOW_ACTION_PANEL" => true,
		"SHOW_GROUP_EDIT_BUTTON" => true,
		"TOTAL_ROWS_COUNT" =>$arResult['COUNT'],

	]
);
?>
<script>
   BX.message({
		CARS_TABLE_TITLE_ADD: '<?= CUtil::JSEscape(Loc::getMessage('CARS_TABLE_TITLE_ADD')) ?>',
		CARS_TABLE_TITLE_EDIT: '<?= CUtil::JSEscape(Loc::getMessage('CARS_TABLE_TITLE_EDIT')) ?>',
		CARS_TABLE_ACTION_ADD: '<?= CUtil::JSEscape(Loc::getMessage('CARS_TABLE_ACTION_ADD')) ?>',
		CARS_TABLE_ACTION_CLOSE: '<?= CUtil::JSEscape(Loc::getMessage('CARS_TABLE_ACTION_CLOSE')) ?>',
		CARS_TABLE_ACTION_SAVE: '<?= CUtil::JSEscape(Loc::getMessage('CARS_TABLE_ACTION_SAVE')) ?>',
		CARS_TABLE_ACTION_CANCEL: '<?= CUtil::JSEscape(Loc::getMessage('CARS_TABLE_ACTION_CANCEL')) ?>',
		CARS_TABLE_LABEL_MODEL: '<?= CUtil::JSEscape(Loc::getMessage('CARS_TABLE_LABEL_MODEL')) ?>',
		CARS_TABLE_LABEL_CARMAKE: '<?= CUtil::JSEscape(Loc::getMessage('CARS_TABLE_LABEL_CARMAKE')) ?>',
		CARS_TABLE_LABEL_PLATE: '<?= CUtil::JSEscape(Loc::getMessage('CARS_TABLE_LABEL_PLATE')) ?>',
	   	CARS_TABLE_LABEL_YEAR: '<?= CUtil::JSEscape(Loc::getMessage('CARS_TABLE_LABEL_YEAR')) ?>',
		CARS_TABLE_LABEL_COLOR: '<?= CUtil::JSEscape(Loc::getMessage('CARS_TABLE_LABEL_COLOR')) ?>',
		CARS_TABLE_LABEL_MILEAGE: '<?= CUtil::JSEscape(Loc::getMessage('CARS_TABLE_LABEL_MILEAGE')) ?>',
		CARS_TABLE_SELECT_PLACEHOLDER: '<?= CUtil::JSEscape(Loc::getMessage('CARS_TABLE_SELECT_PLACEHOLDER')) ?>',
   });
   BX.Lukasheva.Garage.gridUrl = '<?= CUtil::JSEscape($arParams['AJAX_LOADER']['url'] ?? '') ?>';
</script>
