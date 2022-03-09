<?php

class Svg
{
	public static function p($url)
	{
		$fileUrl = PUBLIC_URL . $url . '.svg';

		#1

		if(file_exists($file)){
			$file = file_get_contents($fileUrl);
			echo $file;
		} else {
			echo "no file on $fileUrl";
		}

		#2
		
		// $file = $fileUrl;
		// $ch = curl_init();
		// curl_setopt($ch, CURLOPT_URL, $file);
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// curl_setopt($ch, CURLOPT_REFERER, $_SERVER['REQUEST_URI']);
		// $svg = curl_exec($ch);
		// curl_close($ch);

		// echo $svg;
		
	}

	public static function html($url)
	{
		$fileUrl = PUBLIC_URL . $url . '.svg';

		echo "<image src='{$fileUrl}'>";
	}
}