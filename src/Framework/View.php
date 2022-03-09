<?php

class View
{
	public static $pageTitle;
	
	public static function title($title){
		self::$pageTitle = $title;
	}

	public static function titleChange($title){
		echo "<script>document.title = \"$title\";</script>";
	}

	public static function getDir($path)
	{
		preg_match('/([A-z]*)\/([A-z]*)\.php/', $path, $matches);
		return $matches[1];
	}

	public static function getFile($path)
	{
		preg_match('/([A-z]*)\/([A-z]*)\.php/', $path, $matches);
		return $matches[2];
	}
	
	public static function render($path, array $data = [])
	{
		extract($data);
		$full_path = BACKEND_DIR . "Views/" . $path . ".php";
		require($full_path);
	}

	public static function r($path, array $data = [])
	{
	    $path = BACKEND_DIR . "Views/" . $path . ".php";
	    if (is_file($path)) {
	        ob_start();
	        extract($data);
	        require($path);
	        $content = ob_get_contents();
	        ob_end_clean();
	    } else {
	        throw new RuntimeException(sprintf('Cant find view file %s!', $path));
	    }

	    return $content;
	}

	public static function headline($add = "", $text = null, $level = 3)
	{
		if(!$text){
			echo "<h{$level}><b>" . self::$pageTitle . "{$add}</b></h{$level}>"; 
		} else {
			echo "<h{$level}><b>" .      $add        . "</b></h{$level}>"; 
		}
		echo "<br>";
	}

	public static function noEntitiesMessage($text){
		echo "<br><br>";
      	echo "<div class='text-center text-gray-500'>$text</div>";
	}

}