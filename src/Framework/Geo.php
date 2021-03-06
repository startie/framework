<?php

namespace Startie;

class Geo
{
	public static function getIpInfo($ip = NULL, $purpose = "location", $deep_detect = TRUE)
	{
		$output = NULL;
		if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
			$ip = $_SERVER["REMOTE_ADDR"];
			if ($deep_detect) {
				if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
					$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
					$ip = $_SERVER['HTTP_CLIENT_IP'];
			}
		}
		$purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), "", strtolower(trim($purpose)));
		$support    = array("country", "countrycode", "state", "region", "city", "location", "address");
		$continents = array(
			"AF" => "Africa",
			"AN" => "Antarctica",
			"AS" => "Asia",
			"EU" => "Europe",
			"OC" => "Australia (Oceania)",
			"NA" => "North America",
			"SA" => "South America"
		);
		if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
			$ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
			if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
				switch ($purpose) {
					case "location":
						$output = array(
							"city"           => @$ipdat->geoplugin_city,
							"state"          => @$ipdat->geoplugin_regionName,
							"country"        => @$ipdat->geoplugin_countryName,
							"country_code"   => @$ipdat->geoplugin_countryCode,
							"continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
							"continent_code" => @$ipdat->geoplugin_continentCode
						);
						break;
					case "address":
						$address = array($ipdat->geoplugin_countryName);
						if (@strlen($ipdat->geoplugin_regionName) >= 1)
							$address[] = $ipdat->geoplugin_regionName;
						if (@strlen($ipdat->geoplugin_city) >= 1)
							$address[] = $ipdat->geoplugin_city;
						$output = implode(", ", array_reverse($address));
						break;
					case "city":
						$output = @$ipdat->geoplugin_city;
						break;
					case "state":
						$output = @$ipdat->geoplugin_regionName;
						break;
					case "region":
						$output = @$ipdat->geoplugin_regionName;
						break;
					case "country":
						$output = @$ipdat->geoplugin_countryName;
						break;
					case "countrycode":
						$output = @$ipdat->geoplugin_countryCode;
						break;
				}
			}
		}
		return $output;

		# Source: https://stackoverflow.com/questions/12553160/getting-visitors-country-from-their-ip
	}

	public static function getIP()
	{
		if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') > 0) {
				$addr = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);
				return trim($addr[0]);
			} else {
				return $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
		} else {
			return $_SERVER['REMOTE_ADDR'];
		}
	}

	public static function set($GeoIpInfo)
	{
		#
		# 	Detect settings for UI
		# 	

		#
		#	Country

		$CountryCode = $GeoIpInfo['country_code'];

		# 	Search through avaliable countries
		$Countries = Countries::$all;
		$Country = $Countries[$CountryCode];

		# 	If detected country is not avaliable ??? set default
		if (!$Country) {
			$CountryCode = $_ENV['COUNTRY_CODE'];
		}

		Cookie::set('CountryCode', $CountryCode);

		#
		#	Language

		if ($Country) {
			$LanguageCode = $Country['lang']['code'];
		} else {
			$LanguageCode = $_ENV['LANGUAGE_CODE'];
		}

		Cookie::set('LanguageCode', $LanguageCode);

		#
		#	Currency

		if ($Country) {
			$CurrencyCode = $Country['Currency']['code'];
		} else {
			$CurrencyCode = $_ENV['CURRENCY_CODE'];
		}

		Cookie::set('CurrencyCode', $CurrencyCode);
	}
}
