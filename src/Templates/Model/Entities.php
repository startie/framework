<?php

class Entities extends Model
{

	#
	#
	#		VARS
	#
	#
	
	public static $_ = "Entity";

	#
	#
	#		SHORTCUTS
	#
	#

	public static function select()
	{
		$params = [
			'*'
		];

		return $params;
	}

	#
	#
	#		CREATE
	#	
	#
	
	#
	#
	#		READ
	#	
	#
	
	#
	#
	#		UPDATE
	#	
	#

	public static function complete($e)
	{
		return $e;
	}
	
	#
	#
	#		DELETE
	#	
	#

}