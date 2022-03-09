<?php

class Language
{
    public static function code()
    {
        $LanguageCode = Cookie::get('LanguageCode');
        if(!$LanguageCode){
            $LanguageCode = $_ENV['LANGUAGE_CODE'];
        }

        return $LanguageCode;
    }
}