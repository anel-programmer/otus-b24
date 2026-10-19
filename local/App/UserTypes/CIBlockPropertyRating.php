<?php
namespace App\UserTypes;

class CIBlockPropertyRating 
{
    public static function GetUserTypeDescription()
    {
        return [
            'USER_TYPE' => 'rating',
            'DESCRIPTION' => 'Рейтинг (звездочки)',
            'PROPERTY_TYPE' => 'N',
            'CLASS_NAME' => 'CIBlockPropertyRating',
            'GetPropertyFieldHtml' => [__CLASS__, 'GetPropertyFieldHtml'],
            'ConvertToDB' => [__CLASS__, 'ConvertToDB'],
            'ConvertFromDB' => [__CLASS__, 'ConvertFromDB'],
        ];
    }

        /**
     * GetDBColumnType - опциональный метод
     *
     * Определяет тип SQL-колонки для хранения значений.
     *
     * @param array $arProperty - метаданные свойства
     * @return string - SQL-определение типа колонки
     */
    public static function GetDBColumnType($arProperty)
    {
        // Рейтинг от 1 до 5 - достаточно целого числа
        switch (strtolower($GLOBALS['DB']->type)) {
            case 'mysql':
                return 'int(1)';
            case 'oracle':
                return 'number(1)';
            case 'mssql':
                return 'int';
            default:
                return 'int(1)';
        }
    }

    /**
     * PrepareSettings - опциональный метод
     *
     * Подготавливает настройки свойства перед сохранением.
     *
     * @param array $arProperty - метаданные свойства с новыми настройками
     * @return array - подготовленные настройки
     */
    public static function PrepareSettings($arProperty)
    {
        // Получаем настройки или значения по умолчанию
        $maxRating = isset($arProperty['SETTINGS']['MAX_RATING'])
            ? intval($arProperty['SETTINGS']['MAX_RATING'])
            : 5;

        $defaultRating = isset($arProperty['SETTINGS']['DEFAULT_RATING'])
            ? intval($arProperty['SETTINGS']['DEFAULT_RATING'])
            : 3;

        // Ограничиваем значения
        if ($maxRating < 1) $maxRating = 1;
        if ($maxRating > 10) $maxRating = 10;
        if ($defaultRating < 1) $defaultRating = 1;
        if ($defaultRating > $maxRating) $defaultRating = $maxRating;

        return array(
            'MAX_RATING' => $maxRating,
            'DEFAULT_RATING' => $defaultRating,
            'SHOW_STARS' => isset($arProperty['SETTINGS']['SHOW_STARS'])
                ? $arProperty['SETTINGS']['SHOW_STARS']
                : 'Y',
        );
    }

    /**
     * GetSettingsHTML - опциональный метод
     *
     * Возвращает HTML для отображения настроек свойства в админке.
     *
     * @param array $arProperty - метаданные свойства
     * @param array $strHTMLControlName - управляющие параметры
     * @param bool $bVarsFromForm - признак, что данные из формы
     * @return string - HTML-код настроек
     */
    public static function GetSettingsHTML($arProperty, $strHTMLControlName, &$bVarsFromForm)
    {
        // Получаем текущие значения настроек
        if ($bVarsFromForm) {
            $maxRating = isset($_REQUEST[$strHTMLControlName['NAME']]['MAX_RATING'])
                ? intval($_REQUEST[$strHTMLControlName['NAME']]['MAX_RATING'])
                : 5;
            $defaultRating = isset($_REQUEST[$strHTMLControlName['NAME']]['DEFAULT_RATING'])
                ? intval($_REQUEST[$strHTMLControlName['NAME']]['DEFAULT_RATING'])
                : 3;
            $showStars = isset($_REQUEST[$strHTMLControlName['NAME']]['SHOW_STARS'])
                ? $_REQUEST[$strHTMLControlName['NAME']]['SHOW_STARS']
                : 'Y';
        } else {
            $maxRating = isset($arProperty['SETTINGS']['MAX_RATING'])
                ? intval($arProperty['SETTINGS']['MAX_RATING'])
                : 5;
            $defaultRating = isset($arProperty['SETTINGS']['DEFAULT_RATING'])
                ? intval($arProperty['SETTINGS']['DEFAULT_RATING'])
                : 3;
            $showStars = isset($arProperty['SETTINGS']['SHOW_STARS'])
                ? $arProperty['SETTINGS']['SHOW_STARS']
                : 'Y';
        }

        // Формируем HTML настроек
        $html = '<tr>';
        $html .= '<td>Максимальный рейтинг:</td>';
        $html .= '<td>';
        $html .= '<select name="' . $strHTMLControlName['NAME'] . '[MAX_RATING]">';
        for ($i = 1; $i <= 10; $i++) {
            $selected = ($i == $maxRating) ? ' selected' : '';
            $html .= '<option value="' . $i . '"' . $selected . '>' . $i . '</option>';
        }
        $html .= '</select>';
        $html .= '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td>Рейтинг по умолчанию:</td>';
        $html .= '<td>';
        $html .= '<select name="' . $strHTMLControlName['NAME'] . '[DEFAULT_RATING]">';
        for ($i = 1; $i <= $maxRating; $i++) {
            $selected = ($i == $defaultRating) ? ' selected' : '';
            $html .= '<option value="' . $i . '"' . $selected . '>' . $i . '</option>';
        }
        $html .= '</select>';
        $html .= '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td>Отображать звёзды:</td>';
        $html .= '<td>';
        $html .= '<input type="checkbox" name="' . $strHTMLControlName['NAME'] . '[SHOW_STARS]" ';
        $html .= 'value="Y" ' . ($showStars == 'Y' ? 'checked' : '') . '>';
        $html .= '</td>';
        $html .= '</tr>';

        return $html;
    }

    /**
     * GetPropertyFieldHtml - опциональный метод
     *
     * Возвращает HTML для редактирования значения свойства в форме элемента инфоблока.
     * Аналог GetEditFormHTML для пользовательских полей сущностей.
     *
     * @param array $arProperty - метаданные свойства
     * @param array $value - текущее значение
     * @param array $strHTMLControlName - управляющие параметры
     * @return string - HTML-код для редактирования
     */
    public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        // Получаем настройки
        $maxRating = isset($arProperty['SETTINGS']['MAX_RATING'])
            ? intval($arProperty['SETTINGS']['MAX_RATING'])
            : 5;
        $defaultRating = isset($arProperty['SETTINGS']['DEFAULT_RATING'])
            ? intval($arProperty['SETTINGS']['DEFAULT_RATING'])
            : 3;
        $showStars = isset($arProperty['SETTINGS']['SHOW_STARS'])
            ? $arProperty['SETTINGS']['SHOW_STARS']
            : 'Y';

        // Получаем текущее значение
        $currentValue = isset($value['VALUE']) ? intval($value['VALUE']) : $defaultRating;
        if ($currentValue < 1) $currentValue = 1;
        if ($currentValue > $maxRating) $currentValue = $maxRating;

        // Формируем HTML
        $html = '<div style="display: flex; align-items: center;">';

        // Создаём radio-кнопки для выбора рейтинга
        for ($i = 1; $i <= $maxRating; $i++) {
            $checked = ($i == $currentValue) ? ' checked' : '';
            $html .= '<label style="margin-right: 5px; cursor: pointer;">';
            $html .= '<input type="radio" ';
            $html .= 'name="' . $strHTMLControlName['VALUE'] . '" ';
            $html .= 'value="' . $i . '" ';
            $html .= $checked . '>';

            // Если включено отображение звёзд
            if ($showStars == 'Y') {
                $html .= ' ' . str_repeat('★', $i) . str_repeat('☆', $maxRating - $i);
            } else {
                $html .= ' ' . $i;
            }

            $html .= '</label>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * GetAdminListViewHTML - опциональный метод
     *
     * Возвращает HTML для отображения значения свойства в административном списке элементов.
     *
     * @param array $arProperty - метаданные свойства
     * @param array $value - текущее значение
     * @param array $strHTMLControlName - управляющие параметры
     * @return string - HTML для отображения
     */
    public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
    {
        $rating = isset($value['VALUE']) ? intval($value['VALUE']) : 0;
        $maxRating = isset($arProperty['SETTINGS']['MAX_RATING'])
            ? intval($arProperty['SETTINGS']['MAX_RATING'])
            : 5;

        if ($rating <= 0) {
            return '&nbsp;';
        }

        // Отображаем звёзды
        $html = '<span style="color: #f5a623; font-size: 14px;">';
        $html .= str_repeat('★', $rating);
        $html .= str_repeat('☆', $maxRating - $rating);
        $html .= ' <span style="color: #666; font-size: 12px;">(' . $rating . '/' . $maxRating . ')</span>';
        $html .= '</span>';

        return $html;
    }

    /**
     * GetPublicViewHTML - опциональный метод
     *
     * Возвращает HTML для отображения значения в публичной части сайта.
     *
     * @param array $arProperty - метаданные свойства
     * @param array $value - текущее значение
     * @param array $strHTMLControlName - управляющие параметры
     * @return string - HTML для отображения
     */
    public static function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
    {
        // Используем тот же код, что и для админского списка
        return self::GetAdminListViewHTML($arProperty, $value, $strHTMLControlName);
    }

    /**
     * GetPublicEditHTML - опциональный метод
     *
     * Возвращает HTML для редактирования значения в публичной части.
     *
     * @param array $arProperty - метаданные свойства
     * @param array $value - текущее значение
     * @param array $strHTMLControlName - управляющие параметры
     * @return string - HTML для редактирования
     */
    public static function GetPublicEditHTML($arProperty, $value, $strHTMLControlName)
    {
        // Используем тот же код, что и для админской формы
        return self::GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName);
    }

    /**
     * ConvertToDB - опциональный метод
     *
     * Преобразует значение перед сохранением в базу данных.
     *
     * @param array $arProperty - метаданные свойства
     * @param mixed $value - значение для сохранения
     * @return mixed - преобразованное значение
     */
    public static function ConvertToDB($arProperty, $value)
    {
        // Приводим к целому числу
        $rating = intval($value['VALUE']);
        $maxRating = isset($arProperty['SETTINGS']['MAX_RATING'])
            ? intval($arProperty['SETTINGS']['MAX_RATING'])
            : 5;

        // Ограничиваем значение диапазоном
        if ($rating < 1) $rating = 1;
        if ($rating > $maxRating) $rating = $maxRating;

        $value['VALUE'] = $rating;
        return $rating; //$value['VALUE'];
    }

    /**
     * ConvertFromDB - опциональный метод
     *
     * Преобразует значение при чтении из базы данных.
     *
     * @param array $arProperty - метаданные свойства
     * @param mixed $value - значение из БД
     * @return mixed - преобразованное значение
     */
    public static function ConvertFromDB($arProperty, $value)
    {
        return intval($value);
    }

    /**
     * CheckFields - опциональный метод
     *
     * Проверяет корректность значения перед сохранением.
     *
     * @param array $arProperty - метаданные свойства
     * @param mixed $value - значение для проверки
     * @return array - массив ошибок
     */
    public static function CheckFields($arProperty, $value)
    {
        $errors = array();

        // Если свойство обязательно и значение пустое
        if ($arProperty['IS_REQUIRED'] == 'Y' && empty($value)) {
            $errors[] = 'Поле "' . $arProperty['NAME'] . '" обязательно для заполнения';
            return $errors;
        }

        // Проверяем, что значение - число в допустимом диапазоне
        if (!empty($value)) {
            $rating = intval($value);
            $maxRating = isset($arProperty['SETTINGS']['MAX_RATING'])
                ? intval($arProperty['SETTINGS']['MAX_RATING'])
                : 5;

            if ($rating < 1 || $rating > $maxRating) {
                $errors[] = 'Значение поля "' . $arProperty['NAME'] . '" должно быть числом от 1 до ' . $maxRating;
            }
        }

        return $errors;
    }

    /**
     * GetFilterData - опциональный метод
     *
     * Возвращает настройки для фильтрации по данному свойству.
     *
     * @param array $arProperty - метаданные свойства
     * @return array - настройки фильтра
     */
    public static function GetFilterData($arProperty)
    {
        return array(
            'type' => 'number',
            'filterable' => true,
            'searchable' => false,
        );
    }
}