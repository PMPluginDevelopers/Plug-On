<?php
namespace plugon\module\archive;

use plugon\module\Module;
use plugon\output\OutputManager;
use plugon\Plugon;
use plugon\session\SessionUtils;

class ArchiveModule extends Module {
    
    public function getName() : string {
        return "archive";
    }
    
    public function output() {
        $session = SessionUtils::getInstance();
        if(!$session->isLoggedIn()) {
            ?>
            <html>
                <head>
                    <title>Plugon</title>
                    <?php $this->headIncludes() ?>
                </head>
                <body>
                    <header>
                    <?php $this->bodyHeader() ?>
                    </header>
                    <main>
                        <div id="body">
                            <center>Plugon - PocketMine Plugin Archive</center>
                        </div>
                    </main>
                    <footer class="page-footer">
                        <?php $this->drawFooter() ?>
                    </footer>
                </body>
            </html>
            <?php
        } else {
            $login = $session->getLogin();
            ?>
            <html>
            <head>
                <title>Plugon</title>
                <?php $this->headIncludes() ?>
            </head>
            <body>
            <?php $this->bodyHeader(); ?>
            <?php $this->includeJs("home"); ?>
            <?php $minifier = OutputManager::startMinifyHtml(); ?>
            <?php OutputManager::endMinifyHtml($minifier); ?>
            </body>
            </html>
            <?php
        }
    }
}