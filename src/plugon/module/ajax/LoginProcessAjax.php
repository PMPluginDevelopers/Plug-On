<?php
namespace plugon\module\ajax;

class LoginProcessAjax extends AjaxModule {

    protected function impl() {
        //$out = [];
        var_dump($_REQUEST);
        echo json_encode($out);
    }

    public function getName() : string {
        return "loginProcess";
    }

    protected function fallback() : bool {
        return true;
    }

}