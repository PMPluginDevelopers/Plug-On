<?php
namespace plugon\module\error;
include_once realpath(dirname(__FILE__)) . '/../Module.php';
use plugon\module\Module;
class BadRequestPage extends Module {
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
        <!-- Error ref ID: <?php echo $this->getQuery() ?> -->
        <html>
        <head>
            <?php $this->includeCss("style.css") ?>
            <title>Error: Bad Request Page</title>
        </head>
        <body>
        <div id="body">
            <h1>Error: Bad Request Page</h1>
            <p>This is a bad request page. Reference ID: <code class="code"><?php echo $this->getQuery(); ?></code></p>
        </div>
        </body>
        </html>
        <?php
    }
}
