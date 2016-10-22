<?php
namespace plugon\module\res;

use plugon\module\Module;
use plugon\Poggit;
use plugon\session\SessionUtils;
use const plugon\RES_DIR;

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

    public function getName() : string {
        return "res";
    }

    protected function resDir() : string {
        return RES_DIR;
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

    protected function translateVar(string $key) {
        if($key === "path.relativeRoot") {
            return Poggit::getRootPath();
        }
        if($key === "session.antiForge") {
            return SessionUtils::getInstance()->getAntiForge();
        }
        if($key === "session.isLoggedIn") {
            return SessionUtils::getInstance()->isLoggedIn() ? "true" : "false";
        }
        return '${' . $key . '}';
    }
}
