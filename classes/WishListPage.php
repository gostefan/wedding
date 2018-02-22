<?php
class WishListPage {
	public function __construct() {
		$db = new Database();

		$this->fetchCategories($db);
		$this->fetchWishes($db);
		
	}
	
	private const CATEGORY_SELECT = "SELECT `id`, `name`, `imageName` FROM `wishcategories` ORDER BY `order` ASC";
	private function fetchCategories($db) {
		$statement = $db->prepare(self::CATEGORY_SELECT);
		$statement->execute();
		$statement->bind_result($id, $name, $imageName);
		while($statement->fetch())
			$this->categories[] = array("id" => $id, "name" => $name, "imageName" => $imageName);
	}
	
	private const WISH_SELECT = "SELECT `name`, `description`, `imageName`, `category` from `wishes` ORDER BY `order` ASC";
	private function fetchWishes($db) {
		$statement = $db->prepare(self::WISH_SELECT);
		$statement->execute();
		$statement->bind_result($name, $descr, $imageName, $category);
		while($statement->fetch())
			$this->wishes[$category][] = array("name" => $name, "descr" => $descr, "imageName" => $imageName);
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
			<h1>Wunschliste</h1>
		</div>
<?php
		foreach ($this->categories as $category) {
		$wishesInCategory = $this->wishes[$category["id"]];
		$totalWishes = count($wishesInCategory);
?>		<div class="staticImage" style="background-image: url(<?php echo ImageRequest::getPath($category['imageName']); ?>);">
			<h2><?php echo $category['name']?></h2>
			<div class="wishes">
<?php 	$currentWish = 0;
		for ($col = 0; $col < 2; $col++) { ?>
				<div class="wishColumn">
<?php 		$columnLimit = $col == 0 ? ceil($totalWishes / 2.) : $totalWishes;
			for (; $currentWish < $columnLimit; $currentWish++) { 
				$wish = $wishesInCategory[$currentWish]; ?>
					<div class="wish">
						<img src="<?php echo ImageRequest::getPath($wish['imageName']); ?>" />
						<div>
							<div>
								<div>
									<h3><?php echo htmlentities(utf8_encode($wish['name']), ENT_SUBSTITUTE); ?></h3>
									<p><?php echo htmlentities(utf8_encode($wish['descr']), ENT_SUBSTITUTE); ?></p>
								</div>
							</div>
						</div>
					</div>
<?php 			}?>
				</div>
<?php		} ?>
			</div>
		</div>
<?php
		}
	}

	private $categories = array();
	private $wishes = array();
}
?>