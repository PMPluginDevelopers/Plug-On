<?php
namespace plugon\module\error;

use plugon\module\Module;
use const plugon\RES_DIR;

class InternalErrorModule extends Module {
    public function getName() : string {
        return "err";
    }

    public function output() {
        http_response_code(500);
        ?>
        <!-- Error ref ID: <?= $this->getQuery() ?> -->
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
