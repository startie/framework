<?php

namespace Startie;

class Words
{
	public static function pluralCategory(int $count): string
	{
		$mod10  = $count % 10;
		$mod100 = $count % 100;

		if ($mod10 == 1 && $mod100 != 11) {
			return 'one';
		} elseif (($mod10 > 1 && $mod10 < 5) && ($mod100 < 12 || $mod100 > 14)) {
			return 'few';
		} elseif ($mod10 == 0 || ($mod10 > 4 && $mod10 < 10) || ($mod100 > 10 && $mod100 < 15)) {
			return 'many';
		} else {
			return 'other';
		}
	}

	public static function count(string $vocabPath, int $count): string
	{
		$vocab = Vocabulary::get("Words/$vocabPath");
		$pluralCategory = self::pluralCategory($count);
		return $vocab[$pluralCategory];
	}
}