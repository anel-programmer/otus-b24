<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle('Вывод связанных полей');

use App\Models\Lists\CountryPropertyValuesTable as CountryTable;
//use App\Models\Lists\CityPropertyValuesTable as CityTable;

// получаем список записей из инфоблока Автомобили в виде массива
$cars = CountryTable::getList([        
	'select'=>[
        'ID'=>'IBLOCK_ELEMENT_ID',
        'NAME'=>'ELEMENT.NAME',
 		'CITY_ID'=>'CITIES',
        'ARRCITY' => 'CITYNAME.ELEMENT.NAME',
    ]
])->fetchAll();

pr($cars); 

$cars = CountryTable::getMap();
dump($cars);
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>