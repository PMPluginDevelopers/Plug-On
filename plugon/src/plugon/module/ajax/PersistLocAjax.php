<?php
namespace plugon\module\ajax;

include_once realpath(dirname(__FILE__)) . '/../Module.php';
include_once realpath(dirname(__FILE__)) . '/../../session/SessionUtils.php';

use plugon\module\Module;
use plugon\session\SessionUtils;

class PersistLocAjax extends Module {

    /**
     * @return string
     */
    public function getName() {
        return "PersistLoc";
    }

    public function output() {
        SessionUtils::getInstance()->persistLoginLoc($_REQUEST["path"] ? $_REQUEST["path"] : "");
        echo "{}";
    }
    
}