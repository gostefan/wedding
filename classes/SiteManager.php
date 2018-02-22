<?php 
class SiteManager {
	public function getSite($siteName) {
		$cleanName = Utils::cleanName($siteName);
		if (strpos($siteName, ImageRequest::FOLDER) !== false)
			return new ImageRequest($siteName);
		switch($cleanName) {
			case "wunschliste":
				return new WishListPage();
			default:
				return new HtmlPage($cleanName);
		}
	}
}
?>