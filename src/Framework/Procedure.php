<?php

namespace Startie;

class Procedure
{
	public static function inc($name, array $data = [])
	{
		extract($data);
		require(BACKEND_DIR . "/Procedures/" . $name . ".php");
	}
}
