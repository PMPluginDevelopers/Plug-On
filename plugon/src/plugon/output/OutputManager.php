<?php
namespace plugon\output;

include_once realpath(dirname(__FILE__)) . '/../Plugon.php';
use plugon\Plugon;

class OutputManager {
    public static $current;

    /** @var OutputManager|null */
    private $parent;
    /** @var OutputManager|null */
    private $child = null;

    /** @var string */
    private $buffer = "";

    public function __construct($parent = null) {
        $this->parent = $parent;
        self::$current = $this;

        if($parent === null) {
            ob_start([$this, "handle"], 1024);
        }
    }

    /**
     * @return null|OutputManager
     */
    public static function startMinifyHtml(){
        return self::$current->startChild();
    }

    /**
     * @param OutputManager $manager
     */
    public static function endMinifyHtml($manager) {
        ob_flush();
        $manager->processedOutput(function ($html) {
            $processed = preg_replace('/[ \t]+/m', " ", $html);
            $processed = preg_replace('/[ ]?\n[ ]/', "\n", $processed);
            $hlen = strlen($html);
            $plen = strlen($processed);
            Plugon::getLog()->verbose("Minified $hlen - $plen = " . ($hlen - $plen) . " bytes (" . ((1 - $plen / $hlen) * 100) . "%)");
            return $processed;
        });
    }

    public function startChild(){
        if($this->child !== null) {
            return $this->child->startChild();
        }
        $this->child = new OutputManager($this);
        return $this->child;
    }

    public function handle($buffer) {
        if($this->child !== null) {
            $this->child->handle($buffer);
            return;
        }
        $this->append($buffer);
    }

    public function flush() {
        if($this->parent === null) {
            ob_end_clean();
            echo $this->buffer;
            ob_start([$this, "handle"]);
        } else {
            $this->parent->append($this->buffer);
        }
        $this->buffer = "";
    }

    public function output() {
        if($this->child !== null) {
            throw new \RuntimeException("Cannot close output manager with child");
        }
        if($this->parent === null) {
            if(ob_get_length()) {
                ob_end_clean();
            } else ob_end_flush();
            echo $this->buffer;
        } else {
            $this->parent->closeChild($this->buffer);
        }
    }

    public function outputTree() {
        $this->output();
        if($this->parent !== null) {
            $this->parent->outputTree();
        }
    }

    public function processedOutput(callable $processor) {
        $this->buffer = $processor($this->buffer);
        $this->output();
    }

    public function terminate() {
        if($this->parent === null) {
            echo "\0"; // hack
            ob_end_clean();
        } else {
            $this->parent->closeChild("");
        }
    }

    /**
     * @return bool
     */
    public static function terminateAll(){
        if(OutputManager::$current !== null) {
            OutputManager::$current->terminateTree();
            return true;
        }
        return false;
    }


    private function terminateTree() {
        if($this->parent !== null) {
            $this->parent->terminateTree();
            return;
        }
        $this->terminate();
    }

    /**
     * @param $buffer
     */
    protected function closeChild($buffer) {
        $this->append($buffer);
        $this->child = null;
    }

    /**
     * @param $buffer
     */
    protected function append($buffer) {
        $this->buffer .= $buffer;
    }
}
