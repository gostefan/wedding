<?php 
class SiteManager {
	public function getSite($siteName) {
		$cleanName = Utils::cleanName($siteName);
		if (strpos($siteName, ImageRequest::FOLDER) !== false)
			return new ImageRequest($siteName);
		switch($cleanName) {
			default:
				return new HtmlPage($cleanName);
		}
	}
}
?>