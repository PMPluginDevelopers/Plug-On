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
        		<?php $this->includePhp("head_meta") ?>
        	</head>
        	<body class="landing">
        		<div id="page-wrapper">
                    
        			<?php 
        			$this->drawHeader();
        			$this->includePhp("load_screen");
        			?>
        
        			<!-- Banner -->
        				<section id="banner">
        					<h2>Plug - On</h2>
        					<p>Crowd - Sourced Plugin List for Pocketmine - MP</p>
        					<ul class="actions">
        						<?php
        						if(!$session->isLoggedIn()) {
        						    ?>
            						<li><a href="/register" class="button special">Sign Up</a></li>
            						<li><a href="/login" class="button">Log In</a></li>
            						<?php
        						} else {
        						    ?>
        						    <p>Welcome back <?php echo $session->getDisplayName(); ?>.</p>
        						    <li><a href="#" class="button special">Submit Plugin</a></li>
        						    <li><a href="#" class="button">Plugin List</a></li>
        						    <li><a href="#" class="button">Profile</a></li>
        						    <li><a href="#" class="button">Random Plugin</a></li>
        						    <?php
        						}
        						?>
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
                                        <p>The perfect way to share plugins across the community, Plug - On will help plugin developers share their content with the world! Have Fun!</p> 
        							</section>
        						</div>
        				    </section>
        				    
        				</section>

        			<!-- CTA -->
        				

        			<?php $this->drawFooter() ?>
        
        		</div>
        
        		<!-- Scripts -->
        		<?php $this->includePhp("scripts") ?>
        
        	</body>
        </html>
        <?php
    }
    
}
