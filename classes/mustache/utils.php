<?php defined('SYSPATH') or die('No direct script access.');

class Mustache_Utils
{
	public static function normalize($result)
	{
		$a = array();
		foreach ($result->as_array() as $obj)
		{
			$a[] = $obj->normalize();
		}
		return $a;
	}
}