<?php
/**
 * Created by PhpStorm
 * User: Artem Zinatullin
 * Date: 16.01.2022 20:28
 */

class Queries
{
    public static $life_time = 86400 * 7;
    public static $cache_path = 'allElements';

    // Получить элемент или список элементов. Параметр $idElements - массив ID элементов,
    // $arSelect - передаем массив полей. Если не передать ничего, то выберутся все поля.
    // $allProperties - по умолчанию false, если передать true, то дополнительно вернет свойства элемента.
    // Пример вызова Queries::getElements([11685, 11686], ['ID', 'PROPERTY_NAME_PROP'], true);
    public static function getElements($idElements = [], $arSelect = [], $allProperties = false)
    {
        CModule::IncludeModule('iblock');

        $cache = new CPHPCache();
        $cache_id = $idElements[0] . '_' . +count($idElements) . $_SERVER['REQUEST_URI'] . '_elems';
        self::$cache_path = 'getElements';

        if (self::$life_time > 0 && $cache->InitCache(self::$life_time, $cache_id, self::$cache_path)) {
            $res = $cache->GetVars();
            if (is_array($res["data"]) && (count($res["data"]) > 0))
                return $res["data"];
        }
        $arReturn = [];
        $arFilter = ['ID' => $idElements, 'ACTIVE' => 'Y'];

        if (!empty($arSelect))
            array_unshift($arSelect, 'ID', 'IBLOCK_ID', 'CODE', 'NAME', 'DETAIL_PAGE_URL');

        $rs = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);

        $i = 0;
        while ($ob = $rs->GetNextElement()) {
            $arReturn[$i] = $ob->GetFields();

            if ($allProperties === true) {
                $arReturn[$i]['PROPERTIES'] = $ob->GetProperties();
            }
            $i += 1;
        }

        $cache->StartDataCache(self::$life_time, $cache_id, self::$cache_path);
        $cache->EndDataCache(["data" => $arReturn]);

        return $arReturn;
    }

    // Пример Queries::getAllElements([22], ['ID'], true);
    public static function getAllElements($idBlock = [], $arSelect = [], $allProperties = false)
    {
        CModule::IncludeModule('iblock');

        $cache = new CPHPCache();
        $cache_id = $idBlock[0] . '_' . +count($idBlock) . $_SERVER['REQUEST_URI'] . '_allElems';
        self::$cache_path = 'getAllElements';

        if (self::$life_time > 0 && $cache->InitCache(self::$life_time, $cache_id, self::$cache_path)) {
            $res = $cache->GetVars();
            if (is_array($res["data"]) && (count($res["data"]) > 0))
                return $res["data"];
        }
        $arReturn = [];
        $arFilter = ['IBLOCK_ID' => $idBlock, 'ACTIVE' => 'Y'];

        if (!empty($arSelect))
            array_unshift($arSelect, 'ID', 'IBLOCK_ID', 'CODE', 'NAME', 'DETAIL_PAGE_URL');

        $rs = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);

        $i = 0;
        while ($ob = $rs->GetNextElement()) {
            $arReturn[$i] = $ob->GetFields();

            if ($allProperties === true) {
                $arReturn[$i]['PROPERTIES'] = $ob->GetProperties();
            }
            $i += 1;
        }

        $cache->StartDataCache(self::$life_time, $cache_id, self::$cache_path);
        $cache->EndDataCache(["data" => $arReturn]);

        return $arReturn;
    }

    // Пример Queries::getAllElementsSection([22], 221, ['ID'], true);
    public static function getAllElementsSection($idBlock = [], $idSection, $arSelect = [], $allProperties = false)
    {
        CModule::IncludeModule('iblock');

        $cache = new CPHPCache();
        $cache_id = $idBlock[0] . '_' . $idSection . $_SERVER['REQUEST_URI'] . '_allElemsSection';
        self::$cache_path = 'getAllElementsSection';

        if (self::$life_time > 0 && $cache->InitCache(self::$life_time, $cache_id, self::$cache_path)) {
            $res = $cache->GetVars();
            if (is_array($res["data"]) && (count($res["data"]) > 0))
                return $res["data"];
        }
        $arReturn = [];
        $arFilter = ['IBLOCK_ID' => $idBlock, 'ACTIVE' => 'Y', 'SECTION_ID' => $idSection];

        if (!empty($arSelect))
            array_unshift($arSelect, 'ID', 'IBLOCK_ID', 'CODE', 'NAME', 'DETAIL_PAGE_URL');

        $rs = CIBlockElement::GetList(['SORT' => 'ASC'], $arFilter, false, false, $arSelect);

        $i = 0;
        while ($ob = $rs->GetNextElement()) {
            $arReturn[$i] = $ob->GetFields();

            if ($allProperties === true) {
                $arReturn[$i]['PROPERTIES'] = $ob->GetProperties();
            }
            $i += 1;
        }

        $cache->StartDataCache(self::$life_time, $cache_id, self::$cache_path);
        $cache->EndDataCache(["data" => $arReturn]);

        return $arReturn;
    }

    // Получить все данные текущей секции
    // Пример Queries::getSectAll(null, true);
    public static function getSectAll($iBlockId = null, $currentId = false)
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
        $arFilter = ['IBLOCK_ID' => $iBlockId, 'ACTIVE' => 'Y', 'CODE' => end($slugArr)];
        $arSectionData = [];

        $rs = CIBlockSection::GetList([], $arFilter, false, false, []);
        $arSectionData[] = $rs->Fetch();

        $cache->StartDataCache(self::$life_time, $cache_id, self::$cache_path);
        $cache->EndDataCache(["data" => $arSectionData]);

        return $arSectionData;
    }

    // Получить ID секций. Возвращает массив ID для текущего урла. Чтобы получить 1 текущий id - передайте вторым параметром true.
    // Пример Queries::getSectId(null, true);
    public static function getSectId($iBlockId = null, $currentId = false)
    {
        global $APPLICATION;
        CModule::IncludeModule('iblock');

        $slugArr = array_filter(explode('/', $APPLICATION->GetCurPage()));
        $arFilter = ['IBLOCK_ID' => $iBlockId, 'ACTIVE' => 'Y'];
        $arSelect = ['ID'];
        $arSectionId = [];

        foreach ($slugArr as $item) {
            $arFilter['CODE'] = $item;
            $rs = CIBlockSection::GetList([], $arFilter, false, false, $arSelect);
            $arSectionId[] = $rs->Fetch()['ID'];
        }

        if ($currentId === true) {
            return (int)end($arSectionId);
        } else {
            return $arSectionId;
        }
    }

    // Получить все ID секций ИБ
    // TODO: Добавить кеширование
    public static function getSectionsIB($iBlockId)
    {
        $sectId = [];
        $rsParentSection = CIBlockSection::GetList(
            ['NAME' => 'ASC'],
            ['IBLOCK_ID' => $iBlockId, 'ACTIVE' => 'Y']
        );
        while ($arParentSection = $rsParentSection->GetNext()) {
            $sectId[] = $arParentSection['ID'];
        }

        return $sectId;
    }

    // Получить несколько разделов
    // Пример Queries::getSections($arParams['IBLOCK_ID'], [155, 156], ["UF_SECTION_DESCR", 'UF_BANNER_HAVE', 'UF_CODE_TEXT']);
    public static function getSections($iBlockId, $idSections = [], $ufProperties = [])
    {
        $cache = new CPHPCache();
        $cache_id = $_SERVER['REQUEST_URI'] . $idSections[0];
        self::$cache_path = 'getSections';

        if (self::$life_time > 0 && $cache->InitCache(self::$life_time, $cache_id, self::$cache_path)) {
            $res = $cache->GetVars();
            if (is_array($res["data"]) && (count($res["data"]) > 0))
                return $res["data"];
        }

        $sectValues = [];
        $arFilter = ['IBLOCK_ID' => $iBlockId, 'ID' => $idSections, 'ACTIVE' => 'Y'];
        $rs = CIBlockSection::GetList([], $arFilter, false, $ufProperties);
        $i = 0;

        while ($data = $rs->GetNext()) {
            $sectValues[$i] = $data;
            foreach ($ufProperties as $ufProperty) {
                $sectValues[$i][$ufProperty] = $data[$ufProperty];
            }
            $i += 1;
        }

        $cache->StartDataCache(self::$life_time, $cache_id, self::$cache_path);
        $cache->EndDataCache(["data" => $sectValues]);

        return $sectValues;
    }

    // Получить раздел
    // Пример Queries::getSection($arParams['IBLOCK_ID'], 155, ["UF_SECTION_DESCR", 'UF_BANNER_HAVE', 'UF_CODE_TEXT']);
    public static function getSection($iBlockId, $idSection, $ufProperties = [])
    {
        $cache = new CPHPCache();
        $cache_id = $_SERVER['REQUEST_URI'] . $idSection;
        self::$cache_path = 'getSection';

        if (self::$life_time > 0 && $cache->InitCache(self::$life_time, $cache_id, self::$cache_path)) {
            $res = $cache->GetVars();
            if (is_array($res["data"]) && (count($res["data"]) > 0))
                return $res["data"];
        }

        $sectValues = [];
        $arFilter = ['IBLOCK_ID' => $iBlockId, 'ID' => $idSection, 'ACTIVE' => 'Y'];
        $rs = CIBlockSection::GetList([], $arFilter, false, $ufProperties);
        $i = 0;

        while ($data = $rs->GetNext()) {
            $sectValues[$i] = $data;
            foreach ($ufProperties as $ufProperty) {
                $sectValues[$i][$ufProperty] = $data[$ufProperty];
            }
            $i += 1;
        }

        $cache->StartDataCache(self::$life_time, $cache_id, self::$cache_path);
        $cache->EndDataCache(["data" => $sectValues]);

        return $sectValues;
    }

    // Получить пользовательское значение UF
    // Пример Queries::getUfVal($arParams['IBLOCK_ID'], 155, ["UF_SECTION_DESCR", 'UF_BANNER_HAVE', 'UF_CODE_TEXT']);
    public static function getUfVal($iBlockId, $idSection, $ufProperties = [])
    {
        CModule::IncludeModule('iblock');
        $cache = new CPHPCache();
        $cache_id = $iBlockId . $idSection . $ufProperties[0] . $_SERVER['REQUEST_URI'];
        self::$cache_path = 'getUfVal';

        if (self::$life_time > 0 && $cache->InitCache(self::$life_time, $cache_id, self::$cache_path)) {
            $res = $cache->GetVars();
            if (is_array($res["data"]) && (count($res["data"]) > 0))
                return $res["data"];
        }

        $ufValues = [];
        $arFilter = ['IBLOCK_ID' => $iBlockId, 'ID' => $idSection, 'ACTIVE' => 'Y'];
        $rs = CIBlockSection::GetList([], $arFilter, false, $ufProperties);

        while ($data = $rs->GetNext()) {
            foreach ($ufProperties as $ufProperty) {
                $ufValues[$ufProperty] = $data[$ufProperty];
            }
        }

        $cache->StartDataCache(self::$life_time, $cache_id, self::$cache_path);
        $cache->EndDataCache(["data" => $ufValues]);

        return $ufValues;
    }

}