<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
/** @global $APPLICATION */
$APPLICATION->SetTitle('Множественное свойство');
$APPLICATION->SetAdditionalCSS('/doctors/style.css');

// получение одной записи из инфоблока Страна в виде объекта
/* $countryId = 40; // Element{Country}Table
$country = \Bitrix\Iblock\Elements\ElementCountryTable::getByPrimary(
    $countryId,  
    array(
        'select' => [
            '*',
            // 'NAME',
            'CODE',
            'CURRENCY',
            'CITIES.ELEMENT.NAME', 
            'CITIES.ELEMENT.ENGLISH',
            'CAPITAL.ELEMENT.NAME',
            'CAPITAL.ELEMENT.ENGLISH',
        ] 
    )
)->fetchObject();  */


// pr($country->getId()); // ID элемента
// pr($country->getName()); // имя элемента
// pr($country->getCode()); // символьный код элемента
// pr($country->getCurrency()->getValue()); // свойство элемента Валюта  

// свойство элемента Столица  
// pr($country->getCapital()->getElement()->getId()); 
// pr($country->getCapital()->getElement()->getName()); 
// pr($country->getCapital()->getElement()->getEnglish()->getValue()); // свойство элемента Столица

// множественное свойство элемента Города  
/* foreach($country->getCities()->getAll() as $prItem) {
    // pr($prItem->getElement()->getEnglish()->getValue().' '.$prItem->getElement()->getName());
    pr($prItem->getElement()->get('ID').' '.$prItem->getElement()->get('ENGLISH')->getValue().' '.$prItem->getElement()->getName());
} */


// получение одной записи из инфоблока Страна в виде массива
/* $countryId = 40; 
$res = \Bitrix\Iblock\Elements\ElementCountryTable::getByPrimary($countryId, 
    array('select' => [
            // '*', 
            'CURRENCY',
            'CITIES.ELEMENT.NAME', 
            'CITIES.ELEMENT.ENGLISH',
            'CAPITAL.ELEMENT.NAME',
            'CAPITAL.ELEMENT.ENGLISH',
        ]
    )
)->fetch();

// pr($res['NAME']); // имя элемента
// pr($res['IBLOCK_ELEMENTS_ELEMENT_COUNTRY_CAPITAL_ELEMENT_NAME']); // CAPITAL - единственное свойство Столица, тип привязка к элементам в виде списка
pr($res); */


// получение списка записей из инфоблока Cтрана в виде коллекции
/* $countryId = 40;
$countries = \Bitrix\Iblock\Elements\ElementCountryTable::getList([
        'select' => [
            'ID', 
            'NAME', 
            'CURRENCY', // CURRENCY - единственное свойство Валюта, тип строка
            'CAPITAL.ELEMENT', // CAPITAL - единственное свойство Столица, тип привязка к элементам в виде списка
            'CITIES.ELEMENT', // CITIES - множественное свойство Города, тип привязка к элементам в виде списка 
            'CITIES.ELEMENT.ENGLISH' // ENGLISH - единственное свойство En, инфоблок Города
        ], 
        'filter' => [
            'ID' => $countryId,
            'ACTIVE' => 'Y'
        ],
   ])->fetchCollection();


foreach ($countries as $element) {
    pr($element->getName());
    pr($element->getCurrency()->getValue());

    pr($element->getCapital()->getElement()->getName());

    foreach($element->getCities()->getAll() as $prItem) {
        pr($prItem->getElement()->get('ID').' '.$prItem->getElement()->getName().' '.$prItem->getElement()->get('ENGLISH')->getValue());
    }
} */


/*$countryId = 77;
// получение списка записей из инфоблока Cтрана в виде массива
$countries = \Bitrix\Iblock\Elements\ElementCountryTable::getList([
        'select' => [
            'ID', 
            'NAME', 
            'CURRENCY', // CURRENCY - единственное свойство Валюта, тип строка, инфоблок Страна
            'CAPITAL.ELEMENT', // CAPITAL - единственное свойство Столица, тип привязка к элементам в виде списка, инфоблок Страна
            'CITIES.ELEMENT', // CITIES - множественное свойство Города, тип привязка к элементам в виде списка, инфоблок Страна
            'CITIES.ELEMENT.ENGLISH' // ENGLISH - единственное свойство En, инфоблок Города
        ], 
        'filter' => [
            'ID' => $countryId,
            'ACTIVE' => 'Y'
        ],
   ])->fetchAll();

foreach ($countries as $key => $item) {
    // pr($item['NAME'].' '.$item['IBLOCK_ELEMENTS_ELEMENT_COUNTRY_CITIES_ELEMENT_NAME']);
    pr($item['NAME']);
    // pr($item);
}*/


// число записей
/* $res = \Bitrix\Iblock\ElementTable::getList(array(
    // сортировка
    'order' => array('SORT' => 'ASC'),
    // выбираемые поля без свойств, свойства можно получать только при обращении к ORM классу, конкретного инфоблока
    'select' => array('ID', 'NAME'),
    // фильтр только по полям элемента
    'filter' => array('IBLOCK_ID' => 19),
    // группировка по полю, order должен быть пустой
    'group' => array('TAGS'),
    // ограничение выбираемого кол-ва
    'limit' => 1000,
    // число, указывающее номер первого столбца в результате
    'offset' => 0,
    // дает возможность получить кол-во элементов через метод getCount()
    'count_total' => true,
    // массив полей сущности, создающихся динамически
    'runtime' => array(),
    // разрешает получение нескольких одинаковых записей
    'data_doubling' => false,
    // кеш запроса
    'cache' => array(
        'ttl' => 3600,
        'cache_joins' => true
    ),
));

$count = $res->getCount();// количество записей  в БД
pr($count);

$countries = $res->fetchAll();
pr($countries); */


/* $res = \Bitrix\Iblock\ElementTable::getList(array(
    'select' => array('ID', 'NAME'),
    'filter' => array('IBLOCK_ID' => 19),
    'count_total' => true,
));

$count = $res->getSelectedRowsCount(); // Теперь этот метод точно сработает
pr($count); */



// детальная картинка
/*$countryId = 77; // Element{Country}Table
$country = \Bitrix\Iblock\Elements\ElementCountryTable::getByPrimary(
    $countryId, 
    array(
        'select' => [
            '*',
            // 'NAME',
            'CURRENCY',
            'CITIES.ELEMENT.NAME', 
            'CITIES.ELEMENT.ENGLISH',
            'CAPITAL.ELEMENT.NAME',
            'CAPITAL.ELEMENT.ENGLISH',
        ] 
    )
)->fetchObject();

$country->getDetailPicture();
$arFile = CFile::MakeFileArray($country->getDetailPicture());
pr($arFile);*/



// добавление данных записей в инфоблок Автомобили (через старое ядро)
/*
\Bitrix\Main\Loader::IncludeModule("iblock");
$result = \Bitrix\Iblock\Elements\ElementCarTable::add(array(
   'NAME' => 'TEST',
   'ACTIVE' => 'Y',
)); 

if ($result->isSuccess()) {
    $id = $result->getId();
    CIBlockElement::SetPropertyValuesEx($id, false, array(
        'MODEL' => 'X5',
        'MANUFACTURER_ID'=>30,
        'CITY_ID'=>[36, 37],
        'ENGINE_VOLUME'=>'4',
        'PRODUCTION_DATE'=>date('d.m.Y'),
    ));
}*/


?>

