<?php
namespace plugon\module\auth;

use plugon\module\Module;
use plugon\session\SessionUtils;

class SignInModule extends Module {
    
    public function getName() : string {
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
        		$this->includeJs("auth");
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
            				        <h3> Login </h3>
            				        <form method="POST" action="#" id="login-form" onsubmit="return login()">
            				            <ul>
            				                <p>Username</p>
                				            <li><input name="username" placeholder="Username" value="" type="text" required></input></li>
                				            <p>Password</p>
                				            <li><input name="password" placeholder="" value="" type="password" required></input></li>
                				            <li><input name="submit" value="Login" type="submit" onclick="login()"></input></li>
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
        		<?php $this->includePhp("scripts") ?>
        
        	</body>
        </html>
        <?php
    }
    
}