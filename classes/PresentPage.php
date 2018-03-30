<?php
class PresentPage {
	public function __construct() {
		if (!isset($_GET['id']))
			self::toPage("wunschliste");

		$this->user = User::getLoggedIn();
		if ($this->user == null)
			self::toPage("login");

		$this->getWishData($_GET['id']);
		$this->getPresentData($_GET['id']);

		if (isset($_POST['inKind']) || isset($_POST['amount'])) {
			$this->updateData();
			$this->storeData();
			self::toPage("schenken?id=" . $_GET['id']);
		}
	}

	private const WISH_SELECT = "SELECT `id`, `name`, `description`, `imageName`, `category`, `price`, SUM(`amount`) from `wishes` w LEFT JOIN `presents` p ON w.`id` = p.`wishid` GROUP BY `id` ORDER BY `order` ASC";
	private function fetchWishes($db) {
		$statement = $db->prepare(self::WISH_SELECT);
		$statement->execute();
		$statement->bind_result($id, $name, $descr, $imageName, $category, $price, $amount);
		while($statement->fetch())
			$this->wishes[$category][] = array("id" => $id, "name" => $name, "descr" => $descr, "imageName" => $imageName, "price" => $price, "amount" => $amount);
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
?>		<div class="scrolling">
			<h1>Schenken</h1>
		</div>
?>		<div class="staticImage" style="background-image: url(./image/<?php echo $this->imageName; ?>);">
			<h2><?php echo htmlentities(utf8_encode($this->name)); ?></h2>
			<p><?php echo htmlentities(utf8_encode($this->description)); ?></p>
<?php if ($this->amount > 0) { ?>
			<hr />
			<p>Im Moment schenkst du uns <?php if ($this->inKind) { ?>diesen Wunsch als Sachgeschenk<?php } else { ?>einen Beitrag von <?php echo $this->amount; ?>CHF<?php } ?>.
<?php } ?>
			<hr />
			<p>Wir freuen uns, dass du uns <?php echo htmlentities(utf8_encode($this->name)); ?> schenken m&ouml;chtest.</p>
<?php if ($this->done < $this->price) { ?>
			<?php if ($this->done > 0) { ?><p>Bislang wurde bereits <?php echo $this->done; ?> CHF geschenkt.</p><?php } ?>
			<form action="./schenken?id=<?php echo $_GET['id']; ?>" method="POST">
				<?php if (!$this->type != MONEY_ONLY && $this->done == 0) {  ?><p><input type="checkbox" class="checkbox" name="inKind" id="inKind" /> <label for="inKind">Ich organisiere das Geschenk selbst und bringe es mit.</label></p><?php } ?>
				<?php if (!$this->type != IN_KIND) { ?><p>Ich m&ouml;chte gerne <input name="amount" type="number" min="1" max="<?php echo $this->left; ?>" value="<?php echo $this->amount > 0 ? $this->amount : $this->left; ?>" style="width: 100px;" /> CHF schenken. (<?php echo $this->left; ?> CHF verf&uuml;gbar.)</p><?php } ?>
				<p><input type="submit" value="Schenken!" /></p>
			</form>
<?php } else { ?>
			<p>Leider wurde schon der volle Betrag geschenkt. Wir sind sicher, dass du etwas anderes finden wirst, das du uns schenken m&ouml;chtest.</p>
			<p style="button"><a href="./wunschliste">Zur&uuml;ck zur Geschenkliste</a></p>
<?php } ?>
		</div>
<?php
	}
	private const SELECT_WISH = "SELECT `name`, `description`, `price`, SUM(`amount`), `imageName`, `type` FROM `wishes` w LEFT JOIN `presents` p ON w.`id` = p.`wishid` WHERE `id` = ? GROUP BY `id`";
	private function getWishData($id) {
		$db = new Database();
		$statement = $db->prepare(self::SELECT_WISH);
		$statement->bind_param('i', $id);
		$statement->execute();
		$statement->store_result();
		if ($statement->num_rows > 0) {
			$statement->bind_result($this->name, $this->description, $this->price, $this->done, $this->imageName, $this->type);
			$statement->fetch();
			$this->left = $this->price - $this->done;
		} else
			throw new Exception("This wish could not be found... This seems weird.");
	}

	private const SELECT_PRESENT = "SELECT `amount`, `inKind` FROM `presents` WHERE `userid` = ? AND `wishid` = ?";
	private function getPresentData($id) {
		$db = new Database();
		$statement = $db->prepare(self::SELECT_PRESENT);
		$statement->bind_param('ii', $this->user->id, $id);
		$statement->execute();
		$statement->store_result();
		if ($statement->num_rows > 0) {
			$statement->bind_result($this->amount, $this->inKind);
			$statement->fetch();
			$this->inKind = $this->inKind == 1;
			$this->insert = false;

			$this->done -= $this->amount;
			$this->left += $this->amount;
		}
	}

	private function updateData() {
		if ($_POST['inKind'] == "on") {
			if ($this->done != 0)
				throw new Exception("You cannot make an in-kind present if someone else already takes part.");
			if ($this->type == MONEY_ONLY)
				throw new Exception("You cannot make an in-kind present for this wish.");
			$this->inKind = true;
			$this->amount = $this->price;
		} else {
			if ($this->type == IN_KIND)
				throw new Exception("You cannot make a monetary present for this wish.");
			$this->inKind = false;
			$this->amount = intval($_POST['amount']);
		}
	}

	private const INSERT = "INSERT INTO `presents` (`userid`, `wishid`, `amount`, `inKind`) VALUES (?, ?, ?, ?)";
	private const UPDATE = "UPDATE `presents` SET `amount` = ?, `inKind` = ? WHERE `userid` = ? AND `wishid` = ?";
	private function storeData() {
		$db = new Database();
		if ($this->insert) {
			$statement = $db->prepare(self::INSERT);
			$statement->bind_param('iiii', $this->user->id, $_GET['id'], $this->amount, $this->getInKind());
		} else {
			$statement = $db->prepare(self::UPDATE);
			$statement->bind_param('iiii', $this->amount, $this->getInKind(), $this->user->id, $_GET['id']);
		}
		$statement->execute();
		if ($statement->affected_rows == 0)
			throw new Exception("We couldn't save your present. That is weird...");
	}

	private function getInKind() {
		return $this->inKind ? 1 : 0;
	}

	private static function toPage($page) {
		header("Location: ./" . $page);
		exit();
	}

	private $user;

	private $name;
	private $description;
	private $price;
	private $done;
	private $left;
	private $imageName;
	private $type;
	private const MONEY_ONLY = "MONEYONLY";
	private const IN_KIND = "INKIND";
	private const BOTH = "BOTH";

	private $insert = true;
	private $inKind = false;
	private $amount = 0;
}
