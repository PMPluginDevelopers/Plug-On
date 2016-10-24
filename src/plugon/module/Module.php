<?php
namespace plugon\module;

use plugon\module\error\AccessDeniedPage;
use plugon\module\error\BadRequestPage;
use plugon\module\error\NotFoundPage;
use plugon\module\error\SimpleNotFoundPage;
use plugon\output\OutputManager;
use plugon\Plugon;
use plugon\session\SessionUtils;

abstract class Module {
    /** @var Module|null */
    public static $currentPage = null;

    /** @var string */
    private $query;

    public function __construct(string $query) {
        $this->query = $query;
    }

    public function getQuery() {
        return $this->query;
    }

    public abstract function getName() : string;

    public function getAllNames() : array {
        return [$this->getName()];
    }

    public abstract function output();

    protected function errorNotFound(bool $simple = false) {
        OutputManager::terminateAll();
        if($simple) {
            (new SimpleNotFoundPage(""))->output();
        } else {
            (new NotFoundPage($this->getName() . "/" . $this->query))->output();
        }
        die;
    }

    protected function errorAccessDenied() {
        OutputManager::terminateAll();
        (new AccessDeniedPage($this->getName() . "/" . $this->query))->output();
        die;
    }

    protected function errorBadRequest(string $message) {
        OutputManager::terminateAll();
        (new BadRequestPage($message))->output();
        die;
    }

    protected function bodyHeader() {
        $session = SessionUtils::getInstance();
        ?>
        <div id="header">
            <nav>
                <div class="nav-wrapper">
                    <ul id="nav-mobile" class="left hide-on-med-and-down">
                        <li class="navbutton" data-target="archive">Archive</li>
                        <li class="navbutton" data-target="submit">Submit</li>
                        <li class="navbutton extlink" data-target="https://github.com/PMPluginDevelopers/Plug-On">Github</li>
                    </ul>
                </div>
            </nav>
        </div>
        <?php
    }

    protected function headIncludes() {
        ?>
        <script src="//code.jquery.com/jquery-1.12.4.min.js"></script>
        <?php
        $this->includeJs("std");
        $this->includeCss("style");
    }
    
    protected function drawFooter() {
        ?>
          <div class="container">
            <div class="row">
              <div class="col l6 s12">
                <h5 class="white-text">Footer Content</h5>
                <p class="grey-text text-lighten-4">You can use rows and columns here to organize your footer content.</p>
              </div>
              <div class="col l4 offset-l2 s12">
                <h5 class="white-text">Links</h5>
                <ul>
                  <li><a class="grey-text text-lighten-3" href="#!">Link 1</a></li>
                  <li><a class="grey-text text-lighten-3" href="#!">Link 2</a></li>
                  <li><a class="grey-text text-lighten-3" href="#!">Link 3</a></li>
                  <li><a class="grey-text text-lighten-3" href="#!">Link 4</a></li>
                </ul>
              </div>
            </div>
          </div>
          <div class="footer-copyright">
            <div class="container">
            © 2014 Copyright Text
            <a class="grey-text text-lighten-4 right" href="#!">More Links</a>
            </div>
          </div>
        <?php
    }

    protected function includeJs(string $fileName) {
        ?>
        <script src="<?= \plugon\JS_DIR . $fileName ?>.js"></script>
        <?php
    }

    protected function includeCss(string $fileName) {
        ?>
        <link type="text/css" rel="stylesheet" href="<?= \plugon\CSS_DIR . $fileName ?>.css">
        <?php
    }

    /** @noinspection PhpUnusedPrivateMethodInspection
     * @hide
     */
    private static function uselessFunction() {
    }
}
