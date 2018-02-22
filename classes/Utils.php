<?php
class Utils {
	private static $goodChars = "abcdefghijklmnopqrstuvwxyz/";
	public static function cleanName($name) {
		$length = strlen($name);
		$result = "";
		for ($i = 0; $i < $length; $i++) {
			if (strpos(self::$goodChars, $name[$i]) !== false)
				$result .= $name[$i];
			else
				$result .= '_';
		}
		return $result;
	}
}
?>