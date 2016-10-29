<?php
namespace plugon\module\auth;

include realpath(dirname(__FILE__)) . '/../../../../entry.php';
include realpath(dirname(__FILE__)) . '/../Module.php';
include realpath(dirname(__FILE__)) . '/../../session/SessionUtils.php';
include realpath(dirname(__FILE__)) . '/../../utils/ErrorCatcher.php';

use plugon\module\Module;
use plugon\session\SessionUtils;
use plugon\utils\ErrorCatcher;

class SignUpModule extends Module {

    /**
     * @return string
     */
    public function getName() {
        return "register";
    }
    
    public function output() {
        $session = SessionUtils::getInstance();
        if($session->isLoggedIn()) {
            \plugon\redirect("/", true);
        }
        $siteKey = '6LctPgoUAAAAAOPMNiOYW9DS0vbACvFvECLyk4_H';
        $lang = 'en';
        
        // THIS IS ONLY FOR DEBUGGING
        $ec = new ErrorCatcher($_POST);
        if($ec->scan()) {
            // Error was sent via $_POST
        }

        ?>
        <!DOCTYPE HTML>
        <html>
        	<head>
        		<title>Plug-On | Sign Up</title>
        		<?php 
        		$this->includePhp("head_meta");
        		$this->includeCss("auth");
        		?>
        		<script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=<?php echo $lang; ?>"></script>
        	</head>
        	<body class="landing">
        		<div id="page-wrapper">
                    
        			<?php $this->drawHeader(); ?>
        			
        			<!-- Banner -->
        			<section id="banner"></section>
        
        			<!-- Main -->
        				<section id="main" class="container">
        
            				<div class="box">
            				    <div class="auth-box">
            				        <h3> Register </h3>
            				        <div class="error-log">
            				            <ul>
            				                <?php
            				                    foreach($ec->getErrors() as $error) {
            				                        ?>
            				                        <li><div class="error-badge"><?php echo $error; ?></div></li>
            				                        <?php
            				                    }
            				                ?>
            				            </ul>
            				        </div>
            				        <form method="POST" id="register-form"  onsubmit="return register(this)" action="#">
            				            <ul>
            				                <p>Username</p>
                				            <li><input name="username" placeholder="" value="" type="text" required></li>
                				            <p>Password</p>
                				            <li><input name="password" placeholder="" value="" type="password" required></li>
                				            <p>E-Mail</p>
                				            <li><input type="email" name="email" placeholder="" required></li>
            				            </ul>
            				            <div id="captcha" class="g-recaptcha" data-sitekey="<?php echo $siteKey; ?>"></div>
            				            <input name="submit" value="Register" type="submit">
            				        </form>
            				    </div>
            				    <center><p>Already have an account? <a href="/login">Log in!</a></p></center>
            				</div>
        
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