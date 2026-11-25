<?php
use Bitrix\Main\Loader;
use Bitrix\Iblock\ElementTable;
use Bitrix\Catalog\ProductTable;
use Bitrix\Seo\Engine\Bitrix;
use \Bitrix\Main\Entity\ReferenceField;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Запуск агента для обновления количества запчастей вручную");
if (Loader::includeModule('lukasheva.garage')) {
    $res = \CAgent::GetList([],['NAME' => 'Lukasheva\Garage\Agent\UpdateQuantity::update();', 'MODULE_ID'=>'lukasheva.garage']);
    $exists = $res->fetch();

    if ($exists) {
        Lukasheva\Garage\Agent\UpdateQuantity::update();   
        echo('Агент успешно отработал.');   
    }
    else {
        echo('Агент не найден');
    }
} 
//получить ID каталога
/* $catalog_id = \Bitrix\Main\Config\Option::get('crm', 'default_product_catalog_id'); 
var_dump($catalog_id); */
/*  $result = ProductTable::getList([
    'select' => [
        'ID',
    ],
    'filter' => [
        '=IBLOCK_ELEMENT.IBLOCK_ID' => $catalog_id,
        'IBLOCK_ELEMENT.ACTIVE' => 'Y',
        '=IBLOCK_ELEMENT.IBLOCK_SECTION_ID' => 12,
    ],
]);
while ($product = $result->fetch()) {
    pr($product);
    ProductTable::update($product['ID'], [
        'QUANTITY' => 10
    ]);
} */

/*  $result = ProductTable::getList([
    'select' => [
        'ID',
        'QUANTITY',
    ],
    'filter' => [
        '<=QUANTITY' => 0,
        '=IBLOCK_ELEMENT.IBLOCK_ID' => $catalog_id,
        'IBLOCK_ELEMENT.ACTIVE' => 'Y',
        '=IBLOCK_ELEMENT.IBLOCK_SECTION_ID' => 12,
    ],
]);  
while ($product = $result->fetch()) {
    pr($product);

} */

/* $result = ElementTable::query()
    ->setSelect([
        'ID',
        'NAME',
        'IBLOCK_SECTION_ID',
        'IBLOCK_SECTION',
        'QUANTITY' => 'PRODUCT.QUANTITY',
    ])
    ->registerRuntimeField(
        null,
        new ReferenceField(
            'PRODUCT',
            ProductTable::getEntity(),
            ['=this.ID' => 'ref.ID']
        )
    )
    ->setFilter([
        '=IBLOCK_ID' => $catalog_id,
        'ACTIVE' => 'Y',
        '=IBLOCK_SECTION_ID' => 12,
    ])
    ->exec(); */

/* while ($product = $result->fetch()) {
    pr($product);
} */
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>