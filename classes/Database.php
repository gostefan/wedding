<?php
class Database {
	private const SERVER   = "localhost";
	private const USER     = "wedding";
	private const PASSWORD = "0r71NMW#aLvk4!z$";
	private const DB_NAME  = "sgoetschi_wedding";
	public function __construct() {
		$this->connection = new mysqli(self::SERVER, self::USER, self::PASSWORD, self::DB_NAME);
		if ($this->connection->connect_error)
			throw new Exception("Couldn't connect to the server.");
	}

	public function __destruct() {
		$this->connection->close();
	}

	public function prepare($query) {
		return $this->connection->prepare($query);
	}

	private $connection;
}
?>