<?php
namespace plugon\module\archive;

use plugon\module\Module;
use plugon\output\OutputManager;
use plugon\Plugon;
use plugon\session\SessionUtils;

class ArchiveModule extends Module {
    
    public function getName() : string {
        return "archive";
    }
    
    public function output() {
        $session = SessionUtils::getInstance();
        ?>
        <!DOCTYPE HTML>
        <!--
        	Alpha by HTML5 UP
        	html5up.net | @ajlkn
        	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
        -->
        <html>
        	<head>
        		<title>Plug-On</title>
        		<meta charset="utf-8" />
        		<meta name="viewport" content="width=device-width, initial-scale=1" />
        		<!--[if lte IE 8]><?php $this->includeJs("ie/html5shiv") ?><![endif]-->
        		<?php $this->includeCss("main") ?>
        		<!--[if lte IE 8]><?php $this->includeCss("ie8") ?><![endif]-->
        		<?php $this->headIncludes() ?>
        	</head>
        	<body class="landing">
        		<div id="page-wrapper">
        
        			<!-- Header -->
        				<header id="header" class="alt">
        					<h1><a href="index.html">Plug-On</a> Plugin List</h1>
        					<nav id="nav">
        						<ul>
        							<li><a href="index.html">Home</a></li>
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
        							<li><a href="#" class="button">Sign Up</a></li>
                                    <li><a href="#" class="button">Log In</a></li>
        						</ul>
        					</nav>
        				</header>
        
        			<!-- Banner -->
        				<section id="banner">
        					<h2>Plug - On</h2>
        					<p>Crowd - Sourced Plugin List for Pocketmine - MP</p>
        					<ul class="actions">
        						<li><a href="#" class="button special">Sign Up</a></li>
        						<li><a href="#" class="button">Log In</a></li>
        					</ul>
        				</section>
        
        			<!-- Main -->
        				<section id="main" class="container">
        
        					<section class="box special">
        						<header class="major">
        							<h2>A Crowd Sourced Plugin List for Pocketmine - MP
        							<br />
        							For Customizing Your Server!</h2>
        							<p>New Pocketmine Website Means a New Place to Store Your Plugins!</p>
        						</header>
        						<span class="image featured"><img src="images/pic01.jpg" alt="" /></span>
        					</section>
        
        					<section class="box special features">
        						<div class="features-row">
        							<section>
        								<span class="icon major fa-bolt accent2"></span>
        								<h3>Introducing Plug - On</h3>
        								<p>When plugin reviewers over at http://pocketmine.net went inactive, authors were unable to post their plugins for server owners to enjoy. </p>
        							</section>
        							<section>
        								<span class="icon major fa-area-chart accent3"></span>
                                        <h3>Plug-On is the Solution.</h3>
                                        <p>The perfect way to share plugins across the community</p> 
        							</section>
        						</div>
        						
       
        					<div class="row">
        						<div class="6u 12u(narrower)">
        
        							<section class="box special">
        								<span class="image featured"><img src="images/pic02.jpg" alt="" /></span>
        								
        							</section>
        
        						</div>
        						<div class="6u 12u(narrower)">
        
        							
        
        						
        					
        
        				
        			<!-- CTA -->
        				
        
        			<!-- Footer -->
        				<footer id="footer">
        					<ul class="copyright">
        						<li>&copy; Plug-On. All rights reserved.</li><li>Design: <a href="https://github.com/PMPluginDevelopers">HTML5 UP</a></li>
        					</ul>
        				</footer>
        
        		</div>
        
        		<!-- Scripts -->
        			<?php
        			$this->includeJs("jquery.min");
        			$this->includeJs("jquery.dropotron.min");
        			$this->includeJs("skel.min");
        			$this->includeJs("util");
        			?>
        			<!--[if lte IE 8]><?php $this->includeJs("respond.min") ?><![endif]-->
        
        	</body>
        </html>
        <?php
    }
    
}
