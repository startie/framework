<?php

namespace Startie;

class Favicon
{
	public static function href($filename)
	{
		$href = 'href= "' . PUBLIC_URL . "favicons/" . $filename . '?v=' . Php::hash(10) . '"';
		return $href;
	}

	public static function content($filename)
	{
		$content = 'content= "' . PUBLIC_URL . "favicons/" . $filename . '?v=' . Php::hash(10) . '"';
		return $content;
	}
}
