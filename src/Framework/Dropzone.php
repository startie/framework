<?php

namespace Startie;

class Dropzone
{
	#
	#	It takes entity, adds to it some data-params for requests,
	#	then it takes images of this entity and also adds  some data-params to them.
	#

	public static function init(
		$e,
		$class,
		$params,
		$imagesIndex,
		$imagesParams
	) {
		#	(1) $e

		#	(2) class

		$e['Dropzone']['class'] = $class;

		# 	(3) dropzone params

		$e['Dropzone']['params'] = [];
		$paramsStr = "";
		foreach ($params as $param) {
			$dataName = strtolower($param[0]);
			$dataValue = $e[$param[1]];
			$paramsStr .= "data-$dataName='$dataValue'";
			$paramsStr .= " "; # spacing between data params
		}
		$e['Dropzone']['params'] = $paramsStr;

		# 	(4, 5) images params

		$e[$imagesIndex] = self::initImages($e[$imagesIndex], $imagesParams);

		# 	return

		return $e;
	}

	public static function initImages(
		$images,
		$params
	) {
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