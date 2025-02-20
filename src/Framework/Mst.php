<?php

namespace Startie;

class Mst
{
	public static function ify(array $array): array
	{
		if (count($array)) {
			$array[0]['first'] = 1;
			$array[0]['isFirst'] = 1;

			$array[count($array) - 1]['last'] = 1;
			$array[count($array) - 1]['isLast'] = 1;
		}

		return $array;
	}
}