<?php

namespace Startie;

class Favicon
{
	public static function href($filename)
	{
		$href = 'href= "' . PUBLIC_FAVICONS_URL . $filename . '?v=' . Php::hash(10) . '"';
		return $href;
	}

	public static function content($filename)
	{
		$content = 'content= "' . PUBLIC_FAVICONS_URL . $filename . '?v=' . Php::hash(10) . '"';
		return $content;
	}
}
