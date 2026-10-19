<?php
namespace App\UserTypes;

use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Iblock\Iblock;

class CIBlockPropertyReservation 
{
    public static function GetUserTypeDescription()
    {
        return [
            'USER_TYPE' => 'reservation', // код типа пользовательского свойства
            'DESCRIPTION' => 'Бронирование записи',  // название типа пользовательского свойства
            'PROPERTY_TYPE' => 'S', // тип поля
            'CLASS_NAME' => 'CIBlockPropertyReservation', // название класса
			'GetPublicViewHTML' => [__CLASS__, 'GetPublicViewHTML'], // метод отображения значения в списке в публичной части
            'GetPublicEditHTML' => [__CLASS__, 'GetPublicEditHTML'], // метод редактирования значения в списке в публичной части
            'GetAdminListViewHTML' => [__CLASS__, 'GetAdminListViewHTML'], // метод отображения значения в списке в админке
			'GetPropertyFieldHtml' => [__CLASS__, 'GetPropertyFieldHtml'], // метод отображения свойства при добавлении и редактировании в админке
			"GetSettingsHTML" => [__CLASS__, "GetSettingsHTML"], // генерирует HTML-код полей настройки в форме редактирования свойства
            "PrepareSettings" => [__CLASS__, "PrepareSettings"], // брабатывает и сохраняет выбранные значения настроек в базу данных, должен использоваться с GetSettingsHTML
        ];
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
		$propValue = self::preparePropValue($value['VALUE'], $arProperty);
        
        $strResult = "<a class=\"procedure-item-grid\"";
        $strResult .= " data-procedure-id=\"{$propValue['ID']}\"";
        $strResult .= " data-procedure-name=\"{$propValue['NAME']}\"";
        $strResult .= " data-iblock-id=\"{$propValue['IBLOCK_ID']}\"";
        $strResult .= " data-fio=\"{$propValue['FIO']}\"";
        $strResult .= " href=\"javascript:void(0);\">";
        $strResult .= "{$propValue['NAME']}";
        $strResult .= "</a>";
		/** @var CMain $APPLICATION */
		global $APPLICATION;

		ob_start();
		$APPLICATION->IncludeComponent(
			"otus:reservation.field",
			"",
			Array(
				'CURRENT_VALUES' => $strResult,
			)
		);
		$result = ob_get_contents();
		ob_end_clean();

		return $result;

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
        if ($value['VALUE']!=='')
            return '';
        $propVal = self::preparePropValue($value['VALUE'], $arProperty);
		return $propVal['NAME'];
    }

	public static function preparePropValue(string $val, array $arProperty)
    {
		if ($val!==''){
            $explVal = explode(';', $val); //разбиваем строку на массив частей с разделителем ;

            return [
                'ID' => current($explVal),
                'NAME' => array_pop($explVal),
                'FIO' => CurrentUser::get()->getFullName() ?: CurrentUser::get()->getLogin(),
                'IBLOCK_ID' => intval($arProperty["USER_TYPE_SETTINGS"]["IBLOCK_ID_TO"]),
            ];
        }
        return [];
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
		return self::GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName);
	}

    
	public static function GetPropertyFieldHtml($arProperty, $arValue, $strHTMLControlName)
    {
        

        $strResult = '';

        $propId = self::GetUserTypeDescription()['USER_TYPE']; // код типа пользовательского свойства

        if (!$propId) return $strResult;

        $iblProceduresId = intval($arProperty["USER_TYPE_SETTINGS"]["IBLOCK_ID_FROM"]);

        if (!$iblProceduresId) {
            return $strResult;
        }

		// «поднимает» сущность инфоблока по ID и возвращает объект, у которого можно получить класс ORM-сущности 
        $iblockProcedures = Iblock::wakeUp($iblProceduresId)->getEntityDataClass();

		// ORM-запрос: выбрать активные элементы, только поля ID и NAME
        $rows = $iblockProcedures::query()
            ->where('ACTIVE', 'Y')
            ->setSelect(['ID', 'NAME'])
            ->exec();

		// уникальный «суффикс» для HTML-идентификаторов	
        $inpid = md5('link_' . rand(0, 999));

		//  экранирует текущее значение свойства
        $value = htmlspecialcharsex($arValue['VALUE']);

        $strResult .= "<select id=\"select_{$propId}_{$inpid}\">";
        $strResult .= "<option value=\"\">-не установлено-</option>";

        foreach ($rows as $row) {
            $selected = explode(';', $arValue['VALUE'])[0] == $row['ID'] ? 'selected' : null;
            $strResult .= "<option value=\"{$row['ID']}\" {$selected}>[{$row['ID']}] {$row['NAME']}</option>";
        }

        $strResult .= '</select>&nbsp;';

		// Реальное значение, которое сохранится в свойство, лежит в скрытом input с именем {$strHTMLControlName["VALUE"]}
        $strResult .= "<input id=\"val_{$propId}_{$inpid}\" type=\"hidden\" name=\"{$strHTMLControlName["VALUE"]}\" value=\"{$value}\">";

        $strResult .= "
        <script type=\"text/javascript\">
        BX.bind(
            BX('select_{$propId}_{$inpid}'),
            'bxchange',
            function()
            {
                let select = BX('select_{$propId}_{$inpid}');
                let val = select.value;
                
                let option = select.selectedOptions[0];
                let text = option.text;
                
                BX('val_{$propId}_{$inpid}').value = val + ';' + text.replace(/\[\d+\]\s/g, '');
            }
        );
        </script>";

        return $strResult;
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
        \Bitrix\Main\Loader::includeModule("iblock");
    
    
    $fieldIdPrefix = preg_replace("/[^a-z0-9_]/i", "_", $strHTMLControlName["NAME"]);

    // Инициализируем значения для ОБОИХ блоков
    $settings = array(
        "IBLOCK_TYPE_ID_FROM" => "", "IBLOCK_ID_FROM" => "",
        "IBLOCK_TYPE_ID_TO" => "", "IBLOCK_ID_TO" => ""
    );

    if (isset($arProperty["USER_TYPE_SETTINGS"]) && is_array($arProperty["USER_TYPE_SETTINGS"])) {
        $settings = array_merge($settings, $arProperty["USER_TYPE_SETTINGS"]);
    }

    if ($bVarsFromForm && is_array($GLOBALS[$strHTMLControlName["NAME"]])) {
        $settings = array_merge($settings, $GLOBALS[$strHTMLControlName["NAME"]]);
    }

    // Собираем типы инфоблоков один раз, чтобы не делать лишних запросов к БД
    $arTypes = [];
    $dbIBlockType = \CIBlockType::GetList(array("SORT"=>"ASC"), array("ACTIVE"=>"Y"));
    while($arIBlockType = $dbIBlockType->Fetch()) {
        $arLang = \CIBlockType::GetByIDLang($arIBlockType["ID"], LANGUAGE_ID);
        $arTypes[$arIBlockType["ID"]] = ($arLang ? $arLang["NAME"] : $arIBlockType["ID"]);
    }

    $html = '';

    // Вспомогательная функция для генерации пары селектов внутри PHP
    $renderBlock = function($labelSuffix, $typeKey, $iblockKey) use ($strHTMLControlName, $fieldIdPrefix, $settings, $arTypes) {
        $out = '<tr><td>Тип инфоблока '.$labelSuffix.':</td><td>';
        $out .= '<select id="'.$fieldIdPrefix.'_'.$typeKey.'" name="'.$strHTMLControlName["NAME"].'['.$typeKey.']" class="ajax-iblock-type" data-target="'.$fieldIdPrefix.'_'.$iblockKey.'">';
        $out .= '<option value="">-- Выберите тип --</option>';
        foreach ($arTypes as $id => $name) {
            $selected = ($id == $settings[$typeKey]) ? ' selected' : '';
            $out .= '<option value="'.$id.'"'.$selected.'>'.$name.'</option>';
        }
        $out .= '</select></td></tr>';

        $out .= '<tr><td>Инфоблок '.$labelSuffix.':</td><td>';
        $out .= '<select id="'.$fieldIdPrefix.'_'.$iblockKey.'" name="'.$strHTMLControlName["NAME"].'['.$iblockKey.']" data-selected="'.$settings[$iblockKey].'" disabled>';
        $out .= '<option value="">-- Сначала выберите тип --</option>';
        $out .= '</select></td></tr>';
        
        return $out;
    };

    // Выводим Блок №1 и Блок №2
    $html .= $renderBlock('с услугами для бронирования', 'IBLOCK_TYPE_ID_FROM', 'IBLOCK_ID_FROM');
    $html .= '<tr><td colspan="2"><hr style="border:none; border-top:1px dashed #cedcdd;"></td></tr>'; // Разделитель
    $html .= $renderBlock('с записями брони', 'IBLOCK_TYPE_ID_TO', 'IBLOCK_ID_TO');

    // --- УНИВЕРСАЛЬНЫЙ JAVASCRIPT ДЛЯ ЛЮБОГО КОЛИЧЕСТВА БЛОКОВ ---
    $html .= '<script type="text/javascript">
    BX.ready(function() {
        function initIblockDependency(typeSelectId) {
            var typeSelect = BX(typeSelectId);
            if (!typeSelect) return;
            
            var targetId = typeSelect.getAttribute("data-target");
            var iblockSelect = BX(targetId);
            if (!iblockSelect) return;

            function loadIblocks(typeId, selectedId) {
                if (!typeId) {
                    iblockSelect.innerHTML = "<option value=\"\">-- Сначала выберите тип --</option>";
                    iblockSelect.disabled = true;
                    return;
                }

                iblockSelect.disabled = true;
                iblockSelect.innerHTML = "<option value=\"\">Загрузка...</option>";

                BX.ajax({
                    url: "/local/ajax/get_iblocks.php",
                    method: "POST",
                    data: {
                        iblock_type_id: typeId,
                        sessid: BX.bitrix_sessid()
                    },
                    dataType: "json",
                    onsuccess: function(data) {
                        iblockSelect.innerHTML = "<option value=\"\">-- Выберите инфоблок --</option>";
                        if (data && data.length > 0) {
                            data.forEach(function(item) {
                                var isSelected = (item.ID == selectedId);
                                var option = BX.create("option", {
                                    attrs: { value: item.ID, selected: isSelected },
                                    text: "[" + item.ID + "] " + item.NAME
                                });
                                iblockSelect.appendChild(option);
                            });
                            iblockSelect.disabled = false;
                        } else {
                            iblockSelect.innerHTML = "<option value=\"\">Инфоблоков не найдено</option>";
                        }
                    },
                    onfailure: function() {
                        iblockSelect.innerHTML = "<option value=\"\">Ошибка загрузки</option>";
                    }
                });
            }

            BX.bind(typeSelect, "change", function() {
                loadIblocks(this.value, "");
            });

            if (typeSelect.value) {
                var initialSelected = iblockSelect.getAttribute("data-selected");
                loadIblocks(typeSelect.value, initialSelected);
            }
        }

        // Инициализируем оба блока по их уникальным ID
        initIblockDependency("'.$fieldIdPrefix.'_IBLOCK_TYPE_ID_FROM");
        initIblockDependency("'.$fieldIdPrefix.'_IBLOCK_TYPE_ID_TO");
    });
    </script>';

    return $html;
    }

    public static function PrepareSettings($arProperty)
    {
        $settings = array(
            "IBLOCK_TYPE_ID_FROM" => "",
            "IBLOCK_ID_FROM" => 0,
            "IBLOCK_TYPE_ID_TO" => "",
            "IBLOCK_ID_TO" => 0,
        );

        if (isset($arProperty["USER_TYPE_SETTINGS"]) && is_array($arProperty["USER_TYPE_SETTINGS"])) {
            $settings["IBLOCK_TYPE_ID_FROM"] = trim($arProperty["USER_TYPE_SETTINGS"]["IBLOCK_TYPE_ID_FROM"]);
            $settings["IBLOCK_ID_FROM"] = intval($arProperty["USER_TYPE_SETTINGS"]["IBLOCK_ID_FROM"]);
            
            // Новые ключи для второго блока
            $settings["IBLOCK_TYPE_ID_TO"] = trim($arProperty["USER_TYPE_SETTINGS"]["IBLOCK_TYPE_ID_TO"]);
            $settings["IBLOCK_ID_TO"] = intval($arProperty["USER_TYPE_SETTINGS"]["IBLOCK_ID_TO"]);
        }

        return $settings;
    }

}