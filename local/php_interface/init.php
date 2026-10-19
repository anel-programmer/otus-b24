<?php
//composer
if (file_exists(__DIR__.'/../../vendor/autoload.php'))
{
    require_once __DIR__.'/../../vendor/autoload.php';
}

//App
if (file_exists(__DIR__.'/../App/autoload.php'))
{
    require_once __DIR__.'/../App/autoload.php';
}

//Кастомные события
if (file_exists(__DIR__.'/events.php'))
{
    require_once __DIR__.'/events.php';
}
// подключение класса для бизнес-процесса Обходной лист
if (file_exists(__DIR__.'/classes/LKIblock.php'))
{
    require_once __DIR__.'/classes/LKIblock.php';
}

if (file_exists(__DIR__.'/classes/Dadata.php'))
{
    require_once __DIR__.'/classes/Dadata.php';
}


// вывод данных 
function pr($var, $type = false) {
    echo '<pre style="font-size:10px; border:1px solid #000; background:#FFF; text-align:left; color:#000;">';
    if ($type)
        var_dump($var);
    else
        print_r($var);
    echo '</pre>';
}

/**
 * Подключение события которое кастомный тип поля рейтинг
 */
 /*  $eventManager->addEventHandler(
    'iblock',
    'OnIBlockPropertyBuildList',
    ['CIBlockPropertyRating','GetUserTypeDescription']
);

class CIBlockPropertyRating 
{
    public static function GetUserTypeDescription()
    {
        return [
            'USER_TYPE' => 'rating',
            'DESCRIPTION' => 'Рейтинг (звездочки)',
            'PROPERTY_TYPE' => 'N',
            'CLASS_NAME' => 'CIBlockPropertyRating',
        ];
    }
} */

