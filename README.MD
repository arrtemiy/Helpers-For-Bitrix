Подключение в init.php

<code>Bitrix\Main\Loader::registerAutoLoadClasses(null, [
'Queries' => '/local/php_interface/helpers/Queries.php',
'CustomTypeSteps' => '/local/php_interface/helpers/CustomTypeSteps.php',
]);

AddEventHandler('iblock', 'OnIBlockPropertyBuildList', ['CustomTypeSteps', 'GetUserTypeDescription']);</code>

# Класс Queries

Получаем несколько элементов ИБ и их свойства.

`Queries::getElements([11685, 11686, 11687, 23375], [], true);`

# Параметры методов

**Получить элемент или список элементов**

`public static function getElements($idElements = [], $arSelect = [], bool $allProperties = false): array`

№1. Параметр $idElements - массив ID элементов

№2. $arSelect - передаем массив полей. Если не передать ничего, то выберутся все поля.

№3. $allProperties - по умолчанию false, если передать true, то дополнительно вернет свойства элемента.


**Получить все данные текущей секции**

`public static function getSectAll(int $iblockId = null, bool $currentId = false): array`

**Получить ID секций**

Возвращает массив ID для текущего урла. Чтобы получить 1 текущий id - передайте вторым параметром true.

`public static function getSectId(int $iblockId = null, bool $currentId = false)`

**Получить пользовательское значение UF**

Пример

`Queries::getUfVal($arParams['IBLOCK_ID'], 155, ["UF_SECTION_DESCR", 'UF_BANNER_HAVE', 'UF_CODE_TEXT']);`

Метод

`public static function getUfVal(int $iblockId, int $idSection, $ufProperties = []): array`
