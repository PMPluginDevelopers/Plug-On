<?php
namespace plugon\module\error;
include realpath(dirname(__FILE__)) . '/../Module.php';
use plugon\module\Module;
class AccessDeniedPage extends Module {
    /**
     * @return string
     */
    public function getName(){
        return self::class;
    }

    /**
     *
     */
    public function output() {
        http_response_code(500);
        ?>
        <!-- Error ref ID: <?php $this->getQuery() ?> -->
        <html>
        <head>
            <?php $this->includeCss("style.css") ?>
            <title>Error: Access Denied</title>
        </head>
        <body>
        <div id="body">
            <h1>Error: Access Denied</h1>
            <p>Your access this page was denied. Reference ID: <code class="code"><?php echo $this->getQuery(); ?></code></p>
        </div>
        </body>
        </html>
        <?php
    }
}
