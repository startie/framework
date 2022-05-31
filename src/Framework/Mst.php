<?php

namespace Startie;

class Mst
{
	public static function ify($arr)
	{
		if (is_array($arr)) {
			if (count($arr)) {
				# First
				$arr[0]['first'] = 1;
				$arr[0]['isFirst'] = 1;

				# Last
				$arr[count($arr) - 1]['last'] = 1;
				$arr[count($arr) - 1]['isLast'] = 1;
			}
		}

		return $arr;
	}
}
