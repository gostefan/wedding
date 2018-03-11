<?php
class LoginPage {
	public function __construct() {
		$this->user = User::getLoggedIn();
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
		if (isset($_POST["username"])) {
			if (isset($_GET["new"])) {
				// Create new user
				$user = User::create($_POST['names'], $_POST['mail'], $_POST['username'], $_POST['password']);
				$user->login($_POST['password']);
			} else {
				// Log in existing user
				$user = User::getUserByUsername($_POST["username"]);
				$login = $user->login($_POST["password"]);
				exit(1);
			}
		}
		if ($this->user == null) {
	?>		<div class="scrolling">
				<h1>Anmelden</h1>
			</div>
			<div class="staticImage" style="background-image: url(./image/anmelden.jpg);">
				<h2>Login</h2>
				<form action="./login" method="POST">
					<p><input type="text" name="username" placeholder="Benutzername" /></p>
					<p><input type="password" name="password" placeholder="Passwort" /></p>
					<p><input type="submit" value="Einloggen" /></p>
				</form>
			</div>
			<div class="staticImage" style="background-image: url(./image/anmelden.jpg);">
				<h2>Neuen Benutzer erstellen</h2>
				<form action="./login?new" method="POST">
					<p><input type="text" name="username" placeholder="Benutzername" /></p>
					<p><input type="text" name="names" placeholder="Namen der G&auml;ste" /></p>
					<p><input type="text" name="mail" placeholder="Email Adresse" /></p>
					<p><input type="password" name="password" placeholder="Passwort" /></p>
					<p><input type="submit" value="Benutzer erstellen" /></p>
				</form>
			</div>
<?php
		}
	}

	private $user;
}
?>