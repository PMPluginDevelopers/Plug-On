<?php
namespace plugon\module\error;

include realpath(dirname(__FILE__)) . '/../../Plugon.php';
include realpath(dirname(__FILE__)) . '/../Module.php';

use plugon\module\Module;
use plugon\Plugon;

class NotFoundPage extends Module {

    /**
     * @return string
     */
  public function getName(){
      return self::class;
  }

  public function output() {
    http_response_code(404);
      ?>
      <html>
      <head>
          <?php $this->headIncludes() ?>
          <title>404 Not Found</title>
      </head>
      <body>
      <div id="body">
          <h1>404 Not Found</h1>
          <p>Path <code class="code"><span
                      class="verbose"><?php htmlspecialchars(Plugon::getRootPath()) ?></span><?php $this->getQuery() ?>
              </code>,
              does not exist or is not visible to you.</p>
          <p>Referrer: <?= $_SERVER["HTTP_REFERER"] ?? "<em>nil</em>" ?></p>
      </div>
      </body>
      </html>
      <?php
    }
    
}
