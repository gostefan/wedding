<?php 
class SiteManager {
	public function getSite($siteName) {
		$cleanName = Utils::cleanName($siteName);
		switch($cleanName) {
			default:
				return new HtmlPage($cleanName);
		}
	}
}
?>