<?php 
class SiteManager {
	public function getSite($siteName) {
		$cleanName = Utils::cleanName($siteName);
		if (strpos($siteName, ImageRequest::FOLDER) !== false)
			return new ImageRequest($siteName);
		switch($cleanName) {
			case "wunschliste":
				return new WishListPage();
			case "login":
				return new LoginPage();
			case "logout":
				return new LogoutPage();
			case "anmelden":
				return new RegisterPage();
			case "schenken":
				return new PresentPage();
			default:
				return new HtmlPage($cleanName);
		}
	}
}
?>