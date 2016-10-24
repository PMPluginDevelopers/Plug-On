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

    protected function drawHeader() {
        require \plugon\ASSETS_DIR . "header.php";
    }

    protected function headIncludes() {
        ?>
        <script src="//code.jquery.com/jquery-1.12.4.min.js"></script>
        <?php
        $this->includeJs("std");
        $this->includeCss("style");
    }
    
    protected function drawFooter() {
        require \plugon\ASSETS_DIR . "footer.php";
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
