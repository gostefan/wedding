<?php
class Header {
	private $defaultTitle = "Olivia und Stefan";
	public function renderHeader() { ?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="./main.css" />
	</head>
	<body>
<?php }

	public function renderFooter() {
?>
	</body>
</html>
	<?php }
}
?>