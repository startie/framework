<?php

namespace Startie;

class Language
{
    public static function code(): string
    {
        $code = Cookie::get('LanguageCode');
        if (!$code) {
            if (isset($_ENV['LANGUAGE_CODE'])) {
                $code = $_ENV['LANGUAGE_CODE'];
            } else {
                throw new \Startie\Exception("LANGUAGE_CODE is missing in .env");
            }
        }

        return $code;
    }
}