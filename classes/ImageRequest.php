<?php
class ImageRequest {
	public const FOLDER = "image/";

	public static function getPath($imageName) {
		return self::FOLDER . $imageName;
	}

	public function __construct($siteName) {
		$explodedName = explode(self::FOLDER, $siteName, 2);
		$elementCount = count($explodedName);
		if ($elementCount == 1)
			$this->imageName = $explodedName[0];
		elseif ($elementCount == 2)
			$this->imageName = $explodedName[1];
	}

	public function hasHeader() {
		return false;
	}
	public function hasMenu() {
		return false;
	}
	public function hasFooter() {
		return false;
	}

	public function render() {
		$db = new Database();
		$statement = $db->prepare("SELECT `path` FROM `images` WHERE `name` = ?");
		$statement->bind_param('s', $this->imageName);
		$statement->execute();
		$statement->bind_result($path);
		$statement->fetch();

		$imagePath = "../" . self::FOLDER . $path;
		if (is_file($imagePath)) {
			header('Content-type: image/' . $this->getMimeType($imagePath));
			include($imagePath);
		} else
			include($imagePath);
			exit("Bad image");
	}

	private function getMimeType($path) {
		$exploded = explode(".", $path);
		if (count($exploded) <= 0)
			exit("Bad image");
		$ext = $exploded[count($exploded) - 1];

		switch ($ext) {
			case "jpg":
			case "jpeg":
			case "jpe":
				return "jpeg";
			default:
				return $ext;
		}
	}

	private $imageName = NULL;
}
?>