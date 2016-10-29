<?php
namespace plugon\module;

include realpath(dirname(__FILE__)) . '/error/AccessDeniedPage.php';
include realpath(dirname(__FILE__)) . '/error/BadRequestPage.php';
include realpath(dirname(__FILE__)) . '/error/NotFoundPage.php';
include realpath(dirname(__FILE__)) . '/../output/OutputManager.php';

use plugon\module\error\AccessDeniedPage;
use plugon\module\error\BadRequestPage;
use plugon\module\error\NotFoundPage;
use plugon\output\OutputManager;
abstract class Module {

    const JS_DIR = 'res/js/';
    const ASSETS_DIR = 'assets/';
    const CSS_DIR = 'res/css/';
    const RES_DIR = 'res/';
    const LOG_DIR = 'logs/';
    const EXTENSION = ".php";

    /** @var Module|null */
    public static $currentPage = null;

    /** @var string */
    private $query;

    public function __construct($query){
        $this->query = $query;
    }

    /**
     * @return string
     */
    public function getQuery() {
        return $this->query;
    }

    public abstract function getName();
    public abstract function output();

    /**
     * @return string[]
     */
    public function getAllNames(){
        return [$this->getName()];
    }

    /**
     * @param bool|false $simple
     */
    protected function errorNotFound($simple = false) {
        OutputManager::terminateAll();
        if($simple) {
            (new NotFoundPage(""))->output();
        } else {
            (new NotFoundPage($this->getName() . "/" . $this->query))->output();
        }
        die;
    }

    protected function errorAccessDenied() {
        OutputManager::terminateAll();
        (new AccessDeniedPage($this->getName() . "/" . $this->query))->output();#DENIED! XD
        die;
    }

    /**
     * @param string $message
     */
    protected function errorBadRequest($message) {
        OutputManager::terminateAll();
        (new BadRequestPage($message))->output();
        die;
    }

    protected function drawHeader() {
        $this->includePhp("header");
    }

    protected function headIncludes() {
        ?>
        <!-- <script src="//code.jquery.com/jquery-1.12.4.min.js"></script> -->
        <!-- Added  js libs with integrity check -->
        <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous"></script>
        <?php
        $this->includeJs("std");
        $this->includeCss("style");
    }
    
    protected function drawFooter() {
        $this->includePhp("footer");
    }

    /**
     * @param string $fileName
     */
    protected function includeJs($fileName) {
        ?>
        <script src="<?php echo self::JS_DIR . $fileName ?>.js"></script>
        <?php
    }

    /**
     * @param string $fileName
     */
    protected function includeCss($fileName) {
        ?>
        <link type="text/css" rel="stylesheet" href="<?php echo self::CSS_DIR . $fileName ?>.css">
        <?php
    }

    /**
     * @param string $fileName
     */
    protected function includePhp($fileName) {
        include self::ASSETS_DIR . $fileName . self::EXTENSION;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection
     * @hide
     */
    private static function uselessFunction() {
        return;
    }
}
