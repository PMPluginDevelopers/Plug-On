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
                    <a href="#" class="brand-logo right">Logo</a>
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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.7/js/materialize.min.js"></script>
        <link type="text/css" rel="stylesheet" href="<?= Plugon::getRootPath() ?>res/style.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.7/css/materialize.min.css">
        <link type="image/x-icon" rel="icon" href="<?= Plugon::getRootPath() ?>res/plugon.ico">
        <?php
        $this->includeJs("std");
    }

    protected function includeJs(string $fileName) {
        ?>
        <script src="<?= Plugon::getRootPath() ?>js/<?= $fileName ?>.js"></script>
        <?php
    }

    protected function includeCss(string $fileName) {
        ?>
        <link type="text/css" rel="stylesheet" href="<?= Plugon::getRootPath() ?>res/<?= $fileName ?>.css">
        <?php
    }

    /** @noinspection PhpUnusedPrivateMethodInspection
     * @hide
     */
    private static function uselessFunction() {
    }
}
