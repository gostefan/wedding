<?php
class User {
	public static function getUserByUsername($username) {
		return new User($username, false);
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
	
	private const USERNAME_SELECT = "SELECT `id`, `name`, `email`, `username`, `pwd` FROM `users` WHERE `username` = ?";
	private function getUserByUsernameInternal($username) {
		$db = new Database();
		$statement = $db->prepare(self::USERNAME_SELECT);
		$statement->bind_param('s', $username);
		$statement->execute();
		$statement->store_result();
		if ($statement->num_rows > 0) {
			$statement->bind_result($this->id, $this->name, $this->email, $this->username, $this->pwd);
			$statement->fetch();
		} else
			$this->id = -1;
	}
	
	private const ID_SELECT = "SELECT `id`, `name`, `email`, `username`, `pwd` FROM `users` WHERE `id` = ?";
	private function getUserByIdInternal($id) {
		$db = new Database();
		$statement = $db->prepare(self::ID_SELECT);
		$statement->bind_param('s', $id);
		$statement->execute();
		$statement->store_result();
		if ($statement->num_rows > 0) {
			$statement->bind_result($this->id, $this->name, $this->email, $this->username, $this->pwd);
			$statement->fetch();
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
		$db = new Database();
		$statement = $db->prepare(self::CREATE);
		$hashedPwd = password_hash($password, PASSWORD_DEFAULT);
		$statement->bind_param("ssss", $name, $email, $username, $hashedPwd);
		$statement->execute();
		if ($statement->affected_rows > 0)
			return new User($statement->insert_id, true);
		else
			return null;
	}

	private $id;
	private $name;
	private $email;
	private $username;
	private $pwd;
}
?>