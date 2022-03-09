<?php

class Vocabulary
{	
	public static function get($path, $params = [])
	{
		$CurrentUserId = Auth::getIdInService('app');
        $CurrentUserId = ($CurrentUserId) ? $CurrentUserId : 0;

		$v;
		$fullPath = STORAGE_DIR . "vocabularies/{$path}.json";

		if(file_exists($fullPath)) {
			$v = json_decode(file_get_contents($fullPath), true);

			if ($v === null && json_last_error() !== JSON_ERROR_NONE) {
 			  throw new Exception("Json data on $fullPath incorrect");
			}

			# Arrifying
			if(isset($params['arrify'])){
				if($params['arrify'] == 1){
					$vArrfied;
					foreach ($v as $i => $value) {
						$x = $i-1;
						$vArrfied[$x]['id'] = $i;
						$vArrfied[$x]['name'] = Php::mb_ucfirst($value);
					}
					return $vArrfied;
				}
			}
			# /Arrifying

			return $v;
		} else {

			throw new Exception("Vocabulary on $fullPath doesn't exists");

			AppLogs::create([
	            'insert' => [
	           		['createdAt', '`UTC_TIMESTAMP()`'],
	            	['UserId', $CurrentUserId, 'INT'],
	            	['line', 0, 'INT'],
	            	['file', 'Vocabulary::get()'],
	            	['message',  'File doesnt exsists: ' . $fullPath],
	            	['type', 'errors'],
	            	['object', 'php'],
	            ]
	        ]);
		}
	}

	public static function getFolder($path)
	{
		$dirPath = STORAGE_DIR . "vocabularies/$path";
		$vocabularyNames = scandir($dirPath);
		array_splice($vocabularyNames, 0, 2);
		
		$arr;
		foreach ($vocabularyNames as $vocabularyName) {
			$arr[intval($vocabularyName)] = json_decode(file_get_contents(STORAGE_DIR . 'vocabularies/' . $path . '/' . $vocabularyName), true);
		}
		return $arr;
	}

	public $collection;

	function __construct($name, $lang){
		if(!$lang){
			$lang = Cookie::get('LanguageCode');
		}
		$this->collection = json_decode(file_get_contents(STORAGE_DIR . "vocabularies/$name.json"), true);
	}
	
	public static function collect ($arr)
	{
		$vArr;
		foreach ($arr as $v) {
			$vArr[] = Vocabulary::get("$v");
		}
		return call_user_func_array('array_merge', $vArr);
	}
}