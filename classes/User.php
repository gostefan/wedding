<?php
class User {
	public static function getUserByUsername($username) {
		$user = new User($username, false);
		if ($user->id == -1)
			return null;
		else
			return $user;
	}
	public static function getLoggedIn() {
		if (isset($_SESSION['User.loggedIn']))
			return new User($_SESSION['User.loggedIn'], true);
		return null;
	}

	public static function logOut() {
		if (isset($_SESSION['User.loggedIn']))
			unset($_SESSION['User.loggedIn']);
	}

	private function __construct($value, $fromId) {
		if ($fromId)
			$this->getUserByIdInternal($value);
		else
			$this->getUserByUsernameInternal($value);
	}
	
	private const USERNAME_SELECT = self::FIELDS . " WHERE `username` LIKE ?";
	private function getUserByUsernameInternal($username) {
		if (strpos($username, "%") !== FALSE) {
			$this->id = -1;
			return;
		}
		$db = new Database();
		$statement = $db->prepare(self::USERNAME_SELECT);
		$statement->bind_param('s', utf8_decode($username));
		$statement->execute();
		$this->bindResults($statement);
	}
	
	private const ID_SELECT = self::FIELDS . " WHERE `id` = ?";
	private function getUserByIdInternal($id) {
		$db = new Database();
		$statement = $db->prepare(self::ID_SELECT);
		$statement->bind_param('s', $id);
		$statement->execute();
		$this->bindResults($statement);
	}
	
	private const FIELDS = "SELECT `id`, `singular`, `name`, `dinnerInvite`, `email`, `username`, `pwd` FROM `users`";
	private function bindResults($statement) {
		$statement->store_result();
		if ($statement->num_rows > 0) {
			$statement->bind_result($this->id, $singular, $this->name, $this->dinnerInvite, $this->email, $this->username, $this->pwd);
			$statement->fetch();
			$this->singular = $singular == 1;
		} else
			$this->id = -1;
	}

	public function login($password) {
		$matched = password_verify($password, $this->pwd);
		if ($matched) {
			$_SESSION['User.loggedIn'] = $this->id;
		}
		return $matched;
	}
	
	private const CREATE = "INSERT INTO `users` (`name`, `email`, `username`, `pwd`) VALUES (?, ?, ?, ?)";
	public static function create($name, $email, $username, $password) {
		self::validateUser($name, $email, $username, $password);
		$db = new Database();
		$statement = $db->prepare(self::CREATE);
		$hashedPwd = password_hash($password, PASSWORD_DEFAULT);
		$statement->bind_param("ssss", $name, $email, $username, $hashedPwd);
		$statement->execute();
		if ($statement->affected_rows > 0)
			return new User($statement->insert_id, true);
	}

	private const EMAIL_REGEX = "´(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*|\"(?:[\\x01-\\x08\\x0b\\x0c\\x0e-\\x1f\\x21\\x23-\\x5b\\x5d-\\x7f]|\\\\[\\x01-\\x09\\x0b\\x0c\\x0e-\\x7f])*\")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\\x01-\\x08\\x0b\\x0c\\x0e-\\x1f\\x21-\\x5a\\x53-\\x7f]|\\\\[\\x01-\\x09\\x0b\\x0c\\x0e-\\x7f])+)\\])´";
	private static function validateUser($name, $email, $username, $password) {
		$result = "";
		if (!ctype_alnum($username) || strlen($username) < 6)
			$result .= "Benutzername enthält unerlaubte Zeichen oder ist nicht 6 Zeichen lang. ";
		if (!self::checkUsernameUnique($username))
			$result .= "Der Benutzername ist bereits vergeben. ";
		if (preg_match(self::EMAIL_REGEX, $email) != 1)
			$result .= "Die E-Mail Adresse ist ungültig. ";
		if (!self::checkEmailUnique($email))
			$result .= "Diese Email Adresse wird bereits verwendet. ";
		if (strlen($name) < 10)
			$result .= "Der Gäste der Gäste ist kürzer als 10 Zeichen... das glauben wir dir nicht. ";
		if (strlen($password) < 8)
			$result .= "Das Passwort ist nicht 8 Zeichen lang. ";

		if (strlen($result) > 0) {
			throw new Exception($result);
		}
	}
	private static function checkUsernameUnique($username) {
		$db = new Database();
		$statement = $db->prepare(self::USERNAME_SELECT);
		$statement->bind_param('s', $username);
		$statement->execute();
		$statement->store_result();
		return $statement->num_rows == 0;
	}
	private const EMAIL_SELECT = self::FIELDS . " WHERE `email` LIKE ?";
	private static function checkEmailUnique($email) {
		$db = new Database();
		$statement = $db->prepare(self::EMAIL_SELECT);
		$statement->bind_param('s', $email);
		$statement->execute();
		$statement->store_result();
		return $statement->num_rows == 0;
	}

	public $id;
	public $singular;
	private $name;
	private $email;
	private $username;
	private $pwd;

	public $dinnerInvite;
}
?>