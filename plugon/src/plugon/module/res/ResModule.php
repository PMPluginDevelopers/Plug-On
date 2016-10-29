<?php
namespace plugon\module\res;

include_once realpath(dirname(__FILE__)) . '/../Module.php';
include_once realpath(dirname(__FILE__)) . '/../../Plugon.php';
include_once realpath(dirname(__FILE__)) . '/../../session/SessionUtils.php';

use plugon\module\Module;
use plugon\Plugon;
use plugon\session\SessionUtils;

class ResModule extends Module {
    static $TYPES = [
        "html" => "text/html",
        "css" => "text/css",
        "js" => "application/javascript",
        "json" => "application/json",
        "png" => "image/png",
        "ico" => "image/x-icon",
    ];
    static $BANNED = [
        "banned"
    ];

    public function getName(){
        return "res";
    }

    protected function resDir() {
        return Module::RES_DIR;
    }

    public function output() {
        $resDir = $this->resDir();
        $path = realpath($resDir . $this->getQuery());
        if(isset(self::$BANNED[$this->getQuery()])) {
            $this->errorAccessDenied();
        }
        if(realpath(dirname($path)) === realpath($resDir) and is_file($path)) {
            $ext = substr($path, (strrpos($path, ".") ?: -1) + 1);
            header("Content-Type: " . self::$TYPES[$ext]);
            $cont = file_get_contents($path);
            $cont = preg_replace_callback('@\$\{([a-zA-Z0-9_\.\-:\(\)]+)\}@', function ($match) {
                return $this->translateVar($match[1]);
            }, $cont);
            echo $cont;
        } else {
            $this->errorNotFound();
        }
    }

    /**
     * @param $key
     * @return string
     */
    protected function translateVar($key) {
        if($key === "path.relativeRoot") {
            return Plugon::getRootPath();
        }
//        if($key === "session.antiForge") {
//            return SessionUtils::getInstance()->getAntiForge();
//        }
        if($key === "session.isLoggedIn") {
            return SessionUtils::getInstance()->isLoggedIn() ? "true" : "false";
        }
        return '${' . $key . '}';
    }
}
