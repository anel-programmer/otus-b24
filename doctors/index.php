<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
/** @global $APPLICATION */
$APPLICATION->SetTitle('Врачи');
$APPLICATION->SetAdditionalCSS('/doctors/style.css');

use App\Models\Lists\ProcsPropertyValuesTable as ProcsTable;
use App\Models\Lists\DoctorsPropertyValuesTable as DoctorsTable;

$doctors = [];
$doctor = [];
$procsData = [];

$action = ''; //действие
$doctorCode = ''; // символьный код выбранного доктора

//dump($_GET);
if (isset($_GET['action']))
    $action = $_GET['action'];
if (isset($_GET['doc']))
    $doctorCode = $_GET['doc'];

 
switch ($action) {
    case 'newproc':
        //добавление процедуры
        if (isset($_POST['proc-submit'])){
            unset($_POST['proc-submit']);
            if (ProcsTable::add($_POST)){
                header("Location: /doctors");
                die();
            }
            else {
              echo "Ошибка добавления процедуры";
            }
        }
        break;
     case 'viewproc':
         // получаем массив всех процедур
        $procsData = ProcsTable::getList([
            'select' => [
                'ID' => 'ELEMENT.ID',
                'NAME' => 'ELEMENT.NAME',
            ],
            'filter' => [
                'ELEMENT.ACTIVE' => 'Y',
            ],
        ])->fetchAll();
        break;
    case 'new':
        //добавление редактирование доктора
         if (isset($_POST['doctor-submit'])){
            unset($_POST['doctor-submit']);
            if (DoctorsTable::add($_POST)){
                header("Location: /doctors");
                die();
            }
            else {
              echo "Ошибка добавления врача";
            }
        }
        // получаем массив всех процедур
        $procsData = ProcsTable::getList([
            'select' => [
                'ID' => 'ELEMENT.ID',
                'NAME' => 'ELEMENT.NAME',
            ],
            'filter' => [
                'ELEMENT.ACTIVE' => 'Y',
            ],
        ])->fetchAll();
        break;
    case 'edit':
        //добавление редактирование доктора
        if (isset($_POST['doctor-submit'])){
            unset($_POST['doctor-submit']);

            $aID = $_POST['ID'];
            unset($_POST['ID']);
            $_POST['IBLOCK_ELEMENT_ID'] = $aID;

            $arrProcs = $_POST['PROCS'];
            unset($_POST['PROCS']);
            CIBlockElement::SetPropertyValues($aID, DoctorsTable::IBLOCK_ID, $arrProcs,'PROCS');
            if (DoctorsTable::update($aID, $_POST)){
                header("Location: /doctors");
                die();
            }
            else {
              echo "Ошибка редактирования данных врача";
            }
        }
        $doctorData = \Bitrix\Iblock\Elements\ElementDoctorsTable::getList([
                'select' => [
                    'NAME',
                    'SURNAME',
                    'LASTNAME',
                    'FIRSTNAME',
                    'PROCS.ELEMENT',
                ],
                'filter' => [
                    'NAME' => $doctorCode,
                    'ACTIVE' => 'Y',
                ],
            ])->fetchObject();
        //массив ID процедур привязанных к доктору
        $docProcs = [];
        foreach($doctorData->getProcs()->getAll() as $prItem) {
            $docProcs[] = $prItem->getElement()->getId();
        }

       
        $procsData = ProcsTable::getList([
            'select' => [
                'ID' => 'ELEMENT.ID',
                'NAME' => 'ELEMENT.NAME',
            ],
            'filter' => [
                'ELEMENT.ACTIVE' => 'Y',
            ],
        ])->fetchAll();
        
        break;
    case 'view':
        //выбран доктор выводим информацию о докторе и его процедурах
        if (!empty($doctorCode)) {
            // получение списка записей из инфоблока Врачи в виде объекта
            $doctorData = \Bitrix\Iblock\Elements\ElementDoctorsTable::getList([
                'select' => [
                    'NAME',
                    'SURNAME',
                    'LASTNAME',
                    'FIRSTNAME',
                    'PROCS.ELEMENT', // PROCS - множественное свойство Процедуры, тип привязка к элементам, инфоблок Процедуры
                ],
                'filter' => [
                    'NAME' => $doctorCode,
                    'ACTIVE' => 'Y',
                ],
            ])->fetchObject(); 
        }
        break;
    default:
        // получаем список записей из инфоблока Врачи в виде массива
        $doctors = DoctorsTable::getList([
            'select' => [
                'CODE' => 'ELEMENT.NAME',
                'FIRST_NAME' => 'FIRSTNAME',
                'SUR_NAME' => 'SURNAME',
                'LAST_NAME' => 'LASTNAME',
                'PROCEDURES_NAME' => 'PROCEDURES.ELEMENT',
            ]
        ])->fetchAll();
        
        break;
}
?>

<section class = 'doctors'>
    <h1><a href="/dottors" >Врачи</a></h1>
        <div class="action-buttons">
            <a href="/doctors/" class="button">Все врачи</a>
            <?php if ($action!='new'):?>
                <a href="/doctors/?action=new" class="button">Добавить врача</a>
            <?php endif; ?>
            <a href="/doctors/?action=viewproc" class="button">Список процедур</a>
            <?php if ($action!='newproc'):?>
                <a href="/doctors/?action=newproc" class="button">Добавить процедуру</a>
            <?php endif; ?>
            <?php if (!empty($action) && ($action=='view')):?>
                <a href="/doctors/?action=edit&doc=<?=$doctorCode?>" class="button">Изменить данные врача</a>
            <?php endif; ?>
        </div>
    <?if (!empty($doctors) && ($action == '')):?>
    <div class="cards-list">
        <?php foreach ($doctors as $doc):?>
            <a href="/doctors/?action=view&doc=<?=$doc['CODE']?>" class="card">
                <div class="fio">
                    <?=$doc['LAST_NAME'].' '.$doc['FIRST_NAME'].' '.$doc['SUR_NAME']?>
                </div>
            </a>
        <?php endforeach;?>
    </div>
    <?endif;?>
    <?if (!empty($procsData) && ($action == 'viewproc')):?>
         <h2>Список всех процедур</h2>
         <ul>
        <?php foreach ($procsData as $procItem):?>
            <li><?= $procItem['NAME']?></li>
        <?php endforeach;?>
        </ul>
    <?endif;?>
    <?if ($action=='newproc'):?>
        <form method="POST" class="add-procedure-form">
            <h2>Добавить процедуру</h2>
            <div class="form-group">
                <input type="text" value="" name="NAME" placeholder="Название процедуры">
                <input type="submit" value="Добавить" name="proc-submit">
            </div>
        </form>
    <?endif;?>
    <?if ($action=='new'):?>
        <form method="POST" class="add-procedure-form">
            <h2>Добавить врача</h2>
            <div class="form-group">
                <input type="text" value="" name="NAME" placeholder="Код доктора">
                <input type="text" value="" name="FIRSTNAME" placeholder="Имя">
                <input type="text" value="" name="SURNAME" placeholder="Отчество">
                <input type="text" value="" name="LASTNAME" placeholder="Фамилия">
                <div class="form-group-select">
                <select  name="PROCS[]"  id="procs-select" placeholder="Процедуры" multiple>
                    <?php foreach ($procsData as $procItem):?>
                        <option value="<?= $procItem['ID']?>"><?= $procItem['NAME']?></option>
                    <?php endforeach;?>
                </select>
                </div>
                <input type="submit" value="Добавить" name="doctor-submit">
            </div>
        </form>
    <?endif;?>
    <?if ($action =='view' && !is_null($doctorData)):?>
        <div class="doctor-details">
            <div class="doctor-code"><?= $doctorData->getName(); ?> </div>
                <div class="fio">
                    <?= $doctorData->get('FIRSTNAME')->getValue(); ?> 
                    <?= $doctorData->get('SURNAME')->getValue(); ?> 
                    <?= $doctorData->get('LASTNAME')->getValue(); ?> 
                </div>
                <div class="procedures">
                    <h3>Процедуры:</h3>
                    <ul> 
                   <? foreach($doctorData->getProcs()->getAll() as $prItem):?>
                        <li><?= $prItem->getElement()->getName();?></li>
                   <? endforeach;?>
                    </ul>
                </div>
        </div>
    <?endif;?>
    <?if ($action == 'edit'): ?>
            <form method="POST" class="add-procedure-form">
                <h2>Редактирование данных врача</h2>
                <div class="form-group">
                    <input type="hidden" value="<?= $doctorData->getId(); ?>" name="ID">
                    <input type="text" value="<?= $doctorData->getName(); ?>" name="NAME" placeholder="Код доктора">
                    <input type="text" value="<?= $doctorData->get('FIRSTNAME')->getValue(); ?>" name="FIRSTNAME" placeholder="Имя">
                    <input type="text" value="<?= $doctorData->get('SURNAME')->getValue(); ?>" name="SURNAME" placeholder="Отчество">
                    <input type="text" value="<?= $doctorData->get('LASTNAME')->getValue(); ?>" name="LASTNAME" placeholder="Фамилия">
                    <div class="form-group-select"> 
                    <select  name="PROCS[]"  id="procs-select" placeholder="Процедуры" multiple>
                        <?php foreach ($procsData as $procItem):?>
                            <option value="<?= $procItem['ID']?>" <?if (in_array($procItem['ID'], $docProcs)):?> selected <?endif;?>><?= $procItem['NAME']?></option>
                        <?php endforeach;?>
                    </select>
                    </div>
                    <input type="submit" value="Добавить" name="doctor-submit">
                </div>
            </form>
    <?endif;?> 
 </section>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>