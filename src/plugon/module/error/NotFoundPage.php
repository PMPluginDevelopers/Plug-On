<?php
namespace plugon\module\error;

use plugon\module\Module;
use plugon\Plugon;

class NotFoundPage extends Module {

  public function getName() :string {
      return "err";
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
                      class="verbose"><?= htmlspecialchars(Plugon::getRootPath()) ?></span><?= $this->getQuery() ?>
              </code>,
              does not exist or is not visible to you.</p>
          <p>Referrer: <?= $_SERVER["HTTP_REFERER"] ?? "<em>nil</em>" ?></p>
      </div>
      </body>
      </html>
      <?php
    }
    
}
