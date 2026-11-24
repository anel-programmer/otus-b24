<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Text\HtmlFilter;
use Bitrix\Main\UserGroupTable;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

if (!CurrentUser::get()->isAdmin()) {
    return;
}

Loc::loadMessages(__FILE__);

$moduleId = 'lukasheva.garage';
$request = Context::getCurrent()->getRequest();

if (!Loader::includeModule('crm') || !Loader::includeModule($moduleId)) {
    CAdminMessage::ShowMessage(Loc::getMessage('LUKASHEVA_GARAGE_OPTIONS_CRM_ERROR'));
    return;
}

$category = array_column(\Bitrix\Crm\Category\DealCategory::getAll(true), 'NAME', 'ID');

//получить список групп пользователей
$groupList = [];
$result = \Bitrix\Main\GroupTable::getList([
    'select' => ['ID', 'NAME'],
    'filter' => ['=ACTIVE' => 'Y', '!ID'=>'1'],
]);
while ($group = $result->fetch()) {
    $groupList[$group['ID']] = $group['NAME'];
}

// описание вкладок
$tabs = [
    [
        'DIV' => 'main',
        'TAB' => Loc::getMessage('LUKASHEVA_GARAGE_OPTIONS_TAB_MAIN'),
        'TITLE' => Loc::getMessage('LUKASHEVA_GARAGE_OPTIONS_TAB_MAIN_TITLE'),
    ],
];
$tabControl = new CAdminTabControl('garageTabControl', $tabs);

$error = '';

if ($request->isPost() && $request->getPost('save') !== null && check_bitrix_sessid()) {
    $categoryID = (int)$request->getPost('category_id');
    $groupID = (int)$request->getPost('group_id');
    $headOfDepartment = (int)$request->getPost('head_id');
    $BpTemplateID = (int)$request->getPost('bp_template_id');

    if (!array_key_exists($groupID, $groupList)) {
        $error = Loc::getMessage('LUKASHEVA_GARAGE_OPTIONS_GROUP_ERROR');
    } else {
        Option::set($moduleId, 'group_id', $groupID);
        Option::set($moduleId, 'head_id', $headOfDepartment);
        Option::set($moduleId, 'category_id', $categoryID);
        Option::set($moduleId, 'bp_template_id', $BpTemplateID);
        LocalRedirect($request->getRequestedPage() . '?mid=' . urlencode($moduleId)
            . '&lang=' . LANGUAGE_ID . '&saved=Y&' . $tabControl->ActiveTabParam());
    }
}

if ($error !== '') {
    CAdminMessage::ShowMessage($error);
}
if ($request->get('saved') === 'Y') {
    CAdminMessage::ShowNote(Loc::getMessage('LUKASHEVA_GARAGE_OPTIONS_SAVED'));
}

$formAction = $request->getRequestedPage() . '?mid=' . urlencode($moduleId) . '&lang=' . LANGUAGE_ID;
$currentCategory = (int)Option::get($moduleId, 'category_id');
$currentGroup = (int)Option::get($moduleId, 'group_id');
$currentHeadOfDepartment = (int)Option::get($moduleId, 'head_id');
$currentBp = (int)Option::get($moduleId, 'bp_template_id');

 //получить список пользователей  
$headOfDepartmentList = [];
$res = UserGroupTable::getList([
    'select' => ['USER_ID', 'USER_NAME' => 'USER.NAME', 'USER_LAST_NAME' => 'USER.LAST_NAME'],
    'filter' => [
        '=USER.ACTIVE' => 'Y',
    ],
]);
while ($aUser = $res->fetch()){
    $headOfDepartmentList[$aUser['USER_ID']] = $aUser['USER_NAME'].' '.$aUser['USER_LAST_NAME'];
}

$tabControl->Begin();
?>
<form method="post" action="<?= HtmlFilter::encode($formAction) ?>">
    <?= bitrix_sessid_post() ?>
    <?php $tabControl->BeginNextTab(); ?>
    <tr>
        <td width="40%"><label for="category_id"><?= Loc::getMessage('LUKASHEVA_GARAGE_OPTIONS_CATEGORY') ?></label></td>
        <td width="60%">
            <select id="category_id" name="category_id">
                <?php foreach ($category as $key => $val): ?>
                    <option value="<?= (int)$key ?>"<?= $currentCategory === (int)$key ? ' selected' : '' ?>>
                        <?= HtmlFilter::encode($val) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%"><label for="group_id"><?= Loc::getMessage('LUKASHEVA_GARAGE_OPTIONS_GROUP') ?></label></td>
        <td width="60%">
            <select id="group_id" name="group_id">
                <?php foreach ($groupList as $key => $val): ?>
                    <option value="<?= (int)$key ?>"<?= $currentGroup === (int)$key ? ' selected' : '' ?>>
                        <?= HtmlFilter::encode($val) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%"><label for="head_id"><?= Loc::getMessage('LUKASHEVA_GARAGE_OPTIONS_HEAD_GROUP') ?></label></td>
        <td width="60%">
            <select id="head_id" name="head_id">
                <option value="0"><?= Loc::getMessage('LUKASHEVA_GARAGE_OPTIONS_NO_VALUE') ?></option>
                <?php foreach ($headOfDepartmentList as $key => $val): ?>
                    <option value="<?= (int)$key ?>"<?= $currentHeadOfDepartment === (int)$key ? ' selected' : '' ?>>
                        <?= HtmlFilter::encode($val) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
     <tr>
        <td width="40%"><label for="bp_template_id"><?= Loc::getMessage('LUKASHEVA_GARAGE_OPTIONS_BP') ?></label></td>
        <td width="60%">
            <input type="number" name="bp_template_id" id="bp_template_id" value="<?=$currentBp ?>" >
        </td>
    </tr>
    <?php $tabControl->Buttons(); ?>
    <input type="submit" name="save" class="adm-btn-save"
           value="<?= HtmlFilter::encode(Loc::getMessage('LUKASHEVA_GARAGE_OPTIONS_SAVE')) ?>">
    <?php $tabControl->End(); ?>
</form>