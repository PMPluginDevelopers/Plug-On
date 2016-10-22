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
            <ul class="navbar">
                <li style="padding-right: 0; vertical-align: middle;"><img
                        src="<?= Plugon::getRootPath() ?>res/plugon.png" width="32"></li>
                <li><span class="tm">Plugon</span></li>
                <li class="navbutton" data-target="">Home</li>
                <li class="navbutton" data-target="build">Builds</li>
                <li class="navbutton extlink" data-target="https://github.com/PMPluginDevelopers/Plug-On">GitHub</li>
                <div style="float: right; padding-right: 50px">
                    <?php if($session->isLoggedIn()) { ?>
                        <li><span onclick="logout()" class="action">Logout as <?= $session->getLogin()["name"] ?></span>
                        </li>
                    <?php } else { ?>
                        <li>
                            <span
                                onclick='login(["user:email", "repo"])'
                                class="action">
                                Login with GitHub
                            </span>
                        </li>
                    <?php } ?>
                </div>
            </ul>
        </div>
        <?php
    }

    protected function headIncludes() {
        ?>
        <script src="//code.jquery.com/jquery-1.12.4.min.js"></script>
        <link type="text/css" rel="stylesheet" href="<?= Plugon::getRootPath() ?>res/style.css">
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
