<?php 
class Main {
	private static $singleton = NULL;
	public static function get() {
		if (self::$singleton == NULL)
			self::$singleton = new Main();
		return self::$singleton;
	}

	private function __construct() {
		if (isset($_GET['site']))
			$site = $_GET['site'];
		else
			$site = "home";
	}

	public function render() {
		print "hello world";
	}

	private $site;
}
?>