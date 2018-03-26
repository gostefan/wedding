<?php
class LoginPage {
	public function __construct() {
		$this->user = User::getLoggedIn();
	}
	
	public static function doLogin($afterLoginPage) {
		$_SESSION["login.afterRedirect"] = $afterLoginPage;
		header("Location:./login");
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
				try {
					$this->user = User::create($_POST['names'], $_POST['mail'], $_POST['username'], $_POST['password']);
					$this->user->login($_POST['password']);
				} catch (Exception $e) {
					$_SESSION['login.newError'] = $e->getMessage();
					header("Location:./login");
					exit();
				}
			} else {
				// Log in existing user
				$error = "";
				$this->user = User::getUserByUsername($_POST["username"]);
				if ($this->user == null)
					$error = "Der Benutzer konnte nicht gefunden werden.";
				elseif (!$this->user->login($_POST["password"]))
					$error = "Das Passwort stimmt nicht für diesen Benutzer.";
				if (strlen($error) > 0) {
					$_SESSION['login.loginError'] = $error;
					header("Location:./login");
					exit();
				}
			}
		}
		if ($this->user == null) {
	?>		<div class="scrolling">
				<h1>Anmelden</h1>
			</div>
			<div class="staticImage" style="background-image: url(./image/anmelden.jpg);">
				<h2>Login</h2>
<?php if (isset($_SESSION['login.loginError'])) { ?>
				<p style="max-width: 30%; margin: auto;"><?php echo htmlentities(utf8_encode($_SESSION['login.loginError'])); unset($_SESSION['login.loginError']); ?></p>
<?php } ?>
				<form action="./login" method="POST">
					<p><input type="text" name="username" placeholder="Benutzername" /></p>
					<p><input type="password" name="password" placeholder="Passwort" /></p>
					<p><input type="submit" value="Einloggen" /></p>
				</form>
				<p>Falls ihr euer Passwort verloren habt, meldet euch bei uns und wir schicken euch ein Neues.</p>
			</div>
			<div class="staticImage" style="background-image: url(./image/anmelden.jpg);">
				<h2>Neuen Benutzer erstellen</h2>
<?php if (isset($_SESSION['login.newError'])) { ?>
				<p style="max-width: 30%; margin: auto;"><?php echo htmlentities(utf8_encode($_SESSION['login.newError'])); unset($_SESSION['login.newError']); ?></p>
<?php } ?>
				<form action="./login?new" method="POST">
					<p><input type="text" name="username" placeholder="Benutzername" /></p>
					<p><input type="text" name="names" placeholder="Namen der G&auml;ste" /></p>
					<p><input type="text" name="mail" placeholder="Email Adresse" /></p>
					<p><input type="password" name="password" placeholder="Passwort" /></p>
					<p><input type="submit" value="Benutzer erstellen" /></p>
				</form>
			</div>
<?php	} else {
			if (isset($_SESSION["login.afterRedirect"]))
				$target = $_SESSION["login.afterRedirect"];
			else
				$target = "./home";
			header("Location:" . $target);
		}
	}

	private $user;
}
?>