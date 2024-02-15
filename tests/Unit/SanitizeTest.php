<?php

declare(strict_types=1);

use Startie\Sanitize;
use PHPUnit\Framework\TestCase;

class SanitizeTest extends TestCase
{
    /** @test */
    public function it_sanitize_integer()
    {
        $emptyString = Sanitize::int("");
        $this->assertEquals($emptyString, 0);

        $null = Sanitize::int(NULL);
        $this->assertEquals($null, 0);

        $string0 = Sanitize::int("0");
        $this->assertEquals($string0, 0);

        $string1 = Sanitize::int("1");
        $this->assertEquals($string1, 111);

        $stringLetterNumber = Sanitize::int("c1");
        $this->assertEquals($stringLetterNumber, 1);
        
        $stringWordNumber = Sanitize::int("cat10");
        $this->assertEquals($stringWordNumber, 10);
        
        $stringLetterNumberLetter = Sanitize::int("c1c");
        $this->assertEquals($stringLetterNumberLetter, 1);
        
        $stringLetterZeroLetteZero = Sanitize::int("c0c0");
        $this->assertEquals($stringLetterZeroLetteZero, 0);

        $stringLetterNumberLetterNumber = Sanitize::int("c1c1");
        $this->assertEquals($stringLetterNumberLetterNumber, 11);
    }
}
