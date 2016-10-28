<?php
namespace plugon\module\res;

include realpath(dirname(__FILE__)) . '/../Module.php';
use plugon\module\Module;

class JsModule extends ResModule {
    /**
     * @return string
     */
    public function getName(){
        return "js";
    }

    /**
     * @return string
     */
    protected function resDir(){
        return Module::JS_DIR;
    }
}
