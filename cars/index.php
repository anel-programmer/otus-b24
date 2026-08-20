<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle('Вывод связанных полей');
use App\Models\Lists\CarsPropertyValuesTable as CarsTable;


// получаем список записей из инфоблока Автомобили в виде массива
$cars = CarsTable::getList([        
	'select'=>[
        'ID'=>'IBLOCK_ELEMENT_ID',
        'NAME'=>'ELEMENT.NAME',
 		'MANUFACTURER_ID'=>'MANUFACTURER_ID',
    ]
])->fetchAll();

pr($cars); 


// получаем список записей из инфоблока Автомобили в виде массива методом query
$cars = CarsTable::query()
    ->setSelect([
        '*',
        'NAME' => 'ELEMENT.NAME',
        'MANUFACTURER_NAME' => 'MANUFACTURER.ELEMENT.NAME',
        'MANUFACTURER_COUNTRY' => 'MANUFACTURER.CURRENCY', 
    ])
    ->setOrder(['MANUFACTURER_COUNTRY' => 'desc'])
    ->registerRuntimeField(
        null,
        new \Bitrix\Main\Entity\ReferenceField(
            'MANUFACTURER',
            App\Models\Lists\CountryPropertyValuesTable::getEntity(),
            ['=this.MANUFACTURER_ID' => 'ref.IBLOCK_ELEMENT_ID']
        )
    )   
    ->fetchAll();
pr($cars);

// получаем список записей из инфоблока Автомобили в виде массива методом query
$cars = CarsTable::query()
    ->setSelect([
        '*',
        'NAME' => 'ELEMENT.NAME',
        'CITY_NAME' => 'CITY.ELEMENT.NAME',
        'CITY_ENGLISH' => 'CITY.ENGLISH',
    ])
    ->registerRuntimeField(
        null,
        new \Bitrix\Main\Entity\ReferenceField(
            'CITY',
            App\Models\Lists\CityPropertyValuesTable::getEntity(),
            ['=this.CITY_ID' => 'ref.IBLOCK_ELEMENT_ID']
        )
    )   
    ->fetchAll();
pr($cars);


