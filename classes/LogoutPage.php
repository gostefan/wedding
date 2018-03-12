<?php
class LogoutPage {
	public function __construct() {
		$this->user = User::getLoggedIn();
		User::logOut();
		header("Location:./home");
	}

	// Below will never be called - but a page has this interface.
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
	}

	private $user;
}
?>