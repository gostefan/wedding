<?php
class MenuManager {
	public function render() {
?>
		<script type="text/javascript">
			var scrollFactor = 0.05;
			function scrollToResolver(element) {
				var distance = element.getBoundingClientRect().top;
				var jump = parseInt(distance * scrollFactor);
				if (jump == 0 && Math.abs(distance) > 0.5 && element.scrollFactor <= 0.5) {
					element.scrollFactor *= 2;
					jump = parseInt(distance * element.scrollFactor);
				}
				document.body.scrollTop += jump;
				document.documentElement.scrollTop += jump;
				var lastTop = document.body.scrollTop;
				if (jump != 0 && (!element.lastTop || element.lastTop != lastTop)) {
					element.lastTop = lastTop;
					setTimeout(function() { scrollToResolver(element);}, "20");
				} else
					element.lastTop = null;
			}
			function scrollToId(name) {
				element = document.getElementsByName(name)[0];
				element.scrollFactor = scrollFactor;
				scrollToResolver(element);
				//element.scrollIntoView();
				return false;
			}
		</script>
		<div class="menu">
			<img id="hamburger" src="/images/hamburger.png" />
			<ul>
				<a href="/home"><li>Startseite</li></a>
				<a href="/home#information" onClick="return scrollToId('information')"><li>Informationen</li></a>
				<a href="/anmelden"><li>Anmelden</li></a>
				<a href="/wunschliste"><li>Wunschliste</li></a>
				<a href="/home"><li>Gallerie</li></a>
			</ul>
		</div>
<?php	}
}
?>