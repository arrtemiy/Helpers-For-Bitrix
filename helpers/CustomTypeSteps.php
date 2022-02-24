<?

class CustomTypeSteps
{

    //описываем поведение пользовательского свойства
    function GetUserTypeDescription()
    {
        return [
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'steps',
            'DESCRIPTION' => 'Шаги',
            'GetPropertyFieldHtml' => ['CustomTypeSteps', 'GetPropertyFieldHtml'],
            'ConvertToDB' => ['CustomTypeSteps', 'ConvertToDB'],
            'ConvertFromDB' => ['CustomTypeSteps', 'ConvertToDB']
        ];
    }

    //формируем поля
    function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {

        $value['DESCRIPTION'] = unserialize($value['DESCRIPTION']);

        $return = '<hr>';
        $return .= '<tr class="steps"><td>';
        $return .= '<div style="float:left; margin-right:24px;"><label for="f1">Первая дата:</label><br><input type="date" size="30" name="' . $strHTMLControlName['DESCRIPTION'] . '[DATE_1]"  id="' . $strHTMLControlName['DESCRIPTION'] . '[DATE_1]" value="' . $value['DESCRIPTION']['DATE_1'] . '"></div>';
        $return .= '<div><label for="f3">Вторая дата:</label><br><input type="date" size="30" name="' . $strHTMLControlName['DESCRIPTION'] . '[DATE_2]" id="' . $strHTMLControlName['DESCRIPTION'] . '[DATE_2]" value="' . $value['DESCRIPTION']['DATE_2'] . '" ></div><br>';
        $return .= '<div style="float:left; margin-right:24px;"><label for="f1">Время №1:</label><br><input type="text" size="20" name="' . $strHTMLControlName['DESCRIPTION'] . '[TIME_1]"  id="' . $strHTMLControlName['DESCRIPTION'] . '[TIME_1]" value="' . $value['DESCRIPTION']['TIME_1'] . '"></div>';
        $return .= '<div><label for="f3">Время №2:</label><br><input type="text" size="20" name="' . $strHTMLControlName['DESCRIPTION'] . '[TIME_2]" id="' . $strHTMLControlName['DESCRIPTION'] . '[TIME_2]" value="' . $value['DESCRIPTION']['TIME_2'] . '" ></div><br>';
        $return .= '<div><label for="f3">Вступительное слово (ID участников, через запятую):</label><br><textarea type="text" rows="1" cols="70" name="' . $strHTMLControlName['DESCRIPTION'] . '[OPENING]" id="' . $strHTMLControlName['DESCRIPTION'] . '[OPENING]" value="' . $value['DESCRIPTION']['OPENING'] . '" >' . $value['DESCRIPTION']['OPENING'] . '</textarea></div><br>';
        $return .= '<div><label for="f3">Модераторы (ID участников, через запятую):</label><br><textarea type="text" rows="1" cols="70" name="' . $strHTMLControlName['DESCRIPTION'] . '[MODERATORS]" id="' . $strHTMLControlName['DESCRIPTION'] . '[MODERATORS]" value="' . $value['DESCRIPTION']['MODERATORS'] . '" >' . $value['DESCRIPTION']['MODERATORS'] . '</textarea></div><br>';
        $return .= '<div><label for="f3">Спикеры (ID участников, через запятую):</label><br><textarea type="text" rows="1" cols="70" name="' . $strHTMLControlName['DESCRIPTION'] . '[SPEAKERS]" id="' . $strHTMLControlName['DESCRIPTION'] . '[SPEAKERS]" value="' . $value['DESCRIPTION']['SPEAKERS'] . '" >' . $value['DESCRIPTION']['SPEAKERS'] . '</textarea></div><br>';
        $return .= '<div><label for="f3">Описание*:</label><br><textarea type="text" rows="3" cols="70" name="' . $strHTMLControlName['VALUE'] . '" id="' . $strHTMLControlName['VALUE'] . '" value="' . $value['VALUE'] . '" >' . $value['VALUE'] . '</textarea></div>';
        $return .= '</td></tr>';

        return $return;
    }

    //сохраняем в базу
    function ConvertToDB($arProperty, $value)
    {
        $value['DESCRIPTION'] = serialize($value['DESCRIPTION']);
        return $value;
    }

    //читаем из базы
    function ConvertFromDB($arProperty, $value)
    {
        return $value;
    }

}

?>
