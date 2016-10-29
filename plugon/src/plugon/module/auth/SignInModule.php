<?php
namespace plugon\module\auth;

include_once realpath(dirname(__FILE__)) . '/../../../../entry.php';
include_once realpath(dirname(__FILE__)) . '/../Module.php';
include_once realpath(dirname(__FILE__)) . '/../../session/SessionUtils.php';

use plugon\module\Module;
use plugon\session\SessionUtils;

class SignInModule extends Module {

	/**
	 * @return string
	 */
    public function getName(){
        return "login";
    }
    
    public function output() {
        $session = SessionUtils::getInstance();
        if($session->isLoggedIn()) {
            \plugon\redirect("/", true);
        }
        ?>
        <!DOCTYPE HTML>
        <html>
        	<head>
        		<title>Plug-On | Sign In</title>
        		<?php 
        		$this->includePhp("head_meta");
        		$this->includeCss("auth");
        		?>
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
            				        <div>
            				            <ul class="error-log"></ul>
            				        </div>
            				        <h3> Login </h3>
            				        <form method="POST" action="#" id="login-form" onsubmit="return login(this)">
            				            <ul>
            				                <p>Username</p>
                				            <li><input name="username" placeholder="Username" value="" type="text" required></li>
                				            <p>Password</p>
                				            <li><input name="password" placeholder="" value="" type="password" required></li>
                				            <li><input name="submit" value="Login" type="submit"></li>
            				            </ul>
            				        </form>
            				        <a href="#" id="auth-fp">Forgot password?</a> | <a id="auth-fp" href="/register">Get an account</a>
            				    </div>
            				</div>
        
                        </section>	
        			<!-- CTA -->
        				

        			<?php $this->drawFooter() ?>
        
        		</div>
        
        		<!-- Scripts -->
        		<?php 
        		$this->includePhp("scripts");
        		$this->includeJs("auth");
        		?>
        
        	</body>
        </html>
        <?php
    }
    
}