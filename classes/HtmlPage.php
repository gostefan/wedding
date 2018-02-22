<?php
class HtmlPage {
	public function __construct($fileName) {
		if ($fileName == NULL || $fileName == "")
			$fileName = "home";

		$this->fileName = "../htmlPages/" . $fileName . ".htm";
		if (!file_exists($this->fileName))
			$this->fileName = "../htmlPages/404.htm";
		if (!file_exists($this->fileName))
			throw new Exception("404: We haven't planned that for our wedding yet - but we surely register that you want it.");
	}

	public function hasHeader() {
		return true;
	}
	public function hasMenu() {
		return true;
	}
	public function hasFooter() {
		return true;
	}

	public function render() {
		include $this->fileName;
	}

	private $fileName;
}
?>