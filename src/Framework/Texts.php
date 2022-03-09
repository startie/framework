<?php

class Texts
{
	public static function get($vocabPath, $phrase, $params=[])
	{
		$vocab = Vocabulary::get("Texts/$vocabPath");
		$phrase = $vocab[$phrase];

		if(!empty($params)){
			$paramsNew;
			foreach ($params as $i => $param) {
				$paramsNew['{{'.$i.'}}'] = $param;
			}

			foreach ($paramsNew as $i => &$param) {
				$phrase = str_replace($i, $param, $phrase);
			}
		}
		
		return $phrase;
	}
}