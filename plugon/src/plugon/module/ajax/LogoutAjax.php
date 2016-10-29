<?php
namespace plugon\module\ajax;

include_once realpath(dirname(__FILE__)) . '/AjaxModule.php';

class LogoutAjax extends AjaxModule {

    protected function impl() {
        $_SESSION["plugon"] = [];
        echo "{}";
    }

    /**
     * @return string
     */
    public function getName(){
        return "logout";
    }

    /**
     * @return bool
     */
    protected function fallback(){
        return true;
    }

}