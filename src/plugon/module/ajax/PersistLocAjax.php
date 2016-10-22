<?php
namespace plugon\module\ajax;

use plugon\module\Module;
use plugon\session\SessionUtils;

class PersistLocAjax extends Module {

    public function getName() : string {
        return "persistLoc";
    }

    public function output() {
        SessionUtils::getInstance()->persistLoginLoc($_REQUEST["path"] ?? "");
        echo "{}";
    }
    
}