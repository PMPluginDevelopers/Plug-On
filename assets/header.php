<!-- Header -->
<header id="header" class="alt">
	<h1><a href="/">Plug-On</a> Plugin List</h1>
	<nav id="nav">
		<ul>
			<li><a href="/">Home</a></li>
			<li>
				<a href="#" class="icon fa-angle-down">Pocketmine Forums</a>
				<ul>
					<li><a href="generic.html">Post Your Plugin</a></li>
					<li><a href="contact.html">Authors</a></li>
					<li><a href="elements.html">Top Plugins</a></li>
					<li>
						<a href="#">Categories</a>
						<ul>
							<li><a href="#">Admin Tools</a></li>
							<li><a href="#">Anti-Griefing Tools</a></li>
							<li><a href="#">Chat Related</a></li>
							<li><a href="#">Developer Tools</a></li>
                            <li><a href="#">Economy</a></li>
							<li><a href="#">Fun</a></li>
							<li><a href="#">General</a></li>
							<li><a href="#">Informational</a></li>
                            <li><a href="#">Mechanics</a></li>
                            <li><a href="#">Miscellaneous</a></li>
							<li><a href="#">Teleportational</a></li>
							<li><a href="#">World Editing & Managment</a></li>
							<li><a href="#">World Generator</a></li>
						</ul>
					</li>
				</ul>
			</li>
			<?php
			$session = \plugon\session\SessionUtils::getInstance();
			if($session->isLoggedIn()) {
				?>
				<li><a onclick="logout()" class="button">Log out</a></li>
				<?php
			} else {
				?>
				<li><a href="/register" class="button">Sign Up</a></li>
	            <li><a href="/login" class="button">Log In</a></li>
				<?php
			}
			?>
		</ul>
	</nav>
</header>