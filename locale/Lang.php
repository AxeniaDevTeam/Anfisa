<?php

class Lang
{
    private static $availableLangs;
    private static $messageArray;   //массив сообщений из messages.php
    private static $currentLang;

    public static function availableLangs()
    {
        if (!isset(self::$availableLangs)) {
            self::$availableLangs = [
                "en" => '🇬🇧 English', 
                "ru" => '🇷🇺 Русский', 
                // "ua" => '🇺🇦 Українська', 
                // "uz" => "🇺🇿 O'zbek", 
                // "tr" => "🇹🇷 Türkçe"
            ];
        }

        return self::$availableLangs;
    }

    public static function defaultLangKey()
    {
        return "en";
    }

    /**
     * Обязательно должен вызваться
     * @param string $lang 'ru' or 'en' or etc.
     */
    public static function init($lang = "en")
    {
        self::availableLangs();
        self::loadMessages($lang);
        self::$currentLang = $lang;
    }

    public static function message($modificator, $param = NULL)
    {
        self::loadMessages(isset(self::$currentLang) ? self::$currentLang : self::defaultLangKey());

        $out = self::$messageArray[$modificator];

        return $param != NULL ? vsprintf($out, $param) : $out;
    }

    public static function loadMessages($lang)
    {
        if (!isset(self::$messageArray)) {
            self::$messageArray = include "$lang.php";
        }
    }
}
