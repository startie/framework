<?php

namespace Startie;

class Language
{
    public static function code(): string
    {
        $LanguageCode = Cookie::get('LanguageCode');
        if (!$LanguageCode) {
            if (isset($_ENV['LANGUAGE_CODE'])) {
                $LanguageCode = $_ENV['LANGUAGE_CODE'];
            } else {
                throw new \Startie\Exception("LANGUAGE_CODE is missing in .env");
            }
        }

        return $LanguageCode;
    }
}
