<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
/** @global $APPLICATION */
$APPLICATION->SetTitle('Врачи');
$APPLICATION->SetAdditionalCSS('/doctors/style.css');

use App\Models\Lists\ProcsPropertyValuesTable as ProcsTable;
use App\Models\Lists\DoctorsPropertyValuesTable as DoctorsTable;

$doctors = [];
$doctor = [];
$procs = [];

$path = trim($_GET["path"], '/'); //из текущего пути удаляем последний слеш
$action = ''; //действие
$doctorCode = ''; // символьный код выбранного доктора

dump( $_SERVER['REQUEST_URI']);
dump($_GET);

if (!empty($path)) {
    $path_part = explode('/',$path);
    dump($path_part);
   // if (sizeof($path_part)>3) {
        //действие для редактирования или добавления доктора или добавление процедуры
        if (sizeof($path_part)==2 && $path_part[0] == 'edit') {
            $action = 'edit';
            $doctorCode = $path_part[1];
        }
        else if (sizeof($path_part) == 1 && in_array($path_part[0], ['new','newproc'])) {
            $action = $path_part[0];
        }
        else 
            $doctorCode = $path_part[0];
    //}
}
dump($doctorCode);
dump($action);

//выбран доктор выводим информацию о докторе и его процедурах
if (!empty($doctorCode)) {
    echo 'данные доктора';
}

// доктор не выбран, выводим список всех докторов
if (empty($doctorCode) && empty($action)) {
    echo 'список всех докторов';
    $doctors = DoctorsTable::getList([
        'select' => [
            'CODE' => 'ELEMENT.NAME',
            'FIRST_NAME' => 'FIRSTNAME',
            'SUR_NAME' => 'SURNAME',
            'LAST_NAME' => 'LASTNAME',
        ]
    ])->fetchAll();
    dump($doctors);
}

if ($action == 'newproc') {
    //добавлние процедуры
    echo 'добавлние процедуры';
}

if ($action == 'new' || $action == 'edit') {
    //добавление редактирование доктора
    echo 'добавление редактирование доктора';

}
?>
<section class = 'doctors'>
    <h1><a href="/dottors" >Врачи</a></h1>
    <?php if (empty($action)): ?>
        <div class="action-buttons">
            <?php if (empty($doctorCode)):?>
                <a href="/doctors/new" class="button">Добавить врача</a>
                <a href="/doctors/newproc" class="button">Добавить процедуру</a>
            <?php else:?>
                <a href="/doctors/edit/<?=$doctorCode?>" class="button">Изменить данные врача</a>
            <?php endif;?>
        </div>
    <?php endif;?>

    <div class="cards-list">
        <?php foreach ($doctors as $doc):?>
            <a href="/doctors/<?=$doc['CODE']?>" class="card">
                <div class="fio">
                    <?=$doc['LAST_NAME'].' '.$doc['FIRST_NAME'].' '.$doc['SUR_NAME']?>
                </div>
            </a>
        <?php endforeach;?>
    </div>
</section>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>