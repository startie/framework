<?php

declare(strict_types=1);

namespace Startie;

class Dropzone
{
	/**
	 * Takes an entity, adds some data-params to it for requests,
	 * then it takes images of this entity and also adds  some data-params to them.
	 * 
	 * TODO: create `::initParams()` like `::initImages()`
	 */
	public static function init(
		array $e,
		string $class,
		array $params,
		int $imagesIndex,
		array $imagesParams
	): array {
		$e['Dropzone']['class'] = $class;

		$e['Dropzone']['params'] = [];
		$paramsStr = "";
		foreach ($params as $param) {
			$dataName = strtolower($param[0]);
			$dataValue = $e[$param[1]];
			$paramsStr .= "data-$dataName='$dataValue'";
			// spacing between data params
			$paramsStr .= " ";
		}
		$e['Dropzone']['params'] = $paramsStr;

		$e[$imagesIndex] = self::initImages($e[$imagesIndex], $imagesParams);

		return $e;
	}

	public static function initImages(
		array $images,
		array $params
	): array {
		foreach ($images as &$image) {
			$paramsStr = "";
			foreach ($params as $param) {
				$dataName = strtolower($param[0]);
				$dataValue = $image[$param[1]];
				$paramsStr .= "data-$dataName='$dataValue'";
				// spacing between data params
				$paramsStr .= " ";
			}
			$image['Dropzone']['params'] = $paramsStr;
		}

		return $images;
	}
}