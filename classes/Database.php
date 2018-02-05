<?php
class Database {
	private static $server   = "localhost";
	private static $user     = "yourUser";
	private static $password = "yourPassword";
	private static $dbName   = "yourDB";
	public function __construct() {
		$this->connection = new mysqli($server, $user, $password, $dbName);
		if ($this->connection->connection_error)
			throw new Exception("Couldn't connect to the server.");
	}

	public function __destruct() {
		$this->connection->close();
	}

	public function query($query) {
		return $this->connection->query($query);
	}

	private $connection;
}
?>