<?php
class RegisterPage {
	public function __construct() {
		$this->user = User::getLoggedIn();
		if ($this->user == null)
			LoginPage::doLogin("./anmelden");
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
		assert($this->user != null);
		$s = $this->user->singular;
		$this->getData($this->user);
	
		if (isset($_POST['accept']) || isset($_POST["decline"])) {
			$this->fetchData();
			$this->storeData();
			header("Location:./anmelden");
			exit();
		}
?>
		<div class="scrolling">
			<h1>Anmeldung</h1>
		</div>
		<div class="staticImage" style="background-image: url(./image/food.jpg);">
			<div class="centerContainer">
<?php if ($this->user->dinnerInvite) { ?>
				<p><?php if ($s) echo "Du bist"; else echo "Ihr seid"; ?> herzlich in die Kirche, zum Ap&eacute;ro und zum Hochzeitsfest eingeladen.</p>
				<?php self::printStatus($this->status, $s); ?>
				<form method="POST" action="./anmelden">
					<input type="submit" value="Abmelden" name="decline" />
				</form>
				<hr style="color: white;" />
				<form method="POST" action="./anmelden">
					<div id="acceptance">
						<p><?php if ($s) echo "Ich komme"; else echo "Wir kommen"; ?> gerne ...<br />
							<input type="checkbox" class="checkbox" name="apero" id="apero" <?php if ($this->apero == 1) echo "checked "; ?>/><label for="apero"> in die Kirche und zum Ap&eacute;ro ab ca. 15 Uhr.</label><br />
							<input type="checkbox" class="checkbox" name="dinner" id="dinner" <?php if ($this->dinner == 1) echo "checked "; ?>/><label for="dinner"> zur Hochzeitsfeier.</label>
						</p>
						<p>
							<input type="checkbox" class="checkbox" name="plusOne" id="plusOne" <?php if ($this->plusOne == 1) echo "checked "; ?>/><label for="plusOne"> <?php if ($s) { ?>Ich bringe ein +1 mit. Sein/Ihr Name <input name="plusOneName" value="<?php echo htmlentities($this->plusOneName); ?>"/><?php } else { ?>Wir kommen zu zweit.<?php } ?></label><br />
							<input type="checkbox" class="checkbox" name="hotel" id="hotel" <?php if ($this->hotel == 1) echo "checked "; ?>/><label for="hotel"> Reserviert <?php if ($s) echo "mir"; else echo "uns"; ?> bitte ein Zimmer im Hotel (Doppelzimmer 185 CHF).</label>
						</p>
						<p>
							Was <?php if ($s) echo "ich"; else echo "wir"; ?> noch sagen wollte<?php if (!$s) echo "n"; ?> (z.B. Liedwunsch):<br />
							<textarea name="comments" id="comments" style="width: 100%; min-height: 100px;"><?php echo htmlentities($this->comments); ?></textarea>
						</p>
					</div>
					<p><input type="submit" value="Anmelden!" name="accept" /></p>
				</form>
<?php } else { ?>
				<p><?php if ($s) echo "Du bist"; else echo "Ihr seid"; ?> herzlich in die Kirche, zum Ap&eacute;ron eingeladen.</p>
				<?php self::printStatus($this->status, $s); ?>
				<form method="POST" action="./anmelden">
					<input type="submit" value="Abmelden" name="decline" />
				</form>
				<hr style="color: white;" />
				<form method="POST" action="./anmelden">
					<div id="acceptance">
						<p><?php if ($s) echo "Ich bringe"; else echo "Wir bringen"; ?> <input type="number" name="apero" value="<?php echo max(0, $this->apero - ($s ? 0 : 1)); ?>" /> Personen an den Ap&eacute;ro mit.</p>
						<p>
							Was <?php if ($s) echo "ich"; else echo "wir"; ?> noch sagen wollte<?php if (!$s) echo "n"; ?>:<br />
							<textarea name="comments" style="width: 100%; min-height: 100px;"><?php echo htmlentities($this->comments); ?></textarea>
						</p>
					</div>
					<p><input type="submit" value="Anmelden!" name="accept" /></p>
				</form>
<?php } ?>
			</div>
		</div>
<?php
	}

	private static function printStatus($status, $s) {
		if ($status != "NONE") {
			echo "<p>Im Moment ";
			if ($s)
				echo "bist du";
			else
				echo "seid ihr";
			if ($status == "ACCEPT")
				echo " angemeldet.";
			else
				echo " abgemeldet.";
		}
	}

	private const SELECT = "SELECT `status`, `apero`, `dinner`, `hotel`, `plusOne`, `plusOneName`, `comments` FROM `users` WHERE `id` = ?";
	private function getData($user) {
		$db = new Database();
		$statement = $db->prepare(self::SELECT);
		$statement->bind_param('i', $user->id);
		$statement->execute();
		$statement->store_result();
		if ($statement->num_rows > 0) {
			$statement->bind_result($this->status, $this->apero, $this->dinner, $this->hotel, $this->plusOne, $this->plusOneName, $this->comments);
			$statement->fetch();
		} else
			throw new Exception("Somehow we cannot find data to this user... That's weird.");
	}

	private function fetchData() {
		$this->resetData();
		
		if (isset($_POST['decline'])) {
			$this->status = 'DECLINE';
			return;
		}

		$this->status = "ACCEPT";
		$this->comments = $_POST['comments'];
		if ($this->user->dinnerInvite) {
			$this->apero = self::getCheckboxValue($_POST['apero']);
			$this->dinner = self::getCheckboxValue($_POST['dinner']);
			$this->hotel = self::getCheckboxValue($_POST['hotel']);
			$this->plusOne = self::getCheckboxValue($_POST['plusOne']);
			$this->plusOneName = $_POST['plusOneName'];
		} else {
			$this->apero = intval($_POST['apero']) + ($this->user->singular ? 0 : 1);
		}
	}

	private static function getCheckboxValue($checkbox) {
		return $checkbox == "on" ? 1 : 0;
	}

	private function resetData() {
		$this->apero = 0;
		$this->dinner = 0;
		$this->hotel = 0;
		$this->plusOne = 0;
		$this->plusOneName = "";
		$this->comments = "";
	}

	private const UPDATE = "UPDATE `users` SET `status` = ?, `apero` = ?, `dinner` = ?, `hotel` = ?, `plusOne` = ?, `plusOneName` = ?, `comments` = ? WHERE `id` = ?";
	private function storeData() {
		$db = new Database();
		$statement = $db->prepare(self::UPDATE);
		$statement->bind_param('siiiissi', $this->status, $this->apero, $this->dinner, $this->hotel, $this->plusOne, $this->plusOneName, $this->comments, $this->user->id);
		$statement->execute();
		if ($statement->affected_rows == 0)
			throw new Exception("Somehow we cannot find this user... That's weird.");
	}

	private $user;

	private $status;
	private $apero;
	private $dinner;
	private $hotel;
	private $plusOne;
	private $plusOneName;
	private $comments;
}
?>