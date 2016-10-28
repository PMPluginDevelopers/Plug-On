<?php
namespace plugon\module\error;
include realpath(dirname(__FILE__)) . '/../Module.php';
use plugon\module\Module;
class InternalErrorModule extends Module {
    /**
     * @return string
     */
    public function getName(){
        return "InternalError";
    }

    /**
     *
     */
    public function output() {
        http_response_code(500);
        ?>
        <!-- Error ref ID: <?php echo $this->getQuery() ?> -->
        <html>
        <head>
            <?php $this->includeCss("style.css") ?>
            <title>500 Internal Server Error</title>
        </head>
        <body>
        <div id="body">
            <h1>500 Internal Server Error</h1>
            <p>A server internal error occurred. Reference ID: <code class="code"><?php echo $this->getQuery(); ?></code></p>
        </div>
        </body>
        </html>
        <?php
    }
}
