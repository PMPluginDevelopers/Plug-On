<?php
namespace plugon\module\ajax;

class LogoutAjax extends AjaxModule {

    protected function impl() {
        $_SESSION["plugon"] = [];
        echo "{}";
    }

    public function getName() : string {
        return "logout";
    }

    protected function fallback() : bool {
        return true;
    }

}