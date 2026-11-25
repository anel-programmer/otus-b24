<?php

/**
 * file path: local/components/aclips/base.grid/lazyload.ajax.php
 */
  
use Bitrix\Main\Application;

define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC', 'Y');
define('NO_AGENT_CHECK', true);
define('PUBLIC_AJAX_MODE', true);
define('DisableEventsCheck', true);

$siteID = isset($_REQUEST['site']) ? mb_substr(preg_replace('/[^a-z0-9_]/i', '', $_REQUEST['site']), 0, 2) : '';

if ($siteID !== '') {
    define('SITE_ID', $siteID);
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * Проверка сессии
 */
if (!check_bitrix_sessid()) {
    die();
}

Header('Content-Type: text/html; charset=' . LANG_CHARSET);

global $APPLICATION;
$APPLICATION->ShowAjaxHead();

$request = Application::getInstance()->getContext()->getRequest();

$componentData = $request->get('PARAMS');

if (is_array($componentData) && is_array($componentData['params'] ?? null)) {
    $template = (string)($componentData['template'] ?? '');
    $componentParams = $componentData['params'];
} else {
    $template = 'list';
    $componentParams = [
        'NUM_PAGE' => (int)$request->get('num_page'),
        'SHOW_CHECKBOXES' => $request->get('show_checkboxes') === 'Y' ? 'Y' : 'N',
        'CONTACT_ID' => (int)$request->get('contact_id'),
    ];
}

$server = $request->getServer();

$ajaxLoaderParams = array(
    'url' => $server->get('REQUEST_URI'),
    'method' => 'POST',
    'dataType' => 'ajax',
    'data' => array('PARAMS' => $componentData)
);

$componentParams['AJAX_LOADER'] = $ajaxLoaderParams;

$APPLICATION->IncludeComponent(
    'bitrix:ui.sidepanel.wrapper',
    '',
    [
        'PLAIN_VIEW' => false,
        'USE_PADDING' => true,
        'POPUP_COMPONENT_NAME' => 'lukasheva:cars.table',
        'POPUP_COMPONENT_TEMPLATE_NAME' => $template,
        'POPUP_COMPONENT_PARAMS' => $componentParams
    ]
);

\CMain::FinalActions();