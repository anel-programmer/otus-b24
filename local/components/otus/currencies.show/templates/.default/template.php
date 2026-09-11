<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
$this->setFrameMode(true);


 /* echo 'TEMPLATE';
 pr($arParams); 
 pr($arResult); 
 pr($templateFolder); 
 pr($componentPath);  */

?>
<h1>Валюта: <?=$arResult['CURENT_CURRENCY']?></h1>
<h1>Курс: <?=$arResult['CURENT_CURRENCY_AMOUNT']?></h1>


