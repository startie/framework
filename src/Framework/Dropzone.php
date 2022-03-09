<?php

class Dropzone
{
	#
	#	it takes entity, add to it some data-params
	#	then it takes images of this entity and add some data-params to them
	#
	
	public static function init(
		$e,
		$class,
		$params,
		$imagesIndex,
		$imagesParams
	)
	{
		#	class
		
		$e['Dropzone']['class'] = $class;

		# 	dropzone params
		
		$e['Dropzone']['params'] = [];
		$paramsStr = "";
		foreach ($params as $param) {
			$dataName = strtolower($param[0]);
			$dataValue = $e[$param[1]];
			$paramsStr .= "data-$dataName='$dataValue'";
			$paramsStr .= " "; # spacing between data params
		}
		$e['Dropzone']['params'] = $paramsStr;

		# 	images params
		
		$e[$imagesIndex] = self::initImages($e[$imagesIndex], $imagesParams);
	
		#
		
		return $e;
	}

	public static function initImages(
		$images,
		$params
	)
	{
		foreach ($images as &$image) {
			$paramsStr = "";
			foreach ($params as $param) {
				$dataName = strtolower($param[0]);
				$dataValue = $image[$param[1]];
				$paramsStr .= "data-$dataName='$dataValue'";
				$paramsStr .= " "; # spacing between data params
			}
			$image['Dropzone']['params'] = $paramsStr;
		}

		return $images;
	}
}