<?php

namespace Startie;

use InvalidArgumentException;

class File
{
	public static function downloadFromUrl(string $url): string
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$data = curl_exec($ch);
		if (is_bool($data)) {
			$data = "";
		}

		curl_close($ch);

		return $data;
	}

	public static function saveFromUrl(string $url, string $path): void
	{
		// Create path if it doesn't exist
		$parts = explode('/', $path);
		array_pop($parts);
		$dir = '';
		foreach ($parts as $part)
			if (!is_dir($dir .= "/$part")) mkdir($dir);

		// Download
		$data = self::downloadFromUrl($url);

		// Store
		file_put_contents($path, $data);
	}

	function initDirPath(string $desiredPath): void
	{
		if (is_dir($desiredPath)) {
			throw new Exception("Path already exists.");
		}

		$parts = explode('/', $desiredPath);

		$currentPath = '';

		foreach ($parts as $part) {
			$currentPath .= "/$part";
			if (!is_dir($currentPath)) {
				mkdir($currentPath);
			}
		}
	}

	/**
	 * Return boolean depending on success
	 */
	public static function saveFromPath(string $path1, string $path2): bool
	{
		// Create path if it doesn't exist
		$parts = explode('/', $path2);
		array_pop($parts);
		$dir = '';
		foreach ($parts as $part) {
			if (!is_dir($dir .= "/$part")) {
				mkdir($dir);
			};
		};

		// Store
		return move_uploaded_file($path1, $path2);
	}

	public static function saveFromSsh(
		string $ip,
		string $userName,
		string $pwd,
		string $imagePath,
		string $path
	): void {
		$connection = ssh2_connect($ip);
		ssh2_auth_password($connection, $userName, $pwd);

		$sftp = ssh2_sftp($connection);
		$sftp_fd = intval($sftp);

		$path = "ssh2.sftp://{$sftp_fd}/{$imagePath}";

		$remote = fopen($path, 'r');
		$local = fopen($path, 'w');

		$read = 0;
		$filesize = filesize($path);

		while ($read < $filesize && ($buffer = fread($remote, $filesize - $read))) {
			$read += strlen($buffer);
			if (fwrite($local, $buffer) === false) {
				echo "Unable to write to local file: $path \n";
				break;
			}
		}
		fclose($local);
		fclose($remote);
	}

	public static function getFolders(
		string $path,
		array $exclude = ['..', '.', '.git', '_']
	): array {
		$files = array_diff(scandir($path), $exclude);
		$dirs = [];

		foreach ($files as $file) {
			if (pathinfo($file, PATHINFO_EXTENSION) == "") {
				$dirs[] = $file;
			}
		}

		return $dirs;
	}

	public static function getPermissions(string $path): string
	{
		$perms = fileperms($path);

		switch ($perms & 0xF000) {
			case 0xC000: // сокет
				$info = 's';
				break;
			case 0xA000: // символическая ссылка
				$info = 'l';
				break;
			case 0x8000: // обычный
				$info = 'r';
				break;
			case 0x6000: // файл блочного устройства
				$info = 'b';
				break;
			case 0x4000: // каталог
				$info = 'd';
				break;
			case 0x2000: // файл символьного устройства
				$info = 'c';
				break;
			case 0x1000: // FIFO канал
				$info = 'p';
				break;
			default: // неизвестный
				$info = 'u';
		}

		# Владелец
		$info .= (($perms & 0x0100) ? 'r' : '-');
		$info .= (($perms & 0x0080) ? 'w' : '-');
		$info .= (($perms & 0x0040) ?
			(($perms & 0x0800) ? 's' : 'x') : (($perms & 0x0800) ? 'S' : '-'));

		# Группа
		$info .= (($perms & 0x0020) ? 'r' : '-');
		$info .= (($perms & 0x0010) ? 'w' : '-');
		$info .= (($perms & 0x0008) ?
			(($perms & 0x0400) ? 's' : 'x') : (($perms & 0x0400) ? 'S' : '-'));

		# Мир
		$info .= (($perms & 0x0004) ? 'r' : '-');
		$info .= (($perms & 0x0002) ? 'w' : '-');
		$info .= (($perms & 0x0001) ?
			(($perms & 0x0200) ? 't' : 'x') : (($perms & 0x0200) ? 'T' : '-'));

		return $info;
	}

	/**
	 * Deletes directory on path and all inside it
	 */
	public static function deleteDir(string $path): void
	{
		if (!is_dir($path)) {
			throw new InvalidArgumentException("$path must be a directory");
		}
		if (substr($path, strlen($path) - 1, 1) != '/') {
			$path .= '/';
		}
		$files = glob($path . '*', GLOB_MARK);
		foreach ($files as $file) {
			if (is_dir($file)) {
				self::deleteDir($file);
			} else {
				unlink($file);
			}
		}
		rmdir($path);
	}

	/**
	 * Creates temporary file from response body
	 * 
	 * @return string abosolute file path to that file
	 */
	public static function createTempFromString(string $data): string
	{
		// Создать временный файл
		$temporaryFile = tmpfile();
		$meta = stream_get_meta_data($temporaryFile);
		$temporaryFilePath = $meta['uri'];

		// Сохранить скачанный как временный файл
		file_put_contents($temporaryFilePath, $data);

		return $temporaryFilePath;
	}
}