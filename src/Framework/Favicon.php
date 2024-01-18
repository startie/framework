<?php
declare(strict_types=1);

namespace Startie;

use Startie\Php;
use Startie\Asseter;

class Favicon
{
	public static string $hash = "";

	private static function getHash(): string
	{
		if(self::$hash === ""){
			self::$hash = Php::hash(10);
		} 

		return self::$hash;
	}

	public static function href(string $filename): string
	{	
		$hash = self::getHash();
		$root = Asseter::getRootUrl();

		$href = "href='{$root}favicons/{$filename}?v={$hash}'";
		
		return $href;
	}

	public static function content(string $filename): string
	{
		$hash = self::getHash();
		$root = Asseter::getRootUrl();

		$content = "content='{$root}favicons/{$filename}?v={$hash}'";
		
		return $content;
	}
}
