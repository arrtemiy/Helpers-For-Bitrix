<?php
/**
 * Created by PhpStorm
 * User: Artem Zinatullin
 * Site: https://dev-tutorials.ru
 * Date: 16.01.2022 20:28
 */

class Queries
{
    public static int $life_time = 86400 * 7;
    public static string $cache_path = 'allElements';

    // Получить элемент или список элементов. Параметр $idElements - массив ID элементов,
    // $arSelect - передаем массив полей. Если не передать ничего, то выберутся все поля.
    // $allProperties - по умолчанию false, если передать true, то дополнительно вернет свойства элемента.
    // Пример вызова Queries::getElements(array(11685, 11686), array('ID', 'PROPERTY_NAME_PROP'), true);
    public static function getElements($idElements = array(), $arSelect = array(), bool $allProperties = false): array
    {
        CModule::IncludeModule('iblock');

        $cache = new CPHPCache();
        $cache_id = $idElements[0] . '_' . +count($idElements) . '_elems';
        self::$cache_path = 'getElements';

        if (self::$life_time > 0 && $cache->InitCache(self::$life_time, $cache_id, self::$cache_path)) {
            $res = $cache->GetVars();
            if (is_array($res["data"]) && (count($res["data"]) > 0))
                return $res["data"];
        }
        $arReturn = array();
        $arFilter = array('ID' => $idElements, 'ACTIVE' => 'Y');

        if (!empty($arSelect))
            array_unshift($arSelect, 'ID', 'IBLOCK_ID', 'CODE', 'NAME', 'DETAIL_PAGE_URL');

        $rs = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);

        $i = 0;
        while ($ob = $rs->GetNextElement()) {
            $arReturn[$i] = $ob->GetFields();

            if ($allProperties === true) {
                $arReturn[$i]['PROPERTIES'] = $ob->GetProperties();
            }
            $i += 1;
        }

        $cache->StartDataCache(self::$life_time, $cache_id, self::$cache_path);
        $cache->EndDataCache(array("data" => $arReturn));

        return $arReturn;
    }

    // Получить все данные текущей секции
    public static function getSectAll(int $iblockId = null, bool $currentId = false): array
    {
        global $APPLICATION;
        $cache = new CPHPCache();
        $cache_id = $APPLICATION->GetCurPage();
        self::$cache_path = 'getSectAll';

        if (self::$life_time > 0 && $cache->InitCache(self::$life_time, $cache_id, self::$cache_path)) {
            $res = $cache->GetVars();
            if (is_array($res["data"]) && (count($res["data"]) > 0))
                return $res["data"];
        }

        CModule::IncludeModule('iblock');

        $slugArr = array_filter(explode('/', $APPLICATION->GetCurPage()));
        $arFilter = array('IBLOCK_ID' => $iblockId, 'ACTIVE' => 'Y', 'CODE' => end($slugArr));
        $arSectionData = array();

        $rs = CIBlockSection::GetList(array(), $arFilter, false, false, array());
        $arSectionData[] = $rs->Fetch();

        $cache->StartDataCache(self::$life_time, $cache_id, self::$cache_path);
        $cache->EndDataCache(array("data" => $arSectionData));

        return $arSectionData;
    }

    // Получить ID секций. Возвращает массив ID для текущего урла. Чтобы получить 1 текущий id - передайте вторым параметром true.
    public static function getSectId(int $iblockId = null, bool $currentId = false)
    {
        global $APPLICATION;
        CModule::IncludeModule('iblock');

        $slugArr = array_filter(explode('/', $APPLICATION->GetCurPage()));
        $arFilter = array('IBLOCK_ID' => $iblockId, 'ACTIVE' => 'Y');
        $arSelect = array('ID');
        $arSectionId = array();

        foreach ($slugArr as $item) {
            $arFilter['CODE'] = $item;
            $rs = CIBlockSection::GetList(array(), $arFilter, false, false, $arSelect);
            $arSectionId[] = $rs->Fetch()['ID'];
        }

        if ($currentId === true) {
            return (int)end($arSectionId);
        } else {
            return $arSectionId;
        }
    }

    // Получить пользовательское значение UF
    // Пример Queries::getUfVal($arParams['IBLOCK_ID'], 155, array("UF_SECTION_DESCR", 'UF_BANNER_HAVE', 'UF_CODE_TEXT'));
    public static function getUfVal(int $iblockId, int $idSection, $ufProperties = array()): array
    {
        $cache = new CPHPCache();
        $cache_id = $idSection;
        self::$cache_path = 'getUfVal';

        if (self::$life_time > 0 && $cache->InitCache(self::$life_time, $cache_id, self::$cache_path)) {
            $res = $cache->GetVars();
            if (is_array($res["data"]) && (count($res["data"]) > 0))
                return $res["data"];
        }

        $ufValues = array();
        $arFilter = array('IBLOCK_ID' => $iblockId, 'ID' => $idSection, 'ACTIVE' => 'Y');
        $rs = CIBlockSection::GetList(array(), $arFilter, false, $ufProperties);

        while ($data = $rs->GetNext()) {
            foreach ($ufProperties as $ufProperty) {
                $ufValues[$ufProperty] = $data[$ufProperty];
            }
        }

        $cache->StartDataCache(self::$life_time, $cache_id, self::$cache_path);
        $cache->EndDataCache(array("data" => $ufValues));

        return $ufValues;
    }

}