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
                    
        			<?php $this->drawHeader(); ?>
        
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
        								<p>When plugin reviewers over at http//:pocketmine.net went inactive, authors were unable to post their plugins for server owners to enjoy. </p>
        							</section>
        							<section>
        								<span class="icon major fa-area-chart accent3"></span>
                                        <h3>Plug-On is the Solution.</h3>
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
        				
        
        			<?php $this->drawFooter(); ?>
        
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
