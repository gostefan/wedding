<?php 
class Main {
	private static $singleton = NULL;
	public static function get() {
		if (self::$singleton == NULL)
			self::$singleton = new Main();
		return self::$singleton;
	}

	private function __construct() {
		$this->site = $this->getSite();
	}

	private function getSite() {
		if (isset($_GET['site']))
			$siteName = $_GET['site'];
		else
			$siteName = "home";
		$siteManager = new SiteManager();
		return $siteManager->getSite($siteName);
	}

	public function render() {
		if ($this->site->hasHeader()) {
			$header = new Header();
			$header->renderHeader();
		}
		if ($this->site->hasMenu()) {
			$menu = new MenuManager();
			$menu->render();
		}
		$this->site->render();
		if ($this->site->hasFooter())
			$header->renderFooter();
	}

	private $site;
}
?>