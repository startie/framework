<?php

namespace Startie;

class Procedure
{
	public static function inc($name, array $data = [])
	{
		extract($data);
		require(BACKEND_PROCEDURES_DIR . $name . ".php");
	}
}
