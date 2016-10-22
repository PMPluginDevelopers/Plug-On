<?php
namespace plugon\module\res;

use const plugon\JS_DIR;

class JsModule extends ResModule {
    public function getName() : string {
        return "js";
    }

    protected function resDir() : string {
        return JS_DIR;
    }
}
