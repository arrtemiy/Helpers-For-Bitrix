<?php

class Strings
{
    // Замена текстовых URL и email на ссылки
    public static function replaceLinks($text)
    {
        $text = preg_replace('#(script|about|applet|activex|chrome):#is', "\\1:", $text);

        $ret = ' ' . $text;

        // Replace Links with http://
        $ret = preg_replace("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"\\2\" target=\"_blank\" rel=\"nofollow\">\\2</a>", $ret);

        // Replace Links without http://
        $ret = preg_replace("#(^|[\n ])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"http://\\2\" target=\"_blank\" rel=\"nofollow\">\\2</a>", $ret);

        // Replace Email Addresses
        $ret = preg_replace("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $ret);
        $ret = substr($ret, 1);

        return $ret;
    }

    // Пример Strings::trueWordTitle(32, 'подписчик', 'подписчика', 'подписчиков')
    public static function trueWordTitle($num, $form_for_1, $form_for_2, $form_for_5)
    {
        $num = abs($num) % 100;
        $num_x = $num % 10;
        if ($num > 10 && $num < 20)
            return $form_for_5;
        if ($num_x > 1 && $num_x < 5)
            return $form_for_2;
        if ($num_x == 1)
            return $form_for_1;
        return $form_for_5;
    }

    // Пример Strings::uriPart(0)
    public static function uriPart($part)
    {
        $uri = explode('?', $_SERVER['REQUEST_URI'], 2)[0];
        $uriNewArr = array_values(array_filter(explode('/', $uri)));

        return $uriNewArr[$part];
    }

    // Пример Strings::uriPartEnd()
    public static function uriPartEnd()
    {
        $uri = explode('?', $_SERVER['REQUEST_URI'], 2)[0];
        $uriNewArr = array_values(array_filter(explode('/', $uri)));

        return end($uriNewArr);
    }

    public static function isHttps()
    {
        return isset($_SERVER['HTTPS']) ? $protocol = 'https://' : $protocol = 'http://';
    }
    
    public static function canonical()
    {
        $isHttps = isset($_SERVER['HTTPS']) ? $protocol = 'https://' : $protocol = 'http://';
        $url = $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0];
        return "<link rel='canonical' href='. $isHttps . $url'/>";
    }

    public static function canonicalBxFilter()
    {
        $url = $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0];
        $uri = explode('?', $_SERVER['REQUEST_URI'], 2)[0];
        $uriNewArr = array_values(array_filter(explode('/', $uri)));
        if (in_array('filter', $uriNewArr)) {
            $url = preg_replace('/(filter.*)/m', '', $url);
        }
        return "<link rel=\"canonical\" href=\"$url\">";
    }

    public static function priceFormat($price)
    {
        return number_format($price, 0, '.', ' ');
    }

    // Окгруляет десятичные в большую сторону. Пример 32.782 => 32.79
    public static function roundUp($value, $precision)
    {
        $pow = pow(10, $precision);
        return (ceil($pow * $value) + ceil($pow * $value - ceil($pow * $value))) / $pow;
    }

    public static function generateUUID() {
        // Генерация UUID версии 4
        if (function_exists('com_create_guid')) {
            return trim(com_create_guid(), '{}');
        } else {
            // Если функция com_create_guid не доступна, используем альтернативный способ
            $data = openssl_random_pseudo_bytes(16);
            // Устанавливаем версию UUID (4)
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

            return vsprintf('%.8s-%.4s-%.4s-%.4s-%.12s', str_split(bin2hex($data), 4));
        }
    }
    
}