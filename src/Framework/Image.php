<?php

namespace Startie;

class Image
{
	/**
	 * @deprecated 0.46.0
	 */
	public static function convertImage(
		string $originalImage,
		null|string $outputImage,
		int $quality = 100
	): int {
		// Check jpg, png, gif or bmp
		$exploded = explode('.', $originalImage);
		$ext = $exploded[count($exploded) - 1];

		if (preg_match('/jpg|jpeg/i', $ext)) {
			$imageTmp = imagecreatefromjpeg($originalImage);
		} elseif (preg_match('/png/i', $ext)) {
			$imageTmp = imagecreatefrompng($originalImage);
		} elseif (preg_match('/gif/i', $ext)) {
			$imageTmp = imagecreatefromgif($originalImage);
		} elseif (preg_match('/bmp/i', $ext)) {
			$imageTmp = imagecreatefrombmp($originalImage);
		} else {
			return 0;
		}

		# quality is a value from 0 (worst) to 100 (best)
		imagejpeg($imageTmp, $outputImage, $quality);
		imagedestroy($imageTmp);

		return 1;
	}
}