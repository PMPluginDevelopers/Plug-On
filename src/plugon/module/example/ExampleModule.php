<?php
namespace plugon\module\example;

use plugon\module\Module;
use plugon\session\SessionUtils;

class ExampleModule extends Module {

  public function getName() : string {
    return "example";
  }
  
  public function output() {
    $session = SessionUtils::getInstance();
    ?>
      <html>
        <head>
          <?php $this->headIncludes(); ?>
        </head>
        <body>
          <?php $this->bodyHeader(); ?>
          <h1>This is basic Example page</h1>
          <?php
            if ($session->isLoggedIn()) {
              ?>
                <p>Welcome <?php=$session->getLogin()["name"]; ?></p>
              <?php
            }
          ?>
        </body>
    <?php
  }

}
